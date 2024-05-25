@extends('layouts.admin')

@section('css')
   <style type="text/css">
    .service_type {margin-right: 5px !important;}
    #map {
      width: 100%;
      height: 400px;
    }
    .mapControls {
      margin-top: 10px;
      border: 1px solid transparent;
      border-radius: 2px 0 0 2px;
      box-sizing: border-box;
      -moz-box-sizing: border-box;
      height: 32px;
      outline: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #searchMapInput {
      background-color: #fff;
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
      margin-left: 12px;
      padding: 0 11px 0 13px;
      text-overflow: ellipsis;
      width: 50%;
    }
    #searchMapInput:focus {
      border-color: #4d90fe;
    }

    /*.rezervation_capacity{width:100px;}*/
   </style>
@endsection

@section('content')
  <section class="content-header">
    <h1>
      {{ trans('business_branches.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('business_branches.index')}}">{{trans('business_branches.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('business_branches.add_new') }}
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
            <h3 class="box-title">{{ trans('business_branches.details') }}</h3>
            @can('restaurant-list')
            <ul class="pull-right">
                <a href="{{route('business_branches.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('business_branches.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="branch_name:{{$lk}}" class="content-label">{{trans('business_branches.branch_name')}}</label>
                            <input class="form-control"  placeholder="{{trans('business_branches.branch_name')}}" name="branch_name:{{$lk}}" id="branch_name:{{$lk}}" type="text" value="{{old('branch_name:'.$lk)}}"
                            @if($lk == 'en' ) required @endif >
                            <strong class="help-block">
                                {{ @$errors->first('branch_name:'.$lk) }}
                            </strong>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('business_branches.branch_type')}}</label>
                      <select class="form-control" id="branch_type" name="branch_type">
                        <option value="">
                          {{trans('business_branches.select_branch_type')}}
                        </option>
                        @if($subscription->for_restaurant)
                        <option value="restaurant" 
                        {{ ((old('branch_type') == 'restaurant')) ? 'selected':'' }}
                        >{{trans('business_branches.restaurant')}}</option>
                        @endif

                        @if($subscription->for_catering && $catering_option == 1)
                        <option value="catering" 
                        {{ ((old('branch_type') == 'catering')) ? 'selected':'' }}>{{trans('business_branches.catering')}}</option>
                        @endif

                        @if($subscription->for_food_truck)
                        <option value="food_truck" {{ ((old('branch_type') == 'food_truck')) ? 'selected':'' }}>{{trans('business_branches.food_truck')}}</option>
                        @endif
                        
                      </select>
                    </div>

                    @if($subscription->for_restaurant)
                    <div class="form-group" id="service_type_div">
                      <label for="banners" class="content-label">{{trans('business_branches.service_type')}}</label>
                        <div class="row">
                          @if($subscription->reservation)
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="reservation_allow" name="reservation_allow" value="1" @if(old('reservation_allow')) checked @endif> 
                            <label for="reservation_allow"> {{trans('business_branches.reservation')}}</label>
                          </div>
                          @endif
                          @if($subscription->waiting_list)
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="waiting_list_allow" name="waiting_list_allow" value="1" @if(old('waiting_list_allow')) checked @endif> 
                            <label for="waiting_list_allow"> {{trans('business_branches.waiting_list')}}</label>
                          </div>
                          @endif
                          @if($subscription->pickup)
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="pickup_allow" name="pickup_allow" value="1" 
                            @if(old('pickup_allow')) checked @endif> 
                            <label for="pickup_allow"> {{trans('business_branches.pickup')}}</label>
                          </div>
                          @endif
                        </div>
                    </div>
                    @endif

                    @if($subscription->for_catering)
                    <div id="delivery_info_div">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="min_notice" class="content-label">{{trans('business_branches.min_notice')}}</label>
                              <input type="number" class="form-control" id="min_notice" min="1" name="min_notice" value="{{old('min_notice')}}"> 
                            <strong class="help-block"></strong>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="min_order" class="content-label">{{trans('business_branches.min_order', ['currency' => App\Models\Setting::get('currency')])}}</label>
                            <input type="number" class="form-control" id="min_notice" min="1" name="min_order" value="{{old('min_order')}}"> 
                            <strong class="help-block"></strong>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="delivery_charge" class="content-label">{{trans('business_branches.delivery_charge')}}</label>
                            <select name="delivery_charge" class="form-control">
                              <option value="by_area">{{trans('business_branches.by_area')}}</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endif
                    
                    <div class="form-group">
                {{Form::label('location', trans('business_branches.address'))}}
                <input id="searchMapInput" class="mapControls" type="text" value="{{ old('location_policy_address') }}" placeholder="{{trans('business_branches.address')}}">
                <input type="hidden" name="latitude" id="latitude" value="{{old('latitude')}}">
                <input type="hidden" name="longitude" id="longitude" value="{{old('longitude')}}">
                <div id="map"></div>
              </div> 

                    <div class="form-group">

                        <label for="address_autocom" class="content-label">{{trans('business_branches.address')}}</label>
                        <textarea class="form-control" name="address" id="address_autocom">{{old('address')}}</textarea>
                        <strong class="help-block"></strong>
                    </div>
                    <div class="form-group">
                      <label for="country" class="content-label">{{trans('business_branches.country')}}</label>
                      <select class="form-control" id="country" name="country_id" data-placeholder="{{trans('business_branches.select_country')}}" required>
                        <option value="">{{trans('business_branches.select_country')}}
                        </option>
                        @foreach($countries as $country)
                        <option value="{{$country->id}}"
                          {{ ((old('country_id') == $country->id)) ? 'selected':'' }}
                          >{{$country->country_name}}
                        </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="state" class="content-label">{{trans('business_branches.state')}}</label>
                        
                      <select class="form-control" id="state" name="state_id" data-placeholder="{{trans('business_branches.select_state')}}" required>
                        <option value="">{{trans('business_branches.select_state')}}
                        </option>  
                      </select>
                    </div>
                    <input type="hidden" name="city" id="city">
                    
                  </div>
                  <div class="col-md-6">
                    
                    <div class="form-group">
                        <label for="total_seats" class="content-label">{{trans('business_branches.branch_email')}}</label>
                          <input class="form-control"  placeholder="{{trans('business_branches.branch_email')}}" name="branch_email" id="branch_email" type="email" value="{{old('branch_email')}}" required>
                        <strong class="help-block"></strong>
                    </div>
                    <div class="form-group">
                        <label for="total_seats" class="content-label">{{trans('business_branches.branch_phone_number')}}</label>
                          <input class="form-control"  placeholder="{{trans('business_branches.branch_phone_number')}}" name="branch_phone_number" id="branch_phone_number" type="number" min="1" value="{{old('branch_phone_number')}}" required>
                        <strong class="help-block"></strong>
                    </div>
                    
                    <div class="form-group" id="pickups_per_hour_div">
                        <label for="pickups_per_hour" class="content-label">{{trans('business_branches.pickups_per_hour')}}</label>
                          <input class="form-control"  placeholder="{{trans('business_branches.pickups_per_hour')}}" name="pickups_per_hour" min=1 max=1000 id="pickups_per_hour" type="number" value="1" required>
                        <strong class="help-block"></strong>
                    </div>
                    <div class="form-group">
                        <label for="total_seats" class="content-label">{{trans('business_branches.payment_options')}}</label>
                        <div class="row">
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="cash_payment_allow" name="cash_payment_allow" value="1" @if(old('cash_payment_allow')) checked @endif>
                            <label for="cash_payment_allow"> {{trans('business_branches.cash_payment')}}</label>
                          </div>
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="online_payment_allow" name="online_payment_allow" value="1" @if(old('online_payment_allow')) checked @endif> 
                            <label for="online_payment_allow"> {{trans('business_branches.online_payment')}}</label>
                          </div>
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="wallet_payment_allow" name="wallet_payment_allow" value="1" @if(old('wallet_payment_allow')) checked @endif>
                            <label for="wallet_payment_allow"> {{trans('business_branches.wallet_payment')}}</label>
                          </div>
                        </div>
                    </div>


                    <div class="form-group" id="seating_area_div">
                      <label for="seating_area" class="content-label">
                        {{trans('seating_area.singular')}}
                      </label>
                      <select class="form-control select2" id="seating_area" name="stg_area_id[]" multiple data-placeholder="{{trans('seating_area.select_seating_area')}}">
                        <option value="">{{trans('seating_area.select_seating_area')}}
                        </option>
                        @foreach($seating_areas as $seating_area)
                        <option value="{{$seating_area->id}}"
                          {{ (collect(old('stg_area_id'))->contains($seating_area->id)) ? 'selected':'' }}
                          >{{$seating_area->name}}
                        </option>
                        @endforeach
                      </select>
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
</script>

