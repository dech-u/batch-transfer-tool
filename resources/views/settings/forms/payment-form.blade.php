{{-- Currency Settings --}}
<div class="border border-secondary rounded-lg mb-3">
    <h3 class="col-12 page-title mt-3 ">
        {{ __('Currency Settings') }}
    </h3>
    <div class="row my-4 mx-1">

        <div class="form-group col-sm-12 col-md-6">
            <label for="currency_code">{{ __('Currency') }} <span
                    class="text-danger">*</span></label>
            {{-- <select name="currency_code" id="currency_code" class="form-control select2-dropdown select2-hidden-accessible">
                <option value="USD">USD</option>
                <option value="AED">AED</option>
                <option value="AFN">AFN</option>
                <option value="ALL">ALL</option>
                <option value="AMD">AMD</option>
                <option value="ANG">ANG</option>
                <option value="AOA">AOA</option>
                <option value="ARS">ARS</option>
                <option value="AUD">AUD</option>
                <option value="AWG">AWG</option>
                <option value="AZN">AZN</option>
                <option value="BAM">BAM</option>
                <option value="BBD">BBD</option>
                <option value="BDT">BDT</option>
                <option value="BGN">BGN</option>
                <option value="BMD">BMD</option>
                <option value="BND">BND</option>
                <option value="BOB">BOB</option>
                <option value="BRL">BRL</option>
                <option value="BSD">BSD</option>
                <option value="BWP">BWP</option>
                <option value="BYN">BYN</option>
                <option value="BZD">BZD</option>
                <option value="CAD">CAD</option>
                <option value="CDF">CDF</option>
                <option value="CHF">CHF</option>
                <option value="CNY">CNY</option>
                <option value="COP">COP</option>
                <option value="CRC">CRC</option>
                <option value="CVE">CVE</option>
                <option value="CZK">CZK</option>
                <option value="DKK">DKK</option>
                <option value="DOP">DOP</option>
                <option value="DZD">DZD</option>
                <option value="EGP">EGP</option>
                <option value="ETB">ETB</option>
                <option value="EUR">EUR</option>
                <option value="FJD">FJD</option>
                <option value="FKP">FKP</option>
                <option value="GBP">GBP</option>
                <option value="GEL">GEL</option>
                <option value="GIP">GIP</option>
                <option value="GMD">GMD</option>
                <option value="GTQ">GTQ</option>
                <option value="GYD">GYD</option>
                <option value="HKD">HKD</option>
                <option value="HNL">HNL</option>
                <option value="HTG">HTG</option>
                <option value="HUF">HUF</option>
                <option value="IDR">IDR</option>
                <option value="ILS">ILS</option>
                <option value="INR">INR</option>
                <option value="ISK">ISK</option>
                <option value="JMD">JMD</option>
                <option value="KES">KES</option>
                <option value="KGS">KGS</option>
                <option value="KHR">KHR</option>
                <option value="KYD">KYD</option>
                <option value="KZT">KZT</option>
                <option value="LAK">LAK</option>
                <option value="LBP">LBP</option>
                <option value="LKR">LKR</option>
                <option value="LRD">LRD</option>
                <option value="LSL">LSL</option>
                <option value="MAD">MAD</option>
                <option value="MDL">MDL</option>
                <option value="MKD">MKD</option>
                <option value="MMK">MMK</option>
                <option value="MNT">MNT</option>
                <option value="MOP">MOP</option>
                <option value="MUR">MUR</option>
                <option value="MVR">MVR</option>
                <option value="MWK">MWK</option>
                <option value="MXN">MXN</option>
                <option value="MYR">MYR</option>
                <option value="MZN">MZN</option>
                <option value="NAD">NAD</option>
                <option value="NGN">NGN</option>
                <option value="NIO">NIO</option>
                <option value="NOK">NOK</option>
                <option value="NPR">NPR</option>
                <option value="NZD">NZD</option>
                <option value="PAB">PAB</option>
                <option value="PEN">PEN</option>
                <option value="PGK">PGK</option>
                <option value="PHP">PHP</option>
                <option value="PKR">PKR</option>
                <option value="PLN">PLN</option>
                <option value="QAR">QAR</option>
                <option value="RON">RON</option>
                <option value="RSD">RSD</option>
                <option value="RUB">RUB</option>
                <option value="SAR">SAR</option>
                <option value="SBD">SBD</option>
                <option value="SCR">SCR</option>
                <option value="SEK">SEK</option>
                <option value="SGD">SGD</option>
                <option value="SHP">SHP</option>
                <option value="SLE">SLE</option>
                <option value="SOS">SOS</option>
                <option value="SRD">SRD</option>
                <option value="STD">STD</option>
                <option value="SZL">SZL</option>
                <option value="THB">THB</option>
                <option value="TJS">TJS</option>
                <option value="TOP">TOP</option>
                <option value="TRY">TRY</option>
                <option value="TTD">TTD</option>
                <option value="TWD">TWD</option>
                <option value="TZS">TZS</option>
                <option value="UAH">UAH</option>
                <option value="UYU">UYU</option>
                <option value="UZS">UZS</option>
                <option value="WST">WST</option>
                <option value="XCD">XCD</option>
                <option value="YER">YER</option>
                <option value="ZAR">ZAR</option>
                <option value="ZMW">ZMW</option>
            </select> --}}

            {!! Form::select( 'currency_code', [ 'USD' => 'USD', 'AED' => 'AED', 'AFN' => 'AFN', 'ALL' => 'ALL', 'AMD' => 'AMD', 'ANG' => 'ANG', 'AOA' => 'AOA', 'ARS' => 'ARS', 'AUD' => 'AUD', 'AWG' => 'AWG', 'AZN' => 'AZN', 'BAM' => 'BAM', 'BBD' => 'BBD', 'BDT' => 'BDT', 'BGN' => 'BGN', 'BMD' => 'BMD', 'BND' => 'BND', 'BOB' => 'BOB', 'BRL' => 'BRL', 'BSD' => 'BSD', 'BWP' => 'BWP', 'BYN' => 'BYN', 'BZD' => 'BZD', 'CAD' => 'CAD', 'CDF' => 'CDF', 'CHF' => 'CHF', 'CNY' => 'CNY', 'COP' => 'COP', 'CRC' => 'CRC', 'CVE' => 'CVE', 'CZK' => 'CZK', 'DKK' => 'DKK', 'DOP' => 'DOP', 'DZD' => 'DZD', 'EGP' => 'EGP', 'ETB' => 'ETB', 'EUR' => 'EUR', 'FJD' => 'FJD', 'FKP' => 'FKP', 'GBP' => 'GBP', 'GEL' => 'GEL', 'GIP' => 'GIP', 'GMD' => 'GMD', 'GTQ' => 'GTQ', 'GYD' => 'GYD', 'HKD' => 'HKD', 'HNL' => 'HNL', 'HTG' => 'HTG', 'HUF' => 'HUF', 'IDR' => 'IDR', 'ILS' => 'ILS', 'INR' => 'INR', 'ISK' => 'ISK', 'JMD' => 'JMD', 'KES' => 'KES', 'KGS' => 'KGS', 'KHR' => 'KHR', 'KYD' => 'KYD', 'KZT' => 'KZT', 'LAK' => 'LAK', 'LBP' => 'LBP', 'LKR' => 'LKR', 'LRD' => 'LRD', 'LSL' => 'LSL', 'MAD' => 'MAD', 'MDL' => 'MDL', 'MKD' => 'MKD', 'MMK' => 'MMK', 'MNT' => 'MNT', 'MOP' => 'MOP', 'MUR' => 'MUR', 'MVR' => 'MVR', 'MWK' => 'MWK', 'MXN' => 'MXN', 'MYR' => 'MYR', 'MZN' => 'MZN', 'NAD' => 'NAD', 'NGN' => 'NGN', 'NIO' => 'NIO', 'NOK' => 'NOK', 'NPR' => 'NPR', 'NZD' => 'NZD', 'PAB' => 'PAB', 'PEN' => 'PEN', 'PGK' => 'PGK', 'PHP' => 'PHP', 'PKR' => 'PKR', 'PLN' => 'PLN', 'QAR' => 'QAR', 'RON' => 'RON', 'RSD' => 'RSD', 'RUB' => 'RUB', 'SAR' => 'SAR', 'SBD' => 'SBD', 'SCR' => 'SCR', 'SEK' => 'SEK', 'SGD' => 'SGD', 'SHP' => 'SHP', 'SLE' => 'SLE', 'SOS' => 'SOS', 'SRD' => 'SRD', 'STD' => 'STD', 'SZL' => 'SZL', 'THB' => 'THB', 'TJS' => 'TJS', 'TOP' => 'TOP', 'TRY' => 'TRY', 'TTD' => 'TTD', 'TWD' => 'TWD', 'TZS' => 'TZS', 'UAH' => 'UAH', 'UYU' => 'UYU', 'UZS' => 'UZS', 'WST' => 'WST', 'XCD' => 'XCD', 'YER' => 'YER', 'ZAR' => 'ZAR', 'ZMW' => 'ZMW', ], $settings['currency_code'] ?? null, ['id' => 'currency_code', 'class' => 'form-control select2-dropdown'], ) !!}


        </div>
        <div class="form-group col-md-3 col-sm-12">
            <label for="currency_symbol">{{ __('currency_symbol') }} <span
                    class="text-danger">*</span></label>
            <input name="currency_symbol" id="currency_symbol"
                value="{{ $settings['currency_symbol'] ?? '' }}" type="text"
                placeholder="{{ __('currency_symbol') }}" class="form-control" required />
        </div>
    </div>
