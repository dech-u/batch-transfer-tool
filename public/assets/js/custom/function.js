"use strict";

var toast_position = 'top-right';
function isRTL() {
    var dir = $('html').attr('dir');
    if (dir === 'rtl') {
        return true;
    } else {
        return false;
    }
    return false;
    return dir === 'rtl';
}
if (isRTL()) {
    toast_position = 'top-left';
} else {
    toast_position = 'top-right';
}

function showErrorToast(message) {
    $.toast({
        text: message,
        showHideTransition: 'slide',
        icon: 'error',
        loaderBg: '#f2a654',
        position: toast_position,
        hideAfter: 5000
    });
}

function showSuccessToast(message) {
    $.toast({
        text: message,
        showHideTransition: 'slide',
        icon: 'success',
        loaderBg: '#f96868',
        position: toast_position,
        // hideAfter: 1000

    });
}

function showWarningToast(message) {
    $.toast({
        text: message,
        showHideTransition: 'slide',
        icon: 'warning',
        loaderBg: '#f96868',
        position: toast_position,
        // hideAfter: 1000

    });
}

/**
 *
 * @param type
 * @param url
 * @param data
 * @param {function} beforeSendCallback
 * @param {function} successCallback - This function will be executed if no Error will occur
 * @param {function} errorCallback - This function will be executed if some error will occur
 * @param {function} finalCallback - This function will be executed after all the functions are executed
 * @param processData
 */
function ajaxRequest(type, url, data, beforeSendCallback = null, successCallback = null, errorCallback = null, finalCallback = null, processData = false) {
    $.ajax({
        type: type,
        url: url,
        data: data,
        cache: false,
        processData: processData,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            if (beforeSendCallback != null) {
                beforeSendCallback();
            }
        },
        success: function (data) {
            if (!data.error) {
                if (successCallback != null) {
                    successCallback(data);
                }
            } else {
                if (errorCallback != null) {
                    errorCallback(data);
                }
            }

            if (finalCallback != null) {
                finalCallback(data);
            }
        }, error: function (jqXHR) {
            if (jqXHR.responseJSON) {
                showErrorToast(jqXHR.responseJSON.message);
            }
            if (finalCallback != null) {
                finalCallback();
            }
        }
    })
}

function formAjaxRequest(type, url, data, formElement, submitButtonElement, successCallback = null, errorCallback = null) {
    // To Remove Red Border from the Validation tag.
    formElement.find('.has-danger').removeClass("has-danger");
    formElement.validate();
    if (formElement.valid()) {
        let submitButtonText = submitButtonElement.val();

        function beforeSendCallback() {
            submitButtonElement.val('Please Wait...').attr('disabled', true);
        }

        function mainSuccessCallback(response) {
            if (response.warning) {
                showWarningToast(response.message);
            } else {
                showSuccessToast(response.message);
            }

            if (successCallback != null) {
                successCallback(response);
            }
        }

        function mainErrorCallback(response) {
            showErrorToast(response.message);
            closeLoading();
            if (errorCallback != null) {
                errorCallback(response);
            }
        }

        function finalCallback() {
            submitButtonElement.val(submitButtonText).attr('disabled', false);
        }

        ajaxRequest(type, url, data, beforeSendCallback, mainSuccessCallback, mainErrorCallback, finalCallback)
    }
}

function createCkeditor() {
    for (let equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].destroy();
    }
    CKEDITOR.replaceAll(function (textarea, config) {
        if (textarea.className == "editor_question") {
            config.mathJaxLib = '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML';
            config.extraPlugins = 'mathjax';
            config.height = 200;
            config.contentsLangDirection = layout_direction;
            return true;
        }
        if (textarea.className == "editor_options") {
            config.mathJaxLib = '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML';
            config.extraPlugins = 'mathjax';
            config.height = 100;
            config.contentsLangDirection = layout_direction;
            return true;
        }
        if (textarea.className == "edit_editor_options") {
            config.mathJaxLib = '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML';
            config.extraPlugins = 'mathjax';
            config.height = 100;
            config.contentsLangDirection = layout_direction;
            return true;
        }
        return false;
    });

    // inline editors
    let elements = CKEDITOR.document.find('.equation-editor-inline'), i = 0, element;
    while ((element = elements.getItem(i++))) {
        CKEDITOR.inline(element, {
            mathJaxLib: '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML',
            extraPlugins: 'mathjax',
            readOnly: true,
        });
    }
}

function Select2SearchDesignTemplate(repo) {
    /**
     * This function is used in Select2 Searching Functionality
     */
    if (repo.loading) {
        return repo.text;
    }
    let $container;
    if (repo.id && repo.text) {
        $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__title'></div>" +
            "</div>"
        );
        $container.find(".select2-result-repository__title").text(repo.text);
    } else {
        $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='row'>" +
            "<div class='col-1 select2-result-repository__avatar' style='width:20px'>" +
            "<img src='" + repo.image + "' class='w-100' alt=''/>" +
            "</div>" +
            "<div class='col-10'>" +
            "<div class='select2-result-repository__title'></div>" +
            "<div class='select2-result-repository__description'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.first_name + " " + repo.last_name);
        $container.find(".select2-result-repository__description").text(repo.email);
    }

    return $container;
}

/**
 *
 * @param searchElement
 * @param searchUrl
 * @param {Object|null} data
 * @param {number} data.total_count
 * @param {string} data.email
 * @param {number} data.page
 * @param placeHolder
 * @param templateDesignEvent
 * @param onTemplateSelectEvent
 */
function select2Search(searchElement, searchUrl, data, placeHolder, templateDesignEvent, onTemplateSelectEvent) {
    //Select2 Ajax Searching Functionality function
    if (!data) {
        data = {};
    }
    $(searchElement).select2({
        tags: true,
        ajax: {
            url: searchUrl,
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
                data.email = params.term;
                data.page = params.page;
                return data;
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            }
        },
        placeholder: placeHolder,
        minimumInputLength: 1,
        templateResult: templateDesignEvent,
        templateSelection: onTemplateSelectEvent,
    });
}

/**
 * @param {string} [url] - Ajax URL that will be called when the Confirm button will be clicked
 * @param {string} [method] - GET / POST / PUT / PATCH / DELETE
 * @param {Object} [options] - Options to Configure SweetAlert
 * @param {string} [options.title] - Are you sure
 * @param {string} [options.text] - You won't be able to revert this
 * @param {string} [options.icon] - 'warning'
 * @param {boolean} [options.showCancelButton] - true
 * @param {string} [options.confirmButtonColor] - '#3085d6'
 * @param {string} [options.cancelButtonColor] - '#d33'
 * @param {string} [options.confirmButtonText] - Confirm
 * @param {string} [options.cancelButtonText] - Cancel
 * @param {function} [options.successCallBack] - function()
 * @param {function} [options.errorCallBack] - function()
 */
