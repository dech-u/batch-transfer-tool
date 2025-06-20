<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif;
        }
        html, body {
            margin: 0px !important;
        }
        .full-width
        {
            width: 100%;
        }

        .header th{
            padding: 10px 0px;
            background-color: {{ $settings['staff_header_color'] ?? '#00edff' }};
            color: {{ $settings['staff_header_footer_text_color'] ?? 'black' }};
        }
        table {
            border-collapse: collapse;
            border: none;
            font-size: 14px;
            z-index: 1;
        }
        .staff-image {
            width: 30%;
            padding: 0px 10px;
            text-align: center;
            vertical-align: middle;
            height: 80px;
        }
        .staff-data {
            text-align: left;
            padding-left: 10px;
            padding: 2px 5px;
        }
        .card-title {
            padding: 6px 0px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .school-name {
            padding-right: 10px !important;
            text-align: right;
            font-size: 15px;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom-right-radius: 10px;
        }
        
        .footer {
            background-color: {{ $settings['staff_footer_color'] ?? '#56cc99' }};
            color: {{ $settings['staff_header_footer_text_color'] ?? 'black' }};
            position: fixed;
            width: 100%;
            padding: 2px 0px;
            font-size: 12px;
            bottom: 0px;
            height: 15px;
            text-align: right;
            letter-spacing: 1.5px;
            z-index: 1;
            padding-bottom: 5px;
        }
        .school-logo {
            border-bottom-left-radius: 10px;
        }
        .card-body {
            height: {{ $settings['staff_page_height'] }};
        }
        .vertical-staff-data {
            text-align: left;
            padding: 2px 2px 5px 10px;
        }
        .signature {
            background-size: contain;
            background-position: center center;
            background-repeat: no-repeat;
            padding: 10px;
            position: fixed;
            bottom: 35px;
            right: 10px;   
        }
        .vertical-school-name {
            padding: 10px 10px !important;
            text-align: center;
            font-size: 15px;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom-right-radius: 10px;
            border-bottom-left-radius: 10px;
        }
    </style>

    @if (isset($settings['staff_profile_image_style']) && $settings['staff_profile_image_style'] == 'squre')
        <style>
            .staff-profile {
                border: 3px solid black;
                border-radius: 6px;
                background-size: contain;
                background-position: center center;
                background-repeat: no-repeat;
                padding: 2px;
        }
        </style>
    @else
        <style>
            .staff-profile {
                border: 3px solid black;
                border-radius: 80px;
                background-size: contain;
                background-position: center center;
                background-repeat: no-repeat;
                padding: 2px;
        }
        </style>
    @endif

    @if (isset($settings['staff_layout_type']) && $settings['staff_layout_type'] == 'horizontal')
        <style>
            .background-image {
                position: fixed;
                width: auto;
                padding: 5px;
                height: auto;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                opacity: 0.2;
                z-index: -1;
            }
            .background_image {
                z-index: -1;
                object-fit: cover;
                background-size: contain;
                background-position: center center;
                background-repeat: no-repeat;
            }

        </style>
    @else
        <style>
            .background-image {
                position: fixed;
                width: auto;
                padding: 5px;
                height: auto;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                opacity: 0.2;
                z-index: -1;
            }
            .background_image {
                z-index: -1;
                object-fit: cover;
                background-size: contain;
                background-position: center center;
                background-repeat: no-repeat;
            }
        </style>
    @endif
</head>
<body>
    @foreach ($users as $key => $user)
    <div class="card-body">
        @if ($settings['staff_layout_type'] == 'horizontal')
            
            <table class="table full-width">
                <tr class="header">
                    <th class="school-logo">
                        @if ($settings['horizontal_logo'] ?? '')
                            <img height="40" src="{{ public_path('storage/').$settings['horizontal_logo'] }}" alt="">
                        @else
                            <img height="40" src="{{ public_path('assets/horizontal-logo2.svg') }}" alt="">
                        @endif
                    </th>
                    <th class="school-name" colspan="2">{{ $settings['school_name'] }}</th>
                </tr>
                <tr>
                    <th class="card-title" colspan="3">Staff Identification Card</th>
                </tr>
                <tr>
                    <td class="staff-image" rowspan="{{ count($settings['staff_id_card_fields']) }}">
                        @if ($user->getRawOriginal('image'))
                            <img class="staff-profile" height="120" width="120" align="center" src="{{ public_path('storage/').$user->getRawOriginal('image') }}" alt="">
                        @else
                            <img class="staff-profile" height="120" width="120" align="center" src="{{ public_path('assets/dummy_logo.jpg') }}" alt="">    
                        @endif
                        
                    </td>
                    @if (in_array('name',$settings['staff_id_card_fields']))
                        <th class="staff-data">Name :</th>
                        <td>{{ $user->full_name }}</td>
                    @endif
                    
                </tr>
                
                @if (in_array('role',$settings['staff_id_card_fields']))
                <tr>
                    <th class="staff-data">Role :</th>
                    <td>{{ implode(',',$user->roles->pluck('name')->toArray()) }}</td>
                </tr>
                @endif

                @if (in_array('contact',$settings['staff_id_card_fields']))
                <tr>
                    <th class="staff-data">Contact :</th>
                    <td>{{ $user->mobile }}</td>
                </tr>
                @endif

                @if (in_array('email',$settings['staff_id_card_fields']))
                <tr>
                    <th class="staff-data">Email :</th>
                    <td>{{ $user->email }}</td>
                </tr>
                @endif

                @if (in_array('qualification',$settings['staff_id_card_fields']))
                <tr>
                    <th class="staff-data">Qualification :</th>
                    <td>{{ $user->staff->qualification }}</td>
                </tr>
                @endif

                @if (in_array('dob',$settings['staff_id_card_fields']))
                <tr>
                    <th class="staff-data">DOB :</th>
                    <td>{{ date($settings['date_format'],strtotime($user->dob)) }}</td>
                </tr>
                @endif

                @if (in_array('gender',$settings['staff_id_card_fields']))
                <tr>
                    <th class="staff-data">Gender :</th>
                    <td style="text-transform: capitalize">{{ $user->gender }}</td>
                </tr>
                @endif

                @if (in_array('session_year',$settings['staff_id_card_fields']))
                <tr>
                    <th class="staff-data">Session Year :</th>
                    <td>{{ $sessionYear->name }}</td>
                </tr>
                @endif
                
                @foreach ($users[0]->extra_user_details as $data)
                    @if (in_array($data->form_field->type, ['text','number','radio','textarea']))
                        <tr>
                            <th class="staff-data">{{ $data->form_field->name }} :</th>
                            <td>{{ $data->data }}</td>
                        </tr>
                    @elseif($data->form_field->type == 'dropdown')
                        <tr>
                            <th class="staff-data">{{ $data->form_field->name }} :</th>
                            <td>{!! $data->form_field->default_values[$data->data] !!}</td>
                        </tr>
                    @elseif($data->form_field->type == 'checkbox')
                        <tr>
                            <th class="staff-data">{{ $data->form_field->name }} :</th>
                            <td>{!! implode(",",json_decode($data->data ?? '[]')) !!}</td>
                        </tr>
                    @endif
                @endforeach

                <tr>
                    <td></td>
                    <td colspan="">
                        @if ($settings['signature'] ?? '')
                            <img class="" height="40" class="signature" width="100" align="center" src="{{ public_path('storage/').$settings['signature'] }}" alt="">
                            <span style="position: fixed;bottom:25px;right:40px"><b>Signature</b></span>
                        @endif
                    </td>
                </tr>
            </table>
        @else
            {{-- Vertical --}}
            <table class="table full-width">
                <tr class="header">
                    <th class="vertical-school-name" colspan="2">{{ $settings['school_name'] }}</th>
                </tr>
                <tr>
                    <th colspan="2">
                        @if ($settings['horizontal_logo'] ?? '')
                            <img height="40" style="padding-top: 5px" src="{{ public_path('storage/').$settings['horizontal_logo'] }}" alt="">
                        @else
                            <img height="40" style="padding-top: 5px" src="{{ public_path('assets/horizontal-logo2.svg') }}" alt="">
                        @endif
                    </th>
                </tr>
                <tr>
                    <th class="card-title" colspan="2" style="font-size: 12px">Staff Identification Card</th>
                </tr>
                <tr>
                    <td class="staff-image" colspan="2">
                        @if ($user->getRawOriginal('image'))
                            <img class="staff-profile" height="120" width="120" align="center" src="{{ public_path('storage/').$user->getRawOriginal('image') }}" alt="">
                        @else
                            <img class="staff-profile" height="120" width="120" align="center" src="{{ public_path('assets/dummy_logo.jpg') }}" alt="">    
                        @endif
                    </td>
                </tr>

                @if (in_array('name',$settings['staff_id_card_fields']))
                <tr>
                    <th class="vertical-staff-data">Name :</th>
                    <td>{{ $user->full_name }}</td>
                </tr>
                @endif

                @if (in_array('role',$settings['staff_id_card_fields']))
                <tr>
                    <th class="vertical-staff-data">Role :</th>
                    <td>{{ implode(',',$user->roles->pluck('name')->toArray()) }}</td>
                </tr>
                @endif

                @if (in_array('contact',$settings['staff_id_card_fields']))
                <tr>
                    <th class="vertical-staff-data">Contact :</th>
                    <td>{{ $user->mobile }}</td>
                </tr>
                @endif

                @if (in_array('email',$settings['staff_id_card_fields']))
                <tr>
                    <th class="vertical-staff-data">Email :</th>
                    <td>{{ $user->email }}</td>
                </tr>
                @endif

                @if (in_array('qualification',$settings['staff_id_card_fields']))
                <tr>
                    <th class="vertical-staff-data">Qualification :</th>
                    <td>{{ $user->staff->qualification }}</td>
                </tr>
                @endif

                @if (in_array('dob',$settings['staff_id_card_fields']))
                <tr>
                    <th class="vertical-staff-data">DOB :</th>
                    <td>{{ date($settings['date_format'],strtotime($user->dob)) }}</td>
                </tr>
                @endif

                @if (in_array('gender',$settings['staff_id_card_fields']))
                <tr>
                    <th class="vertical-staff-data">Gender :</th>
                    <td style="text-transform: capitalize">{{ $user->gender }}</td>
                </tr>
                @endif

                @if (in_array('session_year',$settings['staff_id_card_fields']))
                <tr>
                    <th class="vertical-staff-data">Session Year :</th>
                    <td>{{ $sessionYear->name }}</td>
                </tr>
                @endif

                @foreach ($users[0]->extra_user_details as $data)
                    @if (in_array($data->form_field->type, ['text','number','radio','textarea']))
                        <tr>
                            <th class="vertical-staff-data">{{ $data->form_field->name }} :</th>
                            <td>{{ $data->data }}</td>
                        </tr>
                    @elseif($data->form_field->type == 'dropdown')
                        <tr>
                            <th class="vertical-staff-data">{{ $data->form_field->name }} :</th>
                            <td>{!! $data->form_field->default_values[$data->data] !!}</td>
                        </tr>
                    @elseif($data->form_field->type == 'checkbox')
                        <tr>
                            <th class="vertical-staff-data">{{ $data->form_field->name }} :</th>
                            <td>{!! implode(",",json_decode($data->data ?? '[]')) !!}</td>
                        </tr>
                    @endif
                @endforeach

                <tr>
                    <td></td>
                    <td>
                        @if ($settings['signature'] ?? '')
                            <img class="" height="40" class="signature" width="100" align="center" src="{{ public_path('storage/').$settings['signature'] }}" alt="">
                            <span style="position: fixed;bottom:25px;right:40px"><b>Signature</b></span>
                        @endif
                    </td>
                </tr>
            </table>
        @endif
        <div class="footer">
            <span class="footer-text" style="padding-right:10px;">Valid Until {{ $valid_until }}</span>
        </div>
        @if (isset($settings['staff_layout_type']) && $settings['staff_layout_type'] == 'horizontal')
            <div class="background-image">
                @if ($settings['staff_background_image'] ?? '')
                    <img src="{{ public_path('storage/').$settings['staff_background_image'] }}" class="background_image" height="140" width="360" alt="">
                    
                @endif
            </div>
        @else
            <div class="background-image">
                @if ($settings['staff_background_image'] ?? '')
                    <img src="{{ public_path('storage/').$settings['staff_background_image'] }}" class="background_image" height="140" width="280" alt="">
                    
                @endif
            </div>
        @endif
        
        
    </div>
    @endforeach
</body>
</html>