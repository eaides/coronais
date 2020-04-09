@extends('layouts.app')

@section('styles')
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous">
@endsection

@section('head_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>

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
                            <h4 class="card-title">Israel Corona Virus Statistics
                                <a class="btn btn-success ml-5" href="javascript:void(0)" id="createNewItem"> Create New Data</a>
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
                                        <option value="{{$country->id}}" {{$selected}}>{{$country->name}}</option>
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
                                    <form id="dataForm" action="" method="post" class="form-horizontal">
                                        <a href="https://www.worldometers.info/coronavirus/country/israel/" target="_blank">https://www.worldometers.info/coronavirus/country/israel/</a>
                                        <input type="hidden" name="data_id" id="data_id">
                                        <input type="hidden" name="country_id" id="country_id" value="">
                                        <div class="form-group">
                                            <label for="name" class="col-sm-12 control-label">Total Quantity</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="qty" name="qty" placeholder="Enter total quantity" value="" maxlength="50" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Actives</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="actives" name="actives" placeholder="Enter total quantity" value="" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Death</label>
                                            <div class="col-sm-12">
                                                <input type="number" class="form-control" id="death" name="death" placeholder="Enter total quantity" value="" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Date</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="dateis" name="dateis" placeholder="Date in format yyyy-mm-dd" value="" maxlength="50" required>
                                            </div>
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

            $('#country_id').val($('#inputGroupSelectCountries').val());

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
                table.draw(true);
            });

            $('#createNewItem').click(function () {
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

            // $('#saveBtn').click(function (e) {
            $("#dataForm").submit(function(event){
                var form_data = $(this).serialize();
                event.preventDefault();
                $('#saveBtn').html('Sending..');
                $.ajax({
                    data: form_data,
                    url: "{{ route('ajaxStatistic.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#saveBtn').html('Save Changes');
                        $('#dataForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw(reOrder);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('Save Changes');
                    }
                });
            });

            $('#inputGroupSelectCountries').change(function(){
                $('#country_id').val($(this).val());
            });

            $('body').on('click', '.deleteItem', function () {
                var data_id = $(this).data("id");
                confirmDialog("Are You sure want to delete !?", (ans) => {
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
        });

    </script>
@endsection
