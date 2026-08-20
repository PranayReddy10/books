@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card-box table-responsive">

            <div class="row m-b-20">
              <div class="col-md-12">
                {!! Form::open(array('url' => 'admin/coin_redemptions','class'=>'form-inline','role'=>'form','method'=>'get')) !!}
                  <input type="text" name="s" value="{{ request('s') }}" placeholder="Gift card code" class="form-control m-r-5">
                  <select name="status" class="form-control m-r-5">
                    <option value="">All statuses</option>
                    @foreach(['issued','pending','failed','cancelled'] as $st)
                      <option value="{{ $st }}" {{ request('status')==$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                  </select>
                  <button type="submit" class="btn btn-primary">Filter</button>
                {!! Form::close() !!}
              </div>
            </div>

            <table class="table table-hover m-0">
              <thead>
                <tr><th>Student</th><th>Code</th><th class="text-right">Coins</th><th class="text-right">Value</th><th>Status</th><th>Date</th><th>Actions</th></tr>
              </thead>
              <tbody>
                @forelse($list as $r)
                <tr>
                  <td>
                    <a href="{{ url('admin/coins/user/'.$r->user_id) }}">{{ \App\User::getUserInfo($r->user_id,'name') }}</a>
                    <br><small class="text-muted">{{ \App\User::getUserInfo($r->user_id,'email') }}</small>
                  </td>
                  <td><code>{{ $r->code ?: '—' }}</code></td>
                  <td class="text-right">{{ number_format($r->coins) }}</td>
                  <td class="text-right">{{ getcong('currency_symbol') }}{{ $r->amount }}</td>
                  <td>
                    @if($r->status == 'issued')
                      <span class="badge badge-success">Issued</span>
                    @elseif($r->status == 'failed')
                      <span class="badge badge-danger">Failed</span>
                      @if($r->fail_reason)<br><small class="text-muted">{{ $r->fail_reason }}</small>@endif
                    @elseif($r->status == 'cancelled')
                      <span class="badge badge-secondary">Cancelled</span>
                    @else
                      <span class="badge badge-warning">Pending</span>
                    @endif
                  </td>
                  <td><small>{{ $r->created_at ? $r->created_at->format('d M Y') : '-' }}</small></td>
                  <td>
                    @if($r->status != 'issued' && $r->status != 'cancelled')
                      <a href="{{ url('admin/coin_redemptions/retry/'.$r->id) }}" class="btn btn-sm btn-primary">Retry</a>
                      <a href="{{ url('admin/coin_redemptions/cancel/'.$r->id) }}" class="btn btn-sm btn-warning" onclick="return confirm('Return these coins to the student and drop the card?');">Refund</a>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">No gift cards yet</td></tr>
                @endforelse
              </tbody>
            </table>

            <div class="m-t-20">{!! $list->links() !!}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
