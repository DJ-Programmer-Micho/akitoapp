<div class="product-details-tab custom-table-info">
    <ul class="nav nav-pills justify-content-center" role="tablist">
        @if ($productDetail->information->informationTranslation->description)
        <li class="nav-item">
            <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">{{__('Description')}}</a>
        </li>
        @endif
        @if ($productDetail->information->informationTranslation->addition)
        <li class="nav-item">
            <a class="nav-link" id="product-info-link" data-toggle="tab" href="#product-info-tab" role="tab" aria-controls="product-info-tab" aria-selected="false">{{__('Additional Information')}}</a>
        </li>
        @endif
        @if ($productDetail->information->informationTranslation->shipping)
        <li class="nav-item">
            <a class="nav-link" id="product-shipping-link" data-toggle="tab" href="#product-shipping-tab" role="tab" aria-controls="product-shipping-tab" aria-selected="false">{{__('Shipping & Returns')}}</a>
        </li>
        @endif
        @if(!empty($productDetail->information->informationTranslation->question_and_answer))
        <li class="nav-item">
            <a class="nav-link" id="product-review-link" data-toggle="tab" href="#product-review-tab" role="tab" aria-controls="product-review-tab" aria-selected="false">{{__('FAQs')}}</a>
        </li>
        @endif
    </ul>
    <div class="tab-content nav-dir">
        @if ($productDetail->information->informationTranslation->description)
        <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
            <div class="product-desc-content">
                {!! $productDetail->information->informationTranslation->description !!}
            </div><!-- End .product-desc-content -->
        </div><!-- .End .tab-pane -->
        @endif
        @if ($productDetail->information->informationTranslation->addition)
        <div class="tab-pane fade" id="product-info-tab" role="tabpanel" aria-labelledby="product-info-link">
            <div class="product-desc-content">
                {!! $productDetail->information->informationTranslation->addition !!}
            </div><!-- End .product-desc-content -->
        </div><!-- .End .tab-pane -->
        @endif
        @if ($productDetail->information->informationTranslation->shipping)
        <div class="tab-pane fade" id="product-shipping-tab" role="tabpanel" aria-labelledby="product-shipping-link">
            <div class="product-desc-content">
                {!! $productDetail->information->informationTranslation->shipping !!}
            </div><!-- End .product-desc-content -->
        </div><!-- .End .tab-pane -->
        @endif
        @if ($productDetail->information->informationTranslation->question_and_answer)
        @php
            $faqData = json_decode($productDetail->information->informationTranslation->question_and_answer, true);
        @endphp
        @if ($faqData)
        <div class="tab-pane fade" id="product-review-tab" role="tabpanel" aria-labelledby="product-review-link">
            <div class="product-desc-content">
                <div class="accordion accordion-rounded" id="accordion-1">
                    @foreach($faqData as $index => $faq)
                    <div class="card card-box card-sm bg-light">
                        <div class="card-header" id="heading-{{ $index }}">
                            <h5 class="card-title">
                                <a role="button" data-toggle="collapse" href="#collapse-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $index }}">
                                    {{ $faq['question'] }}
                                </a>
                            </h5>
                        </div><!-- End .card-header -->
    
                        <div id="collapse-{{ $index }}" class="collapse @if($index === 0) show @endif" aria-labelledby="heading-{{ $index }}" data-parent="#accordion-1">
                            <div class="card-body">
                                {{ $faq['answer'] }}
                            </div><!-- End .card-body -->
                        </div><!-- End .collapse -->
                    </div><!-- End .card -->
                    @endforeach
                </div><!-- End .accordion -->
            </div><!-- End .product-desc-content -->
        </div><!-- End .tab-pane -->
        @endif
        @endif
    </div><!-- End .tab-content -->
</div><!-- End .product-details-tab -->