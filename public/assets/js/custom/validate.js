"use strict";
/********** Custom Validations ****************/
$.validator.addMethod("noDuplicateValues", function (value, element, options) {
    if (typeof options.class != "undefined" && options.class != null) {
        let components = (typeof options.group != "undefined" && options.group != "") ? $("." + options.class + "[data-group='" + options.group + "']") : $("." + options.class);
        // let components;
        // if (typeof options.group != "undefined" && options.group != "") {
        //     components = $("." + options.class + "[data-group='" + options.group + "']");
        // } else {
        //     components = $("." + options.class);
        // }
        let arrayValues = [];

        components.each(function (index, value) {
            if ($(value).val() !== "" && $(value).attr('name') !== $(element).attr('name')) {
                arrayValues.push($(value).val());
            }
            // if ($(element).attr('name') == "core_subject[0][id]") {
            
            // }
        })

        // if ($(element).attr('name') == "core_subject[0][id]") {
        
        // }
        return !arrayValues.includes(value);
    }
    return false;
}, "Duplicate values are not allowed");

// $.validator.addMethod("warningDuplicateValues", function (value, element, options) {

//     // Current Date to UTC Format
//     let currentDate  = moment.utc($(element).parent().parent().find('.'+options.dateClass).val(),'DD-MM-YYYY').valueOf();

//     // Get Current Time
//     let currentStartTime  = $(element).parent().parent().find('.'+options.startTimeClass).val();
//     let currentEndTime  = $(element).parent().parent().find('.'+options.endTimeClass).val();

//     // Date Element Presents in DOM
//     let dateElement = $("." + options.parentClass).find("." + options.dateClass)

//     // Loop on Date Element
//     $.each(dateElement, function (index, loopElement) {

//         // If Current Element Doesn't match the loop Element
//         if($(element).attr('name') != $(loopElement).attr('name')){
//             // Get Loop Element Date
//             let loopElementDate = moment.utc($(loopElement).val(),'DD-MM-YYYY').valueOf();

//             // If Current Date is equals to Loop Element Date
//             if(currentDate == loopElementDate){
//                 let loopElementStartTime  = $(loopElement).parent().parent().find('.'+options.startTimeClass).val();
//                 let loopElementEndTime  = $(loopElement).parent().parent().find('.'+options.endTimeClass).val();

//                 if(loopElementStartTime && loopElementEndTime){

//                     /** Conditions Explanation :-
//                      *  1. (loopElementStartTime >= currentStartTime && loopElementStartTime < currentEndTime)  - Last Start Time Should be greater or equal to Current Start Time && Last Start Time Should be Lesser Than Current End time.
//                      *
//                      *  2. (loopElementEndTime > currentStartTime && loopElementEndTime <= currentEndTime)      - Last End Time Should be Greater than Current Start Time && Last End Time Should be Lesser than or equal to Current End Time
//                      *
//                      *  3. (currentStartTime >= loopElementStartTime && currentStartTime < loopElementEndTime)  - Current Start Time Should be Greater Than or equal to Last Start Time && Current Start Time Should be Lesser Than Last End Time
//                      *
//                      *  4. (currentEndTime > loopElementStartTime && currentEndTime <= loopElementEndTime)     - Current End Time should be Greater Than Last Start Time && Current End Time Should be Lesser Than or equal to Last End Time
//                      */
//                     if ((loopElementStartTime >= currentStartTime && loopElementStartTime < currentEndTime) ||
//                         (loopElementEndTime > currentStartTime && loopElementEndTime <= currentEndTime)     ||
//                         (currentStartTime >= loopElementStartTime && currentStartTime < loopElementEndTime) ||
//                         (currentEndTime > loopElementStartTime && currentEndTime <= loopElementEndTime))    {

//                     }
//                 }

//             }
//         }else{
//             return false;
//         }

//     });
//     return true;
// }, "Duplicate values are not allowed");

//End Time Custom Validation
$.validator.addMethod("timeGreaterThan", function (value, element, params) {
    let startTime = $(params).val();
    let endTime = $(element).val();
    return endTime > startTime;
}, "End time should be greater than Start time.");

/************ Custom Function **********/

