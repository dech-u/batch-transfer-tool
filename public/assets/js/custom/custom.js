"use strict";
const $table = $("#table_list"); // "table" accordingly
$(function () {
    // $("#sortable-row").sortable({
    //     placeholder: "ui-state-highlight"
    // });
    function checkList(listName, newItem) {
        let duplicate = false;
        $("#" + listName + " > div").each(function () {
            if ($(this)[0] !== newItem[0]) {
                if ($(this).find("li").attr('id') == newItem.find("li").attr('id')) {
                    duplicate = true;
                }
            }
        });
        return duplicate;
    }

    $('#table_list_exam_questions').on('check.bs.table', function (e, row) {
        let questions = $(this).bootstrapTable('getSelections');
        let li = ''
        $.each(questions, function (index, value) {
            li = $('<div class="list-group"><input type="hidden" name="assign_questions[' + value.question_id + '][question_id]" value="' + value.question_id + '"><li id="q' + value.question_id + '" class="list-group-item justify-content-between align-items-center ui-state-default list-group-item-secondary m-2">' + value.question_id + ". " + value.question + ' <span class="text-right row mx-0"><input type="number" min="1" class="list-group-item col-md-3 col-sm-12 form-control-sm mr-2 mb-2" name="assign_questions[' + value.question_id + '][marks]" placeholder="' + trans['enter_marks'] + '"><a class="btn btn-danger btn-sm remove-row mb-2" data-id="' + value.question_id + '"><i class="fa fa-times" aria-hidden="true"></i></a></span></li></div>');
            let pasteItem = checkList("sortable-row", li, row.question_id);
            if (!pasteItem) {
                $("#sortable-row").append(li);
            }
        });
        createCkeditor();
    })
    $('#table_list_exam_questions').on('uncheck.bs.table', function (e, row) {
        $("#sortable-row > div").each(function () {
            $(this).find('#q' + row.question_id).remove();
        });
    })
    // $table.bootstrapTable('destroy').bootstrapTable({
    //     exportTypes: ['csv', 'excel', 'pdf', 'txt', 'json'],
    // });

    $("#toolbar")
        .find("select")
        .change(function () {
            $table.bootstrapTable("refreshOptions", {
                exportDataType: $(this).val()
            });
        });

    //File Upload Custom Component
    $('.file-upload-browse').on('click', function () {
        let file = $(this).parent().parent().parent().find('.file-upload-default');
        file.trigger('click');
    });
    $('.file-upload-default').on('change', function () {

        $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });

    let layout_direction = 'ltl';
    if(isRTL()) {
        layout_direction = 'rtl';
    } else {
        layout_direction = 'ltl';
    }

    if ($('#tinymce_message').length) {
        tinymce.init({
            directionality : layout_direction,
            height: "500",
            selector: '#tinymce_message',
            relative_urls: false,
            remove_script_host: false,
            menubar: 'file edit view formate tools',
            toolbar: [
                'styleselect fontselect fontsizeselect',
                'undo redo | cut copy paste | bold italic | alignleft aligncenter alignright alignjustify | table | image | fullscreen',
                'bullist numlist | outdent indent | blockquote autolink | lists | fontfamily | fontsize | code | preview'
            ],
            content_style: "@import url('https://fonts.googleapis.com/css2?family=Pinyon%20Script:wght@900&family=Pinyon%20Script&display=swap'); body { font-family: 'Lato', sans-serif; } h1,h2,h3,h4,h5,h6 { font-family: 'Pinyon%20Script', sans-serif; }",
            font_family_formats: "Arial Black=arial black,avant garde; Courier New=courier new,courier; Lato=lato; Pinyon Script=Pinyon Script;",
            plugins: 'autolink link image lists code table fullscreen preview',
            font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 28pt 36pt 48pt',
        });
    }

    $('.modal').on('hidden.bs.modal', function () {
        //Reset input file on modal close
        $('.file-upload-default').val('');
        $('.file-upload-info').val('');
    })
    /*simplemde editor*/
    if ($("#simpleMde").length) {
        new SimpleMDE({
            element: $("#simpleMde")[0],
            hideIcons: ["guide", "fullscreen", "image", "side-by-side"],
        });
    }

    if ($(".color-picker").length) {
        $('.color-picker').asColorPicker();
    }

    if ($(".theme_color").length) {
        $('.theme_color').asColorPicker();
    }
    if ($(".primary_color").length) {
        $('.primary_color').asColorPicker();
    }
    if ($(".secondary_color").length) {
        $('.secondary_color').asColorPicker();
    }

    //Color Picker Custom Component

    //Date Picker
    // if ($(".datepicker-popup-no-future").length) {
    //     var today = new Date();
    //     var maxDate = new Date();
    //     maxDate.setDate(today.getDate());
    //     $('.datepicker-popup-no-future').datepicker({
    //         enableOnReadonly: false,
    //         todayHighlight: true,
    //         format: "dd-mm-yyyy",
    //         endDate: maxDate,
    //     });
    // }
    //Added this for Dynamic Date Picker input Initialization
    $('body').on('focus', ".datepicker-popup-no-future", function () {
        let today = new Date();
        let maxDate = new Date();
        maxDate.setDate(today.getDate());
        $(this).datepicker({
            enableOnReadonly: false,
            todayHighlight: true,
            format: "dd-mm-yyyy",
            endDate: maxDate,
            rtl: isRTL()
        });
    });

    $('body').on('focus', ".datepicker-popup-no-past", function () {
        let today = new Date();
        let minDate = new Date();
        minDate.setDate(today.getDate());
        $(this).datepicker({
            enableOnReadonly: false,
            todayHighlight: true,
            format: "dd-mm-yyyy",
            startDate: minDate,
            rtl: isRTL()
        });
    });

    // //Date Picker
    // if ($(".datepicker-popup").length) {
    //     $('.datepicker-popup').datepicker({
    //         enableOnReadonly: false,
    //         todayHighlight: true,
    //         format: "dd-mm-yyyy",
    //     });
    // }
    //Added this for Dynamic Date Picker input Initialization
    $('body').on('focus', ".datepicker-popup", function () {
        $(this).datepicker({
            enableOnReadonly: false,
            todayHighlight: true,
            format: "dd-mm-yyyy",
            rtl: isRTL()
        });
    });

    //Time Picker
    if ($("#timepicker-example").length) {
        $('#timepicker-example').datetimepicker({
            format: 'LT'
        });
    }
    //Select
    if ($(".select2-dropdown").length) {
        $(".select2-dropdown").select2();
    }

    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
});

// date range picker
$(function() {
    $(".daterange").daterangepicker({
        opens: 'right',
        autoUpdateInput: false,
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        $('#table_list').bootstrapTable('refresh');
    }).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});

// $('.edit-class-teacher-form').on('submit', function (e) {
//     e.preventDefault();
//     let formElement = $(this);
//     let submitButtonElement = $(this).find(':submit');
//     let data = new FormData(this);
//     let url = $(this).attr('action');
//
//     function successCallback(response) {
//         $('#table_list').bootstrapTable('refresh');
//
//         //Reset input file field
//         $('.file-upload-default').val('');
//         $('.file-upload-info').val('');
//         setTimeout(function () {
//             window.location.reload();
//         }, 1000)
//     }
//
//     formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
// })

// 'Start-trial-Pacakge' Display and Hide
$("#trialBtn").on("click", function() {
    $("#trialCheckboxContainer").show();
});

$("#staticBackdrop").on("hidden.bs.modal", function () {
    $("#trialCheckboxContainer").hide();
});

$(document).on('change', '.file_type', function () {
    let type = $(this).val();
    let parent = $(this).parent();
    if (type == "file_upload") {
        parent.siblings('#file_name_div').show();
        parent.siblings('#file_thumbnail_div').hide();
        parent.siblings('#file_div').show();
        parent.siblings('#file_link_div').hide();
    } else if (type == "video_upload") {
        parent.siblings('#file_name_div').show();
        parent.siblings('#file_thumbnail_div').show();
        parent.siblings('#file_div').show();
        parent.siblings('#file_link_div').hide();
    } else if (type == "youtube_link") {
        parent.siblings('#file_name_div').show();
        parent.siblings('#file_thumbnail_div').show();
        parent.siblings('#file_div').hide();
        parent.siblings('#file_link_div').show();
    } else if (type == "other_link") {
        parent.siblings('#file_name_div').show();
        parent.siblings('#file_thumbnail_div').show();
        parent.siblings('#file_div').hide();
        parent.siblings('#file_link_div').show();
    } else {
        parent.siblings('#file_name_div').hide();
        parent.siblings('#file_thumbnail_div').hide();
        parent.siblings('#file_div').hide();
        parent.siblings('#file_link_div').hide();
    }
})

// Repeater On Lesson And Topic Files
let IdLessonTopicCounter = 0;
const addNewLessonTopicFileRepeater = $('.files_data').repeater({
    initEmpty: true,
    show: function () {
        // Remove Button
        let newRemoveButtonId = 'remove-lesson-topic-file-' + IdLessonTopicCounter;

        // label *'s IDs
        let newThumbnailRequired = 'thumbnail-required-' + IdLessonTopicCounter;
        let newFileUploadRequired = 'file-upload-required-' + IdLessonTopicCounter;

        // Preview File's IDs
        let newThumbnailId = 'thumbnail-preview-' + IdLessonTopicCounter;
        let newFilePreviewId = 'file-preview-' + IdLessonTopicCounter;

        $(this).find('.remove-lesson-topic-file').attr('id', newRemoveButtonId)

        $(this).find('.thumbnail-required').attr('id', newThumbnailRequired)
        $(this).find('.thumbnail-preview').attr('id', newThumbnailId)

        $(this).find('.file-upload-required').attr('id', newFileUploadRequired)
        $(this).find('.file-preview').attr('id', newFilePreviewId)

        $(this).slideDown();
        IdLessonTopicCounter++;
    },
    hide: function (deleteElement) {
        // If button has Data ID then Call ajax function to delete file
        if ($(this).find('.remove-lesson-topic-file').data('id')) {
            let file_id = $(this).find('.remove-lesson-topic-file').data('id');

            let $this = $(this);

            Swal.fire({
                title: window.trans["Are you sure"],
                text: window.trans["delete_warning"],
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: window.trans["yes_delete"],
                cancelButtonText: window.trans["Cancel"]
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = baseUrl + '/file/delete/' + file_id;
                    let data = null;

                    function successCallback(response) {
                        $this.slideUp(deleteElement);
                        $('#table_list').bootstrapTable('refresh');
                        showSuccessToast(response.message);
                    }

                    function errorCallback(response) {
                        showErrorToast(response.message);
                    }

                    ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
                }
            })
        } else {
            // If button don't have any Data id then simply remove that row from DOM
            $(this).slideUp(deleteElement);
        }
    }
});

$(document).on('click', '.remove-gallery-image', function (e) {
    e.preventDefault();
    var $this = $(this);
    var file_id = $(this).data('id');

    Swal.fire({
        title: window.trans['Are you sure'],
        text: window.trans['You wont be able to revert this'],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes, Change it"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/gallery/file/delete/' + file_id;
            let data = null;

            function successCallback(response) {
                // $this.parent().remove();
                $this.parent().slideUp(500);
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
        }
    })
});

$('#topic_class_section_id').on('change', function () {
    let html = "<option value=''>--Select Lesson--</option>";
    $('#topic-lesson-id').html(html);
    $('#topic_subect_id').trigger('change');
})

$('#topic_subject_id').on('change', function () {
    let url = baseUrl + '/lesson/search';
    let data = {
        'subject_id': $(this).val(),
        'class_section_id': $('#topic_class_section_id').val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length > 0) {
            html += "<option>--Select Lesson--</option>"
            response.data.forEach(function (data) {
                html += "<option value='" + data.id + "'>" + data.name + "</option>";
            })
        } else {
            html = "<option value=''>No Data Found</option>";
        }
        $('#topic-lesson-id').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})

$('#resubmission_allowed').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).val(1);
        $('#extra_days_for_resubmission_div').show(500);
    } else {
        $(this).val(0);
        $('#extra_days_for_resubmission_div').hide(500);
    }
})

$('#edit_resubmission_allowed').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).val(1);
        $('#edit_extra_days_for_resubmission_div').show(500);
    } else {
        $(this).val(0);
        $('#edit_extra_days_for_resubmission_div').hide(500);
    }
})

$('.checkbox_add_url').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).val(1);      
        // $('#fileOption').hide(500);
        $('#add_url').show(500);
    } else {
        $(this).val(0);
        // $('#fileOption').show(500);
        $('#add_url').hide(500);
    }
});

$('.edit_checkbox_add_url').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).val(1);      
        // $('#fileOption').hide(500);
        $('#edit_add_url').show(500);
    } else {
        $(this).val(0);
        // $('#fileOption').show(500);
        $('#edit_add_url').hide(500);
    }
});

$('#edit_topic_class_section_id').on('change', function () {
    let html = "<option value=''>--Select Lesson--</option>";
    $('#topic-lesson-id').html(html);
    $('#topic_subect_id').trigger('change');
})

$('#edit_topic_subject_id').on('change', function () {
    let url = baseUrl + '/lesson/search';
    let data = {
        'subject_id': $(this).val(),
        'class_section_id': $('#edit_topic_class_section_id').val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length > 0) {
            response.data.forEach(function (data) {
                html += "<option value='" + data.id + "'>" + data.name + "</option>";
            })
        } else {
            html = "<option value=''>No Data Found</option>";
        }
        $('#edit_topic_lesson_id').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})

