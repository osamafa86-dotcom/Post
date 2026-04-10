@if ($paginator->hasPages())
<div style="display: flex; justify-content: center; align-items: center; gap: 6px; padding: 40px 0; flex-wrap: wrap; direction: ltr;">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #f5f4f0; color: #ccc; cursor: not-allowed; font-size: 14px;">
            <i class="fas fa-chevron-left"></i>
        </span>
    @else
        <button wire:click="previousPage" wire:loading.attr="disabled"
                style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #fff; color: #1A1A1A; border: 2px solid #e0e0e0; cursor: pointer; font-size: 14px; transition: all 0.2s;"
                onmouseover="this.style.borderColor='#B71C1C';this.style.color='#B71C1C'"
                onmouseout="this.style.borderColor='#e0e0e0';this.style.color='#1A1A1A'">
            <i class="fas fa-chevron-left"></i>
        </button>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; color: #6B6B6B; font-size: 14px;">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #B71C1C; color: #fff; font-weight: 700; font-size: 15px; font-family: 'Cairo', sans-serif;">{{ $page }}</span>
                @else
                    <button wire:click="gotoPage({{ $page }})"
                            style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #fff; color: #1A1A1A; border: 2px solid #e0e0e0; cursor: pointer; font-weight: 600; font-size: 15px; font-family: 'Cairo', sans-serif; transition: all 0.2s;"
                            onmouseover="this.style.borderColor='#B71C1C';this.style.color='#B71C1C'"
                            onmouseout="this.style.borderColor='#e0e0e0';this.style.color='#1A1A1A'">{{ $page }}</button>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <button wire:click="nextPage" wire:loading.attr="disabled"
                style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #fff; color: #1A1A1A; border: 2px solid #e0e0e0; cursor: pointer; font-size: 14px; transition: all 0.2s;"
                onmouseover="this.style.borderColor='#B71C1C';this.style.color='#B71C1C'"
                onmouseout="this.style.borderColor='#e0e0e0';this.style.color='#1A1A1A'">
            <i class="fas fa-chevron-right"></i>
        </button>
    @else
        <span style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: #f5f4f0; color: #ccc; cursor: not-allowed; font-size: 14px;">
            <i class="fas fa-chevron-right"></i>
        </span>
    @endif
</div>
@endif
