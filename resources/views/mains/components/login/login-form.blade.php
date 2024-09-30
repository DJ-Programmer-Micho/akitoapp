<div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="icon-close"></i></span>
                </button>

                <div class="form-box" style="padding: 0;">
                    <div class="tab-content" id="tab-content-5">
                        <div class="tab-pane fade show active" id="signin" role="tabpanel" aria-labelledby="signin-tab">
                            <div class="d-flex justify-content-center">

                                <a href=" {{ route('business.home', ['locale' => app()->getLocale()]) }}" class="logo">
                                    <img src="{{ app('cloudfront').'web-setting/logo2.png' }}" alt="Akito" width="120" height="20">
                                </a>
                            </div>
                            <form action="{{route('customer.login', ['locale' => app()->getLocale()])}}" method="POST">
                                @csrf
                                <div class="form-group p-1 mb-2">
                                  <label for="email-form-signin m-0">Email address</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text" style="padding: 0 .25em">
                                        <lord-icon
                                            src="https://cdn.lordicon.com/ebjjjrhp.json"
                                            trigger="loop"
                                            delay="2000"
                                            colors="primary:#3080e8,secondary:#000000"
                                            style="width:32px;height:32px">
                                        </lord-icon>
                                      </div>
                                    </div>
                                    <input type="email" class="form-control" id="email-form-signin" name="email-form-signin" required aria-describedby="emailHelp">
                                  </div>
                                  <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                                </div>
                                <div class="form-group p-1 mb-2">
                                  <label for="password-form-signin m-0">Password</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text" style="padding: 0 .25em">
                                        <lord-icon
                                            src="https://cdn.lordicon.com/khheayfj.json"
                                            trigger="loop"
                                            colors="primary:#3080e8,secondary:#000000"
                                            delay="2000"
                                            style="width:32px;height:32px">
                                        </lord-icon>
                                      </div>
                                    </div>
                                    <input type="password" class="form-control" id="password-form-signin" name="password-form-signin">
                                  </div>
                                  <a href="#" class="forgot-link">Forgot Your Password?</a>
                                </div>
                                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                <div class="g-recaptcha" id="feedback-recaptcha" data-sitekey="{!! env('GOOGLE_RECAPTCHA_KEY') !!}"></div>
                                @error('g-recaptcha-response')
                                <span class="danger" style="font-size: 12px">{{__('Please Check reCaptcha')}}</span><br>
                                @enderror
                                <div class="form-check p-0 mb-2">
                                    <input class="" type="checkbox" id="autoSizingCheck">
                                    <label class="form-check-label" for="autoSizingCheck">
                                      Remember me
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-outline-primary-2">LOG IN</button>
                                <p>Don't Have Account Yet? <a href="{{ route('business.register', ['locale' => app()->getLocale()]) }}" class="forgot-link">Register</a></p>
                              </form>
                        </div><!-- .End .tab-pane -->
                    </div><!-- End .tab-content -->
                </div><!-- End .form-box -->
            </div><!-- End .modal-body -->
        </div><!-- End .modal-content -->
    </div><!-- End .modal-dialog -->
</div><!-- End .modal -->