function showSweetAlertConfirmPopup(url, method, options = {}) {
    let opt = {
        title: window.trans["Are you sure"],
        text: window.trans["You wont be able to revert this"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Confirm"],
        cancelButtonText: window.trans["Cancel"],
        successCallBack: function () {
        },
        errorCallBack: function (response) {
        },
        ...options,
    }

    Swal.fire({
        title: opt.title,
        text: opt.text,
        icon: opt.icon,
        showCancelButton: opt.showCancelButton,
        confirmButtonColor: opt.showCancelButton,
        cancelButtonColor: opt.cancelButtonColor,
        confirmButtonText: opt.confirmButtonText,
        cancelButtonText: opt.cancelButtonText
    }).then((result) => {
        if (result.isConfirmed) {
            function successCallback(response) {
                showSuccessToast(response.message);
                opt.successCallBack(response);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
                opt.errorCallBack(response);
            }

            ajaxRequest(method, url, null, null, successCallback, errorCallback);
        }
    })
}

/**
 *
 * @param {string} [url] - Ajax URL that will be called when the Delete will be successfully
 * @param {Object} [options] - Options to Configure SweetAlert
 * @param {string} [options.text] - "Are you sure?"
 * @param {string} [options.title] - "You won't be able to revert this!"
 * @param {string} [options.icon] - "warning"
 * @param {boolean} [options.showCancelButton] - true
 * @param {string} [options.confirmButtonColor] - "#3085d6"
 * @param {string} [options.cancelButtonColor] - "#d33"
 * @param {string} [options.confirmButtonText] - "Yes, delete it!"
 * @param {string} [options.cancelButtonText] - "Cancel"
 * @param {function} [options.successCallBack] - function()
 * @param {function} [options.errorCallBack] - function()
 */
function showDeletePopupModal(url, options = {}) {

    // To Preserve OLD
    let opt = {
        title: window.trans["Are you sure"],
        text: window.trans["You wont be able to revert this"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["yes_delete"],
        cancelButtonText: window.trans['Cancel'],
        successCallBack: function () {
        },
        errorCallBack: function (response) {
        },
        ...options,
    }
    showSweetAlertConfirmPopup(url, 'DELETE', opt);
}


/**
 *
 * @param {string} [url] - Ajax URL that will be called when the Delete will be successfully
 * @param {Object} [options] - Options to Configure SweetAlert
 * @param {string} [options.text] - "Are you sure?"
 * @param {string} [options.title] - "You won't be able to revert this!"
 * @param {string} [options.icon] - "warning"
 * @param {boolean} [options.showCancelButton] - true
 * @param {string} [options.confirmButtonColor] - "#3085d6"
 * @param {string} [options.cancelButtonColor] - "#d33"
 * @param {string} [options.confirmButtonText] - "Yes, delete it!"
 * @param {string} [options.cancelButtonText] - "Cancel"
 * @param {function} [options.successCallBack]
 * @param {function} [options.errorCallBack]
 */
function showRestorePopupModal(url, options = {}) {

    // To Preserve OLD
    let opt = {
        title: window.trans["Are you sure"],
        text: window.trans["You wont be able to revert this"],
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans['Yes Restore it'],
        cancelButtonText: window.trans['Cancel'],
        successCallBack: function () {
        },
        errorCallBack: function (response) {
        },
        ...options,
    }
    showSweetAlertConfirmPopup(url, 'PUT', opt);
}

/**
 *
 * @param {string} [url] - Ajax URL that will be called when the Delete will be successfully
 * @param {Object} [options] - Options to Configure SweetAlert
 * @param {string} [options.text] - "Are you sure?"
 * @param {string} [options.title] - "You won't be able to revert this!"
 * @param {string} [options.icon] - "warning"
 * @param {boolean} [options.showCancelButton] - true
 * @param {string} [options.confirmButtonColor] - "#3085d6"
 * @param {string} [options.cancelButtonColor] - "#d33"
 * @param {string} [options.confirmButtonText] - "Yes, delete it!"
 * @param {string} [options.cancelButtonText] - "Cancel"
 * @param {function} [options.successCallBack]
 * @param {function} [options.errorCallBack]
 */
function showPermanentlyDeletePopupModal(url, options = {}) {

    // To Preserve OLD
    let opt = {
        title: window.trans["Are you sure"],
        text: window.trans["You are about to Delete this data"],
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes Delete Permanently"],
        cancelButtonText: window.trans['Cancel'],
        successCallBack: function () {
        },
        errorCallBack: function (response) {
        },
        ...options,
    }
    showSweetAlertConfirmPopup(url, 'DELETE', opt);
}

// const minutesToDuration = (minutes) => {
//     let h = Math.floor(minutes / 60);
//     let m = minutes % 60;
//     h = h < 10 ? '0' + h : h; // (or alternatively) h = String(h).padStart(2, '0')
//     m = m < 10 ? '0' + m : m; // (or alternatively) m = String(m).padStart(2, '0')
//     return `${h}:${m}:00`;
// }

const getSubjectOptionsList = (SubjectId, $this, userId) => {
    $(SubjectId).val("").removeAttr('disabled').show();
    if ($this.val().length) {
        $.each($this.val(), function (index, value) { 
            $(SubjectId).find('option').hide();
            if ($(SubjectId).find('option[data-class-section="' + value + '"][data-user="'+userId+'"]').length) {
                $(SubjectId).find('option[data-class-section="' + value + '"][data-user="'+userId+'"]').show().trigger('change');
            } else {
                $(SubjectId).val("data-not-found").attr('disabled', true).show().trigger('change');
            }
        });
    } else {
        $(SubjectId).val("data-not-found").attr('disabled', true).show().trigger('change');
    }
    
    
    

    // console.log("SubjectId :- ",SubjectId);
    
    // let duplicateIds = [];
    // if(SubjectId == '#subject-id') {
    //     $("#subject-id option").each(function () {
    //         duplicateIds.push($(this).val())
    //     });
        
    // }  else { 
    //     $("#edit-subject-id option").each(function () {
    //         duplicateIds.push($(this).val())
    //     });
       
    // }

    // duplicateIds = duplicateIds.filter(function (elem, index, self) {
    //     return index !== self.indexOf(elem) ?? duplicateIds.push(elem);
    // });

    // console.log("duplicateIds :- ",duplicateIds);
    
    // // if edit subject id then disable it
    // if(SubjectId == '#edit-subject-id'){
    //     $(SubjectId).attr('disabled', true);
    // } else {
        
    //     $(SubjectId).val("").removeAttr('disabled').show();
    //     $(SubjectId).find('option').hide();

    //     duplicateIds.forEach(duplicateId => {
            
    //         if ($(SubjectId).find('option[data-class-section="' + $this.val() + '"]').length) {
    //             $(SubjectId).find('option[data-class-section="' + $this.val() + '"]').show().trigger('change');
    //         } else if ($(SubjectId).find('option[value="' + duplicateId + '"]').length && $this.val().length) {
    //             if (duplicateIds) {
    //                 $(SubjectId).find('option[value="' + duplicateId + '"]:first').show().trigger('change');
    //             }
    //         } else {
    //             $(SubjectId).val("data-not-found").attr('disabled', true).show().trigger('change');
    //         }
    //     });

    // }


    // // if all options are selected then check the checkbox else uncheck the checkbox
    // if( $('#class-section-id').find('option').length == $('#class-section-id').find('option:selected').length) {
    //     $("#select-all").prop('checked', true);
    // } else {
    //     $("#select-all").prop('checked', false);
    // }

//    console.log("class section id length :- " + $('#class-section-id').find('option:selected').val());
//    console.log(window.location.pathname);
//    var currentUrl = window.location.pathname;
//    var isUrlEdit =  currentUrl.split("/")[currentUrl.split("/").length - 1];
//    console.log(isUrlEdit);
//     if(isUrlEdit == 'edit'){
//         //  $('#class-section-id').find('option');
//         console.log("edit page :- " , $('#class_subject_id').find('option'));
//         // $("#class-section-id option").each(function () {
//         //     // console.log("option :- ",($('#class-section-id').find('option:selected').val() == $(this).val()));
//         //     if( $('#class-section-id').find('option:selected').val() == $(this).val() ) {
//         //         console.log($('#class-section-id').find('option[value="' + $(this).val() + '"]').val());
//         //     }
//         // });
//     } else {
//         console.log("not edit page");
//     }
}

const getFeesClassOptionsList = (FeesId, classId) => {
    $(FeesId).val("").removeAttr('disabled').show();
    $(FeesId).find('option').hide();
    if ($(FeesId).find('option[data-class-section-id="' + classId + '"]').length) {
        $(FeesId).find('option[data-class-section-id="' + classId + '"]').show().trigger('change');
    } else {
        $(FeesId).val("").attr('disabled', false).show().trigger('change');
    }
}

// const getOnlineOfflinePaymentOptionsList = (PaymentId, value) => {
//     $(PaymentId).val("").removeAttr('disabled').show();
//     $(PaymentId).find('option').hide();

//     if (value == 1) {
//         // Online
//         value = 'stripe_razorpay';
//     } else if (value == 2) {
//         // Offline
//         value = 'cash_cheque';
//     } else {
//         value = '';
//     }

//     if ($(PaymentId).find('option[value="' + value + '"]').length) {
//         $(PaymentId).find('option[value="' + value + '"]').show().trigger('change');
//     } else {
//         $(PaymentId).val("").attr('disabled', false).show().trigger('change');
//     }
// }

const getClassSubjectOptionsList = (SubjectId, classId) => {
    $(SubjectId).val("").removeAttr('disabled').show();
    $(SubjectId).find('option').hide();
    if ($(SubjectId).find('option[data-class-id="' + classId + '"]').length) {
        $(SubjectId).find('option[data-class-id="' + classId + '"]').show().trigger('change');
    } else {
        $(SubjectId).val("").attr('disabled', false).show().trigger('change');
    }
}

const getFilterSubjectOptionsList = (SubjectId, $this) => {
    $(SubjectId).val("").removeAttr('disabled').show();
    $(SubjectId).find('option').hide();
    if ($this.val()) {
        if ($(SubjectId).find('option[data-class-section="' + $this.val() + '"]').length) {
            $(SubjectId).find('option[value=""]').show();
            $(SubjectId).find('option[data-class-section="' + $this.val() + '"]').show();
        } else {
            $(SubjectId).val("data-not-found").attr('disabled', true).show();
        }
    } else {
        $(SubjectId).val("");
    }
}

const getExamSubjectOptionsList = (SubjectId, $this, class_section_id) => {
    $(SubjectId).val("").removeAttr('disabled').show();
    $(SubjectId).find('option').hide();
    if ($(SubjectId).find('option[data-exam-id="' + $this.val() + '"]').length && $(SubjectId).find('option[data-class-section-id="' + class_section_id + '"]').length) {
        $(SubjectId).find('option[data-exam-id="' + $this.val() + '"][data-class-section-id="'+ class_section_id +'"]').show();
    } else {
        $(SubjectId).val("data-not-found").attr('disabled', true).show();
    }
}


const getExamOptionsListByClass = (examId, $this) => {
    $(examId).val("").removeAttr('disabled').show();
    $(examId).find('option').hide();
    if ($(examId).find('option[data-class-id="' + $this.val() + '"]').length) {
        $(examId).find('option[data-class-id="' + $this.val() + '"]').show();
    } else {
        $(examId).val("data-not-found").attr('disabled', true).show();
    }
}

const getExamOptionsList = (examId, $this) => {
    $(examId).val("").removeAttr('disabled').show();
    $(examId).find('option').hide();
    if ($(examId).find('option[data-session-year="' + $this.val() + '"]').length) {
        $(examId).find('option[data-session-year="' + $this.val() + '"]').show().trigger('change');
    } else {
        $(examId).val("data-not-found").attr('disabled', true).show();
    }
}

const getExamClassOptionsList = (classSection) => {
    var selectedOption = $('#filter_exam_id').find("option:selected");
    let class_id = selectedOption.data("class-id");
    $(classSection).val("").removeAttr('disabled').show();
    $(classSection).find('option').hide();
    if ($(classSection).find('option[data-class-id="' + class_id + '"]').length) {
        $(classSection).find('option[data-class-id="' + class_id + '"]').show().trigger('change');
    } else {
        $(classSection).val("data-not-found").attr('disabled', true).show();
    }
}


const getDashboardExamOptionsList = (examId, $this) => {
    $(examId).val("").removeAttr('disabled').show();
    $(examId).find('option').hide();
    if ($(examId).find('option[data-session-year="' + $this.val() + '"]').length) {
        $(examId).find('option[data-session-year="' + $this.val() + '"]').show().trigger('change');
    } else {
        $(examId).val("").attr('disabled', true).show();
    }
}

// Remove Online Exam Question's Option
const removeOptionWithAnswer = ($this, deleteElement) => {
    let optionNumber = $this.find('.option-number').html();
    $('#answer_select').find('option[value = ' + optionNumber + ']').remove();
    $this.slideUp(deleteElement);
}

function classValidation() {
    $('.subject').each(function (index, subject) {
        $(subject).rules("remove", "noDuplicateValues");
        $(subject).rules("add", {
            "noDuplicateValues": {
                class: "subject",
                group: $(subject).attr('data-group'),
                value: $(subject).find("option:selected").text()
            }
        });
    })
}

function getContrastColor(bgColor) {

    let convert_color_hex;
    if (isHexColor(bgColor)) {
        convert_color_hex = bgColor;
    } else {
        convert_color_hex = rgbToHex(bgColor);
    }

    let hexColor = convert_color_hex.substring(1); // Remove the '#' character
    let r = parseInt(hexColor.substring(0, 2), 16);
    let g = parseInt(hexColor.substring(2, 4), 16);
    let b = parseInt(hexColor.substring(4, 6), 16);

    // Calculate the relative luminance (perceived brightness) of the color
    let brightness = (r * 299 + g * 587 + b * 114) / 1000;
    // brightness = 255 - Math.abs(255 - brightness);
    // Choose white or black text based on the background brightness
    return brightness > 55 ? "#000" : "#fff";
}

function rgbToHex(rgb) {
    // Extract the individual R, G, and B values from the RGB string
    const rgbArray = rgb.match(/\d+/g);

    // Convert the R, G, and B values to hexadecimal and concatenate them
    if (rgbArray) {
        return '#' + rgbArray.map(Number).map(num => {
            // const hex = num.toString(16);
            const hex = num.toString();
            return hex.length === 1 ? '0' + hex : hex; // Ensure two-digit hex values
        }).join('');
    }
    return '#000';
}

function isHexColor(color) {
    // Regular expression to match HEX format (e.g., "#ff00ff" or "ff00ff")
    const hexRegex = /^#?([0-9A-Fa-f]{3}){1,2}$/;
    return hexRegex.test(color);
}

function handlePayInInstallment($document, data) {
    if (data.fees_data.is_installment_paid) {
        $document.find('.pay-in-installment').trigger('click');
        $document.find('.pay-in-installment').attr('disabled', true);
    } else if (data.fees_status == 1) {
        $document.find('.pay-in-installment-row').hide(200);
        $document.find('.pay-in-installment').attr('disabled', true);
        // $document.find('.compulsory-fees-payment').prop('disabled', true);
    } else {
        $document.find('.pay-in-installment').attr('disabled', false);
        // $document.find('.compulsory-fees-payment').prop('disabled', false);
    }
}

function updateStudentIdsHidden(tableId, inputField, buttonClass) {
    var selectedRows = $(tableId).bootstrapTable('getSelections');
    var selectedRowsValues = selectedRows.map(function (row) {
        return row.student_id; // replace 'id' with the field you want
    });
    $(inputField).val(JSON.stringify(selectedRowsValues));

    if (buttonClass != null) {
        if (selectedRowsValues.length) {
            $(buttonClass).show();
        } else {
            $(buttonClass).hide();
        }
    }
}

function getLastDateOfMonth(month, year) {
    // Initialize the date to the first day of the next month
    var date = new Date(year, month, 1);
    // Set the date to the last day of the current month by subtracting one day from the next month's first day
    date.setDate(date.getDate() - 1);

    // Get the day, month, and year
    var day = date.getDate();
    var monthFormatted = ("0" + (date.getMonth() + 1)).slice(-
        2); // Adding 1 to get the right month since months are zero-based
    var yearFormatted = date.getFullYear();

    // Format the date as dd-mm-YYYY
    var formattedDate = `${day < 10 ? '0' : ''}${day}-${monthFormatted}-${yearFormatted}`;
    return formattedDate;
}

function formatMoney(n) {
    return n.toLocaleString().split(".")[0] + "." + n.toFixed(2).split(".")[1];
}

function expense_graph(months, data) {
    var options = {
        series: [{
            name: window.trans['Amount'],
            data: isRTL() ? data.reverse() : data,
        }],
        chart: {
            height: 380,
            type: 'area',
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        tooltip: {

        },
        xaxis: {
            categories: isRTL() ? months.reverse() : months,
        },
        yaxis: {
            opposite:isRTL(),
            min: 0
        }
    };
    $('#expenseChart').html('');
    var chart = new ApexCharts(document.querySelector("#expenseChart"), options);
    chart.render();
}

function getMinutes(minute) {
    if (minute < 10) {
        return '0' + minute;
    } else {
        return minute;
    }
}

function gender_ratio(boys, girls, total_students) {
    var options = {
        series: [boys, girls],
        chart: {
            height: 390,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                dataLabels: {
                    name: {
                        fontSize: '22px',
                    },
                    value: {
                        fontSize: '16px',
                    },
                    total: {
                        show: true,
                        label: window.trans['total'],
                        formatter: function (w) {
                            // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                            // return 60
                            return total_students
                        }
                    }
                },
            },
        },

        labels: [window.trans['male'], window.trans['female']],
        legend: {
            show: true,
            floating: true,
            position: 'top',
            offsetX: 0,
            offsetY: 340
        },
    };

    var chart = new ApexCharts(document.querySelector("#gender-ratio-chart"), options);
    chart.render();
}

function fees_details(data) {
    var options = {
        series: [data.fullPaidFees, data.partialPaidFees, data.unPaidFees],
        chart: {
            width: '100%',
            type: 'donut',
        },

        dataLabels: {
            enabled: true
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: '100%',
                },
                legend: {
                    show: true
                }
            }
        }],
        labels: [window.trans['paid'], window.trans['Partial Paid'], window.trans['unpaid']],
        legend: {
            show: true,
            position: 'bottom',
            offsetY: 0,
            height: 20,
        },
        colors: ['#1BCFB4', '#198AE3', '#FE7C96']
    };

    var chart = new ApexCharts(document.querySelector("#fees_details_chart"), options);
    chart.render();
}

