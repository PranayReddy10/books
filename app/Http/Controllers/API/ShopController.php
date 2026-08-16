<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Read-only proxy to the MadeForU WooCommerce store for the app's Shop tab.
 * The app never sees the Woo API key; it only calls these endpoints, which
 * fetch (cached) from WooCommerce and return app-friendly JSON.
 *
 * Buying/personalisation/checkout happens on the website in a WebView, so the
 * app side is browse-only (no login, no cart, no payment here).
 */
class ShopController extends Controller
{
    /** Product categories for the Shop home row. */
    public function shop_categories()
    {
        // Accept the standard signed payload but don't require any fields.
        if (isset($_POST['data'])) { @checkSignSalt($_POST['data']); }

        $cats = woo_get('products/categories', array(
            'per_page'   => 50,
            'orderby'    => 'count',
            'order'      => 'desc',
            'hide_empty' => true,
        ));

        $response = array();
        foreach ($cats as $c) {
            // Skip the catch-all "Uncategorized" if present.
            if (isset($c['slug']) && $c['slug'] === 'uncategorized') { continue; }
            $response[] = array(
                'id'    => isset($c['id']) ? (string) $c['id'] : '',
                'name'  => isset($c['name']) ? html_entity_decode($c['name']) : '',
                'slug'  => isset($c['slug']) ? $c['slug'] : '',
                'count' => isset($c['count']) ? (string) $c['count'] : '0',
                'image' => (isset($c['image']['src']) ? $c['image']['src'] : ''),
            );
        }

        return \Response::json(array(
            'EBOOK_APP'   => $response,
            'status_code' => 200,
            'success'     => 1,
        ));
    }

    /** Product list. Optional: category id, search, page. */
    public function shop_products()
    {
        $get = isset($_POST['data']) ? checkSignSalt($_POST['data']) : array();

        $query = array(
            'per_page' => 20,
            'status'   => 'publish',
            'page'     => isset($get['page']) && (int) $get['page'] > 0 ? (int) $get['page'] : 1,
            'orderby'  => 'date',
            'order'    => 'desc',
        );
        if (!empty($get['category'])) { $query['category'] = $get['category']; }
        if (!empty($get['search']))   { $query['search']   = $get['search']; }
        if (!empty($get['on_sale']))  { $query['on_sale']  = 'true'; }

        $products = woo_get('products', $query);

        return \Response::json(array(
            'EBOOK_APP'   => $this->mapProducts($products),
            'status_code' => 200,
            'success'     => 1,
        ));
    }

    /** Single product detail (all images, description, buy URL). */
    public function shop_product_detail()
    {
        $get = isset($_POST['data']) ? checkSignSalt($_POST['data']) : array();
        $id  = isset($get['product_id']) ? (int) $get['product_id'] : 0;
        if ($id <= 0) {
            return \Response::json(array('EBOOK_APP' => array(), 'status_code' => 200, 'success' => 0));
        }

        $p = woo_get('products/' . $id, array());
        if (empty($p) || !isset($p['id'])) {
            return \Response::json(array('EBOOK_APP' => array(), 'status_code' => 200, 'success' => 0));
        }

        $images = array();
        if (!empty($p['images']) && is_array($p['images'])) {
            foreach ($p['images'] as $img) {
                if (!empty($img['src'])) { $images[] = $img['src']; }
            }
        }

        $item = array(
            'id'             => (string) $p['id'],
            'name'           => isset($p['name']) ? html_entity_decode($p['name']) : '',
            'price'          => woo_price(isset($p['price']) ? $p['price'] : ''),
            'regular_price'  => woo_price(isset($p['regular_price']) ? $p['regular_price'] : ''),
            'sale_price'     => woo_price(isset($p['sale_price']) ? $p['sale_price'] : ''),
            'on_sale'        => !empty($p['on_sale']) ? '1' : '0',
            'currency'       => '₹',
            'description'    => isset($p['description']) ? strip_tags($p['description']) : '',
            'short_desc'     => isset($p['short_description']) ? strip_tags($p['short_description']) : '',
            'stock_status'   => isset($p['stock_status']) ? $p['stock_status'] : 'instock',
            'images'         => $images,
            'permalink'      => isset($p['permalink']) ? $p['permalink'] : '',
            'buy_url'        => isset($p['permalink']) ? $p['permalink'] : '',
        );

        return \Response::json(array(
            'EBOOK_APP'   => array($item),
            'status_code' => 200,
            'success'     => 1,
        ));
    }

    /** Static links (shop + track order) so the app can open them in a WebView. */
    public function shop_links()
    {
        if (isset($_POST['data'])) { @checkSignSalt($_POST['data']); }
        $cfg = config('services.woocommerce');
        return \Response::json(array(
            'EBOOK_APP' => array(array(
                'shop_url'  => isset($cfg['shop_url'])  ? $cfg['shop_url']  : '',
                'track_url' => isset($cfg['track_url']) ? $cfg['track_url'] : '',
            )),
            'status_code' => 200,
            'success'     => 1,
        ));
    }

    /** Shape a Woo product list into compact tiles for the app grid. */
    private function mapProducts($products)
    {
        $out = array();
        if (!is_array($products)) { return $out; }
        foreach ($products as $p) {
            if (!isset($p['id'])) { continue; }
            $thumb = '';
            if (!empty($p['images'][0]['src'])) { $thumb = $p['images'][0]['src']; }

            // Discount percent for the sale badge.
            $discount = '';
            $reg  = isset($p['regular_price']) ? (float) $p['regular_price'] : 0;
            $sale = isset($p['sale_price']) ? (float) $p['sale_price'] : 0;
            if (!empty($p['on_sale']) && $reg > 0 && $sale > 0 && $sale < $reg) {
                $discount = '-' . round((($reg - $sale) / $reg) * 100) . '%';
            }

            $out[] = array(
                'id'            => (string) $p['id'],
                'name'          => isset($p['name']) ? html_entity_decode($p['name']) : '',
                'price'         => woo_price(isset($p['price']) ? $p['price'] : ''),
                'regular_price' => woo_price(isset($p['regular_price']) ? $p['regular_price'] : ''),
                'on_sale'       => !empty($p['on_sale']) ? '1' : '0',
                'discount'      => $discount,
                'currency'      => '₹',
                'thumb'         => $thumb,
                'stock_status'  => isset($p['stock_status']) ? $p['stock_status'] : 'instock',
                'buy_url'       => isset($p['permalink']) ? $p['permalink'] : '',
            );
        }
        return $out;
    }
}
