@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
        <admin-transaction>
            <div slot="message">@include('layouts._message')</div>
        </admin-transaction>

        </div>
    </div>
</div>
@endsection
