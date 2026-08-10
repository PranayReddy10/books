@extends('public.layout')

@section('meta_title', ($result->student_name ? $result->student_name.' — ' : '').'Result Report Card — JNTU Books')
@section('meta_description', 'Result report card'.($result->branch ? ' for '.$result->branch : '').' — generated on JNTU Books. Unofficial; verify at results.jntuh.ac.in.')
@section('og_type', 'article')
@section('og_image', $image_url)

@section('head')
<style>
  .wrapr{max-width:760px;margin:30px auto;padding:0 16px}
  .rcard{background:#fff;border:1px solid var(--line);border-radius:18px;overflow:hidden}
  .rhead{padding:20px 22px;background:linear-gradient(135deg,#0d47a1,#1976d2);color:#fff}
  .rhead h1{font-family:'Sora';font-size:22px;margin:0}
  .rhead .badge-v{display:inline-block;background:#2e7d32;color:#fff;border-radius:20px;padding:3px 12px;font-size:12px;margin-top:8px}
  .rbody{padding:18px 22px}
  .meta{display:grid;grid-template-columns:1fr 1fr;gap:8px 20px;margin-bottom:18px}
  .meta div span{color:var(--muted);font-size:12px;display:block}
  .meta div b{font-size:15px}
  .semt{margin-top:18px}
  .semt h3{font-family:'Sora';font-size:15px;color:#0d47a1;margin-bottom:8px}
  table.r{width:100%;border-collapse:collapse;font-size:13px}
  table.r th{text-align:left;color:var(--muted);font-weight:600;padding:6px 8px;border-bottom:1px solid var(--line)}
  table.r td{padding:6px 8px;border-bottom:1px solid #f0f0f4}
  .bk{color:#c62828;font-weight:600}
  .imgwrap{margin-top:18px;text-align:center}
  .imgwrap img{max-width:100%;border:1px solid var(--line);border-radius:12px}
  .dl{display:inline-block;margin-top:12px;background:#0d47a1;color:#fff;padding:10px 18px;border-radius:10px;font-weight:600}
  .disc{margin-top:16px;font-size:12px;color:var(--muted);text-align:center}
</style>
@endsection

@section('content')
<div class="wrapr">

  <article class="rcard">
    <div class="rhead">
      <h1>{{ $result->student_name ?: 'Student Result' }}</h1>
      <div>{{ $result->hall_ticket_no }}</div>
      @if($result->verified)<span class="badge-v">✓ Verified by JNTU Books</span>@endif
    </div>

    <div class="rbody">
      <div class="meta">
        <div><span>Branch</span><b>{{ $result->branch ?: '-' }}</b></div>
        <div><span>Regulation</span><b>{{ $result->regulation ?: '-' }}</b></div>
        <div><span>Current CGPA</span><b>{{ $result->current_cgpa ?: '-' }}</b></div>
        <div><span>Total Credits</span><b>{{ $result->total_credits ?: '-' }}</b></div>
        <div><span>Pending Backlogs</span><b>{{ $result->backlogs_count }}</b></div>
      </div>

      @foreach($sems as $sem)
        <div class="semt">
          <h3>Semester {{ $sem->sem_code }}
            @if($sem->sgpa) &middot; SGPA {{ $sem->sgpa }} @endif
            @if($sem->exam_month_year) &middot; {{ $sem->exam_month_year }} @endif
          </h3>
          <table class="r">
            <thead><tr><th>Code</th><th>Subject</th><th>Grade</th><th>Cr</th><th>Status</th></tr></thead>
            <tbody>
              @foreach($sem->loaded_subjects as $sub)
              <tr>
                <td>{{ $sub->subject_code }}</td>
                <td>{{ $sub->subject_name }}</td>
                <td>{{ $sub->grade }}</td>
                <td>{{ $sub->credits }}</td>
                <td>@if($sub->is_backlog)<span class="bk">Backlog</span>@else Pass @endif</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endforeach

      @if($image_url)
        <div class="imgwrap">
          <img src="{{ $image_url }}" alt="Report card for {{ $result->hall_ticket_no }}">
          <br><a class="dl" href="{{ $image_url }}" download>Download Report Card</a>
        </div>
      @endif

      <p class="disc">Unofficial — data entered by the student via JNTU Books.<br>
      Always verify against <a href="https://results.jntuh.ac.in" rel="nofollow">results.jntuh.ac.in</a>.</p>
    </div>
  </article>

</div>

{{-- Structured data --}}
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'CreativeWork',
  'name' => ($result->student_name ?: 'Student').' Result Report Card',
  'about' => $result->branch,
  'publisher' => ['@type' => 'Organization', 'name' => 'JNTU Books', 'url' => 'https://read.jntubooks.in'],
  'creativeWorkStatus' => $result->verified ? 'Verified' : 'Unverified',
], JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection
