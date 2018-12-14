@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <admin-user>
                <div slot="message">@include('layouts._message')</div>
            </admin-user>

        </div>
    </div>
</div>
@endsection
