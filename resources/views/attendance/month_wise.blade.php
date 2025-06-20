@extends('layouts.master')

@section('title')
    {{ __('attendance') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage').' '.__('attendance') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('view').' '.__('attendance') }}
                        </h4>
                        <div class="row mt-4">
                            <div class="form-group col-sm-12 col-md-3">
                                <label class="filter-menu">{{ __('Class') }} {{ __('section') }} <span class="text-danger">*</span></label>
                                <select required name="class_section_id" id="class_section_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{__('select')}}</option>
                                    @foreach($class_sections as $section)
                                        <option value="{{$section->id}}" data-class="{{$section->class->id}}">{{$section->full_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-sm-12 col-md-3">
                                <label class="filter-menu">{{ __('month') }} <span class="text-danger">*</span></label>
                                {!! Form::selectMonth('month',null,['class' => 'form-control','id' => 'month']) !!}
                            </div>
                        </div>

                        <div class="show_attendance_student_list">
                            <table aria-describedby="mydesc" class='table student_table' id='table_list'
                                   data-toggle="table"  data-click-to-select="true"
                                   data-side-pagination="server" data-pagination="false"
                                   data-page-list="[5, 10, 20, 50, 100, 200,All]" data-search="false" data-toolbar="#toolbar"
                                   data-show-columns="false" data-show-refresh="false" data-fixed-columns="false"
                                   data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                                   data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                   data-maintain-selected="true" data-export-data-type='all' data-show-export="false"
                                   data-export-options='{ "fileName": "view-attendance-list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
                                   data-query-params="AttendanceReportqueryParams" data-escape="true">
                                <thead>
                                <tr>
                                    <th data-field="roll_number">{{ __('roll_no') }}</th>
                                    <th data-field="full_name">{{ __('student_name') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')


    <script>
        
        const classSectionSelect = document.getElementById('class_section_id');
        const monthSelect = document.getElementById('month');

        async function handleSelectChange() {
            var month = $('#month').val();
            var class_section_id = $('#class_section_id').val();
            var table = $('#table_list');
            const response = await fetch(`/attendance/month-wise/list?class_section_id=${class_section_id}&month=${month}`);
            const data = await response.json();
            table.bootstrapTable('load', data);
            try {
                // Fetch the attendance data
                // Ensure data is loaded before refreshing the table
                        
                // Update the table columns dynamically based on the month
                table.bootstrapTable('refreshOptions', {
                    columns: [
                        {
                            field: 'roll_number',
                            title: 'Roll No.'
                        },
                        {
                            field: 'full_name',
                            title: 'Student Name'
                        },
                        ...generateDayColumns(month)
                    ]
                });
                
                
            } catch (error) {
                console.error('Error fetching attendance data:', error);
            }
        }

        classSectionSelect.addEventListener('change', handleSelectChange);
        monthSelect.addEventListener('change', handleSelectChange);

        function generateDayColumns(month) {
            var currentYear = new Date().getFullYear();
            const daysInMonth = new Date(currentYear, month, 0).getDate(); // Month is zero-indexed, so no need to subtract 1
            const columns = [];

            for (let day = 1; day <= daysInMonth; day++) {
                columns.push({
                    field: `day_${day}`,
                    title: `${day}`,
                    formatter: attendanceFormatter
                });
            }
            return columns;
            
        }

    
        function attendanceFormatter(value, row, index) {
            if (value == 1) {
                return '<i class="fa fa-check text-success"></i>';
            } else if(value == 0) {
                return '<i class="fa fa-times text-danger"></i>';
            } else if(value == 3){
                return '<i class="fa fa-power-off text-info"></i>';
            }
            return '-'; // return empty string for no data
        }    
    
    </script>

@endsection
