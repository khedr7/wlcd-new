@component('mail::message')
# Welcome, {{$user['fname']}} !!

A new course has been added to our academy : <br>
{{$course['title']}}. 

Check it out.
@component('mail::button', ['url' => asset('course/' . $course['id']. '/'. $course['slug'])])
Click Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
