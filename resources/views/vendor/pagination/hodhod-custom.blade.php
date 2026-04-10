@if ($paginator->hasPages())
    <nav class="pagination-container" role="navigation" aria-label="Pagination">
        <div class="pagination-controls" dir="ltr">

            {{-- أول صفحة --}}
            @if ($paginator->onFirstPage())
                <span class="page-btn is-disabled" aria-disabled="true" aria-label="الصفحة الأولى">
                    <i class="fas fa-angle-double-left"></i>
                </span>
            @else
                <button wire:click="gotoPage(1)" class="page-btn" aria-label="الصفحة الأولى">
                    <i class="fas fa-angle-double-left"></i>
                </button>
            @endif

            {{-- السابقة --}}
            @if ($paginator->onFirstPage())
                <span class="page-btn is-disabled" aria-disabled="true" aria-label="السابقة">
                    <i class="fas fa-angle-left"></i>
                </span>
            @else
                <button wire:click="previousPage" class="page-btn" aria-label="السابقة">
                    <i class="fas fa-angle-left"></i>
                </button>
            @endif

            {{-- أرقام الصفحات --}}
            <ul class="pagination-list">
                @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                    <li>
                        @if ($page == $paginator->currentPage())
                            <span class="page-number active" aria-current="page">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="page-number" aria-label="الذهاب إلى صفحة {{ $page }}">
                                {{ $page }}
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>

            {{-- التالية --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" class="page-btn" aria-label="التالية">
                    <i class="fas fa-angle-right"></i>
                </button>
            @else
                <span class="page-btn is-disabled" aria-disabled="true" aria-label="التالية">
                    <i class="fas fa-angle-right"></i>
                </span>
            @endif

            {{-- الأخيرة --}}
            @if ($paginator->hasMorePages())
                <button wire:click="gotoPage({{ $paginator->lastPage() }})" class="page-btn" aria-label="الصفحة الأخيرة">
                    <i class="fas fa-angle-double-right"></i>
                </button>
            @else
                <span class="page-btn is-disabled" aria-disabled="true" aria-label="الصفحة الأخيرة">
                    <i class="fas fa-angle-double-right"></i>
                </span>
            @endif
        </div>

        <!-- Summary -->
        <div class="pagination-info">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} من {{ $paginator->total() }} نتيجة
        </div>
    </nav>
@endif
