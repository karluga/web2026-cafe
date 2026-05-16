<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Add to favorites (if coming from menu)
if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $item_id = (int) $_GET['add'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO favorites (user_id, item_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $item_id]);
    $success = "Added to favorites!";
}

// Remove from favorites
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $fav_id = (int) $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
    $stmt->execute([$fav_id, $user_id]);
    $success = "Removed from favorites.";
}

// Fetch user's favorites with item details
$stmt = $pdo->prepare("
    SELECT f.id as fav_id, m.* 
    FROM favorites f
    JOIN menu m ON f.item_id = m.id 
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - Cafe</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container my-5">
        <h1 class="text-center mb-4">❤️ My Favorites</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (empty($favorites)): ?>
            <div class="text-center py-5">
                <h4>No favorites yet</h4>
                <a href="menu.php" class="btn btn-primary">Browse Menu</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($favorites as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top"
                                    style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?= htmlspecialchars($item['name']) ?>
                                </h5>
                                <p class="card-text text-muted">
                                    <?= htmlspecialchars($item['category']) ?>
                                </p>
                                <p class="fw-bold">₹
                                    <?= number_format($item['price'], 2) ?>
                                </p>
                                <a href="favorites.php?remove=<?= $item['fav_id'] ?>"
                                    class="btn btn-outline-danger btn-sm">Remove</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>