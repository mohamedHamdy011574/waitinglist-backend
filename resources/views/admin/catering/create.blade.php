@extends('layouts.admin')

@section('css')
<style>
  .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
  .food_servings{margin-bottom: 10px}
  #add_food_serving {margin-left: 5px;}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('catering.index')}}">{{trans('catering.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('catering.add_new') }}
      </li>
    </ol>
  </section>
  <section class="content">
    @if ($errors->any())
    <div class="alert alert-danger">
      <b>{{trans('common.whoops')}}</b>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">{{ trans('catering.details') }}</h3>
            @can('catering-list')
            <ul class="pull-right">
                <a href="{{route('catering.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('catering.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
              @csrf
              <div class="model-body">
                <div class="row">
                  <div class="col-md-6">
                    <ul class="nav nav-tabs" role="tablist">
                      @foreach(config('app.locales') as $lk=>$lv)
                        <li role="presentation" class="@if($lk=='en') active @endif">
                          <a href="#abc_{{$lk}}" aria-controls="" role="tab" data-toggle="tab" aria-expanded="true">
                                    {{$lv['name']}}
                          </a>
                        </li>  
                      @endforeach
                    </ul>
                    <div class="tab-content" style="margin-top: 10px;">
                      @foreach(config('app.locales') as $lk=>$lv)
                        <div role="tabpanel" class="tab-pane @if($lk=='en') active @endif" id="abc_{{$lk}}">
                          <div class="form-group">
                            <label for="name:{{$lk}}" class="content-label">{{trans('catering.name')}}</label>
                            <input class="form-control"  placeholder="{{trans('catering.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{old('name:'.$lk)}}" required>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('catering.description')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('catering.description')}}" name="description:{{$lk}}" id="description:{{$lk}}" required>{{old('description:'.$lk)}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="food_serving:{{$lk}}" class="content-label">{{trans('catering.food_serving')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('catering.food_serving')}}" name="food_serving:{{$lk}}" id="food_serving:{{$lk}}" required>{{old('food_serving:'.$lk)}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="address:{{$lk}}" class="content-label">{{trans('catering.address')}}</label>
                            <textarea class="form-control" rows="5" placeholder="{{trans('catering.address')}}" name="address:{{$lk}}" id="address:{{$lk}}" required>{{old('address:'.$lk)}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('catering.link')}}</label>
                      <input class="form-control" placeholder="{{trans('catering.link')}}" type="url" name="link" value="{{old('link')}}" id="link" required>
                    </div>

                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('catering.cuisine')}}
                      </label>
                      <select class="form-control multiselect1" id="cuisines" name="cuisines[]" multiple data-placeholder="{{trans('catering.select_cuisines')}}" required>
                        @foreach($cuisines as $cuisine)
                        <option value="{{$cuisine->id}}"
                          {{ (collect(old('cuisines'))->contains($cuisine->id)) ? 'selected':'' }}
                          >{{$cuisine->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('catering.banners')}}</label>
                      <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="banners[]" id="banners" required>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                          <select class="form-control" name="status" id="status" required>
                            <option value="active" 
                              @if(old('status') == 'active') selected @endif>
                              {{trans('common.active')}}
                            </option>
                            <option value="inactive" 
                              @if(old('status') == 'inactive') selected @endif>
                              {{trans('common.inactive')}}
                            </option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="price" class="content-label">{{trans('catering.price')}} {{$currency}}</label>
                          <input name="price" required class="form-control" type="number" value="{{old('price')}}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="country" class="content-label">
                            {{trans('catering.country')}}
                          </label>
                          <select class="form-control select2" id="country" name="country_id" data-placeholder="{{trans('catering.select_country')}}" required>
                            <option value="">{{trans('catering.select_country')}}
                            </option>
                            @foreach($countries as $country)
                            <option value="{{$country->id}}"
                              {{ ((old('restaurant_id') == $country->id)) ? 'selected':'' }}
                              >{{$country->country_name}}
                            </option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="state" class="content-label">
                            {{trans('catering.state')}}
                          </label>
                          <select class="form-control select2" id="state" name="state_id" data-placeholder="{{trans('catering.select_state')}}" required>
                            <option value="">{{trans('catering.select_state')}}
                            </option>
                            
                          </select>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <hr class="separator">
                      <label for="status" class="content-label working_hours_title">{{trans('catering.working_hours_title')}}</label>
                      <div class="row">
                        <div class="col-md-6">
                          <label for="from_day" class="content-label">{{trans('catering.day_from')}}</label>
                          <select class="form-control" name="from_day" id="from_day" required>
                            @foreach([0,1,2,3,4,5,6] as $day)
                            <option value="{{$day}}" @if($day == old('from_day')) selected @endif>{{trans('catering.working_hours.'.$day)}}
                            </option>
                            @endforeach
                          </select> 
                          <label for="status" class="content-label">{{trans('catering.time_from')}}</label>
                          <input name="from_time" required class="form-control" type="time" value="{{old('from_time')}}">   
                        </div>
                        <div class="col-md-6">
                          <label for="status" class="content-label">{{trans('catering.day_to')}}</label>
                          <select class="form-control" name="to_day" id="status" required>
                            @foreach([0,1,2,3,4,5,6] as $day2)
                            <option value="{{$day2}}" @if($day2 == old('to_day')) selected @endif>{{trans('catering.working_hours.'.$day2)}}
                            </option>
                            @endforeach
                          </select>
                          <label for="status" class="content-label">{{trans('catering.time_to')}}</label>
                          <input name="to_time" required class="form-control" type="time" value="{{old('to_time')}}">
                        </div>
                      </div>
                    </div>
                </div>
              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('Submit')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
<!-- <script src="{{ asset('admin/bower_components/ckeditor/ckeditor.js') }}"></script> -->
@section('js')
<script>
    
    CKEDITOR.replace('description:en');
    CKEDITOR.replace('description:ar');
    CKEDITOR.replace( 'food_serving:en', {
      toolbarGroups: [
        { name: 'paragraph', groups: [ 'list']},
      ]
    });
    CKEDITOR.replace( 'food_serving:ar', {
      toolbarGroups: [
        { name: 'paragraph', groups: [ 'list'],},
      ]
    });
    
    CKEDITOR.on( 'instanceReady',function(ev) {
            if(ev.editor.name == 'food_serving:en') {
              ev.editor.execCommand( 'bulletedlist' );
            }
    });


    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      var target = $(e.target).attr("href") // activated tab
      CKEDITOR.instances['food_serving:ar'].execCommand('bulletedlist');    
    });

    $(document).on('change','#country',function(){
        var country = $(this).val();
        var id = $(this).attr('id');
        var delay = 500;
        var element = $(this);
        $.ajax({
            type:'post',
            url: "{{route('get_states_by_country')}}",
            data: {
                    "country": country, 
                    "id" : id,  
                    "_token": "{{ csrf_token() }}"
            },
            success: function (data) {
              var states = JSON.parse(data);
              $("#state").html('<option value="">{{trans("catering.select_state")}}                     </option>');
              $.each(states,function(key, val){
                $("#state").append("<option value='"+val.id+"'>"+val.name+"</option>");
              })
              console.log(data);
            },
            error: function () {
              toastr.error(data.error);
            }
        })
    })

    $('#cuisines_input').attr('autocomplete','off');
</script>

<!-- <script>
    //Initialize Select2 Elements
    $('#add_food_serving').click(function(){
      var food_serving_input = '<input name="food_serving[]" required class="form-control food_servings" type="text">';
      $('#food_serving_en').append(food_serving_input);
    });
</script> -->
@endsection