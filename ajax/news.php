<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_POST['id']) || !isset($_POST['data']) || !isset($_POST['action']) ) {
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

	$act = trim($_POST['action']);
	$id = $_POST['id'];
	$data = trim($_POST['data']);

	if ($act == "change_visibility"){
		/* Изменяем видимость новости */
		$query = "UPDATE news SET visibility='".$data."' WHERE id={$id}";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (change_visibility)');
		}
		$statement->execute();
		$statement->close();
		exit('777');
	} elseif($act == "get_description") {
		/* Получаем описание */
		$query = "SELECT description FROM news WHERE id={$id}";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (get_description)');
		}
		$statement->execute();
		$statement->bind_result($description);
		while ($statement->fetch()) {
			echo $description;
			exit;
		}
		$statement->close();
		exit('777');
	} elseif($act == "set_description" && isset($_POST['title'])) {
		/* Пишем описание в бд*/
		$t = trim($_POST['title']);
		$query = "UPDATE news SET description='{$data}', title='{$t}' WHERE id={$id}";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (set_description)');
		}
		$statement->execute();
		$statement->close();
		exit('777');
	} elseif($act == "delete") {
		/* Удаляем запись */
		$query = "DELETE FROM news WHERE id={$id}";
			if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (delete)');
		}
		$statement->execute();
		$statement->close();
		exit('777');
	} elseif($act == "add"  && isset($_POST['title'])) {
		/* Добавляем запись */
		$t = trim($_POST['title']);
		$id = NULL;
		$query = "INSERT INTO news (id, title, description) VALUES (?,?,?)";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (add)');
		}
		$statement->bind_param("iss", $id, $t, $data);
		$statement->execute();
		$statement->close();
		exit('777');
	} else {
		exit('Не верное действие!');
	}
