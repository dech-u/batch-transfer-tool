"use strict";
//Bootstrap actionEvents

window.sectionEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
    }
};

window.addonEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_price').val(row.price);
        setTimeout(() => {
            if( $('#edit_name').val() ) {
                $('#edit_name').prop('required',false);
            } else {
                $('#edit_name').prop('required',true);
            }
            if( $('#edit_price').val() ) {
                $('#edit_price').prop('required',false);
            } else {
                $('#edit_price').prop('required',true);
            }
            $('input[name=feature_id][value=' + row.feature.id + '].feature-radio').prop('checked', true);
        }, 500);
    }
};

window.mediumEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#name').val(row.name);
    }
};

window.certificateTypeEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#edit_name').val(row.name);

        setTimeout(() => {
            if (row.type == 'Student') {
                $('#edit_student').prop('checked', true);
            } else {
                $('#edit_staff').prop('checked', true);
            }    
        }, 500);
    }
};

window.certificateTemplateEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
    }
};

window.subjectEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_code').val(row.code);
        $('#edit_bg_color').asColorPicker('val', row.bg_color);
        $('input[name=medium_id][value=' + row.medium_id + '].edit').prop('checked', true);
        $('input[name=type][value=' + row.eng_type + '].edit').prop('checked', true);
    }
};


window.classGroupEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_description').val(row.description);
        $('#edit_image').attr('src',row.image);
        var class_ids = row.class_ids;
        class_ids = class_ids.split(',');
        $('#edit_class_ids').val(class_ids).trigger('change');
        
    }
};


window.expenseCategoryEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_description').val(row.description);
    }
};


window.expenseEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_category_id').val(row.category_id);
        $('#edit_title').val(row.title);
        $('#edit_ref_no').val(row.ref_no);
        $('#edit_amount').val(row.amount);
        $('#edit_date').val(moment(row.date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $('#edit_description').val(row.description);
        $('#edit_session_year_id').val(row.session_year_id);

    }
};

window.examEvents = {
    'click .publish-exam-result': function (e, value, row) {
        e.preventDefault();
        // alert('working');
        Swal.fire({
            title: window.trans['Are you sure'],
            text: window.trans["change_status"],
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: window.trans["Yes, Change it"],
            cancelButtonText: window.trans["Cancel"]
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/exams/publish/' + row.id;

                function successCallback(response) {
                    if (response.warning) {
                        showWarningToast(response.message);
                    } else {
                        showSuccessToast(response.message);
                    }
                    $('#table_list').bootstrapTable('refresh');
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('POST', url, null, null, successCallback, errorCallback);
            }
        })
    },
    'click .edit-data': function (e, value, row) {
        //Reset to Old Values
        $('.edit-extra-timetable').html('');
        $('.edit_exam_timetable').show();
        $('#edit_id').val(row.id);
        $('.edit_class_id').val(row.class_id);
        $('#edit_name').val(row.name);
        $('#edit_description').val(row.description);
    },
    'click .marks-status': function (e, value, row) {
        //Reset to Old Values
        console.log(row);
        
    }
};

window.assignmentEvents = {
    'click .edit-data': function (e, value, row) {
        //Reset to Old Values
        let html_file = '';
        $('#edit_id').val(row.id);
        $('#edit-class-section-id').val(row.class_section_id);
        $('#edit-subject-id').val(row.class_subject_id);
        $('#edit_name').val(row.name);
        $('#edit_instructions').val(row.instructions);
        
        // set the class section and subject id
        $('.edit_class_section_id').val(row.class_section_id).trigger('change');
        $('#class_subject_id_value').val(row.class_subject_id);


        $('#edit_checkbox_add_url').prop('checked', false).trigger('change');
        
        if (row.file && row.file.length > 0) {
            $.each(row.file, function (key, data) {
                if(data.type == 4) {
                    $('.edit_checkbox_add_url').prop('checked', true).trigger('change');
                    // $('#edit_add_url').val(data.file_url);
                }
            });
        }
        
        let dt = new Date(row.due_date);
        let Fromdatetime = dt.getFullYear() + "-" + ("0" + (dt.getMonth() + 1)).slice(-2) + "-" + ("0" + dt.getDate()).slice(-2) + "T" + ("0" + dt.getHours()).slice(-2) + ":" + ("0" + dt.getMinutes()).slice(-2) + ":" + ("0" + dt.getSeconds()).slice(-2);
        $('#edit_due_date').val(Fromdatetime);
        $('#edit_points').val(row.points);
        if (row.resubmission) {
            $('#edit_resubmission_allowed').prop('checked', true).trigger('change');
            $('#edit_extra_days_for_resubmission').val(row.extra_days_for_resubmission);
        } else {
            $('#edit_resubmission_allowed').prop('checked', false).trigger('change');
            $('#edit_extra_days_for_resubmission').val('');
        }

        if (row.file) {
            $.each(row.file, function (key, data) {
                html_file += '<div class="file"><a target="_blank" href="' + data.file_url + '" class="m-1">' + data.file_name + '</a> <span class="fa fa-times text-danger remove-assignment-file" data-id=' + data.id + '></span><br><br></div>'
            })

            $('#old_files').html(html_file);
        }
    }
};

window.announcementEvents = {
    'click .edit-data': function (e, value, row) {
        let html_file = '';
        $('#id').val(row.id);
        $('#title').val(row.title);
        $('#description').val(row.description);

        $('.edit_class_section_id').val(row.class_sections).trigger('change');
        $('.edit_subject_id').val(row.class_subject_id);

        $('#edit_checkbox_add_url').prop('checked', false).trigger('change');
        
        if (row.file && row.file.length > 0) {
            $.each(row.file, function (key, data) {
                if(data.type == 4) {
                    $('.edit_checkbox_add_url').prop('checked', true).trigger('change');
                    // $('#edit_add_url').val(data.file_url);
                }
            });
        }
        
        if (row.file) {
            $.each(row.file, function (key, data) {
                html_file += '<div class="file"><a target="_blank" href="' + data.file_url + '" class="m-1">' + data.file_name + '</a> <span class="fa fa-times text-danger remove-assignment-file" data-id=' + data.id + '></span><br><br></div>'
            })

            $('#old_files').html(html_file);
        }
        $('.edit-file').val("");
    }
};

window.guardianEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#first_name').val(row.first_name);
        $('#last_name').val(row.last_name);
        $('input[name=gender][value=' + row.gender + '].edit').prop('checked', true);
        $('#email').val(row.email);
        $('#mobile').val(row.mobile);
    }
};

