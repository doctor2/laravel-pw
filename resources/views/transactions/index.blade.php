@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <transaction>
                <div slot="message">@include('layouts._message')</div>
            </transaction>


        </div>
    </div>
</div>
@endsection
