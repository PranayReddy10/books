@extends("admin.admin_app")

@section("content")

  
  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row">
                 <div class="col-md-3 m-b-20 mt-2">
                     {!! Form::open(array('url' => 'admin/suggestions','class'=>'app-search','id'=>'search','role'=>'form','method'=>'get')) !!}   
                      <input type="text" name="s" placeholder="{{trans('words.search_by_title')}}" class="form-control">
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
                      <th>{{trans('words.title')}}</th>
                      <th>{{trans('words.image')}}</th>
                      <th>{{trans('words.message')}}</th>
                      <th>{{trans('words.date')}}</th>
                       <th>{{trans('words.action')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                   @foreach($list as $i => $data)
                    <tr>
                       <td>{{ \App\User::getUserFullname($data->user_id) }}</td>
                       <td>{{ stripslashes($data->title) }}</td>
                       <td>@if(isset($data->image)) <img src="{{URL::to('/upload/'.$data->image)}}" alt="image" class="thumb-md bdr_radius"> @endif</td>
                       <td>{{ stripslashes($data->message) }}</td>
                       <td>{{ date('m-d-Y h:i a',$data->date) }}</td>
                     
                       </td>
 
                      <td>                       
 
                      <a href="{{ url('admin/suggestions/delete/'.$data->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" onclick="return confirm('{{trans('words.dlt_warning_text')}}')" data-toggle="tooltip" title="{{trans('words.remove')}}"> <i class="fa fa-remove"></i> </a>       
                      
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

    

@endsection