$(document).on('click', '.remove-assignment-file', function (e) {
    e.preventDefault();
    let $this = $(this);
    let file_id = $(this).data('id');
    // TODO : Remove this and use deletepopup function
    Swal.fire({
        title: window.trans['Are you sure'],
        text: window.trans['You wont be able to revert this'],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans['yes_delete'],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/announcement/file/delete/' + file_id;
            let data = null;

            function successCallback(response) {
                $this.parent().remove();
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
        }
    })
});

select2Search($(".edit-school-admin-search"), baseUrl + "/schools/admin/search", null, 'Search for school admin Email', Select2SearchDesignTemplate, function (repo) {
    if (!repo.text) {
        $('#edit_admin_email').val(repo.id);
        $('#edit-admin-first-name').val(repo.first_name);
        $('#edit-admin-last-name').val(repo.last_name);
        $('#edit-admin-contact').val(repo.mobile);
        $('#admin-image-tag').attr('src', repo.image);
    } else {
        $('#edit_admin_email').val(repo.text);
        $('#edit-admin-first-name').val('');
        $('#edit-admin-last-name').val('');
        $('#edit-admin-contact').val('');
        $('#admin-image-tag').attr('src', '');
    }
    return repo.email || repo.text;
});

//Guardian Search
select2Search($(".guardian-search"), baseUrl + "/guardian/search", null, 'Search for Guardian Email', Select2SearchDesignTemplate, function (repo) {
    if (!repo.text) {
        $('.guardian_email').val(repo.email);
        $('#guardian_first_name').val(repo.first_name).prop('readonly', true);
        $('#guardian_last_name').val(repo.last_name).prop('readonly', true);
        $('#guardian_mobile').val(repo.mobile).prop('readonly', true);
        if (repo.gender == 'male') {
            $('#guardian_female').removeAttr('checked');
            $('#guardian_female').bind('click', function () {
                return false;
            })

            $('#guardian_male').attr('checked', 'true');
        } else {
            $('#guardian_male').removeAttr('checked');
            $('#guardian_male').bind('click', function () {
                return false;
            })

            $('#guardian_female').attr('checked', 'true');
        }

        $('#guardian_image').siblings('span').find('button').prop('disabled', true);
        $('#guardian-image-preview').attr('src', repo.image);
    } else {
        $('.guardian_email').val(repo.text).prop('readonly', false);
        $('#guardian_first_name').val('').prop('readonly', false);
        $('#guardian_last_name').val('').prop('readonly', false);
        $('#guardian_mobile').val('').prop('readonly', false);
        $('#guardian-image-tag').attr('src', '');
        $('#guardian_image').siblings('span').find('button').prop('disabled', false);
        $('#guardian_male').unbind('click');
        $('#guardian_female').unbind('click');
    }
    return repo.email || repo.text;
});

select2Search($(".edit-guardian-search"), baseUrl + "/guardian/search", null, 'Search for Guardian Email', Select2SearchDesignTemplate, function (repo) {
    if (!repo.text) {
        $('#edit_guardian_email').val(repo.email);
        $('#edit_guardian_first_name').val(repo.first_name);
        $('#edit_guardian_last_name').val(repo.last_name);
        if (repo.gender == 'male') {
            $('#edit-guardian-female').prop('checked', false);
            $('#edit-guardian-male').prop('checked', true);
        } else {
            $('#edit-guardian-male').prop('checked', false);
            $('#edit-guardian-female').prop('checked', true);
        }
        $('#edit_guardian_mobile').val(repo.mobile);
        $('#edit_guardian_dob').val(repo.dob);
        $('#edit-guardian-image-tag').attr('src', repo.image);
    } else {
        $('#edit_guardian_email').val(repo.text);
        $('#edit_guardian_first_name').val('');
        $('#edit_guardian_last_name').val('');
        $('#edit_guardian_mobile').val('');
    }
    return repo.email || repo.text;
});
$(document).on('submit', '.email-template-setting-form', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let data = formData.get('data');
    let name = $('#name').val();

    let email_template_school_registration = formData.get('email_template_school_registration');
    let school_reject_template = formData.get('school_reject_template');

    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let submitButtonText = submitButtonElement.val();
    $.ajax({
        type: "PUT",
        url: url,
        data: {email_template_school_registration: email_template_school_registration, school_reject_template: school_reject_template},
        beforeSend: function () {
            submitButtonElement.val('Please Wait...').attr('disabled', true);
        },
        success: function (response) {
            if (response.error == false) {
                showSuccessToast(response.message);
                submitButtonElement.val(submitButtonText).attr('disabled', false);
            } else {
                submitButtonElement.val(submitButtonText).attr('disabled', false);
                showErrorToast(response.message);
            }
        }

    });
});
$(document).on('submit', '.setting-form', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let data = formData.get('data');
    let name = $('#name').val();
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let submitButtonText = submitButtonElement.val();
    $.ajax({
        type: "PUT",
        url: url,
        data: {data: data, name: name},
        beforeSend: function () {
            submitButtonElement.val('Please Wait...').attr('disabled', true);
        },
        success: function (response) {
            if (response.error == false) {
                showSuccessToast(response.message);
                submitButtonElement.val(submitButtonText).attr('disabled', false);
            } else {
                submitButtonElement.val(submitButtonText).attr('disabled', false);
                showErrorToast(response.message);
            }
        }

    });
});

$(document).on('submit', '.school-email-template', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let staff_data = formData.get('staff_data');
    let parent_data = formData.get('parent_data');
    let reject_email_data = formData.get('reject_email_data');
    let template = formData.get('template');
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let submitButtonText = submitButtonElement.val();
    $.ajax({
        type: "PUT",
        url: url,
        data: {staff_data: staff_data, parent_data: parent_data, reject_email_data:reject_email_data, template: template},
        beforeSend: function () {
            submitButtonElement.val('Please Wait...').attr('disabled', true);
        },
        success: function (response) {
            if (response.error == false) {
                submitButtonElement.val(submitButtonText).attr('disabled', false);
                showSuccessToast(response.message);
            } else {
                submitButtonElement.val(submitButtonText).attr('disabled', false);
                showErrorToast(response.message);
            }
        }

    });
});

// $('.general-setting').on('submit', function (e) {
//     e.preventDefault();
//     let formElement = $(this);
//     let submitButtonElement = $(this).find(':submit');
//     let url = $(this).attr('action');
//     let data = new FormData(this);

//     function successCallback() {
//         setTimeout(function () {
//             location.reload();
//         }, 1000)
//     }

//     formAjaxRequest('post', url, data, formElement, submitButtonElement, successCallback);
// });


$('#edit_class_section_id').on('change', function (e, subject_id) {
    // let class_id = $(this).find(':selected').data('class');
    let class_section_id = $(this).val();
    let url = baseUrl + '/subject-by-class-section';
    let data = {class_section_id: class_section_id};

    function successCallback(response) {
        if (response.length > 0) {
            let html = '';
            $.each(response, function (key, value) {
                html += '<option value="' + value.subject_id + '">' + value.subject.name + ' - ' + value.subject.type + '</option>'
            });
            $('#edit_subject_id').html(html);
            if (subject_id) {
                $('#edit_subject_id').val(subject_id);
            }
        } else {
            $('#edit_subject_id').html("<option value=''>--No data Found--</option>>");
        }
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})

$('#system-update').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(function () {
            window.location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

// $("#create-form-bulk-data").submit(function (e) {
//     e.preventDefault();
//     let formElement = $(this);
//     let submitButtonElement = $(this).find(':submit');
//     let url = $(this).attr('action');
//     let data = new FormData(this);
//
//     function successCallback() {
//         formElement[0].reset();
//     }
//
//     function errorCallback(response) {
//     }
//
//     formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback, errorCallback);
// });

$('#admin-profile-update').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.edit-exam-result-marks-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        $('#editModal').modal('hide');
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('.edit-form-timetable').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        $('#editModal').modal('hide');
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('#verify_email').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
});

/* TODO : Used in Assign Subject Teacher. Remove this if not Required */
// $('.subject_id').on('change', function () {
//     // let class_id = $(this).find(':selected').data('class');
//     let class_section_id = $('.class_section_id').val();
//     let subject_id = $(this).val();
//     let url = baseUrl + '/teacher-by-class-subject';
//     let data = {
//         class_section_id: class_section_id,
//         subject_id: subject_id
//     };
//
//
//     function successCallback(response) {
//         if (response.length > 0) {
//             let html = '';
//             $.each(response, function (key, value) {
//                 html += '<option value="' + value.id + '">' + value.user.first_name + ' ' + value.user.last_name + '</option>'
//             });
//             $('#teacher_id').html(html);
//         } else {
//             $('#teacher_id').html("<option value=''>--No data Found--</option>>");
//         }
//     }
//
//     ajaxRequest('GET', url, data, null, successCallback, null, null, true)
// })

// **************** TODO: MAHESH Route not defined ************
// $('#edit_subject_id').on('change', function () {

//     let edit_id = $('#id').val();
//     let class_section_id = $('#edit_class_section_id').val();
//     let subject_id = $(this).val();
//     let url = baseUrl + '/teacher-by-class-subject';
//     let data = {
//         edit_id: edit_id,
//         class_section_id: class_section_id,
//         subject_id: subject_id
//     };

//     function successCallback(response) {
//         if (response.length > 0) {
//             let html = '';
//             $.each(response, function (key, value) {
//                 html += '<option value="' + value.id + '">' + value.user.first_name + ' ' + value.user.last_name + '</option>'
//             });
//             $('#edit_teacher_id').html(html);
//         } else {
//             $('#edit_teacher_id').html("<option value=''>--No data Found--</option>>");
//         }
//     }

//     ajaxRequest('GET', url, data, null, successCallback, null, null, true)
// })


// $(document).on('click', '.remove-fees-type', function (e) {
//     e.preventDefault();
//     // let $this = $(this);
//     // TODO : Remove this and use deletepopup function
//     if ($(this).data('id')) {
//         Swal.fire({
//             title: 'Are you sure?',
//             text: "You won't be able to revert this!",
//             icon: 'warning',
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Yes, delete it!'
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 let id = $(this).data('id');
//                 let url = baseUrl + '/class/fees-type/' + id;
//
//                 function successCallback(response) {
//                     showSuccessToast(response['message']);
//                     setTimeout(function () {
//                         $('#editModal').modal('hide');
//                     }, 1000)
//                     $('#table_list').bootstrapTable('refresh');
//                     $(this).parent().parent().remove();
//                 }
//
//                 function errorCallback(response) {
//                     showErrorToast(response['message']);
//                 }
//
//                 ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
//             }
//         })
//     } else {
//         $(this).parent().parent().remove();
//     }
// });
$('.mode').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 2) {
        $('.cheque-no-container').show(200);
    } else {
        $('.cheque-no-container').hide(200);
    }
});
$('.pay_student_fees_offline').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        $('#editModal').modal('hide');
        $('.cheque-no-container').hide();
        formElement[0].reset();
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.edit_mode').on('change', function (e) {
    e.preventDefault();
    let mode_val = $(this).val();
    if (mode_val == 1) {
        $('.edit_cheque_no_container').show(200);
    } else {
        $('.edit_cheque_no_container').hide(200);
    }
});
$(document).on('click', '.remove-paid-optional-fees', function (e) {
    e.preventDefault();
    // TODO : Remove this and use deletepopup function
    Swal.fire({
        title: window.trans['Are you sure'],
        text: window.trans['You wont be able to revert this'],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans['yes_delete'],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            // let amount = $(this).data("amount");
            // let url = $(this).attr('href');
            let id = $(this).data("id");
            let url = baseUrl + '/fees/paid/remove-optional-fee/' + id;
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
                window.location.reload();
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
        }
    })
})
$('#create-fees-config-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('#edit-fees-paid-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let data = new FormData(this);
    data.append("_method", "PUT");
    let url = $(this).attr('action') + "/" + data.get('edit_id');

    function successCallback() {
        $('#table_list').bootstrapTable('refresh');
        setTimeout(function () {
            $('#editFeesPaidModal').modal('hide');
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('#razorpay_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        // Disable others when Razorpay is enabled
        $('#stripe_status').val(0);
        $('#paystack_status').val(0);
        $('#flutterwave_status').val(0);
        $('#bank_transfer_status').val(0);
    }
});

$('#stripe_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        // Disable others when Stripe is enabled
        $('#razorpay_status').val(0);
        $('#paystack_status').val(0);
        $('#flutterwave_status').val(0);
        $('#bank_transfer_status').val(0);
    }
});

$('#paystack_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        // Disable others when Paystack is enabled
        $('#razorpay_status').val(0);
        $('#stripe_status').val(0);
        $('#flutterwave_status').val(0);
        $('#bank_transfer_status').val(0);
    }
});

$('#flutterwave_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        // Disable others when Flutterwave is enabled
        $('#razorpay_status').val(0);
        $('#stripe_status').val(0);
        $('#paystack_status').val(0);
        $('#bank_transfer_status').val(0);
    }
});

$('#bank_transfer_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('#razorpay_status').val(0);
        $('#stripe_status').val(0);
    } else {
        $('#bank_transfer_status').val(1);
    }
});

// Paystack toggle for new UI
$(document).ready(function() {
    $('#Paystack').on('change', function() {
        if ($(this).is(':checked')) {
            $('#PaystackForm').removeClass('d-none');
            
            // Disable other payment methods
            $('#stripe_status').val(0);
            $('#razorpay_status').val(0);
            $('#flutterwave_status').val(0);
            $('#bank_transfer_status').val(0);
            
            // Uncheck other toggles
            $('#Flutterwave').prop('checked', false);
            $('#FlutterwaveForm').addClass('d-none');
        } else {
            $('#PaystackForm').addClass('d-none');
        }
    });
    
    // Flutterwave toggle for new UI
    $('#Flutterwave').on('change', function() {
        if ($(this).is(':checked')) {
            $('#FlutterwaveForm').removeClass('d-none');
            
            // Disable other payment methods
            $('#stripe_status').val(0);
            $('#razorpay_status').val(0);
            $('#paystack_status').val(0);
            $('#bank_transfer_status').val(0);
            
            // Uncheck other toggles
            $('#Paystack').prop('checked', false);
            $('#PaystackForm').addClass('d-none');
        } else {
            $('#FlutterwaveForm').addClass('d-none');
        }
    });
});

