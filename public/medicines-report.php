<?php
// Load Laravel environment and Eloquent/DB
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // 1. Total Count (Runs directly on DB, supports 6+ Lakh medicines)
    $totalCount = DB::table('medicines')->count();

    // 2. With Photos Count
    $withImagesCount = DB::table('medicines')
        ->whereNotNull('image_urls')
        ->where('image_urls', '!=', '')
        ->where('image_urls', '!=', '[]')
        ->where('image_urls', '!=', '""')
        ->count();

    $withoutImagesCount = max(0, $totalCount - $withImagesCount);

    // 3. Category Breakdown
    $categoriesData = DB::table('medicines')
        ->select(DB::raw("CASE WHEN category IS NULL OR TRIM(category) = '' THEN 'Uncategorized' ELSE TRIM(category) END as cat_name"), DB::raw('COUNT(*) as total'))
        ->groupBy('cat_name')
        ->orderByDesc('total')
        ->get();

    $totalCategoriesCount = $categoriesData->count();

    // 4. Filtering & Pagination Parameters
    $search = trim($_GET['search'] ?? '');
    $filterImage = $_GET['filter_image'] ?? 'all';
    $filterCategory = $_GET['filter_category'] ?? 'all';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 50;

    // Build Search & Filter Query
    $query = DB::table('medicines');

    if ($search !== '') {
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('composition', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%")
              ->orWhere('id', '=', $search);
        });
    }

    if ($filterImage === 'with_image') {
        $query->whereNotNull('image_urls')
              ->where('image_urls', '!=', '')
              ->where('image_urls', '!=', '[]')
              ->where('image_urls', '!=', '""');
    } elseif ($filterImage === 'no_image') {
        $query->where(function($q) {
            $q->whereNull('image_urls')
              ->orWhere('image_urls', '=', '')
              ->orWhere('image_urls', '=', '[]')
              ->orWhere('image_urls', '=', '""');
        });
    }

    if ($filterCategory !== 'all') {
        if ($filterCategory === 'Uncategorized') {
            $query->where(function($q) {
                $q->whereNull('category')->orWhere(DB::raw("TRIM(category)"), '=', '');
            });
        } else {
            $query->where('category', '=', $filterCategory);
        }
    }

    $filteredTotal = $query->count();
    $totalPages = max(1, (int)ceil($filteredTotal / $perPage));
    if ($page > $totalPages && $totalPages > 0) {
        $page = $totalPages;
    }
    $offset = ($page - 1) * $perPage;

    $medicines = $query->orderBy('id', 'desc')->offset($offset)->limit($perPage)->get();

} catch (\Exception $e) {
    die("Database Connection / Query Error: " . $e->getMessage());
}

