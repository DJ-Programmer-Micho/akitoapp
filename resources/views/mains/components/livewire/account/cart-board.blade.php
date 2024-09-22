<div>
    <style>
        .table td {
            padding: 0
        }
    </style>
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead>
            <tr>
                <th>{{__('Product')}}</th>
                <th class="text-center">{{__('QTY')}}</th>
                <th class="text-center">{{__('Unit Price')}}</th>
                <th class="text-center">{{__('Total Price')}}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cartListItems as $data)
                <tr>
                    <td class="@empty($data['product']['product_translation'][0]['name']) text-danger @endif align-middle">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <img src="{{ app('cloudfront').$data['product']['variation']['images'][0]['image_path'] }}" alt="{{ $data['product']['product_translation'][0]['name'] }}" class="img-fluid" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                            </div>
                            <div>

                                <h6 class="mx-1 mb-0">{{$data['product']['product_translation'][0]['name'] ?? 'unKnown' }}</h6>
                                <p class="mx-1 mb-0">{{__('Category:')}} {{ $data['product']['categories'][0]['category_translation']['name'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle text-center">
                        <div class="quantity-action d-flex align-items-center justify-content-center">
                            @if($data['quantity'] > 1)
                                <button wire:click="updateQuantity({{ $data['id'] }}, {{ $data['quantity'] - 1 }})" class="btn btn-outline-secondary btn-sm" style="min-width: 35px; padding: 5px; border-radius: 40px 0 0 40px">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                            @else
                                <button wire:click="removeFromCart({{ $data['id'] }})" class="btn btn-outline-danger btn-sm" style="min-width: 35px; padding: 5px; border-radius: 40px 0 0 40px">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            @endif
                            <span class="mx-2">{{ $data['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $data['id'] }}, {{ $data['quantity'] + 1 }})" class="btn btn-outline-secondary btn-sm" style="min-width: 35px; padding: 5px; border-radius: 0 40px 40px 0">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    
                    <td class="align-middle text-center">
                        <div class="d-flex justify-content-center align-items-center">
                            ${{ !empty($data['product']['variation']['on_sale']) ?  $data['product']['variation']['discount'] : $data['product']['variation']['price'] }}
                        </div>
                    </td>
                    <td class="align-middle text-center">
                        <div class="d-flex justify-content-center align-items-center">
                            ${{ !empty($data['product']['variation']['on_sale']) ?  $data['quantity'] * $data['product']['variation']['discount'] : $data['quantity'] * $data['product']['variation']['price'] }}
                        </div>
                    </td>
                </tr>
            @empty
            <div class="tab-pane">
                <div class="py-4 text-center">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                    </lord-icon>
                    <h5 class="mt-4">Sorry! No Result Found</h5>
                </div>
            </div>
            @endforelse
        </tbody>
    </table>
</div>