@extends("admin.admin_app")

@section("content")
<style type="text/css">
 
  .color-drops{
    width: 24px;
    height: 24px;
    border-radius: 50%;
    float: left;
    margin: 5px -5px 5px 0px;
    text-align:center;
    box-shadow:1px 0px 10px #000;
    transition: all linear .2s;
  }
  .color-drops:hover{
    transform: scale(1.2);
  }


</style>
  
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                @php
                  $keep = array_filter($filters, 'strlen');
                  $chipUrl = function ($status) use ($keep) { $q = array_merge($keep, ['filter' => $status]); return URL::to('admin/books') . '?' . http_build_query(array_filter($q, 'strlen')); };
                @endphp

                {{-- Toolbar: search + category/author/department, then status chips --}}
                <form method="get" action="{{ URL::to('admin/books') }}" class="bl-toolbar" id="bookFilters">
                  @if($filters['filter'] !== '')<input type="hidden" name="filter" value="{{ $filters['filter'] }}">@endif
                  <div class="app-search bl-search">
                    <input type="text" name="s" value="{{ $filters['s'] }}" placeholder="{{trans('words.search_by_title')}}" class="form-control">
                    <button type="submit"><i class="fa fa-search"></i></button>
                  </div>
                  <select name="cat_id" class="form-control bl-auto">
                    <option value="">All categories</option>
                    @foreach($cat_list as $cat_data)<option value="{{ $cat_data->id }}" @if($filters['cat_id']==$cat_data->id) selected @endif>{{ $cat_data->category_name }}</option>@endforeach
                  </select>
                  <select name="department_id" class="form-control bl-auto">
                    <option value="">All departments</option>
                    @foreach($department_list as $dep)<option value="{{ $dep->id }}" @if($filters['department_id']==$dep->id) selected @endif>{{ stripslashes($dep->department_name) }}</option>@endforeach
                  </select>
                  <select name="author_id" class="form-control bl-auto">
                    <option value="">All authors</option>
                    @foreach($authors_list as $author_data)<option value="{{ $author_data->id }}" @if($filters['author_id']==$author_data->id) selected @endif>{{ $author_data->name }}</option>@endforeach
                  </select>
                  <a href="{{URL::to('admin/books/add')}}" class="btn btn-success bl-add"><i class="fa fa-plus"></i> {{trans('words.add_book')}}</a>
                </form>

                <div class="bl-chips">
                  @foreach($status_counts as $key => $st)
                    <a href="{{ $chipUrl($key) }}" class="bl-chip {{ $filters['filter'] === (string) $key ? 'is-active' : '' }} {{ $key==='Pending' && $st['count'] ? 'is-warn' : '' }}">{{ $st['label'] }} <span>{{ number_format($st['count']) }}</span></a>
                  @endforeach
                  @if(count(array_diff_key($keep, ['filter'=>1])))
                    <a href="{{ URL::to('admin/books') }}" class="bl-clear"><i class="fa fa-times"></i> Clear filters</a>
                  @endif
                </div>

                {{-- Shown while books are ticked --}}
                <div class="bl-bulk" id="bulkBar">
                  <span><b id="bulkCount">0</b> selected</span>
                  <a href="javascript:void(0);" class="btn btn-secondary btn-sm" data-action="enable" id="data_enable_selected"><i class="fa fa-eye"></i> Make live</a>
                  <a href="javascript:void(0);" class="btn btn-secondary btn-sm" data-action="disable" id="data_disable_selected"><i class="fa fa-eye-slash"></i> Hide</a>
                  <a href="javascript:void(0);" class="btn btn-secondary btn-sm bl-danger" data-action="delete" id="data_remove_selected"><i class="fa fa-trash-o"></i> {{trans('words.delete')}}</a>
                </div>

                <div class="table-responsive">
                <table class="table bl-table">
                  <thead>
                    <tr>
                      <th class="bl-check-col"><div class="checkbox checkbox-primary"><input id="sellect_all" type="checkbox" name="sellect_all"><label for="sellect_all"></label></div></th>
                      <th>Book</th>
                      <th class="bl-dept">Department</th>
                      <th>Access</th>
                      <th>Live</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  @forelse($list as $i => $data)
                    @php
                      $cat_name = \App\Category::getCategoryInfo($data->cat_id,'category_name');
                      $sub_name = $data->sub_cat_id ? \App\SubCategory::getSubCategoryInfo($data->sub_cat_id,'sub_category_name') : '';
                      $is_video = isset($data->content_type) && $data->content_type=='video';
                      $depts = \DB::table('book_department')->join('departments','departments.id','=','book_department.department_id')->where('book_department.book_id',$data->id)->pluck('departments.department_name');
                    @endphp
                    <tr id="card_box_id_{{$data->id}}">
                      <td class="bl-check-col">
                        <div class="checkbox checkbox-primary">
                          <input type="checkbox" name="post_ids[]" id="checkbox{{ $i }}" value="{{ $data->id }}" class="post_ids">
                          <label for="checkbox{{ $i }}"></label>
                        </div>
                      </td>
                      <td>
                        <div class="bl-book">
                          <a href="{{ url('admin/books/edit/'.$data->id) }}" class="bl-cover">@include('admin.partials.book_cover', ['book' => $data, 'class' => 'bl-cover-img'])</a>
                          <div class="bl-info">
                            <a href="{{ url('admin/books/edit/'.$data->id) }}" class="bl-title" title="{{ stripslashes($data->title) }}">{{ stripslashes($data->title) }}</a>
                            @php $rt = \App\PostRatings::getPostTotalRatings($data->id,"Book"); @endphp
                            <div class="bl-meta">{{ $cat_name ?: 'No category' }}@if($sub_name) · {{ $sub_name }}@endif
                              <span class="bl-stat"><i class="fa fa-eye"></i> {{ number_format_short(post_views_count($data->id,"Book")) }}</span>
                              @if($rt)<span class="bl-stat"><i class="fa fa-star"></i> {{ $rt }}</span>@endif
                            </div>
                            @if($depts->count())<div class="bl-meta bl-dept-inline"><i class="fa fa-university"></i> {{ $depts->first() }}@if($depts->count() > 1) +{{ $depts->count() - 1 }}@endif</div>@endif
                            <div class="bl-tags">
                              @if($is_video)<span class="badge badge-danger"><i class="fa fa-play"></i> Video</span>@endif
                              @if($data->featured)<span class="badge badge-warning"><i class="fa fa-star"></i> Home slider</span>@endif
                              @if($data->upload_status=='pending')<span class="badge badge-warning">Pending review</span>@endif
                              @if($data->upload_status=='rejected')<span class="badge badge-danger">Rejected</span>@endif
                              @if($data->uploaded_by)<span class="badge badge-secondary">Student upload</span>@endif
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="bl-dept">
                        @if($depts->count())
                          {{ $depts->first() }}@if($depts->count() > 1) <span class="text-muted">+{{ $depts->count() - 1 }}</span>@endif
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                      <td>
                        @if($data->book_access=='Paid')<span class="badge badge-warning">Paid</span>@else<span class="badge badge-success">Free</span>@endif
                        @if($data->book_on_rent)<span class="badge badge-secondary">Rent</span>@endif
                      </td>
                      <td>
                        <input type="checkbox" name="category_on_off" id="book_enable_disable{{$data->id}}" value="1" data-plugin="switchery" data-color="#16a34a" data-size="small" class="enable_disable" data-id="{{$data->id}}" @if($data->status==1) checked @endif/>
                      </td>
                      <td>
                        <a href="{{ url('admin/books/edit/'.$data->id) }}" class="btn btn-icon btn-primary" data-toggle="tooltip" title="{{trans('words.edit')}}"><i class="fa fa-pencil"></i></a>
                        <a href="javascript:void(0)" class="btn btn-icon btn-secondary bl-feature slider_enable_disable {{ $data->featured ? 'is-on' : '' }}" id="slider_id{{$data->id}}" data-id="{{$data->id}}" data-value="{{$data->featured?'false':'true'}}" data-toggle="tooltip" title="{{$data->featured?'Remove from home slider':'Show in home slider'}}"><i class="fa fa-star"></i></a>
                        <a href="#" class="btn btn-icon btn-danger data_remove" data-id="{{$data->id}}" data-toggle="tooltip" title="{{trans('words.remove')}}"><i class="fa fa-trash-o"></i></a>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="6">No books match these filters.</td></tr>
                  @endforelse
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
 
