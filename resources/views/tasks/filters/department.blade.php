<div class="d-flex align-items-center task-department-filter {{ $wrapperClass ?? '' }}">
    <div style="min-width: 190px;">
        <select class="form-control select-picker" name="department" id="department" data-live-search="true" data-size="8">
            <option value="all">{{ __('Tous les départements') }}</option>
            @if(isset($departments))
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ (request('department') == $dept->id) ? 'selected' : '' }}>{{ $dept->team_name }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