window.studentEvents = {
    'click .edit-data': function (e, value, row) {

        // Reset the radio button
        $('input[name=application_status]').prop('checked', false);

        $('#edit_id').val(row.id);
        $('#edit_user_id').val(row.user_id);
        $('#edit_first_name').val(row.user.first_name);
        $('#edit_last_name').val(row.user.last_name);
        $('#edit_mobile').val(row.user.mobile);
        $('#edit_dob').val(moment(row.user.dob, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $('#session_year_id').val(row.session_year_id);
        $('#edit_admission_no').val(row.admission_no);
        $('#edit-student-image-tag').attr('src', row.user.image);
        $('#edit-current-address').val(row.user.current_address);
        $('#edit-permanent-address').val(row.user.permanent_address);
        $('#edit_student_class_section_id').val(row.class_section_id);
      
        $('#edit_student_class_id').val(row.class_id).trigger('change');

        if (row.eng_student_gender == 'male') {
            $(document).find('#female').prop('checked', false);
            $(document).find('#male').prop('checked', true);
        } else {
            $(document).find('#male').prop('checked', false);
            $(document).find('#female').prop('checked', true);
        }

        setTimeout(() => {

            // Fill the Extra Field's Data
            if (row.extra_fields.length) {
                $.each(row.extra_fields, function (index, value) {

                    let fieldName = $.escapeSelector(value.form_field.name.replace(/ /g, '_'));

                    $(`#${fieldName}_id`).val(value.id);
                    if (value.form_field.default_values && value.form_field.default_values.length) {
                        $.each(value.form_field.default_values, function (key) {
                            if (typeof (value.data) == 'object') {
                                $.each(value.data, function (dataKey, dataValue) {
                                    let checked = ($('#' + fieldName + '_' + dataKey).val() == dataValue);
                                    $('#' + fieldName + '_' + dataKey).prop('checked', checked);
                                });
                            } else if (value.form_field.type == 'dropdown') {
                                $('#' + fieldName).val(value.data);
                            } else {
                                $('#' + fieldName + '_' + key).prop('checked', false);
                                // Check data is json format or not
                                if (isJSON(value.data)) { // Checkbox
                                    let chkArray = JSON.parse(value.data);
                                    $.each(chkArray, function (chkKey, chkValue) {
                                        if ($('#' + fieldName + '_' + key).val() == chkValue) {
                                            $('#' + fieldName + '_' + key).prop('checked', true);
                                        }
                                    })
                                } else {
                                    // Radio buttons
                                    let checked = ($('#' + fieldName + '_' + key).val() == value.data);
                                    $('#' + fieldName + '_' + key).prop('checked', checked);
                                }
                            }
                        });
                    } else {
                        if (value.form_field.type == 'file') {
                            if (value.data) {
                                $('#file_div_' + fieldName).removeClass('d-none').find('#file_link_' + fieldName).attr('href', value.file_url);
                            } else {
                                $('#file_div_' + fieldName).addClass("d-none").find('#file_link_' + fieldName).attr('href', "");
                            }
                        } else {
                            $('#' + fieldName).val(value.data);
                        }
                    }
                });
            } else {
                $('.text-fields').val('');
                $('.number-fields').val('');
                $('.select-fields').val('');
                $('.radio-fields').prop('checked', false);
                $('.checkbox-fields').prop('checked', false);
                $('.textarea-fields').val('');
                $('.file-div').addClass('d-none');
            }
        }, 1000);

        function isJSON(data) {
            try {
                JSON.parse(data);
                return true;
            } catch (error) {
                return false;
            }
        }

        // Guardian Data
        $(".edit-guardian-search").select2("trigger", "select", {
            data: {
                id: row.guardian_id || "",
                text: row.guardian.email || "",
                edit_data: true,
            }
        });

        //Adding delay to fill data so that select2 code and this code don't conflict each other
        setTimeout(function () {
            $('#edit_guardian_first_name').val(row.guardian.first_name);
            $('#edit_guardian_last_name').val(row.guardian.last_name);
            $('#edit_guardian_mobile').val(row.guardian.mobile);
            $('#edit-guardian-image-tag').attr('src', row.guardian.image);

        }, 500);
        if (row.eng_guardian_gender == 'male') {
            $(document).find('#edit-guardian-female').prop('checked', false);
            $(document).find('#edit-guardian-male').prop('checked', true);
        } else {
            $(document).find('#edit-guardian-male').prop('checked', false);
            $(document).find('#edit-guardian-female').prop('checked', true);
        }
    }, 'click .deactivate-student': function (e) {
        e.preventDefault();
        showDeletePopupModal($(e.currentTarget).attr('href'), {
            text: window.trans["You want to inactive the Student"],
            confirmButtonText: window.trans["Yes inactive"],
            cancelButtonText: window.trans["Cancel"],
            icon: 'question',
            successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
            }
        })
    }, 'click .activate-student': function (e) {
        e.preventDefault();
        showDeletePopupModal($(e.currentTarget).attr('href'), {
            text: window.trans["You want to Activate the Student"],
            confirmButtonText: window.trans["Yes Activate"],
            cancelButtonText: window.trans["Cancel"],
            icon: 'question',
            successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
            }
        })
    }
};

window.assignmentSubmissionEvents = {
    'click .edit-data': function (e, value, row) {
        let file_html = "";
        $('#edit_id').val(row.id);
        $('#assignment_name').val(row.assignment.name);
        $('#subject').val(row.assignment.class_subject.subject.name_with_type);
        $('#student_name').val(row.student.full_name);

        $.each(row.file, function (key, data) {
            file_html += " <a target='_blank' href='" + data.file_url + "'>" + data.file_name + "</a><br>";
        });

        $('#files').html(file_html);
        if (row.assignment.points) {
            $('#points_div').show();
            $('#assignment_points').text('/ ' + row.assignment.points);
            $('#points').prop('max', row.assignment.points);
            $('#points').val(row.points);
        } else {
            $('#points_div').hide();
            $('#assignment_points').text('');
        }
        $('#feedback').val(row.feedback);
        if (row.status === 1) {
            $('#status_accept').attr('checked', true);
        } else if (row.status === 2) {
            $('#points').val(null);
            $('#status_reject').attr('checked', true);
        }
    }
};

window.examResultEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id)
        $('.student_name').html(row.user.full_name);
        $('.subject_container').html('');

        $.each(row.user.exam_marks, function (key, data) {
            let html_data = '';
            if (data.timetable) {
                
            
            html_data = `<div class="row">` +
                `   <input type="hidden" id="marks_id form-control" readonly name="edit[` + key + `][marks_id]" value="` + data.id + `"/>` +
                `   <div class="row mx-2">` +
                `       <input type="hidden" id="marks_id form-control" readonly name="edit[` + key + `][exam_id]" value="` + data.timetable.exam_id + `"/>` +
                `       <div class="row mx-2">` +
                `           <input type="hidden" id="marks_id form-control" readonly name="edit[` + key + `][student_id]" value="` + row.student_id + `"/>` +
                `               <div class="row mx-2">` +
                `                   <input type="hidden" id="marks_id form-control" readonly name="edit[` + key + `][passing_marks]" value="` + data.timetable.passing_marks + `"/>` +
                `                       <div class="form-group col-sm-12 col-md-4">` +
                `                           <input type="text" class="subject_name form-control" readonly name="edit[` + key + `][subject_name]" value="` + data.subject[0].name_with_type + `" />` +
                `                       </div>` +
                `                       <div class="form-group col-sm-12 col-md-4">` +
                `                           <input type="text" class="total_marks form-control" readonly name="edit[` + key + `][total_marks]" value="` + data.timetable.total_marks + `" />` +
                `                        </div>` +
                `                        <div class="form-group col-sm-12 col-md-4">` +
                `                           <input type="number" max="` + data.timetable.total_marks + `" class="obtained_marks form-control" name="edit[` + key + `][obtained_marks]" value="` + data.obtained_marks + `" />` +
                `                       </div>` +
                `                </div>` +
                `           </div>` +
                `       </div>` +
                `   </div>`;
            }
            $('.subject_container').append(html_data);
        });
    }
};

window.FeesTypeActionEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_description').val(row.description);
    }
};