$('#assign-roll-no-form').on('submit', function (e) {
    e.preventDefault();
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["delete_warning"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes, Change it"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let formElement = $(this);
            let submitButtonElement = $(this).find(':submit');
            let url = $(this).attr('action');
            let data = new FormData(this);

            function successCallback() {
                $('#table_list').bootstrapTable('refresh');
            }

            formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
        }
    })
})

// $('#add-new-option').on('click', function (e) {
//     e.preventDefault();
//     let html = $('.option-container').find('.form-group:last').clone();
//     html.find('.add-question-option').val('');
//     html.find('.error').remove();
//     html.find('.has-danger').removeClass('has-danger');
//     $('.remove-option-content').css('display', 'none');
//     html.addClass('quation-option-extra');

//     // html.removeClass('col-md-6').addClass('col-md-5');
//     // This function will increment in the label option number
//     let inner_html = html.find('.option-number:last').html();
//     html.find('.option-number:last').each(function (key, element) {
//         inner_html = inner_html.replace(/(\d+)/, function (str, p1) {
//             return (parseInt(p1, 10) + 1);
//         });
//     })
//     html.find('.option-number:last').html(inner_html)

//     // This function will replace the last index value and increment in the multidimensional name attribute
//     html.find(':input').each(function () {
//         this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
//             return '[' + (parseInt(p1, 10) + 1) + ']';
//         });
//     })
//     html.find('.remove-option-content').html('<button class="btn btn-inverse-danger remove-option btn-sm mt-1" type="button"><i class="fa fa-times"></i></button>')
//     $('.option-container').append(html)

//     let select_answer_option = '<option value=' + inner_html + ' class="answer_option extra_answers_options">' + window.trans["option"] + ' ' + inner_html + '</option>'
//     $('#answer_select').append(select_answer_option)
// });
// $(document).on('click', '.remove-option', function (e) {
//     e.preventDefault();
//     $(this).parent().parent().remove();
//     $('.option-container').find('.form-group:last').find('.remove-option-content').css('display', 'block');
//     $('#answer_select').find('.answer_option:last').remove();
// })
$('#create-online-exam-questions-form').on('submit', function (e) {
    e.preventDefault();
    for (let equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].updateElement();
    }
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
// $('.question_type').on('change', function (e) {
//     $('.quation-option-extra').remove();
//     $('#answer_select').val(null).trigger("change");
//     if ($(this).val() == 1) {
//         $('#simple-question').hide();
//         $('#equation-question').show(500);
//     } else {
//         $('#simple-question').show(500);
//         $('#equation-question').hide();
//     }
// })
// $('#add-new-eqation-option').on('click', function (e) {
//     e.preventDefault();
//     let html = $('.equation-option-container').find('.quation-option-template:last').clone();
//     html.find('.error').remove();
//     html.find('.has-danger').removeClass('has-danger');
//     $('.remove-equation-option-content').css('display', 'none');

//     // html.removeClass('col-md-6').addClass('col-md-5');
//     // This function will increment in the label equation-option-number
//     let inner_html = html.find('.equation-option-number:last').html();
//     html.find('.equation-option-number:last').each(function (key, element) {
//         inner_html = inner_html.replace(/(\d+)/, function (str, p1) {
//             return (parseInt(p1, 10) + 1);
//         });
//     })

//     // This function will replace the last index value and increment in the multidimensional name attribute
//     let name;
//     html.find(':input').each(function (key, element) {
//         this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
//             name = '[' + (parseInt(p1, 10) + 1) + ']';
//             return name;
//         });
//     })

//     let option_html = '<div class="form-group col-md-6 equation-editor-options-extra quation-option-template"><label>' + window.trans["option"] + ' <span class="equation-option-number">' + inner_html + '</span> <span class="text-danger">*</span></label><textarea class="editor_options" name="eoption' + name + '" placeholder="' + window.trans["Select Option"] + '"></textarea><div class="remove-equation-option-content"><button class="btn btn-inverse-danger remove-equation-option btn-sm mt-1" type="button"><i class="fa fa-times"></i></button></div></div>'
//     $('.equation-option-container').append(option_html).ready(function () {
//         createCkeditor();
//     });
//     let select_answer_option = '<option value=' + inner_html + ' class="answer_option extra_answers_options">' + window.trans["option"] + ' ' + inner_html + '</option>'
//     $('#answer_select').append(select_answer_option)
// });
$(document).on('click', '.remove-equation-option', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    $('.equation-option-container').find('.form-group:last').find('.remove-equation-option-content').css('display', 'block');
    $('#answer_select').find('.answer_option:last').remove();
})

