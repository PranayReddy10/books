@extends("admin.admin_app")

@section("content")

<style>
  .perm-matrix{width:100%;border-collapse:collapse}
  .perm-matrix th,.perm-matrix td{border:1px solid #e6e6ef;padding:8px 10px;text-align:center}
  .perm-matrix th.mod,.perm-matrix td.mod{text-align:left;font-weight:600;white-space:nowrap}
  .perm-matrix thead th{background:#f6f5ff;font-size:12px;text-transform:uppercase;letter-spacing:.4px;color:#6b6f9c}
  .perm-matrix tbody tr:hover{background:#fafaff}
  .perm-matrix input[type=checkbox]{width:17px;height:17px;cursor:pointer}
  .perm-matrix .dash{color:#ccc}
  .rowtools{font-size:11px;color:#5b3df6;cursor:pointer;margin-left:8px}
</style>

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-11">
          <div class="card-box">
            <h4 class="header-title m-t-0">{{ $page_title }}</h4>

            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

            <form action="{{ URL::to('admin/roles/save') }}" method="POST">
              {{ csrf_field() }}
              @if($role)<input type="hidden" name="id" value="{{ $role->id }}">@endif

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Role name</label>
                <div class="col-sm-5"><input type="text" name="name" class="form-control" value="{{ $role->name ?? old('name') }}" required placeholder="e.g. Approver Team"></div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-9"><input type="text" name="description" class="form-control" value="{{ $role->description ?? old('description') }}" placeholder="What this role is for (optional)"></div>
              </div>

              <h5 class="mt-4 mb-2">Permissions</h5>
              <p class="text-muted">Tick the actions this role can perform in each module. Leave a module fully unticked to hide it entirely.</p>

              <div class="table-responsive">
                <table class="perm-matrix">
                  <thead>
                    <tr>
                      <th class="mod">Module <span class="rowtools" onclick="toggleAll(true)">all</span> / <span class="rowtools" onclick="toggleAll(false)">none</span></th>
                      @foreach($action_labels as $akey => $alabel)
                        <th>{{ $alabel }}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($modules as $mkey => $mcfg)
                    <tr>
                      <td class="mod">{{ $mcfg['label'] }} <span class="rowtools" onclick="toggleRow('{{ $mkey }}',true)">all</span></td>
                      @foreach($action_labels as $akey => $alabel)
                        <td>
                          @if(in_array($akey, $mcfg['actions']))
                            @php $perm = $mkey.'.'.$akey; @endphp
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}" data-mod="{{ $mkey }}" data-act="{{ $akey }}" {{ in_array($perm, $granted) ? 'checked' : '' }}>
                          @else
                            <span class="dash">—</span>
                          @endif
                        </td>
                      @endforeach
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Role</button>
                <a href="{{ URL::to('admin/roles') }}" class="btn btn-secondary">Cancel</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

<script>
function toggleAll(state){
  document.querySelectorAll('.perm-matrix input[type=checkbox]').forEach(function(c){ c.checked = state; });
}
function toggleRow(mod, state){
  document.querySelectorAll('.perm-matrix input[data-mod="'+mod+'"]').forEach(function(c){ c.checked = state; });
}
</script>

@endsection
