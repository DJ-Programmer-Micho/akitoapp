{{-- file path: resources/views/super-admins/pages/tickers/ticker-form.blade.php --}}
<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="updateTickerModal" tabindex="-1" aria-labelledby="updateTickerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateTicker">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateTickerModalLabel">{{__('Edit Ticker')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="tickersEdit.{{ $locale }}" class=" @if($locale != 'en') ar-shift @endif">{{__('In ' . $locale . ' Language')}}</label>
                                            <input type="text" 
                                                class="form-control @if($locale != 'en') ar-shift @endif
                                                @error('tickersEdit.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('tickersEdit.' . $locale) && !empty($tickersEdit[$locale])) is-valid @endif"
                                                wire:model="tickersEdit.{{ $locale }}" placeholder="Ticker Text">
                                            @error('tickersEdit.' . $locale)
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    <div class="mb-3">
                                        <label for="urlEdit">{{__('Link / Destination')}}</label>
                                        <input type="text" 
                                            class="form-control 
                                            @error('urlEdit') is-invalid @enderror
                                            @if(!$errors->has('urlEdit') && !empty($urlEdit)) is-valid @endif"
                                            wire:model="urlEdit" placeholder="https://italiancoffee-co.com/">
                                        @error('urlEdit')
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                    <span class="text-danger">{{ __($message) }}</span>
                                                </div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="priority">{{__('Priority')}}</label>
                                        <input type="text" 
                                            class="form-control 
                                            @error('priorityEdit') is-invalid @enderror
                                            @if(!$errors->has('priorityEdit') && !empty($priorityEdit)) is-valid @endif"
                                            wire:model="priorityEdit" placeholder="Priority">
                                        @error('priorityEdit')
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    
                                    <div class="mb-3">
                                        <label for="status">{{__('Status')}}</label>
                                        <select 
                                        class="form-control @error('statusEdit') is-invalid @enderror @if(!$errors->has('statusEdit') && $statusEdit !== null) is-valid @endif" 
                                        wire:model="statusEdit">
                                        <option value="">{{__('Select Status')}}</option>
                                        <option value="1" @if($statusEdit == 1) selected @endif>{{__('Active')}}</option>
                                        <option value="0" @if($statusEdit == 0) selected @endif>{{__('Non-Active')}}</option>
                                        </select>
                                        @error('statusEdit')
                                        <span class="text-danger">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteTickerModal" tabindex="-1" aria-labelledby="deleteTickerModalLabel" aria-hidden="true">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTickerModalLabel">{{__('Delete')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroyTicker">
                    <div class="modal-body @if(app()->getLocale() != 'en') ar-shift @endif">
                        <p>{{ __('Are you sure you want to delete this Ticker?') }}</p>
                        <p>{{ __('Please enter the in below to confirm:')}}</p>
                        <p>{{$showTextTemp}}</p>
                        <input type="text" wire:model="tickerNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}
                        </button>
                        <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || tickerNameToDelete !== $showTextTemp">
                            {{ __('Yes! Delete') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>