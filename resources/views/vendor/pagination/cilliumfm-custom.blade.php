@php
    /** ------------------------------
     * Pagination configuration values
     * ------------------------------ */
    $total       = $paginator->total();                 // Total number of items
    $perPage     = $paginator->perPage();               // Items per page
    $current     = max(1, $paginator->currentPage());   // Current page (minimum 1)
    $last        = max(1, $paginator->lastPage());      // Last available page
    $window      = 2;                                   // Number of pages shown around current page

    /** ------------------------------
     * Calculate the item range being displayed
     * ------------------------------ */
    if ($total === 0) {
        $start = 0;
        $end   = 0;
    } else {
        $start = ($current - 1) * $perPage + 1;
        $end   = min($current * $perPage, $total);
    }

    /** ------------------------------
     * Generate pages to display with ellipses
     * ------------------------------ */
    $pages = [];

    // Add first page and left ellipsis if needed
    if ($current - $window > 1) {
        $pages[] = 1;
        if ($current - $window > 2) $pages[] = '...left';
    }

    // Add window pages around the current page
    for ($i = max(1, $current - $window); $i <= min($last, $current + $window); $i++) {
        $pages[] = $i;
    }

    // Add right ellipsis and last page if needed
    if ($current + $window < $last) {
        if ($current + $window < $last - 1) $pages[] = '...right';
        $pages[] = $last;
    }

    // Optional: custom paginator name (if multiple paginators exist on the page)
    // $pageName = 'searchPage';
@endphp

@if ($paginator->hasPages())
    <section class="pagination-section text-center">
        <nav aria-label="Search results pagination">
            <ul class="pagination justify-content-center">

                {{-- Previous button (always visible) --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <button class="page-link"
                            rel="prev"
                            wire:click="previousPage{{ isset($pageName) ? "('{$pageName}')" : '' }}"
                            @if($paginator->onFirstPage()) disabled @endif>
                        السابق
                    </button>
                </li>

                {{-- First page shortcut (desktop only) --}}
                @if(!$paginator->onFirstPage())
                    <li class="page-item only-desktop">
                        <button class="page-link"
                                wire:click="gotoPage{{ isset($pageName) ? "('1','{$pageName}')" : '(1)' }}">
                            الأول
                        </button>
                    </li>
                @endif

                {{-- Page numbers with ellipses (desktop only) --}}
                @foreach ($pages as $p)
                    @if (is_string($p) && str_starts_with($p, '...'))
                        <li class="page-item disabled only-desktop"><span class="page-link">…</span></li>
                    @else
                        <li class="page-item {{ $p == $current ? 'active' : '' }} only-desktop">
                            <button class="page-link"
                                    wire:click="gotoPage{{ isset($pageName) ? "('{$p}','{$pageName}')" : "({$p})" }}">
                                {{ $p }}
                            </button>
                        </li>
                    @endif
                @endforeach

                {{-- Last page shortcut (desktop only) --}}
                @if($current < $last)
                    <li class="page-item only-desktop">
                        <button class="page-link"
                                wire:click="gotoPage{{ isset($pageName) ? "('{$last}','{$pageName}')" : "({$last})" }}">
                            الأخير
                        </button>
                    </li>
                @endif

                {{-- Next button (always visible) --}}
                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    <button class="page-link"
                            rel="next"
                            wire:click="nextPage{{ isset($pageName) ? "('{$pageName}')" : '' }}"
                            @if(!$paginator->hasMorePages()) disabled @endif>
                        التالي
                    </button>
                </li>
            </ul>
        </nav>

        {{-- Pagination summary (desktop only) --}}
        <div class="pagination-info mt-2 only-desktop">
            عرض {{ $start }} إلى {{ $end }} من أصل {{ $total }}
        </div>
    </section>
@endif

{{-- ===== Responsive CSS ===== --}}
<style>
    @media (max-width: 767.98px){
        .pagination .only-desktop{ display: none !important; }
        .pagination .page-link{ padding: .5rem 1rem; } /* bigger tap target */
    }
</style>
