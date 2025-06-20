/*
* Common JS is used to write code which is generally used for all the UI components
* Specific component related code won't be written here
*/

"use strict";
$(document).ready(function () {
    // $('#toolbar').parent().addClass('col-12  col-md-7 col-lg-7 p-0');
    $('#table_list').on('all.bs.table', function () {
        $('#toolbar').parent().addClass('col-12  col-md-7 col-lg-7 p-0');
    })
    // $('#table_list').on('load-success.bs.table', function () {
    //
    //     $('#toolbar').parent().addClass('col-12  col-md-7 col-lg-7 p-0');
    // })

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
})

//Setup CSRF Token default in AJAX Request
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('#create-form,.create-form,.create-form-without-reset').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let formReset = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    
    if ($(formElement).hasClass('attendance-table') && $('.search-input').val()) {
        Swal.fire({icon: 'error', text: window.trans['Kindly clear the data from the search field']});
        return 0;
    }
    var school_prefix = '';
    var school_code = '';
    if ($(formElement).hasClass('school-registration-form')) {
        showLoading();
        school_prefix = $('#school_code_prefix').val();
        school_code = $('.school_code').val();
    }
        
    let submitButtonText = submitButtonElement.val();
    submitButtonElement.val('Please Wait...').attr('disabled', true);

    setTimeout(() => {
        let data = new FormData(this);
        let preSubmitFunction = $(this).data('pre-submit-function');
        if (preSubmitFunction) {
            //If custom function name is set in the Form tag then call that function using eval
            eval(preSubmitFunction + "()");
        }
        let customSuccessFunction = $(this).data('success-function');
        // noinspection JSUnusedLocalSymbols
        function successCallback(response) {

            if (response.warning) {
                $('#editModal').modal('hide');
                $('#table_list').bootstrapTable('refresh');
                $('#reset').trigger('click');
            }
            
            if ($(formElement).hasClass('reload-window')) {
                
                try {
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } catch (error) {
                    
                }
            }
            
            if (!$(formElement).hasClass('create-form-without-reset')) {
                
                try {
                    formElement[0].reset();
                } catch (error) {
                    
                }
                $(".select2-dropdown").val("").trigger('change').trigger('unselect');
                $('.stream-divs').slideUp(500);
                $('[data-repeater-item]').slice(1).remove();
            }
            setTimeout(() => {
                $('#tags').removeTag();
                $('#editModal').modal('hide');
                $('#application-status-options').hide(500);
                $('#enable-features').modal('hide');
            }, 500);
             
            $('#table_list').bootstrapTable('refresh');
            if (customSuccessFunction) {
                //If custom function name is set in the Form tag then call that function using eval
                eval(customSuccessFunction + "(response)");
            }

            // School registration
            if ($(formElement).hasClass('school-registration-form')) {
                closeLoading();
                $('#school_code_prefix').val(school_prefix);
                $('.school_code').val(parseInt(school_code) + 1);
            }

        }
        submitButtonElement.val(submitButtonText ? submitButtonText : 'Submit').attr('disabled', false);
        formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);

    }, 300);
})

$('.online-admission-form,.create-form-with-captcha').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let formReset = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');    
    let submitButtonText = submitButtonElement.val();
    submitButtonElement.val('Please Wait...').attr('disabled', true);
    setTimeout(() => {
        let data = new FormData(this);
        let preSubmitFunction = $(this).data('pre-submit-function');
        if (preSubmitFunction) {
            //If custom function name is set in the Form tag then call that function using eval
            eval(preSubmitFunction + "()");
        }
        // noinspection JSUnusedLocalSymbols
        function successCallback(response) {
            if ($(formElement).hasClass('reload-window')) {
                
                try {
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } catch (error) {
                    
                }
            }
            if ($(formElement).hasClass('online-admission-form')) {
                var url = window.location.origin;
                $('.default-image').attr('src', url+'/assets/school/images/Image Preview.png');
            }

            formElement[0].reset();
            $('#fileUpload').val('');
            $('.file-select span').text('No File Selected.');
            
            try {
                grecaptcha.reset();
            } catch (error) {
                
            }
        }
        submitButtonElement.val(submitButtonText ? submitButtonText : 'Submit').attr('disabled', false);
        formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);

    }, 300);
})