function errorPlacement(label, element) {
    label.addClass('mt-2 text-danger');
    if (label.text()) {
        closeLoading();
        if (element.is(":radio") || element.is(":checkbox")) {
            label.insertAfter(element.parent().parent().parent());
        } else if (element.is(":file")) {
            label.insertAfter(element.siblings('div:first'));
        } else if (element.hasClass('color-picker')) {
            label.insertAfter(element.parent());
        } else if(element.hasClass('select2-dropdown')) {
            label.insertAfter(element.next());
        } else if(element.hasClass('school_code_prefix')) {
            label.insertAfter(element.next().next());
        } else {
            label.insertAfter(element);
        }
    }
}

function highlight(element) {
    closeLoading();
    if ($(element).hasClass('color-picker')) {
        $(element).parent().parent().addClass('has-danger')
    } else {
        $(element).parent().addClass('has-danger')
    }

    $(element).addClass('form-control-danger')
}

function success(element) {
    if ($(element).attr("name") == "bg_color") {
        $(element).parent().parent().removeClass('has-danger')
    } else {
        $(element).parent().removeClass('has-danger')
        $(element).removeClass('form-control-danger')
    }
}

/************ Specific Validation Forms *************/
$(".medium-create-form").validate({
    rules: {
        'name': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".medium-edit-form").validate({
    rules: {
        'username': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".section-create-form").validate({
    rules: {
        'end_time': {
            'required': true,
            'timeGreaterThan': $('#start_time')
        },
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".section-edit-form").validate({
    rules: {
        'username': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".class-create-form").validate({
    rules: {
        'name': "required",
        'medium_id': "required",
        'section_id[]': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".class-edit-form").validate({
    rules: {
        'name': "required",
        'medium_id': "required",
        'section_id[]': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".subject-create-form").validate({
    rules: {
        'medium_id': "required",
        'name': "required",
        'bg_color': "required",
        image: {
            required: true,
            extension: "png|jpg|jpeg|svg"
        },
        'type': "required",
    },

    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});


$(".edit-class-subject-validate-form").validate({
    rules: {
        'class_id': "required",
        'core_subject_id[0]': {
            "required": true,
        },
        'total_selectable_subjects[]': "required",
    },

    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$("#formdata").validate({
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    },
});
$("#editdata").validate({
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    },
});

$(".student-registration-form").validate({
    rules: {
        'first_name': "required",
        'last_name': "required",
        'mobile': "number",
        'dob': "required",
        'class_section_id': "required",
        'admission_no': "required",
        'admission_date': "required",
        'guardian_email': {
            "required": true,
            "email": true,
        },
        'guardian_first_name': "required",
        'guardian_last_name': "required",
        'guardian_mobile': {
            "number": true,
            "required": true,
        },

    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-student-registration-form").validate({
    rules: {
        'first_name': "required",
        'last_name': "required",
        'dob': "required",
        'class_section_id': "required",
        'admission_no': "required",
        'roll_number': "required",
        'admission_date': "required",
        'guardian_email': "required",
        'guardian_first_name': "required",
        'guardian_last_name': "required",
        'guardian_mobile': {
            "number": true,
            "required": true,
        },
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".add-lesson-form").validate({
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'description': "required",
        'file[0][name]': "required",
        'file[0][thumbnail]': "required",
        'file[0][file]': "required",
        'file[0][link]': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

//Added this Event here because this form has dynamic input fields.
// $('.add-lesson-form').on('submit', function () {
//     var file = $('[name^="file"]');
//     file.filter('input').each(function (key, data) {
//         $(this).rules("add", {
//             required: true,
//         });
//     });
//     file.filter('input[name$="[name]"]').each(function (key, data) {
//         $(this).rules("add", {
//             required: true,
//         });
//     });
// })

$(".edit-lesson-form").validate({
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'description': "required",
        'edit_file[0][name]': "required",
        'edit_file[0][link]': "required",
        'file[0][name]': "required",
        'file[0][thumbnail]': "required",
        'file[0][file]': "required",
        'file[0][link]': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".add-topic-form").validate({
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'lesson_id': "required",
        'name': "required",
        'description': "required",
        'file[0][name]': "required",
        'file[0][thumbnail]': "required",
        'file[0][file]': "required",
        'file[0][link]': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-topic-form").validate({
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'description': "required",
        'edit_file[0][name]': "required",
        'edit_file[0][link]': "required",
        'file[0][name]': "required",
        'file[0][thumbnail]': "required",
        'file[0][file]': "required",
        'file[0][link]': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".add-exam-form").validate({
    rules: {
        'class_id': "required",
        'name': "required",
        'timetable[0][subject_id]': "required",
        'timetable[0][total_marks]': "required",
        'timetable[0][passing_marks]': "required",
        'timetable[0][start_time]': "required",
        'timetable[0][end_time]': "required",
        'timetable[0][date]': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".add-assignment-form").validate({
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'due_date': "required",
        'extra_days_for_resubmission': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-assignment-form").validate({
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'due_date': "required",
        'extra_days_for_resubmission': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});


$(".subject-edit-form").validate({
    rules: {
        'medium_id': "required",
        'name': "required",
        'bg_color': "required",
        image: {
            extension: "png|jpg|jpeg|svg",
        },
        'type': "required",
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".timetable-settings-form").validate({
    rules: {
        'timetable_start_time': 'required',
        'timetable_end_time': {
            'required': true,
            'timeGreaterThan': $('#starting_time')
        },
        'timetable_duration': {
            'required': true,
            'number': true
        }
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".school-registration-validate").validate({
    rules: {
        'school_image': {
            extension: "jpg|jpeg|png|svg"
        },
        admin_image: {
            extension: "jpg|jpeg|png|svg"
        }
    },
    messages: {
        school_image: {
            extension: "Please upload file in these format only (jpg, jpeg, png, svg)."
        },
        admin_image: {
            extension: "Please upload file in these format only (jpg, jpeg, png, svg)."
        }
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});


$(".change-school-admin").validate({
    rules: {
        edit_admin_image: {
            extension: "jpg|jpeg|png|svg"
        },
    },
    messages: {
        edit_admin_image: {
            extension: "Please upload file in these format only (jpg, jpeg, png)."
        },
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".create-staff-form").validate({
    rules: {
        image: {
            extension: "jpg|jpeg|png|svg"
        },
    },
    messages: {
        image: {
            extension: "Please upload file in these format only (jpg, jpeg, png)."
        },
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-staff-form").validate({
    rules: {
        image: {
            extension: "jpg|jpeg|png|svg"
        },
    },
    messages: {
        image: {
            extension: "Please upload file in these format only (jpg, jpeg, png)."
        },
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$('.profile-update-form').validate({
    rules: {
        image: {
            extension: "jpg|jpeg|png|svg"
        },
    },
    messages: {
        image: {
            extension: "Please upload file in these format only (jpg, jpeg, png)."
        },
    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

// THIS FUNCTIONS SHOULD ALWAYS BE AT THE LAST OF THE DOCUMENT
// OTHERWISE IT WILL CONFLICT THE OTHER VALIDATION FUNCTIONS
/************ Common Validation ***********/
let defaultValidationClasses = ['.create-form', '#create-form', '.create-form-without-reset', '.edit-form', '#edit-form','.common-validation'];

defaultValidationClasses.forEach(function (value, index) {
    $(value).validate({
        success: function (label, element) {
            success(element);
        },
        errorPlacement: function (label, element) {
            errorPlacement(label, element);
        },
        highlight: function (element, errorClass) {
            highlight(element, errorClass);
        }
    });

})

$(".online-registration-form").validate({
    rules: {
        'first_name': "required",
        'last_name': "required",
        'mobile': "number",
        'dob': "required",
        'class_section_id': "nullable",
        'admission_no': "required",
        'admission_date': "required",
        'guardian_email': {
            "required": true,
            "email": true,
        },
        'guardian_first_name': "required",
        'guardian_last_name': "required",
        'guardian_mobile': {
            "number": true,
            "required": true,
        },

    },
    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".restore-form").validate({
    rules: {
        zip: {
            required: true,
            extension: "zip"
        },
    },

    success: function (label, element) {
        success(element);
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});
