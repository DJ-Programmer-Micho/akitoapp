<p>The following addresses will be used on the checkout page.</p>
<div class="row">
    {{-- @php
        dd($addresses);
    @endphp --}}
    @foreach ($addresses as $index => $address)
    <div class="col-lg-6">
        <div class="card card-dashboard">
            <div class="card-body">
                <h3 class="card-title">{{$address->building_name ?? $index +1 }} </h3><!-- End .card-title -->

                <p>{{$address->phone_number}}<br>
                    <div class="d-flex">
                        <a href="#">Edit <i class="icon-edit"></i></a></p>
                        <a href="#" class="mx-3 text-danger">Remove <i class="fa-regular fa-trash-can"></i></a></p>
                    </div>
            </div><!-- End .card-body -->
        </div><!-- End .card-dashboard -->
    </div><!-- End .col-lg-6 -->
    @endforeach
    @if (count($addresses) < 5)
    <div class="col-lg-6">
        <div class="card card-dashboard">
            <a href="{{route('customer.address', ['locale' => app()->getLocale()])}}">
                <div class="card-body" style="border: 2px dashed black">
                    <i class="fa-solid fa-plus" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                </div>
            </a>
        </div>
    </div>
@endif
</div><!-- End .row -->