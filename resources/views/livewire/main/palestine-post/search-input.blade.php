@php use Illuminate\Support\Str; @endphp
@section('title')
    {{config('system.site_name')}} |
@endsection

<form wire:submit.prevent="getSearchPage" role="search">
    <div class="form-group">
        <input wire:model="search_text" class="search-input" placeholder="بحث عن.." aria-label="البحث في الموقع" />
        <button type="submit" class="top-bar-btn" aria-label="بحث">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
        </button>
    </div>
</form>


@section('script')
@endsection
