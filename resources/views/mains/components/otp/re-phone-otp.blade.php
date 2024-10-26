
@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    {{-- <h1>{{__('Email OTP Code')}}</h1> --}}
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-3 border">
                <form id="loginForm" action="{{route('updateRePhoneOTP',['locale' => app()->getLocale(),'id' => $id])}}" method="post">
                    @csrf
                    <div class="form-content">
                        <div class="form-group">
                            <label for="attention">{{__('Enter Your Phone')}}</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <button type="submit" class="btn btn-outline-primary">
                            {{__('Update')}}
                        </button>
                        <div class="signup-message">
                            <a class="danger">@error('email'){{$message}}@enderror</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection