<div class="modal fade" id="catalogModal">
    <div class="modal-dialog modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CATALOG</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('update.user.column.settings', ['id' => $id]) }}">
                    @csrf
                    <div class="row">
                        {{-- Column 1 --}}
                        <div class="col-md-4">
                            @foreach ($columns as $column)
                                @if ($column == 'install_time')
                                @break
                            @endif
                            <div class="form-check">
                                <div class="d-flex gap-2">
                                    <input type="checkbox"value="{{ $column }}" name="column[]"
                                        {{ in_array($column, json_decode($role->access)) ? '' : 'checked' }}
                                        type="checkbox" role="switch">
                                    <label style="font-size: 13px;"
                                        for="{{ $column }}">{{ $column }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Column 2 --}}
                    <div class="col-md-4">
                        @php $secondColumn = false; @endphp
                        @foreach ($columns as $column)
                            @if ($column == 'install_time')
                                @php $secondColumn = true; @endphp
                            @endif
                            @if ($secondColumn)
                                <div class="form-check">
                                    <div class="d-flex gap-2">
                                        <input type="checkbox"value="{{ $column }}" name="column[]"
                                            {{ in_array($column, json_decode($role->access)) ? '' : 'checked' }}
                                            type="checkbox" role="switch">
                                        <label style="font-size: 13px;"
                                            for="{{ $column }}">{{ $column }}</label>
                                    </div>
                                </div>
                            @endif
                            @if ($column === 'rim_width_unit_id')
                            @break
                        @endif
                    @endforeach
                </div>

                {{-- Column 3 --}}
                <div class="col-md-4">
                    @php $thirdColumn = false; @endphp
                    @foreach ($columns as $column)
                        @if ($column == 'rim_width_unit_id')
                            @php $thirdColumn = true; @endphp
                        @endif
                        @if ($thirdColumn)
                            <div class="form-check">
                                <div class="d-flex gap-2">
                                    <input type="checkbox"value="{{ $column }}" name="column[]"
                                        {{ in_array($column, json_decode($role->access)) ? '' : 'checked' }}
                                        type="checkbox" role="switch">
                                    <label style="font-size: 13px;"
                                        for="{{ $column }}">{{ $column }}</label>
                                </div>
                            </div>
                        @endif
                        @if ($column === 'width_unit_id')
                        @break
                    @endif
                @endforeach
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Save changes</button>
        </div>
    </form>

</div>
</div>
</div>
</div>
</div>
</div>
