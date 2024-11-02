<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ __('Hero') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Hero') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="save">
            <div class="card mb-3">
            <div class="card-header">
                <h5>Upload Hero Image</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="languageSelect">Select Language</label>
                    <select id="languageSelect" wire:model="selectedLanguage" class="form-control">
                        @foreach ($glang as $lang)
                            <option value="{{ $lang }}">{{ strtoupper($lang) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="newHeroImage">Upload Hero Image</label>
                    <input type="file" accept="image/*" onchange="convertToBase64(event, '{{ $selectedLanguage }}')" class="form-control ">
                    <small class="bg-danger text-white px-2 rounded mb-3">{{__('All Images Should be the same Resolution, for example: ')}}<b>{{__('(1920px X 400px)')}}</b></small>

                </div>
            </div>
            </div>

            @foreach ($glang as $lang)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>{{ strtoupper($lang) }} Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse ($base64Images[$lang] ?? [] as $index => $base64data)
                                <div wire:key="base64-{{ $lang }}-{{ $index }}" class="col-4 mb-3">
                                    <div wire:ignore.self class="img-container position-relative">
                                        <img src="{{ $base64data }}" alt="{{ $lang }}" class="img-thumbnail w-100">
                                        <button wire:click="removeHeroImage('{{ $lang }}', {{ $index }}, false)" type="button" class="btn btn-danger position-absolute" style="top: 5px; right: 5px;">X</button>
                                    </div>
                                </div>
                            @empty
                                @if (empty($uploadedImages[$lang]))
                                    <div class="col-12">
                                        <p>No images available for {{ strtoupper($lang) }}.</p>
                                    </div>
                                @endif
                            @endforelse

                            @foreach ($uploadedImages[$lang] ?? [] as $index => $filename)
                                <div wire:key="uploaded-{{ $lang }}-{{ $index }}" class="col-4 mb-3">
                                    <div wire:ignore.self class="img-container position-relative">
                                        <img src="{{ app('cloudfront') . $filename }}" alt="{{ $lang }}" class="img-thumbnail w-100">
                                        <button wire:click="removeHeroImage('{{ $lang }}', {{ $index }}, true)" type="button" class="btn btn-danger position-absolute" style="top: 5px; right: 5px;">X</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <button type="submit" class="btn btn-success">Save Slider</button>
            @if (session()->has('message'))
                <div class="alert alert-success mt-3">{{ session('message') }}</div>
            @endif
        </form>
    </div>
</div>
<script>
    function convertToBase64(event, language) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                // Emit an event to Livewire to add the base64 image for the selected language
                window.livewire.emit('addHeroImage', language, reader.result);
            };
            reader.readAsDataURL(file); // Convert file to base64
        }
    }
</script>
