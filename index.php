<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? $_SESSION['email'];
$error = "";
// echo "User ID: " . $user_id;

/* =========================
   ADD POST
========================= */
if (isset($_POST['create_post'])) {
    $content = trim($_POST['content']);

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $content);
        $stmt->execute();
        $stmt->close();
    }
}

/* =========================
   ADD COMMENT
========================= */
if (isset($_POST['add_comment'])) {
    $comment = trim($_POST['comment']);
    $post_id = (int)$_POST['post_id'];

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $post_id, $comment);
        $stmt->execute();
        $stmt->close();
    }
}

/* =========================
   DELETE COMMENT
========================= */
if (isset($_GET['delete_comment'])) {
    $id = (int)$_GET['delete_comment'];

    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}
/* =========================
   DELETE POST
========================= */
if (isset($_GET['delete_post'])) {
    $id = (int)$_GET['delete_post'];

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
</head>

<body>

    <h2>Welcome <?php echo htmlspecialchars($username); ?></h2>
    <a href="logout.php">Logout</a>

    <!-- =========================
     Add/CREATE POST
========================= -->
    <h3>Create Post</h3>
    <form method="POST">
        <input type="text" name="content" placeholder="Write a post" required>
        <button type="submit" name="create_post">Post</button>
    </form>

    <hr>

    <!-- =========================
    READ/ DISPLAY POSTS
========================= -->

    <h3>Posts</h3>

    <?php
    //3 variables for limit
//     $limit = 1;
// $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// $offset = ($page - 1) * $limit;
    $posts = $conn->prepare("
    SELECT posts.id, posts.content, posts.user_id, users.username
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.id DESC
    
");
//    LIMIT ? OFFSET ?
// for limit first line only 2 and 3 we need in any case
// $posts->bind_param("ii", $limit, $offset);
    $posts->execute();
    $result = $posts->get_result();
    ?>

    <?php while ($post = $result->fetch_assoc()): ?>
        <div style="border:1px solid #000; margin:10px; padding:10px;">


            <b>My ID is <?php echo htmlspecialchars($post['user_id']); ?> And</b>
            <b>My Name is <?php echo htmlspecialchars($post['username']); ?></b>
            <p>Post: <?php echo htmlspecialchars($post['content']); ?></p>
            <?php if ($post['user_id'] == $user_id): ?>
                <a href="?delete_post=<?php echo $post['id']; ?>"
                       onclick="return confirm('Are you sure?')">
                Delete Post</a>
            <?php endif; ?>
<?php if ($post['user_id'] == $user_id): ?>
    <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a>
<?php endif; ?>
            <!-- =========================
            ADD/CREATE COMMENTS
        ========================= -->

            <h4>Comments</h4>


            <?php
            $post_id = $post['id'];

            $comments = $conn->prepare("
 SELECT comments.id, comments.comment, comments.user_id, users.username
 FROM comments
 JOIN users ON comments.user_id = users.id
 WHERE comments.post_id = ?
 ORDER BY comments.id DESC
");

            $comments->bind_param("i", $post_id);
            $comments->execute();
            $comment_result = $comments->get_result();
            ?>



            <?php while ($comment = $comment_result->fetch_assoc()): ?>

                <p>

                    <b>My id is :<?php echo htmlspecialchars($comment['user_id']); ?>,</b>
                    <b>My name is :<?php echo htmlspecialchars($comment['username']); ?>:</b>
                    <?php echo htmlspecialchars($comment['comment']); ?>

                    <?php if ($comment['user_id'] == $user_id): ?>
                        <a href="?delete_comment=<?php echo $comment['id']; ?>"
                            onclick="return confirm('Are you sure?')">
                            Delete Comment
                        </a>
                        
                    <?php endif; ?>
                         <?php if ($comment['user_id'] == $user_id): ?>
                    <a href="edit_comment.php?id=<?php echo $comment['id']; ?>">Edit</a>
                     <?php endif; ?>
                </p>
            <?php endwhile; ?>

            <!-- =========================
             ADD COMMENT
        ========================= -->

            <form method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="text" name="comment" placeholder="Write a comment" required>
                <button type="submit" name="add_comment">Comment</button>
            </form>

        </div>
    <?php endwhile; ?>
<a href="?page=1">1</a>
<a href="?page=2">2</a>
<a href="?page=3">3</a>
</body>

</html>