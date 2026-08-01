@extends("admin.admin_app")

@section("content")

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row">
                 <div class="col-md-3">
                     {!! Form::open(array('url' => 'admin/department','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}
                      <input type="text" name="s" placeholder="{{trans('words.search_by_title')}}" class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                    {!! Form::close() !!}
                </div>
                <div class="col-sm-6">&nbsp;</div>
                <div class="col-md-3">
                  <a href="{{URL::to('admin/department/add')}}" class="btn btn-success btn-md waves-effect waves-light m-b-20 mt-2 pull-right"><i class="fa fa-plus"></i> Add Department</a>
                </div>
              </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {{ Session::get('flash_message') }}
                    </div>
                @endif

                <table class="table table-hover mb-0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>University</th>
                      <th>Department Name</th>
                      <th>{{trans('words.status')}}</th>
                      <th>{{trans('words.action')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($list as $i => $data)
                    <tr id="row_id_{{$data->id}}">
                      <td>{{ $data->id }}</td>
                      <td>{{ $data->university_id ? stripslashes(\App\University::getUniversityInfo($data->university_id,'university_name')) : '-' }}</td>
                      <td>{{ stripslashes($data->department_name) }}</td>
                      <td>
                        @if($data->status==1)<span class="badge badge-success">{{trans('words.active')}}</span>
                        @else<span class="badge badge-danger">{{trans('words.inactive')}}</span>@endif
                      </td>
                      <td>
                        <a href="{{ url('admin/department/edit/'.$data->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-r-5"><i class="fa fa-edit"></i></a>
                        <a href="{{ url('admin/department/delete/'.$data->id) }}" class="btn btn-icon waves-effect waves-light btn-danger data_remove"><i class="fa fa-remove"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>

                <nav class="paging_simple_numbers">
                @include('admin.pagination', ['paginator' => $list])
                </nav>

              </div>
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright")
    </div>

<script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>
<script type="text/javascript">
$(".data_remove").click(function (e) {
  e.preventDefault();
  var url = $(this).attr("href");
  Swal.fire({
    title: '{{trans('words.dlt_warning')}}',
    text: "{{trans('words.dlt_warning_text')}}",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: '{{trans('words.dlt_confirm')}}',
    cancelButtonText: "{{trans('words.btn_cancel')}}",
    background:"#1a2234",
    color:"#fff"
  }).then((result) => {
    if(result.isConfirmed) { window.location.href = url; }
  })
});
</script>

@endsection
