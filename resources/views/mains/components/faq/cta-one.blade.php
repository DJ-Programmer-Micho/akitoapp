<div class="cta cta-display bg-image pt-4 pb-4" style="background-image: url('{{$bgImg}}');">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9 col-xl-7">
                <div class="row no-gutters flex-column flex-sm-row align-items-sm-center">
                    <div class="col">
                        <h3 class="cta-title text-white">If You Have More Questions</h3><!-- End .cta-title -->
                        {{-- <p class="cta-desc text-white">Quisque volutpat mattis eros</p><!-- End .cta-desc --> --}}
                    </div><!-- End .col -->

                    <div class="col-auto">
                        <a href="{{route('business.contactus',['locale' => app()->getLocale()])}}" class="btn btn-outline-white"><span>CONTACT US</span><i class="icon-long-arrow-right"></i></a>
                    </div><!-- End .col-auto -->
                </div><!-- End .row no-gutters -->
            </div><!-- End .col-md-10 col-lg-9 -->
        </div><!-- End .row -->
    </div><!-- End .container -->
</div><!-- End .cta -->