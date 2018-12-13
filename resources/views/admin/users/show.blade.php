@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        User page
                    </div>
                
                    <div class="card-body">
                        Name: {{$user->name}}
                        <hr>
                        Email: {{$user->email}}
                        <hr>

                        Date created: {{$user->created_at}}

                    </div>
                </div>
                <br>
            

        </div>
    </div>
</div>
@endsection