$('.edit-question-type').on('change', function () {
    if ($(this).val() == 1) {
        $('#edit-simple-question-content').hide();
        $('#edit-equation-question-content').show(500);
    } else {
        $('#edit-simple-question-content').show(500);
        $('#edit-equation-question-content').hide();
    }
})
$(document).on('click', '.add-new-edit-option', function (e) {
    e.preventDefault();
    let html = $('.edit_option_container').find('.form-group:last').clone();
    html.find('.add-edit-question-option').val('');
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    html.find('.edit_option_id').val('')
    let hide_button;
    hide_button = $('.remove-edit-option-content:last').find('.remove-edit-option')
    if (hide_button.data('id')) {
        $('.remove-edit-option-content:last').css('display', 'block');
    } else {
        $('.remove-edit-option-content:last').css('display', 'none');
    }

    // This function will increment in the label option number
    let inner_html = html.find('.edit-option-number:last').html();
    html.find('.edit-option-number:last').each(function () {
        inner_html = inner_html.replace(/(\d+)/, function (str, p1) {
            return (parseInt(p1, 10) + 1);
        });
    })
    html.find('.edit-option-number:last').html(inner_html)

    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find(':input').each(function () {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.remove-edit-option-content').html('<button class="btn btn-inverse-danger remove-edit-option btn-sm mt-1" type="button"><i class="fa fa-times"></i></button>')
    $('.edit_option_container').append(html)

    let select_answer_option = '<option value="new' + $.trim(inner_html) + '" class="edit_answer_option">' + window.trans["option"] + ' ' + inner_html + '</option>'
    $('.edit_answer_select').append(select_answer_option)
});

$('#add-new-question-online-exam').on('submit', function (e) {
    e.preventDefault();
    for (let equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].updateElement();
    }
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback(response) {
        // Get the CKEditor instance
        let editors = Object.values(CKEDITOR.instances);

        setTimeout(() => {
            location.reload();
        }, 1000);

        // Loop through each instance
        editors.filter(editor => editor.element.hasClass('editor_question')).forEach(editor => {
            editor.setData(''); // clear the text
            editor.resetDirty(); // reset the points to save the changes
        });

        editors.filter(editor => editor.element.hasClass('editor_options')).forEach(editor => {
            editor.setData(''); // clear the text
            editor.resetDirty(); // reset the points to save the changes
        });


        // remove the extra options of ckeditor
        $(document).find('.equation-editor-options-extra').remove();
        $(document).find('.extra_answers_options').remove();

        $('.add-new-question-container').hide(200)
        $('.add-new-question-button').show(300).ready(function () {
            $('.add-new-question-button').html(window.trans["add_new_question"]);
        })
        formElement[0].reset();

        $('#answer_select').val(null).trigger("change");
        $('.quation-option-extra').remove();
        $('#table_list_exam_questions').bootstrapTable('refresh');

        function checkList(listName, newItem) {
            let duplicate = false;
            $("#" + listName + " > div").each(function () {
                if ($(this)[0] !== newItem[0]) {
                    if ($(this).html() == newItem.html()) {
                        duplicate = true;
                    }
                }
            });
            return !duplicate;
        }

        let li;

        li = $('<div class="list-group"><input type="hidden" name="assign_questions[' + response.data.question_id + '][question_id]" value="' + response.data.question_id + '"><li id="q' + response.data.question_id + '" class="list-group-item justify-content-between align-items-center ui-state-default list-group-item-secondary m-2">' + response.data.question_id + ". " + response.data.question + ' <span class="text-right row mx-0"><input type="number" class="list-group-item col-md-3 col-sm-12 mr-2 mb-2" name="assign_questions[' + response.data.question_id + '][marks]" placeholder="' + trans['enter_marks'] + '"><a class="btn btn-danger btn-sm remove-row mb-2" data-id="' + response.data.question_id + '"><i class="fa fa-times" aria-hidden="true"></i></a></span></li></div>');

        let pasteItem = checkList("sortable-row", li);
        if (pasteItem) {
            $("#sortable-row").append(li);
        }
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
});
$('.add-new-question-button').on('click', function (e) {
    e.preventDefault();
    $('#answer_select').val(null).trigger("change");
    $('.add-new-question-container').show(300);
    createCkeditor();
    $(this).hide();
    $(this).html('');
})
$('.remove-add-new-question').on('click', function (e) {
    e.preventDefault();
    $('.add-new-question-container').hide(300);
    $('.add-new-question-button').show(300).ready(function () {
        $('.add-new-question-button').html(window.trans["add_new_question"]);
    });
})
$(document).on('click', '.remove-row', function () {
    let id = $(this).data('id');
    let edit_id = $(this).data('edit_id');
    let $this = $(this);
    if (edit_id) {
        Swal.fire({
            title: window.trans["Are you sure"],
            text: window.trans["delete_warning"],
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: window.trans["yes_delete"],
            cancelButtonText: window.trans["Cancel"]
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/online-exam/remove-choiced-question/' + edit_id;

                function successCallback(response) {
                    showSuccessToast(response.message);
                    $this.parent().parent().parent().remove();
                    $('#table_list_exam_questions').bootstrapTable('refresh');
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    } else {
        $(this).parent().parent().parent().remove();
        $('#table_list_exam_questions').bootstrapTable('uncheckBy', {field: 'question_id', values: [id]})
    }
})
$('#store-assign-questions-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        window.location.reload();
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('#edit-online-exam-questions-form').on('submit', function (e) {
    e.preventDefault();
    for (let equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].updateElement();
    }
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let data = new FormData(this);
    data.append("_method", "PUT");
    let url = $(this).attr('action') + "/" + data.get('edit_id');

    function successCallback() {
        setTimeout(function () {
            window.location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

// $(document).on('click', '.delete-question-form', function (e) {
//     e.preventDefault();
//     Swal.fire({
//         title: 'Are you sure?',
//         text: "You won't be able to revert this!",
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes, delete it!'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             let url = $(this).attr('href');
//             let data = null;

//             function successCallback(response) {
//                 $('#table_list_questions').bootstrapTable('refresh');
//                 showSuccessToast(response.message);
//             }

//             function errorCallback(response) {
//                 showErrorToast(response.message);
//             }

//             ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
//         }
//     })
// })
// $('#table_list_questions').on('load-success.bs.table', function () {
//     createCkeditor();
// });
$('#table_list_exam_questions').on('load-success.bs.table', function () {
    createCkeditor();
});
$(document).on('click', '.add-new-edit-eoption', function (e) {
    e.preventDefault();

    // destroy the editors for no cloning the last ckeditor
    for (let equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].destroy();
    }
    let html = $('.edit_eoption_container').find('.form-group:last').clone();
    html.find('.editor_options').val('');
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    html.find('.edit_eoption_id').val('')
    let hide_button;
    hide_button = $('.remove-edit-option-content:last').find('.remove-edit-option')
    if (hide_button.data('id')) {
        $('.remove-edit-option-content:last').css('display', 'block');
    } else {
        $('.remove-edit-option-content:last').css('display', 'none');
    }

    // This function will increment in the label option number
    let inner_html = html.find('.edit-eoption-number:last').html();
    html.find('.edit-eoption-number:last').each(function () {
        inner_html = inner_html.replace(/(\d+)/, function (str, p1) {
            return (parseInt(p1, 10) + 1);
        });
    })
    html.find('.edit-eoption-number:last').html(inner_html)

    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find(':input').each(function () {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.remove-edit-option-content').html('<button class="btn btn-inverse-danger remove-edit-option btn-sm mt-1" type="button"><i class="fa fa-times"></i></button>')
    $('.edit_eoption_container').append(html).ready(function () {
        createCkeditor();
    })

    let select_answer_option = '<option value="new' + $.trim(inner_html) + '" class="edit_answer_option">' + window.trans["option"] + ' ' + inner_html + '</option>'
    $('.edit_answer_select').append(select_answer_option)
});
// $('input[type="file"]').on('change', function () {
//     $(this).closest('form').valid();
// })
select2Search($(".edit-admin-search"), baseUrl + "/schools/admin/search", null, window.trans["search_admin_email"], Select2SearchDesignTemplate, function (repo) {
    if (!repo.text) {
        $('#edit-admin-first-name').val(repo.first_name);
        $('#edit-admin-last-name').val(repo.last_name);
        $('#edit-admin-contact').val(repo.mobile);
        $('#file-upload-admin-browse');
        $("#admin-image-tag").attr('src', repo.image);
    } else {
        $('#edit-admin-first-name').val('').removeAttr('readonly');
        $('#edit-admin-last-name').val('').removeAttr('readonly');
        $('#edit-admin-contact').val('').removeAttr('readonly');
        $("#admin-image-tag").attr('src', '');

    }
    return repo.email || repo.text;
});

$(document).on('click', '.delete-class-section', function (e) {
    e.preventDefault();
    let $this = $(this);
    showDeletePopupModal($(this).attr('href'), {
        successCallBack: function () {
            $this.siblings('label').children('input').attr('checked', false).removeAttr('disabled');
            $this.siblings('a').remove();
            $this.remove();
        }
    })
})

// Function to make remove button accessible on the basis of Option Section Length
let toggleAccessOfDeleteButtons = () => {
    if ($('.option-section').length >= 3) {
        $('.remove-default-option').removeAttr('disabled');
    } else {
        $('.remove-default-option').attr('disabled', true);
    }
}

// Function to make remove button accessible on the basis of Option Section Length
let editToggleAccessOfDeleteButtons = () => {
    if ($('.edit-option-section').length >= 3) {
        $('.remove-edit-default-option').removeAttr('disabled');
    } else {
        $('.remove-edit-default-option').attr('disabled', true);
    }
}


$('.type-field').on('change', function (e) {
    e.preventDefault();

    const inputValue = $(this).val();
    const optionSection = $('.default-values-section');

    // Show/hide the "default-values-section" based on the selected value using a switch statement
    switch (inputValue) {
        case 'dropdown':
        case 'radio':
        case 'checkbox':
            optionSection.show(500).find('input').attr('required', true);
            break;
        default:
            optionSection.hide(500).find('input').removeAttr('required');
            break;
    }

});

$('.edit-type-field').on('change', function (e) {
    e.preventDefault();

    const inputValue = $(this).val();
    const optionSection = $('.edit-default-values-section');

    // Show/hide the "edit-default-values-section" based on the selected value using a switch statement
    switch (inputValue) {
        case 'dropdown':
        case 'radio':
        case 'checkbox':
            optionSection.show(500).find('input').attr('required', true);
            $('.extra-edit-option-section').remove();
            // To Add Second Option
            $('.add-new-edit-option').click();
            break;
        default:
            optionSection.hide(500).find('input').removeAttr('required');
            break;
    }

});


// Repeater On Default Values section's Option Section
var defaultValuesRepeater = $('.default-values-section').repeater({
    show: function () {
        let optionNumber = parseInt($('.option-section:nth-last-child(2)').find('.option-number').text()) + 1;

        if (!optionNumber) {
            optionNumber = 1;
        }

        $(this).find('.option-number').text(optionNumber);

        $(this).slideDown();

        toggleAccessOfDeleteButtons();

    },
    hide: function (deleteElement) {
        $(this).slideUp(deleteElement);
        $(function () {
            toggleAccessOfDeleteButtons();
        });
    }
});

// // To Add Second Option
// $(function () {
//     $('.add-new-option').click()
// });

// Change the order of Form fields Data
$('#change-order-form-field').click(async function () {
    const ids = await $('#table_list').bootstrapTable('getData').map(function (row) {
        return row.id;
    });
    $.ajax({
        type: "post",
        url: baseUrl + "/form-fields/update-rank",
        data: {
            ids: ids
        },
        dataType: "json",
        success: function (data) {
            $('#table_list').bootstrapTable('refresh');
            if (!data.error) {
                showSuccessToast(data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showErrorToast(data.message);
            }
        }
    });
})

// Repeater On Default Values section's Option Section
var editDefaultValuesRepeater = $('.edit-default-values-section').repeater({
    show: function () {
        let optionNumber = parseInt($('.edit-option-section:nth-last-child(2)').find('.edit-option-number').text()) + 1;

        if (!optionNumber) {
            optionNumber = 1;
        }

        $(this).find('.edit-option-number').text(optionNumber);

        $(this).slideDown();
        $(this).addClass('extra-edit-option-section');

        editToggleAccessOfDeleteButtons();

    },
    hide: function (deleteElement) {
        // TODO : Add translation here
        Swal.fire({
            title: window.trans['Are you sure'],
            text: window.trans['You wont to delete this element'],
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: window.trans["yes_delete"],
            cancelButtonText: window.trans["Cancel"]
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).slideUp(deleteElement);
            }
        })

    }
});


/*Edit Class Subject Page*/
$(document).on('change', '.subject', function () {
    let validator = $(this).parents('form').validate();
    validator.form();
})

const coreSubject = $('.core-subject-repeater').repeater({
    initEmpty: true,
    show: function () {
        $(this).slideDown();
    },
    hide: function (deleteElement) {
        let class_subject_id = $(this).find('.class_subject_id').val();
        if (class_subject_id) {
            let url = baseUrl + '/class/subject/' + class_subject_id;
            showDeletePopupModal(url, {
                successCallBack: function () {
                    $(this).slideUp(deleteElement);
                }
            });
        } else {
            $(this).slideUp(deleteElement);
        }
    }
});

const electiveSubjectGroupRepeater = $('.elective-subject-group-repeater').repeater({
    initEmpty: true,
    show: function () {
        $(this).slideDown();
        if ($(this).children('.elective-subject-repeater').children('.row').children().length == 1) {
            // Trigger Click manually to generate second optional subject in list by default
            $(this).find('.add-new-elective-subject').click();
        }
        // changing the manual input fields of number type to number type from text
        $(this).find('input[data-convert="number"]').removeAttr('type').attr('type', "number")
        $(this).find('.or-div:last').hide();
        if ($(this).find('.elective-subject').length <= 2) {
            $(this).find('.remove-elective-subject').attr('disabled', true);
        }


        let total_selectable_subjects = $(this).find('.total_selectable_subjects');
        let max = $(this).find('.subject').length;
        total_selectable_subjects.attr('max', parseInt(max) - 1);
    },
    hide: function (deleteElement) {
        if ($(this).hasClass('elective-subject-group')) {
            let class_subject_group_id = $(this).find('.class_subject_group_id').val();
            if (class_subject_group_id) {
                let url = baseUrl + '/class/subject-group/' + class_subject_group_id;
                showDeletePopupModal(url, {
                    successCallBack: function () {
                        $(this).slideUp(deleteElement);
                    }
                });
            } else {
                $(this).slideUp(deleteElement);
            }
        }
    },
    repeaters: [{
        selector: '.elective-subject-repeater',
        show: function () {
            $(this).fadeIn();
            $(this).parent().find('.or-div').show();
            $(this).find('.or-div').hide();
            $(this).parent().find('.remove-elective-subject').attr('disabled', false);

            let total_selectable_subjects = $(this).parent().parent().parent().find('.total_selectable_subjects');
            let max = total_selectable_subjects.attr('max');
            total_selectable_subjects.attr('max', parseInt(max) + 1);
            $('.semesters').trigger('change');
        },
        hide: function (deleteElement) {
            if ($(this).siblings().length <= 2) {
                $(this).parent().find('.remove-elective-subject').attr('disabled', true);
            }

            if ($(this).siblings().length >= 2) {
                // Local function is created in order to reuse the code
                let deleteSubject = () => {
                    $(this).fadeOut(deleteElement);
                    setTimeout(() => {
                        $('.or-div:last').hide();
                    }, 500);
                    let total_selectable_subjects = $(this).parent().parent().parent().find('.total_selectable_subjects');
                    let max = total_selectable_subjects.attr('max');
                    total_selectable_subjects.attr('max', parseInt(max) - 1);
                }

                let class_subject_id = $(this).find('.class_subject_id').val();
                if (class_subject_id) {
                    let url = baseUrl + '/class/subject/' + class_subject_id;
                    showDeletePopupModal(url, {
                        successCallBack: function () {
                            deleteSubject();
                        }

                    });
                } else {
                    deleteSubject();
                }
            }
        },
    }]
});

// Student Reset Password Event
$(document).on('click', '.reset_password', function (e) {
    e.preventDefault();
    let studentID = $(this).data('id');
    let studentDOB = $(this).data('dob');
    let url = $(this).data('url');
    Swal.fire({
        title: window.trans["are_you_sure"],
        text: window.trans["reset_student_password"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes, Change it"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id: studentID,
                    dob: studentDOB
                },
                success: function (response) {
                    if (response.error == true) {
                        showErrorToast(response.message);
                    } else {
                        showSuccessToast(response.message);
                        $('#table_list').bootstrapTable('refresh');
                    }
                }
            })
        }
    })
})

// For Announcement Create Form Class Section And Subject
// $('.show_class_section_id').hide();
$('#assign_to').on('change', function () {
    let data = $(this).val();
    if (data == 'class_section') {
        $('.show_class_section_id').show();
        $('.show_class_section_id').find('.class_section_id').attr('required', true);
    } else {
        $('.show_class_section_id').find('.class_section_id').removeAttr('required');
        $('.show_class_section_id').hide();
    }
});
// -------------------------------------------------------------------------------------------------------------------


// For Announcement Edit Form Class Section And Subject
$('.edit_show_class_section_id').hide();
$('#edit-assign-to').on('change', function () {
    let data = $(this).val();
    if (data == 'class_section') {
        $('.edit_show_class_section_id').show();
        $('.edit_show_class_section_id').find('#edit-class-section-id').attr('required', true);
    } else {
        $('.edit_show_class_section_id').find('#edit-class-section-id').removeAttr('required');
        $('.edit_show_class_section_id').hide();
    }
});
// -------------------------------------------------------------------------------------------------------------------


// Function to Check that Ending Range Should not be more than 100
function endingRangeEvent() {
    // Get Last Ending Range
    let endingRange = ($('.grade-content').find('.ending-range:last'));

    // Add Key Up Event to check that Value should not be more than 100
    endingRange.on('change keyup', function () {
        if (parseInt($(this).val()) >= 100) {
            $('.add-grade-content').prop('disabled', true); // Make Add New Button Disabled
        } else {
            $('.add-grade-content').prop('disabled', false); // Make Add New Button Clickable
        }
    });
}

// Check the Change max value of Starting Range
function ChangeMaxValueOfStartingRange() {
    $('.ending-range').on('change keyup', function () {
        $(this).parent().siblings().find(".starting-range").attr('max', ($(this).val() - 1));
    })
}

let idGradeCounter = 0;
var gradesRepeater = $('.grade-content').repeater({
    initEmpty: true,
    show: function () {
        // Make Starting And Ending Range Readonly
        $('.starting-range').attr('readonly', true);
        $('.ending-range').attr('readonly', true);

        // Remove Readonly From Current Repeated Starting And Ending Range
        $(this).find('.starting-range').removeAttr('readonly');
        $(this).find('.ending-range').removeAttr('readonly');

        $('.remove-grades-div').find('.remove-grades').prop('disabled', true); // Hide Remove Button
        $(this).slideDown(); // Add Another Data Down
        $('.remove-grades-div:last').find('.remove-grades').prop('disabled', false); // Make Last Remove Button Visible
        $('.add-grade-content').prop('disabled', true); // Make Add New Data Button Disable

        let lastEndingRange = parseFloat($('.ending-range').eq(-2).val()) + 0.01; // Get the Second Last Ending Range Value
        lastEndingRange = isNaN(lastEndingRange) ? 0 : lastEndingRange; // If LastEndingRange is NaN (NOT A NUMBER) Then assign the value 0 or else Assign the value of its own variable

        $(this).find('.starting-range').attr('min', lastEndingRange).val(lastEndingRange) // Add lastEndingRange as Minimum and Value in Starting Range
        $(this).find('.ending-range').attr('min', (parseFloat(lastEndingRange) + 0.01)) // Add lastEndingRange as Minimum with increment one in Ending Range

        endingRangeEvent(); // Call Function to check the Ending Range should not be more than 100
        ChangeMaxValueOfStartingRange(); // Add ending-range Event Key up to add Max attribute as

        // Change the Form Fields type of text to number who has Attribute data-convert = number
        $(this).find('input[data-convert="number"]').removeAttr('type').attr('type', "number");

        // Check the duplicate Values in Grade Text Field
        $(this).find('.grade').rules("add", {
            "noDuplicateValues": {
                class: "grade",
                value: $(this).find('.grade').text()
            }
        });

        // Add Attribute ID with unique Counter in Remove Grades
        $(this).find('.remove-grades').attr('id', 'remove-grades-' + idGradeCounter);
        idGradeCounter++
    },
    hide: function (deleteElement) {
        let $this = $(this)
        let id = $(this).find('.remove-grades').data('id');
        if (id) {
            let url = baseUrl + '/exam/grade/' + id;
            showDeletePopupModal(url, {
                text: "Remove Exam Timetable!",
                successCallBack: function () {
                    $('.row').eq(-2).find('.remove-grades').prop('disabled', false)
                    $('.row').eq(-2).find('.starting-range').removeAttr('readonly');
                    $('.row').eq(-2).find('.ending-range').removeAttr('readonly').trigger('change');
                    $this.slideUp(deleteElement);
                }
            });
        } else {
            $('.row').eq(-2).find('.remove-grades').prop('disabled', false)
            $('.row').eq(-2).find('.starting-range').removeAttr('readonly');
            $('.row').eq(-2).find('.ending-range').removeAttr('readonly').trigger('change');
            $this.slideUp(deleteElement);
        }
    },
});

$(document).ready(function () {
    endingRangeEvent(); // Call Ending Range Function on the DOM LOAD
    ChangeMaxValueOfStartingRange();
});

// --------------------------------------------------------------------------------------------------------------------------

let idExamTimetableCounter = 0;
const examTimetableRepeater = $('.exam-timetable-content').repeater({
    initEmpty: true,
    show: function () {
        let $this = $(this);
        $this.slideDown(); // Add Another Data Down
        $(document).ready(function () {
            /**
             * TODO Validation
             * Timetable Start Time && End Time && Date Conflicts with other Repeater Data
             *
             **/
            // $this.find('.timetable-date').rules("add", {
            //     "warningDuplicateValues": {
            //         parentClass: "exam-timetable-content",
            //         startTimeClass: "start-time",
            //         endTimeClass: "end-time",
            //         dateClass: "timetable-date",
            //     }
            // });
        });
        $this.find('.remove-exam-timetable-content').attr('id', 'remove-exam-timetable-' + idExamTimetableCounter);
        $this.find('input[data-convert="number"]').removeAttr('type').attr('type', "number");
        $this.find('input[data-convert="time"]').removeAttr('type').attr('type', "time");
        idExamTimetableCounter++;

    },
    hide: function (deleteElement) {
        let $this = $(this);
        let id = $this.find('.remove-exam-timetable-content').data('id');
        if (id) {
            let url = baseUrl + '/exams/delete-timetable/' + id;
            showDeletePopupModal(url, {
                text: "Remove Exam Timetable!",
                successCallBack: function () {
                    $this.slideUp(deleteElement);
                }
            })
        } else {
            $this.slideUp(deleteElement);
        }
    },
});

// $('.start-time').on("change", function () {
//     $(this).find('.timetable-date').trigger("change")
// });

// $('.end-time').on("change", function () {
//     $(this).find('.timetable-date').trigger("change")
// });
// $('#exam_id').on('change', function () {
//     let exam_id = $(this).val();
//     $('#exam_class_section_id option').hide();
//     // $('#exam_class_section_id').find('option[data-class=' + class_id + ']').show();

//     let url = baseUrl + '/exams/get-exam-subjects/' + exam_id;

//     function successCallback(response) {
//         let html = ''
//         html = '<option>No Subjects</option>';
//         if (response.data) {
//             html = '<option value="">Select Subject</option>';
//             $.each(response.data, function (key, data) {
//                 html += '<option value=' + data.subject.id + '>' + data.subject.name + ' - ' + data.subject.type + '</option>';
//             });
//         } else {
//             html = '<option>No Subjects Found</option>';
//         }
//         $('#exam_subject_id').html(html);
//     }

//     ajaxRequest('GET', url, null, null, successCallback, null);
// });

/*Create Timetable Page*/

function isRTL() {
    var dir = $('html').attr('dir');
    if (dir === 'rtl') {
        return true;
    } else {
        return false;
    }
    return false;
}
$(document).on('change', '.timetable_start_time', function () {
    let $this = $(this);
    let end_time = $(this).parent().siblings().children('.timetable_end_time');
    $(end_time).rules("add", {
        timeGreaterThan: $this,
    });
})

let days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
let calendarEl = document.getElementById('calendar');
let containerEl = document.getElementById('external-events');
if (calendarEl !== null) {
    var createTimetable = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        contentHeight: 1500,
        direction: layout_direction,
        headerToolbar: {
            start: '', // will normally be on the left. if RTL, will be on the right
            center: '',
            end: '',
            // end: 'listDay,listWeek,timeGridWeek' // will normally be on the right. if RTL, will be on the left
        },
        views: {
            listDay: {buttonText: 'Today'},
            listWeek: {buttonText: 'List'},
            timeGridWeek: {buttonText: 'Calendar'}
        },
        slotMinTime: "00:00:00",
        slotMaxTime: "00:00:00",
        allDaySlot: false,
        firstDay: 1,
        expandRows: true,
        slotDuration: "01:00:00",
        snapDuration: "00:01:00",
        dayHeaderFormat: {
            weekday: 'short'
        },
        editable: true,
        droppable: true,
        eventDurationEditable: true,
        eventResizableFromStart: true,
        eventDidMount: function (event) {
            $(event.el).find('.fc-event-main .fc-event-main-frame').append("<div class='text-right'><span class='fa fa-times remove-timetable' data-id=" + event.event.id + "></span></div>");
        },
        eventReceive: function (event) {
            let subject_teacher_id = $(event.draggedEl).data('subject_teacher_id');
            let subject_id = $(event.draggedEl).data('subject_id');
            let note = $(event.draggedEl).data('note');
            let class_section_id = $('#class_section_id').val();
            let semester_id = $('#semester_id').val();
            let date = new Date(event.event.start);
            let startTime24Hr = date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();

            let end_time = new Date(event.event.end);
            let endTime24Hr = end_time.getHours() + ":" + end_time.getMinutes() + ":" + end_time.getSeconds();

            // Check for overlapping events
            let overlappingEvents = createTimetable.getEvents().filter(function(existingEvent) {
                // Skip the current event being dragged
                if (existingEvent.id === event.event.id) return false;
                
                // Check if events are on the same day
                let existingDate = new Date(existingEvent.start);
                if (existingDate.getDay() !== date.getDay()) return false;
                
                // Check for time overlap
                let existingStart = new Date(existingEvent.start);
                let existingEnd = new Date(existingEvent.end);
                let newStart = new Date(event.event.start);
                let newEnd = new Date(event.event.end);
                
                return (newStart < existingEnd && newEnd > existingStart);
            });

            if (overlappingEvents.length > 0) {
                // Only remove the dragged event
                event.event.remove();
                showErrorToast(window.trans["Subject is already scheduled for this time slot."]);
                return;
            }

            let data = new FormData();
            if (subject_teacher_id)
                data.append('subject_teacher_id', subject_teacher_id);

            if (subject_id)
                data.append('subject_id', subject_id);

            data.append('day', days[date.getDay()]);
            data.append('start_time', startTime24Hr);
            data.append('end_time', endTime24Hr);
            data.append('class_section_id', class_section_id);
            data.append('semester_id', semester_id);
            data.append('note', note);
            data.append('day', days[date.getDay()]);
            ajaxRequest('POST', baseUrl + '/timetable', data, null, function (response) {
                event.event.remove();
                createTimetable.addEvent({
                    id: response.data.id,
                    title: event.event.title,
                    start: event.event.start,
                    end: event.event.end,
                    backgroundColor: event.event.backgroundColor,
                    textColor: getContrastColor(event.event.backgroundColor),
                });
            }, function () {
                event.event.remove();
            })
        },
        eventDrop: function (event) {
            // This event will be called when event will be dragged from one duration to another
            let date = new Date(event.event.start);
            let startTime24Hr = date.getHours() + ":" + getMinutes(date.getMinutes()) + ":" + date.getSeconds() + '0';
            let end_time = new Date(event.event.end);
            let endTime24Hr = end_time.getHours() + ":" + getMinutes(end_time.getMinutes()) + ":" + end_time.getSeconds() + '0';
            let timetable_id = event.event.id;

            // Check for overlapping events
            let overlappingEvents = createTimetable.getEvents().filter(function(existingEvent) {
                // Skip the current event being dragged
                if (existingEvent.id === event.event.id) return false;
                
                // Check if events are on the same day
                let existingDate = new Date(existingEvent.start);
                if (existingDate.getDay() !== date.getDay()) return false;
                
                // Check for time overlap
                let existingStart = new Date(existingEvent.start);
                let existingEnd = new Date(existingEvent.end);
                let newStart = new Date(event.event.start);
                let newEnd = new Date(event.event.end);
                
                return (newStart < existingEnd && newEnd > existingStart);
            });

            if (overlappingEvents.length > 0) {
                // Revert the event to its original position
                event.revert();
                showErrorToast(window.trans["Subject is already scheduled for this time slot."]);
                return;
            }

            let data = new FormData();
            data.append('day', days[date.getDay()]);
            data.append('start_time', startTime24Hr);
            data.append('end_time', endTime24Hr);
            data.append('_method', 'PUT');
            ajaxRequest('POST', baseUrl + '/timetable/' + timetable_id, data, null, null, function () {
                showErrorToast(window.trans["The school hours dont match the current time slots Please select a valid time"]);
            })
        },
        eventResize: function (event) {
            let date = new Date(event.event.start);
            let startTime24Hr = date.getHours() + ":" + getMinutes(date.getMinutes()) + ":" + date.getSeconds() + '0';
            let end_time = new Date(event.event.end);
            let endTime24Hr = end_time.getHours() + ":" + getMinutes(end_time.getMinutes()) + ":" + end_time.getSeconds() + '0';
            let timetable_id = event.event.id;

            // Check for overlapping events
            let overlappingEvents = createTimetable.getEvents().filter(function(existingEvent) {
                // Skip the current event being resized
                if (existingEvent.id === event.event.id) return false;
                
                // Check if events are on the same day
                let existingDate = new Date(existingEvent.start);
                if (existingDate.getDay() !== date.getDay()) return false;
                
                // Check for time overlap
                let existingStart = new Date(existingEvent.start);
                let existingEnd = new Date(existingEvent.end);
                let newStart = new Date(event.event.start);
                let newEnd = new Date(event.event.end);
                
                return (newStart < existingEnd && newEnd > existingStart);
            });

            if (overlappingEvents.length > 0) {
                // Revert the event to its original size
                event.revert();
                showErrorToast(window.trans["Subject is already scheduled for this time slot."]);
                return;
            }

            let data = new FormData();
            data.append('day', days[date.getDay()]);
            data.append('start_time', startTime24Hr);
            data.append('end_time', endTime24Hr);
            data.append('_method', 'PUT');
            ajaxRequest('POST', baseUrl + '/timetable/' + timetable_id, data)
        }
    })
    createTimetable.render();

    var teacherTimetable = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        contentHeight: 1500,
        headerToolbar: {
            start: '', // will normally be on the left. if RTL, will be on the right
            center: '',
            end: '',
            // end: 'listDay,listWeek,timeGridWeek' // will normally be on the right. if RTL, will be on the left
        },
        views: {
            listDay: {buttonText: 'Today'},
            listWeek: {buttonText: 'List'},
            timeGridWeek: {buttonText: 'Calendar'}
        },
        slotMinTime: "00:00:00",
        slotMaxTime: "00:00:00",
        firstDay: 1,
        allDaySlot: false,
        expandRows: true,
        slotDuration: "01:00:00",
        snapDuration: "00:10:00",
        dayHeaderFormat: {
            weekday: 'short'
        },
        editable: false,
        droppable: false,
        eventDurationEditable: false,
        eventResizableFromStart: false,
        eventDidMount: function (event) {
            $(event.el).find(".fc-event-main .fc-event-main-frame .fc-event-title-container").append("<span class='mt-3'>" + event.event.extendedProps.class_section + "</span>");
        },
    })
    teacherTimetable.render();
    $(document).on('click', '.remove-timetable', function (e) {
        e.preventDefault();
        let timetable_id = $(this).data('id');
        let event = createTimetable.getEventById(timetable_id)
        showDeletePopupModal(baseUrl + '/timetable/' + timetable_id, {
            successCallBack: function () {
                event.remove();
            }
        })
    })
}
let layout_direction = 'ltl';
if (isRTL()) {
    layout_direction = 'rtl'
} else {
    layout_direction = 'ltl'
}

if (calendarEl !== null) {
    var createTimetable = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        contentHeight: 1500,
        direction: layout_direction,
        headerToolbar: {
            start: '', // will normally be on the left. if RTL, will be on the right
            center: '',
            end: '',
            // end: 'listDay,listWeek,timeGridWeek' // will normally be on the right. if RTL, will be on the left
        },
        views: {
            listDay: {buttonText: 'Today'},
            listWeek: {buttonText: 'List'},
            timeGridWeek: {buttonText: 'Calendar'}
        },
        slotMinTime: "00:00:00",
        slotMaxTime: "00:00:00",
        allDaySlot: false,
        firstDay: 1,
        expandRows: true,
        slotDuration: "01:00:00",
        snapDuration: "00:01:00",
        dayHeaderFormat: {
            weekday: 'short'
        },
        editable: true,
        droppable: true,
        eventDurationEditable: true,
        eventResizableFromStart: true,
        eventDidMount: function (event) {
            $(event.el).find('.fc-event-main .fc-event-main-frame').append("<div class='text-right'><span class='fa fa-times remove-timetable' data-id=" + event.event.id + "></span></div>");
        },
        eventReceive: function (event) {

            let subject_teacher_id = $(event.draggedEl).data('subject_teacher_id');
            let subject_id = $(event.draggedEl).data('subject_id');
            let note = $(event.draggedEl).data('note');
            let class_section_id = $('#class_section_id').val();
            let semester_id = $('#semester_id').val();
            let date = new Date(event.event.start);
            let startTime24Hr = date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();

            let end_time = new Date(event.event.end);
            let endTime24Hr = end_time.getHours() + ":" + end_time.getMinutes() + ":" + end_time.getSeconds();
            let data = new FormData();
            if (subject_teacher_id)
                data.append('subject_teacher_id', subject_teacher_id);

            if (subject_id)
                data.append('subject_id', subject_id);

            data.append('day', days[date.getDay()]);
            data.append('start_time', startTime24Hr);
            data.append('end_time', endTime24Hr);
            data.append('class_section_id', class_section_id);
            data.append('semester_id', semester_id);
            data.append('note', note);
            data.append('day', days[date.getDay()]);
            ajaxRequest('POST', baseUrl + '/timetable', data, null, function (response) {
                event.event.remove();
                createTimetable.addEvent({
                    id: response.data.id,
                    title: event.event.title,
                    start: event.event.start,
                    end: event.event.end,
                    backgroundColor: event.event.backgroundColor,
                    textColor: getContrastColor(event.event.backgroundColor),
                });
            }, function () {
                event.event.remove();
            })
        },
        eventDrop: function (event) {
            // This event will be called when event will be dragged from one duration to another
            let date = new Date(event.event.start);
            // getMinutes
            // let startTime24Hr = date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds()+'0';
            let startTime24Hr = date.getHours() + ":" + getMinutes(date.getMinutes()) + ":" + date.getSeconds() + '0';
            let end_time = new Date(event.event.end);
            let endTime24Hr = end_time.getHours() + ":" + getMinutes(end_time.getMinutes()) + ":" + end_time.getSeconds() + '0';
            let timetable_id = event.event.id;
            let data = new FormData();
            data.append('day', days[date.getDay()]);
            data.append('start_time', startTime24Hr);
            data.append('end_time', endTime24Hr);
            data.append('_method', 'PUT');
            ajaxRequest('POST', baseUrl + '/timetable/' + timetable_id, data, null, null, function () {
                showErrorToast(window.trans["The school hours dont match the current time slots Please select a valid time"]);

            })
        },
        eventResize: function (event) {
            let date = new Date(event.event.start);
            let startTime24Hr = date.getHours() + ":" + getMinutes(date.getMinutes()) + ":" + date.getSeconds() + '0';
            let end_time = new Date(event.event.end);
            let endTime24Hr = end_time.getHours() + ":" + getMinutes(end_time.getMinutes()) + ":" + end_time.getSeconds() + '0';
            let timetable_id = event.event.id;
            let data = new FormData();
            data.append('day', days[date.getDay()]);
            data.append('start_time', startTime24Hr);
            data.append('end_time', endTime24Hr);
            data.append('_method', 'PUT');
            ajaxRequest('POST', baseUrl + '/timetable/' + timetable_id, data)
        }
    })
    createTimetable.render();

    var teacherTimetable = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        contentHeight: 1500,
        headerToolbar: {
            start: '', // will normally be on the left. if RTL, will be on the right
            center: '',
            end: '',
            // end: 'listDay,listWeek,timeGridWeek' // will normally be on the right. if RTL, will be on the left
        },
        views: {
            listDay: {buttonText: 'Today'},
            listWeek: {buttonText: 'List'},
            timeGridWeek: {buttonText: 'Calendar'}
        },
        slotMinTime: "00:00:00",
        slotMaxTime: "00:00:00",
        firstDay: 1,
        allDaySlot: false,
        expandRows: true,
        slotDuration: "01:00:00",
        snapDuration: "00:10:00",
        dayHeaderFormat: {
            weekday: 'short'
        },
        editable: false,
        droppable: false,
        eventDurationEditable: false,
        eventResizableFromStart: false,
        eventDidMount: function (event) {
            $(event.el).find(".fc-event-main .fc-event-main-frame .fc-event-title-container").append("<span class='mt-3'>" + event.event.extendedProps.class_section + "</span>");
        },
    })
    teacherTimetable.render();
    $(document).on('click', '.remove-timetable', function (e) {
        e.preventDefault();
        let timetable_id = $(this).data('id');
        let event = createTimetable.getEventById(timetable_id)
        showDeletePopupModal(baseUrl + '/timetable/' + timetable_id, {
            successCallBack: function () {
                event.remove();
            }
        })
    })
}

