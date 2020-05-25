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
                            <h4 class="card-title">Statistics
                                <a class="btn btn-success ml-5" href="javascript:void(0)" id="createNewItem">Create New Data</a>
                                <a class="btn btn-danger ml-5" href="javascript:void(0)" id="removeAllStat">Remove All</a>
                            </h4>
                        </div>
                        <div class="col-md-4 offset-md-1 justify-content-center">
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelectCountries">Country:</label>
                                </div>
                                <select class="custom-select" id="inputGroupSelectCountries">
                                    @foreach($countries as $country)
                                        @php
                                            $selected = '';
                                            if (strtoupper($country->twoChars) == 'IL') {
                                                $selected = 'selected';
                                            }
                                        @endphp
                                        <option data-url="{{$country->url}}" value="{{$country->id}}" {{$selected}}>{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
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
                            <th>Total Quantity</th>
                            <th>Total % diff</th>
                            <th>Actives</th>
                            <th>Active % diff.</th>
                            <th width="10%">Deaths</th>
                            <th>% death</th>
                            <th>Date</th>
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
                                        Source: <a id="country_url" href="" target="_blank">...</a>
                                        <div class="form-group">
                                            <div style="display:none" class="generic-invalid-feedback-generic invalid-feedback is-invalid"></div>
                                        </div>
                                        <input type="hidden" name="data_id" id="data_id">
                                        <input type="hidden" name="country_id" id="country_id" value="">
                                        <div class="form-group">
                                            <label for="name" class="col-sm-12 control-label">Total Quantity</label>
                                            <input type="number" class="form-control" id="qty" name="qty" placeholder="Enter total quantity" value="" maxlength="50" required>
                                            <div id="invalid-feedback-qty" class="invalid-feedback is-invalid">Numeric value is required</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Actives</label>
                                            <input type="number" class="form-control" id="actives" name="actives" placeholder="Enter total quantity" value="" maxlength="50">
                                            <div id="invalid-feedback-actives" class="invalid-feedback is-invalid">Only numeric value</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Death</label>
                                            <input type="number" class="form-control" id="death" name="death" placeholder="Enter total quantity" value="" maxlength="50">
                                            <div id="invalid-feedback-death" class="invalid-feedback is-invalid">Only numeric value</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Date</label>
                                            <input type="text" class="form-control" id="dateis" name="dateis" placeholder="Date in format yyyy-mm-dd" value="" maxlength="50" required>
                                            <div id="invalid-feedback-dateis" class="invalid-feedback is-invalid">Mandatory valid date</div>
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
        var selector_country_id = getCookie('corona_stats_country_id');
        if (selector_country_id != "") {
            $('#inputGroupSelectCountries').val(selector_country_id);
        }
        setCookie('corona_stats_country_id', $('#inputGroupSelectCountries').val(), 7);

        // select 2
        $('#inputGroupSelectCountries').select2({
            theme: "bootstrap4",
        });

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

            function displayCountryUrl() {
                var url = $('#inputGroupSelectCountries option:selected').data('url');
                $('#country_url').attr("href", url);
                $('#country_url').text(url);
            }

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

            $('#country_id').val($('#inputGroupSelectCountries').val());
            displayCountryUrl();

            var table = $('#dataTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{route('ajaxStatistic.index')}}",
                    "data": function ( d ) {
                        d.country_id = $('#country_id').val();
                    }
                },
                "columns": [
                    {"data": "qty"},
                    {"data": "percent"},
                    {"data": "actives"},
                    {"data": "active_percent"},
                    {"data": "death"},
                    {"data": "death_percent"},
                    {"data": "dateis"},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

            $('#inputGroupSelectCountries').change(function(){
                $('#country_id').val($(this).val());
                displayCountryUrl();
                setCookie('corona_stats_country_id', $('#inputGroupSelectCountries').val(), 7);
                table.draw(true);
            });

            $('#createNewItem').click(function () {
                formClearFeedback('#dataForm');
                var nowIs = new Date();
                nowIs.setDate(nowIs.getDate() - 1);
                var nowIsStr = padDigits(nowIs.getFullYear(),4)+'-'+padDigits(nowIs.getMonth()+1,2)+'-'+padDigits(nowIs.getDate(),2);
                $('#saveBtn').val("create-Item");
                $('#dataForm').trigger("reset");
                $('#data_id').val("");
                $('#dateis').val(nowIsStr);
                $('#country_id').val($('#inputGroupSelectCountries').val());
                $('#modelHeading').html("Create New Data");
                $('#ajaxModel').modal({backdrop: "static"});
                reOrder = true;
            });

            $('body').on('click', '.editItem', function () {
                formClearFeedback('#dataForm');
                var data_id = $(this).data('id');
                $.get("{{ route('ajaxStatistic.index') }}" +'/' + data_id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Item");
                    $('#saveBtn').val("edit-data");
                    $('#ajaxModel').modal('show');
                    $('#data_id').val(data.id);
                    $('#qty').val(data.qty);
                    $('#death').val(data.death);
                    $('#actives').val(data.actives);
                    $('#dateis').val(data.dateis);
                    $('#country_id').val(data.country_id);
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
                        url: "{{ route('ajaxStatistic.store') }}",
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
                confirmDialog("Are you sure want to delete !?", (ans) => {
                    if (ans) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('ajaxStatistic.store') }}"+'/'+data_id,
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

            $('body').on('click', '#removeAllStat', function () {
                var country_id = $('#country_id').val();
                confirmDialog("Are you sure want to remove all statistics !?", (ans) => {
                    if (ans) {
                        startSpinner();
                        $.ajax({
                            type: "POST",
                            url: "{{ route('ajaxStatistic.empty') }}",
                            data: {
                                county_id: country_id
                            }
                        }).always(function(data) {
                            endSpinner();
                            // location.reload();
                            console.log(data);
                        });
                    }
                });
            });

        });
    </script>
@endsection
