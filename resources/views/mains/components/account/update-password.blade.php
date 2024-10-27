@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    <div class="container p-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>{{__('Reset Password')}}</h2>
                <form method="POST" action="{{ route('password.update', ['locale' => app()->getLocale()]) }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input id="email" name="email" type="email" class="form-control" required>
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">New Password *</label>
                        <input id="password" name="password" type="password" class="form-control" required>
                        @error('password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password *</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