window.feesPaidEvents = {
    // 'click .compulsory-data': function (e, value, row) {
    
    //     const $document = $(document);
    //
    //     $document.find('.cheque-no').val(null);
    //     $document.find('.cheque-compulsory-mode').prop('checked', false);
    //     $document.find('.cash-compulsory-mode').prop('checked', true).trigger('change');
    //     $document.find('#installment-mode').val(0)
    //
    //     $('#compulsory-fees-id').val(row.fees.id);
    //     $('#student-id').val(row.student_id);
    //     $('#class-id').val(row.class_id);
    //
    //     $('.student-name').html(`${row.full_name} :- ${row.student.class_section.full_name}`);
    //     $('.paid-date').val(row.current_date);
    //     $document.find('.cheque_no').val(null);
    //
    //     if (row.fees.compulsory_fees.length) {
    //         $document.find('.mode-container').show(200);
    //         $('.compulsory-div').show();
    //         const feesTotalAmount = row.fees.total_compulsory_fees.toFixed(2);
    //
    //         const html = generateCompulsoryFeesTable(row.fees, row.fees_status);
    //
    //         $('#total-amount').val(feesTotalAmount);
    //         $('.compulsory-fees-content').html(html);
    //         $('.installment_rows').hide();
    //         $('#is-fully-paid').val(1)
    //
    //         // $('.installment-checkbox').on('click', function () {
    //         //     handleInstallmentClick($(this), feesTotalAmount);
    //         // });
    //
    //         // handlePayInInstallment($document, row);
    //     } else {
    //         $document.find('.mode-container').hide(200);
    //         $document.find('.compulsory-fees-payment').prop('disabled', true);
    //         $('.compulsory-div').hide();
    //     }
    // },

    'click .optional-data': function (e, value, row) {
        // Cache frequently used selectors
        const $document = $(document);
        const $optionalFeesPayment = $document.find('.optional_fees_payment');
        const $modeContainer = $document.find('.mode-container');
        const $chequeNo = $document.find('.cheque_no');
        const $cashMode = $('.cash_mode');
        const $optionalDiv = $('.optional_div');
        const $optionalFeesContent = $('.optional_fees_content');

        // Disable PAY Button
        $optionalFeesPayment.prop('disabled', true);

        // Add data in Modal
        $('#optional_fees_id').val(row.fees.id);
        $('#optional_student_id').val(row.student_id);
        $('#optional_class_id').val(row.class_id);
        $('.student_name').html(row.student_name + ' :- ' + row.class_name);
        $('.current-date').val(row.current_date);

        function showModeContainer() {
            $modeContainer.show(200)
            $chequeNo.val(null);
            $cashMode.prop('checked', true).change();
        }

        if (row.mode === 1 || (row.mode === 0 && (row.type_of_fee === 2 || row.type_of_fee === null))) {
            showModeContainer();
            $chequeNo.val(row.cheque_no);
        } else if (row.mode === 0) {
            $modeContainer.hide(200);
        }
        $modeContainer.hide(200);

        if (row.fees.optional_fees.length) {
            $optionalDiv.show();

            // Declare HTML using a template
            let html = '<table class="table"><tbody>';
            row.fees.optional_fees.forEach((value, index) => {
                html += '<tr>';
                if (value.is_paid) {
                    if (value.date) {
                        html += `<td scope="row" class="text-left">
                                    <span class="remove-optional-fees-paid text-left" data-id="${value.paid_id}">
                                        <i class="fa fa-times text-danger" style="cursor:pointer" aria-hidden="true"></i>
                                    </span>
                                </td>
                                <td colspan="2" class="text-left">${value.name}<br>
                                    <span class="text-small text-success">(${window.trans["paid_on"] + ' :- ' + value.date})</span>
                                </td>`;
                    }
                } else {
                    html += `<td scope="row" class="text-left">
                                <input type="checkbox" class="chkclass" id="optional-${index}" name="optional_data[${index}][id]" value="${value.id}" data-amount="${value.amount}">
                                <input type="hidden" value="${value.amount}" name="optional_data[${index}][amount]">
                                <input type="hidden" value="${value.id}" name="optional_data[${index}][fees_class_id]">
                            </td>
                            <td colspan="2" class="text-left"><label for="optional-${index}">${value.name}</lable></td>`;
                }
                html += `<td class="text-right">${value.amount.toFixed(2)}</td></tr>`;
            });

            html += `<tr class="optional-total-amount-row"><td></td><td colspan="2" class="text-left">${window.trans["total_amount"]}</td>
                        <td class="text-right"><strong><span class="optional_total_amount_label"></span>
                        </strong><input type="hidden" name="total_amount" class="optional_total_amount"></td></tr></tbody></table>`;

            // Update HTML and calculate the total amount
            $optionalFeesContent.html(html);
            $('.optional-total-amount-row').hide(200);

            let choiceAmount = 0;
            $('.chkclass').on('click', function () {
                choiceAmount += $(this).is(':checked') ? $(this).data("amount") : -$(this).data("amount");
                $('.optional_total_amount_label').html(choiceAmount.toFixed(2));
                $('.optional_total_amount').val(choiceAmount.toFixed(2));

                // Check if at least one checkbox is checked
                const anyCheckboxChecked = $('.chkclass:checked').length > 0;
                if (anyCheckboxChecked) {
                    $modeContainer.show(200);
                    $('.optional-total-amount-row').show(200);
                } else {
                    $modeContainer.hide(200);
                    $('.optional-total-amount-row').hide(200);
                }

                $optionalFeesPayment.prop('disabled', choiceAmount <= 1);

            });
        } else {
            $optionalDiv.hide();
        }
    },
};

