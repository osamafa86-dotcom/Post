@php
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $elementsToShow = 2; // Number of pages to show before/after current
    $start = max(1, $currentPage - $elementsToShow);
    $end = min($lastPage, $currentPage + $elementsToShow);
@endphp

@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="custom-pagination">

            {{-- Previous --}}
            <li class="{{ $currentPage == 1 ? 'disabled' : '' }}">
                <button wire:click="previousPage" class="page-link prev" aria-label="Previous">
                    <i class="fa-solid fa-angle-left"></i>
                </button>
            </li>


            {{-- First page + ellipsis --}}
            @if ($start > 1)
                <li>
                    <button wire:click="gotoPage(1)" class="page-link">1</button>
                </li>
                @if ($start > 2)
                    <li><span class="page-ellipsis">…</span></li>
                @endif
            @endif


            {{-- Page range --}}
            @for ($i = $start; $i <= $end; $i++)
                <li>
                    <button wire:click="gotoPage({{ $i }})"
                            class="page-link {{ $i == $currentPage ? 'active' : '' }}">
                        {{ $i }}
                    </button>
                </li>
            @endfor

            {{-- Ellipsis + last page --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li><span class="page-ellipsis">…</span></li>
                @endif
                <li>
                    <button wire:click="gotoPage({{ $lastPage }})"
                            class="page-link">
                        {{ $lastPage }}
                    </button>
                </li>
            @endif


            {{-- Next --}}
            <li class="{{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <button wire:click="nextPage" class="page-link next" aria-label="Next">
                    <i class="fa-solid fa-angle-right"></i>
                </button>
            </li>

        </ul>
    </nav>
@endif