$('#class_teacher_id').on("select2:unselecting", function (e) {
    let teacherId = e.params.args.data.id
    let classSectionId = $(this).data('class-section');
    let SelectedOptionElement = $('#class_teacher_id').find('option[value = ' + teacherId + ']');
    let DataDBExists = SelectedOptionElement.data("exists")
    if (DataDBExists) {
        e.preventDefault();
        let url = baseUrl + '/class-section/class-teacher/remove/' + teacherId + '/' + classSectionId;
        showDeletePopupModal(url, {
            text: "Remove Class Teacher!",
            successCallBack: function () {
                SelectedOptionElement.removeAttr("selected");
                SelectedOptionElement.data("exists", false)
                $('#class_teacher_id').trigger("change")
            }
        })
    }
});

$('.subject_teacher_id').on("select2:unselecting", function (e) {
    let $this = $(this)
    let teacherId = e.params.args.data.id;
    let classSectionId = $(this).data('class-section');
    let SelectedOptionElement = $this.find('option[value = ' + teacherId + ']');
    let subjectId = SelectedOptionElement.data("subjectid")
    let DataDBExists = SelectedOptionElement.data("exists")

    if (DataDBExists) {
        e.preventDefault();
        let url = baseUrl + '/class-section/subject-teacher/remove/' + classSectionId + '/' + teacherId + '/' + subjectId;
        showDeletePopupModal(url, {
            text: "Remove Class Teacher!",
            successCallBack: function () {
                SelectedOptionElement.removeAttr("selected");
                SelectedOptionElement.removeAttr("data-exists").attr("data-exists", false);
                SelectedOptionElement.data("exists", false)
                $this.trigger("change")
            }
        })
    }
});
$(document).ready(function () {
    $('#class-section-id').trigger('change')
    $('#exam_id').trigger('change')
    $('#exam-class-section-id').trigger('change');
    $('#exam-id').trigger('change');
    $('#transfer_class_section').trigger('change');
    $('#filter-class-section-id').trigger('change');
    $('#filter_fees_id').trigger('change');
});

