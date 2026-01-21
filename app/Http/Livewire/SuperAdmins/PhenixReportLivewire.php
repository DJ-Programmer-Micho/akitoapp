<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Models\PhenixSyncLog;
use App\Models\PhenixSystem;

class PhenixReportLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $phenixSystems = [];
    public string $phenix_system_id = 'all';


    public $search = '';
    public $perPage = 20;

    public $dateFrom = null; // YYYY-MM-DD
    public $dateTo   = null; // YYYY-MM-DD

    // download modal helpers
    public ?string $downloadUrl = null;
    public ?string $downloadPath = null;

    protected $queryString = [
        'phenix_system_id' => ['except' => ''],
        'search' => ['except' => ''],
        'perPage' => ['except' => 20],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->phenixSystems = PhenixSystem::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPhenixSystemId() { $this->resetPage(); }
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->phenix_system_id = null;
        $this->search = '';
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->perPage = 20;
        $this->resetPage();
    }

    public function downloadXlsx(int $logId): void
    {
        $log = PhenixSyncLog::query()->findOrFail($logId);

        if (!$log->xlsx_path) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => __('No XLSX available for this record.'),
            ]);
            return;
        }

        $this->downloadPath = $log->xlsx_path;

        // S3 temp URL if supported
        try {
            $this->downloadUrl = Storage::disk('s3')->temporaryUrl($log->xlsx_path, now()->addMinutes(60));
        } catch (\Throwable $e) {
            $this->downloadUrl = Storage::disk('s3')->url($log->xlsx_path);
        }

        $this->dispatchBrowserEvent('open-download-modal');
    }

    public function render()
    {
        $logs = PhenixSyncLog::query()
            ->with(['system:id,name,code'])
            ->when($this->phenix_system_id, fn($q) => $q->where('phenix_system_id', $this->phenix_system_id))
            ->when($this->search, function ($q) {
                $s = trim($this->search);
                $q->where(function ($qq) use ($s) {
                    $qq->where('system_code', 'like', "%{$s}%")
                       ->orWhere('xlsx_path', 'like', "%{$s}%");
                });
            })
            ->when($this->dateFrom, fn($q) => $q->whereDate('synced_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('synced_at', '<=', $this->dateTo))
            ->orderByDesc('synced_at')
            ->paginate($this->perPage);

        return view('super-admins.pages.phenixreport.phenix-report', [
            'logs' => $logs,
        ]);
    }
}
