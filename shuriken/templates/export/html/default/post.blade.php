@extends('export.html.' .  config('export.theme') . '.layout')

@section('title')
    {{ $post->title }}
@endsection

@section('head')
{!! $post->json_ld !!}
@endsection

@section('description')
    {{ $post->keyword->sentences(1) }}
@endsection

@section('content')
{!! $post->content !!}

<section>
    <article>
        <p>
            @if($post->previous)
                <a href="{{ $post->previous->slug }}.html"><i>&larr; {{ $post->previous->title }}</i></a>
            @endif

            @if($post->parent)
                <a href="{{ $post->parent->slug }}.html"><i>{{ $post->parent->title }}</i></a>
            @endif

            @if($post->next)
                <a href="{{ $post->next->slug }}.html"><i>{{ $post->next->title }} &rarr;</i></a>
            @endif
        </p>
    </article>
</section>

@endsection
