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
                     {!! Form::open(array('url' => 'admin/category','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}   
                      <input type="text" name="s" value="{{ request('s') }}" placeholder="{{trans('words.search_by_title')}}" class="form-control">
                      @if(request('university') !== null)<input type="hidden" name="university" value="{{ request('university') }}">@endif
                      <button type="submit"><i class="fa fa-search"></i></button>
                    {!! Form::close() !!}
                </div>
                <div class="col-sm-6">
                  @if(\Schema::hasColumn('categories', 'university_id'))
                  <form method="get" action="{{ URL::to('admin/category') }}" class="mt-2">
                    @if(request('s'))<input type="hidden" name="s" value="{{ request('s') }}">@endif
                    <select name="university" class="form-control" style="max-width:280px" onchange="this.form.submit()">
                      <option value="">All categories</option>
                      <option value="none" @if(request('university') === 'none') selected @endif>For all universities</option>
                      @foreach(\App\University::orderBy('university_name')->get() as $uni)
                        <option value="{{ $uni->id }}" @if((string) request('university') === (string) $uni->id) selected @endif>{{ $uni->university_name }}</option>
                      @endforeach
                    </select>
                  </form>
                  @endif
                </div>   

                <div class="col-md-3">
                  <a href="{{URL::to('admin/category/add')}}" class="btn btn-success btn-md waves-effect waves-light m-b-20 mt-2 pull-right" data-toggle="tooltip" title="{{trans('words.add_category')}}"><i class="fa fa-plus"></i> {{trans('words.add_category')}}</a>
                </div>
              </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button>
                        {{ Session::get('flash_message') }}
                    </div>
                @endif

                <div class="row">
                  @foreach($list as $i => $data)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6" id="card_box_id_{{$data->id}}">

                        <!-- Simple card -->
                        <div class="card m-b-20">

                            @if(isset($data->category_image)) <img class="card-img-top thumb-lg img-fluid" src="{{URL::to('/'.$data->category_image)}}" alt="{{ stripslashes($data->category_name) }}"> @endif
 

                            <div class="card-body p-3">
                                <h4 class="card-title mb-3">{{ stripslashes($data->category_name) }}</h4>
                                @if(\Schema::hasColumn('categories', 'university_id'))
                                  @if(!empty($data->university_id))<span class="badge badge-info mb-2"><i class="fa fa-university"></i> {{ \App\University::where('id', $data->university_id)->value('university_name') }}</span>
                                  @else<span class="badge badge-secondary mb-2">All universities</span>@endif
                                @endif
                                
                                <a href="{{ url('admin/category/edit/'.$data->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-r-5" data-toggle="tooltip" title="{{trans('words.edit')}}"> <i class="fa fa-edit"></i> </a>


                                <a href="#" class="btn btn-icon waves-effect waves-light btn-danger data_remove" data-toggle="tooltip" title="{{trans('words.remove')}}" data-id="{{$data->id}}"> <i class="fa fa-remove"></i> </a>
 

                                <a href="Javascript:void(0);" class="ml-2" data-toggle="tooltip" title="@if($data->status==1){{ trans('words.active')}} @else {{trans('words.inactive')}} @endif"><input type="checkbox" name="category_on_off" id="category_on_off" value="1" data-plugin="switchery" data-color="#28a745" data-size="small" class="enable_disable"  data-id="{{$data->id}}" @if($data->status==1) {{ 'checked' }} @endif/></a>    

 
                            </div>
                        </div>

                    </div>
                   @endforeach      

                </div>

  
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
 
<!-- SweetAlert2 -->
<script src="{{ URL::asset('admin_assets/js/sweetalert2@11.js') }}"></script>


<script type="text/javascript">
 
$(".enable_disable").on("change",function(e){      
       
      var post_id = $(this).data("id");
      
      var status_set = $(this).prop("checked"); 

      var action_name='cat_status';
      //alert($(this).is(":checked"));
      //alert(status_set);

      $.ajax({
        type: 'post',
        url: "{{ URL::to('admin/ajax_status') }}",
        dataType: 'json',
        data: {"_token": "{{ csrf_token() }}",id: post_id, value: status_set, action_for: action_name},
        success: function(res) {

          if(res.status=='1')
          {
            Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{trans('words.status_changed')}}',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
             
          } 
          else
          { 
            Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Something went wrong!',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
          }
          
        }
      });
}); 

</script>

<script type="text/javascript">

$(".data_remove").click(function () {  
  
  var post_id = $(this).data("id");
  var action_name='cat_delete';

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

  //alert(post_id);

  //alert(JSON.stringify(result));

    if(result.isConfirmed) { 

        $.ajax({
            type: 'post',
            url: "{{ URL::to('admin/ajax_delete') }}",
            dataType: 'json',
            data: {"_token": "{{ csrf_token() }}",id: post_id, action_for: action_name},
            success: function(res) {

              if(res.status=='1')
              {  

                  var selector = "#card_box_id_"+post_id;
                    $(selector ).fadeOut(1000);
                    setTimeout(function(){
                            $(selector ).remove()
                        }, 1000);

                  Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '{{trans('words.deleted')}}!',
                    showConfirmButton: true,
                    confirmButtonColor: '#10c469',
                    background:"#1a2234",
                    color:"#fff"
                  })
                
              } 
              else
              { 
                Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Something went wrong!',
                        showConfirmButton: true,
                        confirmButtonColor: '#10c469',
                        background:"#1a2234",
                        color:"#fff"
                       })
              }
              
            }
        });
    }
 
})

});

</script>
 
@endsection

