<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>

<?php  
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}
?>

<?php 
$album_id = $_GET['album_id'];
$getAlbumByID = getAlbumByID($pdo, $album_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Album</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="editAlbumForm" style="display: flex; justify-content: center;">
        <div class="formContainer" style="width: 50%; border: 1px solid #ddd; padding: 20px;">
            <h2>Edit Album</h2>
            <?php if ($getAlbumByID): ?>
                <form action="core/handleForms.php" method="POST">
                    <input type="hidden" name="album_id" value="<?php echo $getAlbumByID['album_id']; ?>">
                    <p>
                        <label for="album_name">Album Name</label>
                        <input type="text" name="album_name" value="<?php echo htmlspecialchars($getAlbumByID['album_name']); ?>" placeholder="Enter album name" required>
                    </p>
                    <p>
                        <label for="album_description">Album Description</label>
                        <input type="text" name="album_description" value="<?php echo htmlspecialchars($getAlbumByID['album_description']); ?>" placeholder="Enter album description">
                    </p>
                    <input type="submit" name="editAlbumBtn" value="Update" style="margin-top: 10px;">
                </form>
            <?php else: ?>
                <p>Album not found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