$processedMedicines = [];
foreach ($medicines as $med) {
    $hasImage = false;
    $firstImageUrl = null;
    
    if (!empty($med->image_urls)) {
        $raw = trim($med->image_urls);
        if (str_starts_with($raw, '[')) {
            $urls = json_decode($raw, true);
            if (is_array($urls) && count($urls) > 0) {
                $hasImage = true;
                $firstImageUrl = trim($urls[0]);
            }
        } elseif (str_contains($raw, '|')) {
            $parts = explode('|', $raw);
            $first = trim($parts[0]);
            if (!empty($first)) {
                $hasImage = true;
                $firstImageUrl = $first;
            }
        } elseif (!empty($raw) && $raw !== '[]' && $raw !== '""') {
            $hasImage = true;
            $firstImageUrl = trim($raw, '"[]');
        }
    }

    $cat = !empty($med->category) ? trim($med->category) : 'Uncategorized';

    $processedMedicines[] = [
        'id' => $med->id,
        'name' => $med->name ?? 'N/A',
        'composition' => $med->composition ?? '',
        'category' => $cat,
        'mrp' => number_format((float)($med->mrp ?? 0), 2),
        'price' => number_format((float)($med->price ?? 0), 2),
        'has_image' => $hasImage,
        'image_url' => $firstImageUrl
    ];
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicines Inventory & Analytics Report - Dawalo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .header-banner {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .badge-cat {
            font-size: 0.85rem;
            padding: 0.4em 0.7em;
            margin: 3px;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
        }
        .table-responsive {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            padding: 1.5rem;
        }
        .med-img-thumb {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .no-img-thumb {
            width: 45px;
            height: 45px;
            background: #e9ecef;
            color: #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 1.2rem;
        }
        .search-box {
            border-radius: 25px;
            padding-left: 20px;
        }
    </style>
</head>
<body>

<div class="header-banner">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="fw-bold mb-1"><i class="fa-solid fa-pills me-2"></i>Medicines Analytics & Inventory Report</h1>
                <p class="mb-0 text-white-50">Database Breakdown & Complete Medicines Report</p>
            </div>
            <div>
                <a href="/" class="btn btn-light text-primary fw-semibold rounded-pill px-4 me-2">
                    <i class="fa-solid fa-house me-1"></i> Home Page
                </a>
                <a href="/admin/login" class="btn btn-warning text-dark fw-semibold rounded-pill px-4">
                    <i class="fa-solid fa-user-shield me-1"></i> Admin Panel
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Stats Cards Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Total Medicines</h6>
                        <h2 class="fw-bold mb-0"><?= number_format($totalCount) ?></h2>
                    </div>
                    <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">With Photos</h6>
                        <h2 class="fw-bold mb-0"><?= number_format($withImagesCount) ?></h2>
                        <small class="opacity-75"><?= $totalCount > 0 ? round(($withImagesCount / $totalCount) * 100, 1) : 0 ?>% of Total</small>
                    </div>
                    <div class="stat-icon"><i class="fa-solid fa-image"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-danger text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Without Photos</h6>
                        <h2 class="fw-bold mb-0"><?= number_format($withoutImagesCount) ?></h2>
                        <small class="opacity-75"><?= $totalCount > 0 ? round(($withoutImagesCount / $totalCount) * 100, 1) : 0 ?>% of Total</small>
                    </div>
                    <div class="stat-icon"><i class="fa-solid fa-image-slash"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Categories</h6>
                        <h2 class="fw-bold mb-0"><?= number_format($totalCategoriesCount) ?></h2>
                        <small class="opacity-75">Active Categories</small>
                    </div>
                    <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <h5 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Category-wise Medicine Count</h5>
        <div class="d-flex flex-wrap">
            <a href="?filter_category=all" class="badge bg-<?= $filterCategory === 'all' ? 'primary text-white' : 'light text-dark border' ?> badge-cat me-1 mb-1">
                All Categories (<?= number_format($totalCount) ?>)
            </a>
            <?php foreach ($categoriesData as $catRow): 
                $catName = $catRow->cat_name;
                $cCount = $catRow->total;
                $percentage = $totalCount > 0 ? round(($cCount / $totalCount) * 100, 1) : 0;
                $isActive = ($filterCategory === $catName);
            ?>
                <a href="?filter_category=<?= urlencode($catName) ?>&search=<?= urlencode($search) ?>&filter_image=<?= urlencode($filterImage) ?>" 
                   class="badge <?= $isActive ? 'bg-primary text-white' : 'bg-light text-dark border' ?> badge-cat d-flex align-items-center gap-2">
                    <span class="fw-semibold"><?= htmlspecialchars($catName) ?>:</span> 
                    <span class="badge <?= $isActive ? 'bg-white text-primary' : 'bg-primary text-white' ?> rounded-pill"><?= number_format($cCount) ?> (<?= $percentage ?>%)</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <div class="table-responsive">
        <form method="GET" action="" class="row g-3 mb-3 align-items-center">
            <?php if ($filterCategory !== 'all'): ?>
                <input type="hidden" name="filter_category" value="<?= htmlspecialchars($filterCategory) ?>">
            <?php endif; ?>
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control search-box border-start-0" placeholder="Search by name, category, composition or ID...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="filter_image" class="form-select rounded-pill" onchange="this.form.submit()">
                    <option value="all" <?= $filterImage === 'all' ? 'selected' : '' ?>>All Image Statuses</option>
                    <option value="with_image" <?= $filterImage === 'with_image' ? 'selected' : '' ?>>With Photo Only</option>
                    <option value="no_image" <?= $filterImage === 'no_image' ? 'selected' : '' ?>>Without Photo Only</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-semibold"><i class="fa-solid fa-filter me-1"></i> Filter</button>
            </div>
            <div class="col-md-2 text-end">
                <span class="text-muted fw-semibold small">Found <?= number_format($filteredTotal) ?> results</span>
            </div>
        </form>

        <!-- Medicines List Table -->
        <table class="table table-hover align-middle mb-0" id="medicinesTable">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 70px;">ID</th>
                    <th scope="col" style="width: 80px;">Image</th>
                    <th scope="col">Medicine Name & Composition</th>
                    <th scope="col">Category</th>
                    <th scope="col">MRP</th>
                    <th scope="col">Price</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($processedMedicines) > 0): ?>
                    <?php foreach ($processedMedicines as $med): ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?= $med['id'] ?></td>
                        <td>
                            <?php if ($med['has_image'] && $med['image_url']): ?>
                                <img src="<?= htmlspecialchars($med['image_url']) ?>" alt="<?= htmlspecialchars($med['name']) ?>" class="med-img-thumb" onerror="this.onerror=null; this.src='/images/no-image.png';">
                            <?php else: ?>
                                <div class="no-img-thumb"><i class="fa-solid fa-prescription-bottle-medical"></i></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($med['name']) ?></div>
                            <?php if (!empty($med['composition'])): ?>
                                <small class="text-muted d-block"><?= htmlspecialchars($med['composition']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary opacity-75"><?= htmlspecialchars($med['category']) ?></span>
                        </td>
                        <td class="text-muted text-decoration-line-through">₹<?= $med['mrp'] ?></td>
                        <td class="fw-bold text-success">₹<?= $med['price'] ?></td>
                        <td>
                            <?php if ($med['has_image']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"><i class="fa-solid fa-check me-1"></i>Photo Ready</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i>No Photo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No medicines found matching your search query.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Server-side Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-4 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing Page <strong><?= $page ?></strong> of <strong><?= number_format($totalPages) ?></strong> (50 items per page)
            </div>
            <ul class="pagination pagination-sm mb-0">
                <?php
                $queryParams = $_GET;
                
                // First & Prev
                $queryParams['page'] = 1;
                $firstUrl = '?' . http_build_query($queryParams);
                $queryParams['page'] = max(1, $page - 1);
                $prevUrl = '?' . http_build_query($queryParams);
                
                // Next & Last
                $queryParams['page'] = min($totalPages, $page + 1);
                $nextUrl = '?' . http_build_query($queryParams);
                $queryParams['page'] = $totalPages;
                $lastUrl = '?' . http_build_query($queryParams);
                ?>

                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-item link-secondary page-link" href="<?= $firstUrl ?>">&laquo; First</a>
                </li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $prevUrl ?>">Previous</a>
                </li>

                <!-- Dynamic Page Numbers Range -->
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                for ($p = $startPage; $p <= $endPage; $p++):
                    $queryParams['page'] = $p;
                    $pUrl = '?' . http_build_query($queryParams);
                ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $pUrl ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $nextUrl ?>">Next</a>
                </li>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $lastUrl ?>">Last &raquo;</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
