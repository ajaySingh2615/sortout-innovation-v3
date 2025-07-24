<?php
// Get all categories and apps
$categories = getCategories();
$apps = getApps();

// Count active items
$activeCategories = array_filter($categories, function($cat) {
    return $cat['status'] == 1;
});

$activeApps = array_filter($apps, function($app) {
    return $app['status'] == 1;
});
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted">Welcome to the Artist V2 Dashboard</p>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Categories (Total)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($categories); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($activeCategories); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Apps (Total)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($apps); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Apps</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($activeApps); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="artist_dashboard.php?page=category-form" class="btn btn-primary btn-block">
                                <i class="fas fa-plus me-2"></i> Add New Category
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="artist_dashboard.php?page=app-form" class="btn btn-info btn-block">
                                <i class="fas fa-plus me-2"></i> Add New App
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="artist_dashboard.php?page=setup" class="btn btn-warning btn-block">
                                <i class="fas fa-cogs me-2"></i> Setup Tools
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="../../artist-v2/index.php" class="btn btn-success btn-block" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i> View Frontend
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Categories and Apps -->
    <div class="row">
        <!-- Recent Categories -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Categories</h6>
                    <a href="artist_dashboard.php?page=categories" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (count($categories) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Take only the 5 most recent categories
                                    $recentCategories = array_slice($categories, 0, 5);
                                    foreach ($recentCategories as $category): 
                                    ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo !empty($category['image_url']) ? $category['image_url'] : '../assets/img/default-category.jpg'; ?>" 
                                                     alt="<?php echo $category['name']; ?>" 
                                                     class="img-thumbnail" 
                                                     width="50"
                                                     onerror="this.onerror=null; this.src='../assets/img/default-category.jpg';">
                                            </td>
                                            <td><?php echo $category['name']; ?></td>
                                            <td>
                                                <?php if ($category['status'] == 1): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="artist_dashboard.php?page=category-form&id=<?php echo $category['id']; ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No categories found. <a href="artist_dashboard.php?page=category-form">Add a category</a> to get started.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Apps -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Apps</h6>
                    <a href="artist_dashboard.php?page=apps" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (count($apps) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Take only the 5 most recent apps
                                    $recentApps = array_slice($apps, 0, 5);
                                    foreach ($recentApps as $app): 
                                    ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo !empty($app['image_url']) ? $app['image_url'] : '../assets/img/default-app.jpg'; ?>" 
                                                     alt="<?php echo $app['name']; ?>" 
                                                     class="img-thumbnail" 
                                                     width="50"
                                                     onerror="this.onerror=null; this.src='../assets/img/default-app.jpg';">
                                            </td>
                                            <td><?php echo $app['name']; ?></td>
                                            <td>
                                                <?php if ($app['status'] == 1): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="artist_dashboard.php?page=app-form&id=<?php echo $app['id']; ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No apps found. <a href="artist_dashboard.php?page=app-form">Add an app</a> to get started.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> 