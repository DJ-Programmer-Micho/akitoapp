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
            object-fit: cover; /* Ensure the image covers the square area */
        }
        .filepond--root .filepond--credits {
            display: none;
        }
    </style>
    {{-- style="direction: rtl --}}
    <div class="dashboard">
        <div class="container">
            <form id="myForm" action="{{ route('customer.register', ['locale' => app()->getLocale()]) }}" method="POST">
                @csrf
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
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name', $customer->customer_profile->first_name ?? '') }}" required>
                        @error('first_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
            
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name', $customer->customer_profile->last_name ?? '') }}" required>
                        @error('last_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
            
                        <label for="email">Email address *</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}" required>
                        @error('email')
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
                        <input type="text" id="city" name="city" class="form-control" value="{{ old('city', $customer->customer_profile->city ?? '') }}" required>
                        @error('city')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <label for="address">Address *</label>
                        <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $customer->customer_profile->address ?? '') }}" required>
                        @error('address')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            
                <!-- Contact Details -->
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label for="phone_number">Phone Number *</label>
                        <input id="inputPhone" type="tel" id="phone_number" name="phone_number" class="form-control" value="{{ old('phone_number', $customer->customer_profile->phone_number ?? '') }}" required>
                        @error('phone_number')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <label for="zip_code">Zip Code *</label>
                        <input type="text" id="zip_code" name="zip_code" class="form-control" value="{{ old('zip_code', $customer->customer_profile->zip_code ?? '') }}" required>
                        @error('zip_code')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- Password Fields -->
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                        @error('password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        @error('password_confirmation')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            
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
    // Initialize intl-tel-input for phone number input
    var inputPhone = document.querySelector("#inputPhone");
    var iti = window.intlTelInput(inputPhone, {
      autoPlaceholder: "off",
      formatOnDisplay: true,
      nationalMode: true,
      placeholderNumberType: "MOBILE",
      preferredCountries: ['iq', 'sa', 'kw', 'ae', 'lb', 'eg'],
      separateDialCode: true,
      utilsScript: "{{ asset('main/assets/lib/teleSelect/utils.js') }}",
    });

    // Initialize country selector
    var countrySelector = $("#country_selector");
    countrySelector.countrySelect({
      preferredCountries: ['iq', 'sa', 'ae']
    });

    // Fetch and populate country codes
    $.getJSON("{{ asset('main/assets/lib/country_name/restcountries.json') }}", function(data) {
      var countryOptions = '';
      data.forEach(function(country) {
        var code = country.cca2.toLowerCase();
        var name = country.name.common;
        var dialCode = country.idd.root;

        // Check if suffixes exist and append the first suffix if available
        if (country.idd.suffixes && country.idd.suffixes.length > 0) {
          dialCode += country.idd.suffixes[0];
        }

        countryOptions += `<option value="${code}" data-dialcode="${dialCode}">${name} (+${dialCode})</option>`;
      });
    });

    // Form submission logic
    function submitForm() {
      var formattedPhoneInput = iti.getNumber();
      console.log('Formatted Phone Input:', formattedPhoneInput);
      // Update the phone number with the formatted value
      inputPhone.value = formattedPhoneInput;

      // Submit the form
      document.getElementById("myForm").submit();
    }

    document.getElementById("submitButton").addEventListener('click', function(event) {
      event.preventDefault();
      submitForm();
    });
  });
</script>
@endpush