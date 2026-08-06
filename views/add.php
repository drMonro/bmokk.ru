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

	if (isset($_POST["accounting"]) && isset($_SESSION['id'])){
		/* Тут проверяем и добавляем новый счет */
		include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');
		$accounting = intval($_POST["accounting"]);
		$query = "SELECT ls FROM sberbank WHERE ls={$accounting}";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (select)');
		}
		$statement->execute();
		$statement->bind_result($ls);
		$is_add = TRUE;
		if (!isset($ls)){
			js("$.notification.show('error','Такой счет не существует или он уже привязан к аккаунту пользователя!')");
		}
		while ($statement->fetch()) {
			/*Такой счет найден*/
				$statement->close();
				$query = "SELECT id FROM ls WHERE ls={$accounting}";
				if (!($statement = $db->prepare($query))) {
					exit('Ошибка запроса (select)');
				}
				$statement->execute();
				$statement->bind_result($id2);
				$statement->fetch();
				if (!isset($id2)){
					$statement->close();
					$id = NULL;
					$user_id = intval($_SESSION['id']);
					$query = "INSERT INTO ls (id, user_id, ls) VALUES (?,?,?)";
					if (!($statement2 = $db->prepare($query))) {
						exit('Ошибка запроса (INSERT)');
					}
					$statement2->bind_param("iii", $id, $user_id, $accounting);
					$statement2->execute();
					$statement2->close();
					$_SESSION['ls'] = $accounting;
					header('Location: /?user');
					exit;
				}
		}
		$statement->close();
	}
?>

<div class="col-12 py-2">
		<div class="autorization-form">
		<form method="post">
			<div class="card">
				<div class="card-body">
					<div class="card-title text-center phone">
						<i class="fa fa-address-card-o" aria-hidden="true"></i> Привязка лицевого счёта
					</div>
				<div class="mb-3">
					<label for="inputAccounting" class="form-label">Номер лицевого счёта</label>
					<input type="text" value="<?isset($_POST["accounting"]) ? $_POST["accounting"] : ''?>" name="accounting" class="form-control" id="inputAccounting" placeholder="Цифры из договора" required autofocus>
				</div>
					<?=isset($is_add) ? '<div class="my-2"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Такой счет не существует или он уже привязан к аккаунту пользователя!</div></div>' : ''?>
				<div class="mb-3 text-center">
					<button class="btn btn-dark" type="submit" id="cancel">
						<i class="fa fa-times" aria-hidden="true"></i> Отмена
					</button>
					<button class="btn btn-dark" type="submit">
						<i class="fa fa-plus-square" aria-hidden="true"></i> Привязать
					</button>
				</div>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="col-12 py-2 text-center">
	<img src="../image/add_help.jpeg" width="100%">
</div>

<script>
	// Отмена
	var myAnchor = document.getElementById("cancel");
	myAnchor.addEventListener("click", function(event) {
		event.preventDefault();
		window.location = '/?user';
	}, false);

	// Ввод только цифр
	$('#inputAccounting').on('input', function(){
		var value = this.value.replace(/[^0-9]/g, '');
		if (value < 1) {
			this.value = '';
		} else {
			this.value = value;
		}
	});

</script>
