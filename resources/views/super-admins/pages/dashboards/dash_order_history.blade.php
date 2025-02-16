<div class="row">
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
                                <th scope="col">Order ID</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Order Items</th>
                                <th scope="col">Order Date</th>
                                <th scope="col">Shipping Amount</th>
                                <th scope="col">Total Amount</th>
                                <th scope="col">Payment Method</th>
                                <th scope="col">Status</th>
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
                                    $total_amount = $order->total_amount; 
                                    $paymentStatus = $order->payment_status; 
                                    $statusClass = match($paymentStatus) {
                                        'successful' => 'badge bg-success-subtle text-success',
                                        'pending'    => 'badge bg-warning-subtle text-warning',
                                        'failed'     => 'badge bg-danger-subtle text-danger',
                                        default      => 'badge bg-secondary-subtle text-secondary'
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <a href="#" class="fw-medium link-primary">{{ $orderIdLink }}</a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                {{-- <img src="{{ $order->customer_profile->avatar 
                                                ? app('cloudfront').$order->customer_profile->avatar 
                                                : app('userImg') }}"
                                                 alt="{{ $order->customer_profile->first_name 
                                                     . ' ' 
                                                     . $order->customer_profile->last_name }}"
                                                 class="img-thumbnail rounded-circle"
                                                 style="width:40px;height:40px;object-fit:cover;"
                                            /> --}}
                                            </div>
                                            <div class="flex-grow-1">{{ $customerName }}</div>
                                        </div>
                                    </td>
                                    <td>{{$itemsCount}}</td>
                                    <td>{{$orderDate}}</td>
                                    <td>
                                        <span class="text-success">${{ number_format($shipping_amount, 2) }}</span>
                                    </td>
                                    <td>{{ number_format($total_amount) }}</td>
                                    <td>
                                        <span class="{{ $statusClass }}">
                                            {{ ucfirst($paymentStatus) }}
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
