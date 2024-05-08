<script>
    $(document).ready(function() {
        $('.select_2').select2();
        flatpickr('.date_picker_posting', {
            dateFormat: "d-m-Y"
        })
    });
    $(".form_submit").submit(function(e) {
        // console.log("form submit called");
        e.preventDefault();
        var customConfig = {
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
        };
        var formData = new FormData(this);
        showSweetLoader();
        customAjaxCall(function(res) {
            customSweetAlert(function(result) {
                    // console.log("result", result);
                    if (result.isDismissed == true && result.dismiss != 'timer') {

                        console.log("here");
                        location.reload(true);
                    } else {
                        if (res.redirectTo == 'close' && res.call != '') {
                            // console.log(res.call);
                            closeSweetLoader();
                            window[res.call]();
                            // call(res.call);
                            $('.form_submit').trigger('reset');
                            // console.log("called", res.call);
                        } else if(res.redirectTo == 'print'){
                            commonPrint(res.url, res.id, 1);
                            // location.reload(true);
                        }
                        else if (res.redirectTo == 'close') {
                            closeSweetLoader();
                        } else if (res.redirectTo == 'closeAndModalHide') {
                            closeSweetLoader();
                            $('.modal').modal('hide');
                        } else if (res.redirectTo == 'reload') {
                            location.reload();
                        } else {
                            closeSweetLoader();
                            if (typeof res.newTab != 'undefined') {
                                window.open(res.redirectTo);
                                location.reload(true);
                            } else {
                                // console.log("here");
                                // console.log(res.redirectTo);
                                window.location.href = res.redirectTo;
                            }

                        }
                        form_submit_reset();
                    }
                }, 'success', res.message, res.description, res.buttonShow, null, res.timer, res
                .cancelButton);
        }, 'POST', $(this).attr('action'), formData, customConfig);
    });
    function showSweetLoader(title = "Loading", html = "Please Wait") {
        Swal.fire({
            title: title,
            html: html,
            didOpen: () => {
                Swal.showLoading()
            }
        })
    }
    function closeSweetLoader() {
        Swal.close();
    }
    function customSweetAlert(callback, icon = 'success', title = 'Added', html = 'Data Inserted Successfully',
        showConfirmButton = false, position = 'center', timer = null, showCancelButton = false) {
        // setTimeout(function() {
        Swal.fire({
            icon: icon,
            title: title,
            html: html,
            showConfirmButton: showConfirmButton,
            showCancelButton: showCancelButton,
            position: position,
            timer: timer,
        }).then((result) => {
            callback(result);
        });
    }
    async function customAjaxCall(callback, method, url, data, customConfig) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'accept': 'application/json',
            }
        });

        var defConfig = {
            type: method,
            dataType: 'json',
            url: url,
            data: data,
            success: function(data) {
                callback(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                callback(-1);
                if (jqXHR.status === 0) {
                    customSweetAlert(function() {
                        // console.log(textStatus);
                    }, 'error', 'Oppps!', 'Not connect. Verify Network.', true, null, null);
                } else if (jqXHR.status == 404) {
                    customSweetAlert(function() {
                        // console.log(textStatus);
                    }, 'error', 'Oppps!', 'Requested page not found.', true, null, null);
                } else if (jqXHR.status == 500) {
                    customSweetAlert(function() {
                        // console.log(textStatus);
                    }, 'error', 'Oppps!', 'Internal Server Error.', true, null, null);
                } else if (jqXHR.status == 422) {
                    var object = jqXHR.responseJSON.errors,
                        result = Object.keys(object).reduce(function(r, k) {
                            return r.concat(object[k] + '<br>');
                        }, []);
                    customSweetAlert(function() {
                        // console.log(jqXHR);
                    }, 'error', 'Oppps!', result.join(""), true, null, null);

                } else if (jqXHR.status == 421) {
                    customSweetAlert(function() {
                        // console.log(jqXHR);
                    }, 'error', 'Oppps!', jqXHR.responseJSON.message, true, null, null);
                } else if (errorThrown === 'timeout') {
                    customSweetAlert(function() {
                        // console.log(textStatus);
                    }, 'error', 'Oppps!', 'Time out error.', true, null, null);
                } else if (errorThrown === 'abort') {
                    customSweetAlert(function() {
                        // console.log(textStatus);
                    }, 'error', 'Oppps!', 'Request aborted.', true, null, null);
                } else {
                    customSweetAlert(function() {
                        // console.log(textStatus);
                        // console.log(jqXHR);
                    }, 'error', 'Oppps!', 'Uncaught Error.', true, null, null);
                }
            }
        };
        if (typeof customConfig != 'undefined') {
            defConfig = Object.assign({}, defConfig, customConfig);
        }
        $.ajax(defConfig);
    }
    function form_submit_reset() {
        $('.form_submit').trigger('reset');
        $('.row_id').val('');
        $('.update_button').text('Create').addClass('submit_button').removeClass('update_button');
    }
</script>