@if(!empty($post->ai_title))
{!! $post->ai_title !!}
@else
@php
    $suffix = $post->keyword->sentences(1);
@endphp
@if($suffix)
{!! ucwords($post->keyword->name) !!} - {!! $suffix !!}
@else
{!! ucwords($post->keyword->name) !!}
@endif
@endif
