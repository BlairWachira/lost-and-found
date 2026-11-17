<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $item ? e($item['item_name']) : 'Item not found' ?> | Lost & Found</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<div class="sheet">

<header class="top">
    <h1 class="greeting">Lost &amp; Found</h1>
    <a href="logout.php" class="btn btn-logout">Logout</a>
</header>

<nav class="actions">
    <a href="dashboard.php" class="btn btn-blue">&larr; Back</a>
</nav>

<main>
<?php if (!$item): ?>
    <p class="empty">That item could not be found. It may have been removed.</p>
<?php else: ?>
    <article class="detail">
        <img src="<?= e($item['image_path']) ?>" alt="<?= e($item['item_name']) ?>">
        <div class="detail-info">
            <h2><?= e($item['item_name']) ?></h2>
            <?php if ($item['description']): ?>
                <p><?= e($item['description']) ?></p>
            <?php endif; ?>
            <p class="contact-box">
                <strong>Contact the finder</strong><br>
                <?= e($item['contact']) ?>
            </p>
            <p class="posted">Posted <?= e(date('j M Y', strtotime($item['created_at']))) ?></p>
        </div>
    </article>
<?php endif; ?>
</main>

</div>
</body>
</html>