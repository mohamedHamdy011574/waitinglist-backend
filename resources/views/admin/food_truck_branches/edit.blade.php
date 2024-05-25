@extends('layouts.admin')

@section('css')
   <style type="text/css">
    .timing_heading{padding: 10px; font-size: 15px; text-align:center; font-weight: 600; background: #e5e5e5}
    .food_truck_branch_working_hours_tbl tr td input{background: #eee; border: none; padding: 5px 20px;}
    #monday_onoff, #tuesday_onoff, #wednesday_onoff, #thursday_onoff, #friday_onoff, #saturday_onoff, #sunday_onoff { background: #eee; border: none; padding: 7px 20px; }
    /*.rezervation_capacity{width:100px;}*/
   </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('food_truck_branches.edit') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('food_truck_branches.index')}}">{{trans('food_truck_branches.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('food_truck_branches.edit') }}
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
            <h3 class="box-title">{{ trans('food_truck_branches.details') }}</h3>
            @can('food-truck-list')
            <ul class="pull-right">
                <a href="{{ redirect()->getUrlGenerator()->previous() }}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('food_truck_branches.update', $food_truck_branch->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
              <input name="_method" type="hidden" value="PUT">
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
                            <label for="name:{{$lk}}" class="content-label">{{trans('food_truck_branches.name')}}</label>
                            <input class="form-control"  placeholder="{{trans('food_truck_branches.name')}}" name="name:{{$lk}}" id="name:{{$lk}}" type="text" value="{{$food_truck_branch->translate($lk)->name}}" required>
                            <strong class="help-block"></strong>
                          </div>
                          <div class="form-group">
                            <label for="address:{{$lk}}" class="content-label">{{trans('food_truck_branches.address')}}</label>
                            <textarea class="form-control" rows="5" placeholder="{{trans('food_truck_branches.address')}}" name="address:{{$lk}}" id="address:{{$lk}}" required>{{$food_truck_branch->translate($lk)->address}}</textarea>
                            <strong class="help-block"></strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>

                    <div class="form-group">
                      <label for="cuisines" class="content-label">
                        {{trans('food_truck_branches.food_truck')}}
                      </label>
                      <select class="form-control select2" id="food_truck" name="food_truck_id" data-placeholder="{{trans('food_truck_branches.select_food_trucks')}}" required>
                        @foreach($food_trucks as $food_truck)
                        <option value="{{$food_truck->id}}"
                          {{ ($food_truck_branch->food_truck_id == $food_truck->id) ? 'selected':'' }}
                          >{{$food_truck->name}}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="country" class="content-label">
                        {{trans('food_truck_branches.country')}}
                      </label>
                        <?php //echo "<pre>asdsa";print_r($selected_country);exit; ?>
                      <select class="form-control select2" id="country" name="country_id" data-placeholder="{{trans('food_truck_branches.select_country')}}" required>
                        <option value="">{{trans('food_truck_branches.select_country')}}
                        </option>
                        @foreach($countries as $ctry)
                        <option value="{{$ctry->id}}"
                          {{ ($selected_country->country->id == $ctry->id) ? 'selected':'' }}
                          >{{$ctry->country_name}}
                        </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="states" class="content-label">
                        {{trans('food_truck_branches.state')}}
                      </label>
                      <select class="form-control select2" id="state" name="state_id" data-placeholder="{{trans('food_truck_branches.select_state')}}" required>
                        @foreach($states as $state)
                        <option value="{{$state->id}}"
                          {{ ($food_truck_branch->state_id == $state->id) ? 'selected':'' }}
                          >{{$state->name}}
                        </option>
                        @endforeach
                      </select>
                    </div> 

                    <div class="form-group">
                      <label for="status" class="content-label">{{trans('common.status')}}</label>
                      <select class="form-control" name="status" id="status" required>
                        <option value="active" 
                          @if($food_truck_branch->status == 'active') selected @endif>
                          {{trans('common.active')}}
                        </option>
                        <option value="inactive" 
                          @if($food_truck_branch->status == 'inactive') selected @endif>
                          {{trans('common.inactive')}}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              <div class="modal-footer">
                <button id="submit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('Submit')}}</button>
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
</script>

