@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                <div class="card">
                        
                <admin-transaction-edit :transaction="{{json_encode($transaction)}}"></admin-transaction-edit>

                            
                            <div class="card-body">
                                <a href="{{route('admin.transactions.index')}}"><- Back</a>
                            </div>
                </div>
        </div>
    </div>
</div>
@endsection
