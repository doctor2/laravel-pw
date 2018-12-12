@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
                <div class="card">
                    <div class="card-header">Dashboard</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                <th scope="col">Date/Time</th>
                                <th scope="col">Correspondent Name</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Resulting balance</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($transactions as $transaction)

                                    <tr>
                                        <th scope="row">{{$transaction->created_at}}</th>
                                        <td>{{$transaction->user_name}}</td>
                                        <td>{{$transaction->amount}} ({{$transaction->transaction_type}})</td>
                                        <td> {{$transaction->user_balance}}</td>
                                    </tr>
                                   
                                @endforeach
                        
                            </tbody>
                        </table>
                    

                    </div>
                   
                </div>
            
        </div>
    </div>
</div>
@endsection
