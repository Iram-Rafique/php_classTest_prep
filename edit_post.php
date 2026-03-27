<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$id = (int)$_GET['id'];

/* =========================
   GET POST DATA
========================= */
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

/* =========================
   UPDATE POST
========================= */
if (isset($_POST['update_post'])) {
    $content = trim($_POST['content']);

    if (!empty($content)) {
        $stmt = $conn->prepare("
            UPDATE posts 
            SET content = ? 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("sii", $content, $id, $user_id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}
?>

<form method="POST">
    <input type="text" name="content" value="<?php echo htmlspecialchars($post['content']); ?>">
    <button name="update_post">Update</button>
</form>