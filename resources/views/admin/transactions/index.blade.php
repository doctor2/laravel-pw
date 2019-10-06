@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">Admin table</div>
                    <div class="panel-body">
                        <table class="table" id="datatable">
                            <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Sender Name</th>
                                <th>Recipient Name</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascripts')
    <script>
        $(document).ready(function () {
            $('#datatable').on('xhr.dt', function (e, settings, json, xhr) {

                json.recordsTotal = json.data.original.recordsTotal;
                json.recordsFiltered = json.data.original.recordsFiltered;
                json.draw = json.data.original.draw;
                json.data = json.data.original.data;

            }).DataTable({
                "processing": true,
                "serverSide": true,
                "searchDelay": 500,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    url: "{{ route('admin.transactions.index') }}",
                },
                "columns": [
                    {
                        "data": "created_at",
                        "searchable": false,
                        "name": "id"
                    },
                    {
                        "data": "debit_user_name",
                        "searchable": false,
                    },
                    {
                        "data": "credit_user_name",
                        "searchable": false,
                    },
                    {
                        "data": null,
                        "name": "amount",
                        "searchable": false,
                        "render": function (data, type, row) {
                            if (type === 'display') {
                                return `<a href="/admin/transactions/` + data.id + `" class="editor_edit">` + data.amount + `</a>`;
                            }
                            return data.amount;
                        },
                    },
                ]
            });
        });
    </script>
@endsection
