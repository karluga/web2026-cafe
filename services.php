<?php
$page_title = "Menu - Cafe Bastions";
require_once 'includes/header.php';
require_once 'classes/Service.php';

$service = new Service();
$search = $_GET['search'] ?? '';
$services = $service->readAll($search);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-6 text-center">
            <h2>Our Menu</h2>
        </div>
        <div class="col-md-6">
            <form method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search menu..."
                    value="<?= htmlspecialchars($search) ?>">
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php if (empty($services)): ?>
            <p class="text-center">No foods found!</p>
        <?php else: ?>
            <?php foreach ($services as $item): ?>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($item['title']) ?>">
                        <div class="card-body">
                            <h3>
                                <?= htmlspecialchars($item['title']) ?>
                            </h3>
                            <p>
                                <?= htmlspecialchars($item['description']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>