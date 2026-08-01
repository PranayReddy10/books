@extends('public.layout')

@section('meta_title', 'Reading: '.stripslashes($book->title).' — JNTU Books')
@section('meta_description', 'Read '.stripslashes($book->title).' on JNTU Books.')

@section('head')
<style>
  .reader-bar{background:#12103a;color:#fff;padding:12px 0}
  .reader-bar .wrap{display:flex;align-items:center;gap:14px}
  .reader-bar .bt{font-family:'Sora';font-weight:600;font-size:16px;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .reader-bar a{color:#cfc9ff;font-size:14px;font-weight:600}
  .reader-bar a:hover{color:#fff}
  .reader-stage{height:calc(100vh - 66px - 49px);background:#3a3a48}
  .reader-stage iframe,.reader-stage embed{width:100%;height:100%;border:none;display:block}
  .reader-fallback{max-width:520px;margin:60px auto;background:#fff;border:1px solid var(--line);border-radius:16px;padding:30px;text-align:center}
  .reader-fallback h3{font-family:'Sora';margin-bottom:8px}
  .reader-fallback p{color:var(--muted);margin-bottom:18px}
</style>
@endsection

@section('content')
<div class="reader-bar">
  <div class="wrap">
    <span class="bt">{{ stripslashes($book->title) }}</span>
    <a href="{{ route('public.book', $book->id.'-'.Str::slug($book->title)) }}"><i class="fa fa-arrow-left"></i> Back</a>
    @if($book->download_enable)
      <a href="{{ $file_url }}" download><i class="fa fa-download"></i> Download</a>
    @endif
  </div>
</div>

@if($ext == 'pdf')
  <div class="reader-stage">
    {{-- Google Docs viewer renders PDFs reliably across browsers/devices --}}
    <iframe src="https://docs.google.com/gview?embedded=true&url={{ urlencode($file_url) }}"
            title="{{ stripslashes($book->title) }}"
            onerror="this.style.display='none';document.getElementById('nativeEmbed').style.display='block'"></iframe>
    <embed id="nativeEmbed" src="{{ $file_url }}#toolbar=1" type="application/pdf" style="display:none">
  </div>
@else
  {{-- EPUB or other formats: offer to open directly --}}
  <div class="reader-fallback">
    <h3>Open this book</h3>
    <p>This title is in {{ strtoupper($ext ?: 'e-book') }} format. Open it in a new tab, or read it in the JNTU Books app for the best experience.</p>
    <a href="{{ $file_url }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="fa fa-up-right-from-square"></i> Open book</a>
  </div>
@endif
@endsection
