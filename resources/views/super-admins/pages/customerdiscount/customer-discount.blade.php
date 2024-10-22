<div class="page-content">
    <div class="container-fluid">
        @include('super-admins.pages.customerdiscount.customer-form')
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Discount</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                            <li class="breadcrumb-item active">Discount</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card" id="customerList">
                    <div class="card-header border-bottom-dashed">

                        <div class="row g-4 align-items-center">
                            <div class="col-sm">
                                <div>
                                    <h5 class="card-title mb-0">Customer Discount List</h5>
                                </div>
                            </div>
                            <div class="col-sm-auto">
                                <div class="d-flex flex-wrap align-items-start gap-2">
                                    {{-- <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button> --}}
                                    <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#saveDiscount" wire:click="switchRole">
                                        <i class="ri-add-line align-bottom me-1"></i> Add Discount
                                    </button>
                                    {{-- <button type="button" class="btn btn-secondary"><i class="ri-file-download-line align-bottom me-1"></i> Import</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-bottom-dashed border-bottom">
                        <form>
                            <div class="row g-3">
                                <div class="col-xxl-6 col-sm-12">
                                    <div class="search-box">
                                        <input type="text" class="form-control search" placeholder="Search for customer, email, phone, status or something..."
                                               wire:model="searchTerm">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-xxl-2 col-sm-4">
                                    <div>
                                        <select class="form-control" data-choices data-choices-search-false wire:model="statusFilter">
                                            <option default>All</option>
                                            <option value="brands">Brand</option>
                                            <option value="subcategory">Category & Sub Category</option>
                                            <option value="product">Product</option>
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
                    <div class="card-body">
                        <div>
                            <div class="table-responsive table-card mb-1">
                                <table class="table align-middle" id="customerTable">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th>Customer</th>
                                            <th>Email</th>
                                            <th>Discount Type</th>
                                            <th>Item Name</th>
                                            <th>Discount Percentage</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @forelse($discountRules as $data)
                                        <tr>
                                            <td class="customer_name @empty($data->customer->customer_profile->first_name) text-danger @endif align-middle">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="{{ $data->customer->customer_profile && $data->customer->customer_profile->avatar ? app('cloudfront').$data->customer->customer_profile->avatar : $customerImg }}" 
                                                            alt="{{ $data->customer->customer_profile->first_name ?? 'Unknown Customer' }}" 
                                                            class="img-fluid rounded-circle" 
                                                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 3px solid white; object-position: center;">                             
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $data->customer->customer_profile->first_name ?? 'Unknown' }} {{ $data->customer->customer_profile->last_name ?? '' }}</h6>
                                                            <p class="mb-0"><span>@</span>{{ $data->customer->username ?? 'Customer' }}</p>
                                                        </div>
                                                    </div>
                                                    {{-- <div>
                                                        @if ( $data->phone_verify == 1 && $data->phone_verify == 1 && $data->company_verify == 1 && $data->status == 1)
                                                        <span class="text-success" data-toggle="tooltip" data-placement="top" title="Verified">
                                                            <i class="fa-solid fa-circle"></i>
                                                        </span>
                                                        @elseif($data->phone_verify == 1 && $data->phone_verify == 1)
                                                        <span class="text-warning" data-toggle="tooltip" data-placement="top" title="Not Verified By Company">
                                                            <i class="fa-regular fa-circle-dot"></i>
                                                        </span>
                                                        @else
                                                        <span class="text-danger" data-toggle="tooltip" data-placement="top" title="Not Verified">
                                                            <i class="fa-regular fa-circle"></i>
                                                        </span>
                                                        @endif
                                                    </div> --}}
                                                </div>
                                            </td>
                                            <td class="email">{{$data->customer->email}}</td>
                                            <td>
                                                @if($data->type == 'brand')
                                                <span class="badge bg-primary-subtle text-primary text-uppercase">
                                                    <i class="fa-regular fa-copyright"></i>
                                                    {{__('Brand')}}
                                                </span>
                                                @elseif($data->type == 'category')
                                                <span class="badge bg-warning-subtle text-warning text-uppercase">
                                                    <i class="fa-solid fa-table"></i>
                                                    {{__('Category')}}
                                                </span>
                                                @elseif($data->type == 'subcategory')
                                                <span class="badge bg-secondary-subtle text-secondary text-uppercase">
                                                    <i class="fa-solid fa-table-columns"></i>
                                                    {{__('Sub Category')}}
                                                </span>
                                                @elseif($data->type == 'product')
                                                <span class="badge bg-success-subtle text-success text-uppercase">
                                                    <i class="fa-brands fa-product-hunt"></i>
                                                    {{__('Product')}}
                                                </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->type == 'brand')
                                                    {{ $data->brand->brandtranslation->name ?? 'N/A' }} <!-- Access Brand Translation -->
                                                @elseif($data->type == 'category')
                                                    {{ $data->category->categoryTranslation->name ?? 'N/A' }} <!-- Category Name -->
                                                @elseif($data->type == 'subcategory')
                                                    {{ $data->subCategory->subCategoryTranslation->name ?? 'N/A' }} <!-- Subcategory Name -->
                                                @elseif($data->type == 'product')
                                                    {{ $data->product->productTranslation->first()->name ?? 'N/A' }} <!-- Product Name -->
                                                @else
                                                    ERROR <!-- Default if none match -->
                                                @endif
                                            </td>
                                            <td>{{ number_format($data->discount_percentage, 2) }}%</td>
                                            <td class="align-middle text-center">
                                                <span>
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-soft-secondary btn-sm dropdown"
                                                            type="button" data-bs-toggle="dropdown"
                                                            aria-expanded="false"><i class="ri-more-fill"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" style="">
                                                            <li>
                                                                <button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#updateDiscount" wire:click="editDiscount({{ $data->id }})">
                                                                    <i class="fa-regular fa-pen-to-square me-2"></i>{{__('Edit')}}
                                                                </button>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <button type="button" class="dropdown-item edit-list text-danger" data-bs-toggle="modal" data-bs-target="#deleteDiscountModal" wire:click="removeDiscount({{ $data->id }})">
                                                                    <i class="fa-regular fa-trash-can me-2"></i>{{__('Delete')}}
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <div class="noresult" style="display: none">
                                            <div class="text-center">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px">
                                                </lord-icon>
                                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                                <p class="text-muted mb-0">We've searched On Customers We did not find any Customers for you search.</p>
                                            </div>
                                        </div>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                {{ $discountRules->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->