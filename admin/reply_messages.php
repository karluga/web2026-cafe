<?php
include '../includes/db.php';
include '../includes/header.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $message_id = $_POST['message_id'];
    $reply = $_POST['reply'];

    $stmt = $conn->prepare("UPDATE messages SET reply = ?, replied_at = NOW() WHERE id = ?");
    $stmt->bind_param('si', $reply, $message_id);
    if ($stmt->execute()) {
        $success_message = "Reply sent successfully.";
    } else {
        $error_message = "Failed to send reply.";
    }
}

$result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<div class="container">
    <h1>Reply to Messages</h1>

    <?php if (isset($success_message)) echo "<p class='success'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Reply</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['message']; ?></td>
                    <td><?php echo $row['reply'] ? $row['reply'] : 'No reply yet'; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                            <textarea name="reply" placeholder="Write your reply here..."></textarea>
                            <button type="submit">Send Reply</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
include '../includes/footer.php';
?>
