{{--
  Public reusable book cover with generated-gradient fallback.
  Params: $book (->image, ->title, ->cat_id)
  Real uploaded image -> show it. Otherwise -> gradient card with the title.
--}}
@php
    $img = isset($book->image) ? trim($book->image) : '';
    $has_real = $img !== '' && strpos($img, 'placeholder') === false;
    if (!$has_real) {
        $cc = \App\Category::getCategoryInfo($book->cat_id ?? 0, 'category_color');
        if (empty($cc)) { $cc = '#5b3df6'; }
    }
@endphp

@if($has_real)
    <img src="{{ book_asset_url($book->image) }}" alt="{{ stripslashes($book->title) }}" loading="lazy"
         onerror="this.onerror=null;this.style.display='none';this.parentNode.classList.add('cover-fallback');">
    <span class="cover-fallback-title">{{ Str::limit(stripslashes($book->title), 60) }}</span>
@else
    <div class="pub-auto-cover" style="--cc: {{ $cc }};">
        <span>{{ Str::limit(stripslashes($book->title), 60) }}</span>
    </div>
@endif
