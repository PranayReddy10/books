{{--
  Reusable book cover.
  Params: $book (must have ->image, ->title, ->cat_id)
          $class (optional extra classes for the <img>/box)
  If the book has a real uploaded image -> show it.
  Otherwise -> render an attractive gradient (from category color) + title.
--}}
@php
    $img = isset($book->image) ? trim($book->image) : '';
    $has_real = $img !== '' && strpos($img,'placeholder') === false;
    $cover_class = isset($class) ? $class : 'card-img-top thumb-xs img-fluid';
    if (!$has_real) {
        $cc = \App\Category::getCategoryInfo($book->cat_id ?? 0, 'category_color');
        if (empty($cc)) { $cc = '#4a7dff'; }
        // build a darker second stop for the gradient
        $c2 = $cc;
    }
@endphp

@if($has_real)
    <img class="{{ $cover_class }}" src="{{ book_asset_url($book->image) }}" alt="{{ stripslashes($book->title) }}">
@else
    <div class="auto-cover {{ $cover_class }}"
         style="--cc: {{ $cc }};">
        <div class="auto-cover-inner">
            <span class="auto-cover-title">{{ Str::limit(stripslashes($book->title), 70) }}</span>
        </div>
    </div>
@endif
