@extends("admin.admin_app")

@section("content")
<style>
 a .hover-img {
   position:relative;
}

a .hover-img span {
   position:absolute;
   left:-10000px;
   top:-10000px;
   z-index:10000;<!--   w  ww .jav a  2s  .c o m-->
}

a:hover .hover-img span {
   top:-10px;
   left:60px;
}
</style>
  
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row">
                 <div class="col-md-3 m-b-20 mt-2">
                     {!! Form::open(array('url' => 'admin/reviews','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}   
                      <input type="text" name="s" placeholder="{{trans('words.search_by_review_text')}}" class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                    {!! Form::close() !!}
                 </div>
                 
                </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span></button>
                        {{ Session::get('flash_message') }}
                    </div>
                @endif
 
                <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{{trans('words.name')}}</th>
                      <th>{{trans('words.email')}}</th>
                      <th>{{trans('words.title')}}</th>
                      <th>{{trans('words.review_text')}}</th>
                      <th>{{trans('words.ratings')}}</th>
                      <th>{{trans('words.date')}}</th>
                       <th>{{trans('words.action')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                   @foreach($list as $i => $data)
                    <tr id="post_id_{{$data->id}}">
                       <td>{{ \App\User::getUserFullname($data->user_id) }}</td>
                       <td>{{ \App\User::getUserInfo($data->user_id,'email') }}</td>
                       <td>
 
                       <a href="{{ url('admin/books/edit/'.$data->post_id) }}" data-toggle="tooltip" data-html="true" data-placement="right" title='<img src="{{URL::to('/'.\App\Books::getBookInfo($data->post_id,'image'))}}" alt="image" class="thumb-img"> 
                                <br/><br/>
                                <span class="badge badge-success"><i class="fa fa-eye"></i> {{post_views_count($data->post_id,"Book")}}</span> &nbsp;
                                <span class="badge badge-warning"><i class="fa fa-star"></i> {{\App\PostRatings::getPostTotalRatings($data->post_id,"Book")}}</span> &nbsp;
                                <span class="badge badge-purple"><i class="fa fa-download"></i> {{post_download_count($data->post_id,"Book")}}</span>
                                '> 
                        {{ Str::limit(stripslashes(\App\Books::getBookInfo($data->post_id,'title')), 25) }}
                        </a>  
                       
                      </td>
                       <td>{{ stripslashes($data->review_text) }}</td>
                       <td>{{ $data->rate }}</td>
                       <td>{{ date('M d Y h:i a',$data->date) }}</td>
                     
                       </td>
 
                      <td>                       
                      
                      <a href="#" class="btn btn-icon waves-effect waves-light btn-danger data_remove" data-toggle="tooltip" title="{{trans('words.remove')}}" data-id="{{$data->id}}"> <i class="fa fa-remove"></i> </a>

                      <!-- <a href="{{ url('admin/reports/delete/'.$data->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" onclick="return confirm('{{trans('words.dlt_warning_text')}}')" data-toggle="tooltip" title="{{trans('words.remove')}}"> <i class="fa fa-remove"></i> </a>        -->
                      
                      </td>
                    </tr>
                   @endforeach                     
                     
                     
                  </tbody>
                </table>
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
//Single
$(".data_remove").click(function () {  
  
  var post_id = $(this).data("id");
  var action_name='reviews_delete';

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

                  var selector = "#post_id_"+post_id;
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