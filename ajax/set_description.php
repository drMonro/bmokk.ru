<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_POST['id']) || !isset($_POST['desc']) ) {
		exit ('Нет данных!');
	}

	if ($_POST['id'] < 1) {
		exit ('Не выбран раздел!');
	}

	if (strlen($_POST['desc']) < 1) {
		exit ('Нет описания!');
	}

	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'admin') {
			exit('Error right!');
		}
	} else {
			exit('Error session!');
	}

	include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');

	$id = intval($_POST['id']);
	$desc = strval(trim($_POST['desc']));

	$query = "UPDATE contents SET description='".$desc."' WHERE id={$id}";
	if (!($statement = $db->prepare($query))) {
		exit('Error query!');
	}
	$statement->execute();
	$statement->close();

	echo '777';