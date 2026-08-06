<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_GET['id'])) {
		exit ('Нет данных!');
	}
	if ($_GET['id'] == 0) {
		exit ('Не выбран раздел!');
	}
	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'admin') {
			exit('Error right!');
		}
	} else {
			exit('Error session!');
	}

	include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');

	$id = intval($_GET['id']);
	$query = "SELECT description FROM contents WHERE id={$id} LIMIT 1";
	if (!($statement = $db->prepare($query))) {
		exit('Error query!');
	}
	$statement->execute();
	$statement->bind_result($description);

	while ($statement->fetch()) {
		echo $description;
		exit;
	}
	$statement->close();

	echo 'Нет описания...';