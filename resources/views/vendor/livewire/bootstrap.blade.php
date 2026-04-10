@php
    // Compact windowed pagination (no backend change)
    // - Shows: First, Prev, window around current, Next, Last, with ellipses
    // - Keeps Livewire methods: previousPage, nextPage, gotoPage
    $pageName   = $paginator->getPageName();
    $current    = $paginator->currentPage();
    $last       = $paginator->lastPage();
    $window     = 2;          // how many pages around current
    $maxVisible = 7;          // hard cap of visible page items

    // Build a compact set of page numbers
    $pages = [];
    if ($last <= $maxVisible) {
        $pages = range(1, $last);
    } else {
        $left  = max(2, $current - $window);
        $right = min($last - 1, $current + $window);

        // Always include first & last
        $pages = [1];

        if ($left > 2) $pages[] = '…'; // left ellipsis

        foreach (range($left, $right) as $p) {
            $pages[] = $p;
        }

        if ($right < $last - 1) $pages[] = '…'; // right ellipsis

        $pages[] = $last;
    }

    // Smooth scroll target (kept compatible)
    if (! isset($scrollTo)) { $scrollTo = 'body'; }
    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView({behavior:'smooth', block:'start'})
        JS
        : '';
@endphp

@if ($paginator->hasPages())
    <nav class="devlo-pagination" aria-label="{{ __('pagination.navigation') }}">

        {{-- Mobile: only Prev / Next --}}
        <div class="d-flex d-sm-none justify-content-center">
            <ul class="pagination pagination-compact mb-0">
                {{-- Prev --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    @if ($paginator->onFirstPage())
                        <span class="page-link">{{ __('pagination.previous') }}</span>
                    @else
                        <button type="button" class="page-link"
                                wire:click="previousPage('{{ $pageName }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled">
                            {{ __('pagination.previous') }}
                        </button>
                    @endif
                </li>

                {{-- Next --}}
                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    @if ($paginator->hasMorePages())
                        <button type="button" class="page-link"
                                wire:click="nextPage('{{ $pageName }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled">
                            {{ __('pagination.next') }}
                        </button>
                    @else
                        <span class="page-link">{{ __('pagination.next') }}</span>
                    @endif
                </li>
            </ul>
        </div>

        {{-- Desktop: windowed numbers + First/Last --}}
        <div class="d-none d-sm-flex justify-content-center">
            <ul class="pagination pagination-compact mb-0" role="list">
                {{-- First --}}
                <li class="page-item {{ $current === 1 ? 'disabled' : '' }}">
                    @if ($current === 1)
                        <span class="page-link" aria-disabled="true" aria-label="{{ __('pagination.first') }}">
                        <i class="bi bi-chevron-double-right"></i>
                    </span>
                    @else
                        <button type="button" class="page-link"
                                wire:click="gotoPage(1, '{{ $pageName }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                aria-label="{{ __('pagination.first') }}">
                            <i class="bi bi-chevron-double-right"></i>
                        </button>
                    @endif
                </li>

                {{-- Prev --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    @if ($paginator->onFirstPage())
                        <span class="page-link" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                    @else
                        <button type="button" class="page-link"
                                wire:click="previousPage('{{ $pageName }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled"
                                aria-label="{{ __('pagination.previous') }}">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    @endif
                </li>

                {{-- Numbers + ellipses --}}
                @foreach ($pages as $p)
                    @if ($p === '…')
                        <li class="page-item disabled" aria-hidden="true"><span class="page-link dots">…</span></li>
                    @elseif ($p == $current)
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $p }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" class="page-link"
                                    wire:click="gotoPage({{ $p }}, '{{ $pageName }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    aria-label="{{ __('pagination.goto') }} {{ $p }}">
                                {{ $p }}
                            </button>
                        </li>
                    @endif
                @endforeach

                {{-- Next --}}
                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    @if ($paginator->hasMorePages())
                        <button type="button" class="page-link"
                                wire:click="nextPage('{{ $pageName }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled"
                                aria-label="{{ __('pagination.next') }}">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                    @else
                        <span class="page-link" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                    @endif
                </li>

                {{-- Last --}}
                <li class="page-item {{ $current === $last ? 'disabled' : '' }}">
                    @if ($current === $last)
                        <span class="page-link" aria-disabled="true" aria-label="{{ __('pagination.last') }}">
                        <i class="bi bi-chevron-double-left"></i>
                    </span>
                    @else
                        <button type="button" class="page-link"
                                wire:click="gotoPage({{ $last }}, '{{ $pageName }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                aria-label="{{ __('pagination.last') }}">
                            <i class="bi bi-chevron-double-left"></i>
                        </button>
                    @endif
                </li>
            </ul>
        </div>

        {{-- Loading indicator --}}
        <div class="devlo-pagination-loading" wire:loading></div>
    </nav>
@endif
