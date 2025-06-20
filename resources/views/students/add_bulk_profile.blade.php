@extends('layouts.master')

@section('title')
    {{ __('upload_profile') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Students') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" id="create-form" enctype="multipart/form-data" action="{{ route('students.update-profile') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="row" id="toolbar">
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label class="filter-menu">{{ __('Class Section') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="filter_class_section_id" id="filter_class_section_id"
                                                class="form-control">
                                                <option value="">{{ __('select_class_section') }}</option>
                                                @foreach ($class_sections as $class_section)
                                                    <option value={{ $class_section->id }}>{{ $class_section->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>

                                    <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                        data-url="{{ route('students.list', [1]) }}" data-click-to-select="true"
                                        data-side-pagination="server" data-pagination="false"
                                        data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                        data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                                        data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                                        data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                        data-sort-order="desc" data-maintain-selected="true"
                                        data-query-params="studentDetailsQueryParams" data-show-export="true"
                                        data-export-options='{"fileName": "section-list-<?= date('d-m-y') ?>
                                        ","ignoreColumn": ["operate"]}'
                                        data-escape="true">
                                        <thead>
                                            <tr>
                                                <th scope="col" data-field="id" data-sortable="false" data-visible="false">{{ __('id') }}</th>
                                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                                <th scope="col" data-field="user.image" data-formatter="imageFormatter"> {{ __('image') }}</th>
                                                <th scope="col" data-field="user.id" data-visible="false"> {{ __('User Id') }}</th>
                                                <th scope="col" data-field="user.full_name">{{ __('name') }}</th>
                                                <th scope="col" data-field="roll_number">{{ __('roll_no') }}</th>
                                                <th scope="col" data-field="user.gender" data-visible="false"> {{ __('gender') }}</th>

                                                <th scope="col" data-field="guardian.image" data-formatter="imageFormatter">{{ __('guardian') }} {{ __('image') }}</th>
                                                <th scope="col" data-field="guardian.id" data-visible="false"> {{ __('guardian_user_id') }} {{ __('image') }}</th>
                                                <th scope="col" data-field="guardian.email"> {{ __('guardian') . ' ' . __('email') }}</th>
                                                <th scope="col" data-field="guardian.full_name"> {{ __('guardian') . ' ' . __('name') }}</th>
                                                <th scope="col" data-field="guardian.mobile"> {{ __('guardian') . ' ' . __('mobile') }}</th>
                                                <th scope="col" data-field="guardian.gender" data-visible="false"> {{ __('guardian') . ' ' . __('gender') }}</th>

                                                <th scope="col" data-formatter="studentImageFormatter" data-field="student.profile">{{ __('student') . ' ' . __('profile') }}
                                                </th>
                                                <th scope="col" data-formatter="guardianImageFormatter" data-field="guardian.profile"> {{ __('guardian') . ' ' . __('profile') }}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group col-sm-12 mt-3">
                                <input class="btn btn-theme submit_bulk_file float-right" type="submit" value="{{ __('submit') }}" name="submit"
                                    id="submit_bulk_file">
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
@endsection
