@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-8">
          <div class="card-box">

            {!! Form::open(array('url' => array('admin/coin_settings'),'class'=>'form-horizontal','role'=>'form')) !!}

              <h4 class="header-title m-b-20">Coin settings</h4>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Coins feature</label>
                <div class="col-sm-6">
                  <select class="form-control" name="coins_enabled">
                    <option value="1" {{ isset($settings->coins_enabled) && $settings->coins_enabled == 1 ? 'selected' : '' }}>On</option>
                    <option value="0" {{ isset($settings->coins_enabled) && $settings->coins_enabled == 0 ? 'selected' : '' }}>Off</option>
                  </select>
                  <small class="text-muted">Turning this off stops new earning and blocks redemption. Balances are kept.</small>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Coins per read</label>
                <div class="col-sm-6">
                  <input type="number" min="0" name="coins_per_read" value="{{ isset($settings->coins_per_read) ? $settings->coins_per_read : 1 }}" class="form-control">
                  <small class="text-muted">Paid to the uploader the first time each student reads their book.</small>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Coins per approved upload</label>
                <div class="col-sm-6">
                  <input type="number" min="0" name="coins_per_upload" value="{{ isset($settings->coins_per_upload) ? $settings->coins_per_upload : 10 }}" class="form-control">
                  <small class="text-muted">Paid once, when you approve the book.</small>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">One coin is worth</label>
                <div class="col-sm-6">
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">{{ getcong('currency_symbol') }}</span></div>
                    <input type="number" step="0.0001" min="0" name="coin_value" value="{{ isset($settings->coin_value) ? $settings->coin_value : '0.1000' }}" class="form-control">
                  </div>
                  <small class="text-muted">Sets the gift-card value. 500 coins at 0.10 becomes a {{ getcong('currency_symbol') }}50 card.</small>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Minimum to redeem</label>
                <div class="col-sm-6">
                  <input type="number" min="1" name="coins_min_redeem" value="{{ isset($settings->coins_min_redeem) ? $settings->coins_min_redeem : 500 }}" class="form-control">
                  <small class="text-muted">Students cannot cash out below this.</small>
                </div>
              </div>

              <div class="form-group row m-t-20">
                <div class="col-sm-6 offset-sm-4">
                  <button type="submit" class="btn btn-primary">Save</button>
                  <a href="{{ URL::to('admin/coins') }}" class="btn btn-light">Back to coins</a>
                </div>
              </div>

            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
