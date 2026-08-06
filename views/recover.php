<div class="col-12 py-2">
	<?
		user_rights();

		if (isset($_POST["email"])) {
			$error = false;
			$message = '';

				/* Проверим есть уже ли такой email */
				$query = "SELECT id FROM users WHERE email='".trim($_POST["email"])."' LIMIT 1";
				if (!($statement = $db->prepare($query))) {
					exit('Error query');
				}
				$statement->execute();
				$statement->bind_result($id);
				while ($statement->fetch()) {
					/* Пользователь с таким e-mail eсть - отправляем письмо*/
					$password = generate_string();

					/* Изменяем пароль в БД у пользователя */
						if (isset($password) && isset($id)){
							$statement->close();
							$pass = password_hash($password, PASSWORD_BCRYPT);
							$query = "UPDATE users SET password='{$pass}' WHERE id={$id}";
							if (!($statementx = $db->prepare($query))) {
								exit('Error query update');
							}
							$statementx->execute();
							$statementx->close();
						} else {
							echo 'Не удалось изменить пароль. Попробуйте позже.';
							exit();
						}


					$to	     = strval(trim($_POST["email"]));
					$subject = 'Восстановление пароля на сайте ОКК';
					$message = 'Вами был запрошен новый пароль на сайте bmokk.ru. Ваш новый пароль: '.$password;
					$headers = [
										'From' => 'support@bmokk.ru',
								];
					if(mail($to, $subject, $message, $headers)){
						?>
							<p>
								Ваш новый пароль отправлен на <b><?=$_POST["email"]?></b>. Вы можете изменить его в <a href="/?login">личном кабинете</a>.
							</p>
						<?
						exit();
					} else {
						?>
							<p>
								Не удалось восстановить пароль. Попробуйте позже.
							</p>
						<?
						exit();
					}
                }

			if (!isset($password)) {
				$message2 = 'Адрес электронной почты не найден. <a href="/?register">Зарегистрируйтесь</a>.';
				js("$.notification.show('error','Адрес электронной почты не найден!')");
			}
		}
	?>
	<div class="autorization-form">
		<form method="post">
			<div class="card">
				<div class="card-body">
					<div class="card-title text-center phone">
						<i class="fa fa-user-circle" aria-hidden="true"></i> Восстановление пароля
					</div>
				<div class="mb-3">
					<label for="inputEmail" class="form-label">Введите e-mail который вы указали при регистрации</label>
					<input type="email" value="<?=isset($_POST["email"]) ? $_POST["email"] : ''?>" name="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" placeholder="Введите e-mail" required autofocus>
					<?=isset($message2) ? '<div class="my-2"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '.$message2.'</div></div>' : ''?>
				</div>
				<div class="mb-3">
					<span class="color_grey">
						На этот адрес будет отправлен новый пароль. Вы можете изменить его в личном кабинете. Если Вы регистрировались не по e-mail, то напишите нам в форме обратной связи.
					</span>
				</div>
				<div class="mb-3 text-center">
					<button class="btn btn-dark" type="submit">
						<i class="fa fa-envelope" aria-hidden="true"></i> Отправить
					</button>
				</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	// Регистрация
	var myAnchor = document.getElementById("register");
	myAnchor.addEventListener("click", function(event) {
		event.preventDefault();
		window.location = '/?register';
	}, false);
</script>