function class_attendance(section, data)
{
    var options = {
        series: [
          {
            name: window.trans['attendance'],
            data: data
          }
        ],
        chart: {
          height: 400,
          type: "bar"
        },
        plotOptions: {
          bar: {
            borderRadius: 10,
            dataLabels: {
              position: "top" // top, center, bottom
            }
          }
        },
        dataLabels: {
          enabled: true,
          formatter: function (val) {
            return val + "%";
          },
          offsetY: -20,
          style: {
            fontSize: "12px",
            colors: ["#304758"]
          }
        },
      
        xaxis: {
          categories: section,
          position: "bottom",
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: true
          },
          crosshairs: {
            fill: {
              type: "gradient",
              gradient: {
                colorFrom: "#D8E3F0",
                colorTo: "#BED1E6",
                stops: [0, 100],
                opacityFrom: 0.4,
                opacityTo: 0.5
              }
            }
          },
          tooltip: {
            enabled: true
          }
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          labels: {
            show: true,
            formatter: function (val) {
              return val + "%";
            },
            
          },
          min:0,
          max:100,
          opposite: isRTL()
        },
        title: {
          
        }
      };
      
      $('#attendanChart').html('');
      var chart = new ApexCharts(document.querySelector("#attendanChart"), options);
      chart.render();
      
}


