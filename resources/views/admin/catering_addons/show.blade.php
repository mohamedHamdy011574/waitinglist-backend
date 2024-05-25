@extends('layouts.admin')

@section('css')
<style>
  .details{padding: 10px; background: #efebeb}
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering_addons.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('catering_addons.index')}}">{{trans('catering_addons.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('catering_addons.show') }}
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
            <h3 class="box-title">{{ trans('catering_addons.details') }}</h3>
            @can('restaurant-list')
            <ul class="pull-right">
                <a href="{{route('catering_addons.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('catering_addons.update', $catering_addon->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              <input name="_method" type="hidden" value="PUT">
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
                            <label for="addon_name:{{$lk}}" class="content-label">{{trans('catering_addons.addon_name')}}</label>
                            <p class="details">{{$catering_addon->translate($lk)->addon_name}}
                            </p>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('catering_addons.description')}}</label>
                            <p class="details">{{$catering_addon->translate($lk)->description}}
                            </p>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    
                    <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('catering_addons.addon_rate')}} {{$currency}}</label>
                              <p class="details"> {{$catering_addon->addon_rate}}
                              </p>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="price" class="content-label">{{trans('catering_addons.currency')}}</label>
                              <input name="currency" required class="form-control" readonly type="text" value="{{$currency}}">
                            </div>
                          </div>
                        </div>
                  </div>      
                      <div class="col-md-6">
                        
                        

                        <div class="form-group">
                          <label for="status" class="content-label">{{trans('common.status')}}</label>
                         
                              @if($catering_addon->status == 'active')
                              <p class="details">{{trans('common.active')}}</p>
                              @endif
                            
                              @if($catering_addon->status == 'inactive')
                              <p class="details">{{trans('common.inactive')}}</p>
                              @endif
                        </div>
                        

                    </div>
                </div>
              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('catering_addons.edit',$catering_addon->id)}}">
                  {{trans('common.edit')}}
                </a>
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