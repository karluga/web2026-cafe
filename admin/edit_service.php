<?php
$page_title = "Edit Service - Cafe Bastions";
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../classes/Service.php';
$serviceObj = new Service();

$id = (int) ($_GET['id'] ?? 0);
$service = $serviceObj->getById($id);

if (!$service) {
    echo "<div class='container my-5'><h3>Service not found!</h3></div>";
    require_once '../includes/footer.php';
    exit;
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_path = $service['image'];   // keep old image by default

    // Handle new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../images/';
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;

        $allowed_types = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'images/' . $file_name;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, WEBP & GIF files are allowed.";
        }
    }

    if (empty($title) || empty($description)) {
        $error = "Title and description are required!";
    } elseif (empty($error)) {
        if ($serviceObj->update($id, $title, $description, $image_path)) {
            $success = "Service updated successfully!";
            header("refresh:2;url=dashboard.php");
        } else {
            $error = "Failed to update service.";
        }
    }
}
?>

<div class="container my-5">
    <h2>Edit Service</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($service['title']) ?>" class="form-control"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="6"
                required><?= htmlspecialchars($service['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Image</label><br>
            <img src="/cafe_website/<?= htmlspecialchars($service['image']) ?>"
                style="max-width: 250px; border: 2px solid #ddd; border-radius: 8px;" alt="Current">
        </div>

        <div class="mb-3">
            <label class="form-label">Upload New Image (optional)</label>
            <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">

            <div class="mt-3">
                <img id="imagePreview" src="#" alt="New Preview"
                    style="max-width: 300px; max-height: 200px; display: none; border: 2px solid #ddd; border-radius: 8px;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Service</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
    // Image Preview for new upload
    document.getElementById('imageInput').addEventListener('change', function (e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>