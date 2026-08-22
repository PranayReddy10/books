@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-8">
          <div class="card-box">
            <h4 class="header-title m-b-20">{{ $user->name }}
              <small class="text-muted">{{ $user->email }} · {{ $user->rollnumber }}</small>
            </h4>
            <h2 class="m-t-0">{{ number_format($user->coin_balance) }} <small class="text-muted">coins</small></h2>
            <p class="text-muted">Worth {{ getcong('currency_symbol') }}{{ number_format($coins->valueOf($user->coin_balance), 2) }}</p>
          </div>

          <div class="card-box table-responsive">
            <h4 class="header-title m-b-20">Books uploaded</h4>
            <table class="table table-hover m-0">
              <thead><tr><th>Book</th><th>Status</th><th class="text-right">Views</th><th class="text-right">Paid reads</th><th class="text-right">Coins</th></tr></thead>
              <tbody>
                @forelse($books as $b)
                <tr>
                  <td>{{ $b['title'] }}</td>
                  <td><span class="badge badge-secondary">{{ ucfirst($b['status'] ?: 'live') }}</span></td>
                  <td class="text-right">{{ number_format($b['views']) }}</td>
                  <td class="text-right">{{ number_format($b['reads']) }}</td>
                  <td class="text-right"><strong>{{ number_format($b['coins']) }}</strong></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No uploads yet</td></tr>
                @endforelse
              </tbody>
            </table>
            <p class="text-muted m-t-10 m-b-0">
              <small>"Paid reads" counts distinct readers — the same student reading a book again does not earn more.</small>
            </p>
          </div>

          <div class="card-box table-responsive">
            <h4 class="header-title m-b-20">Ledger</h4>
            <table class="table table-sm m-0">
              <thead><tr><th>Date</th><th>Type</th><th>Note</th><th class="text-right">Coins</th></tr></thead>
              <tbody>
                @forelse($ledger as $t)
                <tr>
                  <td><small>{{ $t->created_at ? $t->created_at->format('d M Y') : '-' }}</small></td>
                  <td><small>{{ ucfirst($t->type) }}</small></td>
                  <td><small>{{ $t->note }}</small></td>
                  <td class="text-right"><span class="badge {{ $t->coins >= 0 ? 'badge-success' : 'badge-secondary' }}">{{ $t->coins > 0 ? '+' : '' }}{{ $t->coins }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">Nothing yet</td></tr>
                @endforelse
              </tbody>
            </table>
            <div class="m-t-20">{!! $ledger->links() !!}</div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card-box">
            <h4 class="header-title m-b-20">Adjust balance</h4>
            {!! Form::open(array('url' => 'admin/coins/adjust/'.$user->id, 'method' => 'post')) !!}
              <div class="form-group">
                <label>Coins (negative to take away)</label>
                <input type="number" name="coins" class="form-control" placeholder="e.g. 100 or -50" required>
              </div>
              <div class="form-group">
                <label>Reason</label>
                <input type="text" name="note" class="form-control" placeholder="Shown to the student">
              </div>
              <button type="submit" class="btn btn-primary btn-block">Apply</button>
            {!! Form::close() !!}
          </div>

          <div class="card-box table-responsive">
            <h4 class="header-title m-b-20">Gift cards</h4>
            <table class="table table-sm m-0">
              <thead><tr><th>Code</th><th>Value</th><th>Status</th></tr></thead>
              <tbody>
                @forelse($cards as $c)
                <tr>
                  <td><code>{{ $c->code ?: '—' }}</code></td>
                  <td>{{ getcong('currency_symbol') }}{{ $c->amount }}</td>
                  <td>
                    @if($c->status == 'issued')
                      <span class="badge badge-success">Issued</span>
                    @elseif($c->status == 'failed')
                      <span class="badge badge-danger">Failed</span>
                    @elseif($c->status == 'cancelled')
                      <span class="badge badge-secondary">Cancelled</span>
                    @else
                      <span class="badge badge-warning">Pending</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted">None</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection
