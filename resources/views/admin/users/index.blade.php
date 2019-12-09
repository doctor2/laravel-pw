@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="panel panel-default">
                @include("layouts._message")
                <div class="panel-heading">Users table</div>
                <div class="panel-body">
                    <table class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Ban</th>
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
                    url: "{{ route('admin.users.index') }}",
                },
                "columns": [
                    {
                        "data": "created_at",
                        "searchable": false,
                        "name": "id"
                    },
                    {
                        "data": null,
                        "name": "name",
                        "searchable": false,
                        "render": function (data, type, row) {
                            if (type === 'display') {
                                return `<a href="/admin/users/${data.id}">${data.name}</a>`;
                            }
                            return data.name;
                        },
                    },
                    {
                        "data": "email",
                        "searchable": false,
                    },
                    {
                        "data": null,
                        "name": "banned",
                        "searchable": false,
                        "render": function (data, type, row) {
                            if (type === 'display') {
                                return (data.banned ? "yes" : "no") +
                                    `  <a href="/admin/users/${data.id}/edit">Edit</a>`;
                            }
                            return data.banned;
                        },
                    },
                ]
            });
        });
    </script>
@endsection
