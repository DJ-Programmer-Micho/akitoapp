@extends('mains.layout.app')
@section('business-content')
<div wire:ignore>
<div class="main">
  <x-mains.components.account.image-header-one />
  <x-mains.components.account.nav-one />

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-3 border">
        <form action="{{ route('verifyEmailOTP', ['locale' => app()->getLocale()]) }}" method="post">
          @csrf
          <div class="form-content">

            <div class="form-group">
              <label>{{ __('Is That Your Email?') }}</label>
              <input type="email" class="form-control" name="email" value="{{ $email }}" readonly>
            </div>

            <div id="choice" class="form-group" style="display:flex; gap:6px;">
              <a class="btn btn-outline-primary" style="margin-top:3px; width:49%;"
                 href="{{ route('goReEmailOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}">
                {{ __('No') }}
              </a>

              <button id="btnEmailSend" type="button" class="btn btn-outline-primary"
                      style="margin-top:3px; width:49%;"
                      data-resend-url="{{ route('resendEmailOTP', ['locale' => app()->getLocale(), 'id' => $id, 'email' => $email]) }}">
                {{ __('Yes!') }}
              </button>
            </div>

            <div id="otpArea" class="d-none">
              <div class="form-group">
                <label for="entered_email_otp_code">{{ __('Enter OTP Code:') }}</label>
                <input id="entered_email_otp_code" type="text" class="form-control"
                       name="entered_email_otp_code" required inputmode="numeric" autocomplete="one-time-code">
              </div>

              <p class="mb-2" style="font-size: 16px;">
                <span style="font-size:10pt">{{ __('Please check your email.') }}</span><br>
                <span style="font-size:8pt">{{ __('Please wait before clicking again.') }}</span>
                <span id="countdownEmail" style="font-size:8pt"></span>
              </p>

              <div id="emailResendMsg" class="text-muted" style="display:none; font-size:8pt;">
                {{ __('Didn\'t receive the code? You can resend after the countdown ends.') }}
              </div>

              <button type="submit" class="btn btn-outline-primary mt-2">{{ __('Submit') }}</button>
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
  window.OTP_RESEND_COOLDOWN = {{ (int) config('otp.resend_cooldown') }};
  window.OTP_EXPIRES_MIN     = {{ (int) config('otp.expires_minutes') }};
</script>

<script>
(function() {
  const KEY_END    = 'otpCountdownEmail';
  const KEY_VIS    = 'otpEmailVisible';

  let t;
  let justSent = false;
  const otpArea        = document.getElementById('otpArea');
  const choiceArea     = document.getElementById('choice');
  const btnEmailSend   = document.getElementById('btnEmailSend');
  const countdownEl    = document.getElementById('countdownEmail');
  const emailResendMsg = document.getElementById('emailResendMsg');

  function setVisible(v) {
    localStorage.setItem(KEY_VIS, v ? '1' : '0');
    if (v) {
      otpArea.classList.remove('d-none');
      choiceArea.classList.add('d-none');
    } else {
      otpArea.classList.add('d-none');
      choiceArea.classList.remove('d-none');
    }
  }

  function getEndTs() {
    const raw = localStorage.getItem(KEY_END);
    return raw ? parseInt(raw, 10) : 0;
  }

  function setEndTs(ts) {
    localStorage.setItem(KEY_END, String(ts));
  }

  function clearEndTs() {
    localStorage.removeItem(KEY_END);
  }

  function startCountdown(seconds) {
    clearInterval(t);
    const endTs = Date.now() + seconds * 1000;
    setEndTs(endTs);

    t = setInterval(() => {
      const left = Math.floor((getEndTs() - Date.now()) / 1000);
      if (left > 0) {
        const m = Math.floor(left / 60), s = left % 60;
        countdownEl.textContent = ` ${m}:${s < 10 ? '0' : ''}${s}`;
      } else {
        clearInterval(t);
        countdownEl.textContent = '';
        clearEndTs();
        btnEmailSend.disabled = false;
        emailResendMsg.style.display = 'none';
      }
    }, 1000);
  }

  function restore() {
    if (justSent) {
      justSent = false;
      return;
    }

    const vis = localStorage.getItem(KEY_VIS) === '1';
    setVisible(vis);

    const left = Math.floor((getEndTs() - Date.now()) / 1000);
    if (vis && left > 0) {
      btnEmailSend.disabled = true;
      emailResendMsg.style.display = 'block';
      startCountdown(left);
    } else if (vis) {
      clearEndTs();
      btnEmailSend.disabled = false;
      emailResendMsg.style.display = 'none';
    }
  }

  function boot() {
    restore();

    btnEmailSend?.addEventListener('click', (e) => {
      e.preventDefault();

      justSent = true;

      // Show OTP area immediately
      setVisible(true);
      btnEmailSend.disabled = true;
      emailResendMsg.style.display = 'block';
      
      setTimeout(() => {
        startCountdown(window.OTP_RESEND_COOLDOWN);
      }, 50);

      const url = btnEmailSend.getAttribute('data-resend-url');

      fetch(url, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(async res => {
          const data = await res.json().catch(() => ({}));
          
          // Handle 429 - OTP was already sent recently
          if (res.status === 429) {
            justSent = false;
            // Keep OTP area visible, adjust countdown if server provides retry_in
            if (data?.retry_in) {
              const retrySeconds = parseInt(data.retry_in, 10);
              if (retrySeconds > 0) {
                startCountdown(retrySeconds);
              }
            }
            // Show message that code was already sent
            if (data?.message) {
              console.log('Rate limit:', data.message);
            }
            return; // KEEP THE OTP AREA VISIBLE
          }

          // Success
          if (res.ok && data?.success) {
            justSent = false;
            return;
          }

          // Other errors - rollback only for real failures
          alert(data?.message || @json(__('Failed to send code. Please try again.')));
          clearInterval(t);
          countdownEl.textContent = '';
          clearEndTs();
          btnEmailSend.disabled = false;
          emailResendMsg.style.display = 'none';
          setVisible(false);
          justSent = false;
        })
        .catch(() => {
          alert(@json(__('Network error. Please try again.')));
          clearInterval(t);
          countdownEl.textContent = '';
          clearEndTs();
          btnEmailSend.disabled = false;
          emailResendMsg.style.display = 'none';
          setVisible(false);
          justSent = false;
        });
    });
  }

  let initialized = false;
  
  document.addEventListener('DOMContentLoaded', () => {
    if (!initialized) {
      initialized = true;
      boot();
    }
  });
  
  window.addEventListener('pageshow', (e) => {
    if (e.persisted || initialized) {
      boot();
    }
  });
  
  document.addEventListener('livewire:navigated', boot);
})();
</script>
</div>
@endsection