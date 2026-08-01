<option value="">{{trans('words.select_sub_category')}}</option>
@foreach($sub_cat_list as $sub_cat_data)  
  <option value="{{$sub_cat_data->id}}">{{$sub_cat_data->sub_category_name}}</option>
@endforeach