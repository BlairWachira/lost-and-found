<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['item_name'] ?? '');
    $desc    = trim($_POST['description'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $file    = $_FILES['item_picture'] ?? null;

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

    if ($name === '' || $contact === '') {
        $error = 'Please enter the item name and your contact.';
    } elseif (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please choose a picture of the item.';
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $error = 'The picture must be smaller than 5 MB.';
    } else {
        // Check the real file type, not just the extension
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            $error = 'Only JPG, PNG, WEBP or GIF pictures are allowed.';
        } else {
            $dir = __DIR__ . '/uploads';
            if (!is_dir($dir)) { mkdir($dir, 0755, true); }

            $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
            if (move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
                $stmt = $pdo->prepare("INSERT INTO items (user_id, item_name, description, image_path, contact)
                                       VALUES (:u, :n, :d, :i, :c)");
                $stmt->execute([
                    ':u' => $_SESSION['user_id'],
                    ':n' => $name,
                    ':d' => $desc,
                    ':i' => 'uploads/' . $filename,
                    ':c' => $contact,
                ]);
                header("Location: dashboard.php");
                exit;
            }
            $error = 'Could not save the picture. Check that the uploads folder is writable.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item | Lost & Found</title>
    <link rel="stylesheet" href="additem.css">
</head>
<body>

<div class="page">
    <div class="collage" role="img" aria-label="Collage of lost and found items"></div>

    <form class="panel" method="post" enctype="multipart/form-data">
        <h1>Add Item</h1>

        <?php if ($error): ?>
            <p class="error" role="alert"><?= e($error) ?></p>
        <?php endif; ?>

        <label for="item_name">Item name</label>
        <input type="text" id="item_name" name="item_name" placeholder="e.g. Black backpack"
               value="<?= e($_POST['item_name'] ?? '') ?>" required>

        <label for="description">Short description (optional)</label>
        <input type="text" id="description" name="description" placeholder="Colour, brand, where you found it"
               value="<?= e($_POST['description'] ?? '') ?>">

        <label for="item_picture">Add Item Picture</label>
        <label class="file-box" for="item_picture"><span id="file-name">Item picture add</span></label>
        <input type="file" id="item_picture" name="item_picture" accept="image/*" required>

        <label for="contact">Add your Contact</label>
        <input type="text" id="contact" name="contact" placeholder="Phone number or email"
               value="<?= e($_POST['contact'] ?? '') ?>" required>

        <div class="row">
            <a href="dashboard.php" class="cancel">Cancel</a>
            <button type="submit">Add item</button>
        </div>
    </form>
</div>

<script>
document.getElementById('item_picture').addEventListener('change', function () {
    document.getElementById('file-name').textContent =
        this.files.length ? this.files[0].name : 'Item picture add';
});
</script>
</body>
</html>