window.onlineExamEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.online_exam_id);
        $('#edit-online-exam-title').val(row.title);
        $('#edit-online-exam-key').val(row.exam_key);
        $('#edit-online-exam-duration').val(row.duration);
        $('#edit-online-exam-start-date').val(row.start_date);
        $('#edit-online-exam-end-date').val(row.end_date);
    },
};
window.onlineExamQuestionsEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.online_exam_question_id);
        $('.edit_question_type').val(row.question_type);
        $('#edit-online-exam-class-id').val(row.class_id).trigger('change');

        //added the subject on class id after 0.5 seconds
        setTimeout(() => {
            $('#edit-online-exam-subject-id').val(row.subject_id).trigger('change');
        }, 1000);

        if (row.question_type) {
            $('.edit_question').html('')
            $('.edit_option_container').html('')
            // set data in question text area
            CKEDITOR.instances['edit_equestion'].setData(row.question_row)

            $('#edit-simple-question').hide(100)
            $('#edit-equation-question').show(300);
            $('.edit_eoption_container').html('')

            let html_option = '';
            $.each(row.options, function (index, value) {
                if (index >= 2) {
                    html_option += '<div class="form-group col-md-6"><input type="hidden" class="edit_eoption_id" name="edit_eoption[' + (index + 1) + '][id]" value=' + value.id + '><label>' + window.trans["option"] + ' <span class="edit-eoption-number">' + (index + 1) + '</span> <span class="text-danger">*</span></label><textarea class="editor_options" name="edit_eoption[' + (index + 1) + '][option]" placeholder="' + window.trans["Enter Option"] + '">' + value.option_row + '</textarea><div class="remove-edit-option-content"><button type="button" class="btn btn-inverse-danger remove-edit-option btn-sm mt-1" data-id="' + value.id + '"><i class="fa fa-times"></i></button></div></div>'
                    $('.edit_eoption_container').html(html_option);
                } else {
                    html_option += '<div class="form-group col-md-6"><input type="hidden" class="edit_eoption_id" name="edit_eoption[' + (index + 1) + '][id]" value=' + value.id + '><label>' + window.trans["option"] + ' <span class="edit-eoption-number">' + (index + 1) + '</span> <span class="text-danger">*</span></label><textarea class="editor_options" name="edit_eoption[' + (index + 1) + '][option]" placeholder="' + window.trans["Enter Option"] + '">' + value.option_row + '</textarea></div>'
                    $('.edit_eoption_container').html(html_option);
                }
            });
            createCkeditor();
        } else {
            $('#edit-equation-question').hide(100);
            $('#edit-simple-question').show(300);
            $('.edit_option_container').html('')

            $('.edit-question').html(row.question);
            // add options and add the options in answers
            let html = ''
            $.each(row.options, function (index, value) {
                if (index >= 2) {
                    html = '<div class="form-group col-md-6"><input type="hidden" class="edit_option_id" name="edit_options[' + (index + 1) + '][id]" value=' + value.id + '><label>' + window.trans["option"] + ' <span class="edit-option-number"> ' + (index + 1) + '</span> <span class="text-danger">*</span></label><input type="text" name="edit_options[' + (index + 1) + '][option]" value="' + value.option + '" placeholder="' + window.trans["Enter Option"] + '" class="form-control add-edit-question-option" /><div class="remove-edit-option-content"><button type="button" class="btn btn-inverse-danger remove-edit-option btn-sm mt-1" data-id="' + value.id + '"><i class="fa fa-times"></i></button></div></div>';
                } else {
                    html = '<div class="form-group col-md-6"><input type="hidden" class="edit_option_id" name="edit_options[' + (index + 1) + '][id]" value=' + value.id + '><label>' + window.trans["option"] + ' <span class="edit-option-number"> ' + (index + 1) + '</span> <span class="text-danger">*</span></label><input type="text" name="edit_options[' + (index + 1) + '][option]" value="' + value.option + '" placeholder="' + window.trans["Enter Option"] + '" class="form-control add-edit-question-option" /><div class="remove-edit-option-content"></div></div>';
                }
                $('.edit_option_container').append(html);
            });
        }
        $('.answers_db').html('');
        $('.edit_answer_select').html('');
        if (row.answers.length) {
            $.each(row.options, function (index, value) {
                $.each(row.answers, function (answer_index, answer_value) {
                    if (value.id == answer_value.option_id) {
                        if (row.answers.length == 1) {
                            let html = '<i class="fa fa-circle" aria-hidden="true"></i> ' + window.trans["option"] + ' ' + (index + 1) + '<br>';
                            $('.answers_db').append(html);
                            return false;
                        } else {
                            let html = '<i class="fa fa-circle" aria-hidden="true"></i> ' + window.trans["option"] + ' ' + (index + 1) + ' <span class="fa fa-times text-danger remove-answers" data-id=' + answer_value.id + ' style="cursor:pointer"></span><br>';
                            $('.answers_db').append(html);
                            return false;
                        }
                    }
                });
            });
        }

        if (row.options_not_answers) {
            $.each(row.options, function (index, value) {
                $.each(row.options_not_answers, function (answer_index, option_data) {
                    if (value.id == option_data.id) {
                        $('.edit_answer_select').append('<option value="' + (option_data.id) + '">' + window.trans["option"] + ' ' + (index + 1) + '</option>');
                        return false;
                    }
                });
            });
        }

        $('.edit_answer_select').ready(function () {
            if ($('.answers_db').html() == '') {
                $('.edit_answer_select').attr('required', true);
            } else {
                $('.edit_answer_select').removeAttr('required');
            }
        })
        $('#image_preview').attr('src', row.image);
        $('.edit_note').val(row.note);
    },
};
window.teacherEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#first_name').val(row.first_name);
        $('#last_name').val(row.last_name);
        $('input[name=gender][value=' + row.gender + '].edit').prop('checked', true);
        $('#current_address').val(row.current_address);
        $('#permanent_address').val(row.permanent_address);
        $('#email').val(row.email);
        $('#mobile').val(row.mobile);
        $('#edit-dob').val(moment(row.dob, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $('#qualification').val(row.staff.qualification);
        $('#edit-teacher-image-tag').attr('src', row.image);
        $('#edit_salary').val(row.staff.salary);
        $('#edit_joining_date').val(moment(row.staff.joining_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));


        setTimeout(() => {

            // Fill the Extra Field's Data
            if (row.extra_student_details.length) {
                $.each(row.extra_student_details, function (index, value) {
                    console.log('value :- ', value);

                    let fieldName = $.escapeSelector(value.form_field.name.replace(/ /g, '_'));

                    $(`#${fieldName}_id`).val(value.id);
                    if (value.form_field.default_values && value.form_field.default_values.length) {
                        $.each(value.form_field.default_values, function (key) {
                            if (typeof (value.data) == 'object') {
                                $.each(value.data, function (dataKey, dataValue) {
                                    let checked = ($('#' + fieldName + '_' + dataKey).val() == dataValue);
                                    $('#' + fieldName + '_' + dataKey).prop('checked', checked);
                                });
                            } else if (value.form_field.type == 'dropdown') {
                                $('#' + fieldName).val(value.data);
                            } else {
                                $('#' + fieldName + '_' + key).prop('checked', false);
                                // Check data is json format or not
                                if (isJSON(value.data)) { // Checkbox
                                    let chkArray = JSON.parse(value.data);
                                    $.each(chkArray, function (chkKey, chkValue) {
                                        if ($('#' + fieldName + '_' + key).val() == chkValue) {
                                            $('#' + fieldName + '_' + key).prop('checked', true);
                                        }
                                    })
                                } else {
                                    // Radio buttons
                                    let checked = ($('#' + fieldName + '_' + key).val() == value.data);
                                    $('#' + fieldName + '_' + key).prop('checked', checked);
                                }
                            }
                        });
                    } else {
                        if (value.form_field.type == 'file') {
                            if (value.data) {
                                $('#file_div_' + fieldName).removeClass('d-none').find('#file_link_' + fieldName).attr('href', value.file_url);
                            } else {
                                $('#file_div_' + fieldName).addClass("d-none").find('#file_link_' + fieldName).attr('href', "");
                            }
                        } else {
                            $('#' + fieldName).val(value.data);
                        }
                    }
                });
            } else {
                $('.text-fields').val('');
                $('.number-fields').val('');
                $('.select-fields').val('');
                $('.radio-fields').prop('checked', false);
                $('.checkbox-fields').prop('checked', false);
                $('.textarea-fields').val('');
                $('.file-div').addClass('d-none');
            }
        }, 1000);

        function isJSON(data) {
            try {
                JSON.parse(data);
                return true;
            } catch (error) {
                return false;
            }
        }
    }, 'click .deactivate-teacher': function (e) {
        e.preventDefault();
        showSweetAlertConfirmPopup($(e.currentTarget).attr('href'), 'PUT', {
            text: window.trans["You want to inactive the Teacher"],
            confirmButtonText: window.trans["Yes inactive"],
            icon: 'question',
            successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
            }
        })
    }, 'click .activate-teacher': function (e) {
        e.preventDefault();
        showSweetAlertConfirmPopup($(e.currentTarget).attr('href'), 'PUT', {
            text: window.trans["You want to Activate the Teacher"],
            confirmButtonText: window.trans["Yes Activate"],
            icon: 'question',
            successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
            }
        })
    }
}

window.sliderEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('.edit_link').val(row.link);

        setTimeout(() => {
            $('input[name=type][value=' + row.type + '].edit_type').prop('checked', true);
        }, 500);

        // Select the file input field
        let fileInput = $('.edit_image');

        // Clear the selected file by resetting the input value
        fileInput.val(null);

        // Update the text input field to display "No file selected"
        fileInput.siblings('.form-control').val('');

        $('#edit_slider_image').attr('src', row.image);
    }
}
// window.classEvents = {
//     'click .edit-data': function (e, value, row) {
//         //Reset the Checkbox and Radio Button
//         $('input[name="section_id[]"].edit').prop('checked', false).off('click');
//         $('input[name="section_id[]"].edit').parent().siblings('a').hide();
//         $('input[name=medium_id].edit').prop('checked', false);
//
//         $('#edit_id').val(row.id);
//         $('#edit_name').val(row.name);
//         $('input[name=medium_id][value=' + row.medium_id + '].edit').prop('checked', true);
//
//         row.sections.forEach(function (data) {
//             let checkBox = $('input[name="section_id[]"][value=' + data.id + '].edit');
//             checkBox.prop('checked', true).on('click', () => false);
//             let anchor = checkBox.parent().siblings('a');
//             anchor.show();
//             anchor.attr('href', anchor.attr('href') + '/' + data.pivot.id);

