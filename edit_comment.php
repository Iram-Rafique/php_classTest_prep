<?php
session_start();
require 'config.php';

$user_id = $_SESSION['user_id'];
$id = (int)$_GET['id'];

/* GET COMMENT */
$stmt = $conn->prepare("SELECT * FROM comments WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$comment = $stmt->get_result()->fetch_assoc();

/* UPDATE */
if (isset($_POST['update_comment'])) {
    $newComment = trim($_POST['comment']);

    if (!empty($newComment)) {
        $stmt = $conn->prepare("
            UPDATE comments 
            SET comment = ? 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("sii", $newComment, $id, $user_id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}
?>

<form method="POST">
    <input type="text" name="comment" value="<?php echo htmlspecialchars($comment['comment']); ?>">
    <button name="update_comment">Update</button>
</form>