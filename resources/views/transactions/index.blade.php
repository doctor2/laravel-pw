@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @include("layouts._message")
                <div class="panel panel-default">
                    <div class="panel-heading">Transactions table</div>
                    <div class="panel-body">
                        <table class="table" id="datatable">
                            <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Correspondent Name</th>
                                <th>Amount</th>
                                <th>Resulting balance</th>
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
                    url: "{{ route('transactions.index') }}",
                },
                "columns": [
                    {
                        "data": "created_at",
                        "searchable": false,
                        "name": "id"
                    },
                    {
                        "data": "user_name",
                        "searchable": false,
                    },
                    {
                        "data": null,
                        "name": "amount",
                        "searchable": false,
                        "render": function (data, type, row) {
                            if (type === 'display') {
                                return data.amount + ' ( ' + data.transaction_type + ' )';
                            }
                            return data.amount;
                        },
                    },
                    {
                        "data": null,
                        "name": "user_balance",
                        "searchable": false,
                        "render": function (data, type, row) {
                            let balance = data.user_balance;
                            if (data.transaction_type == "DEBIT") {
                                balance += ` <a href="/transactions/create?key=${data.id}">Repeat</a>`;
                            }
                            return balance;
                        },
                    },
                ]
            });
        });
    </script>
@endsection
