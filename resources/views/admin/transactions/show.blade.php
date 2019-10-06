@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <table class="table">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col" colspan="2">Transaction page</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">Sender name</th>
                            <td>{{$transaction->debit_user_name}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Sender balance</th>
                            <td>{{$transaction->debit_user_balance}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Recipient name</th>
                            <td>{{$transaction->credit_user_name}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Recipient balance</th>
                            <td>{{$transaction->credit_user_balance}}</td>
                        </tr>
                        <tr>
                            <th scope="row">Amount</th>
                            <td data-update-block style="display: none;">
                                <form action="">
                                    <div class="form-group">
                                        <input
                                            type="text"
                                            class="card-body-form_amount"
                                            name="amount"
                                            data-int
                                            value="{{$transaction->amount}}"
                                        >
                                    </div>
                                    <button data-update-btn  class="btn btn-xs btn-primary">Update</button>
                                    <button class="btn btn-xs btn-link" data-cancel-btn type="button">Cancel
                                    </button>
                                </form>
                            </td>
                            <td data-edit-block>
                                <span data-edit-value>{{$transaction->amount}}</span>
                                <button data-edit-btn class="btn btn-xs btn-primary">Edit</button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Date created</th>
                            <td>{{$transaction->created_at}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="alert alert-success" data-update-success style="display: none">Transaction has been updated!</div>
                    <div class="alert alert-danger" data-update-fail style="display: none"></div>

                    <div class="card-body">
                        <a href="{{route('admin.transactions.index')}}"><- Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascripts')
    <script>
        $(function () {
            let updateBlock = $('[data-update-block]');
            let editBlock = $('[data-edit-block]');
            let updateBtn = $('[data-update-btn]');
            let editBtn = $('[data-edit-btn]');
            let cancelBtn = $('[data-cancel-btn]');
            let updateSuccess = $('[data-update-success]');
            let updateFail = $('[data-update-fail]');
            let editValue = $('[data-edit-value]');


            function hideUpdateForm(){
                updateBlock.hide();
                editBlock.show();
            }

            editBtn.on('click', function () {
                editBlock.hide();
                updateBlock.show();
            });


            cancelBtn.on('click', function (e) {
                e.preventDefault();
                updateBlock.find('input[name="amount"]').val(editValue.text());
                hideUpdateForm();
            });

            updateBtn.on('click', function (e) {
                let amount = updateBlock.find('input[name="amount"]').val();
                e.preventDefault();
                $.ajax({
                    type: "PATCH",
                    url: "{{route('admin.transactions.update', ['id' => $transaction->id])}}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        amount: amount
                    },
                    dataType: "json",
                    success: function(response) {

                        editValue.text(amount);

                        hideUpdateForm();

                        updateSuccess.show();

                        setTimeout(() => (updateSuccess.hide()), 5000);
                    },
                    error: function(response) {
                        updateFail.text(response.responseJSON.message);

                        updateFail.show();

                        setTimeout(() => (updateFail.hide()), 5000);
                    }
                });
            });
        });
    </script>
@endsection
