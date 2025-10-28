@if ($paginator->hasPages())
    <nav class="pagination">
        <ul class="pagination__list">
            @if ($paginator->onFirstPage())
                <li class="pagination__item pagination__item_disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="pagination__link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="pagination__item">
                    <a class="pagination__link pagination__link_prev" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="pagination__item pagination__item_disabled" aria-disabled="true">
                        <span class="pagination__link">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination__item pagination__item_active" aria-current="page">
                                <span class="pagination__link pagination__link_current">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination__item">
                                <a class="pagination__link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="pagination__item">
                    <a class="pagination__link pagination__link_next" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="pagination__item pagination__item_disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="pagination__link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif

        </ul>
    </nav>
@endif
