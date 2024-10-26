@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    <div class="container">
        <div class="row">
            <div id="stepOne" class="col-md-6 offset-md-3 p-5">
                <h2>{{__('Forgot Password')}}</h2>
                <form method="POST" action="{{ route('password.email',["locale" => app()->getLocale()]) }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input id="email" name="email" type="email" class="form-control" required>
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button id="click-once" type="submit" class="btn btn-primary">{{__('Send Password Reset Link')}}</button>
                </form>
            </div>
            <div id="stepTwo" class="col-md-6 offset-md-3 p-5 d-none">
                <h2 class="text-center">{{__('Please Check Your Email')}}</h2>
            </div>
        </div>
    </div>   
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clickOnce = document.getElementById('click-once');
    const stepOne = document.getElementById('stepOne');
    const stepTwo = document.getElementById('stepTwo');
    const email = document.getElementById('email');

    clickOnce.addEventListener('click', function() {
        if(email.value) {
            stepOne.classList.add('d-none');
            stepTwo.classList.remove('d-none');
        }
    });
});
</script>

@endsection
