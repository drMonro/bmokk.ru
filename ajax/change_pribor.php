<?
	if (!session_id()) {
		session_start();
	}
	if (!isset($_POST['new_pribor']) || !isset($_POST['old_pribor']) || !isset($_POST['ls']) || strlen($_POST['ls']) < 1) {
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

	$ls = intval(trim($_POST['ls']));
	$old_pribor = intval(trim($_POST['old_pribor']));
	$new_pribor = intval(trim($_POST['new_pribor']));
	//echo 'ls '.$ls.' old_pribor '.$old_pribor.' new_pribor '.$new_pribor;
	//	exit();

	// Обновляем номер прибора
	$query = "UPDATE pribory SET pribor='".$new_pribor."' WHERE ls={$ls} AND pribor={$old_pribor}";
	if (!($statement = $db->prepare($query))) {
		exit('Error pribory query!');
	}
	$statement->execute();
	$statement->close();

	// Обновляем номер показания для нового прибора учета
	$query = "UPDATE pokazania SET pribor='".$new_pribor."' WHERE pribor={$old_pribor}";
	if (!($statement = $db->prepare($query))) {
		exit('Error pokazania query!');
	}
	$statement->execute();
	$statement->close();

	// Обновляем историю показаний для нового прибора учета
	$query = "UPDATE pokazania_history SET pribor='".$new_pribor."' WHERE pribor={$old_pribor}";
	if (!($statement = $db->prepare($query))) {
		exit('Error pokazania_history query!');
	}
	$statement->execute();
	$statement->close();

	echo '777';