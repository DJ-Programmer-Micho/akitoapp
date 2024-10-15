{{-- resources/views/super-admins/pages/brands/brand-form.blade.php --}}
<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="addTeamModal" tabindex="-1" aria-labelledby="addTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="addTeam">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="addTeamModalLabel">{{ __('Add Team') }}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <hr class="bg-white">
                        <div class="filter-choices-input">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="teamName">{{ __('Team Name') }}</label>
                                        <input type="text" 
                                               class="form-control @error('teamName') is-invalid @enderror @if(!$errors->has('teamName') && !empty($teamName)) is-valid @endif"
                                               wire:model="teamName" placeholder="{{ __('Team Name') }}">
                                        @error('teamName')
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
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{ __('Add Team') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade overflow-auto" id="updateTeamModal" tabindex="-1" aria-labelledby="updateTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateTeam">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateTeamModalLabel">{{ __('Edit Team Name') }}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <hr class="bg-white">
                        <div class="filter-choices-input">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="teamName">{{ __('Team Name') }}</label>
                                        <input type="text" 
                                               class="form-control @error('teamName') is-invalid @enderror @if(!$errors->has('teamName') && !empty($teamName)) is-valid @endif"
                                               wire:model="teamName" placeholder="{{ __('Team Name') }}">
                                        @error('teamName')
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
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{ __('Update Team Name') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>


