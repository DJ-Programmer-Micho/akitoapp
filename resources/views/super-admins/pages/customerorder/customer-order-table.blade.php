<div class="page-content">
    <style>
        .filter-colors a {
        position: relative;
        display: block;
        width: 2.4rem;
        height: 2.4rem;
        border-radius: 50%;
        border: .2rem solid #fff;
        margin: 0 .3rem .3rem;
        transition: box-shadow .35s ease;
    }
    </style>
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{__('Orders')}}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                                <li class="breadcrumb-item active">{{__('Orders Table')}}</li>
                            </ol>
                        </div>
    
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card" id="orderList">
                <div class="card-header border-0">
                    <div class="row align-items-center gy-3">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Orders Of {{$fullName['first_name'] . ' ' . $fullName['last_name']}}</h5>
                        </div>
                        <div class="col-sm-auto">
                            {{-- <div class="d-flex gap-1 flex-wrap">
                                <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal"><i class="ri-add-line me-1"></i> Create Order</button>
                                <button type="button" class="btn btn-info"><i class="ri-file-download-line me-1"></i> Import</button>
                                <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form>
                        <div class="row g-3">
                            <div class="col-xxl-4 col-sm-6">
                                <div class="search-box">
                                    <input type="text" class="form-control search" placeholder="Search for order ID, customer, order status or something..."
                                           wire:model="searchTerm">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-3">
                                <div>
                                    <input type="date" class="form-control" wire:model="startDate" placeholder="Start Date">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-sm-3">
                                <div>
                                    <input type="date" class="form-control" wire:model="endDate" placeholder="End Date">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-4">
                                <div>
                                    <select class="form-control" data-choices data-choices-search-false wire:model="statusPaymentFilter">
                                        <option value="">Status</option>
                                        <option value="all">All</option>
                                        <option value="pending">Pending ({{$pPending}})</option>
                                        <option value="successful">Successful ({{$pPayed}})</option>
                                        <option value="failed">Failed ({{$pFailed}})</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-4">
                                <div>
                                    <select class="form-control" data-choices data-choices-search-false wire:model="statusPaymentMethodFilter">
                                        <option value="">Select Payment</option>
                                        <option value="all">All</option>
                                        <option value="Cash On Delivery">Cash On Delivery</option>
                                        <option value="Credit Card">Credit Card</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            {{-- <div class="col-xxl-1 col-sm-4">
                                <div>
                                    <button type="button" class="btn btn-primary w-100" onclick="SearchData();"> <i class="ri-equalizer-fill me-1"></i>
                                        Filters
                                    </button>
                                </div>
                            </div> --}}
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
                <div class="card-body pt-0">
                    <div>
                        <ul wire:ignore class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" role="tab" aria-selected="true" wire:click="changeTab('all')" >
                                    <i class="fa-solid fa-store me-1"></i> All Orders <span class="badge bg-danger align-middle ms-1">{{$oAll}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Pending" data-bs-toggle="tab" id="pending" role="tab" aria-selected="false" wire:click="changeTab('pending')" >
                                    <i class="fa-regular fa-hourglass-half me-1"></i> Pending <span class="badge bg-danger align-middle ms-1">{{$oPending}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 shipping" data-bs-toggle="tab" id="shipping" role="tab" aria-selected="false" wire:click="changeTab('shipping')" >
                                    <i class="fa-solid fa-truck-moving me-1"></i> Shipping <span class="badge bg-danger align-middle ms-1">{{$oShipping}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Delivered" data-bs-toggle="tab" id="delivered" role="tab" aria-selected="false" wire:click="changeTab('delivered')" >
                                    <i class="fa-regular fa-circle-check me-1"></i> Delivered <span class="badge bg-danger align-middle ms-1">{{$oDelivered}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Returns" data-bs-toggle="tab" id="Returns" role="tab" aria-selected="false" wire:click="changeTab('refunded')" >
                                    <i class="fa-regular fa-circle-xmark me-1"></i> Returns <span class="badge bg-danger align-middle ms-1">{{$oRefunded}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Cancelled" data-bs-toggle="tab" id="canceled" role="tab" aria-selected="false" wire:click="changeTab('canceled')" >
                                    <i class="fa-regular fa-face-frown me-1"></i> Cancelled <span class="badge bg-danger align-middle ms-1">{{$oCancelled}}</span>
                                </a>
                            </li>
                        </ul>

                        <div class="table-responsive table-card mb-1">
                            <table class="table table-nowrap align-middle" id="orderTable">
                                <thead class="text-muted table-light">
                                    <tr class="text-uppercase">
                                        {{-- <th scope="col" style="width: 25px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                            </div>
                                        </th> --}}
                                        <th class="sort" data-sort="id">Order ID</th>
                                        <th class="sort" data-sort="customer_name">Customer</th>
                                        <th class="sort" data-sort="date">Order Date</th>
                                        <th class="sort" data-sort="product_name">Shipping Amount</th>
                                        <th class="sort" data-sort="amount">Total Amount</th>
                                        <th class="sort" data-sort="payment">Payment Method</th>
                                        <th class="sort" data-sort="payment">Payment Status</th>
                                        <th class="sort" data-sort="status">Delivery Status</th>
                                        <th class="sort" data-sort="city">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse ($orderTable as $order)
                                    <tr>
                                        {{-- <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                            </div>
                                        </th> --}}
                                        <td class="id"><a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">#{{$order->tracking_number}}</a></td>
                                        <td class="customer_name">{{$order->first_name}} {{$order->last_name}}</td>
                                        {{-- <td class="product_name">Puma Tshirt</td> --}}
                                        <td class="date">{{$order->created_at}}</td>
                                        <td class="amount">{{$order->shipping_amount}}</td>
                                        <td class="amount">{{$order->total_amount}}</td>
                                        <td class="payment">{{$order->payment_method}}</td>
                                        @if ($order->payment_status == "pending")
                                        <td class="status"><span class="badge bg-warning-subtle text-warning text-uppercase">{{$order->payment_status}}</span></td>
                                        @elseif ($order->payment_status == "successful")
                                        <td class="status"><span class="badge bg-success-subtle text-success text-uppercase">{{$order->payment_status}}</span></td>
                                        @else
                                        <td class="status"><span class="badge bg-danger-subtle text-danger text-uppercase">{{$order->payment_status}}</span></td>
                                        @endif
                                        @if ($order->status == "pending")
                                        <td class="status"><span class="badge bg-warning-subtle text-warning text-uppercase">{{$order->status}}</span></td>
                                        @elseif ($order->status == "shipping")
                                        <td class="status"><span class="badge bg-primary-subtle text-primary text-uppercase">{{$order->status}}</span></td>
                                        @elseif ($order->status == "delivered")
                                        <td class="status"><span class="badge bg-success-subtle text-success text-uppercase">{{$order->status}}</span></td>
                                        @elseif ($order->status == "canceled")
                                        <td class="status"><span class="badge bg-danger-subtle text-danger text-uppercase">{{$order->status}}</span></td>
                                        @else
                                        <td class="status"><span class="badge bg-secondary-subtle text-secondary text-uppercase">{{$order->status}}</span></td>
                                        @endif
                                        <td>
                                            <span>
                                                <div class="dropdown">
                                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" style="">
                                                        <li wire:ignore>
                                                            <a href="{{ route('super.orderManagementsViewer', ['locale' => app()->getLocale(), 'id' => $order->id]) }}" class="dropdown-item edit-list">
                                                                <i class="fa-regular fa-pen-to-square me-2"></i>{{__('View')}}
                                                            </a>
                                                        </li>
                                                        <li wire:ignore>
                                                            <a href="{{ route('super.orderInvoice', ['locale' => app()->getLocale(), 'tracking' => $order->tracking_number]) }}" class="dropdown-item edit-list">
                                                                <i class="fa-solid fa-print me-1"></i>{{__('Print')}}
                                                            </a>
                                                        </li>
                                                        <li class="dropdown-divider"></li>
                                                        <li>
                                                            <button class="dropdown-item" type="button" wire:click="updateStatus({{ $order->id }}, 'pending')">
                                                                <span class="text-warning"><i class="fa-regular fa-hourglass-half "></i> {{__('Pending')}}</span>
                                                            </button>
                                                            <button class="dropdown-item" type="button" wire:click="updateStatus({{ $order->id }}, 'shipping')">
                                                                <span class="text-primary"><i class="fa-solid fa-truck-moving me-1"></i> {{__('Shipping')}}</span>
                                                            </button>
                                                            <button class="dropdown-item" type="button" wire:click="updateStatus({{ $order->id }}, 'delivered')">
                                                                <span class="text-success"><i class="fa-regular fa-circle-check me-1"></i> {{__('Delivered')}}</span>
                                                            </button>
                                                            <button class="dropdown-item" type="button" wire:click="updateStatus({{ $order->id }}, 'canceled')">
                                                                <span class="text-danger"><i class="fa-regular fa-face-frown me-1"></i> {{__('Canceled')}}</span>                                                            
                                                            </button>
                                                            <button class="dropdown-item" type="button" wire:click="updateStatus({{ $order->id }}, 'refunded')">
                                                                <span class="text-secondary"><i class="fa-regular fa-circle-xmark me-1"></i> {{__('Refunded')}}</span>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="noresult" style="display: block">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px">
                                                    </lord-icon>
                                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                                    <p class="text-muted">We've searched more than {{$oAll}} Orders We did not find any orders for you search.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                        <div class="d-flex justify-content-end">
                            <div class="pagination-wrap hstack gap-2">
                                {{ $orderTable->links('pagination::bootstrap-4') }}
                                {{-- <a class="page-item pagination-prev disabled" href="#">
                                    Previous
                                </a>
                                <ul class="pagination listjs-pagination mb-0"></ul>
                                <a class="page-item pagination-next" href="#">
                                    Next
                                </a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @push('tProductScripts')
        <script>
            function updatePriorityValue(itemId) {
                var input = document.getElementById('priority_' + itemId);
                var updatedPriority = input.value;
                @this.call('updatePriority', itemId, updatedPriority);
            }
        </script>
        @endpush