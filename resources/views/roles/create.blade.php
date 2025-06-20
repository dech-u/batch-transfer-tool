@extends('layouts.master')

@section('title')
    {{ __('Create New Role') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Create New Role') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-theme" href="{{ route('roles.index') }}"> {{ __('back') }}</a>
                        </div>

                        <div class="row">
                            {!! Form::open(['route' => 'roles.store', 'method' => 'POST']) !!}
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-sm-12 col-xs-12 col-md-3">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            {{ Form::checkbox('selectall', 1, false, ['class' => 'name form-check-input', 'id' => 'selectall']) }}Select all
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12">
                                    <label><strong>{{ __('permission') }}:</strong></label>
                                    <div class="row mt-4">
                                        @php
                                            $groupedPermissions = $permission->groupBy(function ($item) {
                                                return explode('-', $item->name)[0];
                                            });
                                        @endphp
                                
                                        @foreach ($groupedPermissions as $group => $permissions)
                                            <div class="col-sm-12 col-md-12 mb-4">
                                                <div class="form-check">
                                                    <label class="form-check-label" for="checkbox-{{ $group }}">
                                                        <strong>{{ ucfirst($group) }}</strong>
                                                    <input 
                                                        type="checkbox" 
                                                        class="form-check-input parent-checkbox" 
                                                        id="checkbox-{{ $group }}" 
                                                        data-group="{{ $group }}" 
                                                        onchange="togglePermissions(this)">
                                                   
                                                        
                                                    </label>
                                                </div>
                                
                                                <div class="row mt-2">
                                                    @foreach ($permissions as $value)
                                                        <div class="form-group col-lg-3 col-sm-12 col-xs-12 col-md-3">
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                    {{ Form::checkbox('permission[]', $value->id, false, ['class' => 'form-check-input child-checkbox', 'data-group' => $group, 'onchange' => 'updateParentCheckboxState("' . $group . '")']) }}
                                                                    {{ $value->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <hr>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12">
                                    {{-- <button type="submit" class="btn btn-theme float-right">{{ __('submit') }}</button> --}}
                                    <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                    <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
        $('#selectall').prop("checked", false);
        $('.parent-checkbox').prop("checked", false);
        $('.child-checkbox').prop("checked", false);

        // Ensure parent checkbox states are updated after checking all checkboxes
        $('.parent-checkbox').each(function () {
            updateParentCheckboxState($(this).data('group'));
        });

        // Handle "Select All" checkbox functionality
        $('#selectall').click(function () {
            const isChecked = this.checked;
            // Select or deselect all checkboxes (both parent and child)
            $('.parent-checkbox').prop('checked', isChecked);
            $('.child-checkbox').prop('checked', isChecked);

            // Trigger change event to ensure parent checkboxes are updated properly
            $('.parent-checkbox').each(function () {
                updateParentCheckboxState($(this).data('group'));
            });
        });

        // Function to handle change event for individual child checkboxes
        $('.selectedId').change(function () {
            updateSelectAllState();
        });

        // Function to handle change event for parent checkboxes
        $('.parent-checkbox').change(function () {
            updateSelectAllState();
        });

        // Update the state of the "Select All" checkbox
        function updateSelectAllState() {
            var allSelected = $('.parent-checkbox').length === $('.parent-checkbox:checked').length;

            $('#selectall').prop('checked', allSelected);
        }
    });

    // Function to handle the parent checkbox click
    function togglePermissions(checkbox) {
        const group = checkbox.dataset.group;
        const childCheckboxes = document.querySelectorAll(`.child-checkbox[data-group="${group}"]`);
        
        childCheckboxes.forEach(childCheckbox => {
            childCheckbox.checked = checkbox.checked;
        });

        // Update parent checkbox state
        updateParentCheckboxState(group);
    }

    // Function to update the parent checkbox state based on children
    function updateParentCheckboxState(group) {
        const parentCheckbox = document.querySelector(`#checkbox-${group}`);
        const childCheckboxes = document.querySelectorAll(`.child-checkbox[data-group="${group}"]`);
        
        const allChecked = Array.from(childCheckboxes).every(checkbox => checkbox.checked);
        const someChecked = Array.from(childCheckboxes).some(checkbox => checkbox.checked);
        
        parentCheckbox.checked = allChecked;
        parentCheckbox.indeterminate = !allChecked && someChecked;
    }

    </script>
@endsection
