<?php
// Load Laravel environment and Eloquent/DB
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $medicines = DB::table('medicines')->orderBy('id', 'desc')->get();
} catch (\Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

$totalCount = $medicines->count();
$withImagesCount = 0;
$withoutImagesCount = 0;
$categoryCounts = [];

$processedMedicines = [];

foreach ($medicines as $med) {
    // Process image status
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
        } elseif (!empty($raw) && $raw !== '[]') {
            $hasImage = true;
            $firstImageUrl = trim($raw, '"[]');
        }
    }
    
    if ($hasImage) {
        $withImagesCount++;
    } else {
        $withoutImagesCount++;
    }

    $cat = !empty($med->category) ? trim($med->category) : 'Uncategorized';
    if (!isset($categoryCounts[$cat])) {
        $categoryCounts[$cat] = 0;
    }
    $categoryCounts[$cat]++;

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

arsort($categoryCounts);
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
                        <h2 class="fw-bold mb-0"><?= count($categoryCounts) ?></h2>
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
            <?php foreach ($categoryCounts as $catName => $count): 
                $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 1) : 0;
            ?>
                <span class="badge bg-light text-dark border badge-cat d-flex align-items-center gap-2">
                    <span class="fw-semibold"><?= htmlspecialchars($catName) ?>:</span> 
                    <span class="badge bg-primary rounded-pill"><?= number_format($count) ?> (<?= $percentage ?>%)</span>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="table-responsive">
        <div class="row g-3 mb-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="searchInput" class="form-control search-box border-start-0" placeholder="Search by name, category, or composition...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filterImage" class="form-select rounded-pill">
                    <option value="all">All Image Statuses</option>
                    <option value="with_image">With Photo Only</option>
                    <option value="no_image">Without Photo Only</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <span class="text-muted fw-semibold" id="showingCount">Showing <?= number_format($totalCount) ?> medicines</span>
            </div>
        </div>

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
                <?php foreach ($processedMedicines as $med): ?>
                <tr class="med-row" 
                    data-name="<?= strtolower(htmlspecialchars($med['name'] . ' ' . $med['composition'])) ?>" 
                    data-category="<?= strtolower(htmlspecialchars($med['category'])) ?>"
                    data-has-image="<?= $med['has_image'] ? 'true' : 'false' ?>">
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
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filterImage = document.getElementById('filterImage');
        const rows = document.querySelectorAll('.med-row');
        const showingCount = document.getElementById('showingCount');

        function filterTable() {
            const query = searchInput.value.toLowerCase().trim();
            const imageFilter = filterImage.value;
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const category = row.getAttribute('data-category');
                const hasImage = row.getAttribute('data-has-image');

                const matchesQuery = name.includes(query) || category.includes(query);
                let matchesImage = true;

                if (imageFilter === 'with_image' && hasImage !== 'true') {
                    matchesImage = false;
                } else if (imageFilter === 'no_image' && hasImage !== 'false') {
                    matchesImage = false;
                }

                if (matchesQuery && matchesImage) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            showingCount.textContent = `Showing ${visibleCount.toLocaleString()} of <?= number_format($totalCount) ?> medicines`;
        }

        searchInput.addEventListener('input', filterTable);
        filterImage.addEventListener('change', filterTable);
    });
</script>

</body>
</html>
