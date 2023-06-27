{{-- c_z_rated, length_unit_id, tread_depth, image_url_tread, --}}

{{-- full_bolt_patterns, rim_diameter, simple_finish, max_rim_width_unit_id, season, bolt_circle_diameter_2 --}}

@extends('layouts.mainlayout')

@section('title', 'Admin - API Access Authorization')
@section('content')
    <div class="d-flex">
        <div class="col-2 bg-dark text-light side-bar">
            <!-- Sidebar content goes here -->
            @include('settings.settings')
        </div>

        <div class="col-10">
            <div class="center-form p-4">
                <form method="post" action="{{ route('update.user.column.settings', ['id' => $id]) }}">
                    @csrf
                    <div class="intro p-2 rounded-4">
                        <h2 id="example" class="mt-2">API Access Authorization</h2>
                        <div class="row mt-lg-5">
                            {{-- column 1 --}}
                            <div class="col-md-2">
                                @foreach ($columns as $column)
                                    @if ($column === 'full_bolt_patterns')
                                    @break
                                @endif
                                <div class="form-group">
                                    <div class="form-check form-switch switch round">
                                        <input class="form-check-input" value="{{ $column }}" name="column[]"
                                            {{ in_array($column, json_decode($client['access'])) ? '' : 'checked' }}
                                            type="checkbox" role="switch">
                                        <label class="form-check-label">{{ $column }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- column 2 --}}
                        <div class="col-md-2">
                            @php $secondColumn = false; @endphp
                            @foreach ($columns as $column)
                                @if ($column === 'full_bolt_patterns')
                                    @php $secondColumn = true; @endphp
                                @endif
                                @if ($secondColumn)
                                    <div class="form-group">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" value="{{ $column }}" name="column[]"
                                                {{ in_array($column, json_decode($client['access'])) ? '' : 'checked' }}
                                                type="checkbox" role="switch">
                                            <label class="form-check-label">{{ $column }}</label>
                                        </div>
                                    </div>
                                @endif
                                @if ($column === 'rim_diameter')
                                @break
                            @endif
                        @endforeach
                    </div>

                    {{-- column 3 --}}
                    <div class="col-md-2">
                        @php $thirdColumn = false; @endphp
                        @foreach ($columns as $column)
                            @if ($column === 'rim_diameter_unit_id')
                                @php $thirdColumn = true; @endphp
                            @endif
                            @if ($thirdColumn)
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" value="{{ $column }}" name="column[]"
                                            {{ in_array($column, json_decode($client['access'])) ? '' : 'checked' }}
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

                {{-- column 4 --}}
                <div class="col-md-2">
                    @php $fourthColumn = false; @endphp
                    @foreach ($columns as $column)
                        @if ($column === 'load_index_1')
                            @php $fourthColumn = true; @endphp
                        @endif
                        @if ($fourthColumn)
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" value="{{ $column }}" name="column[]"
                                        {{ in_array($column, json_decode($client['access'])) ? '' : 'checked' }}
                                        type="checkbox" role="switch">
                                    <label class="form-check-label">{{ $column }}</label>
                                </div>
                            </div>
                        @endif
                        @if ($column === 'min_rim_width')
                        @break
                    @endif
                @endforeach
            </div>

            {{-- column 5 --}}
            <div class="col-md-2">
                @php $fifthColumn = false; @endphp
                @foreach ($columns as $column)
                    @if ($column === 'min_rim_width_unit_id')
                        @php $fifthColumn = true; @endphp
                    @endif
                    @if ($fifthColumn)
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" value="{{ $column }}" name="column[]"
                                    {{ in_array($column, json_decode($client['access'])) ? '' : 'checked' }}
                                    type="checkbox" role="switch">
                                <label class="form-check-label">{{ $column }}</label>
                            </div>
                        </div>
                    @endif
                    @if ($column === 'season')
                    @break
                @endif
            @endforeach
        </div>

        {{-- column 6 --}}
        <div class="col-md-2">
            @php $sixthColumn = false; @endphp
            @foreach ($columns as $column)
                @if ($column === 'tire_type_performance')
                    @php $sixthColumn = true; @endphp
                @endif
                @if ($sixthColumn)
                    <div class="form-group">
                        <div class="form-check form-switch">
                            <input class="form-check-input" value="{{ $column }}" name="column[]"
                                {{ in_array($column, json_decode($client['access'])) ? '' : 'checked' }}
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

<div class="has-bg-img">
    <div class="row my-4">
        <div class="col-md-12">
            <div class="form-group d-flex justify-content-end">
                <button class="btn btn-secondary me-2">Back</button>
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </div>
        <img src="" alt="">
    </div>
</div>
</div>
</form>
</div>
</div>
</div>
@endsection
