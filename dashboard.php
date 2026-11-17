<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require __DIR__ . '/db.php';

$firstName = explode(' ', trim($_SESSION["full_name"]))[0];
$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $pdo->prepare("SELECT id, item_name, image_path FROM items
                           WHERE item_name LIKE :q OR description LIKE :q
                           ORDER BY created_at DESC");
    $stmt->execute([':q' => '%' . $q . '%']);
} else {
    $stmt = $pdo->query("SELECT id, item_name, image_path FROM items ORDER BY created_at DESC");
}
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Lost & Found</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<div class="sheet">

<header class="top">
    <h1 class="greeting">Hello <?= e($firstName) ?></h1>
    <a href="logout.php" class="btn btn-logout">Logout</a>
</header>

<form class="search" method="get" action="dashboard.php" role="search">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search for a lost item..." aria-label="Search items">
</form>

<nav class="actions">
    <a href="add-item.php" class="btn btn-add">Add items</a>
    <a href="about.php"    class="btn btn-blue">About</a>
    <a href="contacts.php" class="btn btn-blue">Contacts</a>
</nav>

<main>
    <?php if ($q !== ''): ?>
        <p class="result-note">
            <?= count($items) ?> result<?= count($items) === 1 ? '' : 's' ?> for “<?= e($q) ?>”
            · <a href="dashboard.php">clear</a>
        </p>
    <?php endif; ?>

    <?php if (!$items): ?>
        <p class="empty">
            <?= $q !== '' ? 'Nothing matches that search. Try a different word.' : 'No items yet. Click “Add items” to post the first one.' ?>
        </p>
    <?php else: ?>
        <section class="grid">
            <?php foreach ($items as $item): ?>
                <a class="card" href="lostitem.php?id=<?= (int)$item['id'] ?>">
                    <img src="<?= e($item['image_path']) ?>" alt="<?= e($item['item_name']) ?>" loading="lazy">
                    <span class="card-name"><?= e($item['item_name']) ?></span>
                </a>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

</div>
</body>
</html>