//         });
//     }
// };

window.schoolEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_school_name').val(row.name);
        $('#edit-school-logo-tag').attr('src', row.logo);
        $('#edit_school_support_email').val(row.support_email);
        $('#edit_school_support_phone').val(row.support_phone);
        $('#edit_school_address').val(row.address);
        $('#edit_school_tagline').val(row.tagline);


        // set the school url based on the domain type
        $('.school_url').attr('href', '');
        $('.school_url').text('');
        if (row.school_domain && row.school_url) {
            if(row.domain_type == "default") {
                $('.school_url').attr('href', row.school_url);
                $('.school_url').text(row.school_url);
            } else {
                $('.school_url').attr('href', row.school_url);
                $('.school_url').text(row.school_url);
            }
        } else {
            $('.school_url').attr('href', '');
            $('.school_url').text('');
        }
      
        $('#school_code').val(row.code);

        
        if(row.domain_type == "default")
        {
            $('.edit_default').prop('checked',true);

            $('.defaultDomain').show().find('input').prop('disabled', false);
            $('.customDomain').hide().find('input').prop('disabled', true);

            $('#edit_custom_domain').val('');
            $('#edit_default_domain').val(row.domain);

        }else if(row.domain_type == "custom"){
            $('.edit_custom').prop('checked',true);

            $('.customDomain').show().find('input').prop('disabled', false);
            $('.defaultDomain').hide().find('input').prop('disabled', true);

            $('#edit_default_domain').val('');
            $('#edit_custom_domain').val(row.domain);

        }else{
            $('.edit_default').prop('checked',false);
            $('.edit_custom').prop('checked',false);
            $('#edit_custom_domain').val('');
            $('#edit_default_domain').val('');
            $('.defaultDomain').hide().find('input').prop('disabled', true);
            $('.customDomain').hide().find('input').prop('disabled', true);

        }

        // Hide the school url if the default domain is not set
        if($('#edit_default_domain').val()) {
            $('#school_url').hide();
        } else {
            $('#school_url').show();
        }
        
        if (row.active_plan == '-') {
            $('#edit_assign_package_container').show();
            $('#edit_assign_package').attr('disabled', false);
        } else {
            $('#edit_assign_package_container').hide();
            $('#edit_assign_package').attr('disabled', true);
        }

        setTimeout(() => {

            // Fill the Extra Field's Data
            if (row.extra_fields.length) {
                $.each(row.extra_fields, function (index, value) {
                    let fieldName = $.escapeSelector(value.form_field.name.replace(/ /g, '_'));

                    $(`#edit_${fieldName}_id`).val(value.id || '');

                    if (value.form_field.default_values && value.form_field.default_values.length) {
                        $.each(value.form_field.default_values, function (key) {
                            if (typeof (value.data) == 'object') {
                                $.each(value.data, function (dataKey, dataValue) {
                                    let checked = ($('#' + fieldName + '_' + dataKey).val() == dataValue);
                                    $('#edit_' + fieldName + '_' + dataKey).prop('checked', checked);
                                });
                            } else if (value.form_field.type == 'dropdown') {
                                $('#edit_' + fieldName).val(value.data);
                            } else {
                                $('#edit_' + fieldName + '_' + key).prop('checked', false);
                                // Check data is json format or not
                                if (isJSON(value.data)) { // Checkbox
                                    let chkArray = JSON.parse(value.data);
                                    $.each(chkArray, function (chkKey, chkValue) {
                                        if ($('#edit_' + fieldName + '_' + key).val() == chkValue) {
                                            $('#edit_' + fieldName + '_' + key).prop('checked', true);
                                        }
                                    })
                                } else {
                                    // Radio buttons
                                    let checked = ($('#' + fieldName + '_' + key).val() == value.data);
                                    $('#edit_' + fieldName + '_' + key).prop('checked', checked);
                                }
                            }
                        });
                    } else {
                        if (value.form_field.type == 'file') {
                            if (value.data) {
                                var file_url = value.data;
                                var storage_url = window.location.origin + "/storage/" + file_url;
                        
                                $('#edit_file_div_' + fieldName).removeClass('d-none').find('#edit_file_link_' + fieldName).attr('href', storage_url);
                                $('#edit_' + fieldName).removeAttr('required');
                            } else {
                                $('#edit_file_div_' + fieldName).addClass("d-none").find('#edit_file_link_' + fieldName).attr('href', "");
                                $('#edit_' + fieldName).attr('required', 'required');
                            }
                        } else {
                            $('#edit_' + fieldName).val(value.data);
                        }
                    }
                });
            } else {
                $('.text-fields').val('');
                $('.number-fields').val('');
                $('.select-fields').val('');
                $('.radio-fields').prop('checked', false);
                $('.checkbox-fields').prop('checked', false);
                $('.textarea-fields').val('');
                $('.file-div').addClass('d-none');
                $('.edit_extra_fields_id').val('');
            }
        }, 1000);

        function isJSON(data) {
            try {
                JSON.parse(data);
                return true;
            } catch (error) {
                return false;
            }
        }
    },
    'click .update-admin-data': function (e, value, row) {
        $('#edit_school_id').val(row.id);

        $('#edit_admin_id').val(row.user.id);
        $('#edit-admin-email').val(row.user.email);
        $('#edit-admin-first-name').val(row.user.first_name);
        $('#edit-admin-last-name').val(row.user.last_name);
        $('#edit-admin-contact').val(row.user.mobile);
        $("#admin-image-tag").attr('src', row.user.image);

        // Check if the school admin email is verified
        if (row.user.email_verified_at) {
            $('#manually_verify_email').prop('checked', true);
            $('#manually_verify_email').prop('disabled', true);
        } else {
            $('#manually_verify_email').prop('checked', false);
            $('#manually_verify_email').prop('disabled', false);
        }
        console.log(row.user.two_factor_enabled);
        
        if (row.user.two_factor_enabled == 1) {
            $('#two_factor_verification').prop('checked', true);
        } else {
            $('#two_factor_verification').prop('checked', false);
        }
        // $('#edit-admin-first-name').val(row.id);


        // $(".edit-admin-search").select2("destroy").select2();

        // // School admin Data
        // setTimeout(() => {
        //     $(".edit-school-admin-search").select2("trigger", "select", {
        //         data: {
        //             id: row.user.id ? row.user.id : "",
        //             text: row.user.email ? row.user.email : "",
        //             edit_data: true,
        //         }
        //     });
        // }, 500);
        // setTimeout(() => {
        //     $('#edit_admin_email').val(row.user.id);

        //     $(".edit-admin-search").val("").trigger("change");
        //     $('#edit-admin-first-name').removeAttr('readonly').val(row.user.first_name);
        //     $('#edit-admin-last-name').removeAttr('readonly').val(row.user.last_name);
        //     $('#edit-admin-contact').removeAttr('readonly').val(row.user.mobile);
        //     $("#admin-image-tag").attr('src', row.user.image);
        // }, 1000);
    }
};

window.packageEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);

    }
};

