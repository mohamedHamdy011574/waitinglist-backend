<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <h1>{{trans('vendors.email_messages.hello')}} {{ $details['name'] }}</h1>
    <p>{{trans('vendors.email_messages.congratulations_for_registration')}}</p>
    <p>{{trans('vendors.email_messages.login_link')}} <a href="{{route('home')}}">{{trans('vendors.email_messages.link')}}</a>
    <p>{{trans('vendors.email_messages.use_this_credentials')}} </p>
  	<p>{{trans('vendors.email_messages.email')}} {{ $details['email'] }} <br/>{{trans('vendors.email_messages.password')}} {{ $details['password'] }}</p>
   
    <p>{{trans('vendors.email_messages.thankyou')}}</p>
</body>
</html>