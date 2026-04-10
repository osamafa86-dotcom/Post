@php
    $total = $paginator->total();
    $perPage = $paginator->perPage();
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $elementsToShow = 2;

    $start = max(1, $currentPage - $elementsToShow);
    $end = min($lastPage, $currentPage + $elementsToShow);
@endphp

@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">

            {{-- Previous --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <button class="page-link"
                        wire:click="previousPage"
                        @if($paginator->onFirstPage()) disabled @endif>
                    السابق
                </button>
            </li>

            {{-- Leading --}}
            @if ($start > 1)
                <li class="page-item">
                    <button class="page-link" wire:click="gotoPage(1)">1</button>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                @endif
            @endif

            {{-- Pages --}}
            @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <button class="page-link" wire:click="gotoPage({{ $i }})">{{ $i }}</button>
                </li>
            @endfor

            {{-- Trailing --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                @endif
                <li class="page-item">
                    <button class="page-link" wire:click="gotoPage({{ $lastPage }})">{{ $lastPage }}</button>
                </li>
            @endif

            {{-- Next --}}
            <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <button class="page-link"
                        wire:click="nextPage"
                        @if(!$paginator->hasMorePages()) disabled @endif>
                    التالي
                </button>
            </li>

        </ul>
    </nav>
@endif
