<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_POST['mn']) || !isset($_POST['l'])) {
		exit ('Нет данных!');
	}
	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'user') {
			exit('Error right!');
		}
	} else {
			exit('Error session!');
	}

	include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');

	$mn = $_POST['mn'];
	$l = intval($_POST['l']);

	$query = "SELECT summa FROM sberbank WHERE file_n='{$mn}' AND ls='{$l}' LIMIT 1";

	if (!($statement = $db->prepare($query))) {
		exit('Error query!');
	}
	$statement->execute();
	$statement->bind_result($summa);

	while ($statement->fetch()) {
		echo "Сумма начислений по счету № {$l} за выбранный месяц составляет <span id='summa'>".$summa."</span> руб.";
		exit;
	}
	$statement->close();

	echo 'Не возможно найти сумму к оплате!';