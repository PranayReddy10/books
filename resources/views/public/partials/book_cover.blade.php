{{--
  Public reusable book cover with generated-gradient fallback + video support.
  Params: $book (->image, ->title, ->cat_id, ->content_type, ->url, ->url_type)
  - Video with no custom image -> YouTube thumbnail + play overlay.
  - Book with real image -> show it.
  - Otherwise -> gradient card with the title.
--}}
@php
    $is_video = isset($book->content_type) && $book->content_type === 'video';
    $img = isset($book->image) ? trim($book->image) : '';
    $has_real = $img !== '' && strpos($img, 'placeholder') === false;

    // For videos with no custom cover, use the YouTube thumbnail.
    $video_thumb = '';
    if ($is_video && !$has_real && isset($book->url)) {
        $video_thumb = youtube_thumb($book->url);
    }

    if (!$has_real && !$video_thumb) {
        $cc = \App\Category::getCategoryInfo($book->cat_id ?? 0, 'category_color');
        if (empty($cc)) { $cc = '#5b3df6'; }
    }
@endphp

@if($has_real)
    <img src="{{ book_asset_url($book->image) }}" alt="{{ stripslashes($book->title) }}" loading="lazy"
         onerror="this.onerror=null;this.style.display='none';this.parentNode.classList.add('cover-fallback');">
    <span class="cover-fallback-title">{{ Str::limit(stripslashes($book->title), 60) }}</span>
@elseif($video_thumb)
    <img src="{{ $video_thumb }}" alt="{{ stripslashes($book->title) }}" loading="lazy" style="width:100%;height:100%;object-fit:cover">
@else
    <div class="pub-auto-cover" style="--cc: {{ $cc }};">
        <span>{{ Str::limit(stripslashes($book->title), 60) }}</span>
    </div>
@endif

@if($is_video)
    <span class="video-badge" aria-hidden="true"><i class="fa fa-play"></i></span>
@endif
