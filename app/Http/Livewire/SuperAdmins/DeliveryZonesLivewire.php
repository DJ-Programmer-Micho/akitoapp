<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Zone;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryZonesLivewire extends Component
{
    use WithPagination;
    public $name;
    public $coordinates;
    public $delivery_man;
    public $digit_payment;
    public $cod_payment;

    public $activeCount = 0;
    public $nonActiveCount = 0;
    public $search = '';
    public $statusFilter = 'all';
    
    public $zone_selected_id_delete;
    public $zone_name_selected_delete;
    public $showTextTemp;
    public $confirmDelete;
    public $zoneNameToDelete;
    public function storeZone()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'coordinates' => 'required|array',
        ]);

        Zone::create([
            'name' => $this->name,
            'coordinates' => json_encode($this->coordinates),
            'delivery_man' => $this->delivery_man,
            'digit_payment' => $this->digit_payment ?? 0,
            'cod_payment' => $this->cod_payment ?? 0,
            'status' => '1',
        ]);

        $this->reset(['name', 'coordinates']);
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Zone Added successfully!')]);
        $this->dispatchBrowserEvent('reint', []);
    }
    public function filterBrands($status)
    {
        $this->statusFilter = $status;
    }

    // QUERY FUNCTION
    public function changeTab($status)
    {
        $this->statusFilter = $status;
        $this->page = 1;
        $this->emitSelf('refresh');
    }

    public $status; // Add status property
    public $selectedZoneId; // Add this property to store the selected zone ID

    public function updateZone()
    {
        if (!$this->selectedZoneId) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('No zone selected for update!')]);
            return;
        }
        $zone = Zone::findOrFail($this->selectedZoneId);
        $zone->coordinates = json_encode($this->coordinates); // Save updated coordinates
        $zone->status = 1; // Update status based on editing
        $zone->save();

        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Zone updated successfully!')]);
        $this->resetInput();
    }

    
    public function closeModal(){
        $this->dispatchBrowserEvent('close-modal');
    }
    public function resetInput(){
        $this->coordinates = null;
        $this->selectedZoneId = null;
    }

    public function updateStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $zone = Zone::find($id);
    
        if ($zone) {
            // Toggle the status (0 to 1 and 1 to 0)
            $zone->status = !$zone->status;
            $zone->save();

            $fillColor = $zone->status == 1 ? '#00FF00' : '#FF0000';
            $this->dispatchBrowserEvent('updatePolygonColor', [
                'zoneId' => $zone->id,
                'fillColor' => $fillColor,
            ]);
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function updateDigPayment(int $id)
    {
        // Find the brand by ID, if not found return an error
        $zone = Zone::find($id);
    
        if ($zone) {
            // Toggle the status (0 to 1 and 1 to 0)
            $zone->digit_payment = !$zone->digit_payment;
            $zone->save();

            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Digital Payment Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function updateCodPayment(int $id)
    {
        // Find the brand by ID, if not found return an error
        $zone = Zone::find($id);
    
        if ($zone) {
            // Toggle the status (0 to 1 and 1 to 0)
            $zone->cod_payment = !$zone->cod_payment;
            $zone->save();

            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Cash On Delivery Payment Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function removeZone (int $id) {
        $this->zone_selected_id_delete = Zone::find($id);
        $this->zone_name_selected_delete = $this->zone_selected_id_delete->name ?? "Delete";
        if ($this->zone_name_selected_delete) {
            $this->showTextTemp = $this->zone_name_selected_delete;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public function destroyZone () {
        if ($this->confirmDelete && $this->zoneNameToDelete === $this->showTextTemp) {
            Zone::find($this->zone_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('removePolygon', ['zoneId' => $this->zone_selected_id_delete->id]);
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Zone Deleted Successfully')]);
            $this->confirmDelete = false;
            $this->zone_selected_id_delete = null;
            $this->zone_name_selected_delete = null;
            $this->zoneNameToDelete = '';
            $this->showTextTemp = null;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Operaiton Faild')]);
        }
    }

    public function render()
    {
        $this->activeCount = Zone::where('status', 1)->count();
        $this->nonActiveCount = Zone::where('status', 0)->count();

        // Start with a query builder instance
        $query = Zone::query();

        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('status', 1);
        } elseif ($this->statusFilter === 'non-active') {
            $query->where('status', 0);
        }
        // Apply search filter if applicable
        if (!empty($this->search)) {
            $query->whereHas('brandtranslation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }

        // Fetch the data after applying filters
        $tableData = $query->get();

        return view('super-admins.pages.delivery.delivery-table', [
            'zones' => $tableData
        ]);
    }
}