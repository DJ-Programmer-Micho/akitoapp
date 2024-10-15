<div class="page-content">
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{__('Create Product')}}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Table Driver')}}</a></li>
                        <li class="breadcrumb-item active">{{__('Combime Driver Team')}}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="addDriverToTeam">
                <div class="modal-body">
                    <div class="modal-header mb-3">
                        <h5 class="modal-title" id="addTeamMemberModalLabel">{{ __('Add Driver Member') }}</h5>
                    </div>
                    <hr class="bg-white">
                    <div class="filter-choices-input">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="selectedTeam">{{ __('Select Team') }}</label>
                                    <select class="form-control @error('selectedTeam') is-invalid @enderror @if(!$errors->has('selectedTeam') && $selectedTeam !== null) is-valid @endif"
                                            wire:model="selectedTeam">
                                        <option value="">{{ __('Select Team') }}</option>
                                        @foreach($TeamList as $tList)
                                            <option value="{{ $tList->id }}">{{ $tList->team_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedTeam')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="selectedDelivery">{{ __('Delivery Name') }}</label>
                                    <select class="js-delivery-basic-multiple form-select" name="selectedDelivery[]" multiple="multiple" wire:model="selectedDelivery">
                                        @foreach($deliveryList as $dList)
                                            <option value="{{ $dList->id }}">{{ $dList->profile->first_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedDelivery')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>
                <div class="modal-footer">
                    <a href="{{route('super.driver.team', ['locale' => app()->getLocale()])}}" class="btn btn-secondary mx-2">{{ __('Back') }}</a>
                    <button type="submit" class="btn btn-success submitJs">{{ __('Submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@push('teamDelivery')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- Use the full jQuery version -->

<link rel="stylesheet" href="{{ asset('dashboard/css/select2.css') }}">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$('.js-delivery-basic-multiple').select2();
// Bind change event to update Livewire data
$('.js-delivery-basic-multiple').on('change', function (e) {
    var selectedDelivery = $(this).val(); // Get selected values
    @this.set('selectedDelivery', selectedDelivery); // Update Livewire property
});

// Reinitialize Select2 after each Livewire update
Livewire.hook('message.processed', () => {
    $('.js-delivery-basic-multiple').select2();
});
</script>
@endpush