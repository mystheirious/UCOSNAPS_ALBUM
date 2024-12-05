<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>

<?php  
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$album_id = $_GET['album_id'];
$getAlbumByID = getAlbumByID($pdo, $album_id);
$photos = getAllPhotosByAlbum($pdo, $album_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Album</title>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .album-container {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid red;
            border-radius: 8px;
            background-color: #ffcbd1;
        }
        .photo-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }
        .photo-container {
            background-color: ghostwhite;
            border: 1px solid gray;
            padding: 10px;
            width: 20%;
        }
        .photo-description {
            padding: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="album-container" style="position: relative;">
        <h2 style="text-align: center;">Are you sure you want to delete the album: <?php echo htmlspecialchars($getAlbumByID['album_name']); ?>?</h2>

        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="album_id" value="<?php echo $getAlbumByID['album_id']; ?>">
            <input type="submit" name="deleteAlbumBtn" value="Delete Album" style="margin-top: 10px;">
        </form>

        <?php if (count($photos) > 0): ?>
            <div class="photo-gallery">
                <?php foreach ($photos as $photo): ?>
                    <div class="photo-container">
                        <img src="images/<?php echo htmlspecialchars($photo['photo_name']); ?>" alt="Photo" style="width: 100%;">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No photos available in this album.</p>
        <?php endif; ?>
    </div>
</body>
</html>
