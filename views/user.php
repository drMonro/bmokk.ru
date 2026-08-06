<?
	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'user') {
			header('Location: /?login');
			exit;
		}
	} else {
			header('Location: /');
			exit;
	}

	// Считываем все счета пользователя
		$query = "SELECT id,ls FROM ls WHERE user_id={$_SESSION['id']}";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (select)');
		}
		$statement->execute();
		$statement->bind_result($id,$ls);
		$_SESSION['user_ls'] = [];
		$i = 0;
		while ($statement->fetch()) {
			$_SESSION['user_ls'][$i]['id'] = $id;
			$_SESSION['user_ls'][$i]['ls'] = $ls;
			$i++;
		}
		$statement->close();

		//Изменяем информацию пользователя
		if (isset($_POST['input_desc']) && $_POST['input_desc'] !== $_SESSION['description']) {
					$t = trim($_POST['input_desc']);
					$query = "UPDATE users SET description='{$t}' WHERE id={$_SESSION['id']}";
					if (!($statement = $db->prepare($query))) {
						exit('Ошибка запроса (set_description)');
					}
					$statement->execute();
					$statement->close();

					$_SESSION['description'] = $_POST['input_desc'];
					js("$.notification.show('success','Контактная информация успешно изменена.')");
					record_log(1, "(Пользователь {$_SESSION['id']}) Контактная информация успешно изменена.");
		}

		// Изменяем пароль пользователя
		if (isset($_POST['old_pass']) && isset($_POST['new_pass']) && isset($_POST['new_pass_two'])
				&& $_POST['old_pass'] === $_SESSION['password']
				&& $_POST['new_pass'] === $_POST['new_pass_two']) {
					if ($_POST['old_pass'] === $_POST['new_pass']){
						js("$.notification.show('error','Старый и новый пароли совпадают!')");
					} else {
						$pass = password_hash($_POST['new_pass'], PASSWORD_BCRYPT);
						$query = "UPDATE users SET password='{$pass}' WHERE id={$_SESSION['id']}";
						if (!($statementx = $db->prepare($query))) {
							exit('Error query update');
						}
						$statementx->execute();
						$statementx->close();
						js("$.notification.show('success','Пароль успешно изменен.')");
						record_log(1, "(Пользователь {$_SESSION['id']}) Пароль успешно изменен.");
					}
		}

?>

<div class="col-12 py-2">
	<nav>
	  <div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><i class="fa fa-address-card-o" aria-hidden="true"></i> Лицевые счета</a>
		<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"><i class="fa fa-user-circle" aria-hidden="true"></i> Профиль</a>
	  </div>
	</nav>

	<div class="tab-content" id="nav-tabContent">
		<!-- Лицевые счета -->
		<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
			<div class="row">
				<div class="col-12 my-2">
					<table style="width: 100%;">
						<tr>
							<td style="width: 40%; text-align: left">
								<span class="logo color_grey">Всего лицевых счётов: <?=isset($_SESSION['user_ls']) ? count($_SESSION['user_ls']) : 0?></span>
							</td>
							<td style="text-align: right">
									<button class="btn btn-dark" id="register">
										<i class="fa fa-plus-square" aria-hidden="true" title = "Привязать лицевой счет"></i>
										<span class="href-description">
											Привязать лицевой счет
										</span>
									</button>
							</td>
						</tr>
					</table>
				</div>
				<div class="col-12 my-2">
					<?
					// Выводим все счета
					foreach ($_SESSION['user_ls'] as $ls){
					?>
							<div class="card news">
								<div class="card-body">
									<div class="card-title phone">
										Счет № <?=$ls['ls']?>
									</div>
									<div class="mb-3">
										<?
										// Заполним информацию о счете
												$query = "SELECT kvartira,address FROM sberbank WHERE ls={$ls['ls']} LIMIT 1";
											if (!($statement = $db->prepare($query))) {
												exit('Ошибка запроса (select)');
											}
											$statement->execute();
											$statement->bind_result($kvartira,$address);
											while ($statement->fetch()) {
												echo $address.(strpos($address, ", кв. ") ? '' : '<br>Квартира: '.$kvartira);
												$_SESSION['address'.$ls['ls']] = $address;
												$_SESSION['kvartira'.$ls['ls']] = $kvartira;
												?>
												<a href="/?ls&ls=<?=$ls['ls']?>" target='_blank'>
													<span class="btn btn-dark ls-btn" id="">
														<i class="fa fa-eye" aria-hidden="true" title = "Подробности счета"></i>
														<span class="href-description">
															Подробности
														</span>
													</span>
												</a>
												<?
											}
											$statement->close();
										?>
									</div>
								</div>
							</div>
					<?
					}
					?>
				</div>
			</div>
		</div>

		<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
			<!-- Профиль -->
			<div class="row">
				<div class="col my-2 text-left">
					<form method="POST">
							<div class="card">
								<div class="card-body">
									<div class="card-title phone text-center">
										<i class="fa fa-user-circle" aria-hidden="true"></i> <?=$_SESSION['user'];?>
									</div>
									<div class="mb-3 my-2">
										<label for="input_desc" class="form-label">Дополнительная информация</label>
										<input type="text" value="<?=$_SESSION['description']?>" name="input_desc" class="form-control" id="input_desc" placeholder="Можете добавить дополнительную информацию для связи Ф.И.О., телефон, дополнительный e-mail и т.д.">
									</div>
									<div class="mb-3 my-2">
										<label for="old_pass" class="form-label">Изменение пароля</label>
										<input type="password" value="" name="old_pass" class="form-control" id="old_pass" placeholder="Текущий пароль">
									</div>
									<div class="mb-3 my-2">
										<input type="password" value="" name="new_pass" class="form-control" placeholder="Новый пароль">
									</div>
									<div class="mb-3 my-2">
										<input type="password" value="" name="new_pass_two" class="form-control" placeholder="Повторите новый пароль">
									</div>
									<div class="mb-3 text-center my-2">
										<button class="btn btn-dark" name="" value="">
											<i class="fa fa-floppy-o" aria-hidden="true" title = "Сохранить изменения"></i>
												Сохранить изменения
										</button>
									</div>
								</div>
							</div>
					</form>
				</div>
			</div>

		</div>
	</div>
</div>

<script>
	// Регистрация
	var myAnchor = document.getElementById("register");
	myAnchor.addEventListener("click", function(event) {
		event.preventDefault();
		window.location = '/?add';
	}, false);
</script>