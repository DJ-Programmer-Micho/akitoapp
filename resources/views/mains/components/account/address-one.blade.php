<p>The following addresses will be used on the checkout page.</p>
<div class="row">
    {{-- @php
        dd($addresses);
    @endphp --}}
    @foreach ($addresses as $index => $address)
    <div class="col-lg-6">
        <div class="card card-dashboard ">
            <div class="card-body p-3">
                <h3 class="card-title text-center">{{$address->type ?? $index +1 }} </h3><!-- End .card-title -->
                <h3 class="card-title text-center">{{$address->address_name ?? $index +1 }} </h3><!-- End .card-title -->
                <p class="text-center">{{$address->phone_number}}</p>

                    <div class="d-flex justify-content-around">
                        <a href="{{ route('customer.addresses.edit', ['locale' => app()->getLocale(), 'addressId' => $address->id]) }}">Edit
                            <i class="icon-edit"></i>
                        </a>
                        <form action="{{ route('customer.addresses.delete', ['locale' => app()->getLocale(), 'addressId' => $address->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger" style="border-bottom: none">Remove <i class="fa-regular fa-trash-can"></i></button>
                        </form>
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