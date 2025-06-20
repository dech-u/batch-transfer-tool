@extends('layouts.master')
@section('title')
    {{ __('dashboard') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-theme text-white mr-2">
                    <i class="fa fa-home"></i>
                </span> {{ __('dashboard') }}
            </h3>
        </div>
        {{-- School Dashboard --}}
        @if (Auth::user()->hasRole('School Admin') || Auth::user()->school_id)
            <div class="row">
                {{-- License expire message --}}
                @if (Auth::user()->hasRole('School Admin'))
                    @if ($license_expire <= ($settings['current_plan_expiry_warning_days'] ?? 7) && $subscription)
                        <div class="col-sm-12 col-md-12">
                            <div class="alert alert-danger" role="alert">
                                <li>
                                    {{ __('Kindly note that your license will expire on') }} <strong
                                        class="package-expire-date">
                                        {{ date('F d, Y', strtotime($subscription->end_date)) }} - 11:59 PM. </strong>
                                    {{ __('If you want to modify your upcoming plan or remove any add-ons, please ensure that these changes are made before your current license expires') }}.
                                </li>
                                <li class="mt-2">
                                    {{ __('If you want to activate or deactivate students, teachers, or staff members in your upcoming plan, Please') }}
                                    <a href="{{ url('users/status') }}">{{ __('click here') }}.</a>
                                </li>

                                @if ($prepiad_upcoming_plan && $prepiad_upcoming_plan->package->type == 0 && !$check_payment)
                                    
                                    @if ($paymentConfiguration && $paymentConfiguration->payment_method == 'Stripe')
                                        <li class="mt-2">
                                        {{ __('We kindly request that you make the necessary payment for the upcoming prepaid plan to avoid any interruptions in service') }}
                                            <a href="{{ url('subscriptions/pay-prepaid-upcoming-plan', $prepiad_upcoming_plan->package_id) . '/type/' . $prepiad_upcoming_plan_type . '/subscription/' . $prepiad_upcoming_plan->id }}">{{ __('click_here_to_pay') }}</a>
                                        </li>
                                    @else
                                        <form action="{{ url('subscriptions/razorpay') }}" class="razorpay-form" method="POST">
                                            @csrf
                                            <input type="hidden" name="package_id" class="package_id" value="{{ $prepiad_upcoming_plan->package_id }}">
                                            <input type="hidden" name="amount" class="bill_amount" value="{{ $prepiad_upcoming_plan->charges }}">

                                            <input type="hidden" name="type" class="type" value="package">
                                            <input type="hidden" name="package_type" class="package_type" value="upcoming">

                                            <input type="hidden" name="razorpay_payment_id" class="razorpay_payment_id" value="">
                                            <input type="hidden" name="razorpay_signature" class="razorpay_signature" value="">
                                            <input type="hidden" name="razorpay_order_id" class="razorpay_order_id" value="">

                                            <input type="hidden" name="paymentTransactionId" class="paymentTransactionId" value="">

                                            <input type="hidden" name="subscription_id" class="subscription_id" value="{{ $prepiad_upcoming_plan->id }}">
                                            <input type="hidden" name="upcoming_plan_type" value="{{ $prepiad_upcoming_plan_type }}">

                                            <li class="mt-2">
                                                {{ __('We kindly request that you make the necessary payment for the upcoming prepaid plan to avoid any interruptions in service') }}
                                                <a href="#" id="razorpay-button">{{ __('click_here_to_pay') }}</a>
                                            </li>
                                            {{-- <button class="btn btn-theme" id="razorpay-button">{{ __('razorpay') }}</button> --}}
                                        </form>

                                        
                                    @endif                                        
                                    
                                @endif

                            </div>
                        </div>
                    @endif

                    <div class="col-sm-12 col-md-12">
                        @foreach ($previous_subscriptions as $subscription)
                            @if ($subscription->status == 3)
                                <div class="alert alert-danger" role="alert">
                                    {{ __('Please make the necessary payment as your license has expired on') }} <strong
                                        class="package-expire-date">
                                        {{ date('F d, Y', strtotime($subscription->end_date)) }}.
                                    </strong>
                                </div>
                            @break
                        @endif
                        @if ($subscription->status == 4)
                            <div class="alert alert-danger" role="alert">
                                {{ __('We apologize for inconvenience but your payment was not successful Please try to process the payment again') }}.
                            </div>
                        @break
                    @endif
                @endforeach
            </div>
        @endif
    </div>
    @if (Auth::user()->hasRole('School Admin'))
        <div class="row">
            {{-- Teachers --}}
            <div class="col-md-2-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <div class="d-flex flex-row flex-wrap">
                            <div class="ms-3">
                                {{ __('total_teachers') }}
                                <p class="text-muted">
                                <h3>{{ $teacher }}</h3>
                                </p>
                                <p class="mt-2 text-success font-weight-bold"> </p>
                            </div>
                            <img class="ml-auto" src="{{ url('images/teachers.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            {{-- Students --}}
            <div class="col-md-2-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <div class="d-flex flex-row flex-wrap">
                            <div class="ms-3">
                                {{ __('total_students') }}
                                <p class="text-muted">
                                <h3>{{ $student }}</h3>
                                </p>
                                <p class="mt-2 text-success font-weight-bold"> </p>
                            </div>
                            <img class="ml-auto" src="{{ url('images/students.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            {{-- Guardians --}}
            <div class="col-md-2-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <div class="d-flex flex-row flex-wrap">
                            <div class="ms-3">
                                {{ __('Total Guardians') }}
                                <p class="text-muted">
                                <h3>{{ $parent }}</h3>
                                </p>
                                <p class="mt-2 text-success font-weight-bold"> </p>
                            </div>
                            <img class="ml-auto" src="{{ url('images/guardians.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            {{-- Class --}}
            <div class="col-md-2-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <div class="d-flex flex-row flex-wrap">
                            <div class="ms-3">
                                {{ __('total_classes') }}
                                <p class="text-muted">
                                <h3>{{ $classes_counter }}</h3>
                                </p>
                                <p class="mt-2 text-success font-weight-bold"> </p>
                            </div>
                            <img class="ml-auto" src="{{ url('images/classes.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            {{-- Stream --}}
            <div class="col-md-2-4 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <div class="d-flex flex-row flex-wrap">
                            <div class="ms-3">
                                {{ __('total_streams') }}
                                <p class="text-muted">
                                <h3>{{ $streams }}</h3>
                                </p>
                                <p class="mt-2 text-success font-weight-bold"> </p>
                            </div>
                            <img class="ml-auto" src="{{ url('images/stream.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- End Counter --}}

    <div class="row">

        {{-- Expense Graph --}}
        @if (Auth::user()->canany(['expense-create', 'expense-list']))
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body custom-card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <h4 class="card-title">{{ __('expense') }}</h4>
                            </div>
                            <div class="col-md-3 text-right">
                                <select name="session_year_id" id="filter_expense_session_year_id"
                                    class="form-control form-control-sm">
                                    @foreach ($sessionYear as $session)
                                        @if ($session->default == 1)
                                            <option value="{{ $session->id }}" selected>{{ $session->name }}</option>
                                        @else
                                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                                        @endif
                                        
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @hasNotFeature('Expense Management')
                            <div
                                class="align-items-center d-flex flex-column justify-content-center v-scroll text-small">
                                {{ __('Purchase') . ' ' . __('Expense Management') . ' ' . __('to Continue using this functionality') }}
                            </div>
                        @endHasNotFeature

                        @hasFeature('Expense Management')
                            <div class="chartjs-wrapper mt-2" style="height: 330px">
                                <div id="expenseChart" style="direction: ltr;"> </div>
                            </div>
                        @endHasFeature

                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">{{ __('leaves') }}</h3>
                        </div>
                        <div class="col-md-6 dropdown text-right">
                            {!! Form::select(
                                'leave_filter',
                                ['Today' => __('today'), 'Tomorrow' => __('tomorrow'), 'Upcoming' => __('upcoming')],
                                'today',
                                ['class' => 'form-control form-control-sm filter_leaves'],
                            ) !!}
                        </div>
                    </div>

                    <div class="v-scroll mt-2">
                        <table class="table custom-table">
                            @hasNotFeature('Staff Leave Management')
                                <tbody class="leave-list">
                                    <tr>
                                        <td colspan="2" class="text-center text-small">
                                            {{ __('Purchase') . ' ' . __('Staff Leave Management') . ' ' . __('to Continue using this functionality') }}
                                        </td>
                                    </tr>
                                </tbody>
                            @endHasNotFeature

                            @hasFeature('Staff Leave Management')
                                <tbody class="leave-list">

                                </tbody>
                            @endHasFeature


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <h3 class="card-title">{{ __('attendance') }}</h3>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            {!! Form::select('class_id', count($class_names) > 0 ? $class_names : ['' => 'Not Data Available'], null, ['class' => 'form-control form-control-sm class-section-attendance',]) !!}
                        </div>
                    </div>
                    <div id="attendanChart">

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <h4 class="card-title">{{ __('announcement') }}</h4>
                    <div class="table-responsive v-scroll">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> {{ __('no.') }}</th>
                                    <th class="col-md-2"> {{ __('title') }}</th>
                                    <th> {{ __('description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($announcement))
                                    @foreach ($announcement as $key => $row)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $row->title }}</td>
                                            <td>{{ $row->description }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <h3 class="card-title">
                                {{ __('exam_result') }}
                            </h3>
                        </div>
                        <div class="col-md-3">
                            <select name="session_year_id" id="exam_result_session_year_id"
                                class="form-control form-control-sm">
                                @foreach ($sessionYear as $session)
                                    @if ($session->default == 1)
                                        <option value="{{ $session->id }}" selected>{{ $session->name }}</option>
                                    @else
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="exam_name" id="exam_reuslt_exam_name"
                                class="form-control form-control-sm">
                                <option value="">{{ __('select') . ' ' . __('exam') }}</option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->name }}"
                                        data-session-year="{{ $exam->session_year_id }}">
                                        {{ $exam->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-1 mb-3 v-scroll">
                        <div class="exam-report" id="class-progress-report">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <h3 class="card-title">
                                {{ __('fees_over_due') }}  <span class="ml-2"><i class="fa fa-exclamation-circle" title="{{ __('inactivate_student_by_submitting_records') }}"></i></span>
                            </h3>
                        </div>
                        <div class="col-sm-12 col-md-5">
                            {!! Form::select('class_section_id', $class_section_names, null, [
                                'class' => 'form-control form-control-sm fees-over-due-class',
                                'id' => 'fees-over-due-class-section'
                            ]) !!}
                        </div>
                    </div>
                    <form method="POST" id="fees-overdue-form" class="fees-overdue-form" action="{{ route('deactivate-student-account') }}">
                        <div class="mt-1 mb-3 v-scroll">
                            <table class="table custom-table">
                                @hasNotFeature('Fees Management')
                                    <tbody class="leave-list">
                                        <tr>
                                            <td colspan="2" class="text-center text-small">
                                                {{ __('Purchase') . ' ' . __('Fees Management') . ' ' . __('to Continue using this functionality') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                @endHasNotFeature

                                @hasFeature('Fees Management')
                                    <tbody class="fees-over-due-list">
                                    </tbody>
                                @endHasFeature  
                            </table>           
                        </div>
                        @hasFeature('Fees Management')
                            <input type="submit" class="btn btn-success float-right fees-overdue-btn" name="submit" value="{{ __('submit') }}">
                        @endHasFeature
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <h3 class="card-title">
                        {{ __('fees_details') }}
                    </h3>

                    <div id="fees_details_chart">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">{{ __('birthday') }}</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            {!! Form::select(
                                'birthday_filter',
                                ['today' => __('today'), 'this_month' => __('this_month'), 'next_month' => __('next_month')],
                                'today',
                                ['class' => 'form-control form-control-sm filter_birthday'],
                            ) !!}
                        </div>
                    </div>
                    <div class="v-scroll mt-2">
                        <table class="table custom-table">
                            <tbody class="birthday-list">

                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <h4 class="card-title">{{ __('holiday') }}</h4>
                    <div class="v-scroll dashboard-description">
                        <table class="table custom-table">
                            @hasNotFeature('Holiday Management')
                                <tbody class="leave-list">
                                    <tr>
                                        <td colspan="2" class="text-center text-small">
                                            {{ __('Purchase') . ' ' . __('Holiday Management') . ' ' . __('to Continue using this functionality') }}
                                        </td>
                                    </tr>
                                </tbody>
                            @endHasNotFeature

                            @hasFeature('Holiday Management')
                                <tbody>
                                    @foreach ($holiday as $holiday)
                                    <tr>
                                        <td>{{ $holiday->title }}</td>
                                        <td><span
                                                class="float-right text-muted">{{ date('d - M', strtotime($holiday->date)) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            @endHasFeature
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <h4 class="card-title">{{ __('student_gender') }}</h4>
                    <div id="gender-ratio-chart"></div>
                </div>
            </div>
        </div>
    </div>
@endif
{{-- End School Dashboard --}}

{{-- Super Admin Dashboard --}}
@if (Auth::user()->hasRole('Super Admin') || !Auth::user()->school_id)
    <div class="row">

        <div class="col-md-3 stretch-card grid-margin">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="d-flex flex-row flex-wrap">
                        <div class="ms-3">
                            {{ __('total_schools') }}
                            <p class="text-muted">
                            <h3>{{ $super_admin['total_school'] }}</h3>
                            </p>
                            <p class="mt-2 text-success font-weight-bold"> </p>
                        </div>
                        <img class="ml-auto" src="{{ url('images/total-schools.svg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 stretch-card grid-margin">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="d-flex flex-row flex-wrap">
                        <div class="ms-3">
                            {{ __('active_schools') }}
                            <p class="text-muted">
                            <h3>{{ $super_admin['active_schools'] }}</h3>
                            </p>
                            <p class="mt-2 text-success font-weight-bold"> </p>
                        </div>
                        <img class="ml-auto" src="{{ url('images/active-schools.svg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 stretch-card grid-margin">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="d-flex flex-row flex-wrap">
                        <div class="ms-3">
                            {{ __('inactive_schools') }}
                            <p class="text-muted">
                            <h3>{{ $super_admin['inactive_schools'] }}</h3>
                            </p>
                            <p class="mt-2 text-success font-weight-bold"> </p>
                        </div>
                        <img class="ml-auto" src="{{ url('images/inactive-schools.svg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 stretch-card grid-margin">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="d-flex flex-row flex-wrap">
                        <div class="ms-3">
                            {{ __('total_packages') }}
                            <p class="text-muted">
                            <h3>{{ $super_admin['total_packages'] }}</h3>
                            </p>
                            <p class="mt-2 text-success font-weight-bold"> </p>
                        </div>
                        <img class="ml-auto" src="{{ url('images/package.svg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title">
                                {{ __('transaction') }}
                            </h4>
                        </div>
                        <div class="col-md-3">
                            {!! Form::selectRange('year', $start_year, date('Y'), date('Y'), [
                                'class' => 'form-control form-control-sm year-filter',
                            ]) !!}
                        </div>
                    </div>

                    <div id="subscriptionTransactionChart">

                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <h4 class="card-title">
                        {{ __('schools') }}
                    </h4>
                    <div class="v-scroll">
                        <table class="table custom-table">
                            <thead>
                                <th></th>
                                <th>{{ __('school') }}</th>
                                <th class="text-right">{{ __('admin') }}</th>
                            </thead>
                            <tbody>
                                @foreach ($schools as $school)
                                    <tr>
                                        <td>
                                            <img src="{{ $school->logo }}" onerror="onErrorImage(event)"
                                                class="me-2" alt="image">
                                        </td>
                                        <td>{{ $school->name }}</td>
                                        <td class="text-right">{{ $school->user->full_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <h4 class="card-title">
                        {{ __('subscription') }} {{ __('details') }}
                    </h4>
                    <div id="packageChart"> </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <h4 class="card-title">
                        {{ __('staff') }} {{ __('details') }}
                    </h4>
                    <div class="v-scroll">
                        <table class="table custom-table">
                            @hasNotFeature('Staff Management')
                                <thead>
                                    <th></th>
                                    <th>{{ __('name') }}</th>
                                    <th>{{ __('role') }}</th>
                                    <th class="text-right">{{ __('assign_schools') }}</th>
                            </thead>
                            @endHasNotFeature
                            @hasFeature('Staff Management')
                                <tbody>
                                    @foreach ($staffs as $staff)
                                        <tr>
                                        <td>
                                            <img src="{{ $staff->image }}" onerror="onErrorImage(event)"
                                                class="me-2" alt="image">
                                        </td>
                                        <td>{{ $staff->full_name }}</td>
                                        <td>{{ $staff->roles->first()->name ?? '' }}</td>
                                        <td>{{ $staff->school_names }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            @endHasFeature
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body custom-card-body">
                    <h4 class="card-title">
                        {{ __('addon') }}
                    </h4>
                    <div id="addonChart"> </div>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
@endsection
@section('script')

@if (Auth::user()->school_id)
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

@endif




@if (!Auth::user()->school_id)
<script>
    window.onload = setTimeout(() => {
        $('.year-filter').trigger('change');

        addon_graph(<?php echo json_encode($addon_graph[0]); ?>, <?php echo json_encode($addon_graph[1]); ?>);
        package_graph(<?php echo json_encode($package_graph[0]); ?>, <?php echo json_encode($package_graph[1]); ?>);
    }, 500);
</script>
@endif
<script>
    window.onload = setTimeout(() => {
        $('#filter_expense_session_year_id').trigger('change');
        $('.filter_birthday').trigger('change');
        $('.filter_leaves').trigger('change');
        $('#exam_result_session_year_id').trigger('change');
        
        const selectElement = document.getElementById('exam_reuslt_exam_name');
        if (selectElement) {
            var selectedIndex = selectElement.selectedIndex || 0;
            var options = selectElement.options;
            
            // Iterate through options starting from the next index
            for (var i = selectedIndex + 1; i < options.length; i++) {
                if (options[i].style.display !== "none") {
                    // Set the next visible option as selected
                    selectElement.selectedIndex = i;
                    break;
                }
            }
        }


        $('#exam_reuslt_exam_name').trigger('change');
        fees_details(<?php echo json_encode($fees_detail); ?>);

        $('.class-section-attendance').trigger('change');
        $('#fees-over-due-class-section').trigger('change');

    }, 500);
</script>

@if ($boys || $girls)
<script>
    gender_ratio(<?php echo $boys; ?>, <?php echo $girls; ?>, <?php echo $total_students; ?>);
</script>
@endif
@endsection
