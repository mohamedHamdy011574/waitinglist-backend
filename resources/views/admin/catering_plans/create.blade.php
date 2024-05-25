@extends('layouts.admin')

@section('css')
<style>
  .separator{ padding: 1px; margin:20px 0px; background: #e5e5e5; border:none; }
  .working_hours_title { font-size: 20px  }
  .food_servings{margin-bottom: 10px}
  #add_food_serving {margin-left: 5px;}
  .person_to_served{width: 70px; display: inline;}
  .person_to_served_seprator{padding: 0px 10px; display: inline;}
  .service_type {margin-right: 5px !important;}

  .weekDays-selector input {
    position: absolute;
    visibility: hidden;
    /*display: none!important;*/
  }

  .weekDays-selector input[type=checkbox] + label {
    display: inline-block;
    border-radius: 6px;
    background: #dddddd;
    height: 40px;
    width: 40px;
    margin-right: 3px;
    line-height: 40px;
    text-align: center;
    cursor: pointer;
  }

  .weekDays-selector input[type=checkbox]:checked + label {
    background: green;
    color: #ffffff;
  }

  input.off_prem_times {width: 50%; display: inline;}
  select.off_prem_times {width: 40%; display: inline;}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering_plans.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('catering_plans.index')}}">{{trans('catering_plans.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('catering_plans.add_new') }}
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
            <h3 class="box-title">{{ trans('catering_plans.details') }}</h3>
            @can('catering-plan-list')
            <ul class="pull-right">
                <a href="{{route('catering_plans.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('catering_plans.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
              @csrf
              <div class="model-body">
                <div class="row">
                  <div class="col-md-12">
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
                            <label for="plan_name:{{$lk}}" class="content-label">{{trans('catering_plans.plan_name')}}</label>
                            <input class="form-control"  placeholder="{{trans('catering_plans.plan_name')}}" name="plan_name:{{$lk}}" id="plan_name:{{$lk}}" type="text" value="{{old('plan_name:'.$lk)}}" 
                            @if($lk=='en' || $lk=='ar') required @endif>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('catering_plans.description')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('catering_plans.description')}}" name="description:{{$lk}}" id="description:{{$lk}}" @if($lk=='en' || $lk=='ar') required @endif>{{old('description:'.$lk)}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="food_serving:{{$lk}}" class="content-label">{{trans('catering_plans.food_serving')}}</label>
                            <textarea class="form-control" rows="9" placeholder="{{trans('catering_plans.food_serving')}}" name="food_serving:{{$lk}}" id="food_serving:{{$lk}}" @if($lk=='en' || $lk=='ar') required @endif>{{old('food_serving:'.$lk)}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="persons_served_min" class="content-label">{{trans('catering_plans.person_to_served')}}</label><br/>
                      <input class="form-control person_to_served" placeholder="{{trans('catering_plans.min')}}" type="number" min=1 name="persons_served_min" value="{{(old('persons_served_min')) ? old('persons_served_min') : 1 }}" id="persons_served_min" required>
                      <span class="person_to_served_seprator">-</span> 
                      <input class="form-control person_to_served" placeholder="{{trans('catering_plans.max')}}" type="number" min=1 name="persons_served_max" value="{{(old('persons_served_max')) ? old('persons_served_max') : 1}}" id="persons_served_max" required>
                    </div>                    
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('catering_plans.serving_days_time')}}</label>
                      <div class="weekDays-selector">
                          <input type="checkbox" name="sunday_serving" id="sunday_serving" class="weekday" value="1" 
                          @if((old('sunday_serving')==1)) checked @endif />
                          <label for="sunday_serving">{{trans('catering_plans.week_days.0')}}</label>

                          <input type="checkbox" name="monday_serving" id="monday_serving" class="weekday" value="1"
                          @if((old('monday_serving')==1)) checked @endif />
                          <label for="monday_serving">{{trans('catering_plans.week_days.1')}}</label>

                          <input type="checkbox" name="tuesday_serving" id="tuesday_serving" class="weekday" value="1" 
                          @if((old('tuesday_serving')==1)) checked @endif/>
                          <label for="tuesday_serving">{{trans('catering_plans.week_days.2')}}</label>

                          <input type="checkbox" name="wednesday_serving" id="wednesday_serving" class="weekday" value="1"
                          @if((old('wednesday_serving')==1)) checked @endif/>
                          <label for="wednesday_serving">{{trans('catering_plans.week_days.3')}}</label>

                          <input type="checkbox" name="thursday_serving" id="thursday_serving" class="weekday" value="1"
                          @if((old('thursday_serving')==1)) checked @endif/>
                          <label for="thursday_serving">{{trans('catering_plans.week_days.4')}}</label>

                          <input type="checkbox" name="friday_serving" id="friday_serving" class="weekday" value="1"
                          @if((old('friday_serving')==1)) checked @endif/>
                          <label for="friday_serving">{{trans('catering_plans.week_days.5')}}</label>

                          <input type="checkbox" name="saturday_serving" id="saturday_serving" class="weekday" value="1"
                          @if((old('saturday_serving')==1)) checked @endif/>
                          <label for="saturday_serving">{{trans('catering_plans.week_days.6')}}</label>
                      </div>
                    </div>
                    <div class="row form-group">
                      <div class="col-md-3">
                        <label for="status" class="content-label">{{trans('restaurants.time_from')}}</label>
                        <input name="from_time" required class="form-control" type="time" value="{{old('from_time')}}"> 
                      </div>
                      <div class="col-md-3">
                        <label for="status" class="content-label">{{trans('restaurants.time_to')}}</label>
                        <input name="to_time" required class="form-control" type="time" value="{{old('to_time')}}">
                      </div>
                    </div>
                    
                    
                    <div class="form-group">
                      <label for="banners" class="content-label">{{trans('catering_plans.banners')}}</label>
                      <input class="form-control" multiple type="file" accept=".png,.jpg,.jpeg,.PNG,.JPG,.JPEG" name="banners[]" id="banners" required>
                    </div>
                  </div>

                      <div class="col-md-6">
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('catering_plans.plan_rate')}} {{$currency}}</label>
                              <input name="plan_rate" required class="form-control" type="number" value="{{old('plan_rate')}}">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('catering_plans.currency')}} {{$currency}}</label>
                              <input name="currency" required class="form-control" readonly type="text" value="{{$currency}}">
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="price" class="content-label">{{trans('catering_plans.plan_served')}}</label><br/>
                          <input type="checkbox" class="service_type" id="served_in_restaurant" name="served_in_restaurant" value="1" @if(old('served_in_restaurant')==1)) checked @endif> 
                          <label for="served_in_restaurant"> {{trans('catering_plans.served_in_restaurant')}}</label>
                          &nbsp; | &nbsp;
                          <input type="checkbox" class="service_type" id="served_off_premises" name="served_off_premises" value="1" @if(old('served_off_premises')==1)) checked @endif> 
                          <label for="served_off_premises"> {{trans('catering_plans.served_off_premises')}}</label>
                        </div>

                        <div class="row" id="setup_max_time_div">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('catering_plans.setup_time')}}</label>
                              <br/>
                              <input name="setup_time" class="form-control off_prem_times" type="number" min=1 value="{{old('setup_time')}}" 
                              @if(old('setup_time')==1)) checked @endif>
                              <select class="form-control off_prem_times" name="setup_time_unit">
                                <option value="minutes" @if(old('setup_time_unit') == 'minutes') selected @endif>{{trans('catering_plans.minutes')}}</option>
                                <option value="hours" @if(old('setup_time_unit') == 'hours') selected @endif>
                                  {{trans('catering_plans.hours')}}
                                </option>
                              </select>
                            </div>  
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('catering_plans.max_time')}}</label>
                              <br/>
                              <input name="max_time" class="form-control off_prem_times" type="number" min=1 value="{{old('max_time')}}" 
                              @if(old('max_time')==1)) checked @endif>
                              <select class="form-control off_prem_times" name="max_time_unit">
                                <option value="minutes" @if(old('max_time_unit') == 'minutes') selected @endif>{{trans('catering_plans.minutes')}}</option>
                                <option value="hours" @if(old('max_time_unit') == 'hours') selected @endif>
                                  {{trans('catering_plans.hours')}}
                                </option>
                              </select>
                            </div>  
                          </div>
                        </div>

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
                </div>
              <div class="modal-footer">
                <button id="edit_btn" type="submit" onclick="return checkfiles();" class="btn btn-info btn-fill btn-wd">{{trans('Submit')}}</button>
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
    

    CKEDITOR.replaceAll();

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

    //upload files limit
    function checkfiles() {
      var $fileUpload = $("input[type='file']");
      if (parseInt($fileUpload.get(0).files.length) > 5){
        alert("You are only allowed to upload a maximum of 5 files");
          return false;
      }
    }

    // off premises
    @if(old('served_off_premises') != 1)
      $('#setup_max_time_div').hide();
    @endif

    $('#served_off_premises').change(function(){
    if(this.checked) {
      $('#setup_max_time_div').show();
    }else{
      $('#setup_max_time_div').hide();
    }
  })
</script>

<!-- <script>
    //Initialize Select2 Elements
    $('#add_food_serving').click(function(){
      var food_serving_input = '<input name="food_serving[]" required class="form-control food_servings" type="text">';
      $('#food_serving_en').append(food_serving_input);
    });
</script> -->
@endsection