$(".enable_disable").on("change",function(e){      
       
      var post_id = $(this).data("id");
      
      var status_set = $(this).prop("checked"); 

      var action_name='book_status';
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
 
$(".slider_enable_disable").on("click",function(e){      
      
 
      var post_id = $(this).data("id");
      
      var status_set = $(this).data("value"); 

      var action_name='book_featured';
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

            //slider_id
            var s_post_id= '#slider_id'+post_id;

            $(s_post_id).toggleClass('is-on', res.set_color == 'green');
            $(s_post_id).attr('data-original-title', res.set_title);            
            $(s_post_id).data('value',res.re_set_value);

            Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: res.res_msg,
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
//Single
$(".data_remove").click(function () {  
  
  var post_id = $(this).data("id");
  var action_name='book_delete';

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

//Multiple Delete
$("#data_remove_selected").click(function () {  
  
  var post_ids = $.map($('.post_ids:checked'), function(c) {
      return c.value;
    });
         
  var action_name='book_delete_selected';

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
 
    if(result.isConfirmed) { 

        $.ajax({
            type: 'post',
            url: "{{ URL::to('admin/ajax_delete') }}",
            dataType: 'json',
            data: {"_token": "{{ csrf_token() }}",id: post_ids, action_for: action_name},
            success: function(res) {

              if(res.status=='1')
              {  
                  $.map($('.post_ids:checked'), function(c) {
                    
                    var post_id= c.value;
                    
                    var selector = "#card_box_id_"+post_id;
                      $(selector ).fadeOut(1000);
                      setTimeout(function(){
                              $(selector ).remove()
                          }, 1000);

                    return c.value;
                  });
 
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


//Multiple Enable
$("#data_enable_selected").click(function () {  
  
  var post_ids = $.map($('.post_ids:checked'), function(c) {
      return c.value;
    });
         
  var action_name='book_enable_multiple';

  $.ajax({
        type: 'post',
        url: "{{ URL::to('admin/ajax_status') }}",
        dataType: 'json',
        data: {"_token": "{{ csrf_token() }}",id: post_ids, action_for: action_name},
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
                  }).then((result) => {

                    var url="{{url()->full()}}";
                    window.location = url;

                    });
             
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


//Multiple Disable
$("#data_disable_selected").click(function () {  
  
  var post_ids = $.map($('.post_ids:checked'), function(c) {
      return c.value;
    });
         
  var action_name='book_disable_multiple';

  $.ajax({
        type: 'post',
        url: "{{ URL::to('admin/ajax_status') }}",
        dataType: 'json',
        data: {"_token": "{{ csrf_token() }}",id: post_ids, action_for: action_name},
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
                  }).then((result) => {

                    var url="{{url()->full()}}";
                    window.location = url;
     
                  });
             
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
<script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
<script type="text/javascript">

  


   

  // Bulk bar: appears with a count while any book is ticked.
  function refreshBulk() {
    var n = $("input[name='post_ids[]']:checked").length;
    $('#bulkCount').text(n);
    $('#bulkBar').toggleClass('is-open', n > 0);
    $('#sellect_all').prop('checked', n > 0 && n === $("input[name='post_ids[]']").length);
  }
  $(document).on('change', '#sellect_all', function () {
    $("input[name='post_ids[]']").prop('checked', this.checked);
    refreshBulk();
  });
  $(document).on('change', '.post_ids', refreshBulk);

  // Category / department / author pick applies straight away.
  // requestSubmit fires a real submit event, so the handler below runs whichever jQuery copy bound it.
  $('#bookFilters .bl-auto').on('change', function () { document.getElementById('bookFilters').requestSubmit(); });
  // Leave empty fields out of the address so links stay short.
  $('#bookFilters').on('submit', function () { $(this).find('input, select').filter(function () { return !this.value; }).prop('disabled', true); });


</script> 
 
@endsection

