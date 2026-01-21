<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\Models\PhenixSystem;
use Livewire\WithPagination;
use App\Models\PhenixSyncLog;
use App\Models\VariationSize;
use App\Models\VariationColor;

use Illuminate\Support\Carbon;
use App\Models\ProductVariation;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdjustProductLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $brandIds = [];
    public $categoryIds = [];
    public $subCategoryIds = [];
    public $sizeIds = [];
    public $colorIds = [];
    public $capacityIds = [];
    public $materialIds = [];
    public $minPrice = 0;
    public $maxPrice = 1000000000000;
    public $sortBy = 'priority';
    public $items = 15;
    public $activeCount;
    public $nonActiveCount;

    // Pre-INT
    public $brands;
    public $categoriesData;
    public $tags;
    public $colors;
    public $sizes;
    public $materials;
    public $capacities;
    public $selectedColors = [];

    // Filter and Search
    public $search = '';
    public $statusFilter = 'all';
    public $page = 1;
    public $glang;

    public array $syncChanges = [];
    public ?string $syncDownloadUrl = null;
    public ?string $syncLogPath = null;
    public bool $showSyncModal = false;
    public ?int $phenix_system_id = null;
    public $phenixSystems = [];

    public function mount()
    {
        $this->glang = app()->getLocale();
        $this->loadInitialData();

        $this->phenixSystems = PhenixSystem::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name', 'code']);

        $this->phenix_system_id = $this->phenixSystems->first()->id ?? null;
    }

    public function loadInitialData()
    {
        // Load all brands
        $this->brands = Brand::with(['brandtranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        // Load all categories and subcategories
        $this->categoriesData = Category::with([
            'subCategory' => function ($query) {
                $query->with(['subCategoryTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                }])->orderBy('priority');
            },
            'categoryTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }
        ])->orderBy('priority')->get();

        // Load all tags
        $this->tags = Tag::with(['tagtranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        // Load all colors, sizes, materials, capacities
        $this->colors = VariationColor::with(['variationColorTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        $this->materials = VariationMaterial::with(['variationMaterialTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        $this->sizes = VariationSize::with(['variationSizeTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        $this->capacities = VariationCapacity::with(['variationCapacityTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();
    }

    public function updateMaterial(int $p_id, $updatedMaterial): void
    {
        // Validate numeric
        if (!is_numeric($updatedMaterial)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Invalid material ID value'),
            ]);
            return;
        }

        $materialId = (int) $updatedMaterial;

        // Optional: allow clearing material_id (empty => null)
        // if ($updatedMaterial === '' || $updatedMaterial === null) { $materialId = null; }

        $product = Product::with('variation')->find($p_id);

        if (!$product) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found'),
            ]);
            return;
        }

        if (!$product->variation) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('This product has no variation.'),
            ]);
            return;
        }

        // Update on product_variations table
        $product->variation->update([
            'material_id' => $materialId,
        ]);

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Material ID Updated Successfully'),
        ]);
    }

    // CRUD HANDLER
    public function updateStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $brandStatus = Product::find($id);
    
        if ($brandStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $brandStatus->status = !$brandStatus->status;
            $brandStatus->save();
    
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

    public function updatePriority(int $p_id, $updatedPriority)
    {
        // Validate if updatedPriority is a number
        if (!is_numeric($updatedPriority)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Invalid priority value')
            ]);
            return;
        }
    
        // Find the brand by ID
        $brand = Product::find($p_id);
        
        if ($brand) {
            $brand->priority = $updatedPriority;
            $brand->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function updatePrice(int $p_id, $updatedprice)
    {

        // Validate if updatedPriority is a number
        if (!is_numeric($updatedprice)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Invalid priority value')
            ]);
            return;
        }
    
        // Find the brand by ID
        $product = Product::with('variation')->find($p_id);
        if ($product) {
            $product->variation->price = $updatedprice;
            $product->variation->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Price Has Been Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function updateDiscount(int $p_id, $updatedDiscount)
    {

        // Validate if updatedPriority is a number
        if (!is_numeric($updatedDiscount)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Invalid priority value')
            ]);
            return;
        }
    
        // Find the brand by ID
        $product = Product::with('variation')->find($p_id);
        if ($product) {
            $product->variation->discount = $updatedDiscount;
            $product->variation->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Discount Has Been Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Record Not Found')
            ]);
        }
    }
    
    public function updateStock(int $p_id, $updatedStock, $updateOrderLimit)
    {

        // Validate if updatedPriority is a number
        if (!is_numeric($updatedStock)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Invalid priority value')
            ]);
            return;
        }

        // Find the brand by ID
        $product = Product::with('variation')->find($p_id);
        if ($product) {
            $product->variation->stock = $updatedStock;
            
            if($updatedStock < $updateOrderLimit){
                $product->variation->order_limit = $updatedStock;
            }
            
            $product->variation->save();
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Stock Has Been Updated Successfully')
            ]);

        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function updateOrderLimitValue(int $p_id, $updateOrderLimit, $updateStock)
    {

        // Validate if updatedPriority is a number
        if (!is_numeric($updateOrderLimit)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Invalid Order Limit value')
            ]);
            return;
        }

        if ($updateOrderLimit > $updateStock) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Order Limit Should Not Be more Than in stock')
            ]);
            return;
        }
    
        // Find the brand by ID
        $product = Product::with('variation')->find($p_id);
        if ($product) {
            $product->variation->order_limit = $updateOrderLimit;
            $product->variation->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Stock Has Been Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function toggleColor($colorId)
    {
        if (in_array($colorId, $this->selectedColors)) {
            // Remove the color if it's already selected
            $this->selectedColors = array_diff($this->selectedColors, [$colorId]);
        } else {
            // Add the color if it's not selected
            $this->selectedColors[] = $colorId;
        }
    
        // Trigger reactivity by calling render
        $this->filterProducts();
    }
    
    public function filterProducts()
    {
        // Re-render the component to apply filters
        $this->render();
    }
    
    public function statusFilter($status)
    {
        $this->statusFilter = $status;
    }

    public function changeTab($status)
    {
        $this->statusFilter = $status;
        $this->page = 1;
    }
    
    public function render()
    {
        // Calculate active and non-active counts
        $this->activeCount = Product::where('status', 1)->count();
        $this->nonActiveCount = Product::where('status', 0)->count();
        
        // Build the product query
        $productsQuery = Product::query()
        ->distinct()
        ->join('product_translations', 'products.id', '=', 'product_translations.product_id')
        ->select('products.*', 'product_translations.name as product_name')
        ->where('product_translations.locale', $this->glang)
    
        // Apply search filter
        ->when($this->search, function ($query) {
            $query->where('product_translations.name', 'like', '%' . $this->search . '%');
        })
    
            // Apply status filter
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('status', 1);
            })
            ->when($this->statusFilter === 'non-active', function ($query) {
                $query->where('status', 0);
            })
            // Apply brand filter
            ->when(!empty($this->brandIds), function ($query) {
                $query->whereIn('products.brand_id', $this->brandIds);
            })
    
            // Apply category filter
            ->when(!empty($this->categoryIds), function ($query) {
                $query->whereHas('categories', function ($query) {
                    $query->whereIn('categories.id', $this->categoryIds);
                });
            })
    
            // Apply sub-category filter
            ->when(!empty($this->subCategoryIds), function ($query) {
                $query->whereHas('subCategories', function ($query) {
                    $query->whereIn('sub_categories.id', $this->subCategoryIds);
                });
            })
    
            // Apply color filter
            ->when(!empty($this->selectedColors), function ($query) {
                $query->whereHas('variation.colors', function ($query) {
                    $query->whereIn('variation_colors.id', $this->selectedColors);
                });
            })
    
            // Apply size filter
            ->when(!empty($this->sizeIds), function ($query) {
                $query->whereHas('variation.sizes', function ($query) {
                    $query->whereIn('variation_sizes.id', $this->sizeIds);
                });
            })
    
            // Apply material filter
            ->when(!empty($this->materialIds), function ($query) {
                $query->whereHas('variation.materials', function ($query) {
                    $query->whereIn('variation_materials.id', $this->materialIds);
                });
            })
    
            // Apply capacity filter
            ->when(!empty($this->capacityIds), function ($query) {
                $query->whereHas('variation.capacities', function ($query) {
                    $query->whereIn('variation_capacities.id', $this->capacityIds);
                });
            })
    
            // Apply price filters
            ->when($this->minPrice, function ($query) {
                $query->whereHas('variation', function ($query) {
                    $query->where('product_variations.price', '>=', $this->minPrice);
                });
            })
            ->when($this->maxPrice, function ($query) {
                $query->whereHas('variation', function ($query) {
                    $query->where('product_variations.price', '<=', $this->maxPrice);
                });
            })
    
            // Apply sorting and pagination
            ->orderBy($this->sortBy)
            ->paginate($this->items)->withQueryString();
        
        // Return view with data
        return view('super-admins.pages.adjustproducts.product-table', [
            'tableData' => $productsQuery,
            'phenixSystems' => $this->phenixSystems,
        ]);
    }
    
    public function syncPhenixPrices(): void
    {
        @set_time_limit(0);

        $this->syncChanges = [];
        $this->syncDownloadUrl = null;
        $this->syncLogPath = null;
        $this->showSyncModal = false;

        $log = null; // ✅ will hold PhenixSyncLog row

        try {
            if (!$this->phenix_system_id) {
                throw new \RuntimeException('Please select a Phenix system first.');
            }

            /** @var PhenixSystem $system */
            $system = PhenixSystem::query()
                ->where('id', $this->phenix_system_id)
                ->where('is_active', true)
                ->firstOrFail();

            $baseUrl  = rtrim((string) $system->base_url, '/');
            $username = (string) $system->username; // decrypted by casts
            $password = (string) $system->password;
            $token    = (string) $system->token;

            if (!$baseUrl || !$username || !$password || !$token) {
                throw new \RuntimeException('Selected Phenix system is missing base_url/username/password/token.');
            }

            $timeout = (int) ($system->timeout_seconds ?? 10);
            $retries = (int) ($system->retry_times ?? 2);

            // ✅ Create "running" log row FIRST
            $log = PhenixSyncLog::create([
                'phenix_system_id' => $system->id,
                'system_code'      => $system->code,
                'matched'          => 0,
                'updated'          => 0,
                'changes'          => 0,
                'xlsx_path'        => null,
                'meta'             => [
                    'base_url' => $baseUrl,
                    'timeout'  => $timeout,
                    'retries'  => $retries,
                    'status'   => 'running',
                ],
                'synced_at'        => null,
            ]);

            $this->dispatchBrowserEvent('alert', [
                'type' => 'info',
                'message' => "SYNC started ({$system->name})...",
            ]);

            // 1) Fetch items from Phenix
            $resp = Http::baseUrl($baseUrl)
                ->withBasicAuth($username, $password)
                ->withHeaders(['phenixtoken' => $token])
                ->timeout($timeout)
                ->retry($retries, 200)
                ->get('/api/rest/TPhenixApi/ItemsGetAllList')
                ->throw()
                ->json();

            $items = data_get($resp, 'result.0.items', []);
            if (empty($items)) {
                throw new \RuntimeException('No items received from Phenix.');
            }

            // 2) Build maps using Ut_Equal==1:
            // material_id (Ut_Mat_ID) => Ut_Sell_Price
            // material_id (Ut_Mat_ID) => Ut_Id
            $priceMap = [];
            $unitMap  = [];

            foreach ($items as $item) {
                foreach (($item['Funits'] ?? []) as $u) {
                    if (($u['Ut_Equal'] ?? null) == 1) {

                        $matId = (int) ($u['Ut_Mat_ID'] ?? 0);
                        $price = $u['Ut_Sell_Price'] ?? null;
                        $utId  = (int) ($u['Ut_Id'] ?? 0);

                        if ($matId > 0) {
                            if (is_numeric($price)) {
                                $priceMap[$matId] = (int) $price;
                            }
                            if ($utId > 0) {
                                $unitMap[$matId] = $utId;
                            }
                        }

                        break; // stop at first Ut_Equal==1
                    }
                }
            }

            if (empty($priceMap)) {
                throw new \RuntimeException('No prices mapped from Phenix (Ut_Equal==1 not found).');
            }

            // 3) Update only matched variations (fast): chunk + bulk CASE update (price + unit_id)
            $updated = 0;
            $matched = 0;

            ProductVariation::query()
                ->select(['id', 'sku', 'material_id', 'unit_id', 'price'])
                ->with([
                    'product.productTranslation' => function ($q) {
                        $q->where('locale', 'en')->select('id','product_id','name','locale');
                    },
                    'images' => function ($q) {
                        $q->orderByRaw('is_primary DESC, priority ASC, id ASC')
                        ->select('id','variation_id','image_path','priority','is_primary');
                    }
                ])
                ->whereNotNull('material_id')
                ->where('material_id', '>', 0)
                ->whereIn('material_id', array_keys($priceMap))
                ->orderBy('id')
                ->chunkById(500, function ($rows) use (&$updated, &$matched, $priceMap, $unitMap) {

                    $matched += $rows->count();

                    $idsToUpdate = [];

                    $priceCases = [];
                    $unitCases  = [];

                    foreach ($rows as $v) {
                        $matId = (int) $v->material_id;

                        $newPrice = $priceMap[$matId] ?? null;
                        $newUnit  = $unitMap[$matId] ?? null;

                        $priceChanged = ($newPrice !== null && (int) $v->price !== (int) $newPrice);
                        $unitChanged  = ($newUnit !== null && (int) $v->unit_id !== (int) $newUnit);

                        // ✅ If neither changed, skip
                        if (!$priceChanged && !$unitChanged) {
                            continue;
                        }

                        $id = (int) $v->id;
                        $idsToUpdate[] = $id;

                        // For SQL CASE, if value missing keep current value
                        if ($newPrice !== null) {
                            $priceCases[] = "WHEN {$id} THEN {$newPrice}";
                        }
                        if ($newUnit !== null) {
                            $unitCases[] = "WHEN {$id} THEN {$newUnit}";
                        }

                        // Product + name + image for modal/xlsx
                        $product = $v->product->first();

                        $enName = optional(
                            optional($product)->productTranslation
                                ->where('locale', 'en')
                                ->first()
                        )->name ?? 'Unknown';

                        $imagePath = optional($v->images->first())->image_path;
                        $imageUrl  = $imagePath ? (app('cloudfront') . $imagePath) : null;

                        $this->syncChanges[] = [
                            'sku'         => $v->sku,
                            'material_id' => $matId,
                            'unit_id'     => $newUnit,                 // ✅ new
                            'en_name'     => $enName,
                            'image'       => $imageUrl,
                            'old_price'   => (int) $v->price,
                            'new_price'   => $newPrice ?? (int) $v->price,
                        ];
                    }

                    if (!empty($idsToUpdate)) {
                        $idsList = implode(',', $idsToUpdate);

                        // If no cases, keep field as-is
                        $priceSql = !empty($priceCases)
                            ? "price = CASE id " . implode(' ', $priceCases) . " ELSE price END"
                            : "price = price";

                        $unitSql = !empty($unitCases)
                            ? "unit_id = CASE id " . implode(' ', $unitCases) . " ELSE unit_id END"
                            : "unit_id = unit_id";

                        DB::statement("
                            UPDATE product_variations
                            SET {$priceSql}, {$unitSql}
                            WHERE id IN ({$idsList})
                        ");

                        $updated += count($idsToUpdate);
                    }
                });

            // ✅ If matched = 0, still store log and end normally
            if ($matched === 0) {
                if ($log) {
                    $log->update([
                        'matched'   => 0,
                        'updated'   => 0,
                        'changes'   => 0,
                        'synced_at' => now(),
                        'meta'      => array_merge(($log->meta ?? []), [
                            'status' => 'done',
                            'note'   => 'No products matched (material_id not set or not found).',
                        ]),
                    ]);
                }

                $this->dispatchBrowserEvent('alert', [
                    'type' => 'warning',
                    'message' => "SYNC finished ({$system->name}): no products matched (material_id not set or not found).",
                ]);
                return;
            }

            // 4) Create XLSX log and upload (only if there are changes)
            $xlsxPath = null;

            if (!empty($this->syncChanges)) {
                $xlsxPath = $this->buildAndUploadSyncXlsx($system->code ?? 'phenix');
                $this->syncLogPath = $xlsxPath;

                try {
                    $this->syncDownloadUrl = Storage::disk('s3')->temporaryUrl($xlsxPath, now()->addMinutes(60));
                } catch (\Throwable $e) {
                    $this->syncDownloadUrl = Storage::disk('s3')->url($xlsxPath);
                }
            }

            // ✅ Update log row AFTER everything
            if ($log) {
                $log->update([
                    'matched'   => $matched,
                    'updated'   => $updated,
                    'changes'   => count($this->syncChanges),
                    'xlsx_path' => $xlsxPath,
                    'synced_at' => now(),
                    'meta'      => array_merge(($log->meta ?? []), [
                        'status' => 'done',
                        'items_received' => count($items),
                        'price_map_count' => count($priceMap),
                    ]),
                ]);
            }

            // 5) Show modal
            $this->showSyncModal = true;
            $this->dispatchBrowserEvent('open-sync-modal');

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "SYNC done ({$system->name}). Updated: {$updated}. Matched: {$matched}. Changes: " . count($this->syncChanges),
            ]);

        } catch (\Throwable $e) {

            // ✅ Store failure in log meta (if log exists)
            if ($log) {
                $log->update([
                    'synced_at' => now(),
                    'meta'      => array_merge(($log->meta ?? []), [
                        'status' => 'failed',
                        'error'  => $e->getMessage(),
                    ]),
                ]);
            }

            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => 'SYNC failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Build XLSX file from $this->syncChanges and upload to S3 in /sync_log/.
     * Returns the S3 path.
     */
    private function buildAndUploadSyncXlsx(string $systemCode): string
    {
        // Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray([
            ['sku', 'material_id', 'en_name', 'old_price', 'new_price', 'changed_at']
        ], null, 'A1');


        $now = Carbon::now()->toDateTimeString();

        // Rows
        $row = 2;
        foreach ($this->syncChanges as $c) {
            $sheet->fromArray([[
                $c['sku'] ?? '',
                $c['material_id'] ?? '',
                $c['en_name'] ?? '',
                $c['old_price'] ?? '',
                $c['new_price'] ?? '',
                $now,
            ]], null, 'A' . $row);

            $row++;
        }


        // Write to temp file
        $tmpFile = tempnam(sys_get_temp_dir(), 'phenix_sync_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($tmpFile);

        // Upload to S3
        $date = Carbon::now()->format('Y-m-d');
        $ts   = Carbon::now()->format('Ymd_His');
        $safeSystem = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $systemCode);

        $s3Path = "sync_log/{$safeSystem}/{$date}/price_sync_{$ts}.xlsx";

        Storage::disk('s3')->put($s3Path, file_get_contents($tmpFile), [
            'visibility' => 'public',
            'ContentType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        @unlink($tmpFile);

        return $s3Path;
    }
}