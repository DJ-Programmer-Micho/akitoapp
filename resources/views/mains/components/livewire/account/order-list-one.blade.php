{{-- resources/views/mains/components/livewire/account/order-list-one.blade.php --}}
<div>
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
                            <th>{{ __('Order ID') }}</th>
                            <th class="text-center">{{ __('Price') }}</th>
                            <th class="text-center">{{ __('Payment Method') }}</th>
                            <th class="text-center">{{ __('Payment Status') }}</th>
                            <th class="text-center">{{ __('Order Status') }}</th>
                            <th class="text-center">{{ __('Date') }}</th>
                            <th class="text-center">{{ __('Change Date') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderTable as $item)
                            @php
                                $hasRefundLock     = $refundPendingMap[$item->id]  ?? false; // net locked > 0
                                $hasRefundRejected = $refundRejectedMap[$item->id] ?? false; // admin denied

                                // Can pay: not cancelled/refunded, payment not successful/refunded
                                $canPay = !in_array($item->status, ['cancelled', 'refunded'], true)
                                    && !in_array($item->payment_status, ['successful', 'refunded'], true);

                                // Can cancel: not already terminal AND no pending refund lock
                                $canCancel = !in_array($item->status, ['cancelled', 'refunded', 'delivered'], true)
                                    && !$hasRefundLock;
                            @endphp
                            <tr>
                                {{-- ORDER ID / RECEIPT LINK --}}
                                <td>
                                    @if ($item->status == 'cancelled')
                                        <a target="_blank"
                                           href="{{ route('pdf.order.customer.cancel', ['locale' => app()->getLocale(), 'tracking' => $item->tracking_number]) }}">
                                            #{{ $item->tracking_number }}
                                        </a>
                                    @else
                                        <a target="_blank"
                                           href="{{ route('pdf.order.customer', ['locale' => app()->getLocale(), 'tracking' => $item->tracking_number]) }}">
                                            #{{ $item->tracking_number }}
                                        </a>
                                    @endif
                                </td>

                                {{-- TOTAL PRICE (items + shipping) --}}
                                <td class="align-middle text-center">
                                    <span class="cart-total-price flip-symbol text-left">
                                        <span class="amount">
                                            {{ number_format(($item->total_amount_iqd ?? 0) + ($item->shipping_amount ?? 0), 0) }}
                                        </span>
                                        <span class="currency">{{ __('IQD') }}</span>
                                    </span>
                                </td>

                                {{-- PAYMENT METHOD --}}
                                <td class="align-middle text-center">
                                    {{ $item->payment_method }}
                                </td>

                                {{-- PAYMENT STATUS (with "refund pending" support) --}}
                                <td class="align-middle text-center">
                                    @if ($item->payment_status === 'refunded')
                                        <span class="badge bg-warning text-dark p-2">
                                            {{ __('Refunded') }}
                                        </span>

                                    @elseif ($hasRefundLock)
                                        <span class="badge bg-info text-white p-2">
                                            {{ __('Refund Pending') }}
                                        </span>

                                    @elseif ($hasRefundRejected)
                                        <span class="badge bg-danger text-white p-2">
                                            {{ __('Refund Rejected') }}
                                        </span>

                                    @elseif ($item->payment_status === 'successful' || $item->payment_status === 'paid')
                                        <span class="badge bg-success text-white p-2">
                                            {{ __('Successful') }}
                                        </span>

                                    @elseif ($item->payment_status === 'failed')
                                        <span class="badge bg-danger text-white p-2">
                                            {{ __('Failed') }}
                                        </span>

                                    @else
                                        <span class="badge bg-primary text-white p-2">
                                            {{ __('Pending') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- ORDER / SHIPPING STATUS --}}
                                <td class="align-middle text-center">
                                    @if ($item->status === 'pending')
                                        <span class="badge bg-primary text-white p-2">
                                            {{ __('Pending') }}
                                        </span>
                                    @elseif ($item->status === 'delivered')
                                        <span class="badge bg-success text-white p-2">
                                            {{ __('Delivered') }}
                                        </span>
                                    @elseif ($item->status === 'shipping')
                                        <span class="badge bg-secondary text-white p-2">
                                            {{ __('Shipping') }}
                                        </span>
                                    @elseif ($item->status === 'cancelled')
                                        <span class="badge bg-danger text-white p-2">
                                            {{ __('Cancelled') }}
                                        </span>
                                    @elseif ($item->status === 'refunded')
                                        <span class="badge bg-warning text-dark p-2">
                                            {{ __('Refunded') }}
                                        </span>
                                    @else
                                        <span class="badge bg-dark text-white p-2">
                                            {{ __('Unknown') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- CREATED / UPDATED --}}
                                <td class="align-middle text-center">
                                    {{ $item->created_at->format('Y-m-d') }}
                                </td>
                                <td class="align-middle text-center">
                                    {{ $item->updated_at->format('Y-m-d') }}
                                </td>

                                {{-- ACTIONS --}}
                                <td class="align-middle text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                                data-toggle="dropdown" aria-expanded="false">
                                            {{ __('Options') }}
                                        </button>
                                        <div class="dropdown-menu bg-light">

                                            {{-- View Receipt --}}
                                            @if ($item->status == 'cancelled')
                                                <p>
                                                    <a target="_blank" class="dropdown-item"
                                                       href="{{ route('pdf.order.customer.cancel', ['locale' => app()->getLocale(), 'tracking' => $item->tracking_number]) }}">
                                                        <i class="fa-regular fa-eye"></i> {{ __('View Receipt') }}
                                                    </a>
                                                </p>
                                            @else
                                                <p>
                                                    <a target="_blank" class="dropdown-item"
                                                       href="{{ route('pdf.order.customer', ['locale' => app()->getLocale(), 'tracking' => $item->tracking_number]) }}">
                                                        <i class="fa-regular fa-eye"></i> {{ __('View Receipt') }}
                                                    </a>
                                                </p>
                                            @endif

                                            {{-- Pay Now (only if not paid/refunded & not cancelled) --}}
                                            @if ($canPay)
                                                <p>
                                                    <a class="dropdown-item"
                                                       href="{{ route('business.checkout.order', ['locale' => app()->getLocale(), 'orderId' => $item->id]) }}">
                                                        <i class="fa-solid fa-money-check-dollar"></i>
                                                        {{ __('Pay Now') }}
                                                    </a>
                                                </p>
                                            @endif

                                            {{-- Cancel Order (normal cancel or refund request) --}}
                                            @if ($canCancel)
                                                <p>
                                                    <button class="dropdown-item"
                                                            onclick="cancelUserOrder('{{ $item->id }}')">
                                                        <i class="fa-solid fa-xmark"></i> {{ __('Cancel Order') }}
                                                    </button>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{ __('No Items Are Added') }}
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orderTable->links('pagination::bootstrap-4') }}
            </div>
        @else
            <div class="tab-pane">
                <div class="py-4 text-center">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                               colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                    </lord-icon>
                    <h5 class="mt-4">{{ __('Sorry! No Result Found') }}</h5>
                </div>
            </div>
        @endif

        {{-- Cancel via AJAX -> calls OrderActionController::cancelOrder --}}
        <script>
            function cancelUserOrder(id) {
                fetch(`{{ route('action.payment.cancel', ['locale' => app()->getLocale(), 'orderId' => '__PAYMENT_ID__']) }}`
                    .replace('__PAYMENT_ID__', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({reason: 'User Cancelled'})
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json()
                                .catch(() => ({}))
                                .then(err => {
                                    throw new Error(err.message || 'HTTP Error ' + response.status);
                                });
                        }
                        return response.json();
                    })
                    .then(data => {
                        toastr.success(
                            data.message || "Your order has been successfully cancelled.",
                            "Cancelled",
                            {
                                "closeButton": true,
                                "progressBar": true,
                                "positionClass": "toast-bottom-right",
                                "timeOut": "5000"
                            }
                        );

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        console.error(error);
                        toastr.error(
                            error.message || "Failed to cancel the order. Please try again.",
                            "Error",
                            {
                                "closeButton": true,
                                "progressBar": true,
                                "positionClass": "toast-bottom-right",
                                "timeOut": "5000"
                            }
                        );
                    });
            }
        </script>
    </div>
</div>
