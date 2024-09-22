<div class="page-content">
    <style>
        .form-control {
            border: 1px solid #333435;
        }
        .filepond--root {
        height: 200px;
        }
        .filepond--file-preview {
            height: 100%;
        }
        .filepond--image-preview {
            object-fit: cover;
        }
        .filepond--root .filepond--credits {
            display: none;
        }
    </style>
    <div class="dashboard">
        <div class="container">
            <form id="myForm" action="#" method="">
                <div class="row custom-row">
                    <div class="col-md-3 col-sm-12">
                        <label for="profile_picture">Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture" height="300">                    
                        <input type="hidden" id="profile_picture_data" name="profile_picture_data">
                        <!-- Error for profile picture -->
                        @error('profile_picture')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror                   
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <label for="first_name">First Name * 5</label>
                        <input type="text" class="form-control" wire:model="fName" @if(!$editable) disabled @endif required>                    
                        @error('fName')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <label for="last_name">Last Name *</label>
                        <input type="text" class="form-control" wire:model="lName" @if(!$editable) disabled @endif required>
                        @error('lName')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
            
                        <label for="email">Email address *</label>
                        <input type="email" class="form-control" wire:model="emailAddress" @if(!$editable) disabled @endif required>
                        @error('emailAddress')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            
                <!-- Address Details -->
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <label for="country_selector">Country *</label>
                        <input id="country_selector" name="country" class="form-control" required>
                        @error('country')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <label for="city">City *</label>
                        <input type="text" id="city" wire:model="city" class="form-control" @if(!$editable) disabled @endif required>
                        @error('city')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <label for="address">Address *</label>
                        <input type="text" id="address" wire:model="address" class="form-control" @if(!$editable) disabled @endif required>
                        @error('address')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            
                <!-- Contact Details -->
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label for="phone">Phone Number *</label>
                        <input type="text" id="phone" wire:model="phone" class="form-control" @if(!$editable) disabled @endif required>
                        @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <label for="zipcode">Zip Code *</label>
                        <input type="text" id="zipcode" wire:model="zipcode" class="form-control" @if(!$editable) disabled @endif required>
                        @error('zipcode')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- Password Fields -->
                <!-- Submit Button -->
                <button type="submit" id="submitButton" class="btn btn-outline-primary-2">
                    <span>Register</span>
                    <i class="icon-long-arrow-right"></i>
                </button>
            </form>
            
        </div><!-- End .container -->
    </div><!-- End .dashboard -->
</div><!-- End .page-content -->


@push('register')
<link rel="stylesheet" href="{{ asset('main/assets/lib/country_select/countrySelect.min.css') }}">
<link rel="stylesheet" href="{{ asset('main/assets/lib/teleSelect/intlTelInput.css') }}">
<script src="{{ asset('main/assets/lib/teleSelect/intlTelInput.min.js') }}"></script>
<script src="{{ asset('main/assets/lib/teleSelect/utils.js') }}"></script>
<script src="{{ asset('main/assets/lib/country_select/countrySelect.min.js') }}"></script>
<link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.min.css" rel="stylesheet">

<!-- Include FilePond JS -->
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.min.js"></script>

<script>

FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageCrop,
        FilePondPluginImageTransform,
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType,
        FilePondPluginImageExifOrientation,
        FilePondPluginImageEdit
    );

    document.addEventListener('DOMContentLoaded', function() {
        const profilePictureInput = document.querySelector('#profile_picture');
        const profilePictureDataInput = document.querySelector('#profile_picture_data');

        if (profilePictureInput) {
            FilePond.create(profilePictureInput, {
                imagePreviewHeight: 200, // Height of the image preview
                allowImagePreview: true,
                imageCropAspectRatio: '1:1', // Ensure square cropping
                imageResizeTargetWidth: 200, // Adjust width as needed
                imageResizeTargetHeight: 200, // Adjust height as needed
                imageCrop: true, // Enable cropping
                imageTransformOutput: {
                    type: 'image/jpeg', // Output format
                },
                // server: {
                //     url: '/avatarupload',
                //     process: {
                //         url: '/avatarupload',
                //         method: 'POST',
                //         headers: {
                //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                //         }
                //     }
                // },
                onprocessfile: (error, file) => {
                    if (error) {
                        console.error('File processing error:', error);
                        return;
                    }
                    console.log('File processed successfully:', file);
                    
                const reader = new FileReader();
                reader.onloadend = function () {
                    const base64data = reader.result;
                    profilePictureDataInput.value = base64data; // Append base64 data to hidden input
                    console.log('img-data',base64data)
                };
                reader.readAsDataURL(file.file); // Convert file to base64
                }
            });
        } else {
            console.error('Profile picture input element not found.');
        }
    });
</script>
 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var countrySelector = $("#country_selector");
        
        // Fetch and populate country codes
        $.getJSON("{{ asset('main/assets/lib/country_name/restcountries.json') }}", function(data) {
            var countryOptions = '';
            var countryCode = "{{ $country }}"; // Use the country name from Livewire
            var iso2Code = '';
    
            data.forEach(function(country) {
                var code = country.cca2.toLowerCase();
                var name = country.name.common;
                var dialCode = country.idd.root;
    
                // Check if suffixes exist and append the first suffix if available
                if (country.idd.suffixes && country.idd.suffixes.length > 0) {
                    dialCode += country.idd.suffixes[0];
                }
    
                countryOptions += `<option value="${name}" data-dialcode="${dialCode}">${name} (+${dialCode})</option>`;
    
                // Check for the matching country to get ISO2 code
                if (name === countryCode) {
                    iso2Code = code; // Get the ISO2 code
                }
            });
    
            // Populate the select element
            countrySelector.append(countryOptions);
    
            // Set the default country by name
            countrySelector.val(countryCode).change(); // Set the default country
    
            // Initialize country select with the correct ISO2 code
            countrySelector.countrySelect({
                defaultCountry: iso2Code,
                preferredCountries: ['iq', 'sa', 'ae']
            });
        });
    });
    </script>
    
@endpush
