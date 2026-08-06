<?php
function load_public_data($file){
	$path = __DIR__.'/data/'.$file.'.json';
	$data = json_decode(file_get_contents($path), true);

	if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
		error_log('Не удалось загрузить публичные данные из '.$path.': '.json_last_error_msg());
		return [];
	}

	array_walk_recursive($data, function (&$value) {
		if (is_string($value)) {
			$value = str_replace('{{max_chat_url}}', MAX_CHAT_URL, $value);
		}
	});

	return $data;
}
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
	static $public_content = null;
	if ($public_content === null) {
		$public_content = load_public_data('content');
	}

	return $public_content[$page] ?? [];
}

// Получить новости
function get_news($limit = false){
	static $public_news = null;
	if ($public_news === null) {
		$public_news = load_public_data('news');
	}

	return $limit ? array_slice($public_news, 0, (int) $limit) : $public_news;
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
	$log_user_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
	$log_type = ($log_type) ? 'ОК' : 'Ошибка';
	error_log("[{$log_type}] [{$log_user_ip}] {$log_description}");
}
