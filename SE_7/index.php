<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>
<?php  
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$addToAlbum = isset($_POST['addToAlbum']) ? $_POST['addToAlbum'] : 'no';
$getAllPhotos = getAllPhotos($pdo); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Home</title>
	<link rel="stylesheet" href="styles/styles.css">
	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
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
	<form action="" method="POST" enctype="multipart/form-data">
	    <p>
	        <label for="addToAlbum">Would you like to create an album?</label>
	        <select id="addToAlbum" name="addToAlbum" onchange="this.form.submit()">
	            <option value="no" <?php echo $addToAlbum === 'no' ? 'selected' : ''; ?>>No</option>
	            <option value="yes" <?php echo $addToAlbum === 'yes' ? 'selected' : ''; ?>>Yes</option>
	        </select>
	    </p>
	</form>

	<form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
	    <?php if ($addToAlbum === 'yes'): ?>
	        <p>
	            <label for="albumName">Album Name</label>
	            <input type="text" name="albumName" placeholder="Enter album name">
	        </p>
	        <p>
	            <label for="photoDescription">Description</label>
	            <input type="text" name="albumDescription" placeholder="Album description">
	        </p>
	        <p>
	            <label for="photos">Upload Photos</label>
	            <input type="file" name="photos[]" multiple>
	        </p>
	    <?php else: ?>
	        <p>
	            <label for="photoDescription">Description</label>
	            <input type="text" name="photoDescription" placeholder="Photo description">
	        </p>
	        <p>
	            <label for="singlePhoto">Upload Photo</label>
	            <input type="file" name="image">
	        </p>
	    <?php endif; ?>
	    <input type="hidden" name="addToAlbum" value="<?php echo $addToAlbum; ?>">
	    <input type="submit" name="insertPhotoBtn" value="Publish" style="margin-top: 10px;">
	</form>

	<?php foreach ($getAllPhotos as $row): ?>
	    <div class="images" style="display: flex; justify-content: center; margin-top: 25px;">
	        <div class="photoContainer" style="background-color: ghostwhite; border-style: solid; border-color: gray;width: 50%;">

	            <img src="images/<?php echo $row['photo_name']; ?>" alt="" style="width: 100%;">

	            <div class="photoDescription" style="padding:25px;">
	                <a href="profile.php?username=<?php echo $row['username']; ?>">
	                    <h2><?php echo $row['username']; ?></h2>
	                </a>
	                <p><i><?php echo $row['date_added']; ?></i></p>

	                <?php if (!empty($row['album_id'])): ?>
	                    <?php $albumDetails = getAlbumDetails($pdo, $row['album_id']); ?>
	                    <?php if ($albumDetails): ?>
	                        <h4><?php echo htmlspecialchars($albumDetails['album_name']); ?></h4>
	                        <p><?php echo htmlspecialchars($albumDetails['album_description']); ?></p>
	                    <?php endif; ?>
	                <?php else: ?>
	                    <h4><?php echo htmlspecialchars($row['description']); ?></h4>
	                <?php endif; ?>

	                <?php if ($_SESSION['username'] == $row['username']): ?>
	                    <a href="editphoto.php?photo_id=<?php echo $row['photo_id']; ?>" style="float: right;"> Edit </a>
	                    <br><br>
	                    <a href="deletephoto.php?photo_id=<?php echo $row['photo_id']; ?>" style="float: right;"> Delete</a>
	                <?php endif; ?>
	            </div>
	        </div>
	    </div>
	<?php endforeach; ?>

</body>
</html>
