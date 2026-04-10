@php
    $total = $paginator->total();
    $perPage = $paginator->perPage();
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();

    $elementsToShow = 2; // Number of pages to show before and after current
    $start = max(1, $currentPage - $elementsToShow);
    $end = min($lastPage, $currentPage + $elementsToShow);

    $query = request()->except('page');
@endphp

@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination">

            <!-- First Page -->
            <li class="{{ $currentPage == 1 ? 'disabled' : '' }}">
                <a href="{{ $currentPage == 1 ? '#' : $paginator->url(1) }}"
                   class="page-item" aria-label="First page">
                    <i class="fa-solid fa-angle-double-left"></i>
                </a>
            </li>

            <!-- Previous Page -->
            <li class="{{ $currentPage == 1 ? 'disabled' : '' }}">
                <a href="{{ $paginator->previousPageUrl() ?? '#' }}"
                   class="page-item" aria-label="Previous page">
                    <i class="fa-solid fa-angle-left"></i>
                </a>
            </li>

            <!-- Page Numbers -->
            @if ($start > 1)
                <li><a href="{{ $paginator->url(1) }}" class="page-item">1</a></li>
                @if ($start > 2)
                    <li><span class="page-ellipsis">&hellip;</span></li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                <li>
                    <a href="{{ $paginator->url($i) }}"
                       class="page-item {{ $i == $currentPage ? 'active' : '' }}"
                       aria-current="{{ $i == $currentPage ? 'page' : false }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor

            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li><span class="page-ellipsis">&hellip;</span></li>
                @endif
                <li><a href="{{ $paginator->url($lastPage) }}" class="page-item">{{ $lastPage }}</a></li>
            @endif

            <!-- Next Page -->
            <li class="{{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $paginator->nextPageUrl() ?? '#' }}"
                   class="page-item" aria-label="Next page">
                    <i class="fa-solid fa-angle-right"></i>
                </a>
            </li>

            <!-- Last Page -->
            <li class="{{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <a href="{{ $currentPage == $lastPage ? '#' : $paginator->url($lastPage) }}"
                   class="page-item" aria-label="Last page">
                    <i class="fa-solid fa-angle-double-right"></i>
                </a>
            </li>
        </ul>
    </nav>
@endif
