@if ($paginator->hasPages())
    <div class="pagination-area mt-30">
        <div aria-label="التنقل بين الصفحات">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <button
                            wire:click="previousPage"
                            wire:loading.attr="disabled"
                            class="page-link"
                            aria-label="@lang('pagination.previous')">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </li>
                @endif

                {{-- Pagination Numbers --}}
                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <button
                                wire:click="gotoPage({{ $page }})"
                                class="page-link">
                                {{ $page }}
                            </button>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <button
                            wire:click="nextPage"
                            wire:loading.attr="disabled"
                            class="page-link"
                            aria-label="@lang('pagination.next')">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endif
