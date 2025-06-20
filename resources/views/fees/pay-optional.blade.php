@extends('layouts.master')

@section('title')
    {{ __('Pay Optional Fees') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Pay Optional Fees') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body d-flex justify-content-center">
                        <form class="pt-3 create-form form-validation col-sm-12 col-md-5" method="post" action="{{ route('fees.optional.store') }}" novalidate="novalidate" data-success-function="formSuccessFunction">
                            <input type="hidden" name="fees_id" id="optional-fees-id" value="{{$fees->id}}"/>
                            <input type="hidden" name="student_id" id="student-id" value="{{$student->id}}"/>
                            <input type="hidden" name="class_id" id="class-id" value="{{$student->student->class_section->class_id}}"/>
                            <h4>{{$student->full_name.' :- '.$student->student->class_section->full_name}}</h4><br>
                            <div class="form-group">
                                <label for="payment-date">{{ __('date') }} <span class="text-danger">*</span></label>
                                <input id="payment-date" type="text" name="date" class="datepicker-popup paid-date form-control" placeholder="{{ __('date') }}" autocomplete="off" required>
                            </div>

                            <hr>
                            <div class="form-group col-sm-12 col-md-12">
                                <div class="optional-fees-content">
                                    <table class="table">
                                        <tbody>
                                        @foreach($optionalFeesData as $key =>$optionalFee)
                                            <tr>
                                                <td class="text-left">
                                                    @if(count($optionalFee->optional_fees_paid))
                                                        <span data-id="{{ $optionalFee->optional_fees_paid[0]['id']}}" class="text-danger remove-paid-optional-fees" style="cursor: pointer;"><i class="fa fa-times"></i></span>
                                                    @else
                                                        <input style="cursor: pointer;" type="checkbox" class="optional-fee-payment" id="optional-{{ $optionalFee->id }}" data-amount="{{ $optionalFee->amount }}" name="fees_class_type[{{ $key }}][id]" value="{{ $optionalFee->id }}">
                                                    @endif
                                                </td>
                                                <td colspan="2" class="text-left">
                                                    <label style="cursor: pointer;" for="optional-{{ $optionalFee->id }}">{{$optionalFee->fees_type_name}}</label>
                                                </td>
                                                <td style="cursor: default;" class="text-right">
                                                    {{$optionalFee->amount}}
                                                    {!! Form::hidden('fees_class_type['.$key.'][amount]', $optionalFee->amount) !!}
                                                </td>
                                            </tr>
                                        @endforeach

                                        <tr id="optional-total-amount-to-pay" style="display: none">
                                            <td class="text-left"></td>
                                            <td colspan="2" class="text-left"><label>{{__("Total Amount")}}</label></td>
                                            <td class="text-right" id="optional-total-amount"></td>
                                            {!! Form::hidden('total_amount',null, ["id" => "form-total-optional-amount"]) !!}
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <div class="row mode-container">
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('Mode') }} <span class="text-danger">*</span></label><br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="mode" class="cash-compulsory-mode  mode" value="1" checked>
                                                {{ __('cash') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="mode" class="cheque-compulsory-mode mode" value="2">
                                                {{ __('cheque') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group cheque-no-container" style="display: none">
                                <label for="cheque_no">{{ __('cheque_no') }} <span class="text-danger">*</span></label>
                                <input type="number" id="cheque_no" name="cheque_no" placeholder="{{ __('cheque_no') }}" class="form-control cheque-no" required/>
                            </div>
                            <input class="btn btn-theme float-right" type="submit" id="pay-button" disabled value={{ __('pay') }} />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        $('#payment-date').datepicker({
            format: "dd-mm-yyyy",
            rtl: isRTL()
        }).datepicker("setDate", 'now');

        let totalAmount = 0;
        $('.optional-fee-payment').on('click', function () {
            totalAmount += $(this).is(':checked') ? $(this).data("amount") : -$(this).data("amount");
            if (totalAmount > 0) {
                $('#pay-button').removeAttr('disabled')
                $('#optional-total-amount-to-pay').show().find('#optional-total-amount').html(totalAmount)
                $('#optional-total-amount-to-pay').show().find('#form-total-optional-amount').val(totalAmount)
            } else {
                $('#pay-button').attr('disabled', true)
                $('#optional-total-amount-to-pay').hide().find('#optional-total-amount').html(totalAmount)
                $('#optional-total-amount-to-pay').hide().find('#form-total-optional-amount').val(totalAmount)
            }
        })

        function formSuccessFunction() {
            setTimeout(function () {
                window.location.reload();
            }, 1000)
        }
    </script>
@endsection
