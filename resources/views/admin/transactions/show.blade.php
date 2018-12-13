@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                            Transaction page
                    </div>
                
                    <div class="card-body">
                        debit_user_name: {{$transaction['debit_user_name']}}
                        <hr>
                        debit_user_balance: {{$transaction['debit_user_balance']}}
                        <hr>
                        crebit_user_name: {{$transaction['crebit_user_name']}}
                        <hr>
                        crebit_user_balance: {{$transaction['crebit_user_balance']}}
                        <hr>
                        Amount: {{$transaction['amount']}}
                        <hr>

                        Date created: {{$transaction['created_at']}}

                    </div>
                </div>
                <br>
            

        </div>
    </div>
</div>
@endsection