window.formFieldsEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit-id').val(row.id);
        $('#edit-name').val(row.name);
        $('#edit-type-field-value').val(row.type);
        $('#edit-type-select').val(row.type).trigger('change').attr('disabled', true);
        (row.is_required) ? $('#customSwitch2').prop('checked', true).change() : $('#customSwitch2').prop('checked', false).change();

        if (row.type == 'dropdown' || row.type == 'radio' || row.type == 'checkbox') {
            if (row.default_values.length >= 3) {
                $('.add-new-edit-option').click();
            }

            let dataArray = [];

            $.each(row.default_values, function (index, value) {
                dataArray.push({'option': value});
            });
            editDefaultValuesRepeater.setList(dataArray);
            $(function () {
                editToggleAccessOfDeleteButtons();
            });
        }
    }
};
window.holidayEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#edit-date').val(moment(row.date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $('#edit-title').val(row.title);
        $('#edit-description').val(row.description);
    }
};


window.galleryEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#edit-title').val(row.title);
        $('#edit-description').val(row.description);
        $('#edit_session_year_id').val(row.session_year_id);
        $('#edit-thumbnail').attr('src',row.thumbnail);
    }
};


window.faqsEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#edit-title').val(row.title);
        $('#edit-description').val(row.description);
    }
};

window.guidanceEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#edit-name').val(row.name);
        $('#edit-link').val(row.link);
    }
};

window.leaveEvents = {
    'click .edit-data': function (e, value, row) {
        let html_file = '';
        $('#id').val(row.id);
        $('#edit_from_date').val(moment(row.from_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $('#edit_to_date').val(moment(row.to_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $('#edit_reason').val(row.reason);

        if (row.file) {
            $.each(row.file, function (key, data) {
                html_file += '<div class="file"><a target="_blank" href="' + data.file_url + '" class="m-1">' + data.file_name + '</a></span><br><br></div>'
            })

            $('#attachment').html(html_file);
        }

        setTimeout(() => {
            $('#edit_to_date').trigger('change');
        }, 500);
        setTimeout(() => {
            $.each(row.leave_detail, function (index, value) {
                $('input[name="type[' + moment(value.date, 'YYYY-MM-DD').format('DD-MM-YYYY') + '][]"][value="' + value.type + '"].form-check-input').prop('checked', true);
            });
        }, 500);


        $('input[name=status][value=' + row.status + '].leave-status').prop('checked', true);
    }
};

window.sessionYearEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit-name').val(row.name);
        $('#edit-start-date').val(moment(row.start_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $('#edit-end-date').val(moment(row.end_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
    }, 'click .default-session-year': function (e, value, row) {
        e.preventDefault();
        let url = $(e.currentTarget).attr('href');
        showSweetAlertConfirmPopup(url, 'PUT', {
            text: window.trans["You want to Change the Current Session Year"],
            successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
                $('#sessionYearNameHeader').text(row.name);
            }
        })
        // ajaxRequest('PUT', url, null, null, function () {
        //     $('#table_list').bootstrapTable('refresh');
        // });
    },
};

window.languageSettingsEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_name').val(row.name);
        $('#edit_code').val(row.code);
        if (row.is_rtl) {
            $('#edit_rtl').prop('checked', true); // set CheckBox True
        } else {
            $('#edit_rtl').prop('checked', false); // set CheckBox False
        }
    },
    'click .change-default-lang': function (e, value, row) {
        e.preventDefault();
        let id = row.id
        Swal.fire({
            title: window.trans["Are you sure"],
            text: window.trans["change_default_language"],
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: window.trans["Yes"],
            cancelButtonText: window.trans["Cancel"]
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/language/default/' + id;
                let data = null;

                function successCallback(response) {
                    setTimeout(() => {
                        $('#table_list').bootstrapTable('refresh');
                    }, 500);
                    showSuccessToast(response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('PUT', url, data, null, successCallback, errorCallback);
            }
        })
    }
};

window.lessonEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
    }
};

window.leaveSettingsEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#edit_leaves').val(row.leaves);
        $('#edit_session_year_id').val(row.session_year_id);
        let holidays = row.holiday;
        holidays = holidays.split(",");
        $('#edit_holiday_days').val(holidays).trigger('change');
    }
};


window.subscriptionExpiryEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#school_id').val(row.school_id);
        $('.expiry-date').val(moment(row.end_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        let today = new Date();
        let minDate = new Date();
        minDate.setDate(today.getDate());
        $('.expiry-date').datepicker({
            enableOnReadonly: false,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            startDate: minDate,
            rtl: isRTL()
        });

    },
    'click .change-bill': function (e, value, row) {
        $('#due_bill_id').val(row.subscription_bill_id);
        $('#due_bill_school_id').val(row.school_id);
        $('.due-date').val(moment(row.due_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        let today = new Date();
        let minDate = new Date();
        minDate.setDate(today.getDate());
        $('.due-date').datepicker({
            enableOnReadonly: false,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            startDate: minDate,
            rtl: isRTL()
        });

    },
    'click .update-current-plan': function (e, value, row) {
        $('#current_plan_id').val(row.id);
        $('#update_package_id').val(row.package_id);
    },
    'click .stop-auto-renewal-plan': function (e, value, row) {
        e.preventDefault();
        let id = row.id
        Swal.fire({
            title: window.trans["Are you sure"],
            text: window.trans["Agree to This Subscription"],
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: window.trans["Yes"],
            cancelButtonText: window.trans["Cancel"]
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/subscriptions/' + id;
                let data = null;

                function successCallback(response) {
                    setTimeout(() => {
                        $('#table_list').bootstrapTable('refresh');
                    }, 500);
                    showSuccessToast(response.message);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
            }
        })
    },
    'click .generate-bill': function (e, value, row) {
        e.preventDefault();
        let id = row.id
        Swal.fire({
            title: window.trans["Are you sure"],
            text: window.trans["generate_bill"],
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: window.trans["Yes"],
            cancelButtonText: window.trans["Cancel"]
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/subscriptions/generate-bill/' + id;
                let data = null;

                function successCallback(response) {
                    setTimeout(() => {
                        $('#table_list').bootstrapTable('refresh');
                    }, 500);
                    showSuccessToast(response.message);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('get', url, data, null, successCallback, errorCallback);
            }
        })
    }


};


window.lessonTopicEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
    }
};

