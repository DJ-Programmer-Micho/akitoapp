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
                        <th>Order ID</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Payment Method</th>
                        <th class="text-center">Payment Status</th>
                        <th class="text-center">Order Status</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Actions</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($orderTable as $item)
                    <tr>
                        <td>
                            #{{$item->tracking_number}}
                        </td>
                        <td class="align-middle text-center">
                            ${{ $item->total_amount }}
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
                            {{$item->created_at}}
                        </td>
                        <td class="align-middle text-center">
                            <a href="{{route('pdf.order.action', ['locale' => app()->getLocale(),'tracking' => $item->tracking_number])}}"  class="btn btn-outline-primary">
                                {{__('view Details')}}
                            </a>
                        </td>
                    </tr>
                    @empty
                    {{__('No Items Are Added')}}
        
                    @endforelse
                </tbody>
            </table><!-- End .table table-wishlist -->

        </div>
        <div class="mt-4">
            {{ $orderTable->links() }}
        </div>
        @else
        <div class="tab-pane">
            <div class="py-4 text-center">
                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                </lord-icon>
                <h5 class="mt-4">Sorry! No Result Found</h5>
            </div>
        </div>
        @endif
    <!-- end card body -->
</div>
</div>