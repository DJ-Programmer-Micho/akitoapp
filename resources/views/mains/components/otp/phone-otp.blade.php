@extends('mains.layout.app')
@section('business-content')
<div wire:ignore>
<div class="main">
  <x-mains.components.account.image-header-one />
  <x-mains.components.account.nav-one />

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-3 border">
        <form action="{{ route('verifyOTP', ['locale' => app()->getLocale()]) }}" method="post">
          @csrf
          <div class="form-content">
            <div class="form-group">
              <label>{{ __('Is That Your Phone Number?') }}</label>
              <input type="text" class="form-control" name="phone" value="{{ $phone }}" readonly>
              <input type="hidden" name="id" value="{{ $id }}">
            </div>

            {{-- Primary choice --}}
            <div id="choiceOTP" class="form-group">
              <a id="noPhone" href="{{ route('goRePhoneOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}"
                 class="btn btn-primary" style="width:100%; margin-bottom:10px;">
                {{ __('No, Change Number') }}
              </a>

              {{-- Channel buttons --}}
              <div class="actions mb-2" id="sendButtons">
                <a class="btn btn-outline-success"
                   id="btnWhatsApp"
                   data-channel="whatsapp"
                   href="{{ route('resendPhoneOTP', ['locale' => app()->getLocale(), 'id' => $id, 'phone' => $phone]) }}?channel=whatsapp&lang={{ $lang ?? app()->getLocale() }}">
                  <img src="{{ app('whatsapp-logo') }}" width="20" alt=""> WhatsApp
                </a>

                <a class="btn btn-outline-info"
                   id="btnTelegram"
                   data-channel="telegram"
                   href="{{ route('resendPhoneOTP', ['locale' => app()->getLocale(), 'id' => $id, 'phone' => $phone]) }}?channel=telegram&lang={{ $lang ?? app()->getLocale() }}">
                  <img src="{{ app('telegram-logo') }}" width="20" alt=""> Telegram
                </a>

                <a class="btn btn-outline-secondary"
                   id="btnSms"
                   data-channel="sms"
                   href="{{ route('resendPhoneOTP', ['locale' => app()->getLocale(), 'id' => $id, 'phone' => $phone]) }}?channel=sms&lang={{ $lang ?? app()->getLocale() }}">
                  <img src="{{ app('sms-logo') }}" width="20" alt=""> SMS
                </a>
              </div>

              <p class="help">
                {{ __('Tap a channel to receive your code. You can resend after the cooldown ends.') }}
                <span id="chosenPill" class="pill hide"></span>
              </p>
            </div>

            {{-- Fallback actions shown after first send --}}
            <div id="fallbackButtons" class="d-none" style="display:flex; gap:6px; margin:6px 0;">
              <button id="btnResendSame" type="button" class="btn btn-outline-primary" style="flex:1;">
                {{ __('Resend with same') }}
              </button>
              <button id="btnResendOther" type="button" class="btn btn-outline-secondary" style="flex:1;">
                {{ __('Try another channel') }}
              </button>
            </div>

            {{-- OTP input area --}}
            <div id="otpAreaPhone" class="d-none">
              <div class="form-group">
                <label for="entered_otp_code">{{ __('Enter OTP Code:') }}</label>
                <input id="entered_otp_code" type="text" class="form-control" name="entered_otp_code"
                       required inputmode="numeric" autocomplete="one-time-code">
              </div>

              <p class="mb-2" style="font-size: 16px;">
                <span style="font-size:10pt">{{ __('Please check your messages.') }}</span><br>
                <span style="font-size:8pt">{{ __('Please wait before clicking again.') }}</span>
                <span id="countdownPhone" style="font-size:8pt"></span>
              </p>

              <button type="submit" class="btn btn-outline-primary">{{ __('Submit') }}</button>
            </div>

            <div class="signup-message">
              <a class="danger">@error('phone'){{ $message }}@enderror</a>
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

<style>
.actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.actions .btn {
  flex: 1;
  min-width: calc(66.666% - 6px);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 8px 12px;
  font-size: 14px;
}

.help {
  font-size: 12px;
  color: #6c757d;
  margin-top: 8px;
  margin-bottom: 0;
}

.pill {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  margin-left: 5px;
}

.pill.hide {
  display: none;
}

.pill.whatsapp {
  background-color: #25D366;
  color: white;
}

.pill.telegram {
  background-color: #0088cc;
  color: white;
}

.pill.sms {
  background-color: #6c757d;
  color: white;
}

@media (max-width: 576px) {
  .actions .btn {
    min-width: 100%;
  }
}
</style>

