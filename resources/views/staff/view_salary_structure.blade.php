@extends('layouts.master')

@section('title')
    {{ __('Salary Structure') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Salary Structure') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="salary-summary">
                            {{-- <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Basic Salary</th>
                                        <td></td>
                                        <td>{{ number_format($salary ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Allowances (+)</th>
                                        <td>
                                            @foreach ($allowances as $allowance)
                                                {{ $allowance ?? '' }}<br><br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($allowanceAmount as $amount)
                                                {{ number_format($amount ?? 0, 2) }}<br><br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr> 
                                        <th>Total Deductions (-)</th>
                                        <td>
                                            @foreach ($deductions as $deduction)
                                                {{ $deduction ?? '' }}<br><br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($deductionAmount as $amount)
                                                {{ number_format($amount ?? 0, 2) }}<br><br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Net Salary</th>
                                        <th>{{ number_format($netSalary ?? 0, 2) }}</th >
                                    </tr>
                                </tbody>
                            </table> --}}
                        </div>

                        <form action="{{ url('staff/payroll-setting',$user->staff->id) }}" method="post" class="edit-form-staff-payroll-setting edit-form" data-success-function="formSuccessFunction">
                        @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <h3 class="mb-3">{{ __('allowances') }}</h3>
                                    @foreach ($user->staff->staffSalary as $row)
                                        @if ($row->payrollSetting->type == 'allowance')
                                            <div class="row delete-type">
                                                <div class="form-group col-sm-12 col-md-5">
                                                    <label>{{ __('allowance_type') }} </label>
                                                    <select disabled id="allowance_id" name="allowance_type"
                                                        class="form-control allowance_id">
                                                        <option value="">--{{ __('select') }}--</option>
                                                        @foreach ($allowances as $allowance)
                                                            @if ($row->payroll_setting_id == $allowance->id)
                                                                <option selected value="{{ $allowance->id }}"
                                                                    data-value="{{ isset($allowance->amount) ? $allowance->amount : $allowance->percentage }}"
                                                                    data-type="{{ isset($allowance->amount) ? 'amount' : 'percentage' }}">
                                                                    {{ $allowance->name }}</option>
                                                            @else
                                                                <option value="{{ $allowance->id }}"
                                                                    data-value="{{ isset($allowance->amount) ? $allowance->amount : $allowance->percentage }}"
                                                                    data-type="{{ isset($allowance->amount) ? 'amount' : 'percentage' }}">
                                                                    {{ $allowance->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @if ($row->amount)
                                                    <div class="form-group col-md-5" id="amount_allowance_div">
                                                        <label>{{ __('amount') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input disabled type="number" id="allowance_amount"
                                                            name="allowance_amount"
                                                            class="allowance_amount form-control"
                                                            placeholder="{{ __('amount') }}" value="{{ $row->amount }}"
                                                            required>
                                                    </div>
                                                @else
                                                    <div class="form-group col-md-5" id="percentage_allowance_div">
                                                        <label>{{ __('percentage') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input disabled type="number" id="allowance_percentage"
                                                            name="allowance_percentage"
                                                            class="allowance_percentage form-control"
                                                            placeholder="{{ __('percentage') }}" required
                                                            value="{{ $row->percentage }}">
                                                    </div>
                                                @endif
                                                <div class="form-group col-sm-12 col-md-2 mt-4">
                                                    <button type="button" class="btn btn-inverse-danger delete-payroll-setting btn-icon" data-id="{{ $row->id }}"> <i class="fa fa-times"></i> </button>
                                                </div>
                                            </div>
                                        @endif

                                    @endforeach

                                    <div data-repeater-list="allowance_data">

                                        <div class="row allowance_type_div" id="allowance_type_div" data-repeater-item>
                                            <div class="form-group col-md-5">
                                                <label>{{ __('allowance_type') }} </label>
                                                <select id="allowance_id" name="allowance[0][id]"
                                                    class="form-control allowance_id">
                                                    <option value="">--{{ __('select') }}--</option>
                                                    @foreach ($allowances->where('deleted_at',null) as $allowance)
                                                        <option value="{{ $allowance->id }}"
                                                            data-value="{{ isset($allowance->amount) ? $allowance->amount : $allowance->percentage }}"
                                                            data-type="{{ isset($allowance->amount) ? 'amount' : 'percentage' }}">
                                                            {{ $allowance->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-5" id="amount_allowance_div" style="display: none">
                                                <label>{{ __('amount') }} <span class="text-danger">*</span></label>
                                                <input type="number" id="allowance_amount" name="allowance[0][amount]" class="allowance_amount real_time_change form-control" placeholder="{{ __('amount') }}" required>
                                            </div>

                                            <div class="form-group col-md-5" id="percentage_allowance_div" style="display: none">
                                                <label>{{ __('percentage') }} <span class="text-danger">*</span></label>
                                                <input type="number" id="allowance_percentage" name="allowance[0][percentage]" class="allowance_percentage real_time_change form-control" placeholder="{{ __('percentage') }}" required >
                                            </div>


                                            <div class="form-group col-xl-1 mt-4">
                                                <button type="button"
                                                    class="btn btn-inverse-danger btn-icon remove-allowance-div"
                                                    data-repeater-delete>
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group col-sm-12 mt-4">
                                        <button type="button" class="btn btn-inverse-success add-allowance-div"
                                            data-repeater-create>
                                            <i class="fa fa-plus"></i> {{ __('add_new_allowances') }}
                                        </button>
                                    </div>
                                </div>

                                {{-- deductions --}}
                                <div class="form-group col-sm-12 col-md-6">
                                    <h3 class="mb-3">{{ __('deductions') }}</h3>
                                    @foreach ($user->staff->staffSalary as $row)
                                        @if ($row->payrollSetting->type == 'deduction')
                                            <div class="row delete-type">
                                                <div class="form-group col-sm-12 col-md-5">
                                                    <label>{{ __('deduction_type') }} </label>
                                                    <select disabled id="deduction_id" name="deduction_type"
                                                        class="form-control deduction_id">
                                                        <option value="">--{{ __('select') }}--</option>
                                                        @foreach ($deductions as $deduction)
                                                            @if ($row->payroll_setting_id == $deduction->id)
                                                                <option selected value="{{ $deduction->id }}"
                                                                    data-value="{{ isset($deduction->amount) ? $deduction->amount : $deduction->percentage }}"
                                                                    data-type="{{ isset($deduction->amount) ? 'amount' : 'percentage' }}">
                                                                    {{ $deduction->name }}</option>
                                                            @else
                                                                <option value="{{ $deduction->id }}"
                                                                    data-value="{{ isset($deduction->amount) ? $deduction->amount : $deduction->percentage }}"
                                                                    data-type="{{ isset($allowance->amount) ? 'amount' : 'percentage' }}">
                                                                    {{ $deduction->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @if ($row->amount)
                                                    <div class="form-group col-md-5" id="amount_deduction_div">
                                                        <label>{{ __('amount') }} <span class="text-danger">*</span></label>
                                                        <input disabled type="number" id="deduction_amount" name="deduction_amount" class="deduction_amount form-control" placeholder="{{ __('amount') }}" value="{{ $row->amount }}" required>
                                                    </div>
                                                @else
                                                    <div class="form-group col-md-5" id="percentage_deduction_div">
                                                        <label>{{ __('percentage') }} <span class="text-danger">*</span></label>
                                                        <input disabled type="number" id="deduction_percentage" name="deduction_percentage" class="deduction_percentage form-control" placeholder="{{ __('percentage') }}" required value="{{ $row->percentage }}">
                                                    </div>
                                                @endif
                                                <div class="form-group col-sm-12 col-md-2 mt-4">
                                                    <button type="button" class="btn btn-inverse-danger delete-payroll-setting btn-icon" data-id="{{ $row->id }}"> <i class="fa fa-times"></i> </button>
                                                </div>
                                            </div>
                                        @endif

                                    @endforeach

                                    <div class="form-group col-sm-12 deduction-div">
                                        <div data-repeater-list="deduction_data">
                                            <div class="row deduction_type_div" id="deduction_type_div" data-repeater-item>
                                                <div class="form-group col-md-5">
                                                    <label>{{ __('deduction_type') }} </label>
                                                    <select id="deduction_id" name="deduction[0][id]" class="form-control deduction_id">
                                                        <option value="">--{{ __('select') }}--</option>
                                                        @foreach ( $deductions->where('deleted_at',null) as  $deduction)
                                                            <option value="{{ $deduction->id }}" data-value="{{ (isset($deduction->amount)) ? $deduction->amount : $deduction->percentage }}" data-type="{{ (isset($deduction->amount)) ? 'amount' : 'percentage' }}">{{ $deduction->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-5" id="amount_deduction_div" style="display: none">
                                                    <label>{{ __('amount') }} <span class="text-danger">*</span></label>
                                                    <input type="number" id="deduction_amount" name="deduction[0][amount]" class="deduction_amount real_time_change form-control" placeholder="{{ __('amount') }}" required>
                                                </div>
                                                
                                                <div class="form-group col-md-5" id="percentage_deduction_div" style="display: none">
                                                    <label>{{ __('percentage') }} <span class="text-danger">*</span></label>
                                                    <input type="number" id="deduction_percentage" name="deduction[0][percentage]" class="deduction_percentage real_time_change form-control" placeholder="{{ __('percentage') }}" required>
                                                </div>

                                                <div class="form-group col-xl-1 mt-4">
                                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-deduction-div" data-repeater-delete>
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div> 
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="form-group col-sm-12 mt-4">
                                        <button type="button" class="btn btn-inverse-success add-deduction-div"
                                                data-repeater-create>
                                            <i class="fa fa-plus"></i> {{ __('add_new_deduction') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{-- Summary --}}
                            <div class="row mb-3">
                                <div class="form-form col-sm-12 col-md-3">
                                    <label for="">{{ __('basic_salary') }}</label>
                                    <input type="text" name="basic_salary" disabled value="{{ $user->staff->salary }}" class="form-control basic-salary">
                                </div>
                                <div class="form-form col-sm-12 col-md-3">
                                    <label for="">{{ __('allowances') }}</label>
                                    <input type="text" name="allowance" disabled class="form-control total_allowance">
                                </div>
                                <div class="form-form col-sm-12 col-md-3">
                                    <label for="">{{ __('deductions') }}</label>
                                    <input type="text" name="deduction" disabled class="form-control total_deduction">
                                </div>
                                <div class="form-form col-sm-12 col-md-3">
                                    <label for="">{{ __('net_salary') }}</label>
                                    <input type="text" name="net_salary" disabled class="form-control net_salary">
                                </div>
                            </div>
                            <input type="submit" class="btn btn-theme float-right" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

<script>

    $(document).on('change', '.real_time_change', function (e) { 
        e.preventDefault();
        calculate_net_salary();
    });

    var basic_salary = parseFloat($('.basic-salary').val());

    window.onload = setTimeout(() => {
        calculate_net_salary();
    }, 500);

    function calculate_net_salary()
    {
        total_allowance = allowance_calculation();
        total_deduction = deduction_calculation();
        $('.total_allowance').val(total_allowance);
        $('.total_deduction').val(total_deduction);
        net_salary = basic_salary + total_allowance - total_deduction;
        $('.net_salary').val(net_salary);
    }

    function allowance_calculation()
    {
        let allowance_amount = 0, allowance_percentage = 0, total_allowance = 0, total_deduction = 0;
        $('.allowance_amount').each(function() {
            if ($(this).val()) {
                allowance_amount += parseFloat($(this).val());
            } 
        });
        $('.allowance_percentage').each(function() {
            if ($(this).val()) {
                allowance_percentage += parseFloat($(this).val());
            } 
        });
        
        total_allowance = (basic_salary * allowance_percentage) / 100;
        total_allowance += allowance_amount;

        return total_allowance;
    }

    function deduction_calculation()
    {
        let deduction_amount = 0, deduction_percentage = 0, total_deduction = 0;
        $('.deduction_amount').each(function() {
            if ($(this).val()) {
                deduction_amount += parseFloat($(this).val());
            } 
        });
        $('.deduction_percentage').each(function() {
            if ($(this).val()) {
                deduction_percentage += parseFloat($(this).val());
            } 
        });
        var basic_salary = $('.basic-salary').val();
        total_deduction = (basic_salary * deduction_percentage) / 100;
        total_deduction += deduction_amount;

        return total_deduction;
    }
</script>


    <script type="text/javascript">
        let allowanceCounter = 1; // Initialize a counter for new allowance rows
        // Function to toggle visibility of amount and percentage fields
        function toggleAllowanceFields(allowanceTypeElement) {
            const selectedOption = allowanceTypeElement.options[allowanceTypeElement.selectedIndex];
            const allowanceType = selectedOption.getAttribute('data-type');
            const allowanceValue = selectedOption.getAttribute('data-value');
            const allowanceDiv = allowanceTypeElement.closest('.allowance_type_div');
            const amountDiv = allowanceDiv.querySelector('#amount_allowance_div');
            const percentageDiv = allowanceDiv.querySelector('#percentage_allowance_div');

            if (allowanceType === 'amount') {
                percentageDiv.style.display = 'none';
                amountDiv.style.display = 'block';
                allowanceDiv.querySelector('.allowance_amount').value = allowanceValue;
                allowanceDiv.querySelector('.allowance_percentage').value = '';
            } else if (allowanceType === 'percentage') {
                amountDiv.style.display = 'none';
                percentageDiv.style.display = 'block';
                allowanceDiv.querySelector('.allowance_amount').value = '';
                allowanceDiv.querySelector('.allowance_percentage').value = allowanceValue;
            } else {
                amountDiv.style.display = 'none';
                percentageDiv.style.display = 'none';
                allowanceDiv.querySelector('.allowance_amount').value = '';
                allowanceDiv.querySelector('.allowance_percentage').value = '';
            }

            setTimeout(() => {
                calculate_net_salary();
            }, 500);
            
        }

        // Attach change event listener to the initial allowance type dropdown
        document.querySelectorAll('.allowance_id').forEach(function(element) {
            element.addEventListener('change', function() {
                toggleAllowanceFields(element);
            });
        });



        // Repeater functionality to handle adding new allowance rows
        const addAllowanceButton = document.querySelector('.add-allowance-div');
        const allowanceDataContainer = document.querySelector('[data-repeater-list="allowance_data"]');

        addAllowanceButton.addEventListener('click', function() {
            const newItem = allowanceDataContainer.querySelector('[data-repeater-item]').cloneNode(true);

            // Clear input values
            allowanceDataContainer.querySelector('#allowance_type_div').style.display = '';
            newItem.querySelectorAll('input').forEach(input => input.value = '');
            newItem.querySelector('.allowance_id').value = '';
            newItem.querySelector('#amount_allowance_div').style.display = 'none';
            newItem.querySelector('#percentage_allowance_div').style.display = 'none';

            // Update the name attributes
            newItem.querySelectorAll('[name]').forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/\[\d+\]/, `[${allowanceCounter}]`);
                input.setAttribute('name', newName);
            });

            // Increment the counter
            allowanceCounter++;

            // Add event listeners to new elements
            newItem.querySelector('.allowance_id').addEventListener('change', function() {
                toggleAllowanceFields(newItem.querySelector('.allowance_id'));
            });
            newItem.querySelector('.remove-allowance-div').addEventListener('click', function() {
                newItem.remove();
                setTimeout(() => {
                    calculate_net_salary();
                }, 500);
            });

            allowanceDataContainer.appendChild(newItem);
        });

        // Attach click event listener to the initial remove button
        document.querySelectorAll('.remove-allowance-div').forEach(function(button) {
            button.addEventListener('click', function() {
                // button.closest('[data-repeater-item]').remove();

                // reset the select option
                const element = document.getElementById("allowance_id");
                element.selectedIndex = '0';

                button.closest('[data-repeater-item]').style.display = 'none';
            });
        });


        let deductionCounter = 1; // Initialize a counter for new deduction rows

        // Function to toggle visibility of amount and percentage fields
        function toggleDeductionFields(deductionTypeElement) {
            const selectedOption = deductionTypeElement.options[deductionTypeElement.selectedIndex];
            const deductionType = selectedOption.getAttribute('data-type');
            const deductionValue = selectedOption.getAttribute('data-value');
            const deductionDiv = deductionTypeElement.closest('.deduction_type_div');
            const amountDiv = deductionDiv.querySelector('#amount_deduction_div');
            const percentageDiv = deductionDiv.querySelector('#percentage_deduction_div');

            if (deductionType === 'amount') {
                percentageDiv.style.display = 'none';
                amountDiv.style.display = 'block';
                deductionDiv.querySelector('.deduction_amount').value = deductionValue;
                deductionDiv.querySelector('.deduction_percentage').value = '';
            } else if (deductionType === 'percentage') {
                amountDiv.style.display = 'none';
                percentageDiv.style.display = 'block';
                deductionDiv.querySelector('.deduction_amount').value = '';
                deductionDiv.querySelector('.deduction_percentage').value = deductionValue;
            } else {
                amountDiv.style.display = 'none';
                percentageDiv.style.display = 'none';
                deductionDiv.querySelector('.deduction_amount').value = '';
                deductionDiv.querySelector('.deduction_percentage').value = '';
            }

            setTimeout(() => {
                calculate_net_salary();
            }, 500);
        }

        // Attach change event listener to the initial deduction type dropdown
        document.querySelectorAll('.deduction_id').forEach(function(element) {
            element.addEventListener('change', function() {
                toggleDeductionFields(element);
            });
        });

        // Repeater functionality to handle adding new deduction rows
        const addDeductionButton = document.querySelector('.add-deduction-div');
        const deductionDataContainer = document.querySelector('[data-repeater-list="deduction_data"]');

        addDeductionButton.addEventListener('click', function() {
            const newItem = deductionDataContainer.querySelector('[data-repeater-item]').cloneNode(true);

            // Clear input values
            deductionDataContainer.querySelector('#deduction_type_div').style.display = '';
            newItem.querySelectorAll('input').forEach(input => input.value = '');
            newItem.querySelector('.deduction_id').value = '';
            newItem.querySelector('#amount_deduction_div').style.display = 'none';
            newItem.querySelector('#percentage_deduction_div').style.display = 'none';

            // Update the name attributes
            newItem.querySelectorAll('[name]').forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/\[\d+\]/, `[${deductionCounter}]`);
                input.setAttribute('name', newName);
            });

            // Increment the counter
            deductionCounter++;

            // Add event listeners to new elements
            newItem.querySelector('.deduction_id').addEventListener('change', function() {
                toggleDeductionFields(newItem.querySelector('.deduction_id'));
            });
            newItem.querySelector('.remove-deduction-div').addEventListener('click', function() {
                newItem.remove();
            });

            deductionDataContainer.appendChild(newItem);
        });

        // Attach click event listener to the initial remove button
        document.querySelectorAll('.remove-deduction-div').forEach(function(button) {
            button.addEventListener('click', function() {
                // button.closest('[data-repeater-item]').remove();

                // reset the select option
                const element = document.getElementById("deduction_id");
                element.selectedIndex = '0';

                button.closest('[data-repeater-item]').style.display = 'none';
            });
        });

        function formSuccessFunction(response) {
            setTimeout(() => {
                location.reload();
            }, 2000);
        }
    </script>
@endsection
