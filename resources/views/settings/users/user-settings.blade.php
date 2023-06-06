<form method="POST" action="{{ route('submitCatalog') }}">
    @csrf
    @foreach($catalogs as $catalog)
    <label>{{ $catalog->catalog_key }}</label>
    <input type="checkbox" name="selectedKeys[]" value="{{ $catalog->id }}" @if($catalog->is_show === 1) checked @endif>
    <br>
    @endforeach

    <button type="submit">Submit</button>
</form>