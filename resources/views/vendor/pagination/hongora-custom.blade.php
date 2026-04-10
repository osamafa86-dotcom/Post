@if ($paginator->hasPages())
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">

            {{-- Previous --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link"
                   href="#"
                   wire:click.prevent="previousPage"
                   @if($paginator->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
            </li>

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                            <a class="page-link"
                               href="#"
                               wire:click.prevent="gotoPage({{ $page }})">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link"
                   href="#"
                   wire:click.prevent="nextPage"
                   @if(!$paginator->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
                    <i class="fa-solid fa-angle-left"></i>
                </a>
            </li>

        </ul>
    </nav>
@endif
