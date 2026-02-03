{{-- resources/views/mains/components/otp/phone-otp.blade.php --}}
@extends('mains.layout.app')

@section('business-content')
<div wire:ignore>
  <div class="main mb-5">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />

    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card otp-card shadow-sm">
            <div class="card-body p-4">

              <div class="text-center mb-3">
                <div class="otp-icon mb-2">
                  <img src="{{ asset('main/assets/images/otp/pngotp.png') }}" alt="otp" width="54">
                </div>
                <h4 class="mb-1">{{ __('Verify your phone') }}</h4>
                <p class="text-muted mb-0" style="font-size: 13px;">
                  {{ __('Choose a channel to receive your OTP code.') }}
                </p>
              </div>

              <form action="{{ route('verifyOTP', ['locale' => app()->getLocale()]) }}" method="post">
                @csrf

                {{-- Phone info --}}
                <div class="mb-3">
                  <label class="form-label mb-1">{{ __('Phone Number') }}</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fa fa-phone"></i>
                    </span>
                    <input type="text" class="form-control" name="phone" value="{{ $phone }}" readonly>
                  </div>
                  <input type="hidden" name="id" value="{{ $id }}">
                </div>

                {{-- Primary choice --}}
                <div id="choiceOTP" class="mb-3">

                  <a id="noPhone"
                     href="{{ route('goRePhoneOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}"
                     class="btn btn-light w-100 otp-change-btn mb-3">
                    {{ __('No, Change Number') }}
                  </a>

                  <div class="actions mb-2" id="sendButtons">
                    <a class="btn btn-outline-success otp-btn"
                       id="btnWhatsApp"
                       data-channel="whatsapp"
                       href="{{ route('resendPhoneOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}?channel=whatsapp&lang={{ $lang ?? app()->getLocale() }}">
                      <img src="{{ app('whatsapp-logo') }}" width="18" alt="">
                      <span>WhatsApp</span>
                    </a>

                    <a class="btn btn-outline-info otp-btn"
                       id="btnTelegram"
                       data-channel="telegram"
                       href="{{ route('resendPhoneOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}?channel=telegram&lang={{ $lang ?? app()->getLocale() }}">
                      <img src="{{ app('telegram-logo') }}" width="18" alt="">
                      <span>Telegram</span>
                    </a>

                    <a class="btn btn-outline-secondary otp-btn"
                       id="btnSms"
                       data-channel="sms"
                       href="{{ route('resendPhoneOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}?channel=sms&lang={{ $lang ?? app()->getLocale() }}">
                      <img src="{{ app('sms-logo') }}" width="18" alt="">
                      <span>SMS</span>
                    </a>
                  </div>

                  <div class="otp-help mt-2">
                    <span class="text-muted" style="font-size: 12px;">
                      {{ __('Tap a channel to receive your code. You can resend after the cooldown ends.') }}
                    </span>
                    <span id="chosenPill" class="pill hide"></span>
                  </div>
                </div>

                {{-- Fallback actions shown after first send --}}
                <div id="fallbackButtons" class="d-none mb-3" style="display:flex; gap:8px;">
                  <button id="btnResendSame" type="button" class="btn btn-outline-primary w-50">
                    {{ __('Resend same') }}
                  </button>
                  <button id="btnResendOther" type="button" class="btn btn-outline-secondary w-50">
                    {{ __('Other channel') }}
                  </button>
                </div>

                {{-- OTP input area --}}
                <div id="otpAreaPhone" class="d-none">
                  <div class="mb-2">
                    <label class="form-label mb-1" for="entered_otp_code">{{ __('OTP Code') }}</label>
                    <input id="entered_otp_code"
                           type="text"
                           class="form-control form-control-lg text-center otp-input"
                           name="entered_otp_code"
                           required
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           placeholder="••••••">
                  </div>

                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted" style="font-size: 12px;">
                      {{ __('Check your messages') }}
                    </span>
                    <span class="text-muted" style="font-size: 12px;">
                      <span>{{ __('Resend in') }}</span>
                      <span id="countdownPhone"></span>
                    </span>
                  </div>

                  <button type="submit" class="btn btn-primary w-100">
                    {{ __('Verify') }}
                  </button>
                </div>

                {{-- Errors --}}
                <div class="mt-3">
                  <div class="text-danger" style="font-size: 13px;">
                    @error('phone'){{ $message }}@enderror
                  </div>
                </div>

              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    window.OTP_RESEND_COOLDOWN = {{ (int) config('otp.resend_cooldown') }};
    window.OTP_EXPIRES_MIN     = {{ (int) config('otp.expires_minutes') }};
  </script>

  <style>
    .otp-card {
      border-radius: 14px;
      border: 1px solid rgba(0,0,0,.08);
    }

    .otp-icon img {
      filter: drop-shadow(0 6px 10px rgba(0,0,0,.10));
    }

    .otp-change-btn {
      border-radius: 10px;
      border: 1px dashed rgba(0,0,0,.15);
    }

    .actions {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .otp-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px 12px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 14px;
    }

    .otp-input {
      letter-spacing: 6px;
      border-radius: 12px;
    }

    .otp-help {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      flex-wrap: wrap;
    }

    .pill {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
    }
    .pill.hide { display: none; }

    .pill.whatsapp { background:#25D366; color:#fff; }
    .pill.telegram { background:#0088cc; color:#fff; }
    .pill.sms      { background:#6c757d; color:#fff; }

    @media (min-width: 576px) {
      .actions {
        grid-template-columns: 1fr 1fr;
      }
      #btnSms {
        grid-column: 1 / -1;
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