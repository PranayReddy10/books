@extends("admin.admin_app")

@section("content")

<style>
.rc-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(210px, 1fr)); gap:18px; }
.rc-item{ background:#fff; border:1px solid var(--ad-line); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; }
.rc-item:hover{ box-shadow:var(--ad-shadow-lg); }
.rc-thumb{ display:block; height:240px; background:var(--ad-surface-2); overflow:hidden; border-bottom:1px solid var(--ad-line); }
.rc-thumb img{ width:100%; display:block; }
.rc-body{ padding:12px 14px; display:flex; flex-direction:column; gap:4px; flex:1; }
.content-page .rc-name{ font-weight:700; color:var(--ad-ink) !important; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.rc-meta{ font-size:12.5px; color:var(--ad-muted); }
.rc-actions{ display:flex; gap:6px; margin-top:8px; }
.rc-actions .btn{ flex:1; padding:6px 8px; font-size:12.5px; }
.rc-empty{ text-align:center; color:var(--ad-muted); padding:48px 10px; }
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="card-box">
        <h4 class="header-title">Generated report cards</h4>
        <p class="text-muted" style="margin:-8px 0 18px;font-size:13px;">Images students can share. Generate or regenerate one from a result's page.</p>

        @if($list->count())
          <div class="rc-grid">
            @foreach($list as $c)
              @php $r = $results[$c->result_id] ?? null; @endphp
              <div class="rc-item">
                <a href="{{ $c->pdf_url }}" target="_blank" rel="noopener" class="rc-thumb"><img src="{{ $c->pdf_url }}" alt="Report card" loading="lazy"></a>
                <div class="rc-body">
                  @if($r)
                    <a href="{{ url('admin/results/view/'.$r->id) }}" class="rc-name">{{ $r->student_name ?: $r->hall_ticket_no }}</a>
                    <span class="rc-meta">{{ $r->hall_ticket_no }}@if($r->current_cgpa) · CGPA {{ number_format((float) $r->current_cgpa, 2) }}@endif</span>
                  @else
                    <span class="rc-name">Result #{{ $c->result_id }} (deleted)</span>
                  @endif
                  <span class="rc-meta">{{ $c->generated_at ? $c->generated_at->format('d M Y, h:i A') : '' }}
                    @if($c->verified_at_generation)<span class="badge badge-success">Verified</span>@else<span class="badge badge-warning">Unverified</span>@endif
                  </span>
                  <div class="rc-actions">
                    <a href="{{ $c->pdf_url }}" target="_blank" rel="noopener" class="btn btn-secondary"><i class="fa fa-external-link"></i> Open</a>
                    <a href="{{ $c->pdf_url }}" download class="btn btn-secondary"><i class="fa fa-download"></i> Download</a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div class="m-t-20">{!! $list->links() !!}</div>
        @else
          <div class="rc-empty"><i class="fa fa-id-card-o" style="font-size:26px;display:block;margin-bottom:8px;color:var(--ad-faint)"></i>No cards generated yet. Open a result and choose <b>Generate card</b>.</div>
        @endif
      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

@endsection
