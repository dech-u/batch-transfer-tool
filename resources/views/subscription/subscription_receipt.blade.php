<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt</title>

    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
        }
        .full-width-table {
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bill {
            font-size: 30px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .mt-3 {
            margin-top: 2.5rem;
        }

        .table-heading th {
            background-color: rgb(218, 218, 218);
            padding: 10px 0 !important;

        }

        table {
            border-collapse: collapse;
        }

        .bill-info tr td,
        tr th {
            padding: 10px;
        }

        .bill-info tr td,
        tr th {
            border: 1px solid rgb(183, 183, 183);
        }

        .mt-1 {
            margin-top: 0.5rem;
        }

        .system-address {
            white-space: pre-wrap;
        }

        .badge-outline-success {
            color: #1bcfb4;
            border: 1px solid #1bcfb4;
        }

        .badge-outline-danger {
            color: #fe7c96;
            border: 1px solid #fe7c96;
        }

        .badge-outline-warning {
            color: #fed713;
            border: 1px solid #fed713;
        }

        .badge {
            border-radius: 0.125rem;
            font-size: 11px;
            font-weight: initial;
            line-height: 1;
            padding: 0.375rem 0.5625rem;
            font-family: "ubuntu-medium", sans-serif;

            display: inline-block;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;

            text-transform: uppercase;
        }

        .alert-danger {
            color: #ff2b55;
            letter-spacing: 1px;
        }

        .alert {
            font-size: 0.975rem;
        }
        .logo {
            height: 5%;
            width: auto;
        }
        .total_paidable_amount {
            background-color: lightgray;
        }
        .text-info {
            color: #198ae3 !important;
            font-size: 12px;
        }

    </style>
</head>

<body>
    <div class="body">
        <div class="header">
            <table class="full-width-table">
                <tr>
                    <td>
                        <div>
                            @if ($settings['horizontal_logo'])
                                <img class="logo" src="{{ public_path('storage/super-admin/system-settings/' . $settings['horizontal_logo']) }}" alt="">
                            @else
                                <img class="logo" src="{{ public_path('assets/no_image_available.jpg') }}" alt="">    
                            @endif
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="">
                            <span class="bill">Bill</span><br>
                            <span>#
                                {{ date('Y', strtotime($subscriptionBill->subscription->start_date)) }}0{{ $subscriptionBill->id }}</span>
                            <br>
                            <div class="mt-1">
                                @if ($status == 'failed')
                                    <div class="badge badge-outline-danger">
                                        {{-- {{ __('failed') }} --}}
                                        Failed
                                    </div>
                                @elseif($status == 'succeed' || $subscriptionBill->amount == 0 || $subscriptionBill->subscription_bill_payment)
                                    <div class="badge badge-outline-success">
                                        {{-- {{ __('paid') }} --}}
                                        Paid
                                    </div>
                                @elseif($status == 'pending')
                                    <div class="badge badge-outline-warning">
                                        {{-- {{ __('Pending') }} --}}
                                        Pending
                                    </div>
                                @else
                                    <div class="badge badge-outline-danger">
                                        {{-- {{ __('unpaid') }} --}}
                                        Unpaid
                                    </div>
                                @endif
                            </div>

                            <div class="mt-1">
                                @if ($subscriptionBill->transaction)
                                    <div class="badge badge-outline-success">
                                        {{ $subscriptionBill->transaction->payment_gateway }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="mt-3">
                            <b>{{ $settings['system_name'] }}</b>
                        </div>
                        <div class="mt-1 system-address">{{ $settings['address'] }}</div>
                    </td>
                    <td class="text-right" width="300">
                        <div class="mt-3">
                            <b>Bill No. :</b>
                        </div>
                        <div class="mt-1">
                            {{ $school_settings['school_name'] }}
                        </div>
                        <div>
                            <b>Billing Cycle :</b>
                            {{ format_date($subscriptionBill->subscription->start_date) }} - {{ format_date($subscriptionBill->subscription->end_date) }}
                        </div>
                        <div class="mt-1">
                            <b>Invoice Date : </b>
                            {{ format_date($subscriptionBill->subscription->bill_date) }}
                        </div>
                        <div class="mt-1">
                            <b>Due Date : </b>
                            {{ format_date($subscriptionBill->due_date) }}
                        </div>
                        <div class="mt-1">
                            @if ($subscriptionBill->transaction)
                                <strong>Transaction ID : </strong> {{ $transaction_id ?? null }}
                            @endif
                            @if ($subscriptionBill->subscription_bill_payment)
                                @if ($subscriptionBill->subscription_bill_payment->payment_type == 'Cheque')
                                    <strong>Cheque No. : </strong>
                                    {{ $subscriptionBill->subscription_bill_payment->cheque_number }}
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        @php
            $student_charges = number_format(($subscriptionBill->subscription->student_charge / $subscriptionBill->subscription->billing_cycle) * $usage_days, 2);

            $staff_charges = number_format(($subscriptionBill->subscription->staff_charge / $subscriptionBill->subscription->billing_cycle) * $usage_days, 2);

        @endphp
        <div class="main-body mt-3">
            <table class="full-width-table bill-info">
                @if ($subscriptionBill->subscription->package_type == 1)
                <tr class="table-heading">
                    <th>Plan</th>
                    <th>User</th>
                    <th>Charges ({{ $settings['currency_symbol'] }})</th>
                    <th>Total Users</th>
                    <th>Total Amount ({{ $settings['currency_symbol'] }})</th>
                </tr>
                <tr>
                    <td rowspan="2">
                        <div>
                            {{ $subscriptionBill->subscription->name }}
                        </div>
                        <div class="text-info">
                            @if ($subscriptionBill->subscription->package_type == 1)
                                Postpaid
                            @else
                            Prepaid
                            @endif
                        </div>
                    </td>
                    <td>Students</td>
                    <td class="text-right">{{ $student_charges }}</td>
                    <td class="text-right">{{ $subscriptionBill->total_student }}</td>
                    <td class="text-right">
                        {{ number_format($student_charges * $subscriptionBill->total_student, 2) }}
                    </td>
                </tr>

                <tr>
                    <td>Staffs</td>
                    <td class="text-right">{{ $staff_charges }}</td>
                    <td class="text-right">{{ $subscriptionBill->total_staff }}</td>
                    <td class="text-right">
                        {{ number_format($staff_charges * $subscriptionBill->total_staff, 2) }}
                    </td>

                    @php
                        $total_user_charges = ($student_charges * $subscriptionBill->total_student) + ($staff_charges * $subscriptionBill->total_staff);
                    @endphp
                </tr>
                <tr>
                    <th colspan="4" class="text-left"> Total User Charges : </th>
                    <th class="text-right">{{ $settings['currency_symbol'] }}
                        {{ number_format($total_user_charges, 2) }}</th>
                </tr>
                @else
                <tr>
                    <th colspan="4" class="text-left"> Package Amount : </th>
                    <th class="text-right">{{ $settings['currency_symbol'] }}
                        {{ number_format($subscriptionBill->subscription->charges, 2) }}</th>
                </tr>
                @endif
                
                <tr>
                    <th colspan="5">Addon Charges</th>
                </tr>
                @if ($subscriptionBill->subscription->package_type == 1)
                    {{-- Postpaid --}}
                    <tr class="table-heading">
                        <th colspan="4">Addon</th>
                        <th>Total Amount ({{ $settings['currency_symbol'] }})</th>
                    </tr>
                    @php
                        $addons_charges = 0;
                    @endphp
                    @foreach ($subscriptionBill->subscription->addons as $addon)
                        <tr>
                            <td colspan="4">{{ $addon->feature->name }}</td>
                            <td class="text-right">{{ number_format($addon->price, 2) }}</td>
                            @php
                                $addons_charges += $addon->price;
                            @endphp
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="4" class="text-left">Total Addon Charges : </th>
                        <th class="text-right">{{ $settings['currency_symbol'] }}
                            {{ number_format($addons_charges, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-left">Total User Charges : </th>
                        <th class="text-right">{{ $settings['currency_symbol'] }}
                            {{ number_format($total_user_charges, 2) }}</th>
                    </tr>

                    @php
                        $total_amount = $subscriptionBill->amount;
                    @endphp
                    <tr>
                        <th colspan="4" class="text-left">Total bill Amount : </th>
                        <th class="text-right">{{ $settings['currency_symbol'] }}
                            {{ number_format($total_amount, 2) }}</th>
                    </tr>

                    @if ($deafult_amount > number_format($total_amount, 2))
                        <tr>
                            <th colspan="4" class="text-left">Total Payable Amount : </th>
                            <th class="text-right">{{ $settings['currency_symbol'] }}
                                {{ number_format($deafult_amount, 2) }}</th>
                        </tr>
                    @endif
                    {{-- End Postpaid --}}
                @else
                    {{-- Prepaid --}}
                    <tr class="table-heading">
                        <th colspan="3">Addon</th>
                        <th>Status</th>
                        <th>Total Amount ({{ $settings['currency_symbol'] }})</th>
                    </tr>
                    @php
                        $addons_charges = 0;
                    @endphp
                    @foreach ($subscriptionBill->subscription->addons as $addon)
                        <tr>
                            @if ($addon->transaction)
                                @if ($addon->transaction->payment_status == "succeed")
                                    <td colspan="3">
                                        <div>
                                            {{ $addon->feature->name }}
                                        </div>
                                        <div class="text-info text-small">
                                            {{ $addon->transaction->order_id }}
                                        </div>
                                    </td>
                                    <td>Success</td>
                                    <td class="text-right">{{ number_format($addon->price, 2) }}</td>
                                    @php
                                        $addons_charges += $addon->price;
                                    @endphp
                                @else
                                    <td colspan="3">
                                        <div>
                                            {{ $addon->feature->name }}
                                        </div>
                                        <div class="text-info text-small">
                                            {{ $addon->transaction->order_id }}
                                        </div>
                                    </td>
                                    <td>{{ $addon->transaction->payment_status }}</td>
                                    <td class="text-right">{{ number_format($addon->price, 2) }}</td>
                                @endif
                                
                            @else
                                <td colspan="3">{{ $addon->feature->name }}</td>
                                <td>Failed</td>
                                <td class="text-right">{{ number_format($addon->price, 2) }}</td>
                            @endif
                            
                            
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="4" class="text-left">Total Addon Charges : </th>
                        <th class="text-right">{{ $settings['currency_symbol'] }}
                            {{ number_format($addons_charges, 2) }}</th>
                    </tr>
                    @php
                        $total_amount = $subscriptionBill->amount;
                    @endphp
                    <tr>
                        <th colspan="4" class="text-left">Package Amount : </th>
                        <th class="text-right">{{ $settings['currency_symbol'] }}
                            {{ number_format($total_amount, 2) }}</th>
                    </tr>

                    
                    <tr>
                        <th colspan="4" class="text-left">Total Bill Amount : </th>
                        <th class="text-right">{{ $settings['currency_symbol'] }}
                            {{ number_format($total_amount + $addons_charges, 2) }}</th>
                    </tr>

                    @if ($deafult_amount > number_format($total_amount, 2))
                        <tr>
                            <th colspan="4" class="text-left">Total Payable Amount : </th>
                            <th class="text-right">{{ $settings['currency_symbol'] }}
                                {{ number_format($deafult_amount, 2) }}</th>
                        </tr>
                    @endif

                    {{-- End Prepaid --}}
                @endif
                

            </table>
        </div>

        <div class="note mt-3">
            <div class="alert alert-danger">
                @if ($subscriptionBill->description)
                    NOTE : {{ $subscriptionBill->description }}    
                @endif
            </div>
        </div>

    </div>
</body>

</html>
