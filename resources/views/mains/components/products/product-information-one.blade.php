<div class="product-details-tab">
    <ul class="nav nav-pills justify-content-center" role="tablist">
        @if ($productDetail->information->informationTranslation->description)
        <li class="nav-item">
            <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">Description</a>
        </li>
        @endif
        @if ($productDetail->information->informationTranslation->addition)
        <li class="nav-item">
            <a class="nav-link" id="product-info-link" data-toggle="tab" href="#product-info-tab" role="tab" aria-controls="product-info-tab" aria-selected="false">Additional information</a>
        </li>
        @endif
        @if ($productDetail->information->informationTranslation->shipping)
        <li class="nav-item">
            <a class="nav-link" id="product-shipping-link" data-toggle="tab" href="#product-shipping-tab" role="tab" aria-controls="product-shipping-tab" aria-selected="false">Shipping & Returns</a>
        </li>
        @endif
        @if ($productDetail->information->informationTranslation->question_and_answer)
        <li class="nav-item">
            <a class="nav-link" id="product-review-link" data-toggle="tab" href="#product-review-tab" role="tab" aria-controls="product-review-tab" aria-selected="false">Reviews (2)</a>
        </li>
        @endif
    </ul>
    <div class="tab-content">
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
        <div class="tab-pane fade" id="product-review-tab" role="tabpanel" aria-labelledby="product-review-link">
            <div class="product-desc-content">
                {!! $productDetail->information->informationTranslation->question_and_answer !!}
            </div><!-- End .product-desc-content -->
        </div><!-- .End .tab-pane -->
        @endif
    </div><!-- End .tab-content -->
</div><!-- End .product-details-tab -->