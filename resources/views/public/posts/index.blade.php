@extends('layouts.public')
@section('title', 'Главная страница')

@section('content')
    <section class="posts">
        @foreach($posts as $post)
            <article class="post">
                <h2 class="post__title">{{ $post->title }}</h2>
                @if ($post->image)
                    <p class="post__preview">
                        <img src="{{ config('custom.symbolic_images_link') . $post->image }}" alt="" class="post__image">
                    </p>
                @endif
                <div class="post__excerpt">{!! $post->excerpt !!}</div>
                <a href="{{ route('public.posts.show', $post->slug) }}" class="button post__read-more">Читать далее</a>
            </article>
        @endforeach
    </section>

    <div class="pagination">
        {{ $posts->links('vendor.pagination.custom') }}
    </div>
@endsection
