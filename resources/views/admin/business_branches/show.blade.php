@extends('layouts.admin')

@section('css')
   <style type="text/css">
    .service_type {margin-right: 5px !important;}
    .details{padding: 10px; background: #efebeb}
    /*.rezervation_capacity{width:100px;}*/
    #service_type_div, #payment_options_div {pointer-events: none;}

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
      {{ trans('business_branches.show') }}
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
        {{ trans('business_branches.show') }}
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
            <form method="POST" action="{{route('business_branches.update', $business_branch->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
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
                            <label for="branch_name:{{$lk}}" class="content-label">{{trans('business_branches.branch_name')}}</label>
                            <p class="details">{{$business_branch->translate($lk)->branch_name}}
                            </p>
                          </div>
                        </div>    

                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('business_branches.branch_type')}}</label>
                        @if($business_branch->branch_type == 'restaurant')
                        <p class="details">
                          {{trans('business_branches.restaurant')}}
                        </p>
                        @endif
                         
                        @if($business_branch->branch_type == 'catering') 
                        <p class="details">
                        {{trans('business_branches.catering')}}
                        </p>
                        @endif

                        @if($business_branch->branch_type == 'food_truck')
                        <p class="details">
                        {{trans('business_branches.food_truck')}}
                        </p>
                        @endif
                    </div>

                    @if($subscription->for_restaurant)
                    <div class="form-group" id="service_type_div">
                      <label for="banners" class="content-label">{{trans('business_branches.service_type')}}</label>
                        <div class="row">
                          @if($subscription->reservation)
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="reservation_allow" name="reservation_allow" value="1" @if($business_branch->reservation_allow) checked @endif> 
                            <label for="reservation_allow"> {{trans('business_branches.reservation')}}</label>
                          </div>
                          @endif
                          @if($subscription->waiting_list)
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="waiting_list_allow" name="waiting_list_allow" value="1" @if($business_branch->waiting_list_allow) checked @endif> 
                            <label for="waiting_list_allow"> {{trans('business_branches.waiting_list')}}</label>
                          </div>
                          @endif
                          @if($subscription->pickup)
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="pickup_allow" name="pickup_allow" value="1" 
                            @if($business_branch->pickup_allow) checked @endif> 
                            <label for="pickup_allow"> {{trans('business_branches.pickup')}}</label>
                          </div>
                          @endif
                        </div>
                    </div>
                    @endif
                    
                    <div class="form-group">
                {{Form::label('location', trans('business_branches.address'))}}
                <input type="hidden" name="latitude" id="latitude" value="{{$business_branch->latitude}}">
                <input type="hidden" name="longitude" id="longitude" value="{{$business_branch->longitude}}">
                <div id="map"></div>
              </div> 

                    <div class="form-group">

                        <label for="address_autocom" class="content-label">{{trans('business_branches.address')}}</label>
                        <p class="details"> {{$business_branch->address}}</p>
                        <strong class="help-block"></strong>
                    </div>
                    <div class="form-group">
                      <label for="country" class="content-label">{{trans('business_branches.country')}}</label>
                      <p class="details">
                        {{$country_name}}
                      </p>
                    </div>
                    <div class="form-group">
                      <label for="state" class="content-label">{{trans('business_branches.state')}}</label>
                      <p class="details">
                        {{$state_name}}
                      </p>
                    </div>
                    <input type="hidden" name="city" id="city">
                    
                  </div>
                  <div class="col-md-6">
                    
                    <div class="form-group">
                        <label for="total_seats" class="content-label">{{trans('business_branches.branch_email')}}</label>
                        <p class="details">
                          {{$business_branch->branch_email}}
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="total_seats" class="content-label">{{trans('business_branches.branch_phone_number')}}</label>
                          <p class="details">
                            {{$business_branch->branch_phone_number}}
                          </p>
                        <strong class="help-block"></strong>
                    </div>
                    
                    <div class="form-group" id="pickups_per_hour_div">
                        <label for="pickups_per_hour" class="content-label">{{trans('business_branches.pickups_per_hour')}}</label>
                          <input class="form-control"  placeholder="{{trans('business_branches.pickups_per_hour')}}" name="pickups_per_hour" min=1 max=1000 id="pickups_per_hour" type="number" value="1" required>
                        <strong class="help-block"></strong>
                    </div>
                    <div class="form-group" id="payment_options_div">
                        <label for="total_seats" class="content-label">{{trans('business_branches.payment_options')}}</label>
                        <div class="row">
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="cash_payment_allow" name="cash_payment_allow" value="1" @if($business_branch->cash_payment_allow) checked @endif>
                            <label for="cash_payment_allow"> {{trans('business_branches.cash_payment')}}</label>
                          </div>
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="online_payment_allow" name="online_payment_allow" value="1" @if($business_branch->online_payment_allow) checked @endif> 
                            <label for="online_payment_allow"> {{trans('business_branches.online_payment')}}</label>
                          </div>
                          <div class="col-md-4">
                            <input type="checkbox" class="service_type" id="wallet_payment_allow" name="wallet_payment_allow" value="1" @if($business_branch->wallet_payment_allow) checked @endif>
                            <label for="wallet_payment_allow"> {{trans('business_branches.wallet_payment')}}</label>
                          </div>
                        </div>
                    </div>


                    <div class="form-group">
                      <label for="seating_area" class="content-label">
                        {{trans('seating_area.singular')}}
                      </label>
                      
                        
                        @foreach($seating_areas as $seating_area)
                        
                          @if(collect($selected_seating_areas)->contains($seating_area->id))
                            <p class="details">
                              {{$seating_area->name}}
                            </p>
                          @endif
                        @endforeach
                      
                    </div>

                    
                    <div class="form-group">
                      <label for="status" class="content-label">{{trans('common.status')}}</label>
                      <p class="details">
                        {{$business_branch->status}}
                      </p>
                    </div>
                  </div>
                </div>
              <div class="modal-footer">
                <a class="btn btn-danger btn-fill btn-wd" href="{{route('business_branches.edit',$business_branch->id)}}">
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
    // CKEDITOR.replaceAll();
</script>

<script>
    //Initialize Select2 Elements
    $('.select2').select2();
</script>

<script src="{{ asset('admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>


<script>
  /*code start for autocomplete map*/
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: <?=$business_branch->latitude?>, lng: <?=$business_branch->longitude?>},
          zoom: 13
        });
        var input = document.getElementById('searchMapInput');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
       
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
      
        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            map: map,
            position: {lat: <?=$business_branch->latitude?>, lng: <?=$business_branch->longitude?>},
            anchorPoint: new google.maps.Point(0, -29),
            draggable:true,
            animation: google.maps.Animation.DROP
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
  @if($business_branch->branch_type != 'restaurant')
    $('#service_type_div').hide();
  @endif

  $('#pickups_per_hour_div').hide();

  $('#branch_type').change(function(){
      var type = $(this).val();
      if(type == 'restaurant') {
        $('#service_type_div').show();
      }else{
        $('#service_type_div').hide();
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