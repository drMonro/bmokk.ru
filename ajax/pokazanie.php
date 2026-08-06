<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_POST['pribor']) || !isset($_POST['data'])) {
		exit ('Нет данных!');
	}

	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'admin') {
			exit('Error right!');
		}
	} else {
			exit('Error session!');
	}

	include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');

	$pribor = intval($_POST['pribor']);
	$data = intval($_POST['data']);

	/* Изменяем видимость принятия показания */
		$query = "UPDATE pokazania SET visibility={$data} WHERE pribor={$pribor}";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (change_visibility)');
		}
		$statement->execute();
		$statement->close();
		exit('777');