@extends('layouts.admin')
@section('content')
  <section class="content-header">
    <h1>
      {{trans('setting.heading')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>

      <li class="active"> {{trans('setting.heading')}}</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header">  
            <h3 class="box-title">{{trans('setting.title')}}</h3>  
            <p><a style="font-size: 16px" href="{{asset('admin/settings_module_inputs_manual.pdf')}}" download>{{trans('setting.settings_module_inputs_manual')}} <i class="fa fa-download"></i> </a>   </p>
          </div>
          <form method="POST" action="{{route('settings.update')}}" accept-charset="UTF-8">
            @csrf
            <div class="box-body">
                  @foreach($settings_data as $k=>$v)
                    @if($k == 'default_sort_by')
                      <div class="form-group">
                        <label for="content" class="content-label">{{trans('setting.'.strtolower($k))}}</label>
                        <select class="form-control" name="default_sort_by">
                          <option value="alphabetical" @if($v == 'alphabetical') selected @endif>{{trans('setting.alphabetical')}}</option>
                          <option value="newest" @if($v == 'newest') selected @endif>{{trans('setting.newest')}}</option>
                        </select>
                        <!-- <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('setting.'.strtolower($k))}}" name="{{$k}}" type="text" value="{{$v}}"> -->
                        @error(strtolower($k))
                          <div class="help-block">{{ $message }}</div>
                        @enderror
                      </div>
                    @elseif($k == 'default_order')
                      <div class="form-group">
                        <label for="content" class="content-label">{{trans('setting.'.strtolower($k))}}</label>
                        <select class="form-control" name="default_order">
                          <option value="asc" @if($v == 'asc') selected @endif>{{trans('setting.ascending')}}</option>
                          <option value="desc" @if($v == 'desc') selected @endif>{{trans('setting.descending')}}</option>
                        </select>
                        <!-- <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('setting.'.strtolower($k))}}" name="{{$k}}" type="text" value="{{$v}}"> -->
                        @error(strtolower($k))
                          <div class="help-block">{{ $message }}</div>
                        @enderror
                      </div>
                    @elseif($k == 'currency_exchange_rate')
                      <div class="form-group">
                        <label for="content" class="content-label">{{trans('setting.'.strtolower($k))}}</label>
                        <input class="form-control" placeholder="{{trans('setting.'.strtolower($k))}}" name="{{$k}}" type="number" min="1" value="{{$v}}">
                        @error(strtolower($k))
                          <div class="help-block">{{ $message }}</div>
                        @enderror
                      </div>  
                    @else
                      <div class="form-group">
                        <label for="content" class="content-label">{{trans('setting.'.strtolower($k))}}</label>
                        <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('setting.'.strtolower($k))}}" name="{{$k}}" type="text" value="{{$v}}">
                        @error(strtolower($k))
                          <div class="help-block">{{ $message }}</div>
                        @enderror
                      </div>
                    @endif  
                  @endforeach  
            </div>
            <div class="modal-footer">
              <button id="edit_btn" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div> 
  </section>

  @endsection
           


