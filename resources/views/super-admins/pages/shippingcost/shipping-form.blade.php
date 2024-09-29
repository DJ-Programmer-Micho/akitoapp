<div class="row">
    <div class="col-6">
        <div class="card p-3">
            <div>
                <h3>Instructions</h3>
                <p>For example, if the distance is 2.60 KM, the additional distance beyond the first KM is 1.60 KM. This will result in an additional cost of $1 for the first KM and $0.5 for the extra 0.60 KM.</p>
            </div>
            <hr class="my-3">
            <form wire:submit.prevent="shippingSubmit">
                <div class="form-group mb-3">
                    <label for="name">Minimum Delivery Charge / First KM Cost</label>
                    <input type="text" id="name" class="form-control" wire:model="first_km_cost" required>
                </div>
                <div class="form-group mb-3">
                    <label for="name">Delivery Charge per KM / Additional KM Cost</label>
                    <input type="text" id="name" class="form-control" wire:model="additional_km_cost" required>
                </div>
                <div class="form-group mb-3">
                    <label for="name">Free Delivery Over Specific Cost</label>
                    <input type="text" id="name" class="form-control" wire:model="free_delivery_over" required>
                </div>
                <input type="hidden" id="coordinates" wire:model="coordinates">
                <button type="submit" class="btn btn-primary">Update Shipping Cost</button>
            </form>
            <!-- Button to update coordinates -->
        </div>
    </div>
</div>