<script>
    //Initialize Select2 Elements
    $('.select2').select2();


    // WHEN CLICK 'For all'
        $('#from_to_for_all').click(function(){
            var fa_from = $('#1_from').val();
            var fa_to = $('#1_to').val();
            var fa_reservations_capacity = $('#1_reservations_capacity').val();
            $('#2_from,#3_from,#4_from,#5_from').val(fa_from);
            $('#2_to,#3_to,#4_to,#5_to').val(fa_to);
            $('#2_reservations_capacity,#3_reservations_capacity,#4_reservations_capacity,#5_reservations_capacity').val(fa_reservations_capacity);
            $('#monday_onoff,#tuesday_onoff,#wednesday_onoff,#thursday_onoff,#friday_onoff').val('on');
        })

        // monday_onoff DropDown List
        $('#monday_onoff').change(function(){
            if($(this).val() == 'on'){
                var fa_from = $('#1_from').val();
                var fa_to = $('#1_to').val();
                var fa_reservations_capacity = $('#1_reservations_capacity').val();
                $('#1_from').val(fa_from);
                $('#1_to').val(fa_to);
                $('#1_reservations_capacity').val(fa_reservations_capacity);
                $('#1_from, #1_to').attr('required','required');
            }else{
                $('#1_from').val('');
                $('#1_to').val('');
                $('#1_reservations_capacity').val('');
                $('#1_from, #1_to').removeAttr('required');
            }
        })

        // tuesday_onoff DropDown List
        $('#tuesday_onoff').change(function(){
            if($(this).val() == 'on'){
                var fa_from = $('#1_from').val();
                var fa_to = $('#1_to').val();
                var fa_reservations_capacity = $('#1_reservations_capacity').val();
                $('#2_from').val(fa_from);
                $('#2_to').val(fa_to);
                $('#2_reservations_capacity').val(fa_reservations_capacity);
                $('#2_from, #2_to').attr('required','required');
            }else{
                $('#2_from').val('');
                $('#2_to').val('');
                $('#2_reservations_capacity').val('');
                $('#2_from, #2_to').removeAttr('required');
            }
        })

        // wednesday_onoff DropDown List
        $('#wednesday_onoff').change(function(){
            if($(this).val() == 'on'){
                var fa_from = $('#1_from').val();
                var fa_to = $('#1_to').val();
                var fa_reservations_capacity = $('#1_reservations_capacity').val();
                $('#3_from').val(fa_from);
                $('#3_to').val(fa_to);
                $('#3_reservations_capacity').val(fa_reservations_capacity);
                $('#3_from, #3_to').attr('required','required');
            }else{
                $('#3_from').val('');
                $('#3_to').val('');
                $('#3_reservations_capacity').val('');
                $('#3_from, #3_to').removeAttr('required');
            }
        })

        // thursday_onoff DropDown List
        $('#thursday_onoff').change(function(){
            if($(this).val() == 'on'){
                var fa_from = $('#1_from').val();
                var fa_to = $('#1_to').val();
                var fa_reservations_capacity = $('#1_reservations_capacity').val();
                $('#4_from').val(fa_from);
                $('#4_to').val(fa_to);
                $('#4_reservations_capacity').val(fa_reservations_capacity);
                $('#4_from, #4_to').attr('required','required');
            }else{
                $('#4_from').val('');
                $('#4_to').val('');
                $('#4_reservations_capacity').val('');
                $('#4_from, #4_to').removeAttr('required');
            }
        })

        // friday_onoff DropDown List
        $('#friday_onoff').change(function(){
            if($(this).val() == 'on'){
                var fa_from = $('#1_from').val();
                var fa_to = $('#1_to').val();
                var fa_reservations_capacity = $('#1_reservations_capacity').val();
                $('#5_from').val(fa_from);
                $('#5_to').val(fa_to);
                $('#5_reservations_capacity').val(fa_reservations_capacity);
                $('#5_from, #5_to').attr('required','required');
            }else{
                $('#5_from').val('');
                $('#5_to').val('');
                $('#5_reservations_capacity').val('');
                $('#5_from, #5_to').removeAttr('required');
            }
        })

        // saturday_onoff DropDown List
        $('#saturday_onoff').change(function(){
            if($(this).val() == 'on'){
                var fa_from = $('#1_from').val();
                var fa_to = $('#1_to').val();
                var fa_reservations_capacity = $('#1_reservations_capacity').val();
                $('#6_from').val(fa_from);
                $('#6_to').val(fa_to);
                $('#6_reservations_capacity').val(fa_reservations_capacity);
                $('#6_from, #6_to').attr('required','required');
            }else{
                $('#6_from').val('');
                $('#6_to').val('');
                $('#6_reservations_capacity').val('');
                $('#6_from, #6_to').removeAttr('required');
            }
        })

        // SUNDAY_onoff DropDown List
        $('#sunday_onoff').change(function(){
            if($(this).val() == 'on'){
                var fa_from = $('#1_from').val();
                var fa_to = $('#1_to').val();
                var fa_reservations_capacity = $('#1_reservations_capacity').val();
                $('#0_from').val(fa_from);
                $('#0_to').val(fa_to);
                $('#0_reservations_capacity').val(fa_reservations_capacity);
                $('#0_from, #0_to').attr('required','required');
            }else{
                $('#0_from').val('');
                $('#0_to').val('');
                $('#0_reservations_capacity').val('');
                $('#0_from, #0_to').removeAttr('required');
            }
        })

        $(document).ready(function() {
            // WHEN CLICK ON OFF DAY From and To hours
            $('[id=1_from]').click(function(){
                console.log($(this).val());
            })
            $('[id$=_from]').keypress(function(){
                $(this).parent().prev().children('select').val('on');
            })
        })
</script>

<script>
  $(document).ready(function(){
    if($('#monday_onoff').val() == 'on'){
      $('#1_from, #1_to').attr('required','required');
    }
    if($('#tuesday_onoff').val() == 'on'){
      $('#2_from, #2_to').attr('required','required');
    }
    if($('#wednesday_onoff').val() == 'on'){
      $('#3_from, #3_to').attr('required','required');
    }
    if($('#thursday_onoff').val() == 'on'){
      $('#4_from, #4_to').attr('required','required');
    }
    if($('#friday_onoff').val() == 'on'){
      $('#5_from, #5_to').attr('required','required');
    }
    if($('#saturday_onoff').val() == 'on'){
      $('#6_from, #6_to').attr('required','required');
    }
    if($('#sunday_onoff').val() == 'on'){
      $('#0_from, #0_to').attr('required','required');
    }
  })
  // $('#submit_btn').click(function() {
  //   if($('#monday_onoff').val() == 'on'){
  //     $('#1_from').
  //   }
  // })
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
              $("#state").html('<option value="">{{trans("food_truck_branches.select_state")}}                     </option>');
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
</script>
@endsection