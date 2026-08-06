<div class="col-12 py-2">
На сайте более не принимаются показания.<br>
Для передачи показаний используйте приложение "Госуслуги.Дом"<br>
<a href="/">Перейти на главную страницу</a>
</div>
<div style="text-align: center; padding-top: 50px;width: 100%;">
<a href="https://www.gosuslugi.ru/mp_dom" target="_blank">
    <img src="image/dom_2.jpg" class="img-fluid"></a>
</div>

<div class="col-12 py-2">
	<?
		user_rights();

		if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["password2"]) && isset($_POST["medig"])) {
			$error = false;
			$message = '';
			if ($_POST["password"] != $_POST["password2"]) {
				$error = true;
				$message = "Пароли не совпадают!";
			} elseif (strlen($_POST["password"]) < 6 || strlen($_POST["password2"]) < 6) {
				$error = true;
				$message = "Пароль должен быть не менее 6 символов!";
				js("$.notification.show('error','{$message}')");
			} else if ($_POST["medig"] != $_SESSION['captcha']){
				$error = true;
				js("$.notification.show('error','Вы не верно ввели число с картинки. Попробуйте заново!')");
			} elseif(	mb_stripos($_POST["email"], '.fr') !== false ||
						mb_stripos($_POST["email"], '.ua') !== false ||
						mb_stripos($_POST["email"], '.de') !== false ||
						mb_stripos($_POST["email"], '@yahoo.') !== false ||
						mb_stripos($_POST["email"], '.ca') !== false ||
						mb_stripos($_POST["email"], '.net') !== false ||
						mb_stripos($_POST["email"], '.uk') !== false ||
						mb_stripos($_POST["email"], '.pl') !== false ||
						mb_stripos($_POST["email"], '.vn') !== false ||
						mb_stripos($_POST["email"], '.tr') !== false ||
						mb_stripos($_POST["email"], '.ed') !== false ||
						mb_stripos($_POST["email"], '.nl') !== false ||
						mb_stripos($_POST["email"], '.no') !== false ||
						mb_stripos($_POST["email"], 'live.') !== false ||
						mb_stripos($_POST["email"], 'aol.') !== false ||
						mb_stripos($_POST["email"], '@hotmail.') !== false
					) {
					js("$.notification.show('error','Используйте другой адрес электронной почты или используйте номер телефона!')");
			} else {
				/*Ошибок нет - регистрируем */
				/* Проверим есть уже ли такой email */
				$query = "SELECT id FROM users WHERE email='".trim($_POST["email"])."' LIMIT 1";
				if (!($statement = $db->prepare($query))) {
					exit('Error query');
				}
				$statement->execute();
				$statement->bind_result($id);
				while ($statement->fetch()) {
					$error = true;
					$message2 = " Пользователь с таким e-mail уже зарегистрирован. Если вы забыли пароль, то перейдите на страницу <a href='/?recover'>восстановления пароля</a>";
					js("$.notification.show('error','Пользователь с таким e-mail уже зарегистрирован!')");
					record_log(0, "(Пользователь) Пользователь с таким e-mail уже зарегистрирован!");
                }
				$statement->close();

				if (strlen($_POST["email"]) > 31) {
					$error = true;
					js("$.notification.show('error','Логин слишком большой! Введите адрес электронной почты или номер телефона!')");
				}

				if (!$error){
					/* Такого пользователя нет - регистрируем */
					$id = null;
					$email = strval(trim($_POST["email"]));
					$password = strval(password_hash(trim($_POST["password"]), PASSWORD_BCRYPT));

					$query = "INSERT INTO users (id, email, password) VALUES (?,?,?)";
					if (!($statement_n = $db->prepare($query))) {
						exit('Error query users');
					}
					$statement_n->bind_param("iss", $id, $email, $password);
					$result = $statement_n->execute() ? true : false;
					// printf("Ошибка: %s.\n", $statement_n->error);
					$statement_n->close();

					if (!$result) {
						/* Не удалось добавить запись */
						record_log(0, "(Пользователь Регистрация) Не удалось добавить запись.");
						?>
							<p>
								Не удалось создать учетную запись (БД). Попробуйте позже.
							</p>
						<?
						exit;
					}

					$to      = trim($_POST["email"]);
					$subject = 'Регистрация на сайте ОКК';
					$message = include_once $_SERVER['DOCUMENT_ROOT'].'/views/mail_register.php';
					$headers = [
										'From' => 'support@bmokk.ru',
								];
					if(mail($to, $subject, $message, $headers)){
						?>
							<p>
								Поздравляем с успешной регистрацией!
								<? if (strpos($to,'@')) { ?>
								На <b><?=$to?></b> вам отправленно письмо с учетными данными.
								<? } else { ?>
								При регистрации вы указали в логине не адрес электронный почты. Вы не сможете использовать функцию восстановления пароля.
								<? }?>
							</p>
							<p>
								Авторизируйтесь в <a href="/?login">личном кабинете</a> и заполните необходимые поля (лицевой счет и приборы учета).
							</p>
						<?
						record_log(1, "(Пользователь {$to} Регистрация) Успешная регистрация.");
						exit;
					} else {
						record_log(0, "(Пользователь Регистрация) Не удалось добавить запись.");
						?>
							<p>
								Не удалось создать учетную запись. Попробуйте позже.
							</p>
						<?
						exit;
					}
				}
			}
		}
	?>

	<div class="autorization-form">
		<form method="post">
			<div class="card">
				<div class="card-body">
					<div class="card-title text-center phone">
						<i class="fa fa-user-circle" aria-hidden="true"></i> Регистрация
					</div>
				<div class="mb-3">
					<label for="inputEmail" class="form-label">Введите номер телефона или e-mail</label>
					<input type="text" value="<?=isset($_POST["email"]) ? $_POST["email"] : ''?>" name="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" placeholder="Введите e-mail или номер телефона" required autofocus>
					<span class="color_grey">
						Вы так же можете придумать любой логин, но если это не e-mail, то не будет возможности восстановления пароля.
					</span>
					<?=isset($message2) ? '<div class="my-2"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>'.$message2.'</div></div>' : ''?>
				</div>
				<div class="mb-3">
					<label for="inputPassword" class="form-label">Придумайте пароль</label>
					<input type="password" name="password" class="form-control" id="inputPassword" autocomplete="current-password" aria-describedby="passHelp" placeholder="Введите пароль" required>
				</div>
				<div class="mb-3">
					<label for="inputPassword2" class="form-label">Повторите пароль</label>
					<input type="password" name="password2" class="form-control" id="inputPassword2" autocomplete="current-password" aria-describedby="passHelp" placeholder="Введите пароль" required>
				</div>
				<div class="mb-3">
					<img src="<?='/views/captcha.php'?>">
					<input type="text" value="" name="medig" class="form-control my-1" placeholder="Введите число с картинки" required>
				</div>
				<div class="mb-3 text-center">
					<span class="color_grey">
						Нажимая кнопку «Регистрация», вы принимаете условия
						<a href="/?agreement" target="_blank">пользовательского соглашения</a>
					</span>
				</div>
				<div id="messages"></div>
				<div class="mb-3 text-center">
					<button class="btn btn-dark" type="submit" id="cancel">
						<i class="fa fa-times" aria-hidden="true"></i> Отмена
					</button>
					<button class="btn btn-dark" type="submit">
						<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Регистрация
					</button>
				</div>
				</div>
			</div>
		</form>
	</div>
</div>

<?
	if (isset($error)) {
		if ($error && strlen($message) > 1) {
		?>

			<script>
				$(document).ready(function() {
					$('#messages').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?=$message?></div>');
				});
			</script>
<?
		}
	}
?>

<script>
	// Отмена
	var myAnchor = document.getElementById("cancel");
	myAnchor.addEventListener("click", function(event) {
		event.preventDefault();
		window.location = '/?login';
	}, false);
</script>
