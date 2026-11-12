---
title: "{{ $post->title }}"
description: "{{ $post->keyword->sentences(1) }}"
date: "{{ $post->published_at->format('Y-m-d') }}"
categories:
- "{{ config('export.category') }}"
images:
- "{{ $post->keyword->images->shuffle()->random()->url }}"
featuredImage: "{{ $post->keyword->images->shuffle()->random()->url}}"
featured_image: "{{ $post->keyword->images->shuffle()->random()->url }}"
image: "{{ $post->keyword->images->shuffle()->random()->url }}"
---

{!! $post->markdown !!}
