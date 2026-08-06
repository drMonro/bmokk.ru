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

	function record_history($pribor, $pokazania){
		//Запишим строку в таблицу истории показаний
		$id = NULL;
		global $db;
		$query = "INSERT INTO pokazania_history (id, pribor, pokazan) VALUES (?,?,?)";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (INSERT)');
		}
		$statement->bind_param("iis", $id, $pribor, $pokazania);
		$statement->execute();
		$statement->close();
		record_log(1, "(Пользователь {$_SESSION['id']}) Запись истории показаний.");
	}

$ls = isset($_GET['ls']) ? intval($_GET['ls']) : 0;
	$content = get_content('ls');

	// опеделяем какие месяцы загружены
		$query = "SELECT id, file_name, month FROM month_list ORDER BY id DESC LIMIT 15";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (select)');
		}
		$statement->execute();
		$statement->bind_result($id, $file_name, $month);
		$month_list = [];
		$i = 0;
		while ($statement->fetch()) {
			$month_list[$i]['id'] = $id;
			$month_list[$i]['file_name'] = $file_name;
			$month_list[$i]['month'] = $month;
			$i++;
		}
		$statement->close();

	// Пишем новые показания
		if (isset($_POST['pribor_n']) && isset($_POST['pokazania']) && (int)$_POST['pokazania'] != 0){
			$prb_is_true = false;
			/*$pribor = intval($_POST['pribor_n']);*/
                        $pribor = $_POST['pribor_n'];
			$pokazania = substr($_POST['pokazania'], 0, 32);

			// проверяем если на прибор уже есть показания то их обновляем, если нет то добавляем
			$query = "SELECT pokazan FROM pokazania WHERE pribor={$pribor}";
			if (!($statement = $db->prepare($query))) {
				exit('Ошибка запроса (select)');
			}
			$statement->execute();
			$statement->bind_result($pokazan);
			while ($statement->fetch()) {
				$prb_is_true = true;
			}
			$statement->close();

			if ($prb_is_true == true) {
                            $pokazania = str_replace('.','', str_replace(',','',trim($pokazania)));
                            $pokazan_tmp = ltrim($pokazan, '0'); // убираем 0 слева предыдущие показания
                            $pokazania_tmp = ltrim($pokazania, '0'); // убираем 0 слева которые передают
                            $err_msg_print = (is_numeric($pokazania) && strlen($pokazania) == 8) ? true : false;
                            
				// показания у прибора есть их нужно обновить
				if ( (
                                        floatval(str_replace(',','.',$pokazania_tmp)) > floatval(str_replace(',','.',$pokazan_tmp))
                                        || floatval(str_replace(',','.',$pokazania_tmp)) == floatval(str_replace(',','.',$pokazan_tmp))
                                     )
                                     && $err_msg_print){
                                    
                                //if (isset($pokazan)){
					$query = "UPDATE pokazania SET pokazan='".$pokazania."', visibility=1, record_date=current_timestamp() WHERE pribor={$pribor}";
					if (!($statement = $db->prepare($query))) {
						exit('Ошибка запроса (UPDATE)');
					}
					$statement->execute();
					$statement->close();
					record_history($pribor, $pokazania);
					js("$.notification.show('success','Показания приняты в обработку.')");
					record_log(1, "(Пользователь {$_SESSION['id']}) Обновление показаний. {$pokazania}");
				} else {
                                        $err_msg_print = ($err_msg_print) ? 'Проверьте правильность показаний!' : 'Введите 8 цифр без точек и запятых! Включая 0 спереди.';
                                        js("$.notification.show('error','{$err_msg_print}')");
				}
			} elseif ($prb_is_true == false) {
				// показания у прибора нет их нужно добавить
				if (floatval($pokazania) >= 0) {
					$id = NULL;
					$pokazania = str_replace(',','.',$pokazania);
					$query = "INSERT INTO pokazania (id, pokazan, pribor) VALUES (?,?,?)";
					if (!($statement = $db->prepare($query))) {
						exit('Ошибка запроса (INSERT)');
					}
					$statement->bind_param("isi", $id, $pokazania, $pribor);
					$statement->execute();
					$statement->close();
					record_history($pribor, $pokazania);
					unset($prb_is_true);
					js("$.notification.show('success','Начальные показания приняты в обработку.')");
					record_log(1, "(Пользователь {$_SESSION['id']}) Начальные показания приняты в обработку.");
				}
			}
		}
        // Если переданные показания = 0 то игнорим
            if( isset($_POST['pokazania']) && (int)$_POST['pokazania'] == 0){
                js("$.notification.show('error','Нельзя передать нулевые показания. Если показания те же, то передайте последние.')");
            }
	// Добавляем прибор учета
		if (isset($_POST['pribor'])){
			$prb = intval($_POST['pribor']);
			if (strlen($prb) < 16) {
				/* Проверим есть ли такой прибор учета */
				$error = false;
				$query = "SELECT id FROM pribory WHERE pribor={$prb}";
				if (!($statement = $db->prepare($query))) {
						exit('Ошибка запроса (SELECT)');
					}
				$statement->execute();
				$statement->bind_result($id);
				while ($statement->fetch()) {
					$error = true;
					js("$.notification.show('error','Прибор учета с таким номером уже привязан!')");
                }
				$statement->close();

				if (!$error) {
					/* Такого прибора еще нет - добавим */
					$id = NULL;
					$query = "INSERT INTO pribory (id, 	ls, pribor) VALUES (?,?,?)";
					if (!($statement = $db->prepare($query))) {
						exit('Ошибка запроса (INSERT)');
					}
					$statement->bind_param("iii", $id, $ls, $prb);
					$statement->execute();
					$statement->close();
					js("$.notification.show('success','Прибор учета № {$prb} успешно добавлен.')");
					record_log(1, "(Пользователь {$_SESSION['id']}) Прибор учета № {$prb} успешно добавлен.");
				}
			} else {
				js("$.notification.show('error','Номер не должен быть более 16 знаков!')");
			}

			}

	// Считываем все приборы учета
		$query = "SELECT id, pribor FROM pribory WHERE ls={$ls}";
		if (!($statement = $db->prepare($query))) {
			exit('Ошибка запроса (select)');
		}
		$statement->execute();
		$statement->bind_result($id, $pribor);
		$pribory = [];
		$i = 0;
		while ($statement->fetch()) {
			$pribory[$i]['id'] = $id;
			$pribory[$i]['pribor'] = $pribor;
			$pribory[$i]['pokazan'] = 0;
			$pribory[$i]['visibility'] = 1;
			$i++;
		}
		$statement->close();

		// Читаем текущие показания счетчика
		foreach ($pribory as $key => $value) {
			$query = "SELECT pokazan, visibility FROM pokazania WHERE pribor={$value['pribor']}";
			if (!($statement = $db->prepare($query))) {
				exit('Ошибка запроса (select_p)');
			}
			$statement->execute();
			$statement->bind_result($pokazan, $visibility);
			while ($statement->fetch()) {
				$pribory[$key]['pokazan'] = $pokazan;
				$pribory[$key]['visibility'] = $visibility;
			}
			$statement->close();
		}


