@component('mail::message')
# Welcome, {{$user['fname']}} !!

{{$course['title']}}
course will start tomorrow !! <br>

Get ready.
@component('mail::button', ['url' => asset('course/' . $course['id']. '/'. $course['slug'])])
Click Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
