<?
/* Генерация случайной строки (для восстановления пароля)*/
function generate_string($input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $strength = 8) {
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }

    return $random_string;
}

/* Проверка прав и переадресация */
function user_rights(){
	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] == 'user') {
			header('Location: /?user');
			exit;
		} elseif ($_SESSION['user_right'] == 'admin') {
			header('Location: /?admin');
			exit;
		}
	}
}

/* Получаем контент страницы из БД
	$name = true / false - получаем / Не получаем имя области
 */
function get_content($page, $name = false){
		global $db;
		$nm = $name ? ', name' : '';
		$query = "SELECT description {$nm} FROM contents WHERE page='{$page}'";
		if (!($statement = $db->prepare($query))) {
			exit('Error query');
		}
		$statement->execute();
		if ($name) {
			$statement->bind_result($description, $names);
		} else {
			$statement->bind_result($description);
		}
		$content = [];
		$i = 0;
		while ($statement->fetch()) {
			$content[$i]['description'] = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
			if ($name) {
				$content[$i]['name'] = $names;
			}
			$i++;
		}
		$statement->close();
	return $content;
}

// Получить новости
function get_news($limit = false){
	global $db;
	$s = $limit ? ' LIMIT '.$limit : '';

	$query = "SELECT id, title, description, date_new FROM news WHERE visibility = 1 ORDER BY id DESC {$s}";
	if (!($statement = $db->prepare($query))) {
		exit('Ошибка запроса (news)');
	}
	$statement->execute();
	$statement->bind_result($id, $title, $description, $date_new);
	$main_news = [];
	$i = 0;
	while ($statement->fetch()) {
		$main_news[$i]['id'] = $id;
		$main_news[$i]['title'] = $title;
		$main_news[$i]['description'] = $description;
		$main_news[$i]['date_new'] = $date_new;
		$i++;
	}
	$statement->close();
	return $main_news;
}

//Получить из 02/22  = Февраль 2022 г.
function get_date($d){
	$monthes = ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"];
	$s = explode("/", $d);
	if ($s[0] < 1 || $s[0] >12){
		return 'Не верный месяц!';
	}
	return $monthes[$s[0]-1]." 20".$s[1]." г.";
}

// Выполнить скрипт js из php
function js($s){
	echo "<script>{$s}</script>";
}

// Отправить письмо
function send_mail($to = '', $message = '', $subject = 'Письмо с сайта bmokk.ru'){
	$result = false;
	$headers = [
						'From' => 'support@bmokk.ru',
				];

	//echo 'to='.$to.'sb='.$subject.'ms='.$message;
	if(mail($to, $subject, $message, $headers)){
		js("$.notification.show('success','Письмо успешно отправлено!')");
		record_log(1, "(Отправка письма. Успешно) {$message}");
		$result = true;
	} else {
		js("$.notification.show('error','Что-то пошло не так. Письмо не отправлено!')");
		record_log(0, "(Отправка письма. Ошибка) {$message}");
	}

	return $result;
}

// Вывести accordion
function accordion ($id, $btn, $body){
	echo '	<div class="accordion" id="'.$id.'">
				  <div class="card">
					<div class="card-header" id="'.$id.'headingOne">
						<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#'.$id.'One" aria-expanded="true" aria-controls="'.$id.'One">
						  '.$btn.'
						</button>
					</div>

					<div id="'.$id.'One" class="collapse" aria-labelledby="'.$id.'headingOne" data-parent="#'.$id.'">
					  <div class="card-body">
					  '.$body.'
					  </div>
					</div>
				  </div>
				</div>';

}

// Логированиесобытий
function record_log ($log_type, $log_description){
	global $db;
	$id = NULL;
	$log_user_ip = $_SERVER['REMOTE_ADDR'];
	$log_type = ($log_type) ? 'ОК' : 'Ошибка';
		/* Добавляем запись */
		$query = "INSERT INTO record_logs (id, log_type, log_user_ip, log_description) VALUES (?,?,?,?)";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (logs)');
		}
		$statement->bind_param("isss", $id, $log_type, $log_user_ip, $log_description);
		$statement->execute();
		$statement->close();
}