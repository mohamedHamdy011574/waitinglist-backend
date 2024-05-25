@extends('layouts.admin')

@section('css')
<style>
</style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('catering_addons.add_new') }}
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
        {{ trans('catering_addons.add_new') }}
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
            @can('catering-addon-list')
            <ul class="pull-right">
                <a href="{{route('catering_addons.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('catering_addons.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <input class="form-control"  placeholder="{{trans('catering_addons.addon_name')}}" name="addon_name:{{$lk}}" id="addon_name:{{$lk}}" type="text" value="{{old('addon_name:'.$lk)}}" 
                            @if($lk=='en' || $lk=='ar') required @endif>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="description:{{$lk}}" class="content-label">{{trans('catering_addons.description')}}</label>
                            <textarea class="form-control"  placeholder="{{trans('catering_addons.description')}}" name="description:{{$lk}}" id="description:{{$lk}}" @if($lk=='en' || $lk=='ar') required @endif>{{old('description:'.$lk)}}  </textarea>  
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
                          <input name="addon_rate" required class="form-control" type="number" value="{{old('addon_rate')}}">
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
    

    // CKEDITOR.replaceAll();

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

<script>
    //Initialize Select2 Elements
    $('.select2').select2();
    $('#catering_plans_input').attr('autocomplete','off');
</script> -->
@endsection