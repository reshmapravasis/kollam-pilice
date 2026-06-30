<x-mail::message>
# New Inquiry Received

You have received a new inquiry from the website contact form.

**Name:** {{ $inquiry['name'] }}  
**Email:** {{ $inquiry['email'] }}  
@if(!empty($inquiry['phone']))
**Phone:** {{ $inquiry['phone'] }}  
@endif
@if(!empty($inquiry['subject']))
**Subject:** {{ $inquiry['subject'] }}  
@endif

**Message:**  
{{ $inquiry['message'] }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