$('#class-section-id').on('change', function () {
    let user_id = $('#user_id').val();
    getSubjectOptionsList('#subject-id', $(this), user_id)
});

$('#class-section-id').on('change', function () {
    var selectedOption = $(this).find(':selected');
    var dataId = selectedOption.data('class-id');
    getClassSubjectOptionsList('#class-subject-id', dataId)
});

$('#filter-class-section-id').on('change', function () {
    var selectedOption = $(this).find(':selected');
    var dataId = selectedOption.data('class-id');
    getClassSubjectOptionsList('#filter-class-subject-id', dataId)
});

$('#filter_fees_id').on('change', function () {
    var selectedOption = $(this).find(':selected');
    var dataId = selectedOption.data('class-section-id');
    getFeesClassOptionsList('#filter-class-section-id', dataId)
});

$('#edit-class-section-id').on('change', function () {
    getSubjectOptionsList('#edit-subject-id', $(this))
});

$('#filter-class-section-id').on('change', function () {
    getFilterSubjectOptionsList('#filter-subject-id', $(this))
});

$('#exam-id').on('change', function () {
    getExamSubjectOptionsList('#class_subject_id', $(this), $('#exam-class-section-id').val())
});

$('#filter_session_year_id').on('change', function () {
    // TODO : this code needs to be improved. Instead of this Ajax should be here
    getExamOptionsList('#filter_exam_id', $(this))
});
$('#session_year_id').on('change', function () {
    // TODO : this code needs to be improved. Instead of this Ajax should be here
    getExamOptionsList('#exam_id', $(this))
});

$('#exam_result_session_year_id').on('change', function () {
    getDashboardExamOptionsList('#exam_reuslt_exam_name', $(this))
});

$('#filter-class-id').on('change', function () {
    getExamOptionsListByClass('#filter-exam-id', $(this))
});

$('#filter_exam_id').on('change', function () {
    // TODO : this code needs to be improved. Instead of this Ajax should be here
    getExamClassOptionsList('#filter_class_section_id')
});

const addNewOptionRepeater = $('.options-data').repeater({
    initEmpty: true,
    show: function () {

        // Find the current maximum option number
        let maxOptionNumber = Math.max.apply(Math, $('.option-number').map(function () {
            return $(this).html();
        }).get());
        let newOptionNumber = isNaN(maxOptionNumber) ? 1 : maxOptionNumber + 1;

        $(this).find('.option-number').html(newOptionNumber);
        $(this).find('.option-number').val(newOptionNumber);
        $(this).find('.editor_options').attr('id', 'option-' + newOptionNumber);
        $(this).find('.edit_editor_options').attr('id', 'option-' + newOptionNumber);
        $(this).find('.remove-option').attr('id', 'remove-option-' + newOptionNumber);
        $(this).slideDown();
        let select_answer_option = '<option value="' + newOptionNumber + '" class="answer_option extra_answers_options" data-option="option-' + newOptionNumber + '">' + window.trans["option"] + ' ' + newOptionNumber + '</option>'
        $('#answer_select').append(select_answer_option)
        createCkeditor();
    },
    hide: function (deleteElement) {
        let $this = $(this)
        let id = $(this).find('.remove-option').data('id')
        if (id) {
            let url = baseUrl + '/online-exam-question/remove-option/' + id;
            showDeletePopupModal(url, {
                successCallBack: function () {
                    setTimeout(() => {
                        window.location.reload()
                        // removeOptionWithAnswer($this, deleteElement)
                    }, 100);
                }
            });
        } else {
            removeOptionWithAnswer($this, deleteElement)
        }
    }
});

const compulsoryFeesTypeRepeater = $('.compulsory-fees-types').repeater({
    isFirstItemUndeletable: true,
    initEmpty: true,
    show: function () {
        $(this).slideDown();
        // Check the duplicate Values in Fees Type Select Option
        $(this).find('.fees_type').rules("add", {
            "noDuplicateValues": {
                parentClass: "fees-class-types",
                class: "fees_type",
                value: $(this).find('.fees_type').find("option:selected").text()
            }
        });
        // Change the Form Fields type of text to number who has Attribute data-convert = number
        $(this).find('input[data-convert="number"]').removeAttr('type').attr('type', "number");
        $(this).find('.optional_no').prop('checked', true);
    },
    hide: function (deleteElement) {
        let feesClassTypeID = $(this).find('.fees_class_type_id').val();
        if (feesClassTypeID) {
            let url = baseUrl + '/fees/class-type/' + feesClassTypeID;
            showDeletePopupModal(url, {
                successCallBack: function () {
                    $(this).slideUp(deleteElement);
                }
            });
        } else {
            $(this).slideUp(deleteElement);
        }
    }
});

const optionalFeesTypeRepeater = $('.optional-fees-types').repeater({
    initEmpty: true,
    show: function () {
        $(this).slideDown();
        // Check the duplicate Values in Fees Type Select Option
        $(this).find('.fees_type').rules("add", {
            "noDuplicateValues": {
                parentClass: "fees-class-types",
                class: "fees_type",
                value: $(this).find('.fees_type').find("option:selected").text()
            }
        });
        // Change the Form Fields type of text to number who has Attribute data-convert = number
        $(this).find('input[data-convert="number"]').removeAttr('type').attr('type', "number");
        $(this).find('.optional_no').prop('checked', true);
    },
    hide: function (deleteElement) {
        let feesClassTypeID = $(this).find('.fees_class_type_id').val();
        if (feesClassTypeID) {
            let url = baseUrl + '/fees/class-type/' + feesClassTypeID;
            showDeletePopupModal(url, {
                successCallBack: function () {
                    $(this).slideUp(deleteElement);
                }
            });
        } else {
            $(this).slideUp(deleteElement);
        }
    }
});

const feesInstallmentRepeater = $('.fees-installment-repeater').repeater({
    initEmpty: true,
    show: function () {
        $(this).slideDown();
        $(this).find('.installment-name').rules("add", {
            "noDuplicateValues": {
                class: "installment-name",
                value: $(this).find('.installment-name').text()
            }
        });

        // Change the Form Fields type of text to number who has Attribute data-convert = number
        $(this).find('input[data-convert="number"]').removeAttr('type').attr('type', "number");
    },
    hide: function (deleteElement) {
        let installmentID = $(this).find('.installment_id').val();
        if (installmentID) {
            let url = baseUrl + '/fees/installment/' + installmentID;
            showDeletePopupModal(url, {
                successCallBack: function () {
                    $(this).slideUp(deleteElement);
                }
            });
        } else {
            $(this).slideUp(deleteElement);
        }
        if ($('.fees-installment-repeater [data-repeater-item]').length <= 1) {
            $('#disable-installment').prop('disabled', false);
        }
    }
});

$('.fees-installment-toggle').on('change', function () {
    if ($(this).val() == 1) {
        $('#add-installment').trigger('click');
        $('.fees-installment-repeater').delay(50).show(600)
    } else {
        $('.fees-installment-repeater').hide(200);
        $('.fees-installment-repeater').find('[data-repeater-item]').slice(0).empty();
    }
})

$(document).on('click', '.pay-in-installment', function () {
    if ($(this).is(':checked')) {
        $('#installment-mode').val(1)
        $('.installment_rows').show(200);
        $('#total_amount_text').html(Number(0).toFixed(2));
        $('.without_installment_enter_amount').addClass('d-none');

        $('.installment-checkbox').each(function () {
            if ($(this).hasClass('default-checked-installment')) {
                $(this).prop('checked', true).trigger('change');
                // $(this).bind("click", function () {
                //     return false;
                // });
            }
        })
    } else {
        // 
        $('.without_installment_enter_amount').removeClass('d-none');
        $('.default-checked-installment').prop('checked', false).trigger('change');
        $('.installment_rows').hide(200);
        $('#installment-mode').val(0);
        $('#total_amount_text').html($('#total_compulsory_fees').val());
    }
})

$('.installment-checkbox').on('change', function () {
    let installmentAmount = parseFloat($(this).siblings('.installment-amount').val());
    let dueChargesAmount = parseFloat($(this).siblings('.due-charges-amount').val());
    let installmentWithDueCharges = installmentAmount + dueChargesAmount;
    let totalInstallmentAmount = parseFloat($('#total_installment_amount').val());
    let remainingAmount = parseFloat($('#remaining_amount').val());

    $('.enter_amount').val(0);
    $('#advance').trigger('change');


    if ($(this).is(':checked')) {
        // $('#total_amount_text').html((totalAmount + totalInstallmentAmount).toFixed(2));
        $('#total_installment_amount').val((totalInstallmentAmount + installmentWithDueCharges).toFixed(2)).trigger('change');
        $('#remaining_amount').val((remainingAmount - installmentAmount).toFixed(2)).trigger('change');
        $(this).siblings().prop('disabled', false);
    } else {
        // $('#total_amount_text').html((totalAmount - totalInstallmentAmount).toFixed(2));
        $('#total_installment_amount').val((totalInstallmentAmount - installmentWithDueCharges).toFixed(2)).trigger('change');

        $('#remaining_amount').val((remainingAmount + installmentAmount).toFixed(2)).trigger('change');
        $(this).siblings().prop('disabled', true);
    }
});

