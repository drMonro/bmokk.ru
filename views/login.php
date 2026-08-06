<div class="col-12 py-2">
На сайте более не принимаются показания.<br>
Для передачи показаний используйте приложение "Госуслуги.Дом"<br>
<a href="/">Перейти на главную страницу</a>
</div>
<div style="text-align: center; padding-top: 50px;width: 100%;">
<a href="https://www.gosuslugi.ru/mp_dom" target="_blank">
    <img src="image/dom_2.jpg" class="img-fluid"></a>
</div>
<? exit; ?>


<div class="col-12 py-2">
	<?
		user_rights();

		if (isset($_POST["email"]) && isset($_POST["password"])) {
		    $_SESSION = [];

			$email_p = trim($_POST["email"]);
			$password_p = strval(trim($_POST["password"]));

			/* Проверим есть уже ли такой email */
			$query = "SELECT id, email, password, description, user_right FROM users WHERE email='".$email_p."' LIMIT 1";
			if (!($statement = $db->prepare($query))) {
				exit('Error query');
			}
			$statement->execute();
			$statement->bind_result($id, $email, $password, $description, $user_right);
			while ($statement->fetch()) {
				if (password_verify($password_p, $password)){
					$_SESSION['id'] = intval($id);
					$_SESSION['user'] = $email;
					$_SESSION['description'] = $description;
					$_SESSION['user_right'] = ($user_right == 0) ? 'user' : 'admin';
					$_SESSION['password'] = $password_p;
				}
			}
			$statement->close();

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

	?>
	<div class="autorization-form">
		<form method="post">
			<div class="card">
				<div class="card-body">
					<div class="card-title text-center phone">
						<i class="fa fa-user-circle" aria-hidden="true"></i> Авторизация
					</div>
				<div class="mb-3">
					<label for="inputEmail" class="form-label">Логин</label>
					<input type="text" value="<?=isset($_POST["email"]) ? $_POST["email"] : ''?>" name="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" placeholder="Введите e-mail или номер телефона" required autofocus>
				</div>
				<div class="mb-3">
					<label for="inputPassword" class="form-label">Пароль</label>
					<input type="password" name="password" class="form-control" id="inputPassword" autocomplete="current-password" aria-describedby="passHelp" placeholder="Введите пароль" required>
					<div id="passHelp" class="form-text"><a href="/?recover">Забыли пароль?</a></div>
				</div>
					<?=(!isset($_SESSION['user_right']) && isset($email_p)) ? '<div class="my-2"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Не верный логин или пароль!</div></div>' : ''?>
				<div class="mb-3 text-center">
					<span class="color_grey">
						Нажимая кнопку «Войти», вы принимаете условия
						<a href="/?agreement" target="_blank">пользовательского соглашения</a>
					</span>
				</div>

				<div class="mb-3 text-center">
					<button class="btn btn-dark" id="register">
						<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Регистрация
					</button>
					<button class="btn btn-dark" type="submit">
						<i class="fa fa-sign-in" aria-hidden="true"></i> Войти
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