window.staffEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_first_name').val(row.first_name);
        $('#edit_last_name').val(row.last_name);
        $('#edit_mobile').val(row.mobile);
        $('#edit_email').val(row.email);
        $('#edit_salary').val(row.staff.salary);
        $('#edit_school_id').val(row.support_school_id).trigger('change');
        $('#edit_role_id').val(row.roles[0].id);
        $('#edit_staff_image').attr('src',row.image);
        $('.edit-dob').val(moment(row.dob, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        
        $('#edit_joining_date').val(moment(row.staff.joining_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));

        setTimeout(() => {
            // Fill the Extra Field's Data
            if (row.extra_fields.length) {
                $.each(row.extra_fields, function (index, value) {
                    let fieldName = $.escapeSelector(value.form_field.name.replace(/ /g, '_'));

                    $(`#edit_${fieldName}_id`).val(value.id || '');
                    if (value.form_field.default_values && value.form_field.default_values.length) {
                        $.each(value.form_field.default_values, function (key) {
                            if (typeof (value.data) == 'object') {
                                $.each(value.data, function (dataKey, dataValue) {
                                    let checked = ($('#' + fieldName + '_' + dataKey).val() == dataValue);
                                    $('#edit_' + fieldName + '_' + dataKey).prop('checked', checked);
                                });
                            } else if (value.form_field.type == 'dropdown') {
                                $('#edit_' + fieldName).val(value.data);
                            } else {
                                $('#edit_' + fieldName + '_' + key).prop('checked', false);
                                // Check data is json format or not
                                if (isJSON(value.data)) { // Checkbox
                                    let chkArray = JSON.parse(value.data);
                                    $.each(chkArray, function (chkKey, chkValue) {
                                        if ($('#edit_' + fieldName + '_' + key).val() == chkValue) {
                                            $('#edit_' + fieldName + '_' + key).prop('checked', true);
                                        }
                                    })
                                } else {
                                    // Radio buttons
                                    let checked = ($('#' + fieldName + '_' + key).val() == value.data);
                                    $('#edit_' + fieldName + '_' + key).prop('checked', checked);
                                }
                            }
                        });
                    } else {
                        if (value.form_field.type == 'file') {
                            if (value.data) {
                                var file_url = value.data;
                                var storage_url = window.location.origin + "/storage/" + file_url;
                        
                                $('#edit_file_div_' + fieldName).removeClass('d-none').find('#edit_file_link_' + fieldName).attr('href', storage_url);
                        
                                $('#edit_' + fieldName).removeAttr('required');
                            } else {
                                $('#edit_file_div_' + fieldName).addClass("d-none").find('#edit_file_link_' + fieldName).attr('href', "");
                        
                                $('#edit_' + fieldName).attr('required', 'required');
                            }
                        } else {
                            $('#edit_' + fieldName).val(value.data);
                        }        
                    }
                });
            } else {
                $('.text-fields').val('');
                $('.number-fields').val('');
                $('.select-fields').val('');
                $('.radio-fields').prop('checked', false);
                $('.checkbox-fields').prop('checked', false);
                $('.textarea-fields').val('');
                $('.file-div').addClass('d-none');
                $('.edit_extra_fields_id').val('');
            }
        }, 1000);

        function isJSON(data) {
            try {
                JSON.parse(data);
                return true;
            } catch (error) {
                return false;
            }
        }
        
    }, 'click .deactivate-staff': function (e) {
        e.preventDefault();
        showSweetAlertConfirmPopup($(e.currentTarget).attr('href'), 'DELETE', {
            text: window.trans["You want to inactive the Staff"],
            confirmButtonText: window.trans["Yes inactive"],
            icon: 'question',
            successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
            }
        })
    }, 'click .activate-staff': function (e) {
        e.preventDefault();
        showSweetAlertConfirmPopup($(e.currentTarget).attr('href'), 'PUT', {
            text: window.trans["You want to Activate the Staff"],
            confirmButtonText: window.trans["Yes Activate"],
            icon: 'question',
            successCallBack: function () {
                $('#table_list').bootstrapTable('refresh');
            }
        })
    }
};

window.feesEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit-id').val(row.id);
        $('#edit-name').val(row.name);
        $('#edit-due-date').val(moment(row.due_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        $('#edit-due-charges').val(row.due_charges);

        if (row.include_fee_installments == 1) {
            $('.edit-fees-installment-repeater').show(200)
        } else {
            $('.edit-fees-installment-repeater').hide(200)
        }
        if (row.installment_data) {
            let installmentData = [];
            $.each(row.installment_data, function (index, value) {
                installmentData.push({
                    id: value.id,
                    name: value.name,
                    due_date: value.due_date,
                    due_charges: value.due_charges
                });
            });
            addNewEditInstallmentData.setList(installmentData);
        }
    }
};

window.semesterEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit-name').val(row.name);
        $('#edit-start-month').val(row.start_month);
        $('#edit-end-month').val(row.end_month);

    }
};

window.streamEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
    }
};

window.shiftEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_name').val(row.name);
        $('#edit_start_time').val(row.start_time);
        $('#edit_end_time').val(row.end_time);
        $('input[name="status"][value="' + row.status + '"]').attr('checked', 'checked');
    }
};
window.subscriptionEvents = {
    'click .edit-data': function (e, value, row) {
        $('#edit_id').val(row.id);
        $('.subscription_id').val(row.id);
        
        
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        $('.plan-name').html(row.subscription.name);
        let start_date = new Date(row.subscription.start_date);
        start_date = new Intl.DateTimeFormat('en-US', options).format(start_date);

        let end_date = new Date(row.subscription.end_date);
        end_date = new Intl.DateTimeFormat('en-US', options).format(end_date);
        $('.billing_cycle').html(start_date + ' - ' + end_date);

        let package_type = row.subscription.package_type;
        
        let total_user_charges = 0;
        if (package_type == 1) {
            $('.postpaid-table').removeClass('d-none');
            $('.package-type').html(window.trans['postpaid']);
            $('.package-type').removeClass('badge-info');
            $('.package-type').addClass('badge-primary');

            $('.prepaid-table').addClass('d-none');
            $('.prepaid-package-info').addClass('d-none');
            $('.postpaid-package-info').removeClass('d-none');

        } else {
            $('.prepaid-table').removeClass('d-none');
            $('.package-type').html(window.trans['prepaid']);
            $('.package-type').addClass('badge-info');
            $('.package-type').removeClass('badge-primary');

            $('.postpaid-table').addClass('d-none');
            $('.prepaid-package-info').removeClass('d-none');
            $('.postpaid-package-info').addClass('d-none');
        }

        if (package_type == 1) {
            let student_charges = ((row.subscription.student_charge / row.subscription.billing_cycle) * row.usage_days).toFixed(4);
            let staff_charges = ((row.subscription.staff_charge / row.subscription.billing_cycle) * row.usage_days).toFixed(4);


            $('.total-student').html(row.total_student);
            $('.student-charge').html(student_charges);
            $('.total-student-charge').html((row.total_student * student_charges).toFixed(2));

            $('.total-staff').html(row.total_staff);
            $('.staff-charge').html(staff_charges);
            $('.total-staff-charge').html((row.total_staff * staff_charges).toFixed(2));

            total_user_charges = (row.total_student * student_charges) + (row.total_staff * staff_charges);
            $('.total-user-charges').html(formatMoney(parseFloat(total_user_charges)));
        } else {
            $('.package_amount').html(formatMoney(parseFloat(row.subscription.charges)));
        }
        
        let html = '';
        let total_addon = 0;
        let prepaid_total_addon_charges = 0;
        
        if (row.subscription.package_type == 1) {
            // Postpaid
            $.each(row.addons, function (index, value) {
                html += '<tr>';
                html += '<td colspan="4">' + window.trans[value.feature.name] + '</td>';
                html += '<td class="text-right">' + amountFormatter(value.price) + '</td>';
                html += '</tr>';
                total_addon += parseFloat(value.price);
            });
            html += '<tr>';
            html += '<th colspan="4">' + window.trans['total_addon_charges'] + ' :</th>';
            html += '<th class="text-right">' + row.currency_symbol + ' ' + amountFormatter(total_addon, null) + '</th>';
            html += '</tr>';
    
            html += '<tr>';
            html += '<th colspan="4">' + window.trans['Total User Charges'] + ' :</th>';
            html += '<th class="text-right">' + row.currency_symbol + ' ' + formatMoney(total_user_charges) + '</th>';
            html += '</tr>';
    
            html += '<tr>';
            html += '<th colspan="4">' + window.trans['total_bill_amount'] + ' :</th>';
            let total_amount = (total_addon + total_user_charges);
            total_amount = Math.ceil(total_amount * 100) / 100;
            
            
            html += '<th class="text-right">' + row.currency_symbol + ' ' + (row.amount) + '</th>';
            
            html += '</tr>';
    
            if (row.amount < row.default_amount) {
                html += '<tr class="total_paidable_amount">';
                html += '<th colspan="4">' + window.trans['total_paidable_amount'] + ' :</th>';
                html += '<th class="text-right">' + row.currency_symbol + ' ' + row.default_amount + '</th>';
                html += '</tr>';
            }
    
    
            setTimeout(() => {
                $('.postpaid-addon-charges').html(html);
            }, 500);
            // End Postpaid
        } else {
            // Prepaid
            $.each(row.addons, function (index, value) {
                html += '<tr>';
                html += '<td colspan="2">' + value.feature.name + '</td>';
                if (row.subscription.package_type == 0) {
                    if (value.transaction != null) {
                        html += '<td>'+ value.transaction.order_id ?? null +'</td>';
                        if (value.transaction.payment_status == "succeed") {
                            html += '<td>'+ window.trans['Success'] +'</td>';
                            prepaid_total_addon_charges += parseFloat(value.price);
                        } else {
                            html += '<td>'+ value.transaction.payment_status +'</td>';
                        }
                    } else {
                        html += '<td></td>';
                        html += '<td>'+ window.trans['failed'] +'</td>';
                    }
                }
                
                
                html += '<td class="text-right">' + amountFormatter(value.price) + '</td>';
                html += '</tr>';
            });
            html += '<tr>';
            html += '<th colspan="4">' + window.trans['total_addon_charges'] + ' :</th>';
            html += '<th class="text-right">' + row.currency_symbol + ' ' + amountFormatter(prepaid_total_addon_charges, null) + '</th>';
            html += '</tr>';

            html += '<tr>';
            html += '<th colspan="4">' + window.trans['package_amount'] + ' :</th>';
            html += '<th class="text-right">' + row.currency_symbol + ' ' + amountFormatter(row.subscription.charges, null) + '</th>';
            html += '</tr>';
    
            html += '<tr>';
            html += '<th colspan="4">' + window.trans['total_bill_amount'] + ' :</th>';
            let total_amount = (total_addon + total_user_charges);
            total_amount = Math.ceil(total_amount * 100) / 100;
        
            html += '<th class="text-right">' + row.currency_symbol + ' ' + amountFormatter((parseFloat(row.subscription.charges) + parseFloat(prepaid_total_addon_charges))) + '</th>';
        
            html += '</tr>';
    
            if (row.amount < row.default_amount) {
                html += '<tr class="total_paidable_amount">';
                html += '<th colspan="4">' + window.trans['total_paidable_amount'] + ' :</th>';
                html += '<th class="text-right">' + row.currency_symbol + ' ' + row.default_amount + '</th>';
                html += '</tr>';
            }
    
    
            setTimeout(() => {
                $('.prepaid-addon-charges').html(html);
            }, 500);
            // End Prepaid
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                "amount": (total_addon + total_user_charges),
            };
            
            var rzp1 = new Razorpay(options);
            document.getElementByClassName('razorpay-payment-button').onclick = function(e){
                rzp1.open();
                e.preventDefault();
            }

        });


        if (row.subscription.package_type == 1) {
            if (row.payment_status == 'succeed' || (total_addon + total_user_charges) == 0) {
                $('.payment-status').hide();
            } else {
                $('.payment-status').show();
            }    
        } else {
            if (row.payment_status == 'succeed' || row.amount == 0) {
                $('.payment-status').hide();
            } else {
                $('.payment-status').show();
            }
        }

        let bill_amount = parseFloat(total_addon + total_user_charges);
        $('#bill_amount').val(bill_amount.toFixed(2));
        $('.bill_amount').val(bill_amount.toFixed(2));
        
        
    }
};