$('#remaining_amount').on('change', function () {
    $('#advance').prop('max', parseFloat($(this).val()).toFixed(2));
})

$('#total_installment_amount').on('change', function () {
    let totalInstallmentAmount = parseFloat($(this).val());
    let advance = parseFloat($('#advance').val());
    $('#total_amount_text').html((totalInstallmentAmount + advance).toFixed(2));
})

$('#advance').on('change', function () {
    let totalAmount = parseFloat($('#total_installment_amount').val());
    let advance = parseFloat($(this).val());
    $('#total_amount_text').text((totalAmount + advance).toFixed(2));
})

// $('#exam-class-section-id').on('change', function () {
//     // Get Class ID form the Data Attribute of Class Selected
//     let classId = $(this).find('option[value="' + $(this).val() + '"]').data('classid');

//     // Add Exams Options According to Class ID
//     $('#exam-id').val("").removeAttr('disabled').show();
//     $('#exam-id').find('option').hide();
//     if ($('#exam-id').find('option[data-classId="' + classId + '"]').length) {
//         $('#exam-id').find('option[data-classId="' + classId + '"]').show();
//     } else {
//         $('#exam-id').val("data-not-found").attr('disabled', true).show();
//     }
// })

// Timetable set text color depend in subject div color
$(document).ready(function () {
    // Sidebar #Subject
    setTimeout(() => {
        // fc-div-color
        $(".fc-div-color").each(function () {
            // Access each element using $(this)
            let div_color = $(this).css("background-color");
            // Convert color rgb to hex

            let textColor = getContrastColor(div_color);

            $(this).find('.fc-event-main').css('color', textColor);
        });

        // fc-event-time
    }, 1000);

    // Calendar data
    setTimeout(() => {
        $('.fc-event-start').each(function () {
            // element == this
            let div_color = $(this).css('background-color');
            var textColor = getContrastColor(div_color);
            $(this).find("*").css('color', textColor);

        });
    }, 1000);

});


// End timetable color

$('#subject-id').on('change', function () {
    let selectedSubjectId = $(this).val();
    let selectedClassSectionId = $(this).find('option:selected').data('class-section');

    // Reset and hide lesson dropdown
    $("#topic-lesson-id").val("").attr('disabled', true).hide();
    $("#topic-lesson-id").find('option').hide();

    // Show default options
    $("#topic-lesson-id option[value='']").show();
    $("#topic-lesson-id option[value='data-not-found']").hide();

    let lessonOptionsFound = false;

    // Filter lessons based on selected subject
    $("#topic-lesson-id option").each(function () {
        let lessonSubjectId = $(this).data('subject');
        
        if (selectedSubjectId == lessonSubjectId) {
            $(this).show();
            lessonOptionsFound = true;
        } else {
            $(this).hide();
        }
    });

    // Show appropriate message based on whether lessons were found
    if (!lessonOptionsFound) {
        $("#topic-lesson-id").val("data-not-found").attr('disabled', true).show();
    } else {
        $("#topic-lesson-id").removeAttr('disabled').show();
    }
});


$(document).on('click', '.remove-optional-fees-paid', function (e) {
    e.preventDefault();
    let optionalPaidId = $(this).data('id');
    if (optionalPaidId) {
        let url = baseUrl + '/fees/paid/remove-optional-fee/' + optionalPaidId;
        showDeletePopupModal(url, {
            successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
                setTimeout(() => {
                    $('#optionalModal').modal('hide');
                }, 500);
            }
        });
    }
});
$(document).on('click', '.remove-installment-fees-paid', function (e) {
    e.preventDefault();
    let installmentPaidId = $(this).data('id');
    if (installmentPaidId) {
        let url = baseUrl + '/fees/paid/remove-installment-fees/' + installmentPaidId;
        showDeletePopupModal(url, {
            successCallBack: function () {
                window.location.reload();
            }
        });
    }
});

$('.include_semesters').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).val(1)
    } else {
        $(this).val(0)
    }
})

$(document).on('change', '.semesters', function () {
    let semester = $(this);
    let subjects = $(this).parents('.semester-div').next('div').find('.subject');

    subjects.each(function (index, subject) {
        $(subject).attr('data-group', $(semester).val());
    })
})

$("#stream_id").on("select2:selecting", function (e) {
    setTimeout(function () {
        if ($("#stream_id").val().length > 0) {
            $('#default-section-div').hide();
            if ($('#stream-wise-section-div:visible').length === 0) {
                $('#stream-wise-section-div').show();
            }
        }
    }, 1);

    let id = e.params.args.data.text;
    id = id.replace(/\s+/g, "-").trim();
    setTimeout(function () {
        $("#" + $.escapeSelector(id) + "-section-div").slideDown(500);
    }, 300)

});

$('#stream_id').on("select2:unselecting", function (e) {
    setTimeout(function () {
        if ($("#stream_id").val().length == 0) {
            $('#default-section-div').show();
            $('#stream-wise-section-div').hide();
        }
        $(e.target).select2('close');
    }, 1);

    let id = e.params.args.data.text;
    id = id.replace(/\s+/g, "-").trim();
    setTimeout(function () {
        $("#" + $.escapeSelector(id) + "-section-div").slideUp(500);
    }, 300)
});

// $('#stream_id').on('change', function (e) {
//     if ($('#stream_id').val().length == 0) {
//         $('#default-section-div').show();
//         $('#stream-wise-section-div').hide();
//     }
// })

$('#roll-number-order').on('change', function () {
    let value = $(this).val().split(',');
    $('#roll-number-sort-column').val(value[0]);
    $('#roll-number-sort-order').val(value[1]);
})

$(document).ready(function () {
    let sortColumn = $('#roll-number-sort-column').val();
    let sortOrder = $('#roll-number-sort-order').val();
    if (sortColumn && sortOrder) {
        let selectValue = sortColumn + ',' + sortOrder;
        $('#roll-number-order').val(selectValue).trigger('change');
    }
});

$('#change-roll-ckh-settings').on('click', function () {
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["Change Roll Number for All Classes"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            $(this).prop("checked", true);
        } else {
            $(this).prop("checked", false);
        }
    })
})
$('.delete-related-data').on('click', function (e) {
    e.preventDefault();
    let url = baseUrl + "/related-data/delete/" + $(this).data('table') + "/" + $(this).data('id');

    showDeletePopupModal(url, {
        text: "After deleting this, It won't be possible to recover this data",
    });
})


$("#select-all").on('click', function () {
    let dropdown = $(this).parent().parent().siblings('select');
    if ($(this).is(':checked')) {
        $(dropdown).find("option").prop("selected", "selected");
        $(dropdown).trigger("change");
    } else {
        $(dropdown).find("option").removeAttr("selected");
        $(dropdown).val('').trigger("change");
    }
});


$('#to_date,#from_date').change(function (e) {
    e.preventDefault();
    let from_date = $('#from_date').val().split("-").reverse().join("-");
    let to_date = $('#to_date').val().split("-").reverse().join("-");
    let div = '.leave_dates';
    let to_date_null = '#to_date';
    let disabled = '';
    let holiday_days = $('.holiday_days').val();
    // public_holiday
    let public_holiday = $('.public_holiday').val();
    if (holiday_days) {
        holiday_days = holiday_days.split(',');
    } else {
        holiday_days = [];
    }
    let html = date_list(from_date, to_date, div, to_date_null, disabled, holiday_days, public_holiday);

    $('.leave_dates').html(html);
});

$('#edit_to_date,#edit_from_date').change(function (e) {
    e.preventDefault();
    let from_date = $('#edit_from_date').val().split("-").reverse().join("-");
    let to_date = $('#edit_to_date').val().split("-").reverse().join("-");
    let div = '.edit_leave_dates';
    let to_date_null = '#edit_to_date';
    let disabled = 'disabled';
    let holiday_days = $('.holiday_days').val();
    let public_holiday = $('.public_holiday').val();
    if (holiday_days) {
        holiday_days = holiday_days.split(',');
    } else {
        holiday_days = [];
    }
    let html = date_list(from_date, to_date, div, to_date_null, disabled, holiday_days, public_holiday);

    $('.edit_leave_dates').html(html);
});

function date_list(from_date, to_date, div, to_date_null, disabled, holiday_days, public_holiday) {
    if (from_date && to_date) {
        from_date = new Date(from_date);
        to_date = new Date(to_date);
        var days = [window.trans["Sunday"], window.trans["Monday"], window.trans["Tuesday"], window.trans["Wednesday"], window.trans["Thursday"], window.trans["Friday"], window.trans["Saturday"]];
        if (from_date > to_date) {
            $(to_date_null).val('');
        }

        if (public_holiday) {
            public_holiday = public_holiday.split(',');
        }

        let html = '';
        $(div).slideDown(500);
        while (from_date <= to_date) {
            let date = moment(from_date, 'YYYY-MM-DD').format('DD-MM-YYYY');
            let day = days[from_date.getDay()];
            if (!holiday_days.includes(day) && !public_holiday.includes(date)) {
                html += '<div class="form-group col-sm-12 col-md-12">';
                html += '<label class="mr-2">' + date + '</label>-';
                html += '<label class="ml-2">' + day + '</label>';
                html += '<div class="form-group row col-sm-12 col-md-12"> <div class="form-check mr-3"> <label class="form-check-label"> <input type="radio" class="form-check-input" name="type[' + date + '][]" id="optionsRadios1" checked="" ' + disabled + ' value="Full"> ' + window.trans['full'] + ' <i class="input-helper"></i></label> </div> <div class="form-check mr-3"> <label class="form-check-label"> <input type="radio" class="form-check-input" name="type[' + date + '][]" id="optionsRadios2" ' + disabled + ' value="First Half"> ' + window.trans['first_half'] + ' <i class="input-helper"></i></label> </div> <div class="form-check mr-3"> <label class="form-check-label"> <input type="radio" class="form-check-input" name="type[' + date + '][]" id="optionsRadios3" ' + disabled + ' value="Second Half">' + window.trans['second_half'] + ' <i class="input-helper"></i></label> </div> </div>';
                html += '</div>';
            }
            from_date.setDate(from_date.getDate() + 1);
        }
        return html;
    }
}


$('#send_verification_email').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);
    formAjaxRequest('POST', url, data, formElement, submitButtonElement, function () {
        $('#error-div').hide();
    }, function (response) {
        $('#error-div').show();
        $('#error').text(response.data.error);
        $('#stacktrace').text(response.data.stacktrace);
    });

})

$(document).on('input', '.amount', function () {
    $('#due_charges_percentage').trigger('input');
})

$('#due_charges_percentage').on('input', function () {
    let compulsoryFeesAmounts = $('.compulsory-fees-types').find('.amount');

    let totalCompulsoryFee = 0;

    compulsoryFeesAmounts.each(function (value, element) {
        totalCompulsoryFee += parseFloat($(element).val());
    })

    let dueAmount = totalCompulsoryFee * $("#due_charges_percentage").val() / 100;
    $('#due_charges_amount').val(dueAmount);
})

$('#due_charges_amount').on('input', function () {
    let compulsoryFeesAmounts = $('.compulsory-fees-types').find('.amount');

    let totalCompulsoryFee = 0;

    compulsoryFeesAmounts.each(function (value, element) {
        totalCompulsoryFee += parseFloat($(element).val());
    })

    let duePercentage = ($("#due_charges_amount").val() * 100) / totalCompulsoryFee;
    $('#due_charges_percentage').val(duePercentage);
})

$('#tags').tagsInput({
    'width': '100%',
    'height': '75%',
    'interactive': true,
    'defaultText': window.trans['Add More'],
    'removeWithBackspace': true,
    'minChars': 0,
    // 'maxChars': 20, // if not provided there is no limit
    'placeholderColor': '#666666'
});

$('.filter_birthday').change(function (e) { 
    e.preventDefault();
    let type = $(this).val();
    $.ajax({
        type: "get",
        url: baseUrl + '/users/birthday/'+ type,
        success: function (response) {
            let html = '';
            if (response.data.length) {
                $.each(response.data, function (index, value) { 
                    html += '<tr> <td> <img src="'+value.image+'" onerror="onErrorImage(event)" class="me-2" alt="image"> </td> <td>'+value.full_name+' </td> <td class="text-right">'+value.dob_date+'</td> </tr>';
                });
            } else {
                html += '<tr> <td colspan="2" class="text-center"> '+window.trans['no_data_found']+' </td> </tr>';
            }
            setTimeout(() => {
                $('.birthday-list').html(html);
            }, 500);
        }
    });
});

$('.filter_leaves').change(function (e) {
    e.preventDefault();
    let filter_leave = $(this).val();
    let url = baseUrl + '/leave/filter';
    let data = {
        'filter_leave': filter_leave,
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length > 0) {
            $.each(response.data, function (index, value) {
                if (value.type == "Full") {
                    html += '<tr> <td>'+value.leave.user.full_name+'<span class="m-2 text-white text-small leave-type leave-full">'+value.type+' Day</span> </td> <td class="text-right">'+value.leave_date+'</td> </tr>';
                }
                if (value.type == "First Half") {
                    html += '<tr> <td>'+value.leave.user.full_name+'<span class="m-2 text-white text-small leave-type leave-half">'+value.type+'</span> </td> <td class="text-right">'+value.leave_date+'</td> </tr>';
                }
                if (value.type == "Second Half") {
                    html += '<tr> <td>'+value.leave.user.full_name+'<span class="m-2 text-white text-small leave-type leave-half">'+value.type+'</span> </td> <td class="text-right">'+value.leave_date+'</td> </tr>';
                }
                
            });
        } else {
            // 
            html += '<tr> <td colspan="2" class="text-center"> '+window.trans['All are presents']+' </td> </tr>';
        }
        $('.leave-list').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
});