function subscription_transaction(labels, data)
{
    var options = {
        series: [
          {
            name: window.trans['Amount'],
            data: data
          }
        ],
        chart: {
          height: 400,
          type: "bar"
        },
        plotOptions: {
          bar: {
            borderRadius: 10,
            dataLabels: {
              position: "top" // top, center, bottom
            }
          }
        },
        dataLabels: {
          enabled: true,
          formatter: function (val) {
            return val.toFixed(2);
          },
          offsetY: -20,
          style: {
            fontSize: "12px",
            colors: ["#304758"]
          }
        },
      
        xaxis: {
          categories: labels,
          position: "bottom",
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: true
          },
          crosshairs: {
            fill: {
              type: "gradient",
              gradient: {
                colorFrom: "#D8E3F0",
                colorTo: "#BED1E6",
                stops: [0, 100],
                opacityFrom: 0.4,
                opacityTo: 0.5
              }
            }
          },
          tooltip: {
            enabled: true
          }
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          labels: {
            show: true,
          },
          min:0,
            opposite: isRTL(),
            labels: {
                show: true,
                formatter: function (val) {
                    return val.toFixed(2);
                }
            }

        },
        
      };
      
      $('#subscriptionTransactionChart').html('');
      var chart = new ApexCharts(document.querySelector("#subscriptionTransactionChart"), options);
      chart.render(); 
}
function addon_graph(labels, data) {
    var options = {
        series: [
            {
                name: window.trans['No'],
                data: data
            }
        ],
        chart: {
            height: 400,
            type: "bar"
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                dataLabels: {
                    position: "top" // top, center, bottom
                }
            }
        },
        dataLabels: {
            enabled: true,

            offsetY: -20,
            style: {
                fontSize: "12px",
                colors: ["#304758"]
            }
        },

        xaxis: {
            categories: labels,
            position: "bottom",
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: true
            },
            crosshairs: {
                fill: {
                    type: "gradient",
                    gradient: {
                        colorFrom: "#D8E3F0",
                        colorTo: "#BED1E6",
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5
                    }
                }
            },
            tooltip: {
                enabled: true
            }
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                show: true,
            },
            min: 0,
            opposite: isRTL()
        },
        title: {

        }
    };

    $('#addonChart').html('');
    var chart = new ApexCharts(document.querySelector("#addonChart"), options);
    chart.render();
}

function package_graph(labels, data) {
    var options = {
        series: data,
        chart: {
            width: "100%",
            type: "donut",
            height: 450,
        },
        labels: labels,

        plotOptions: {
            donut: {
                dataLabels: {
                    offset: -5
                }
            }
        },
        dataLabels: {
            formatter(val) {
                return [val.toFixed(1) + "%"];
            }
        },
        legend: {
            show: true,
            position: 'bottom'
        },
        colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A', '#E308F7', '#FF8400', '#00F5FE', '#0DFF00', '#AC40FF', '#F200FF']
    };

    $('#packageChart').html('');
    var chart = new ApexCharts(document.querySelector("#packageChart"), options);
    chart.render();
}



function generateRandomColors(count) {
    var colors = [];
    var letters = '0123456789ABCDEF';
    while (colors.length < count) {
      var color = '#';
      for (var j = 0; j < 6; j++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      if (!colors.includes(color)) {
        colors.push(color);
      }
    }
    return colors;
  }