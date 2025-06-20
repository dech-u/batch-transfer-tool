<?php
return [
    'RESPONSE_CODE' => [
        'LOGIN_SUCCESS'                  => 100,
        'INVALID_LOGIN'                  => 101,
        'VALIDATION_ERROR'               => 102,
        'EXCEPTION_ERROR'                => 103,
        'ASSIGNMENT_ALREADY_SUBMITTED'   => 104,
        'STUDENT_ALREADY_ATTEMPTED_EXAM' => 105,
        'INVALID_SUBJECT_ID'             => 106,
        'INVALID_CHILD_ID'               => 107,
        'RESET_PASSWORD_FAILED'          => 108,
        'INVALID_PASSWORD'               => 109,
        'INVALID_USER_DETAILS'           => 110,
        'NOT_UNIQUE_IN_CLASS'            => 113,
        'GRADES_NOT_FOUND'               => 114,
        'INACTIVE_CHILD'                 => 115,
        'SUCCESS'                        => 200,
        'EXAM_ALREADY_PUBLISHED'         => 400,
        'EXAM_NOT_COMPLETED'             => 401,
        'ENABLE_PAYMENT_GATEWAY'         => 404,
        'FEE_ALREADY_PAID'               => 405,
        'INACTIVATED_USER'               => 116,
    ],
    'CACHE'         => [
        'SYSTEM' => [
            'LANGUAGE' => 'languages',
            'SETTINGS' => 'systemSettings'
        ],
        'SCHOOL' => [
            'SETTINGS'     => 'schoolSettings',
            'SESSION_YEAR' => 'sessionYear',
            'SEMESTER'     => 'semester',
            'FEATURES'     => 'features'
        ]
    ]
];