$('.school-registration').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let formReset = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');    
    let submitButtonText = submitButtonElement.val();
    let isValid = true; 
    
        $(".checkbox-group").each(function () {
            let checkboxes = $(this).find("input[type='checkbox']");
            let errorMessage = $(this).next(".checkbox-error");

            if (checkboxes.length > 0 && errorMessage.length > 0) {
                let isChecked = checkboxes.is(":checked");

                if (!isChecked) {
                    isValid = false;
                    errorMessage.removeClass("d-none");
                } else {
                    errorMessage.addClass("d-none");
                }
            }
        });
        
    if (!isValid) {
        return;
    }
  
    submitButtonElement.val('Please Wait...').attr('disabled', true);
    showLoading();

    setTimeout(() => {
        let data = new FormData(this);
        let preSubmitFunction = $(this).data('pre-submit-function');
        if (preSubmitFunction) {
            //If custom function name is set in the Form tag then call that function using eval
            eval(preSubmitFunction + "()");
        }
        let customSuccessFunction = $(this).data('success-function');
        // noinspection JSUnusedLocalSymbols
        function successCallback(response) {
            
            // Reset the form
            formReset[0].reset();

            if (response.warning) {
                $('#staticBackdrop').modal('hide');
                $('#reset').trigger('click');
            }
            
            if ($(formElement).hasClass('reload-window')) {
                
                try {
                    closeLoading();
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } catch (error) {
                    
                }
            }
        
            $('#staticBackdrop').modal('hide');             
            
            if (customSuccessFunction) {
                //If custom function name is set in the Form tag then call that function using eval
                eval(customSuccessFunction + "(response)");
            }

            try {

            } catch (error) {
                grecaptcha.reset();
            }

            closeLoading();

        }
        submitButtonElement.val(submitButtonText).attr('disabled', false);
        formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);

    }, 300);
})

function showLoading() {
    Swal.fire({
        title: please_wait,
        text: processing_your_request,
        allowOutsideClick: false, // Disable clicking outside to close
        showConfirmButton: false, // Hide the confirm button
        didOpen: () => {
            Swal.showLoading();  // Show loading spinner
        }
    });
}

// Close the alert manually
function closeLoading() {
    Swal.close();  // Close the alert
}

// create-form-without-reset-text-editor
$('.create-form-without-reset-text-editor').on('submit', function (e) {
    e.preventDefault();
    setTimeout(() => {
        let request_data = new FormData(this);
        // let data = request_data.get('data');
        let formElement = $(this);
        let submitButtonElement = $(this).find(':submit');
        let url = $(this).attr('action');
        formAjaxRequest('POST', url, request_data, formElement, submitButtonElement);
    }, 1000);

})

$('#edit-form,.edit-form,.edit-form-without-reset,.edit-form-staff-payroll-setting').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');

    let submitButtonText = submitButtonElement.val();
    submitButtonElement.val('Please Wait...').attr('disabled', true);
    let status = 1;
    if ($(formElement).hasClass('edit-form-staff-payroll-setting')) {
        let allowance_id = [];
        let deduction_id = [];
        $('.allowance_id').each(function() {
            if ($(this).val()) {
                allowance_id.push($(this).val());
            }
        });

        $('.deduction_id').each(function() {
            if ($(this).val()) {
                deduction_id.push($(this).val());
            }
        });

        if (hasDuplicates(allowance_id) || hasDuplicates(deduction_id)) {
            status = 0;
        }
    }
    if (status) {
        setTimeout(() => {
            let data = new FormData(this);
            data.append("_method", "PUT");
            // let url = $(this).attr('action') + "/" + data.get('edit_id');
            let url = $(this).attr('action');
            let preSubmitFunction = $(this).data('pre-submit-function');
            if (preSubmitFunction) {
                //If custom function name is set in the Form tag then call that function using eval
                eval(preSubmitFunction + "()");
            }
            let customSuccessFunction = $(this).data('success-function');
    
            // noinspection JSUnusedLocalSymbols
            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                setTimeout(function () {
                    $('#editModal').modal('hide');
                    $('#change-bill').modal('hide');
                    $('#viewModal').modal('hide');
                    
                    $('#update-current-plan').modal('hide');
                    $('#tags').removeTag();
    
                    if (!$(formElement).hasClass('edit-form-without-reset')) {
                        formElement[0].reset();
                    }
    
                }, 1000)
                if (customSuccessFunction) {
                    //If custom function name is set in the Form tag then call that function using eval
                    eval(customSuccessFunction + "(response)");
                }
            }
            submitButtonElement.val(submitButtonText).attr('disabled', false);
            formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
        }, 300);
    } else {
        Swal.fire({icon: 'error', text: window.trans['Duplicate values are not allowed']});
        submitButtonElement.val(submitButtonText).attr('disabled', false);
    }
    
    
})