?>
<div class="col-12">
	<h2>Счет № <?=$ls?></h2>
	<input id="ls" hidden="true" value="<?=$ls?>">

	<div>
		<?='<b>Адрес: </b>'.$_SESSION['address'.$ls].(strpos($_SESSION['address'.$ls], ", кв. ") ? '' : ', кв. '.$_SESSION['kvartira'.$ls])?>
	</div>
	<div class="row my-2">
		<div class="col-sm-4">
			<select class="form-control" id="month_select">
				<option value="0" selected>Необходимо выбрать месяц</option>
				<?
				foreach ($month_list as $m) {
					echo '<option value="'.$m['file_name'].'">'.get_date($m['month']).'</option>';
				}
				?>
			</select>

			<?
				foreach ($pribory as $p) {
			?>
				<div class="mb-3 my-2">
					<form method="post" class="my-4">
						<input name="pribor_n" hidden="true" value="<?=$p['pribor']?>">
						<div class="card">
							<div class="card-body">
								<div class="card-title text-center phone">
									<i class="fa fa-calendar" aria-hidden="true"></i> № <?=$p['pribor']?>
								</div>
							<div class="mb-3">
								<label class="form-label">Введите показания прибора учета</label>                                                              
								<?
									// Провверяем текущее число для возможности вносить изменения
									$dtt = intval(date("d"));
									//echo $dtt.' '.$p['visibility'];
									if (($dtt > 14 && $dtt < 26) ){
										$p['visibility'] = true;
									} elseif (($dtt < 15 || $dtt > 25) || $p['visibility'] == 0) {
										$p['visibility'] = false;
									}
                                                                        //echo $dtt.' '.$p['visibility'];
								?>
								<input type="text" value="<?=$p['pokazan']?>" name="pokazania" class="form-control" placeholder="Введите показания" required <?=$p['visibility'] ? '':'disabled'?>>
							</div>

							<div class="mb-3 text-center">
                                                                <span class="color_red">
                                                                    Обратите внимание, передавать сейчас необходимо все <b>8 цифр</b>, включая 0. Например если показания на счетчике 0013964.1 то необходимо ввести 00139641 в качестве новых показаний.
                                                                </span>
                                                                <br>
								<span class="color_grey">
									<b>ВНИМАНИЕ!</b> Передача показаний доступна <b>ТОЛЬКО</b> с 15 по 25 число месяца. Вы их можете изменить если они еще не приняты диспетчером.
								</span>
							</div>
								<?=$p['visibility'] ? '':'<div class="my-2"><div class="alert alert-primary" role="alert"><i class="fa fa-exclamation-circle"></i> Внимание! данные приняты диспетчером их можно изменить с 15 по 25 число следующего месяца</div></div>'?>
							<div class="mb-3 text-center">
								<button class="btn btn-dark" type="submit" <?=$p['visibility'] ? '':'disabled'?>>
									<i class="fa fa-exchange" aria-hidden="true"></i> Передать
								</button>
							</div>
							<div class="mb-3 text-center">
								<!-- История передачи показаний  начало -->
								<?
									// читаем историю передачи показаний
									$query = "SELECT pokazan,record_date FROM pokazania_history WHERE pribor={$p['pribor']} ORDER BY record_date DESC LIMIT 15";
									if (!($statement = $db->prepare($query))) {
										exit('Ошибка запроса (select)');
									}
									$statement->execute();
									$statement->bind_result($hpokazan,$hrecord_date);
									$pokazania_history = [];
									$i = 0;
									while ($statement->fetch()) {
										$pokazania_history[$i]['pokazan'] = $hpokazan;
										$pokazania_history[$i]['record_date'] = $hrecord_date;
										$i++;
									}
									$statement->close();

								?>

								<?
									if ($pokazania_history !== []){
								?>
										<table class="table table-hover">
											<thead>
											  <tr>
												<th scope="col">Переданные показания</th>
												<th scope="col">Дата</th>
											  </tr>
											</thead>
											<tbody>
												<?
													foreach ($pokazania_history as $ph) {
												?>
														<tr>
															<td><?=$ph['pokazan']?></td>
															<td><?=date("d.m.Y",strtotime($ph['record_date']))?></td>
														</tr>
												<?
													}
												?>
											</tbody>
										</table>
								<?
									}
								?>
								<!-- История передачи показаний  конец -->
							</div>
							</div>
						</div>
					</form>
				</div>

			<? } ?>



			<div class="mb-3 my-2">
				<div class="card">
					<div class="card-body">
						<div class="card-title text-center phone">
							<i class="fa fa-tachometer" aria-hidden="true"></i> Приборы учета
						</div>
						<form method="post">
							<div class="mb-3">
								<input type="text" value="" name="pribor" id="pribor" class="form-control" placeholder="Введите заводской номер" required>
							</div>
							<div class="mb-3 text-center">
								<button class="btn btn-dark" type="submit">
									<i class="fa fa-plus-square" aria-hidden="true"></i> Добавить прибор учета
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-8">
			<!-- Начисления -->
			<span id="ls-description" class="phone"></span>
			<div id="for_print">
				<div id="ls-description-head"></div>
			</div>
		</div>
	</div>