</div>
{{-- End Currency Settings --}}

{{-- Stripe --}}
<div class="border border-secondary rounded-lg mb-3">


    <h3 class="col-12 page-title mt-3 ">
        {{ __('Stripe') }}
    </h3>
    <div class="row my-4 mx-1">
        <div class="form-group col-sm-12 col-md-6">
            <label for="stripe_status">{{ __('status') }} <span
                    class="text-danger">*</span></label>
            <select name="gateway[Stripe][status]" id="stripe_status" class="form-control">
                <option value="1"
                    {{ isset($paymentGateway['Stripe']['status']) && $paymentGateway['Stripe']['status'] == 1 ? 'selected' : '' }}>
                    {{ __('Enable') }}</option>
                <option value="0"
                    {{ isset($paymentGateway['Stripe']['status']) && $paymentGateway['Stripe']['status'] == 0 ? 'selected' : '' }}>
                    {{ __('Disable') }}</option>
            </select>
        </div>
        <input type="hidden" name="gateway[Stripe][currency_code]" id="stripe_currency"
            value="{{ $paymentGateway['Stripe']['currency_code'] ?? '' }}">

        <div class="form-group col-sm-12 col-md-6">
            <label for="stripe_publishable_key">{{ __('Stripe Publishable Key') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Stripe][api_key]" id="stripe_publishable_key"
                class="form-control" placeholder="Stripe Publishable Key"
                value="{{ $paymentGateway['Stripe']['api_key'] ?? '' }}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="stripe_secret_key">{{ __('Stripe Secret Key') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Stripe][secret_key]" id="stripe_secret_key"
                class="form-control" placeholder="Stripe Secret Key"
                value="{{ $paymentGateway['Stripe']['secret_key'] ?? '' }}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="stripe_webhook_secret">{{ __('Stripe Webhook Secret') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Stripe][webhook_secret_key]"
                id="stripe_webhook_secret" class="form-control"
                placeholder="Stripe Webhook Secret"
                value="{{ $paymentGateway['Stripe']['webhook_secret_key'] ?? '' }}">
        </div>

        @if (Auth::user()->school_id)
            <div class="form-group col-sm-12 col-md-6">
                <label for="stripe_webhook_url">{{ __('Stripe Webhook URL') }}</label>
                <input type="text" name="gateway[Stripe][webhook_url]"
                    id="stripe_webhook_url" class="form-control"
                    placeholder="Stripe Webhook URL" disabled
                    value="{{ url('webhook/stripe') }}">
            </div>
        @else
            <div class="form-group col-sm-12 col-md-6">
                <label for="stripe_webhook_url">{{ __('Stripe Webhook URL') }}</label>
                <input type="text" name="gateway[Stripe][webhook_url]"
                    id="stripe_webhook_url" class="form-control"
                    placeholder="Stripe Webhook URL" disabled
                    value="{{ url('subscription/webhook/stripe') }}">
            </div>
        @endif
    </div>

</div>

{{-- Razorpay --}}
<div class="border border-secondary rounded-lg mb-3">


    <h3 class="col-12 page-title mt-3 ">
        {{ __('Razorpay') }}
    </h3>
    <div class="row my-4 mx-1">
        <div class="form-group col-sm-12 col-md-6">
            <label for="razorpay_status">{{ __('status') }} <span
                    class="text-danger">*</span></label>
            <select name="gateway[Razorpay][status]" id="razorpay_status" class="form-control">
                <option value="0"
                    {{ isset($paymentGateway['Razorpay']['status']) && $paymentGateway['Razorpay']['status'] == 0 ? 'selected' : '' }}>
                    {{ __('Disable') }}</option>
                <option value="1"
                    {{ isset($paymentGateway['Razorpay']['status']) && $paymentGateway['Razorpay']['status'] == 1 ? 'selected' : '' }}>
                    {{ __('Enable') }}</option>
            </select>
        </div>
        <input type="hidden" name="gateway[Razorpay][currency_code]" id="razorpay_currency"
            value="{{ $paymentGateway['Razorpay']['currency_code'] ?? '' }}">

        <div class="form-group col-sm-12 col-md-6">
            <label for="razorpay_api_key">{{ __('Razorpay Api Key') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Razorpay][api_key]" id="razorpay_api_key"
                class="form-control" placeholder="Razorpay Api Key"
                value="{{ $paymentGateway['Razorpay']['api_key'] ?? '' }}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="razorpay_secret_key">{{ __('Razorpay Secret Key') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Razorpay][secret_key]"
                id="razorpay_secret_key" class="form-control"
                placeholder="Razorpay Secret Key"
                value="{{ $paymentGateway['Razorpay']['secret_key'] ?? '' }}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="razorpay_webhook_secret">{{ __('Razorpay Webhook Secret') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Razorpay][webhook_secret_key]"
                id="razorpay_webhook_secret" class="form-control"
                placeholder="Razorpay Webhook Secret"
                value="{{ $paymentGateway['Razorpay']['webhook_secret_key'] ?? '' }}">
        </div>

        @if (Auth::user()->school_id)
            <div class="form-group col-sm-12 col-md-6">
                <label for="razorpay_webhook_url">{{ __('Razorpay Webhook URL') }}</label>
                <input type="text" name="gateway[Razorpay][webhook_url]"
                    id="razorpay_webhook_url" class="form-control"
                    placeholder="Razorpay Webhook URL" readonly
                    value="{{ url('webhook/razorpay') }}">
            </div>
        @else
            <div class="form-group col-sm-12 col-md-6">
                <label for="razorpay_webhook_url">{{ __('Razorpay Webhook URL') }}</label>
                <input type="text" name="gateway[Razorpay][webhook_url]"
                    id="razorpay_webhook_url" class="form-control"
                    placeholder="Razorpay Webhook URL" readonly
                    value="{{ url('subscription/webhook/razorpay') }}">
            </div>
        @endif
    </div>

</div>

{{-- Paystack --}}
<div class="border border-secondary rounded-lg mb-3">
    <h3 class="col-12 page-title mt-3 ">
        {{ __('Paystack') }}
    </h3>
    <div class="row my-4 mx-1">
        <div class="form-group col-sm-12 col-md-6">
            <label for="paystack_status">{{ __('status') }} <span
                    class="text-danger">*</span></label>
            <select name="gateway[Paystack][status]" id="paystack_status" class="form-control">
                <option value="0"
                    {{ isset($paymentGateway['Paystack']['status']) && $paymentGateway['Paystack']['status'] == 0 ? 'selected' : '' }}>
                    {{ __('Disable') }}</option>
                <option value="1"
                    {{ isset($paymentGateway['Paystack']['status']) && $paymentGateway['Paystack']['status'] == 1 ? 'selected' : '' }}>
                    {{ __('Enable') }}</option>
            </select>
        </div>
        <input type="hidden" name="gateway[Paystack][currency_code]" id="paystack_currency"
            value="{{ $paymentGateway['Paystack']['currency_code'] ?? '' }}">

        <div class="form-group col-sm-12 col-md-6">
            <label for="paystack_api_key">{{ __('Paystack Publishable Key') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Paystack][api_key]" id="paystack_api_key"
                class="form-control" placeholder="Paystack Publishable Key"
                value="{{ $paymentGateway['Paystack']['api_key'] ?? '' }}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="paystack_secret_key">{{ __('Paystack Secret Key') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Paystack][secret_key]" id="paystack_secret_key"
                class="form-control" placeholder="Paystack Secret Key"
                value="{{ $paymentGateway['Paystack']['secret_key'] ?? '' }}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="paystack_webhook_secret">{{ __('Paystack Webhook Secret') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Paystack][webhook_secret_key]" id="paystack_webhook_secret"
                class="form-control" placeholder="Paystack Webhook Secret"
                value="{{ $paymentGateway['Paystack']['webhook_secret_key'] ?? '' }}">
        </div>

        @if (Auth::user()->school_id)
            <div class="form-group col-sm-12 col-md-6">
                <label for="paystack_webhook_url">{{ __('Paystack Webhook URL') }}</label>
                <input type="text" name="gateway[Paystack][webhook_url]"
                    id="paystack_webhook_url" class="form-control"
                    placeholder="Paystack Webhook URL" disabled
                    value="{{ url('webhook/paystack') }}">
            </div>
        @else
            <div class="form-group col-sm-12 col-md-6">
                <label for="paystack_webhook_url">{{ __('Paystack Webhook URL') }}</label>
                <input type="text" name="gateway[Paystack][webhook_url]"
                    id="paystack_webhook_url" class="form-control"
                    placeholder="Paystack Webhook URL" disabled
                    value="{{ url('subscription/webhook/paystack') }}">
            </div>
        @endif
    </div>
</div>

{{-- Flutterwave --}}
<div class="border border-secondary rounded-lg mb-3">


    <h3 class="col-12 page-title mt-3 ">
        {{ __('Flutterwave') }}
    </h3>
    <div class="row my-4 mx-1">
        <div class="form-group col-sm-12 col-md-6">
            <label for="flutterwave_status">{{ __('status') }} <span
                    class="text-danger">*</span></label>
            <select name="gateway[Flutterwave][status]" id="flutterwave_status" class="form-control">
                <option value="0"
                    {{ isset($paymentGateway['Flutterwave']['status']) && $paymentGateway['Flutterwave']['status'] == 0 ? 'selected' : '' }}>
                    {{ __('Disable') }}</option>
                <option value="1"
                    {{ isset($paymentGateway['Flutterwave']['status']) && $paymentGateway['Flutterwave']['status'] == 1 ? 'selected' : '' }}>
                    {{ __('Enable') }}</option>
            </select>
        </div>
        <input type="hidden" name="gateway[Flutterwave][currency_code]" id="flutterwave_currency"
            value="{{ $paymentGateway['Flutterwave']['currency_code'] ?? '' }}">

        <div class="form-group col-sm-12 col-md-6">
            <label for="flutterwave_publishable_key">{{ __('Flutterwave Publishable Key') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Flutterwave][api_key]" id="flutterwave_publishable_key"
                class="form-control" placeholder="Flutterwave Publishable Key"
                value="{{ $paymentGateway['Flutterwave']['api_key'] ?? '' }}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="flutterwave_secret_key">{{ __('Flutterwave Secret Key') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Flutterwave][secret_key]"
                id="flutterwave_secret_key" class="form-control"
                placeholder="Flutterwave Secret Key"
                value="{{ $paymentGateway['Flutterwave']['secret_key'] ?? '' }}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="flutterwave_webhook_secret">{{ __('Flutterwave Webhook Secret') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="gateway[Flutterwave][webhook_secret_key]"
                id="flutterwave_webhook_secret" class="form-control"
                placeholder="Flutterwave Webhook Secret"
                value="{{ $paymentGateway['Flutterwave']['webhook_secret_key'] ?? '' }}">
        </div>

        @if (Auth::user()->school_id)
            <div class="form-group col-sm-12 col-md-6">
                <label for="flutterwave_webhook_url">{{ __('Flutterwave Webhook URL') }}</label>
                <input type="text" name="gateway[Flutterwave][webhook_url]"
                    id="flutterwave_webhook_url" class="form-control"
                    placeholder="Flutterwave Webhook URL" readonly
                    value="{{ url('webhook/flutterwave') }}">
            </div>
        @else
            <div class="form-group col-sm-12 col-md-6">
                <label for="flutterwave_webhook_url">{{ __('Flutterwave Webhook URL') }}</label>
                <input type="text" name="gateway[Flutterwave][webhook_url]"
                    id="flutterwave_webhook_url" class="form-control"
                    placeholder="Flutterwave Webhook URL" readonly
                    value="{{ url('subscription/webhook/flutterwave') }}">
            </div>
        @endif
    </div>

</div>

{{-- Bank transfer --}}
{{-- <div class="border border-secondary rounded-lg mb-3">


    <h3 class="col-12 page-title mt-3 ">
        {{ __('bank_transfer') }}
    </h3>
    <div class="row my-4 mx-1">
        <div class="form-group col-sm-12 col-md-6">
            <label for="bank_transfer_status">{{__("status")}} <span class="text-danger">*</span></label>
            <select name="gateway[bank_transfer][status]" id="bank_transfer_status" class="form-control">
                <option value="0" {{(isset($paymentGateway["bank_transfer"]["status"]) && $paymentGateway["bank_transfer"]["status"]==0) ? 'selected' : ''}}>{{__("Disable")}}</option>
                <option value="1" {{(isset($paymentGateway["bank_transfer"]["status"]) && $paymentGateway["bank_transfer"]["status"]==1) ? 'selected' : ''}}>{{__("Enable")}}</option>         
            </select>
        </div>
        <input type="hidden" name="gateway[bank_transfer][currency_code]" id="bank_transfer_currency" value="{{$paymentGateway["bank_transfer"]['currency_code'] ?? ''}}">

        <div class="form-group col-sm-12 col-md-6">
            <label for="bank_name">{{__("bank_name")}} <span class="text-danger">*</span></label>
            <input type="text" name="gateway[bank_transfer][bank_name]" id="bank_name" class="form-control" placeholder="{{ __('bank_name') }}" required value="{{$paymentGateway["bank_transfer"]['bank_name'] ?? ''}}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="account_name">{{__("account_name")}} <span class="text-danger">*</span></label>
            <input type="text" name="gateway[bank_transfer][account_name]" id="razorpay_secret_key" class="form-control" placeholder="{{ __('account_name') }}" required value="{{$paymentGateway["bank_transfer"]['account_name'] ??''}}">
        </div>

        <div class="form-group col-sm-12 col-md-6">
            <label for="account_no">{{__("account_no")}} <span class="text-danger">*</span></label>
            <input type="text" name="gateway[bank_transfer][account_no]" id="account_no" class="form-control" placeholder="{{ __('account_no') }}" required value="{{$paymentGateway["bank_transfer"]['account_no'] ?? ''}}">
        </div>

       
    </div>
    
</div> --}}