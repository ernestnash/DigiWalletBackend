@extends('layouts.skeleton')

@section('main')

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Reset Pin</h1>
                                    <h5 class="h5 mb-4">Enter account number to verify your account</h5>
                                </div>
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        {{-- <div>Something went wrong!</div> --}}
                
                                        <ul style="list-style: none">
                                            @foreach ($errors->all() as $error )
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if(Session::has('success'))
                                    <div class="alert alert-success">
                                        {{ Session::get('success')}}
                                    </div>
                                @endif
                                <form class="user" method="POST" action="{{ url('login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-user"
                                            id="phone_number" name="phone_number"
                                            placeholder="Enter Account Number...">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Verify
                                    </button>
                                </form>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection