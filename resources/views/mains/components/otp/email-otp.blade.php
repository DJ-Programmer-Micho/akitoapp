@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-3 border">
                <form action="{{ route('verifyEmailOTP', ['locale' => app()->getLocale()]) }}" method="post">
                    @csrf
                    <div class="form-content">
                        <div class="form-group">
                            <label for="attention">{{ __('Is That Your Email?') }}</label>
                            <input type="email" class="form-control" name="email" value="{{ $email }}" readonly>
                        </div>
                        <div id="choice" class="form-group choice">
                            <a href="{{ route('goReEmailOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}">
                                <button id="no" type="button" class="btn btn-outline-primary" style="margin-top: 3px; width: 49%;">{{__('No')}}</button>
                            </a>
                            <button id="yes" type="button" class="btn btn-outline-primary" style="margin-top: 3px; width: 49%;" 
                                data-resend-url="{{ route('resendEmailOTP', ['locale' => app()->getLocale(), 'id' => $id, 'email' => $email]) }}">
                                {{__('Yes!')}}
                            </button>
                        </div>

                        <div id="otp-show" class="d-none">
                            <div class="form-group">
                                <label for="entered_otp_code">Enter OTP Code:</label>
                                <input id="entered_email_otp_code" type="text" class="form-control" name="entered_email_otp_code" required>
                            </div>

                            <p style="font-size: 16px;">
                                <span style="font-size:10pt">Please Check Your Email</span><br>
                                <span style="font-size:8pt">Please Wait before clicking again.</span>
                                <span id="countdown" style="font-size:8pt"></span>
                                <br>
                                {{-- <span id="resendLink" style="display:none;">
                                    Not received the code? <a href="{{ route('resendEmailOTP', ['locale' => app()->getLocale(), 'id' => $id, 'email' => $email]) }}" style="color: #cc0022;"><b>Send Code Again</b></a>
                                </span> --}}
                            </p>
                            <button type="submit" class="btn btn-outline-primary">Submit</button>
                        </div>

                        <div class="signup-message">
                            <a class="danger">@error('email'){{ $message }}@enderror</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpShow = document.getElementById('otp-show');
    const options = document.getElementById('choice');
    const yesButton = document.getElementById('yes');
    const noButton = document.getElementById('no');
    const countdownElement = document.getElementById('countdown');
    const resendLink = document.getElementById('resendLink');
    const countdownKey = 'otpCountdown';
    let countdownTimer;

    // Function to start and display countdown
    function startCountdown(seconds) {
        clearInterval(countdownTimer);
        const endTime = Date.now() + seconds * 1000;
        localStorage.setItem(countdownKey, endTime); // Store end time

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
                localStorage.removeItem(countdownKey); // Clear storage after countdown ends
                yesButton.disabled = false;
                noButton.disabled = false;
                otpShow.classList.add('d-none');
                options.classList.remove('d-none');
            }
        }, 1000);
    }

    // Restore countdown from localStorage on page load
    function restoreCountdown() {
        const endTime = localStorage.getItem(countdownKey);
        if (endTime) {
            const timeLeft = Math.floor((endTime - Date.now()) / 1000);
            if (timeLeft > 0) {
                otpShow.classList.remove('d-none');
                options.classList.add('d-none');
                yesButton.disabled = true;
                noButton.disabled = true;
                startCountdown(timeLeft);
            } else {
                localStorage.removeItem(countdownKey); // Clear expired countdown on load
            }
        }
    }

    // AJAX request to resend OTP and start countdown when "Yes" is clicked
    yesButton.addEventListener('click', function() {
        otpShow.classList.remove('d-none');
        options.classList.add('d-none');
        yesButton.disabled = true;
        noButton.disabled = true;

        const resendUrl = yesButton.getAttribute('data-resend-url');
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
