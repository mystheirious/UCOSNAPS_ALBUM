<?php  

require_once 'dbConfig.php';

function checkIfUserExists($pdo, $username) {
	$response = array();
	$sql = "SELECT * FROM user_accounts WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result"=> true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		}

		else {
			$response = array(
				"result"=> false,
				"status" => "400",
				"message"=> "User doesn't exist from the database"
			);
		}
	}

	return $response;

}


function insertNewUser($pdo, $username, $first_name, $last_name, $password) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO user_accounts (username, first_name, last_name, password) 
		VALUES (?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $first_name, $last_name, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		}

		else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	}

	else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}


function getAllUsers($pdo) {
	$sql = "SELECT * FROM user_accounts";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


function getUserByID($pdo, $username) {
	$sql = "SELECT * FROM user_accounts WHERE username = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$username]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}


function insertPhoto($pdo, $photo_name, $username, $description, $album_id = null, $photo_id = null) {
    if (empty($photo_id)) {
        $sql = "INSERT INTO photos (photo_name, username, description, album_id) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $executeQuery = $stmt->execute([$photo_name, $username, $description, $album_id]);
    } else {
        $sql = "UPDATE photos SET photo_name = ?, description = ?, album_id = ? WHERE photo_id = ?";
        $stmt = $pdo->prepare($sql);
        $executeQuery = $stmt->execute([$photo_name, $description, $album_id, $photo_id]);
    }
    return $executeQuery;
}


function getAllPhotos($pdo, $username = null) {
    if (empty($username)) {
        $sql = "
            SELECT photos.*, albums.album_name, albums.album_description AS album_description
            FROM photos
            LEFT JOIN albums ON photos.album_id = albums.album_id
            ORDER BY photos.date_added DESC";
        $stmt = $pdo->prepare($sql);
        $executeQuery = $stmt->execute();

        if ($executeQuery) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        $sql = "
            SELECT photos.*, albums.album_name, albums.album_description AS album_description
            FROM photos
            LEFT JOIN albums ON photos.album_id = albums.album_id
            WHERE photos.username = ?
            ORDER BY photos.date_added DESC";
        $stmt = $pdo->prepare($sql);
        $executeQuery = $stmt->execute([$username]);

        if ($executeQuery) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return [];
}


function getPhotoByID($pdo, $photo_id) {
	$sql = "SELECT * FROM photos WHERE photo_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$photo_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}


function deletePhoto($pdo, $photo_id) {
	$sql = "DELETE FROM photos WHERE photo_id  = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$photo_id]);

	if ($executeQuery) {
		return true;
	}
	
}


function insertComment($pdo, $photo_id, $username, $description) {
	$sql = "INSERT INTO photos (photo_id, username, description) VALUES(?,?,?)";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$photo_id, $username, $description]);

	if ($executeQuery) {
		return true;
	}
}


function getCommentByID($pdo, $comment_id) {
	$sql = "SELECT * FROM comments WHERE comment_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$comment_id]);

	if ($executeQuery) {
		return $stmt->fetch();
	}
}


function updateComment($pdo, $description, $comment_id) {
	$sql = "UPDATE comments SET description = ?, WHERE comment_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$description, $comment_id,]);

	if ($executeQuery) {
		return true;
	}
}


function deleteComment($pdo, $comment_id) {
	$sql = "DELETE FROM comments WHERE comment_id = ?";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute([$comment_id]);

	if ($executeQuery) {
		return true;
	}
}


function insertAlbum($pdo, $album_name, $username, $album_description) {
    $sql = "INSERT INTO albums (album_name, username, album_description) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$album_name, $username, $album_description]);
}


function getAllAlbums($pdo) {
	$stmt = $pdo->prepare("SELECT * FROM albums");
	$stmt->execute();
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAllPhotosByAlbum($pdo, $album_id) {
	$stmt = $pdo->prepare("SELECT * FROM photos WHERE album_id = ?");
	$stmt->execute([$album_id]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAlbumDetails($pdo, $album_id) {
    if (!$album_id) return null;
    $sql = "SELECT album_name, album_description FROM albums WHERE album_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$album_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function updateAlbum($pdo, $album_id, $album_name, $album_description) {
    $sql = "UPDATE albums SET album_name = ?, album_description = ? WHERE album_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$album_name, $album_description, $album_id]);
}


function deletePhotosByAlbum($pdo, $album_id) {
    $sql = "SELECT photo_name FROM photos WHERE album_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$album_id]);

    while ($photo = $stmt->fetch()) {
        $photoPath = "../images/" . $photo['photo_name'];
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }

    $sql = "DELETE FROM photos WHERE album_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$album_id]);
}


function deleteAlbum($pdo, $album_id) {
    $sql = "DELETE FROM albums WHERE album_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$album_id]);
}


function getAlbumByID($pdo, $album_id) {
    $query = "SELECT * FROM albums WHERE album_id = :album_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':album_id', $album_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $album ? $album : null;
}