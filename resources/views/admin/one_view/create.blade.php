@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{ trans('reservations.add_new') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('reservations.index')}}">{{trans('reservations.singular')}}</a></li>
      <li class="active">{{ trans('reservations.add_new') }} </li>
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
                <h3 class="box-title">{{ trans('reservations.details') }}</h3>
                @can('reservation-list')
                <ul class="pull-right">
                    <a href="{{route('reservations.index')}}" class="btn btn-danger">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
                @endcan
            </div>
          <div class="box-body">
            <form method="POST" id="reservation_form" action="{{route('reservations.store')}}" accept-charset="UTF-8">
              @csrf
              <div class="model-body">
                <div class="row">
                  
                  <div class="col-md-6">
                      <div class="form-group">
                        <label for="customer_id"> {{trans('reservations.customer')}}</label>
                        <select name="customer_id" id="customer_id" class="form-control select2">
                          <option value="">{{trans('reservations.select_customer')}}</option>
                          @foreach($customers as $cust)
                          <option value="{{$cust->id}}" 
                            {{ ((old('customer_id') == $cust->id)) ? 'selected':'' }}
                          >{{$cust->first_name}}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="form-group">
                        <label for="restaurant_id"> {{trans('reservations.restaurants')}}</label>
                        <select name="restaurant_id" id="restaurant_id" class="form-control select2">
                          <option value="">{{trans('reservations.select_restaurant')}}</option>
                          @foreach($restaurants as $rest)
                          <option value="{{$rest->id}}" 
                            {{ ((old('restaurant_id') == $rest->id)) ? 'selected':'' }}
                          >{{$rest->name}}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="form-group">
                        <label for="rest_branch_id"> {{trans('reservations.rest_branches')}}</label>
                        <select name="rest_branch_id" id="rest_branch_id" class="form-control select2">
                          <option value="">{{trans('reservations.select_rest_branch')}}</option>                          
                        </select>
                      </div>                                     
                  </div>
                    
                  <div class="col-md-6">
                    <div class="form-group">
                        <label for="no_of_persons"> {{trans('reservations.no_of_persons')}}</label>
                        <input class="form-control" placeholder="{{trans('reservations.no_of_persons')}}" required="true" name="reserved_chairs" type="number" id="reserved_chairs" value="{{old('reserved_chairs')}}">
                      </div> 

                      <div class="form-group">
                        <label for="check_in_date"> {{trans('reservations.check_in_date')}}</label>
                        <input class="form-control datetimepicker" placeholder="{{trans('reservations.check_in_date')}}" required="true" name="check_in_date" type="text" id="check_in_date" value="{{old('check_in_date')}}" autocomplete="off">
                      </div> 

                      <div class="form-group">
                        <label for="status"> {{trans('common.status')}}</label>
                        <select name="status" id="status" class="form-control select2">              
                          <option value="reserved">Reserved</option>                          
                          <option value="cancelled">Cancelled</option>                        
                          <option value="checked_in">Checked-in</option>                      
                          <option value="checked_out">Checked-out</option>                      
                        </select>
                      </div>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
@section('css')
@endsection   

@section('js')
  
  <script type="text/javascript">
    $('.select2').select2();

    //DateTimepicker
    $(".datetimepicker").datetimepicker({
        format: 'YYYY-MM-DD H:mm',
        icons:{
            time: 'glyphicon glyphicon-time',
            date: 'glyphicon glyphicon-calendar',
            previous: 'glyphicon glyphicon-chevron-left',
            next: 'glyphicon glyphicon-chevron-right',
            today: 'glyphicon glyphicon-screenshot',
            up: 'glyphicon glyphicon-chevron-up',
            down: 'glyphicon glyphicon-chevron-down',
            clear: 'glyphicon glyphicon-trash',
            close: 'glyphicon glyphicon-remove'
        },
        locale : '{{config("app.locale")}}'
    });

    $(document).on('change','#restaurant_id',function(){
        var rest_id = $(this).val();
        var id = $(this).attr('id');
        var delay = 500;
        var element = $(this);
        $.ajax({
            type:'post',
            url: "{{route('rest.rest_branches')}}",
            data: {
                    "rest_id": rest_id, 
                    "id" : id,  
                    "_token": "{{ csrf_token() }}"
            },
            success: function (data) {
              var rest_branches = JSON.parse(data);
              $("#rest_branch_id").html('<option value="">{{trans("reservations.select_rest_branch")}}                     </option>');
              $.each(rest_branches,function(key, val){
                $("#rest_branch_id").append("<option value='"+val.id+"'>"+val.name+"</option>");
              })
              console.log(data);
            },
            error: function () {
              toastr.error(data.error);
            }
        })
    });
</script>
@endsection