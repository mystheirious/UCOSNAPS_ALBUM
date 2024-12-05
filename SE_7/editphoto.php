<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$addToAlbum = isset($_POST['addToAlbum']) ? $_POST['addToAlbum'] : 'no'; 
$getPhotoByID = getPhotoByID($pdo, $_GET['photo_id']); 
$albums = getAllAlbums($pdo, $_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Photo</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="editPhotoForm">
        <form action="" method="POST" enctype="multipart/form-data">
            <p>
                <label for="addToAlbum">Do you want to add this photo to an album?</label>
                <select id="addToAlbum" name="addToAlbum" onchange="this.form.submit()">
                    <option value="no" <?php echo $addToAlbum === 'no' ? 'selected' : ''; ?>>No</option>
                    <option value="yes" <?php echo $addToAlbum === 'yes' ? 'selected' : ''; ?>>Yes</option>
                </select>
            </p>
        </form>
		<form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
	    <?php if ($addToAlbum === 'no'): ?>
	        <p>
	            <label for="photoDescription">Description</label>
	            <input type="text" name="photoDescription" value="<?php echo $getPhotoByID['description']; ?>" placeholder="Enter photo description">
	        </p>
	        <p>
	            <label for="singlePhoto">Upload Photo</label>
	            <input type="file" name="image">
	        </p>
	    <?php else: ?>
	        <p>
	            <label for="album">Assign to Album</label>
	            <select name="album_id">
	                <option value="">No Album</option>
	                <?php foreach ($albums as $album) { ?>
	                    <option value="<?php echo $album['album_id']; ?>" 
	                        <?php echo $getPhotoByID['album_id'] == $album['album_id'] ? 'selected' : ''; ?>>
	                        <?php echo htmlspecialchars($album['album_name']); ?>
	                    </option>
	                <?php } ?>
	            </select>
	        </p>
	        <p>
	            <label for="photos">Upload Photo</label>
	            <input type="file" name="image">
	        </p>
	    <?php endif; ?>

	    <input type="hidden" name="addToAlbum" value="<?php echo $addToAlbum; ?>">
	    <input type="hidden" name="photo_id" value="<?php echo $_GET['photo_id']; ?>">
	    <input type="submit" name="updatePhotoBtn" value="Update" style="margin-top: 10px;">
	</form>
    </div>
</body>
</html>