function hasDuplicates(array) {
    let uniqueValues = new Set(array);
    return uniqueValues.size !== array.length;
}

$(document).on('click', '.delete-form', function (e) {
    e.preventDefault();
    showDeletePopupModal($(this).attr('href'), {
        successCallBack: function () {
            $('#table_list').bootstrapTable('refresh');
        }, errorCallBack: function (response) {
            // showErrorToast(response.message);
        }
    })
})

$(document).on('click', '.restore-data', function (e) {
    e.preventDefault();
    showRestorePopupModal($(this).attr('href'), {
        successCallBack: function () {
            $('#table_list').bootstrapTable('refresh');
        }
    })
})

$(document).on('click', '.trash-data', function (e) {
    e.preventDefault();
    showPermanentlyDeletePopupModal($(this).attr('href'), {
        successCallBack: function () {
            $('#table_list').bootstrapTable('refresh');
        }
    })
})

$(document).on('click', '.set-form-url', function (e) {
    //This event will be called when user clicks on the edit button of the bootstrap table
    e.preventDefault();
    $('#edit-form,.edit-form').attr('action', $(this).attr('href'));

})

$(document).on('click', '.delete-form-reload', function (e) {
    e.preventDefault();
    showDeletePopupModal($(this).attr('href'), {
        successCallBack: function () {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    })
})

$(document).on('click', '.change-school-status', function (e) {
    e.preventDefault();
    let school_id = $(this).data('id');
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["change_school_status"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes, Change it"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/schools/change/status/' + school_id;
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('PUT', url, data, null, successCallback, errorCallback);
        }
    })
})

$(document).on('click', '.change-package-status', function (e) {
    e.preventDefault();
    let package_id = $(this).data('id');
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["change_package_status"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes, Change it"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/package/status/' + package_id;
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('GET', url, data, null, successCallback, errorCallback);
        }
    })
})


$(document).on('click', '.delete-class-timetable', function (e) {
    e.preventDefault();
    let class_section_id = $(this).data('id');
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["Delete Class Timetable"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes, Change it"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/timetable/delete/' + class_section_id;
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('delete', url, data, null, successCallback, errorCallback);
        }
    })
})



$(document).on('click', '.change-addon-status', function (e) {
    e.preventDefault();
    let addon_id = $(this).data('id');
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["change_addon_status"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes, Change it"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/addons/status/' + addon_id;
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('PUT', url, data, null, successCallback, errorCallback);
        }
    })
})

$(document).on('click', '.delete-payroll-setting', function (e) {
    e.preventDefault();
    let $this = $(this);
    let id = $(this).data('id');
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["Delete this item"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/staff/payroll-setting/' + id;
            let data = null;

            function successCallback(response) {
                showSuccessToast(response.message);
                $this.parent().parent().remove();
                setTimeout(() => {
                    calculate_net_salary();
                }, 1000);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
        }
    })
})


$(document).on('click', '.cancel-upcoming-plan', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["Cancel This Plan"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/subscriptions/cancel-upcoming/' + id;
            let data = null;

            function successCallback(response) {
                showSuccessToast(response.message);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('GET', url, data, null, successCallback, errorCallback);
        }
    })
})

