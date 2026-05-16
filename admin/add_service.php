<?php
$page_title = "Add New Service - Cafe Bastions";
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../classes/Service.php';
$serviceObj = new Service();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_path = 'images/food-default.jpg';

    // Handle image upload
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
        if ($serviceObj->create($title, $description, $image_path)) {
            $success = "Service added successfully!";
            header("refresh:2;url=dashboard.php");
        } else {
            $error = "Failed to add service.";
        }
    }
}
?>

<div class="container my-5">
    <h2 class="mb-4">Add New Menu Item</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Image</label>
            <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">

            <!-- Image Preview -->
            <div class="mt-3">
                <img id="imagePreview" src="#" alt="Preview"
                    style="max-width: 300px; max-height: 200px; display: none; border: 2px solid #ddd; border-radius: 8px;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Add Service</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
    // Image Preview
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