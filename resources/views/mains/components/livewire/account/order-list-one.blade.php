<div>
    {{-- {{$orderTable}} --}}
    <div class="card-body">
        <style>
            .table td {
                padding: 5px 0px;
            }
        </style>
        @if ($orderTable->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-cart table-mobile">
                <thead>
                    <tr>
                        <th>{{_('Order ID')}}</th>
                        <th class="text-center">{{_('Price')}}</th>
                        <th class="text-center">{{_('Payment Method')}}</th>
                        <th class="text-center">{{_('Payment Status')}}</th>
                        <th class="text-center">{{_('Order Status')}}</th>
                        <th class="text-center">{{_('Date')}}</th>
                        <th class="text-center">{{_('Change Date')}}</th>
                        <th class="text-center">{{_('Actions')}}</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($orderTable as $item)
                    <tr>
                        <td>
                            #{{$item->tracking_number}}
                        </td>
                        <td class="align-middle text-center">
                            <span class="cart-total-price flip-symbol text-left">
                                <span class="amount">{{ number_format($item->total_amount_iqd + $item->shipping_amount, 0)}} </span>
                                <span class="currency">{{ __('IQD') }}</span>
                            </span>
                        </td>
                        <td class="align-middle text-center">
                            {{$item->payment_method}}
                        </td>
                        <td class="align-middle text-center">
                            @if ($item->payment_status == 'successful')
                            <span class="badge bg-success text-white p-2">
                                {{__('Successful')}}
                            </span>
                            @elseif ($item->payment_status == 'failed')
                            <span class="badge bg-danger text-white p-2">
                                {{__('Faild')}}
                            </span>
                            @else
                            <span class="badge bg-primary text-white p-2">
                                {{__('Pending')}}
                            </span>
                            @endif
                        </td>
                        <td class="align-middle text-center">
                            @if ($item->status == 'pending')
                            <span class="badge bg-primary text-white p-2">
                                {{__('Pending')}}
                            </span>
                            @elseif ($item->status == 'delivered')
                            <span class="badge bg-success text-white p-2">
                                {{__('Delivered')}}
                            </span>
                            @elseif ($item->status == 'shipping')
                            <span class="badge bg-secondary text-white p-2">
                                {{__('Shipping')}}
                            </span>
                            @elseif ($item->status == 'canceled')
                            <span class="badge bg-danger text-white p-2">
                                {{__('Cancelled')}}
                            </span>
                            @else
                            <span class="badge bg-warning text-dark p-2">
                                {{__('refunded')}}
                            </span>
                            @endif
                        </td>
                        <td class="align-middle text-center">
                            {{$item->created_at->format('Y-m-d')}}
                        </td>
                        <td class="align-middle text-center">
                            {{$item->updated_at->format('Y-m-d')}}
                        </td>
                        <td class="align-middle text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                  {{_('Options')}}
                                </button>
                                <div class="dropdown-menu bg-light">
                                @if ($item->status == 'canceled')
                                <p><a class="dropdown-item" href="{{route('pdf.order.customer.cancel', ['locale' => app()->getLocale(),'tracking' => $item->tracking_number])}}"><i class="fa-regular fa-eye"></i> {{__('View Reciept')}}</a></p>
                                @else
                                <p><a class="dropdown-item" href="{{route('pdf.order.customer', ['locale' => app()->getLocale(),'tracking' => $item->tracking_number])}}"><i class="fa-regular fa-eye"></i> {{__('View Reciept')}}</a></p>
                                @endif
                                  @if ($item->status != 'canceled' && $item->payment_status != 'successful')
                                  <p><a class="dropdown-item" href="{{ route('business.checkout.order', ['locale' => app()->getLocale(), 'orderId' => $item->id]) }}"><i class="fa-solid fa-money-check-dollar"></i> {{__('Pay Now')}}</a></p>
                                  @endif
                                  @if ($item->status != 'canceled' && $item->payment_status != 'successful')
                                  <p><button class="dropdown-item" onclick="cancelUserOrder('{{$item->id}}')"><i class="fa-solid fa-xmark"></i> {{__('Cancel Order')}}</button></p>
                                  @endif
                                </div>
                            </div>
                            </div>
                            {{-- <a href="{{route('pdf.order.action', ['locale' => app()->getLocale(),'tracking' => $item->tracking_number])}}"  class="btn btn-outline-primary">
                                {{__('view Details')}}
                            </a> --}}
                        </td>
                    </tr>
                    @empty
                    {{__('No Items Are Added')}}
        
                    @endforelse
                </tbody>
            </table><!-- End .table table-wishlist -->

        </div>
        <div class="mt-4">
            {{ $orderTable->links('pagination::bootstrap-4') }}
        </div>
        @else
        <div class="tab-pane">
            <div class="py-4 text-center">
                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                </lord-icon>
                <h5 class="mt-4">{{__('Sorry! No Result Found')}}</h5>
            </div>
        </div>
        @endif
    <!-- end card body -->
    <script>
        function cancelUserOrder(id) {
            fetch(`{{ route('action.payment.cancel', ['orderId' => '__PAYMENT_ID__']) }}`.replace('__PAYMENT_ID__', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason: 'User Cancelled' })
            }).then(response => {
                if (!response.ok) {
                    console.log(response)
                    throw new Error(`HTTP Error 5: ${respons.body}`);
                }
                return response.json();
            }).then(data => {
                toastr.success("Your order has been successfully cancelled.", "Cancelled", {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "timeOut": "5000"
                });
    
                setTimeout(() => {
                    location.reload();
                }, 1000);
            })
            .catch(error => {
                console.log('asd',error)
                toastr.error("Failed to cancel the order. Please try again.", "Error", {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-bottom-right",
                    "timeOut": "5000"
                });
            });
        }
    </script>
    
</div>
</div>