<script>
(function() {
  const KEY_END     = 'otpCountdownPhone';
  const KEY_VIS     = 'otpPhoneVisible';
  const KEY_CHAN    = 'otpPhoneChannel';

  let t;
  let justSent = false;
  const otpArea      = document.getElementById('otpAreaPhone');
  const choiceArea   = document.getElementById('choiceOTP');
  const countdownEl  = document.getElementById('countdownPhone');
  const fallbackBox  = document.getElementById('fallbackButtons');
  const sendButtons  = document.getElementById('sendButtons');
  const btnWhatsApp  = document.getElementById('btnWhatsApp');
  const btnTelegram  = document.getElementById('btnTelegram');
  const btnSms       = document.getElementById('btnSms');
  const btnResendSame = document.getElementById('btnResendSame');
  const btnResendOther = document.getElementById('btnResendOther');
  const chosenPill   = document.getElementById('chosenPill');

  const channelButtons = { whatsapp: btnWhatsApp, telegram: btnTelegram, sms: btnSms };
  let currentChannel = null;

  function updatePill(channel) {
    if (!channel) {
      chosenPill.classList.add('hide');
      return;
    }

    const labels = {
      whatsapp: 'WhatsApp',
      telegram: 'Telegram',
      sms: 'SMS'
    };

    chosenPill.textContent = labels[channel] || channel;
    chosenPill.className = `pill ${channel}`;
  }

  function setButtonsDisabled(disabled) {
    Object.values(channelButtons).forEach(btn => {
      if (btn) btn.style.pointerEvents = disabled ? 'none' : 'auto';
      if (btn) btn.style.opacity = disabled ? '0.6' : '1';
    });
  }

  function setVisible(v) {
    localStorage.setItem(KEY_VIS, v ? '1' : '0');
    if (v) {
      otpArea.classList.remove('d-none');
      choiceArea.classList.add('d-none');
      fallbackBox.classList.remove('d-none');
    } else {
      otpArea.classList.add('d-none');
      choiceArea.classList.remove('d-none');
      fallbackBox.classList.add('d-none');
    }
  }

  function getEndTs() {
    const raw = localStorage.getItem(KEY_END);
    return raw ? parseInt(raw, 10) : 0;
  }
  function setEndTs(ts) { localStorage.setItem(KEY_END, String(ts)); }
  function clearEndTs() { localStorage.removeItem(KEY_END); }

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
        setButtonsDisabled(false);
      }
    }, 1000);
  }

  function restore() {
    if (justSent) {
      justSent = false;
      return;
    }

    const savedChan = localStorage.getItem(KEY_CHAN);
    if (savedChan) {
      currentChannel = savedChan;
      updatePill(savedChan);
    }

    const vis = localStorage.getItem(KEY_VIS) === '1';
    setVisible(vis);

    const left = Math.floor((getEndTs() - Date.now()) / 1000);
    if (vis && left > 0) {
      setButtonsDisabled(true);
      startCountdown(left);
    } else if (vis) {
      clearEndTs();
      setButtonsDisabled(false);
    }
  }

  function sendWith(channel, url) {
    justSent = true;
    currentChannel = channel;

    localStorage.setItem(KEY_CHAN, channel);
    updatePill(channel);
    setVisible(true);
    setButtonsDisabled(true);
    
    setTimeout(() => {
      startCountdown(window.OTP_RESEND_COOLDOWN);
    }, 50);

    fetch(url, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(async res => {
        const body = await res.json().catch(() => ({}));

        // Handle 429 - OTP was already sent recently
        if (res.status === 429) {
          justSent = false;
          if (body?.retry_in) {
            const retrySeconds = parseInt(body.retry_in, 10);
            if (retrySeconds > 0) {
              startCountdown(retrySeconds);
            }
          }
          if (body?.message) {
            console.log('Rate limit:', body.message);
          }
          return; // KEEP THE OTP AREA VISIBLE
        }

        // Success
        if (res.ok && body?.success) {
          justSent = false;
          return;
        }

        // Other errors - rollback only for real failures
        alert(body?.message || @json(__('Failed to send code. Try another channel.')));
        clearInterval(t);
        countdownEl.textContent = '';
        clearEndTs();
        setButtonsDisabled(false);
        setVisible(false);
        justSent = false;
      })
      .catch(() => {
        alert(@json(__('Network error. Try again.')));
        clearInterval(t);
        countdownEl.textContent = '';
        clearEndTs();
        setButtonsDisabled(false);
        setVisible(false);
        justSent = false;
      });
  }

  function boot() {
    restore();

    // Handle channel button clicks
    Object.entries(channelButtons).forEach(([channel, btn]) => {
      btn?.addEventListener('click', (e) => {
        e.preventDefault();
        const url = btn.getAttribute('href');
        sendWith(channel, url);
      });
    });

    // Resend with same channel
    btnResendSame?.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentChannel && channelButtons[currentChannel]) {
        const url = channelButtons[currentChannel].getAttribute('href');
        sendWith(currentChannel, url);
      }
    });

    // Try another channel - show choice area again
    btnResendOther?.addEventListener('click', (e) => {
      e.preventDefault();
      clearInterval(t);
      countdownEl.textContent = '';
      clearEndTs();
      setButtonsDisabled(false);
      setVisible(false);
      updatePill(null);
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