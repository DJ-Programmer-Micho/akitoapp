<div class="page-content">
    <div class="dashboard">
        <div class="container">
            <form id="myForm" method="POST" action="{{ route('business.account', ['locale' => app()->getLocale()]) }}">
                @csrf <!-- CSRF token for security -->
                <div class="row">
                    <!-- Old Password -->
                    <div class="col-md-4 col-sm-12 mb-3">
                        <label for="old_password">Old Password *</label>
                        <div class="position-relative">
                            <input id="old_password" name="old_password" type="password" class="form-control" required aria-describedby="oldPasswordHelp">
                            <i id="old_password_icon" class="fa-regular fa-eye toggle-password" role="button" aria-label="Toggle password visibility"></i>
                        </div>
                        @error('old_password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="col-md-4 col-sm-12 mb-3">
                        <label for="new_password">New Password *</label>
                        <div class="position-relative">
                            <input id="new_password" name="new_password" type="password" class="form-control" required aria-describedby="passwordHelp">
                            <i id="new_password_icon" class="fa-regular fa-eye toggle-password" role="button" aria-label="Toggle password visibility"></i>
                        </div>
                        <div id="password_strength" class="mt-2" style="height: 10px;"></div>
                        <span id="strength_text" class="mt-1"></span>
                        @error('new_password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-4 col-sm-12 mb-3">
                        <label for="confirm_password">Confirm Password *</label>
                        <div class="position-relative">
                            <input id="confirm_password" name="confirm_password" type="password" class="form-control" required aria-describedby="confirmPasswordHelp">
                            <i id="confirm_password_icon" class="fa-regular fa-eye toggle-password" role="button" aria-label="Toggle password visibility"></i>
                        </div>
                        @error('confirm_password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha" id="feedback-recaptcha" data-sitekey="{!! env('GOOGLE_RECAPTCHA_KEY') !!}"></div>
                @error('g-recaptcha-response')
                <span class="danger" style="font-size: 12px">{{__('Please Check reCaptcha')}}</span><br>
                @enderror
                <!-- Submit Button -->
                <button type="submit" class="btn btn-outline-primary-2">
                    <span>Update Password</span>
                    <i class="icon-long-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</div>

@push('styles-password')
<style>
    /* Styles for password strength meter and icons */
    .position-relative {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
    }

    .form-control {
        padding-right: 40px;
    }

    #password_strength {
        background-color: #f3f3f3;
        border-radius: 5px;
        transition: width 0.5s ease; /* Smooth transition for width changes */
    }

    #password_strength.weak {
        background-color: red;
        width: 30%;
    }

    #password_strength.medium {
        background-color: yellow;
        width: 60%;
    }

    #password_strength.strong {
        background-color: green;
        width: 100%;
    }
</style>
@endpush

@push('password')
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Add event listeners for toggling password visibility
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const input = this.previousElementSibling;
            togglePassword(input.id, icon.id);
        });
    });

    // Password strength checking
    function checkPasswordStrength(password) {
        let strength = 0;
        const strengthMeter = document.getElementById('password_strength');
        const strengthText = document.getElementById('strength_text');

        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[\W_]/.test(password)) strength++;

        strengthMeter.className = ''; // Reset classes
        strengthText.textContent = ''; // Reset text

        if (strength <= 2) {
            strengthMeter.classList.add('weak');
            strengthText.textContent = 'Weak';
            strengthText.style.color = 'red';
        } else if (strength === 3) {
            strengthMeter.classList.add('medium');
            strengthText.textContent = 'Medium';
            strengthText.style.color = 'orange';
        } else {
            strengthMeter.classList.add('strong');
            strengthText.textContent = 'Strong';
            strengthText.style.color = 'green';
        }
    }

    // Add event listener to new password field
    document.getElementById('new_password').addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });
</script>
@endpush