<script src="{{ asset('admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>

<script>
  $( document ).ready(function() {
    get_stateList('{{old("country_id")}}');
    setTimeout(function(){ 
      $("#country option[value='{{old('country_id')}}']").prop('selected', true);
      $("#state option[value='{{old('state_id')}}']").prop('selected', true);
    }, 3000);
  });

  function get_stateList(countryID = null){
    var url = '{{ route("state.list", ["coutry_id" => ":coutry_id"]) }}';  
    if(countryID){
      $.ajax({
        type:"GET",
        url:url.replace(':coutry_id', countryID),
        success:function(res){   
        if(res.success == "1"){
          $("#state").empty()
          //$("#state").append('<option>Select</option>');
          $.each(res.data,function(key,value){
            $("#state").append('<option value="'+key+'">'+value+'</option>');
          });
        } else {
          toaster.error(res.message);
          $("#state").empty();
        }
         }
      });
    } else{
      $("#state2").empty();
      $("#city").empty();
    }
  }
</script>


<script>
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
              $("#state").html('<option value="">{{trans("business_branches.select_state")}}                     </option>');
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

<script>
  /*code start for autocomplete map*/
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 29.31166, lng: 47.481766},
          zoom: 13
        });
        var input = document.getElementById('searchMapInput');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
       
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
      
        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            map: map,
            position: {lat: 29.31166, lng: 47.481766},
            anchorPoint: new google.maps.Point(0, -29),
            draggable:true,
            animation: google.maps.Animation.DROP
        });

        //prevent auto submit    
        var input = document.getElementById('searchMapInput');
        google.maps.event.addDomListener(input, 'keydown', function(e) {
          if (e.keyCode == 13)
          {
            if (e.preventDefault)
            {
              e.preventDefault();
            }
          }
        });  
        
        // Pointer movemnt listener
            google.maps.event.addListener(marker, 'dragend', function(evt){
              $('#latitude').val(evt.latLng.lat().toFixed(3));
              $('#longitude').val(evt.latLng.lng().toFixed(3))
            });
      
        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
        
            /* If the place has a geometry, then present it on a map. */
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setIcon(({
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
          
            var address = '';
            if (place.address_components) {
                address = [
                  (place.address_components[0] && place.address_components[0].short_name || ''),
                  (place.address_components[1] && place.address_components[1].short_name || ''),
                  (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }
          
            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infowindow.open(map, marker);
            
            /* Location details */
            $("textarea[name='address']").val(place.formatted_address);
            $('#latitude').val(place.geometry.location.lat())
            $('#longitude').val(place.geometry.location.lng())
        });
    }
  /*code end for autocomplete map*/
</script>

<script>
  @if(old('branch_type') != 'restaurant')
    $('#service_type_div').hide();
  @endif

  @if(old('branch_type') != 'catering')
    $('#delivery_info_div').hide();
  @endif

  @if(old('branch_type') == 'food_truck')
    $('#seating_area_div').hide();
  @endif

  $('#pickups_per_hour_div').hide();

  $('#branch_type').change(function(){
      var type = $(this).val();

      if(type == 'restaurant') {
        $('#service_type_div').show();
      }else{
        $('#service_type_div').hide();
      }

      if(type == 'catering') {
        $('#delivery_info_div').show();
      }else{
        $('#delivery_info_div').hide();
      }

      if(type == 'food_truck') {
        $('#seating_area_div').hide();
      }else{
        $('#seating_area_div').show();
      }
  })

  $('#pickup_allow').change(function(){
    if(this.checked) {
      $('#pickups_per_hour_div').show();
    }else{
      $('#pickups_per_hour_div').hide();
    }
  })
</script>

<?php 
    $google_map_api_key = App\Models\Setting::get('google_API_key');
?>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initMap&key=<?php echo $google_map_api_key; ?>" async defer></script>
@endsection