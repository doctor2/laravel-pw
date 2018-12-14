@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                <div class="card">
                        <table class="table " >
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" colspan="2">User page</th>
                                    </tr>
                                </thead>
                                <tbody>
                                      <tr>
                                        <th scope="row">Name</th>
                                        <td>{{$user->name}}</td>
                                      </tr>
                                      <tr>
                                        <th scope="row">Email</th>
                                        <td>{{$user->email}}</td>
                                      </tr>
                                      <tr>
                                        <th scope="row">Date created</th>
                                        <td>{{$user->created_at}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Ban</th>
                                        <td>{{$user->hasBan}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="card-body">
                                <a href="{{route('admin.users.index')}}" ><- Back</a>    
                            </div>
                </div>
        </div>
    </div>
</div>
@endsection