</div>

<script>
	// Изменение select
	$('#month_select').on('change', function() {
		let mn = this.value,
			l = $('#ls').val();

			if (mn == 0) {
				$('#ls-description').html('');
				$('#ls-description-head').html('');
				//$('#ls-description-btn').attr('hidden', true);
				//$('#ls-description-rekvizit').attr('hidden', true);
				return false;
			}

			$.post('/ajax/get_ls_description.php', {mn: mn, l: l},
			function(result){
					$('#ls-description').html(result);
					$('#ls-description-head').html(
							'<div class="card my-2"><div class="card-body">'+
							'<table style="width: 100%;" class="table table-bordered my-2"><tr><td>Лицевой счет</td><td>Месяц, год</td><td>Сумма к оплате</td>'+
							'<tr><td><b>л/с № '+l+'</b></td><td><b>'+$('#month_select option:selected').html()+'</b></td><td><b>'+$('#summa').html()+'</b></td>'+
							'<table>'+
							'<div class="my-2"><?=$content[0]["description"]?></div>'+
							'<div class="my-2 text-center"><span class="btn btn-dark" onclick="printPageArea(\'for_print\')"><i class="fa fa-print"></i> Распечатать</span></div>'+
							'</div></div>'
					);
					//$('#ls-description-btn').attr('hidden', false);
					//$('#ls-description-rekvizit').attr('hidden', false);

				}
		);
	});

	// Печать части страницы
	function printPageArea(areaID){
		let printContent = document.getElementById(areaID);
		let WinPrint = window.open('', '', 'width=900,height=650');
		WinPrint.document.write(printContent.innerHTML);
		WinPrint.document.close();
		WinPrint.focus();
		WinPrint.print();
		WinPrint.close();
	}


	// Ввод только цифр
	$('input').on('input', function(){
		var value = replace(/[^0-9.]/g, '');
                this.value = value;
                return;
		if (value < 1) {
			this.value = '';
		} else {
			this.value = value;
		}
	});
</script>