<?
	if (!session_id()) {
		session_start();
	}

	if (!isset($_POST['email']) || !isset($_POST['message'])) {
		exit ('Нет данных!');
	}

	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'admin') {
			exit('Error right!');
		}
	} else {
			exit('Error session!');
	}

	$to = trim($_POST['email']);
	$message = trim($_POST['message']);
	$subject = 'Письмо с сайта bmokk.ru';
	$headers = [
						'From' => 'support@bmokk.ru',
				];

	if(mail($to, $subject, $message, $headers)){
		echo 777;
	} else {
		echo 'no777';
	}