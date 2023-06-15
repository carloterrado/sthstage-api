@extends('layouts.mainlayout')
@section('content')
    <form method="post" action="{{ route('update.user.column.settings', ['id' => $id]) }}">
        @csrf
        <section class="intro d-flex align-items-center justify-content-center min-">
            <div class="container mb-5">
                <h2 id="example" class="mt-2">Hidden Catalog Column For User </h2>
                <div class="bd-example">
                    @if (session('error_message'))
                        <div class="alert alert-danger">
                            {{ session('error_message') }}
                        </div>
                    @endif
                    <div class="row">
                        {{-- column 1 --}}
                        <div class="col-md-3">
                            @foreach ($columns as $column)
                                @if ($column === 'notes')
                                @break
                            @endif
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" value="{{ $column }}" name="column[]"
                                        {{ in_array($column, json_decode($client['catalog_column_settings'])) ? '' : 'checked' }}
                                        type="checkbox" role="switch">
                                    <label class="form-check-label">{{ $column }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- column 2 --}}
                    <div class="col-md-3">
                        @php
                            $secondColumn = false;
                        @endphp
                        @foreach ($columns as $column)
                            @if ($column === 'notes')
                                @php
                                    $secondColumn = true;
                                @endphp
                            @endif
                            @if ($secondColumn)
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" value="{{ $column }}" name="column[]"
                                            {{ in_array($column, json_decode($client['catalog_column_settings'])) ? '' : 'checked' }}
                                            type="checkbox" role="switch">
                                        <label class="form-check-label">{{ $column }}</label>
                                    </div>
                                </div>
                            @endif

                            @if ($column === 'side_wall_style')
                            @break
                        @endif
                    @endforeach
                </div>
                {{-- column 3 --}}
                <div class="col-md-3">
                    @php
                        $thirdColumn = false;
                    @endphp
                    @foreach ($columns as $column)
                        @if ($column === 'load_index_1')
                            @php
                                $thirdColumn = true;
                            @endphp
                        @endif
                        @if ($thirdColumn)
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" value="{{ $column }}" name="column[]"
                                        {{ in_array($column, json_decode($client['catalog_column_settings'])) ? '' : 'checked' }}
                                        type="checkbox" role="switch">
                                    <label class="form-check-label">{{ $column }}</label>
                                </div>
                            </div>
                        @endif

                        @if ($column === 'max_psi')
                        @break
                    @endif
                @endforeach
            </div>
            {{-- column 4 --}}
            <div class="col-md-3">
                @php
                    $fourthColumn = false;
                @endphp
                @foreach ($columns as $column)
                    @if ($column === 'max_load_lb')
                        @php
                            $fourthColumn = true;
                        @endphp
                    @endif
                    @if ($fourthColumn)
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="{{ $column }}" name="column[]"
                                    {{ in_array($column, json_decode($client['catalog_column_settings'])) ? '' : 'checked' }}
                                    type="checkbox" role="switch">
                                <label class="form-check-label">{{ $column }}</label>
                            </div>
                        </div>
                    @endif

                    @if ($column === 'bolt_circle_diameter_2')
                    @break
                @endif
            @endforeach
        </div>
    </div>
    <div class="row my-4">
        <div class="col-md-12">
            <div class="form-group">
                <button class="btn btn-danger">Back</button>
                <input type="submit" class="btn btn-success" value="Submit">
            </div>
        </div>
    </div>
</div>
</div>
</section>
</form>
@endsection
