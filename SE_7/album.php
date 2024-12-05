<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$albums = getAllAlbums($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums</title>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .album-container {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .edit-delete-buttons {
            position: absolute;
            bottom: 10px;
            right: 10px;
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
    <?php  
    if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

        if ($_SESSION['status'] == "200") {
            echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
        }

        else {
            echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>"; 
        }

    }
    unset($_SESSION['message']);
    unset($_SESSION['status']);
    ?>
    <?php include 'navbar.php'; ?> 
    <?php foreach ($albums as $album): ?>
        <?php
        $photos = getAllPhotosByAlbum($pdo, $album['album_id']);
        
        if (count($photos) > 0): ?>
            <div class="album-container" style="position: relative;">
                <div class="album-header">
                    <h2><?php echo htmlspecialchars($album['username']); ?>'s Album: <?php echo htmlspecialchars($album['album_name']); ?></h2>
                </div>
                <div class="album-description">
                    <p><i><?php echo htmlspecialchars($album['album_description']); ?></i></p>
                </div>

                <?php if ($_SESSION['username'] == $album['username']): ?>
                    <div class="edit-delete-buttons">
                        <a href="editalbum.php?album_id=<?php echo $album['album_id']; ?>" style="margin-right: 10px;">Edit Album</a> |
                        <a href="deletealbum.php?album_id=<?php echo $album['album_id']; ?>">Delete Album</a>
                    </div>
                <?php endif; ?>

                <div class="photo-gallery">
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-container">
                            <img src="images/<?php echo $photo['photo_name']; ?>" alt="Photo" style="width: 100%;">
                            <div class="photo-description">
                                <p><i>Uploaded on: <?php echo $photo['date_added']; ?></i></p>
                                <?php if ($_SESSION['username'] == $photo['username']) { ?>
                                    <a href="editphoto.php?photo_id=<?php echo $photo['photo_id']; ?>">Edit</a> |
                                    <a href="deletephoto.php?photo_id=<?php echo $photo['photo_id']; ?>">Delete</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</body>
</html>
