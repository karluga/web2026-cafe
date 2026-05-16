<?php
$page_title = "Change Password - Cafe Bastions";
require_once 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if(empty($username) || empty($confirm_password) || empty($password)) 
    {
        $error = "Please fill all fields";
    } 
    elseif($password != $confirm_password)
    {
        $error = "Passwords do not match";
    }
    elseif(strlen($password) < 6) 
    {
        $error = "Password must be at least 6 characters";
    }
    else 
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users set password = ? WHERE username = ?");
        $success = $stmt->execute([$hashed, $username]);

        if($success && $stmt->rowCount() > 0) 
        {
            header("Location: login.php");
            exit;
        } else 
        {
            $error = "Username not found or password not changed";
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h2 class="text-center mb-4">Change Password</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Change Password</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>