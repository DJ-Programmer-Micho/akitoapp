<div class="row mb-5">
    <div class="col-xl-12">
        <div class="card">
            {{-- Debugging preview (optional) --}}
            {{-- @php dd($orderTable) @endphp --}}
            
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Recent Orders</h4>
                <div class="flex-shrink-0">
                    <a href="{{route('super.orderManagements',['locale' => app()->getLocale()])}}" type="button" class="btn btn-soft-info btn-sm">
                        <i class="ri-file-list-3-line align-middle"></i> Check All
                    </a>
                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col">{{ __('Order ID') }}</th>
                                <th scope="col">{{ __('Customer') }}</th>
                                <th scope="col">{{ __('Order Items') }}</th>
                                <th scope="col">{{ __('Order Date') }}</th>
                                <th scope="col">{{ __('Shipping Amount') }}</th>
                                <th scope="col">{{ __('Total Amount (USD)') }}</th>
                                <th scope="col">{{ __('Total Amount (IQD)') }}</th>
                                <th scope="col">{{ __('Exchange Rate') }}</th>
                                <th scope="col">{{ __('Payment Method') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderTable as $order)
                                @php
                                    $orderIdLink = '#'.$order->tracking_number; 
                                    $customerName = $order->first_name.' '.$order->last_name;
                                    $itemsCount = $order->orderItems->count();
                                    $orderDate = $order->created_at;
                                    $shipping_amount = $order->shipping_amount; 
                                    $total_amount_usd = $order->total_amount_usd; 
                                    $total_amount_iqd = $order->total_amount_iqd; 
                                    $exchange_rate = $order->exchange_rate; 
                                    $paymentStatus = $order->payment_status; 
                                    $statusClass = match($paymentStatus) {
                                        'successful' => 'badge bg-success-subtle text-success',
                                        'pending'    => 'badge bg-warning-subtle text-warning',
                                        'failed'     => 'badge bg-danger-subtle text-danger',
                                        default      => 'badge bg-secondary-subtle text-secondary'
                                    };
                                    $orderStatus = $order->status; 
                                    $orderClass = match($orderStatus) {
                                        'pending' => 'badge bg-primary-subtle text-primary',
                                        'shipping'    => 'badge bg-secondary-subtle text-secondary',
                                        'delivered'     => 'badge bg-success-subtle text-success',
                                        'canceled'     => 'badge bg-danger-subtle text-danger',
                                        'refunded'     => 'badge bg-warning-subtle text-warning',
                                        default      => 'badge bg-primary-subtle text-primary'
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <a href="#" class="fw-medium link-primary">{{ $orderIdLink }}</a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img src="{{ $order->customer->customer_profile->avatar 
                                                ? app('cloudfront').$order->customer->customer_profile->avatar 
                                                : app('userImg') }}"
                                                 alt="{{ $order->customer->customer_profile->first_name 
                                                     . ' ' 
                                                     . $order->customer->customer_profile->last_name }}"
                                                 class="img-thumbnail rounded-circle"
                                                 style="width:40px;height:40px;object-fit:cover;"
                                            />
                                            </div>
                                            <div class="flex-grow-1">{{ $customerName }}</div>
                                        </div>
                                    </td>
                                    <td>{{$itemsCount}}</td>
                                    <td>{{$orderDate}}</td>
                                    <td>
                                        <span class="cart-total-price flip-symbol text-left">
                                            <span class="amount text-info">{{ number_format($shipping_amount, 0)}} </span>
                                            <span class="currency text-info">{{ __('IQD') }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success">$ {{ number_format($total_amount_usd,2) }}</span>
                                    </td>
                                    <td>
                                        <span class="cart-total-price flip-symbol text-left">
                                            <span class="amount text-success">{{ number_format($total_amount_iqd, 0)}} </span>
                                            <span class="currency text-success">{{ __('IQD') }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="cart-total-price flip-symbol text-left">
                                            <span class="amount text-info">{{ number_format($exchange_rate, 0)}} </span>
                                            <span class="currency text-info">{{ __('IQD') }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $statusClass }}">
                                            {{ ucfirst($paymentStatus) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $orderClass }}">
                                            {{ ucfirst($orderStatus) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div>
        </div> <!-- .card-->
    </div> <!-- .col-->
</div> <!-- end row-->
