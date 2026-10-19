<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Read-only proxy to the MadeForU store's product API for the app's Shop tab.
 * The app never sees the API key; it only calls these endpoints, which
 * fetch (cached) from madeforu.co.in/api/v1 and return app-friendly JSON.
 *
 * Buying/personalisation/checkout happens on the website in a WebView, so the
 * app side is browse-only (no login, no cart, no payment here).
 */
class ShopController extends Controller
{
    const PER_PAGE = 20;

    /** Top-level product categories for the Shop home row (category ids are slugs). */
    public function shop_categories()
    {
        // Accept the standard signed payload but don't require any fields.
        if (isset($_POST['data'])) { @checkSignSalt($_POST['data']); }

        $res = mfu_get('categories', array());
        $cats = isset($res['data']) && is_array($res['data']) ? $res['data'] : array();

        // Filtering by a parent category includes its sub-categories, so the
        // row only needs the top level.
        $cats = array_filter($cats, function ($c) {
            return empty($c['parent_id']) && !empty($c['products']);
        });
        usort($cats, function ($a, $b) {
            return (int) $b['products'] - (int) $a['products'];
        });

        $response = array();
        foreach ($cats as $c) {
            $response[] = array(
                'id'    => isset($c['slug']) ? $c['slug'] : '',
                'name'  => isset($c['name']) ? html_entity_decode($c['name']) : '',
                'slug'  => isset($c['slug']) ? $c['slug'] : '',
                'count' => (string) $c['products'],
                'image' => isset($c['image']) ? (string) $c['image'] : '',
            );
        }

        return \Response::json(array(
            'EBOOK_APP'   => $response,
            'status_code' => 200,
            'success'     => 1,
        ));
    }

    /** Product list. Optional: category (slug), search, on_sale, page. */
    public function shop_products()
    {
        $get = isset($_POST['data']) ? checkSignSalt($_POST['data']) : array();

        $query = array(
            'per_page' => self::PER_PAGE,
            'page'     => isset($get['page']) && (int) $get['page'] > 0 ? (int) $get['page'] : 1,
        );
        if (!empty($get['category'])) { $query['category'] = $get['category']; }
        if (!empty($get['search']))   { $query['q']        = $get['search']; }

        $res = mfu_get('products', $query);
        $products = isset($res['data']) && is_array($res['data']) ? $res['data'] : array();

        // The store API has no sale filter; narrow this page down instead.
        if (!empty($get['on_sale'])) {
            $products = array_values(array_filter($products, function ($p) {
                return !empty($p['on_sale']);
            }));
        }

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
        $id  = isset($get['product_id']) ? trim((string) $get['product_id']) : '';
        // Ids are numeric or slugs; anything else never reaches the store URL.
        if ($id === '' || !preg_match('/^[A-Za-z0-9\-]+$/', $id)) {
            return \Response::json(array('EBOOK_APP' => array(), 'status_code' => 200, 'success' => 0));
        }

        $res = mfu_get('products/' . $id, array());
        $p = isset($res['data']) && is_array($res['data']) ? $res['data'] : array();
        if (empty($p['id'])) {
            return \Response::json(array('EBOOK_APP' => array(), 'status_code' => 200, 'success' => 0));
        }

        $item = array(
            'id'             => (string) $p['id'],
            'name'           => isset($p['name']) ? html_entity_decode($p['name']) : '',
            'price'          => woo_price(isset($p['price']) ? $p['price'] : ''),
            'regular_price'  => woo_price(isset($p['regular_price']) ? $p['regular_price'] : ''),
            'sale_price'     => !empty($p['on_sale']) ? woo_price($p['price']) : '',
            'on_sale'        => !empty($p['on_sale']) ? '1' : '0',
            'currency'       => '₹',
            'description'    => isset($p['description_text']) ? (string) $p['description_text'] : '',
            'short_desc'     => isset($p['short_description']) ? html_entity_decode(strip_tags((string) $p['short_description'])) : '',
            'stock_status'   => !empty($p['in_stock']) ? 'instock' : 'outofstock',
            'images'         => $this->images($p),
            'permalink'      => isset($p['url']) ? $p['url'] : '',
            'buy_url'        => isset($p['url']) ? $p['url'] : '',
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
        $cfg = config('services.madeforu');
        return \Response::json(array(
            'EBOOK_APP' => array(array(
                'shop_url'  => isset($cfg['shop_url'])  ? $cfg['shop_url']  : '',
                'track_url' => isset($cfg['track_url']) ? $cfg['track_url'] : '',
            )),
            'status_code' => 200,
            'success'     => 1,
        ));
    }

    /** Shape store products into compact tiles for the app grid. */
    private function mapProducts($products)
    {
        $out = array();
        if (!is_array($products)) { return $out; }
        foreach ($products as $p) {
            if (!isset($p['id'])) { continue; }
            $images = $this->images($p);

            // Discount percent for the sale badge.
            $discount = '';
            if (!empty($p['on_sale']) && !empty($p['discount_percent'])) {
                $discount = '-' . (int) $p['discount_percent'] . '%';
            }

            $out[] = array(
                'id'            => (string) $p['id'],
                'name'          => isset($p['name']) ? html_entity_decode($p['name']) : '',
                'price'         => woo_price(isset($p['price']) ? $p['price'] : ''),
                'regular_price' => woo_price(isset($p['regular_price']) ? $p['regular_price'] : ''),
                'on_sale'       => !empty($p['on_sale']) ? '1' : '0',
                'discount'      => $discount,
                'currency'      => '₹',
                'thumb'         => isset($images[0]) ? $images[0] : '',
                'stock_status'  => !empty($p['in_stock']) ? 'instock' : 'outofstock',
                'buy_url'       => isset($p['url']) ? $p['url'] : '',
            );
        }
        return $out;
    }

    /**
     * Gallery URLs, or the main image when there is no gallery. The store's
     * placeholder is an SVG the app's image loader can't draw, so it is left
     * out and the app shows its own placeholder.
     */
    private function images($p)
    {
        $urls = array();
        if (!empty($p['images']) && is_array($p['images'])) {
            foreach ($p['images'] as $img) {
                if (!empty($img['url'])) { $urls[] = $img['url']; }
            }
        }
        if (empty($urls) && !empty($p['image'])) {
            $urls[] = $p['image'];
        }
        return array_values(array_filter($urls, function ($u) {
            return !preg_match('/\.svg($|\?)/i', $u);
        }));
    }
}
