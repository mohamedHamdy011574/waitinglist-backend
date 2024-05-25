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
      {{ trans('waiting_list.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('business_branches.index')}}">{{trans('waiting_list.w_title')}}</a>
      </li>
      <li class="active">
        {{ trans('waiting_list.add_new') }}
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
            <h3 class="box-title">{{ trans('waiting_list.customer_details') }}</h3>
            @can('waiting-list')
            <ul class="pull-right">
                <a href="{{route('waiting_list.index')}}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
            @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('waiting_list.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
              @csrf
              <div class="model-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('waiting_list.customer')}}</label>
                     <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('waiting_list.add_name')}}" name="first_name" type="text" value="{{old('first_name')}}">
                          @if ($errors->has('first_name')) 
                           
                            <strong class="help-block">
                                {{ $errors->first('first_name') }}
                            </strong>
                          @endif
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('waiting_list.contact')}}</label>
                     <input class="form-control" minlength="8" maxlength="15" placeholder="{{trans('waiting_list.add_contact')}}" name="phone_number" type="text" value="{{old('phone_number')}}">
                          @if ($errors->has('phone_number')) 
                           
                            <strong class="help-block">
                                {{ $errors->first('phone_number') }}
                            </strong>
                          @endif
                    </div>
                  </div>
                <!-- </div>
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="link" class="content-label">{{trans('waiting_list.reserve')}}</label>
                     <input class="form-control" min="1" max="50" placeholder="{{trans('waiting_list.add_name')}}" name="reserved_chairs" type="number" value="{{old('reserved_chairs')}}">
                          @if ($errors->has('reserved_chairs')) 
                           
                            <strong class="help-block">
                                {{ $errors->first('reserved_chairs') }}
                            </strong>
                          @endif
                    </div>
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
<script src="{{asset('admin/plugins/js/bootstrap-timepicker.min.js')}}"></script>
<script type="text/javascript">
 $(function () {
    //Timepicker
    $('.timepicker').timepicker();
  });
</script>
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