<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../login.php");
			}

			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}

		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}


if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
			header("Location: ../index.php");
		}

		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}

}


if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	header("Location: ../login.php");
}


if (isset($_POST['insertPhotoBtn'])) {
    $addToAlbum = $_POST['addToAlbum'];
    $description = $_POST['photoDescription'] ?? null;
    $albumName = $_POST['albumName'] ?? null;
    $albumDescription = $_POST['albumDescription'] ?? null;

    if (!empty($_FILES['photos']['name'][0]) || !empty($_FILES['image']['name'])) {
        if ($addToAlbum === 'yes') {
            $albumInserted = insertAlbum($pdo, $albumName, $_SESSION['username'], $albumDescription);

            if ($albumInserted) {
                $albumId = $pdo->lastInsertId();

                foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                    $fileName = $_FILES['photos']['name'][$key];
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $uniqueID = sha1(md5(rand(1, 9999999)));
                    $photoName = $uniqueID . "." . $fileExtension;

                    $photoPath = "../images/" . $photoName;
                    if (move_uploaded_file($tmp_name, $photoPath)) {
                        insertPhoto($pdo, $photoName, $_SESSION['username'], null, $albumId);
                    }
                }
            }
        }
        elseif ($addToAlbum === 'no') {
            $fileName = $_FILES['image']['name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueID = sha1(md5(rand(1, 9999999)));
            $photoName = $uniqueID . "." . $fileExtension;

            $photoPath = "../images/" . $photoName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $photoPath)) {
                insertPhoto($pdo, $photoName, $_SESSION['username'], $description);
            }
        }
    }
    header("Location: ../index.php");
    exit();
}


if (isset($_POST['updatePhotoBtn'])) {
    $photoID = $_POST['photo_id'];
    $description = $_POST['photoDescription'];
    $albumID = !empty($_POST['album_id']) ? $_POST['album_id'] : null;

    $sql = "UPDATE photos SET description = ?, album_id = ? WHERE photo_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$description, $albumID, $photoID]);

    $sql = "SELECT photo_name FROM photos WHERE photo_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$photoID]);
    $photo = $stmt->fetch();

    if (!empty($_FILES['image']['name'])) {
        $fileName = $_FILES['image']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueID = sha1(md5(rand(1, 9999999)));
        $newPhotoName = $uniqueID . "." . $fileExtension;
        $newPhotoPath = "../images/" . $newPhotoName;

        $oldPhotoPath = "../images/" . $photo['photo_name'];
        if (file_exists($oldPhotoPath)) {
            unlink($oldPhotoPath);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $newPhotoPath)) {
            $sql = "UPDATE photos SET photo_name = ? WHERE photo_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$newPhotoName, $photoID]);
        } else {
            $_SESSION['message'] = "Error uploading the photo.";
            $_SESSION['status'] = '400';
            header("Location: editphoto.php?photo_id=" . $photoID);
            exit();
        }
    }

    $_SESSION['message'] = "Photo updated successfully!";
    $_SESSION['status'] = '200';
    header("Location: ../index.php");
    exit();
}


if (isset($_POST['deletePhotoBtn'])) {
	$photo_name = $_POST['photo_name'];
	$photo_id = $_POST['photo_id'];
	$deletePhoto = deletePhoto($pdo, $photo_id);

	if ($deletePhoto) {
		unlink("../images/".$photo_name);
		header("Location: ../index.php");
	}

}


if (isset($_POST['insertAlbumBtn'])) {
    $album_name = trim($_POST['album_name']);
    if (!empty($album_name)) {
        $insertAlbum = insertAlbum($pdo, $album_name, $_SESSION['username'], $_SESSION['album_description']);
        $_SESSION['message'] = $insertAlbum ? "Album created successfully!" : "Failed to create album.";
        header("Location: ../album.php");
    } else {
        $_SESSION['message'] = "Album name cannot be empty.";
        header("Location: ../album.php");
    }
}

if (isset($_POST['editAlbumBtn'])) {

    $album_id = $_POST['album_id'];
    $album_name = trim($_POST['album_name']);
    $album_description = trim($_POST['album_description']);

    if (!empty($album_name)) {
        $updateQuery = updateAlbum($pdo, $album_id, $album_name, $album_description);
        
        if ($updateQuery) {
            $_SESSION['message'] = "Album updated successfully!";
            $_SESSION['status'] = '200';
        } else {
            $_SESSION['message'] = "Failed to update album.";
            $_SESSION['status'] = '400';
        }

        header("Location: ../album.php");
        exit();
    } else {
        $_SESSION['message'] = "Album name cannot be empty.";
        $_SESSION['status'] = '400';
        header("Location: ../album.php");
        exit();
    }
}


if (isset($_POST['deleteAlbumBtn'])) {
    $album_id = $_POST['album_id'];
    $album_name = $_POST['album_name'];

    $deletePhotosQuery = deletePhotosByAlbum($pdo, $album_id);

    if ($deletePhotosQuery) {
        $deleteAlbumQuery = deleteAlbum($pdo, $album_id);

        if ($deleteAlbumQuery) {
            $_SESSION['message'] = "Album and its photos deleted successfully!";
            $_SESSION['status'] = '200';
        } else {
            $_SESSION['message'] = "Failed to delete album.";
            $_SESSION['status'] = '400';
        }
    } else {
        $_SESSION['message'] = "Failed to delete photos in album.";
        $_SESSION['status'] = '400';
    }

    header("Location: ../album.php");
    exit();
}
?>
