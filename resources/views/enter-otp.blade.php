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
                                    <p class="">Enter OTP sent to your email to verify your identity</p>
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
                                <form class="user" method="POST" action="{{ url('verify') }}">
                                    @csrf
                                    <div class="form-group">
                                            <div class="code-container">
                                                <input type="text" name="" id="" class="code" placeholder="0" min="0" max="9" required>
                                                <input type="text" name="" id="" class="code" placeholder="0" min="0" max="9" required>
                                                <input type="text" name="" id="" class="code" placeholder="0" min="0" max="9" required>
                                                <input type="text" name="" id="" class="code" placeholder="0" min="0" max="9" required>
                                            </div>
                                    </div>
                                    <script>
                                        const codes = document.querySelectorAll('.code');

                                        codes[0].focus();

                                        codes.forEach((code, idx) => {
                                            code.addEventListener('keydown', (e) => {
                                                if(e.key >= 0 && e.key <= 9) {
                                                    codes[idx].value = ''
                                                    setTimeout(() => codes[idx+1].focus(), 10)
                                                } else if(e.key === 'Backspace') {
                                                    setTimeout(() => codes[idx - 1].focus(), 10)
                                                }
                                            })
                                        })
                                                                            </script>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Verify
                                    </button>
                                </form>
                                <hr>
                                <div class="text-center">
                                <small class="small">if you didn't receive code </small><strong>Resend!!!</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection