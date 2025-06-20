<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student Admission Form</title>

    <style>
        html {
            margin: 0px;
        }
        .text-left {
            text-align: left;
            padding-left: 5px;
        }
        .text-right {
            text-align: right;
        }
        .full-table-width
        {
            width: 100%;
        }
        .school-info th, .school-info td 
        {
            padding: 2px 5px;
        }
        .header {
            margin-top: 1rem;
        }
        .school-name {
            font-size: 24px;
        }
        table {
            border-collapse: collapse;
            border: none;
            font-size: 14px;
            z-index: 1;
        }
        .section-heading {
            margin: 0.5rem 0rem;
            letter-spacing: 1px;
            font-weight: 700;
            background-color: #E1E1E1;
            padding: 5px 0px;
        }
        .text-center {
            text-align: center;
        }
        .main-body {
            margin: 0rem 2rem;
        }
        .student-section {
            
        }
        .section {
            margin-top: 0.5rem;
        }
        
        .label {
            width: 5rem;
        }
        .section span {
            display: inline-block;
            width: auto;
        }
        .section .col-line {
            display: inline-block;
            
            
        }
        .line {
            border-bottom: 1px solid black;
            width: auto;
        }
        .box {
            border: 1px solid gray;
            height: 25px;
            width: 25px;
        }
        table
        {
            margin-bottom: 0.5rem;
        }
        .text-small {
            font-size: 12px;
        }
        .photo {
            margin-top: 0.5rem;
            width: 8rem;
            height: 10rem;
            border: 1px solid gray;
        }
        .note {
            padding: 5px;
            vertical-align: top;
            text-align: left;
        }
        .big-label {
            width: 7rem;
        }
        .tr-bottom {
            
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table class="full-table-width school-info">
                <tr>
                    <th class="text-left school-name">{{ $schoolSettings['school_name'] ?? 'eSchool' }}</th>
                    <th class="text-right" rowspan="3">
                        @if ($settings['horizontal_logo'] ?? '')
                            <img height="50" src="{{ public_path('storage/') . $settings['horizontal_logo'] }}" alt="">
                        @else
                            <img height="50" src="{{ public_path('assets/horizontal-logo2.svg') }}" alt="">
                        @endif
                    </th>
                </tr>
                <tr>
                    <td class="text-left">{{ $schoolSettings['school_address'] ?? '' }}</td>
                </tr>

                <tr>
                    <td class="text-left">{{ $schoolSettings['school_email'] ?? '' }} | {{ $schoolSettings['school_phone'] ?? '' }}</td>
                </tr>
            </table>
        </div>
        <div class="section-heading text-center">
            STUDENT REGISTRATION FORM
        </div>
        <div class="main-body">
            <table class="full-table-width">
                <tr>
                    <td class="note">
                        Before filing student registration form kindly make sure:
                        <ul class="text-small">
                            <li>
                                PLEASE FILL UP THE FORM IN CAPITAL LETTERS.
                            </li>
                            <li>
                                * FIELDS ARE MANDATORY.
                            </li>
                        </ul>
                    </td>

                    <td class="photo text-center">
                        Student photo
                    </td>
                    <td class="photo text-center">
                        Guardian photo
                    </td>
                </tr>
                
            </table>
            

            <div class="student-detail section-heading text-center">
                STUDENT DETAILS
            </div>

            <div class="student-section section">
                <table class="full-table-width">
                    <tr>
                        <td class="text-left label">
                            First Name *
                        </td>
                        <td class="line"> </td>

                        <td class="text-right label">
                            Last Name *
                        </td>
                        <td class="line"> </td>
                    </tr>
                </table>
                <table class="full-table-width">
                    <tr>
                        <td class="text-left label">
                            DOB *
                        </td>
                        <td class="box"></td>
                        <td class="box"></td>
                        <td class="box"></td>
                        <td class="box"></td>
                        <td class="box"></td>
                        <td class="box"></td>
                        <td class="box"></td>
                        <td class="box"></td>
                        <td class="text-right label">
                            Gender *
                        </td>
                        <td>
                            <td class="box"></td>
                            <td class="text-left">Male</td>
                            <td class="box"></td>
                            <td class="text-left">Female</td>
                        </td>
                    </tr>
                    <tr>
                        <td></td>

                        <td class="text-small text-center">D</td>
                        <td class="text-small text-center">D</td>
                        <td class="text-small text-center">M</td>
                        <td class="text-small text-center">M</td>
                        <td class="text-small text-center">Y</td>
                        <td class="text-small text-center">Y</td>
                        <td class="text-small text-center">Y</td>
                        <td class="text-small text-center">Y</td>

                        <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td>
                            Mobile No.
                        </td>
                        <td class="line" colspan="8">

                        </td>
                        <td colspan="6"></td>
                    </tr>
                </table>
                <table class="full-table-width">
                    <tr>
                        <td>Current Address *</td>
                        <td class="line"></td>
                    </tr>
                    <tr>
                        <td class="line" colspan="2" style="height: 1.5rem;"></td>
                    </tr>
                    
                    <tr>
                        <td style="padding-top: 10px;width: 8rem">Permanent Address *</td>
                        <td class="line"></td>
                    </tr>
                    <tr>
                        <td class="line" colspan="2" style="height: 1.5rem"></td>
                    </tr>
                </table>
            </div>

            <div class="student-detail section-heading text-center">
                GUARDIAN DETAILS
            </div>

            <table class="full-table-width">
                <tr>
                    <td class="text-left label">
                        First Name *
                    </td>
                    <td class="line"> </td>

                    <td class="text-right label">
                        Last Name *
                    </td>
                    <td class="line"> </td>
                </tr>
            </table>
            <table class="full-table-width">
                <tr>
                    <td>Gender *</td>
                    <td class="box"></td>
                    <td class="text-left">Male</td>
                    <td class="box"></td>
                    <td class="text-left">Female</td>

                    <td class="label">Mobile No. *</td>
                    <td class="line" style="width: 17rem"></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>