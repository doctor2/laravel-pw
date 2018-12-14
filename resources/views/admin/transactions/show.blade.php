@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                <div class="card">
                   
                            <table class="table " >
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" colspan="2">Transaction page</th>
                                    </tr>
                                </thead>
                                <tbody>
                                      <tr>
                                        <th scope="row">Sender name</th>
                                        <td>{{$transaction['debit_user_name']}}</td>
                                      </tr>
                                      <tr>
                                        <th scope="row">Sender balance</th>
                                        <td>{{$transaction['debit_user_balance']}}</td>
                                      </tr>
                                      <tr>
                                        <th scope="row">Recipient name</th>
                                        <td>{{$transaction['crebit_user_name']}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Recipient balance</th>
                                        <td>{{$transaction['crebit_user_balance']}}</td>
                                    </tr>
                                      <tr>
                                        <th scope="row">Amount</th>
                                        <td>{{$transaction['amount']}}</td>
                                      </tr>
                                      <tr>
                                        <th scope="row">Date created</th>
                                        <td>{{$transaction['created_at']}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="card-body">
                                <a href="{{route('admin.transactions.index')}}"><- Back</a>
                            </div>
                </div>
        </div>
    </div>
</div>
@endsection
