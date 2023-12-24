{{-- Previous Page  default--}}
@if ($paginator->onFirstPage())
    <span>&lt;</span>
@else
    <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&lt;</a>
@endif

{{-- Pagination Elements --}}
@foreach ($elements as $element)
    {{-- "Three Dots" Separator --}}
    @if (is_string($element))
        <span>{{ $element }}</span>
    @endif

    {{-- Array Of Links --}}
    @if (is_array($element))
        @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
                <span>{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach
    @endif
@endforeach

{{-- Next Page default --}}
@if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" rel="next">&gt;</a>
@else
    <span>&gt;</span>
@endif