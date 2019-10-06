@if(session('success'))
    <div class="alert alert-success" data-success>
        {{session('success')}}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" data-error>
        {{session('error')}}
    </div>
@endif
