<?

// ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ (БД)
// Значения задаются переменными окружения. Для локальной разработки можно
// создать config.local.php на основе config.local.php.example.

$localConfigPath = __DIR__.'/config.local.php';
$localConfig = is_file($localConfigPath) ? require $localConfigPath : [];

function database_config_value($name, $localConfig) {
	$value = getenv($name);
	return $value !== false && $value !== '' ? $value : ($localConfig[$name] ?? '');
}

$DB_NAME = database_config_value('DB_NAME', $localConfig);
$DB_SERVER = database_config_value('DB_SERVER', $localConfig);
$DB_USER_NAME = database_config_value('DB_USER_NAME', $localConfig);
$DB_USER_PASS = database_config_value('DB_USER_PASS', $localConfig);

if ($DB_NAME === '' || $DB_SERVER === '' || $DB_USER_NAME === '' || $DB_USER_PASS === '') {
	exit('Сайт в данный момент не доступен. Попробуйте обновить страницу или зайдите позже.');
}

$db = new mysqli($DB_SERVER, $DB_USER_NAME, $DB_USER_PASS, $DB_NAME);
mysqli_set_charset($db, 'utf8');
if ($db->connect_error) {
	exit('Сайт в данный момент не доступен. Попробуйте обновить страницу или зайдите позже.');
}
