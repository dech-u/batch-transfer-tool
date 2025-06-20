// noinspection JSJQueryEfficiency

/**
 * Table Query Params
 */
function classQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_id').val(),
        medium_id: $('#filter_medium_id').val(),
        show_deleted: tableListType,
    };
}

function NotificationUserqueryParams(p) {
    
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        roles: $('#roles').val(),
        over_due_fees_roles: $('#over_due_fees_roles').val(),
        type: $('input[name="type"]:checked').val(),
    };
}

function feesQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        session_year_id: $('#filter_session_year_id').val(),
        medium_id: $('#filter_medium_id').val(),
        show_deleted: tableListType,
    };
}


function PayrollSettingsqueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        type: $('#filter_type').val(),
        show_deleted: tableListType,
    };
}

function leaveDetailQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        session_year_id: $('#filter_session_year_id').val(),
        staff_id: $('#filter_staff_id').val(),
        
    };
}

function schoolQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        package_id: $('#filter_package_id').val(),
        show_deleted: tableListType,
    };
}

function ExamClassQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_id: $('#filter_exam_name').val(),
        class_id: $('#filter_class_name').val()
    };
}

function timetableQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        medium_id: $('#filter_medium_id').val()
    };
}

function getExamResult(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_id: $('.result_exam').val(),
        session_year_id: $('#filter_session_year_id').val(),
        class_section_id: $('#filter_class_section_id').val(),
    };
}


function SubjectQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        medium_id: $('#filter_subject_id').val(),
        show_deleted: tableListType,
    };
}


function ExpenseQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        category_id: $('#filter_category_id').val(),
        session_year_id: $('#filter_session_year_id').val(),
        month: $('#filter_month').val(),
    };
}

function payrollQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        month: $('#month').val(),
        year: $('#year').val(),
    };
}

function payrollListQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        year: $('#filter_year').val(),
    };
}

function leaveQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        session_year_id: $('#session_year_id').val(),
        filter_upcoming: $('#filter_upcoming').val(),
        month_id: $('#filter_month_id').val(),
        user_id: $('#filter_user_id').val(),
    };
}

function AssignTeacherQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_id').val(),
    };
}


function webSettingsQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}

function StudentDetailQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_section_id').val(),

    };
}


function AssignmentSubmissionQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        subject_id: $('#filter-subject-id').val(),
        class_section_id: $('#filter-class-section-id').val(),

    };
}

function CreateAssignmentSubmissionQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        subject_id: $('#filter-subject-id').val(),
        class_id: $('#filter-class-section-id').val(),
        session_year_id: $("#filter_session_year_id").val()
    };
}

function CreateLessonQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_subject_id: $('#filter-subject-id').val(),
        class_id: $('#filter-class-section-id').val(),
        lesson_id: $('#filter_lesson_id').val(),
    };
}

function CreateTopicQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_subject_id: $('#filter-subject-id').val(),
        class_id: $('#filter-class-section-id').val(),
        lesson_id: $('#filter_lesson_id').val(),
    };
}

function uploadMarksqueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#class_section_id').val(),
        'class_subject_id': $('#subject_id').val(),
        'exam_id': $('#exam_id').val(),
    };
}

function feesPaidListQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        fees_id: $('#filter_fees_id').val(),
        class_id: $('#filter_fees_id').find('option:selected').data('class-section-id'),
        session_year_id: $('#filter_session_year_id').val(),
        mode: $('#filter_mode').val(),
        paid_status: $('#filter_paid_status').val(),
        month: $('.paid-month').val(),
        payment_gateway: $('.payment-gateway').val(),
        class_section_id: $('#filter-class-section-id').val(),
        online_offline_payment: $('#filter_online_offline_payment').val(),
    };
}

function feesPaymentTransactionQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        payment_status: $('#filter_payment_status').val(),
        session_year_id: $('#filter_session_year_id').val(),
        month: $('.paid-month').val(),
    };
}

function subscriptionTransactionQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        payment_status: $('#filter_payment_status').val(),
    };
}

function studentRollNumberQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#filter_roll_number_class_section_id').val(),
        'sort_by': $('#sort_by').val(),
        'order_by': $('#order_by').val(),
    };
}

function onlineExamQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        show_deleted: tableListType,
        'class_section_id': $('#filter-class-section-id').val(),
        'class_subject_id': $('#filter-subject-id').val(),
        'subject_id': $('#filter-class-subject-id').val(),
        'session_year_id': $('#filter_session_year_id').val(),
    };
}


function onlineExamQuestionsQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#filter-class-section-id').val(),
        'class_subject_id': $('#filter-subject-id').val(),
        'subject_id': $('#filter-class-subject-id').val(),
    };
}

function studentDetailsQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    // var options = $table.bootstrapTable('getOptions');
    // if (!options.pagination) {
    //     p.limit = options.totalRows
    //     // return p;
    // }
    // p.limit = -1;
    var options = $table.bootstrapTable('getOptions');
    if (options.pagination != undefined && !options.pagination) {
        // sample data only contains 20 items - so replace limit = options.totalRows;
        p.limit = options.totalRows;
        // .NET API fails if these params are unset
        // if they = undefined  they are not passed to server
        // for some reason all params must be present when submitted to a .NET Web API
        // even if defined as optional in .NET method - call fails if not present
      }

    var data = {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_section_id').val(),
        session_year_id: $('#filter_session_year_id').val(),
        exam_id: $('#exam_id').val(),
        show_deactive: tableListType,
    };

    return data;
}

function attendanceQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#timetable_class_section').val(),
        'date': $('#date').val(),
    }
}

function holidayQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'session_year_id': $('#filter_session_year_id').val(),
        'month': $('#filter_month').val(),
    }
}

function galleryQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'session_year_id': $('#filter_session_year_id').val(),
    }
}

function userStatusQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        role: $('.role').val(),
        class_section_id: $('.class_section_id').val(),
    }
}

function queryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    if (tableListType === 1) {
        $('.btn-update-rank').hide();
    } else {
        $('.btn-update-rank').show();
    }
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        show_deleted: tableListType,
    };
}


function packageQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    if (tableListType === 'Trashed') {
        $('.btn-update-rank').hide();
    } else {
        $('.btn-update-rank').show();
    }
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        type: $('#type').val(),
        show_deleted: tableListType,
    };
}

function promoteStudentQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#student_class_section').val(),
        'session_year_id': $('#session_year_id').val(),
    };
}

function examQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        session_year_id: $('#filter_session_year_id').val(),
        medium_id: $('#filter_medium_id').val(),
        show_deleted: tableListType,
    };
}

function subscriptionQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}

function subscriptionReportQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        status: $('#status').val()
    };
}

function examTimetableQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_id: $('#filter-exam-id').val()
    };
}

$("#filter_class_id,#filter_class_section_id,#filter_teacher_id,#filter_subject_id,#filter_medium_id,#filter_subject_id").on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})


$('#filter-question-class-section-id,#filter-subject-id,#filter-class-section-id').on('change', function () {
    $('#table_list_questions').bootstrapTable('refresh');
})


//Show All / Trashed list Event
$('.table-list-type').on('click', function (e) {
    e.preventDefault();
    //Highlight the current selected type
    $('.table-list-type').removeClass('active').parent("b").contents().unwrap();
    $(this).wrap("<b></b>").addClass('active');

    //Refresh the bootstrap table so that data can be loaded according to the selected type
    //Based on this selected value new query param will be added in Bootstrap Table Query Params
    $('#table_list').bootstrapTable('refresh');
})


// $('.student-list-type').on('click', function (e) {
//     e.preventDefault();
//     //Highlight the current selected type
//     $('.student-list-type').removeClass('active').parent("b").contents().unwrap();
//     $(this).wrap("<b></b>").addClass('active');

//     //Refresh the bootstrap table so that data can be loaded according to the selected type
//     //Based on this selected value new query param will be added in Bootstrap Table Query Params
//     $('#table_list').bootstrapTable('refresh');
// })

function transferStudentQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'current_class_section': $('#transfer_class_section').val(),
    };
}

function activeDeactiveQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        show_deactive: tableListType,
    };
}

function studentsQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_id': $('#filter_class_id').val(),
        'class_section_id': $('#filter_class_section_id').val()
    }
}


function schoolInquiryQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'status' : $('#filter_status_id').val(),
        'date' : $('#filter_date').val(),
    };
}

function guardianQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_id').val(),
        class_section_id: $('#filter_class_section_id').val()
    };
}

function FormFieldQueryParams(p) {
    let tableListType = $('.table-list-type.active').data('id');
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        filter_all_user_type: $('#filter_all_user_type').val(),
        show_deleted: tableListType
    };
}

function certificateTemplateQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}