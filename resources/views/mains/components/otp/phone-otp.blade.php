
@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    {{-- <h1>{{__('Email OTP Code')}}</h1> --}}
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-3 border">
                <form action="{{route('verifyOTP',['locale' => app()->getLocale()])}}" method="post">
                    @csrf
                    <div class="form-content">
                        <div class="form-group">
                            <label for="attention">{{__('Is That Your Phone Number?')}}</label>
                            <input type="phone" class="form-control" name="phone" value="{{ $phone }}" readonly>
                            <input type="hidden" name="id" value="{{ $id }}">
                        </div>
                        <div id="choiceOTP" class="form-group">
                            <a id="noPhone" href="{{ route('goRePhoneOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}" class="btn btn-primary" style="margin-top: 3px; width: 49%;">
                               {{__(' No!')}}
                            </a>
                            <button id="yesPhone" type="button" class="btn btn-outline-primary" style="margin-top: 3px; width: 49%;" 
                                data-resend-url="{{ route('resendPhoneOTP', ['locale' => app()->getLocale(), 'id'=> $id, 'phone' => $phone]) }}">
                                {{__('Yes!')}}
                            </button>
                        </div>
                        <div id="otp-show-otp" class="d-none">
                            <div class="form-group">
                                <label for="entered_otp_code">Enter OTP Code:</label>
                                <input id="entered_email_otp_code" type="text" class="form-control" name="entered_otp_code" required>
                            </div>

                            <p style="font-size: 16px;">
                                <span style="font-size:10pt">Please Check Your SMS</span><br>
                                <span style="font-size:8pt">Please Wait before clicking again.</span>
                                <span id="countdown" style="font-size:8pt"></span>
                                <br>
                                {{-- <span id="resendLink" style="display:none;">
                                    Not received the code? <a href="{{ route('resendEmailOTP', ['locale' => app()->getLocale(), 'id' => $id, 'email' => $email]) }}" style="color: #cc0022;"><b>Send Code Again</b></a>
                                </span> --}}
                            </p>
                            <button type="submit" class="btn btn-outline-primary">Submit</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpShowOTP = document.getElementById('otp-show-otp');
        const optionsOTP = document.getElementById('choiceOTP');
        const yesPhoneButton = document.getElementById('yesPhone');
        const noPhoneButton = document.getElementById('noPhone');
        const countdownElement = document.getElementById('countdown');
        const resendLink = document.getElementById('resendLink');
        const countdownKeyPhone = 'otpCountdown';
        let countdownTimer;
    
        // Function to start and display countdown
        function startCountdown(seconds) {
            clearInterval(countdownTimer);
            const endTime = Date.now() + seconds * 1000;
            localStorage.setItem(countdownKeyPhone, endTime); // Store end time
    
            countdownTimer = setInterval(() => {
                const timeLeft = Math.floor((endTime - Date.now()) / 1000);
    
                if (timeLeft > 0) {
                    const minutes = Math.floor(timeLeft / 60);
                    const remainingSeconds = timeLeft % 60;
                    countdownElement.textContent = `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
                } else {
                    clearInterval(countdownTimer);
                    countdownElement.style.display = 'none';
                    resendLink.style.display = 'block';
                    localStorage.removeItem(countdownKeyPhone); // Clear storage after countdown ends
                    yesPhoneButton.disabled = false;
                    noPhoneButton.disabled = false;
                    otpShowOTP.classList.add('d-none');
                    optionsOTP.classList.remove('d-none');
                }
            }, 1000);
        }
    
        // Restore countdown from localStorage on page load
        function restoreCountdown() {
            const endTime = localStorage.getItem(countdownKeyPhone);
            if (endTime) {
                const timeLeft = Math.floor((endTime - Date.now()) / 1000);
                if (timeLeft > 0) {
                    otpShowOTP.classList.remove('d-none');
                    optionsOTP.classList.add('d-none');
                    yesPhoneButton.disabled = true;
                    noPhoneButton.disabled = true;
                    startCountdown(timeLeft);
                } else {
                    localStorage.removeItem(countdownKeyPhone); // Clear expired countdown on load
                }
            }
        }
    
        // AJAX request to resend OTP and start countdown when "Yes" is clicked
        yesPhoneButton.addEventListener('click', function() {
            otpShowOTP.classList.remove('d-none');
            optionsOTP.classList.add('d-none');
            yesPhoneButton.disabled = true;
            noPhoneButton.disabled = true;
    
            const resendUrl = yesPhoneButton.getAttribute('data-resend-url');
            fetch(resendUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startCountdown(300); // Start 5-minute countdown
                    // alert(data.message); // Success message
                } else {
                    // alert(data.message); // Error message if user not found
                }
            })
            .catch(error => console.error('Error:', error));
        });
    
        // Initialize countdown on page load
        restoreCountdown();
    });
    </script>
@endsection