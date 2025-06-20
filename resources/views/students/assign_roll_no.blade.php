@extends('layouts.master')

@section('title')
    {{ __('assign_roll_no') }}
@endsection

@section('content')
    <style>
        .btn-outline-success {
            padding: 15px;
        }
    </style>
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_students_roll_no') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('students') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3">
                                    <label class="filter-menu" for="filter_roll_number_class_section_id">{{ __('Class Section') }} </label>
                                    <select name="filter_roll_number_class_section_id" id="filter_roll_number_class_section_id" class="form-control">
                                        @foreach ($class_section as $class)
                                            <option value={{ $class->id }}>
                                                {{ $class->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label class="filter-menu" for="sort_by">{{ __('sort_by') }} </label>
                                    <select name="sort_by" id="sort_by" class="form-control">
                                        <option value="first_name">{{ __('first_name') }}</option>
                                        <option value="last_name">{{ __('last_name') }}</option>
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label class="filter-menu" for="order_by">{{ __('Order By') }} </label>
                                    <select name="order_by" id="order_by" class="form-control">
                                        <option value="asc" selected>{{ __('Ascending') }}</option>
                                        <option value="desc">{{ __('Descending') }}</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <form id="assign-roll-no-form" action="{{ route('students.roll-number.update') }}" method="post">
                            @csrf
                            <div class="row search-container">
                                <div class="col-12">
                                    <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table" data-url="{{ route('students.roll-number.show',1) }}" data-click-to-select="true" data-search="true" data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-mobile-responsive="true" data-maintain-selected="true" data-show-export="true" data-export-data-type='all' data-export-options='{ "fileName": "{{__('students')}} {{__('roll_no')}}-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}' data-query-params="studentRollNumberQueryParams">
                                        <thead>
                                        <tr>
                                            <th scope="col" data-field="no">{{ __('no.') }}</th>
                                            <th scope="col" data-field="student_id" data-visible="false">{{ __('student_id') }} </th>
                                            <th scope="col" data-field="user_id" data-visible="false">{{ __('User Id') }}</th>
                                            <th scope="col" data-field="new_roll_number">{{ __('new_roll_no') }}</th>
                                            <th scope="col" data-field="old_roll_number">{{ __('old_roll_no') }}</th>
                                            <th scope="col" data-field="first_name">{{ __('first_name') }}</th>
                                            <th scope="col" data-field="last_name">{{ __('last_name') }}</th>
                                            <th scope="col" data-field="dob">{{ __('dob') }}</th>
                                            <th scope="col" data-field="image" data-formatter="imageFormatter">{{ __('image') }}</th>
                                            <th scope="col" data-field="class_section_id" data-visible="false">{{ __('Class') . ' ' . __('section') . ' ' . __('id') }}</th>
                                            <th scope="col" data-field="admission_no">{{ __('admission_no') }}</th>
                                            <th scope="col" data-field="admission_date">{{ __('admission_date') }}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="text-left">
                                <input class="btn btn-theme btn_generate_roll_number my-4 float-right" id="create-btn" type="submit" value={{ __('submit') }}>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
