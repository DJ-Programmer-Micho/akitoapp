{{-- resources/views/mains/components/otp/re-phone-otp.blade.php --}}
@extends('mains.layout.app')

@section('business-content')
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
              <h4 class="mb-1">{{ __('Change phone number') }}</h4>
              <p class="text-muted mb-0" style="font-size: 13px;">
                {{ __('Enter the correct phone number to receive the OTP code.') }}
              </p>
            </div>

            <form id="loginForm"
                  action="{{ route('updateRePhoneOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}"
                  method="post">
              @csrf

              <div class="mb-3">
                <label class="form-label mb-1" for="phone">{{ __('Phone Number') }}</label>

                <div class="input-group">
                  <span class="input-group-text">
                    <i class="fa fa-phone"></i>
                  </span>
                  <input id="phone"
                         type="tel"
                         class="form-control form-control-lg"
                         name="phone"
                         required
                         inputmode="tel"
                         autocomplete="tel"
                         placeholder="{{ __('e.g. +9647xxxxxxxxx') }}">
                </div>

                @error('phone')
                  <div class="text-danger mt-2" style="font-size: 13px;">{{ $message }}</div>
                @enderror
              </div>

              <button type="submit" class="btn btn-primary w-100">
                {{ __('Update') }}
              </button>

              <div class="text-center mt-3">
                <a href="{{ route('goOTP', ['locale' => app()->getLocale(), 'id' => $id]) }}"
                   class="btn btn-link text-muted"
                   style="text-decoration:none;">
                  {{ __('Back to verification') }}
                </a>
              </div>

            </form>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
  .otp-card {
    border-radius: 14px;
    border: 1px solid rgba(0,0,0,.08);
  }

  .otp-icon img {
    filter: drop-shadow(0 6px 10px rgba(0,0,0,.10));
  }

  .input-group-text {
    border-radius: 12px 0 0 12px;
  }

  .form-control-lg {
    border-radius: 0 12px 12px 0;
  }
</style>
@endsection
