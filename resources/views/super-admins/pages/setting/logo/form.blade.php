<div class="page-content">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.5/cropper.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.5/cropper.min.js"></script>
    {{-- inline style for modal --}}
    <style>
        .image_area { position: relative; }
        img { display: block; max-width: 100%; }
        .preview { overflow: hidden; width: 260px;  height: 260px; margin: 10px; border: 1px solid red;}
        .modal-lg{max-width: 1000px !important;}
        .overlay { position: absolute; bottom: 10px; left: 0; right: 0; background-color: rgba(255, 255, 255, 0.5); overflow: hidden; height: 0; transition: .5s ease; width: 100%;}
        .image_area:hover .overlay { height: 50%; cursor: pointer; }
        .text { color: #333; font-size: 20px; position: absolute; top: 50%; left: 50%; -webkit-transform: translate(-50%, -50%); -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); text-align: center;}
        .switch input { display:none; }
        .switch { display:inline-block; width:60px; height:20px; margin:8px; position:relative; }
        .slider { position:absolute; top:0; bottom:0; left:0; right:0; border-radius:30px; box-shadow:0 0 0 2px #cc0022, 0 0 4px #cc0022; cursor:pointer; border:4px solid transparent; overflow:hidden; transition:.4s; }
        .slider:before { position:absolute; content:""; width:100%; height:100%; background:#cc0022; border-radius:30px; transform:translateX(-30px); transition:.4s; }
        input:checked + .slider:before { transform:translateX(30px); background:limeGreen; }
        input:checked + .slider { box-shadow:0 0 0 2px limeGreen,0 0 2px limeGreen; }
    </style>
    
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Logo/Icon')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Logo/Icon')}}</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h4 class="text-white">{{__('Company Logo')}}</h4>
                        <div class="row">
                            <div class="col-12">
                                <small class="bg-danger text-white px-2 rounded">{{__('The Image Size Should be from')}}<b>{{__('(600px X 180px)')}}</b> {{__('to')}} <b>{{__('(1180px X 360px)')}}</b></small>
                                <label for="img">{{__('Upload Image')}}</label>
                                <input type="file" name="logoImg" id="logoImg" class="form-control" style="height: auto">
                                @error('objectNameLogo') <span class="text-danger">{{ $message }}</span> @enderror
                                <input type="file" name="croppedLogoImg" id="croppedLogoImg" style="display: none;">
                            </div>
                            <div class="col-12">
                                <div class="mb-3 d-flex justify-content-center mt-1">
                                    <img id="showLogoImg" class="img-thumbnail rounded" src="{{ $tempImgLogo }}" style="width: 600px; height: 180px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-12">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h4 class="text-white">{{__('Company App Icon')}}</h4>
                        <div class="row">
                            <div class="col-12">
                                <small class="bg-danger text-white px-2 rounded">{{__('The Image Size Should be from')}}<b>{{__('(64px X 64px)')}}</b> {{__('to')}} <b>{{__('(1024px X 1024px)')}}</b></small>
                                <label for="img">{{__('Upload Image')}}</label>
                                <input type="file" name="IconImg" id="IconImg" class="form-control" style="height: auto">
                                @error('objectNameIcon') <span class="text-danger">{{ $message }}</span> @enderror
                                <input type="file" name="croppedIconImg" id="croppedIconImg" style="display: none;">
                            </div>
                            <div class="col-12">
                                <div class="mb-3 d-flex justify-content-center mt-1">
                                    <img id="showIconImg" class="img-thumbnail rounded" src="{{ $tempImgIcon }}" style="width: 180px; height: 180px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- IMAGE CROP MODAL --}}
    <div class="modal fade blockm" id="modal_logo_image" tabindex="-2" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-target="#modal_logo_image">
        <div class="modal-dialog modal-lg text-white" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Crop Image Before Upload')}}</h5>
                    <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <div class="row">
                            <div class="col-md-8">
                                <img src="" id="sample_image_logo_image" />
                            </div>
                            <div class="col-md-4">
                                <div class="preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" id="crop" class="btn btn-primary">Crop</button> --}}
                    <button type="button" class="btn btn-primary crop-btn" data-index="">{{__('Crop')}}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
                </div>
            </div>
        </div>
    </div> 

    <div class="modal fade blockm" id="modal_icon_image" tabindex="-2" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-target="#modal_icon_image">
        <div class="modal-dialog modal-lg text-white" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Crop Image Before Upload')}}</h5>
                    <button type="button" class="btn btn-danger close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <div class="row">
                            <div class="col-md-8">
                                <img src="" id="sample_image_icon_image" />
                            </div>
                            <div class="col-md-4">
                                <div class="preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" id="crop" class="btn btn-primary">Crop</button> --}}
                    <button type="button" class="btn btn-primary crop-btn" data-index="">{{__('Crop')}}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
                </div>
            </div>
        </div>
    </div> 
</div>

@push('scripts')
{{-- Add Logo OR Avatar--}}
<script>
    document.addEventListener('livewire:load', function () {
    // Logo Modal Crop
    setupCropModal({
        inputId: 'logoImg',
        modalId: 'modal_logo_image',
        imageId: 'sample_image_logo_image',
        cropBtnClass: '.crop-btn',
        livewireEvent: 'updateCroppedLogoImg',
        aspectRatio: 1603 / 667,
        cropWidth: 1603,
        cropHeight: 667,
        fileInputId: 'croppedLogoImg'
    });

    // Icon Modal Crop
    setupCropModal({
        inputId: 'IconImg',
        modalId: 'modal_icon_image',
        imageId: 'sample_image_icon_image',
        cropBtnClass: '.crop-btn',
        livewireEvent: 'updateCroppedIconImg',
        aspectRatio: 1 / 1,
        cropWidth: 1024,
        cropHeight: 1024,
        fileInputId: 'croppedIconImg'
    });
});

function setupCropModal({
    inputId, modalId, imageId, cropBtnClass, livewireEvent, aspectRatio, cropWidth, cropHeight, fileInputId
}) {
    const inputFile = document.getElementById(inputId);
    const modalElement = document.getElementById(modalId);
    const imageElement = document.getElementById(imageId);
    const modal = new bootstrap.Modal(modalElement);
    let cropper;

    inputFile.addEventListener('change', function (event) {
        const files = event.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function () {
                imageElement.src = reader.result;
                modal.show();
            };
            reader.readAsDataURL(files[0]);
        }
    });

    modalElement.addEventListener('shown.bs.modal', function () {
        if (cropper) cropper.destroy();
        cropper = new Cropper(imageElement, {
            aspectRatio: aspectRatio,
            viewMode: 1,
            preview: '.preview'
        });
    });

    modalElement.querySelector(cropBtnClass).addEventListener('click', function () {
        cropper.getCroppedCanvas({
            width: cropWidth,
            height: cropHeight
        }).toBlob(function (blob) {
            const reader = new FileReader();
            reader.onloadend = function () {
                const base64data = reader.result;
                modal.hide();
                Livewire.emit(livewireEvent, base64data); // Emit Livewire event with base64 data
                if (cropper) {
                    cropper.destroy();
                    inputFile.value = null;
                }
            };
            reader.readAsDataURL(blob);

            const file = new File([blob], 'cropped_image.jpg', { type: 'image/jpeg' });
            const fileInput = document.getElementById(fileInputId);
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
        }, 'image/png');
    });

    modalElement.querySelector('[data-dismiss="modal"]').addEventListener('click', function () {
        modal.hide();
        inputFile.value = null;
    });
}

</script>
@endpush