$('#filter_expense_session_year_id').change(function(e) {
    e.preventDefault();
    let session_year_id = $(this).val();
    $.ajax({
        type: "get",
        url: baseUrl + '/expense/filter/' + session_year_id,
        success: function(response) {
            if (response.data) {
                setTimeout(() => {
                    expense_graph(response.data.expense_months, response.data.expense_amount);
                }, 1000);
            }
        }
    });
});

$('#exam_result_session_year_id,#exam_reuslt_exam_name').on('change', function (e) {
    e.preventDefault();
    let exam_name = $('#exam_reuslt_exam_name').val();
    let session_year_id = $('#exam_result_session_year_id').val();
    if (exam_name && session_year_id) {
        $.ajax({
            type: "get",
            url: baseUrl + '/exams/result-report/'+session_year_id+'/'+exam_name,
            success: function (response) {                
                let html = '';
                if (response.data.length) {
                    let bg_colors = ['bg-success','bg-info','bg-primary','bg-warning','bg-danger'];
                    $.each(response.data, function (index, value) { 
                         let total_students = parseInt(value.total_students);
                         let total_pass = parseInt(value.pass_students);
                         let per = (total_pass*100) / total_students;
                         per = per.toFixed(2);
                         html += '<div class="d-flex justify-content-between mt-3"> <small class="font-weight-bold">'+window.trans['Class']+': '+value.class_name+'</small> <small class="font-weight-bold">'+per+'%</small> </div> <div class="progress progress-lg mt-2"> <div class="progress-bar '+bg_colors[index]+'" role="progressbar" style="width: '+per+'%" aria-valuenow="'+per+'" aria-valuemin="0" aria-valuemax="100"></div> </div>';

                    });
                } else {
                    html += '<div class="text-center"> <span class="text-small"> '+window.trans['no_exam_result_found']+' </span> </div>';
                }
                $('#class-progress-report').html(html);
            }
        });    
    } else {
        $('#class-progress-report').html('<div class="text-center"> <span class="text-small"> '+window.trans['no_exam_result_found']+' </span> </div>');
    }
})

$('.class-section-attendance').change(function (e) { 
    e.preventDefault();
    let class_id = $(this).val();

    $.ajax({
        type: "get",
        url: baseUrl + '/class/attendance/' + class_id,
        success: function(response) {
            if (response.data) {
                setTimeout(() => {
                    class_attendance(response.data.section, response.data.data);
                }, 1000);
            } else {
                setTimeout(() => {
                    class_attendance(['A','B','C','D','E'], []);
                }, 1000);
            }
        }
    });
});


$('.year-filter').change(function (e) { 
    e.preventDefault();
    let year = $(this).val();

    $.ajax({
        type: "get",
        url: baseUrl + '/subscriptions/transaction/' + year,
        success: function(response) {

            if (response.data) {
                setTimeout(() => {
                    subscription_transaction(Object.keys(response.data), Object.values(response.data));
                }, 1000);    
            } else {
                setTimeout(() => {
                    subscription_transaction(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], []);
                }, 1000); 
            }
        }
    });
});

$('.page_layout').change(function (e) { 
    e.preventDefault();
    let layout = $(this).val();
    if (layout == 'A4 Landscape') {
        $('.height').val(210);
        $('.width').val(297);
    } else if(layout == 'A4 Portrait') {
        $('.height').val(297);
        $('.width').val(210);

    } else {
        // $('.height').val('');
        // $('.width').val('');
    }
});

$('.certificate_type').change(function (e) { 
    e.preventDefault();
    var type = $('input[name="type"]:checked').val();
    if (type == 'Student') {
        $('#staff_tags').hide(500);
        $('#student_tags').show(500);
    } else {
        $('#staff_tags').show(500);
        $('#student_tags').hide(500);
    }
});


$('.btn_tag').click(function (e) { 
    e.preventDefault();
    var value = $(this).data('value');
    if (tinymce.activeEditor) { // Check if editor is active
        tinymce.activeEditor.insertContent(value);
    } else {
        alert('TinyMCE editor not active');
    }
});

$('#razorpay_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('#stripe_status').val(0);
    }
});
$('#stripe_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('#razorpay_status').val(0);
    }
});

$('.fees-over-due-class').change(function (e) { 
    e.preventDefault();
    let class_section_id = $(this).val();
   
    $.ajax({
        type: "get",
        url: baseUrl + '/fees/fees-over-due/' + class_section_id,
        success: function(response) {
            let html = '';
            if (response.data.length) {
                $.each(response.data, function (index, value) { 
                    html += '<tr> <td> <img src="'+ value.user.image +'"/></td><td>' + value.full_name + '</td> <td> <input type="checkbox" name="studentids[]" data-id="' + value.user.id + '"> </td> </tr>';
                });
                $('.fees-overdue-btn').removeClass('d-none');
            } else {
                html += '<tr> <td colspan="2" class="text-center"> '+ window.trans['no_data_found'] +' </td> </tr>';
                $('.fees-overdue-btn').addClass('d-none');
            }
            setTimeout(() => {
                $('.fees-over-due-list').html(html);
            }, 500);
        }
    });
});

$('#fees-overdue-form').on('submit', function(e) {
    // Collect checked checkbox IDs
    var checkedIds = [];
    $('input[type="checkbox"]:checked').each(function() {
        checkedIds.push($(this).data('id'));
    });

    // Add the checked IDs to a hidden input field
    $('<input>').attr({
        type: 'hidden',
        name: 'checked_ids',
        value: checkedIds.join(',')
    }).appendTo('#fees-overdue-form');

    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = formElement.find(':submit');
    let url = formElement.attr('action');
    let data = new FormData(this);


    function successCallback() {
        setTimeout(function () {
            window.location.reload();
        }, 2000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
            
});

$(document).ready(function() {
    $('.domain-pattern').on('input', function() {
        // Replace spaces with dashes
        var inputVal = $(this).val().replace(/ /g, '-');
        // Allow only letters, numbers, and dashes
        inputVal = inputVal.replace(/[^a-zA-Z0-9-.]/g, '');
        $(this).val(inputVal);
    });
});


$('#edit_student_class_id').on('change', function () {

    let class_id = $(this).val();
    let url = baseUrl + '/students/get-class-section-by-class/' + class_id;
  
    $('#edit_student_class_section_id option').hide();

    function successCallback(response) {
        let html = ''
        html = '<option value="">Select Class Section</option>';
        if (response.data) {
            // html = '<option value="">Select Exam</option>';
            $.each(response.data, function (key, data) {
                html += '<option value=' + data.id + '>' + data.full_name +  '</option>';
            });
        } else {
            html = '<option>No Class Section Found</option>';
        }
        $('#edit_student_class_section_id').html(html);
    }

    ajaxRequest('GET', url, null, null, successCallback, null);
});

$('#filter_class_id').on('change', function () {

    let class_id = $(this).val();
    console.log(class_id);

    // If class_id is empty, reset the class_section_id filter
    if (!class_id) {
        $('#filter_class_section_id').html('<option value="">Select Class Section</option>');
        return; // Exit early, no need to send AJAX request
    }

    let url = baseUrl + '/students/get-class-section-by-class/' + class_id;
  
    $('#filter_class_section_id option').hide();

    function successCallback(response) {
        let html = ''
        html = '<option value="">Select Class Section</option>';
        if (response.data) {
            $.each(response.data, function (key, data) {
                html += '<option value=' + data.id + '>' + data.full_name +  '</option>';
            });
        } else {
            html = '<option>No Class Section Found</option>';
        }
        $('#filter_class_section_id').html(html);
    }

    ajaxRequest('GET', url, null, null, successCallback, null);
});


$('.edit_default').on('change', function(){

    $('.defaultDomain').show().find('input').prop('disabled', false);
    $('.customDomain').hide().find('input').prop('disabled', true);

});

$('.edit_custom').on('change', function(){

    $('.customDomain').show().find('input').prop('disabled', false);
    $('.defaultDomain').hide().find('input').prop('disabled', true);
    
});

$('#class_section_id').on('change', function () {

    let class_section_id = $(this).val();
   
    let url = baseUrl + '/exams/get-exams/' + class_section_id;
    $('#exam_id option').hide();
    $('#subject_id option').hide();

    function successCallback(response) {
        let html = ''
        if (response.data) {
            html = '<option value="">Select Exam</option>';
            $.each(response.data, function (key, data) {
                html += '<option value=' + data.id + '>' + data.name +  '</option>';
            });
        } else {
            html += '<option>No Exams Found</option>';
        }
        $('#exam_id').html(html);
    }

    ajaxRequest('GET', url, null, null, successCallback, null);
});

$('#exam_id').on('change', function () {

    let class_section_id = $('#class_section_id').val();
    let exam_id = $(this).val();

    let url = baseUrl + '/exams/get-subjects/' + exam_id + '?class_section_id=' + class_section_id; 

    $('#subject_id option').hide();

    function successCallback(response) {
        let html = ''
        html = '<option>No Subjects</option>';
        if (response.data) {
            html = '<option value="">Select Subject</option>';
            $.each(response.data, function (key, data) {
                html += '<option value=' + data.class_subject_id + '>' + data.subject_with_name + '</option>';
            });
        } else {
            html = '<option>No Subjects Found</option>';
        }
        $('#subject_id').html(html);
    }

    ajaxRequest('GET', url, null, null, successCallback, null);
});

$('#subject_id').on('change', function(){
    let subject_id = $('#subject_id').val();

    if(!subject_id)
    {
        $('#downloadDummyFile').hide();
    }else{
        $('#downloadDummyFile').show();
    }
   
});

$('#change-order-school-form-field').click(async function () {
    const ids = await $('#table_list').bootstrapTable('getData').map(function (row) {
        return row.id;
    });
    $.ajax({
        type: "post",
        url: baseUrl + "/school-custom-fields/update-rank",
        data: {
            ids: ids
        },
        dataType: "json",
        success: function (data) {
            $('#table_list').bootstrapTable('refresh');
            if (!data.error) {
                showSuccessToast(data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showErrorToast(data.message);
            }
        }
    });
});

// Payment gateway toggle interactions
$(document).ready(function() {
    // Stripe toggle
    $('#Stripe').on('change', function() {
        if ($(this).is(':checked')) {
            $('#StripeForm').removeClass('d-none');
            
            // Uncheck other toggles
            $('#Paystack').prop('checked', false);
            $('#PaystackForm').addClass('d-none');
            $('#Razorpay').prop('checked', false);
            $('#RazorpayForm').addClass('d-none');
            $('#Flutterwave').prop('checked', false);
            $('#FlutterwaveForm').addClass('d-none');
            
            // For legacy dropdowns
            $('#razorpay_status').val(0);
            $('#paystack_status').val(0);
            $('#flutterwave_status').val(0);
            $('#bank_transfer_status').val(0);
        } else {
            $('#StripeForm').addClass('d-none');
        }
    });
    
    // Razorpay toggle
    $('#Razorpay').on('change', function() {
        if ($(this).is(':checked')) {
            $('#RazorpayForm').removeClass('d-none');
            
            // Uncheck other toggles
            $('#Stripe').prop('checked', false);
            $('#StripeForm').addClass('d-none');
            $('#Paystack').prop('checked', false);
            $('#PaystackForm').addClass('d-none');
            $('#Flutterwave').prop('checked', false);
            $('#FlutterwaveForm').addClass('d-none');
            
            // For legacy dropdowns
            $('#stripe_status').val(0);
            $('#paystack_status').val(0);
            $('#flutterwave_status').val(0);
            $('#bank_transfer_status').val(0);
        } else {
            $('#RazorpayForm').addClass('d-none');
        }
    });
    
    // Paystack toggle
    $('#Paystack').on('change', function() {
        if ($(this).is(':checked')) {
            $('#PaystackForm').removeClass('d-none');
            
            // Uncheck other toggles
            $('#Stripe').prop('checked', false);
            $('#StripeForm').addClass('d-none');
            $('#Razorpay').prop('checked', false);
            $('#RazorpayForm').addClass('d-none');
            $('#Flutterwave').prop('checked', false);
            $('#FlutterwaveForm').addClass('d-none');
            
            // For legacy dropdowns
            $('#stripe_status').val(0);
            $('#razorpay_status').val(0);
            $('#flutterwave_status').val(0);
            $('#bank_transfer_status').val(0);
        } else {
            $('#PaystackForm').addClass('d-none');
        }
    });
    
    // Flutterwave toggle
    $('#Flutterwave').on('change', function() {
        if ($(this).is(':checked')) {
            $('#FlutterwaveForm').removeClass('d-none');
            
            // Uncheck other toggles
            $('#Stripe').prop('checked', false);
            $('#StripeForm').addClass('d-none');
            $('#Razorpay').prop('checked', false);
            $('#RazorpayForm').addClass('d-none');
            $('#Paystack').prop('checked', false);
            $('#PaystackForm').addClass('d-none');
            
            // For legacy dropdowns
            $('#stripe_status').val(0);
            $('#razorpay_status').val(0);
            $('#paystack_status').val(0);
            $('#bank_transfer_status').val(0);
        } else {
            $('#FlutterwaveForm').addClass('d-none');
        }
    });
});