$(document).on('click', '.select-plan', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let type = $(this).data('type');
    let iscurrentplan = $(this).data('iscurrentplan');
    
    let link = baseUrl + '/school-terms-condition';

    Swal.fire({
        title: window.trans["Are you sure"],
        html: window.trans["Agree to This Subscription"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"],
        input: 'checkbox',
        inputPlaceholder: '<div class="m-2">'+ window.trans['I accept the provided'] + '<a href="'+ link +'" target="_blank"> '+window.trans['terms_condition']+' </a></div>',


    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value) {
                let url = baseUrl + '/subscriptions/plan/' + id +'/type/'+type+'/current-plan/'+iscurrentplan;
                let data = null;

                function successCallback(response) {
                    // If currently active package
                    if (response.data) {
                        let package_id = response.data.package_id;
                        let package_type = response.data.plan
                        confirm_upcoming_plan(package_id, package_type);
                    } else {
                        if (response.type == 'prepaid') {
                            window.location.href = response.url;
                        } else {
                            showSuccessToast(response.message);
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }

                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('GET', url, data, null, successCallback, errorCallback);
            } else {
                Swal.fire({icon: 'error', text: window.trans['please_confirm_terms_condition']});
            }

        }
    })

})


$(document).on('click', '.id-card-settings', function (e) {
    e.preventDefault();
    let type = $(this).data('type');
    let link = baseUrl + '/school-settings/id-card/remove/';

    Swal.fire({
        title: window.trans["Are you sure"],
        html: window.trans["You wont be able to revert this"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"],

    }).then((result) => {
        if (result.isConfirmed) {
                let url = link + type;
                let data = null;

                function successCallback(response) {
                    $('#'+type).hide(500);
                    showSuccessToast(response.message);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('GET', url, data, null, successCallback, errorCallback);
        }
    })

})

function confirm_upcoming_plan(package_id, package_type = null) {
    let id = package_id;
    let type = package_type;
    let message = window.trans["Accept Upcoming Billing Cycle Subscription"];
    if (type == 'Trial') {
        message = window.trans["You want to go ahead with this plan?"];
    }
    Swal.fire({
        title: window.trans["Are you sure"],
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/subscriptions/confirm-upcoming-plan/' + id;
            let data = null;

            function successCallback(response) {
                showSuccessToast(response.message);
                setTimeout(() => {
                    window.location.href = baseUrl + '/subscriptions/history';
                }, 2000);
            }

            function errorCallback(response) {
                let message = window.trans["You have already selected an upcoming billing cycle plan If you want to change your upcoming billing cycle plan please ensure to remove the previously selected plan before making any alterations"];

                if (response.data == 0) {
                    message = response.message;
                }
                already_added_upcoming_plan(message);
            }

            ajaxRequest('GET', url, data, null, successCallback, errorCallback);
        }
    })
}

function already_added_upcoming_plan(message) {
    Swal.fire({
        text: message,
        icon: 'error',
    })
}

// Start immediate plan if currently working on another plan
$(document).on('click', '.start-immediate-plan', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let type = $(this).data('type');
    let link = baseUrl + '/school-terms-condition';
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["start_immediate_this_plan"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"],
        input: 'checkbox',
        inputPlaceholder: '<div class="m-2">'+ window.trans['I accept the provided'] + '<a href="'+ link +'" target="_blank"> '+window.trans['terms_condition']+' </a></div>',
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value) { 
                let url = baseUrl + '/subscriptions/start-immediate-plan/' + id + '/type/'+type;
                let data = null;

                function successCallback(response) {
                    if (response.data.type == 0) {
                        window.location.href = response.data.url;
                    } else {
                        showSuccessToast(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 2000);    
                    }
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }
                ajaxRequest('GET', url, data, null, successCallback, errorCallback);
            } else {
                Swal.fire({icon: 'error', text: window.trans['please_confirm_terms_condition']});
            }            
        }
    })
})

// add-addon
$(document).on('click', '.add-addon', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let type = $(this).data('type');
    let link = baseUrl + '/school-terms-condition';
    Swal.fire({
        title: window.trans["Are you sure you want to add this add-on"],
        text: window.trans["This add-on will be added into your current subscribed package and will expire when your current subscription expires"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"],
        input: 'checkbox',
        inputPlaceholder: '<div class="m-2">'+ window.trans['I accept the provided'] + '<a href="'+ link +'" target="_blank"> '+window.trans['terms_condition']+' </a></div>',
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value) {
                let url = baseUrl + '/addons/subscribe/' + id + '/'+'package-type/' + type;
                let data = null;

                function successCallback(response) {
                    if (response.type == 'prepaid') {
                        window.location.href = response.url;
                    } else {
                        showSuccessToast(response.message);
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('GET', url, data, null, successCallback, errorCallback);
            } else {
                Swal.fire({icon: 'error', text: window.trans['please_confirm_terms_condition']});
            }

        }
    })
})

