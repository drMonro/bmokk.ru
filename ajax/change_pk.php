<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_POST['pk']) || !isset($_POST['pu'])) {
		exit ('Нет данных!');
	}

	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'admin') {
			exit('Error right!');
		}
	} else {
			exit('Error session!');
	}

	$pu = intval(trim($_POST['pu']));
	$pk = strval(trim($_POST['pk']));

	include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');

	$query = "UPDATE pokazania SET pokazan='".$pk."' WHERE pribor={$pu}";

		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (change_visibility)');
		}
		$statement->execute();
		$statement->close();

echo 777;