window.tableDescriptionEvents = {
    'click .bootstrap-table-description': function (e, value, row) {
        $('.modal-title').html(row.name);
        $('.modal-title').html(row.title);
        $('.description-data').html(row.instructions);
        $('.description-data').html(row.description);
        $('.description-data').html(row.reason);
        $('.description-data').html(row.message);
    }
};

window.paryollSettingsEvents = {
    'click .edit-data': function (e, value, row) {
        $('#amount-div').hide();
        $('#percentage-div').hide();

        $('#edit_id').val(row.id);
        $('#name').val(row.name);
        $("input[name='type'][value='"+row.type+"']").prop("checked", true);
        if(row.amount != null)
        {
            $('#amount-div').show();
            $('#amount').val(row.amount);
        }

        if(row.percentage != null)
            {
                $('#percentage-div').show();
                $('#percentage').val(row.percentage);
            }
        
    }
};

window.deductionEvents = {
    'click .edit-data': function (e, value, row) {
        $('#amount-div').hide();
        $('#percentage-div').hide();

        $('#edit_id').val(row.id);
        $('#name').val(row.name);

        if(row.amount != null)
        {
            $('#amount-div').show();
            $('#amount').val(row.amount);
        }

        if(row.percentage != null)
            {
                $('#percentage-div').show();
                $('#percentage').val(row.percentage);
            }
        
    }
};

window.schoolInquiryEvents = {
    'click .edit-data': function (e, value, row) {
        
       // Reset the radio button
       $('input[name=application_status]').prop('checked', false);
    
        $('#edit_school_id').val(row.id);
        $('#edit_school_name').val(row.school_name);
        $('#edit_school_support_email').val(row.school_email);
        $('#edit_school_support_phone').val(row.school_phone);
        $('#edit_school_address').val(row.school_address);
        $('#edit_school_tagline').val(row.school_tagline);
        $('input[name=status][value=' + row.status + ']').prop('checked', true);


        setTimeout(() => {

            // Fill the Extra Field's Data
            if (row.extra_fields.length) {
                $.each(row.extra_fields, function (index, value) {

                    let fieldName = $.escapeSelector(value.form_field.name.replace(/ /g, '_'));

                    $(`#${fieldName}_id`).val(value.id);
                    if (value.form_field.default_values && value.form_field.default_values.length) {
                        $.each(value.form_field.default_values, function (key) {
                            if (typeof (value.data) == 'object') {
                                $.each(value.data, function (dataKey, dataValue) {
                                    let checked = ($('#' + fieldName + '_' + dataKey).val() == dataValue);
                                    $('#' + fieldName + '_' + dataKey).prop('checked', checked);
                                });
                            } else if (value.form_field.type == 'dropdown') {
                                $('#' + fieldName).val(value.data);
                            } else {
                                $('#' + fieldName + '_' + key).prop('checked', false);
                                // Check data is json format or not
                                if (isJSON(value.data)) { // Checkbox
                                    let chkArray = JSON.parse(value.data);
                                    $.each(chkArray, function (chkKey, chkValue) {
                                        if ($('#' + fieldName + '_' + key).val() == chkValue) {
                                            $('#' + fieldName + '_' + key).prop('checked', true);
                                        }
                                    })
                                } else {
                                    // Radio buttons
                                    let checked = ($('#' + fieldName + '_' + key).val() == value.data);
                                    $('#' + fieldName + '_' + key).prop('checked', checked);
                                }
                            }
                        });
                    } else {
                        if (value.form_field.type == 'file') {
                            if (value.data) {
                                $('#file_div_' + fieldName).removeClass('d-none').find('#file_link_' + fieldName).attr('href', value.file_url);
                            } else {
                                $('#file_div_' + fieldName).addClass("d-none").find('#file_link_' + fieldName).attr('href', "");
                            }
                        } else {
                            $('#' + fieldName).val(value.data);
                        }
                    }
                });
            } else {
                $('.text-fields').val('');
                $('.number-fields').val('');
                $('.select-fields').val('');
                $('.radio-fields').prop('checked', false);
                $('.checkbox-fields').prop('checked', false);
                $('.textarea-fields').val('');
                $('.file-div').addClass('d-none');
            }
        }, 1000);

        function isJSON(data) {
            try {
                JSON.parse(data);
                return true;
            } catch (error) {
                return false;
            }
        }
    },
};    