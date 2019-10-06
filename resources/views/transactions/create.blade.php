@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                <div class="card-header">Create a new transaction</div>
                <div class="card-body">
                    <form action="{{route('transactions.store')}}" method="POST" class="card-body-form">
                        @csrf
                        <div class="form-group">
                            <label for="user_name">The recipient</label>
                            <input type="text" name="user_name" data-autocomplete value="{{old('user_name', $transaction['user_name'])}}">
                            <input type="hidden" name="user_id" value="{{old('user_id', $transaction['user_id'])}}">
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" name="amount" id="amount" data-int class="card-body-form_amount" value="{{old('amount', $transaction['amount'])}}">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                    @if (count($errors))
                        <div class="alert alert-danger" data-error>
                            @foreach ($errors->all() as $error)
                                 {{$error}}<br>
                            @endforeach
                        </div>
                    @endif
                </div>
        </div>
    </div>
</div>
@endsection
@section('javascripts')

    <script>
        $(function () {
            const URL =  "{{route('auto-users.index')}}?name=:query";
            let autoInput = $('[data-autocomplete]');
            let autoUsers = [];
            let autoInputUserId = $('input[name="user_id"]');

            autoInput.autocomplete({
                delay: 300,
                select: function(event, ui) {
                    $.map(autoUsers, function(item){
                        if(item.name == ui.item.value){
                            autoInputUserId.val(item.id);
                        }
                    });
                },
                source: function(request, response){

                    $.ajax({
                        type: "GET",
                        url: URL.replace(':query', autoInput.val()),
                        success: function(data){
                            autoUsers = data.data;
                            response($.map(data.data, function(item){
                                return{
                                    label: item.name,
                                    value: item.name
                                }
                            }));
                        }
                    });
                },
                minLength: 2
            });
        });
    </script>
@endsection

