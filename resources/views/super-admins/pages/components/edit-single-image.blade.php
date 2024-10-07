<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.5/cropper.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.5/cropper.min.js"></script>
<style>
.profile-picture-update {
    position: relative;
    width: 100%;
    padding-top: 100%; /* Maintains a 1:1 aspect ratio */
    overflow: hidden;
    opacity: 0.75;
    border: 1px solid #ccc;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    box-shadow: 0 8px 6px -6px black;
}

.profile-picture-update img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures the image covers the entire area without stretching */
}

.profile-picture-update-viewer {
    position: relative;
    width: 100%;
    padding-top: 100%; /* Maintains a 1:1 aspect ratio */
    overflow: hidden;
    opacity: 0.75;
    border: 1px solid #ccc;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    box-shadow: 0 8px 6px -6px black;
}

.profile-picture-update-viewer img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures the image covers the entire area without stretching */
}

.file-uploader {
    opacity: 0; /* Invisible but still clickable */
    height: 100%;
    width: 100%;
    cursor: pointer;
    position: absolute;
    top: 0;
    left: 0;
}

.upload-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0; /* Initial icon state, invisible */
    transition: opacity 0.3s ease;
    color: #ccc;
    -webkit-text-stroke-width: 2px;
    -webkit-text-stroke-color: #bbb;
}

.profile-picture-update:hover .upload-icon {
    opacity: 1; /* Icon becomes visible on hover */
}

.image_area { position: relative; }
    img { display: block; max-width: 100%; }
    .previewUpdate { overflow: hidden; width: 160px;  height: 160px; margin: 10px; border: 1px solid red;}
    .previewUpdate-cover { overflow: hidden; width: 160px;  height: 160px; margin: 10px; border: 1px solid red;}
    .modal-lg{max-width: 1000px !important;}
    .overlay { position: absolute; bottom: 10px; left: 0; right: 0; background-color: rgba(255, 255, 255, 0.5); overflow: hidden; height: 0; transition: .5s ease; width: 100%;}
    .image_area:hover .overlay { height: 50%; cursor: pointer; }
    .text { color: #333; font-size: 20px; position: absolute; top: 50%; left: 50%; -webkit-transform: translate(-50%, -50%); -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); text-align: center;}
    .galleryFoodTab:focus { border: #fff; }
    .galleryFoodTab:hover { transform: scale(1.2); border: #fff;}
    .galleryCoverTab:focus { border: #fff; }
    .galleryCoverTab:hover { transform: scale(1.2); border: #fff;}
    .loader { position: relative; left: 44%; border: 6px solid #f3f3f3; border-top: 6px solid #cc0022; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite;}
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<div>
    <div class="d-flex justify-content-between">
        <label for="priority">{{__($title)}}</label>
        <button type="button" class="btn btn-secondary" wire:click="sadImage">{{__('Remove/Update Image')}}</button>
    </div>
    
    <div class="@if ($de==1) d-none @endif">
    <div wire:ignore.self class="profile-picture-update-viewer" >

            <h1 class="upload-icon">
                <i class="bx bx-plus fs-22" aria-hidden="true"></i>
            </h1>
            <img src="{{app('cloudfront').$objectReader}}" alt="" srcset="">
        </div>
    </div>

    
    <div class="@if ($de==0) d-none @endif">
    <div wire:ignore.self class="profile-picture-update">

            <h1 class="upload-icon">
                <i class="bx bx-plus fs-22" aria-hidden="true"></i>
            </h1>
            <input
            class="file-uploader"
            type="file"
            id="brandImgUpdate"
          accept="image/png, image/jpeg, image/jpg"
            />
        </div>
    </div>
    
    
    {{-- IMAGE CROP MODAL --}}
    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="modalUpdateLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg text-white" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Crop Image Before Upload')}}</h5>
                    <button type="button" class="close" data-dismiss="modalUpdate" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <div class="row">
                            <div class="col-md-8">
                                <img src="" id="sample_image_update" />
                            </div>
                            <div class="col-md-4">
                                <div class="previewUpdate"></div>
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
    {{-- ----------------------------------------- --}}
</div>

{{-- Add --}}
@push('brandScripts')

<script>
       document.addEventListener('livewire:load', function () {
    const modalUpdate = new bootstrap.Modal(document.getElementById('modalUpdate'));
    let cropperUpdate;
    const profilePicture = document.querySelector('.profile-picture-update');

    function initializeCropper() {
        $('#modalUpdate').on('shown.bs.modal', function () {
            const image = document.getElementById('sample_image_update');
            if (cropperUpdate) {
                cropperUpdate.destroy();
            }
            cropperUpdate = new Cropper(image, {
                aspectRatio: 1 / 1,
                viewMode: 1,
                preview: '.previewUpdate'
            });
        });
    }

    function handleFileInputChange() {
        $('#brandImgUpdate').change(function (event) {
            profilePicture.style.backgroundImage = `url('')`;
            const image = document.getElementById('sample_image_update');
            const files = event.target.files;
            const maxSize = 2 * 1024 * 1024; // 2MB limit

            if (files && files.length > 0) {
                const file = files[0];

                // Check if file size exceeds 2MB
                if (file.size > maxSize) {
                    alert('Image size exceeds 2MB. Please upload a smaller file.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    image.src = reader.result;
                    modalUpdate.show();
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function handleCropButtonClick() {
        $('.crop-btn').off('click').on('click', function () {
            if (!cropperUpdate) {
                return;
            }

            const canvasUpdate = cropperUpdate.getCroppedCanvas({
                width: 512,
                    height: 512,
                    // png
                    fillColor: 'rgba(255, 255, 255, 0)',
            });

            if (!canvasUpdate) {
                console.error("Cropped canvas not generated.");
                return;
            }

            canvasUpdate.toBlob(function (blob) {
                const url = URL.createObjectURL(blob);

                const reader = new FileReader();
                reader.onloadend = function () {
                    const base64data = reader.result;
                    modalUpdate.hide();

                    
                    profilePicture.style.backgroundImage = `url(${base64data})`;

                    // Emit Livewire event
                    livewire.emit('imgCrop', base64data);

                    // Cleanup
                    if (cropperUpdate) {
                        cropperUpdate.destroy();
                        document.getElementById('brandImgUpdate').value = null;
                    }
                };
                reader.readAsDataURL(blob);

                // Optional: Handle file input update
                // const fileInput = document.getElementById('croppedbrandImgUpdate');
                // const dataTransfer = new DataTransfer();
                // dataTransfer.items.add(new File([blob], 'cropped_image.jpg', { type: 'image/jpeg' }));
                // fileInput.files = dataTransfer.files;

                modalUpdate.hide();
                cleanupCropper();
            }, 'image/png');
        });
    }

    $('#modalUpdate').on('shown.bs.modal', function () {
        const image = document.getElementById('sample_image_update');
        if (image.complete) {
            initializeCropper();
        } else {
            image.onload = initializeCropper;
        }
    });

    // Initialize everything
    // initializeCropper();
    handleFileInputChange();
    handleCropButtonClick();

    function cleanupCropper() {
        if (cropperUpdate) {
            cropperUpdate.destroy();
            document.getElementById('brandImg').value = null;
        }
    }

    Livewire.on('resetEditData', () => {
        // Call your JavaScript function here
        const profilePicture = document.querySelector('.profile-picture-update');
        profilePicture.style.backgroundImage = ``;
    });
});

    </script>

@endpush