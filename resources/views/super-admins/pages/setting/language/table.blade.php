<div class="page-content">

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
        <div>
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif
        
            <table class="table">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>EN</th>
                        <th>AR</th>
                        <th>KU</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($translations as $key => $translation)
                        <tr>
                            <td>{{ $key }}</td>
                            <td><input type="text" wire:model.defer="translations.{{ $key }}.en" class="form-control" /></td>
                            <td><input type="text" wire:model.defer="translations.{{ $key }}.ar" class="form-control" /></td>
                            <td><input type="text" wire:model.defer="translations.{{ $key }}.ku" class="form-control" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        
            <button wire:click="saveTranslations" class="btn btn-primary">Save Translations</button>
        </div>
        
        
    </div>
</div>
