@php
    $total   = $paginator->total();
    $perPage = $paginator->perPage();
    $current = max(1, $paginator->currentPage());
    $last    = max(1, $paginator->lastPage());
    $window  = 2;

    if ($total === 0) {
        $start = 0; $end = 0;
    } else {
        $start = ($current - 1) * $perPage + 1;
        $end   = min($current * $perPage, $total);
    }

    $pages = [];
    if ($current - $window > 1) {
        $pages[] = 1;
        if ($current - $window > 2) $pages[] = '...left';
    }
    for ($i = max(1, $current - $window); $i <= min($last, $current + $window); $i++) {
        $pages[] = $i;
    }
    if ($current + $window < $last) {
        if ($current + $window < $last - 1) $pages[] = '...right';
        $pages[] = $last;
    }
@endphp

@if ($paginator->hasPages())
    <div class="pagination-area mt-50 text-center">
        <div aria-label="Page navigation">
            <ul class="pagination justify-content-center">

                {{-- Previous --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <button class="page-link"
                            wire:click="previousPage"
                            @if($paginator->onFirstPage()) disabled @endif>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </li>

                {{-- Pages --}}
                @foreach ($pages as $p)
                    @if (is_string($p))
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @else
                        <li class="page-item {{ $p == $current ? 'active' : '' }}">
                            <button class="page-link"
                                    wire:click="gotoPage({{ $p }})">
                                {{ $p }}
                            </button>
                        </li>
                    @endif
                @endforeach

                {{-- Next --}}
                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    <button class="page-link"
                            wire:click="nextPage"
                            @if(!$paginator->hasMorePages()) disabled @endif>
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </li>
            </ul>
        </div>

{{--        <div class="pagination-info mt-2">--}}
{{--            عرض {{ $start }} إلى {{ $end }} من أصل {{ $total }}--}}
{{--        </div>--}}
    </div>
@endif
