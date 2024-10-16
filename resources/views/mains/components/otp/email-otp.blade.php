
@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    {{-- <h1>{{__('Email OTP Code')}}</h1> --}}
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-3 border">
                <form id="loginForm" action="{{route('verifyEmailOTP',['locale' => app()->getLocale()])}}" method="post">
                    @csrf
                    <div class="form-content">
                        <div class="form-group">
                            <label for="attention">{{__('Is That Your Email?')}}</label>
                            <input type="email" class="form-control" name="email" value="{{ $email }}" readonly>
                        </div>
                        <div id="choice" class="form-group">
                            {{-- <button id="no" type="button" class="button" style="margin-top: 3px; width: 49%;">No!</button> --}}
                            <a href="{{ route('goReEmailOTP', ['locale' => app()->getLocale(),'id' => $id]) }}">
                                <button id="no" type="button"  class="btn btn-outline-primary" style="margin-top: 3px; width: 49%;">No</button>
                            </a>
                            <a href="{{ route('resendEmailOTP', ['locale' => app()->getLocale(),'id' => $id,'email' => $email]) }}">
                                <button id="yes" type="button"  class="btn btn-outline-primary" style="margin-top: 3px; width: 49%;">Yes!</button>
                            </a>
                        </div>
            
                        <div id="otp-show" class="hide">
                            <div class="form-group">
                                <label for="entered_otp_code">Enter OTP Code:</label>
                                <input id="entered_email_otp_code" type="text" class="form-control" name="entered_email_otp_code" required>
                            </div>
                            
                            <br>
                            {{-- <p style="font-size: 16px;">
                                <span style="font-size:12pt">Please Check Your Email</span><br>
                                <span style="font-size:12pt">It May Take Up To (5)min</span><br>
                                <span id="countdown" style="font-size:12pt">Wait for 1 minute before clicking again.</span>
                                <br>
                                <span id="resendLink" style="display:none;">
                                    Not received the code? <a href="{{ route('resendEmailOTP', ['locale' => app()->getLocale(), 'id' => $id,'email' => $email]) }}" style="color: #cc0022;"><b>Send Code Again</b></a>
                                </span>
                            </p> --}}
                            <button type="submit" class="btn btn-outline-primary">
                                Submit
                            </button>
                        </div>
                        <br />
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