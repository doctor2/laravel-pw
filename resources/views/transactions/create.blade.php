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
                             <autocomplete user_name="{{old('user_name', $transaction['user_name'])}}" user_id="{{old('user_id', $transaction['user_id'])}}"></autocomplete>
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" name="amount" id="amount" class="card-body-form_amount" value="{{old('amount', $transaction['amount'])}}">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                    @if (count($errors))
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{$error}}</li>                            
                            @endforeach
                        </ul>        
                    @endif
                </div>
        </div>
    </div>
</div>
@endsection
