@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card-box table-responsive">

            <div class="d-flex align-items-center mb-3">
              <h4 class="m-0">Roles &amp; Permissions</h4>
              @if(admin_can('roles.create'))
                <a href="{{ URL::to('admin/roles/add') }}" class="btn btn-sm btn-success ml-auto"><i class="fa fa-plus"></i> Add Role</a>
              @endif
            </div>

            @if(Session::has('flash_message'))
              <div class="alert alert-success">{{ Session::get('flash_message') }}</div>
            @endif

            <p class="text-muted">Create roles and grant exactly the pages and actions each team needs. Assign a role to an admin from the <a href="{{ url('admin/admins') }}">Admins</a> page.</p>

            <table class="table table-bordered">
              <thead>
                <tr><th>Role</th><th>Description</th><th>Permissions</th><th>Members</th><th>Action</th></tr>
              </thead>
              <tbody>
                @foreach($list as $role)
                <tr>
                  <td><b>{{ $role->name }}</b> @if($role->is_system)<span class="badge badge-secondary">system</span>@endif</td>
                  <td>{{ $role->description }}</td>
                  <td>{{ count($role->permissionList()) }} granted</td>
                  <td>{{ $role->memberCount() }}</td>
                  <td style="white-space:nowrap">
                    @if(admin_can('roles.edit'))
                      <a href="{{ URL::to('admin/roles/edit/'.$role->id) }}" class="btn btn-icon btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></a>
                    @endif
                    @if(admin_can('roles.delete') && !$role->is_system)
                      <a href="{{ URL::to('admin/roles/delete/'.$role->id) }}" class="btn btn-icon btn-sm btn-danger data_remove" title="Delete"><i class="fa fa-remove"></i></a>
                    @endif
                  </td>
                </tr>
                @endforeach
                @if(count($list)==0)
                <tr><td colspan="5" class="text-center text-muted" style="padding:20px">No roles yet. Create one to get started.</td></tr>
                @endif
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
  @include("admin.copyright")
</div>

<script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>
<script>
window.addEventListener('load', function () {
  document.querySelectorAll('.data_remove').forEach(function(a){
    a.addEventListener('click', function(e){
      e.preventDefault(); var url=this.getAttribute('href');
      Swal.fire({title:'Delete this role?',text:'Members will be unassigned.',icon:'warning',showCancelButton:true,confirmButtonText:'Delete',confirmButtonColor:'#d33'})
        .then(function(r){ if(r.isConfirmed) window.location.href=url; });
    });
  });
});
</script>

@endsection
