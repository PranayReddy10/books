@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-md-3 col-6">
          <div class="card-box"><h4 class="m-t-0">{{ number_format($stats['in_circulation']) }}</h4><p class="text-muted m-b-0">Coins held by students</p></div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card-box"><h4 class="m-t-0 text-info">{{ getcong('currency_symbol') }}{{ number_format($stats['liability'], 2) }}</h4><p class="text-muted m-b-0">What that is worth</p></div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card-box"><h4 class="m-t-0 text-success">{{ number_format($stats['earned']) }}</h4><p class="text-muted m-b-0">Earned all time</p></div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card-box"><h4 class="m-t-0 text-warning">{{ $stats['pending_cards'] }}</h4><p class="text-muted m-b-0">Cards needing attention</p></div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-7">
          <div class="card-box table-responsive">
            <div class="float-right m-b-10">
              <a href="{{ URL::to('admin/coin_settings') }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-cog"></i> Rates</a>
              <a href="{{ URL::to('admin/coin_redemptions') }}" class="btn btn-primary btn-sm"><i class="fa fa-gift"></i> Gift cards</a>
            </div>
            <h4 class="header-title m-b-20">Balances</h4>
            <table class="table table-hover m-0">
              <thead><tr><th>Student</th><th>Roll</th><th class="text-right">Coins</th><th class="text-right">Value</th><th></th></tr></thead>
              <tbody>
                @forelse($earners as $u)
                <tr>
                  <td><strong>{{ $u->name }}</strong><br><small class="text-muted">{{ $u->email }}</small></td>
                  <td>{{ $u->rollnumber }}</td>
                  <td class="text-right"><span class="badge badge-warning">{{ number_format($u->coin_balance) }}</span></td>
                  <td class="text-right">{{ getcong('currency_symbol') }}{{ number_format($coins->valueOf($u->coin_balance), 2) }}</td>
                  <td class="text-right"><a href="{{ url('admin/coins/user/'.$u->id) }}" class="btn btn-icon btn-sm btn-info"><i class="fa fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">Nobody has earned coins yet</td></tr>
                @endforelse
              </tbody>
            </table>
            <div class="m-t-20">{!! $earners->links() !!}</div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card-box table-responsive">
            <h4 class="header-title m-b-20">Latest activity</h4>
            <table class="table table-sm m-0">
              <thead><tr><th>Student</th><th>What</th><th class="text-right">Coins</th></tr></thead>
              <tbody>
                @forelse($recent as $t)
                <tr>
                  <td><small>{{ \App\User::getUserInfo($t->user_id,'name') }}</small></td>
                  <td><small>{{ $t->note ?: ucfirst($t->type) }}</small></td>
                  <td class="text-right">
                    <span class="badge {{ $t->coins >= 0 ? 'badge-success' : 'badge-secondary' }}">{{ $t->coins > 0 ? '+' : '' }}{{ $t->coins }}</span>
                  </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted">No activity yet</td></tr>
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
