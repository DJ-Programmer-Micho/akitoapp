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
                            <h5 class="card-title mb-0">Order Tasks</h5>
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
                <div class="card-body border border-dashed border-end-0 border-start-0 mb-3">
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
                        <div class="table-responsive table-card mb-1">
                            <table class="table table-nowrap align-middle" id="orderTable">
                                <thead class="text-muted table-light">
                                    <tr class="text-uppercase">
                                        {{-- <th scope="col" style="width: 25px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                            </div>
                                        </th> --}}
                                        <th data-sort="id">Order ID</th>
                                        <th data-sort="customer_name">Customer</th>
                                        <th data-sort="driver">Driver</th>
                                        <th data-sort="date">Order Date</th>
                                        <th data-sort="date">Location</th>
                                        <th data-sort="amount">Total Amount</th>
                                        <th data-sort="city">Action</th>
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
                                        @if ($order->driverUser)
                                        <td class="driver">
                                            <p class="mb-0">{{$order->driverUser->profile->first_name}} {{$order->driverUser->profile->last_name}}</p>
                                            <p class="mb-0"><span>@</span>{{$order->driverUser->username}}</p>
                                            
                                        </td>
                                        @else
                                        <td class="driver text-danger">{{__('Driver Not Selected!')}}</td>
                                        @endif
                                        {{-- <td class="product_name">Puma Tshirt</td> --}}
                                        <td class="date">{{$order->created_at}}</td>
                                        <td class="link">
                                            <a href="{{ 'https://www.google.com/maps?q=' . $order->latitude . ',' . $order->longitude }}" class="btn-link" target="_blank">
                                                <i class="fa-solid fa-location-dot me-2"></i> MAP
                                            </a>
                                        </td>
                                        <td class="amount">{{Number::currency($order->shipping_amount + $order->total_amount)}}</td>
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
                                {{ $orderTable->links() }}
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
                    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
                                    <h5 class="modal-title" id="exampleModalLabel">&nbsp;</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                                </div>
                                <form class="tablelist-form" autocomplete="off">
                                    <div class="modal-body">
                                        <input type="hidden" id="id-field" />

                                        <div class="mb-3" id="modal-id">
                                            <label for="orderId" class="form-label">ID</label>
                                            <input type="text" id="orderId" class="form-control" placeholder="ID" readonly />
                                        </div>

                                        <div class="mb-3">
                                            <label for="customername-field" class="form-label">Customer Name</label>
                                            <input type="text" id="customername-field" class="form-control" placeholder="Enter name" required />
                                        </div>

                                        <div class="mb-3">
                                            <label for="productname-field" class="form-label">Product</label>
                                            <select class="form-control" data-trigger name="productname-field" id="productname-field" required />
                                                <option value="">Product</option>
                                                <option value="Puma Tshirt">Puma Tshirt</option>
                                                <option value="Adidas Sneakers">Adidas Sneakers</option>
                                                <option value="350 ml Glass Grocery Container">350 ml Glass Grocery Container</option>
                                                <option value="American egale outfitters Shirt">American egale outfitters Shirt</option>
                                                <option value="Galaxy Watch4">Galaxy Watch4</option>
                                                <option value="Apple iPhone 12">Apple iPhone 12</option>
                                                <option value="Funky Prints T-shirt">Funky Prints T-shirt</option>
                                                <option value="USB Flash Drive Personalized with 3D Print">USB Flash Drive Personalized with 3D Print</option>
                                                <option value="Oxford Button-Down Shirt">Oxford Button-Down Shirt</option>
                                                <option value="Classic Short Sleeve Shirt">Classic Short Sleeve Shirt</option>
                                                <option value="Half Sleeve T-Shirts (Blue)">Half Sleeve T-Shirts (Blue)</option>
                                                <option value="Noise Evolve Smartwatch">Noise Evolve Smartwatch</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="date-field" class="form-label">Order Date</label>
                                            <input type="date" id="date-field" class="form-control" data-provider="flatpickr" required data-date-format="d M, Y" data-enable-time required placeholder="Select date" />
                                        </div>

                                        <div class="row gy-4 mb-3">
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="amount-field" class="form-label">Amount</label>
                                                    <input type="text" id="amount-field" class="form-control" placeholder="Total amount" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="payment-field" class="form-label">Payment Method</label>
                                                    <select class="form-control" data-trigger name="payment-method" required id="payment-field">
                                                        <option value="">Payment Method</option>
                                                        <option value="Mastercard">Mastercard</option>
                                                        <option value="Visa">Visa</option>
                                                        <option value="COD">COD</option>
                                                        <option value="Paypal">Paypal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="delivered-status" class="form-label">Delivery Status</label>
                                            <select class="form-control" data-trigger name="delivered-status" required id="delivered-status">
                                                <option value="">Delivery Status</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Inprogress">Inprogress</option>
                                                <option value="Cancelled">Cancelled</option>
                                                <option value="Pickups">Pickups</option>
                                                <option value="Delivered">Delivered</option>
                                                <option value="Returns">Returns</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success" id="add-btn">Add Order</button>
                                            <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4>You are about to delete a order ?</h4>
                                        <p class="text-muted fs-15 mb-4">Deleting your order will remove
                                            all of
                                            your information from our database.</p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button class="btn btn-link link-success fw-medium text-decoration-none" id="deleteRecord-close" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i>
                                                Close</button>
                                            <button class="btn btn-danger" id="delete-record">Yes,
                                                Delete It</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end modal -->
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
</div>