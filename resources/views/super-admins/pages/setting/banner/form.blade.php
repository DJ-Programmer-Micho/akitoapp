<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ __('Banner') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Banner') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div>
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Banner Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="images">{{ __('Images') }}</label>
                            <button type="button" class="btn btn-secondary mb-3" wire:click="addImage">Add Image</button>

                            @foreach($images as $index => $image)
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <input type="file" class="form-control" accept="image/*" data-index="{{ $index }}" onchange="convertToBase64(event, {{ $index }})">
                                        <div wire:ignore.self class="img-container position-relative mt-2">
                                            @if ($base64Images[$index])
                                                <img src="{{ $base64Images[$index] }}" alt="Base64 Image" class="img-thumbnail w-100">
                                            @elseif($image['image'])
                                                <img src="{{  app('cloudfront') . $image['image'] }}" alt="Existing Image" class="img-thumbnail w-100">
                                            @endif
                                            <button wire:click="removeImage('{{ $image['image'] }}', false)" type="button" class="btn btn-danger position-absolute" style="top: 5px; right: 5px;">X</button>
                                        </div>
                                        @error('images.' . $index . '.image') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <select wire:model="images.{{ $index }}.category_id" class="form-control">
                                            <option value="">{{ __('Select Category') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->categoryTranslation->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('images.' . $index . '.category_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger" wire:click="removeImage({{ $index }}, true)">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                            <hr class="mt-4">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" wire:click="saveSettings">Save Settings</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function convertToBase64(event, index) {
        const file = event.target.files[0];
        const reader = new FileReader();
        reader.onloadend = function () {
            @this.emit('imageUploaded', index, reader.result);
        }
        reader.readAsDataURL(file);
    }
</script>
