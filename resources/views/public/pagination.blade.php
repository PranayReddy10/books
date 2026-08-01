@if ($paginator->hasPages())
  @if ($paginator->onFirstPage())
    <span>‹</span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
  @endif

  @foreach ($paginator->getUrlRange(max(1,$paginator->currentPage()-2), min($paginator->lastPage(),$paginator->currentPage()+2)) as $page => $url)
    @if ($page == $paginator->currentPage())
      <span class="cur">{{ $page }}</span>
    @else
      <a href="{{ $url }}">{{ $page }}</a>
    @endif
  @endforeach

  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
  @else
    <span>›</span>
  @endif
@endif