// discontinue_addon
$(document).on('click', '.discontinue_addon', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["discontinue_upcoming_billing_cycle"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/addons/discontinue/' + id;
            let data = null;

            function successCallback(response) {
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('GET', url, data, null, successCallback, errorCallback);
        }
    })
})


$(document).on('click', '.no-feature-lock-menu,.no-feature-lock-menu-sub-item', function (e) {
    e.preventDefault();
    let link = baseUrl + '/addons/plan';
    let role = $(this).data('name');

    if (role == 'School Admin') {
        Swal.fire({
            title: window.trans["no_permission"],
            icon: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: window.trans["ok"],
            html: '<div class="mb-2">' + window.trans["no_permission_upgrade_plan"] + '</div><a href="' + link + '">' + window.trans["click_here_to_buy_addon"] + '</a>',
        })
    } else if (role == 'basic-features') {
        Swal.fire({
            title: window.trans["Your License Has Expired"],
            icon: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: window.trans["ok"],
        })
    } else {
        Swal.fire({
            title: window.trans["no_permission"],
            icon: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: window.trans["ok"],
        })
    }

})

$(document).ready(function () {
    $('.sidebar .nav-link').each(function (index, value) {
        if (typeof $(value).data('access') != "undefined" && !$(value).data('access')) {
            if ($(value).data('toggle') == "collapse") {
                $(value).addClass('no-feature-lock-menu-without-alert');
            } else {
                if ($(value).parents('.collapse').length) {
                    $(value).addClass('no-feature-lock-menu-sub-item');
                } else {
                    $(value).addClass('no-feature-lock-menu');
                }
                $(value).attr('href', '#');
            }

        }
    })
})

// Drop down menu
$(document).ready(function(){
    $('.dropdown-toggle').click(function(){
        var buttonId = $(this).attr('id');
        var menuId = $(this).next('.custom-dropdown-menu').attr('id');

        // Toggle the visibility of the dropdown menu
        $('#' + menuId).toggleClass('show-custom-dropdown');
    });

    // Hide the dropdown menu when clicking outside of it
    $(document).click(function(event) {
        if (!$(event.target).closest('.dropdown').length) {
            $('.custom-dropdown-menu').removeClass('show-custom-dropdown');
        }
    });

    // Hide the dropdown menu when an item is selected
    $('.dropdown-item').click(function() {
        $(this).closest('.custom-dropdown-menu').removeClass('show-custom-dropdown');
    });
});

$(document).on('click', '.restore-database', function (e) {
    e.preventDefault();
    let database_id = $(this).data('id');
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["restore_this_databse"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/database-backup/restore/' + database_id;
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('GET', url, data, null, successCallback, errorCallback);
        }
    })
})

// create-backup
$(document).on('click', '.create-backup', function (e) {
    e.preventDefault();
    Swal.fire({
        title: window.trans["Are you sure"],
        text: window.trans["want_to_create_a_backup"],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: window.trans["Yes"],
        cancelButtonText: window.trans["Cancel"]
    }).then((result) => {
        if (result.isConfirmed) {
            let url = baseUrl + '/database-backup/store/';
            let data = null;

            function successCallback(response) {
                // download the file
                if(response.data) {
                    
                    const aTag = document.createElement('a');
                    aTag.href = response.data;
                    // a.download = fileName;
                    document.body.appendChild(aTag);
                    aTag.click();
                    aTag.remove();

                    window.URL.revokeObjectURL(objectUrl); // Clean up the object URL after download
                }
                
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);

            }
            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('GET', url, data, null, successCallback, errorCallback);
        }
    })
})

// send notification check box
$(document).on('click', '#send_notification', function (e) {
    let send_notification = $('#send_notification').is(':checked');
    
    if(send_notification) {
        $('#send_notification').val(1);
    } else {
        $('#send_notification').val(0);
    }
})

// reset multiple select
$(document).on('click', '[type="reset"]', function (e) {
    $('.select2-dropdown').val(null).trigger('change');
})