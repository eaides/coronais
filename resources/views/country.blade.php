@extends('layouts.app')

@section('styles')
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('css/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <style>

    </style>
@endsection

@section('head_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <h4 class="card-title">Countries
                                <a class="btn btn-success ml-5" href="javascript:void(0)" id="createNewItem">Create New Country</a>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table class="table table-bordered data-table" id="dataTable">
                        <thead>
                        <tr>
                            <th>Country</th>
                            <th>ID</th>
                            <th>Source Url</th>
                            <th>population</th>
                            <th>Last Update</th>
                            <th width="15%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="modal fade" id="ajaxModel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modelHeading"></h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="dataForm" action="" method="post" class="form-horizontal needs-validation">
                                        Source: <a id="country_url" href="https://www.worldometers.info/population/world/" target="_blank">https://www.worldometers.info/population/world/</a>
                                        <div class="form-group">
                                            <div style="display:none" class="generic-invalid-feedback-generic invalid-feedback is-invalid"></div>
                                        </div>
                                        <input type="hidden" name="data_id" id="data_id">

                                        <div class="form-group">
                                            <label for="name" class="col-sm-12 control-label">Country Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Country name" value="" maxlength="50" required>
                                            <div id="invalid-feedback-name" class="invalid-feedback is-invalid">The country name is required</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">ID (2 characters)</label>
                                            <input type="text" class="form-control" id="twoChars" name="twoChars" placeholder="Enter ID (2 chars)" value="" maxlength="50" required>
                                            <div id="invalid-feedback-twoChars" class="invalid-feedback is-invalid">The ID is required</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">url for data scrapping</label>
                                            <input type="text" class="form-control" id="url" name="url" placeholder="Enter URL" value="" maxlength="50" required>
                                            <div id="invalid-feedback-twoChars" class="invalid-feedback is-invalid">The url is required</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Population</label>
                                            <input type="number" class="form-control" id="population" name="population" placeholder="Enter population quantity" value="" maxlength="50">
                                            <div id="invalid-feedback-population" class="invalid-feedback is-invalid">Only numeric value</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Date</label>
                                            <input type="text" class="form-control" id="last_dateis" name="last_dateis" placeholder="Date in format yyyy-mm-dd" value="" maxlength="50">
                                            <div id="invalid-feedback-last_dateis" class="invalid-feedback is-invalid">entry a valid date</div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('body_scripts')
    <script>
        var reOrder = false;

        function padDigits(num, size) {
            num = parseInt(num);
            if (num.toString().length >= size) return num;
            return ( Math.pow( 10, size ) + Math.floor(num) ).toString().substring( 1 );
        }

        //custom Confrim Dialog with Custom message and callback handler
        function confirmDialog(message, handler){
            $(`<div class="modal fade" id="comfirmModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body" style="padding:10px;">
                                <h4 class="text-center">${message}</h4>
                                <div class="text-center">
                                    <a class="btn btn-danger btn-yes">yes</a>
                                    <a class="btn btn btn-primary btn-no">no</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`).appendTo('body');

            //Trigger the modal
            $("#comfirmModal").modal({
                backdrop: 'static',
                keyboard: false
            });

            //Pass true to a callback function
            $(".btn-yes").click(function () {
                handler(true);
                $("#comfirmModal").modal("hide");
            });

            //Pass false to callback function
            $(".btn-no").click(function () {
                handler(false);
                $("#comfirmModal").modal("hide");
            });

            //Remove the modal once it is closed.
            $("#comfirmModal").on('hidden.bs.modal', function () {
                $("#comfirmModal").remove();
            });
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            function formClearFeedback(formID) {
                $(formID).removeClass('was-validated');
                $(formID).removeClass('needs-validated');
                $(formID + ' .form-control').removeClass('is-invalid');
                $(formID + ' .form-control').addClass('is-valid');

                $(formID + ' .generic-invalid-feedback-generic').html('');
                $(formID + ' .generic-invalid-feedback-generic').hide();
            }

            function formValidateFromServer(formID, data) {
                formClearFeedback(formID);
                if (data.success) {
                    return true;
                }
                if (data.failure) {
                    $(formID + ' .generic-invalid-feedback-generic').html(data.failure);
                    $(formID + ' .generic-invalid-feedback-generic').show();
                    return false;
                }
                data = JSON.parse(data);
                for(var i in data) {
                    var invalidID = '#invalid-feedback-'+i;
                    var errorIs = data[i];
                    var j;
                    for (j = 0; j < errorIs.length; ++j) {
                        // do something with `substr[i]`
                        $(invalidID).html(errorIs[j]);
                        break;
                    }
                    $(invalidID).parent('.form-group').children('.form-control').removeClass('is-valid');
                    $(invalidID).parent('.form-group').children('.form-control').addClass('is-invalid');
                }
                $('#saveBtn').html('Save Changes');
                return false;
            }

            var table = $('#dataTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{route('country.index')}}"
                },
                "columns": [
                    {"data": "name"},
                    {"data": "twoChars"},
                    {"data": "url"},
                    {"data": "population"},
                    {"data": "last_dateis"},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

            $('#createNewItem').click(function () {
                formClearFeedback('#dataForm');
                var nowIs = new Date();
                nowIs.setDate(nowIs.getDate() - 1);
                var nowIsStr = padDigits(nowIs.getFullYear(),4)+'-'+padDigits(nowIs.getMonth()+1,2)+'-'+padDigits(nowIs.getDate(),2);
                $('#saveBtn').val("create-Item");
                $('#dataForm').trigger("reset");
                $('#data_id').val("");
                $('#last_dateis').val('2020-01-01');  // nowIsStr
                $('#modelHeading').html("Create New Data");
                $('#ajaxModel').modal({backdrop: "static"});
                reOrder = true;
            });

            $('body').on('click', '.editItem', function () {
                var data_id = $(this).data('id');
                formClearFeedback('#dataForm');
                $.get("{{ route('country.index') }}" +'/' + data_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Item");
                    $('#saveBtn').val("edit-data");
                    $('#ajaxModel').modal('show');
                    $('#data_id').val(data.id);
                    $('#name').val(data.name);
                    $('#twoChars').val(data.twoChars);
                    $('#url').val(data.url);
                    $('#last_dateis').val(data.last_dateis);
                    $('#population').val(data.population);
                    reOrder = false;
                })
            });

            $('#saveBtn').click(function (event) {
                event.preventDefault();
                event.stopPropagation();
                var theForm = $("#dataForm");
                if ($(theForm).get(0).checkValidity()==false) {
                    $(theForm).addClass('was-validated');
                    return false;
                } else {
                    var form_data = $(theForm).serialize();
                    formClearFeedback('#dataForm');
                    $('#saveBtn').html('Sending..');
                    $.ajax({
                        data: form_data,
                        url: "{{ route('country.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            $('#saveBtn').html('Save Changes');
                            var check = formValidateFromServer('#dataForm', data);
                            if (check) {
                                $('#dataForm').trigger("reset");
                                $('#ajaxModel').modal('hide');
                                table.draw(reOrder);
                            } else {
                                $('#saveBtn').html('Save Changes');
                                return false;
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            $('#saveBtn').html('Save Changes');
                        }
                    });
                }
            });

            $('body').on('click', '.deleteItem', function () {
                var data_id = $(this).data("id");
                confirmDialog("Are You sure want to delete !?", (ans) => {
                    if (ans) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('country.store') }}"+'/'+data_id,
                            success: function (data) {
                                table.draw(false);
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
