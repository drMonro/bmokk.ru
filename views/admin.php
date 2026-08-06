<?
	if (isset($_SESSION['user_right'])) {
		if ($_SESSION['user_right'] != 'admin') {
			header('Location: /?login');
			exit;
		}
	} else {
			header('Location: /');
			exit;
	}

	/* Контент на страницах сайта */
		$query = "SELECT id, page, name FROM contents";
		if (!($statement = $db->prepare($query))) {
			exit('Error query');
		}
		$statement->execute();
		$statement->bind_result($id, $page, $name);

		$content = [];
		$i = 0;

		while ($statement->fetch()) {
			$content[$i]['id'] = $id;
			$content[$i]['page'] = $page;
			$content[$i]['name'] = $name;
			$i++;
		}
		$statement->close();

	/* Новости */
		$query = "SELECT id, title, visibility, date_new FROM news ORDER BY id DESC LIMIT 20";
		if (!($statement = $db->prepare($query))) {
			exit('Error query');
		}
		$statement->execute();
		$statement->bind_result($id, $title, $visibility, $date_new);

		$news = [];
		$i = 0;

		while ($statement->fetch()) {
			$news[$i]['id'] = $id;
			$news[$i]['title'] = $title;
			$news[$i]['visibility'] = $visibility;
			$news[$i]['date'] = $date_new;
			$i++;
		}
		$statement->close();

	/* Количество выгрузок в upload */
		$filelist = glob($_SERVER['DOCUMENT_ROOT'].'/upload/*.txt', GLOB_NOSORT);

	/* Загружен ли файл */
		$is_load = [];
		foreach ($filelist as $f){
			$query = "SELECT file_n FROM sberbank WHERE file_n LIKE '%".basename($f)."' LIMIT 1";
			if (!($statement = $db->prepare($query))) {
				exit('Ошибка запроса (select)');
			}
			$statement->execute();
			$statement->bind_result($file_n);
			while ($statement->fetch()) {
				$is_load[] = $file_n;
			}
			$statement->close();
		}

?>

<div class="col-12 py-2">
	<nav>
	  <div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link <?= (!isset($_GET['nav_news']) && !isset($_GET['nav_import'])) ? 'active' : ''?>" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><i class="fa fa-address-card-o" aria-hidden="true"></i> Показания</a>
		<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-content" role="tab" aria-controls="nav-content" aria-selected="false"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Контент</a>
		<a class="nav-item nav-link <?= isset($_GET['nav_news']) ? 'active' : ''?>" id="nav-profile-tab" data-toggle="tab" href="#nav-news" role="tab" aria-controls="nav-news" aria-selected="false"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Новости</a>
		<a class="nav-item nav-link <?= isset($_GET['nav_import']) ? 'active' : ''?>" id="nav-profile-tab" data-toggle="tab" href="#nav-import" role="tab" aria-controls="nav-import" aria-selected="false"><i class="fa fa-exchange" aria-hidden="true"></i> Импорт данных</a>
		<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-data" role="tab" aria-controls="nav-data" aria-selected="false"><i class="fa fa-table" aria-hidden="true"></i> Данные в выгрузках</a>
	  </div>
	</nav>

	<div class="tab-content" id="nav-tabContent">

		<!-- Лицевые счета -->
		<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
			<div class="row">
				<div class="col-12 my-2">
						<table style="width: 100%;" class="my-2">
							<tr>
								<td style="width: 200px; text-align: left">
									<span class="logo color_grey">Показывать записей</span>
										<select class="form-control" id="user_record_select">
											<option value="50" selected>50</option>
											<option value="100">100</option>
											<option value="200">200</option>
											<option value="1000">1000</option>
										</select>
								</td>
								<!--
								<td style="text-align: left">
									<span class="logo color_grey">Выберите месяц</span>
									<select class="form-control" id="user_month_select">
										<option value="0" selected>Необходимо выбрать файл выгрузки</option>
										<?
										foreach ($filelist as $f) {
											echo '<option value="'.basename($f).'">'.basename($f).'</option>';
										}
										?>
									</select>
								</td>
								-->
								<td>&nbsp;</td>
								<td style="text-align: left;">
										<span class="logo color_grey">Поиск по полям: л/с, почта</span>
										<input type="text" value="<?=isset($_POST["search_txt"]) ? $_POST["search_txt"] : ''?>" name="search_txt" class="form-control" id="user_search_txt_id" placeholder="Строка для поиска" style="background: #fff !important;">
								</td>
								<td style="text-align: right">
									&nbsp;
									<span class="btn btn-dark" onclick="users()">
										<i class="fa fa-refresh" aria-hidden="true"></i>
										<span class="href-description" id="btn_see">Показать список</span>
									</span>
								</td>
							</tr>
						</table>

						<span id="users_table"></span>

				</div>
			</div>
		</div>

		<!-- Контент на страницах -->
		<div class="tab-pane fade" id="nav-content" role="tabpanel" aria-labelledby="nav-profile-tab">
			<div class="row">
				<div class="col-12 my-2">
					<span class="logo color_grey">Разделы сайта</span>
					<select class="form-control" id="content_desc">
						<option value="0" selected>Необходимо выбрать область</option>
						<?
						foreach ($content as $c) {
							echo '<option value="'.$c['id'].'">'.$c['name'].' ('.$c['page'].')</option>';
						}
						?>
					</select>
				</div>
				<div class="col-12 my-2 text-left">
					<div contenteditable="true" id="edit_content" title="Введите описание">
					</div>
					<div class="mb-3 text-center my-2" id="save_btn">
					</div>
				</div>

				<div class="col-12 my-2 text-left color_grey">
					<?
						echo accordion("accordionExample","Основные теги для форматирование текста (как памятка).",include_once ($_SERVER['DOCUMENT_ROOT'].'/views/tips.php'));
					?>
				</div>

			</div>
		</div>

		<!-- Новости -->
		<div class="tab-pane fade" id="nav-news" role="tabpanel" aria-labelledby="nav-profile-tab">
			<div class="row">
				<div class="col my-2 text-left">
					<table style="width: 100%;" class="my-2">
						<tr>
							<td style="width: 40%; text-align: left">
								<span class="logo color_grey">Всего новостей: <?=count($news)?></span>
							</td>
							<td style="text-align: right">
								<span class="btn btn-dark" onclick="add_new()">
										<i class="fa fa-plus-square" aria-hidden="true" title = "Добавить новость"></i>
										<span class="href-description">
											Добавить новость
										</span>
									</span>
							</td>
						</tr>
					</table>

					<div class="color_grey" id="add_new_title"></div>
					<div id="add_new_description"></div>
					<div class="mb-3 text-center my-2">
						<span id="add_new_btn"></span>
					</div>

					<table class="table table-hover">
						<thead>
						  <tr>
							<th scope="col" style="width: 100px;">Дата</th>
							<th scope="col">Заголовок</th>
							<th scope="col">Действие</th>
						  </tr>
						</thead>
						<tbody>
							<?
								foreach ($news as $n) {
							?>
									<tr id="tr<?=$n['id']?>">
										<td><?=$n['date']?></td>
										<td>
											<div id="new_title_<?=$n['id']?>"><?=$n['title']?></div>
											<div id="edit_new_description_<?=$n['id']?>"></div>
											<div class="mb-3 text-center my-2" id="save_new_description_btn_<?=$n['id']?>"></div>
										</td>
										<td>
											<span class="actions" onclick="change_new_visibility('<?=$n['id']?>')" id="act_chg_<?=$n['id']?>" data-val="<?=$n['visibility']?>"><i class="fa fa-toggle-<?=$n['visibility'] ? 'on' : 'off'?> fa-2x" aria-hidden="true" title="Видимость (<?=$n['visibility'] ? 'включено' : 'отключено'?>)"></i></span>
											<span class="actions" onclick="get_new_description('<?=$n['id']?>')"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true" title="Редактировать"></i></span>
											<span class="actions" onclick="delete_new('<?=$n['id']?>')"><i class="fa fa-trash fa-2x" aria-hidden="true" title="Удалить"></i></span>
										</td>
									</tr>
							<?
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Импорт данных -->
		<div class="tab-pane fade show" id="nav-import" role="tabpanel" aria-labelledby="nav-home-tab">
			<div class="row">
				<div class="col-12 my-2">
					<div class="mb-3 text-left my-2">
						<span class="logo color_grey">Всего выгрузок: <?=count($filelist)?></span>

						<div id="locker" hidden="true">
							<div class="locker"></div>
							<div class="cw">
								<h2>
									<i class="fa fa-spinner fa-spin fa-fw"></i>
									Ожидайте. Идет импорт...
								</h2>
							</div>
						</div>

						<table class="table table-hover">
							<thead>
							  <tr>
								<th scope="col" style="width: 200px;">Дата</th>
								<th scope="col">Имя файла</th>
								<th scope="col">Статус</th>
								<th scope="col"></th>
							  </tr>
							</thead>
							<tbody>
								<?
									$i = 0;
									foreach ($filelist as $f) {
								?>
										<tr>
											<td><?=date("F d Y H:i:s", filectime($f))?></td>
											<td><?=basename($f)?></td>
											<td id="tdl_<?=$i?>"><?=in_array(basename($f), $is_load) ? 'Загружен' : 'Не загружен'?></td>
											<td>
												<div class="mb-3 text-right">
													<?
														if (!in_array(basename($f), $is_load)){
													?>
														<span class="btn btn-dark" onclick="import_bd('<?=basename($f)?>','<?=$i?>')" id="imp_<?=$i?>">
															<i class="fa fa-database" aria-hidden="true"></i> Импортировать содержимое файла в БД
														</span>
													<?
														} else {
													?>
														<span class="btn btn-dark">
															<i class="fa fa-database" aria-hidden="true"></i> Файл импортирован в БД
														</span>
													<?
														}
													?>
												</div>
											</td>
										<tr>
								<?
										$i++;
									}
								?>
							</tbody>
						</table>
					</div>

					<?
						if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload' && $_FILES !== []) {
							// проверяем, что загрузка файла прошла успешно.
							if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
								$fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
								// Задаем новое имя файла месяц_год.txt
								$dt = date('F_Y');
								$newFileName =$dt.".txt";
								// Перемещаем файл
								$uploadFileDir = $_SERVER['DOCUMENT_ROOT'].'/upload/';
								$dest_path = $uploadFileDir . $newFileName;
								if(move_uploaded_file($fileTmpPath, $dest_path))
								{
								  $message ='Файл успешно загружен.';
								  $file = mb_convert_encoding(file_get_contents($dest_path), "UTF-8","Windows-1251");
								  file_put_contents($dest_path, $file);
								}
								else
								{
								  $message = 'Файл не загружен!';
								}
								echo $message;
							} else {
								$message = 'Ошибка: '.$_FILES['uploadedFile']['error'].'!';
							}
							?>
								<div class="my-2">
									<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <?=$message?></div>
								</div>
							<?
						}
					?>
					<div class="mb-3 text-center my-2">
						<form enctype="multipart/form-data" method="POST">
							<!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
							<input type="hidden" name="MAX_FILE_SIZE" value="1999999" />
							<!-- Название элемента input определяет имя в массиве $_FILES -->
							<input name="uploadedFile" type="file"/>
							<div class="mb-3 text-center my-2">
								<button class="btn btn-dark" name="uploadBtn" value="Upload">
									<i class="fa fa-download" aria-hidden="true"></i> Загрузить новый файл (выгрузка для Сбера)
								</button>
							</div>
						</form>
					</div>
					<div class="mb-3 text-center my-2">
						<span class="color_grey">* В одном месяце может быть только один файл выгрузки сбера. Загружать их можно не ограниченное количество. Будет сохранять только последний (остальные удаляются). Имя файла он создает автоматически в зависимости от того какой сейчас месяц и год. <b>ВАЖНО:</b> После загрузки данные НЕ записываются в БД. Что бы перенести данные из файла в базу данных нужно нажать кнопку "Импортировать содержимое файла в БД" справа от имени файла.</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Данные в выгрузках -->
		<div class="tab-pane fade show" id="nav-data" role="tabpanel" aria-labelledby="nav-home-tab">
			<div class="row">
				<div class="col-12 my-2">

						<table style="width: 100%;" class="my-2">
							<tr>
								<td style="width: 200px; text-align: left">
									<span class="logo color_grey">Показывать записей</span>
										<select class="form-control" id="record_select">
											<option value="50" selected>50</option>
											<option value="100">100</option>
											<option value="200">200</option>
										</select>
								</td>
								<td style="text-align: left">
									<span class="logo color_grey">Выберите месяц</span>
									<select class="form-control" id="month_select">
										<option value="0" selected>Необходимо выбрать файл выгрузки</option>
										<?
										foreach ($filelist as $f) {
											echo '<option value="'.basename($f).'">'.basename($f).'</option>';
										}
										?>
									</select>
								</td>
								<td style="text-align: right">
									<span class="logo color_grey">Поиск по полям: л/с, адрес, сумма, ФИО</span>
									<input type="text" value="<?=isset($_POST["search_txt"]) ? $_POST["search_txt"] : ''?>" name="search_txt" class="form-control" id="search_txt_id" placeholder="Строка для поиска" style="background: #fff !important;">
								</td>
							</tr>
						</table>

						<span id="sber_table"></span>

				</div>
			</div>
		</div>

	</div>
</div>

		<!-- HTML-код модального окна ОТПРАВИТЬ начало-->
		<div id="modal-send" tabindex="-1" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<div class="card-title text-center phone">
						<i class="fa fa-envelope" aria-hidden="true"></i> Сообщение для <span id="send_user"></span>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Введите сообщение</label>
							<textarea id="suser" class="form-control" rows="8" required style="background: #f4f4f5 !important;"></textarea>
						</div>
						<div class="mb-3 text-center">
							<span class="btn btn-dark" onclick="send_mail();">
								<input id="btn_email" hidden="true" value="">
								<i class="fa fa-envelope" aria-hidden="true"></i> Отправить
							</span>
						</div>
				</div>
			</div>
		  </div>
		</div>

		<!-- HTML-код модального окна ОТПРАВИТЬ начало-->
		<div id="modal-change" tabindex="-1" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<div class="card-title text-center phone">
						<i class="fa fa-calendar" aria-hidden="true"></i> Изменение показаний для прибора учета № <span id="pu"></span>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Введите новые показания</label>
							<input id="pk" class="form-control">
						</div>
						<div class="mb-3 text-center">
							<span class="btn btn-dark" onclick="change_pk()">
								<input id="btn_email" hidden="true" value="">
								<i class="fa fa-floppy-o"></i> Сохранить
							</span>
						</div>
				</div>
			</div>
		  </div>
		</div>

<script>
	// Изменение select
	$('#content_desc').on('change', function() {
		let id = this.value;

			if (id == 0) {
				$('#edit_content').html('');
				$('#save_btn').html('');
				return false;
			}
			ajax("/ajax/get_description.php?id="+id);
	});

	// Сохранить описание
	function save_change_click() {
		let id = $('#content_desc').val(),
			desc = $('#edit_content').html();

		if (desc.length < 1 || id < 1) {
			return false;
		}

		$.post('/ajax/set_description.php', {id: id, desc: desc},
			function(result){
				if (result == 777) {
					$('#edit_content').html('Успешно сохранено!');
					$('#save_btn').html('');
					$('#content_desc').val(0);
				} else {
						alert('Не удалось сохранить запись!');
					}
				}
		);
	};

	// Изменить видимость новости
	function change_new_visibility(id) {
		let v = $('#act_chg_'+id).attr('data-val') == 1 ? 0 : 1;

		$.post('/ajax/news.php', {id: id, data: v, action: 'change_visibility'},
			function(result){
				if (result != 777) {
						alert('Не удалось сохранить запись!');
						return false;
					}
				}
		);

		$('#act_chg_'+id).html('<i class="fa fa-toggle-'+(v?'on':'off')+' fa-2x" aria-hidden="true" title="Видимость ('+(v?'включено':'отключено')+')"></i>');
		$('#act_chg_'+id).attr('data-val',v);
	}

	// Отмена редактирование новости
	function cancel_new_edit(id){
		$('#new_title_'+id).attr('contenteditable',false);
		$('#new_title_'+id).removeClass('color_grey');
		$('#edit_new_description_'+id).html('');
		$('#save_new_description_btn_'+id).html('');
	}

	// Получить описание новости
	function get_new_description(id) {
		var id2 = id;
		$.post('/ajax/news.php', {id: id, data: '0', action: 'get_description'},
			function(result){
				if (result == 777) {
						alert('Не удалось получить описание!');
						return false;
					} else {
						$('#edit_new_description_'+id2).html(result);
						$('#edit_new_description_'+id2).attr('contenteditable',true);
						$('#new_title_'+id2).attr('contenteditable',true);
						$('#new_title_'+id2).addClass('color_grey');
						$('#save_new_description_btn_'+id2).html('<button class="btn btn-dark my-1" onclick="cancel_new_edit('+id2+')"><i class="fa fa-times" aria-hidden="true"></i> Отмена</button> <button class="btn btn-dark" onclick="set_new_description('+id2+')"><i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить изменения</button>');
					}
				}
		);
	}
	// Записать новость в бд
		function set_new_description(id) {
		let d = $('#edit_new_description_'+id).html(),
			t = $('#new_title_'+id).html();
			$.post('/ajax/news.php', {id: id, data: d, title: t, action: 'set_description'},
				function(result){
					if (result != 777) {
							alert('Не удалось сохранить запись!');
							return false;
						}
					}
			);

			cancel_new_edit(id);
		}

		// Удалить новость
		function delete_new(id) {
			if (confirm("Вы действительно хотите удалить новость?")) {
				$.post('/ajax/news.php', {id: id, data: '0', action: 'delete'},
					function(result){
						if (result != 777) {
								alert('Не удалось удалить запись!');
								return false;
							}
						}
				);
				$("#tr"+id).remove();
			} else {
				return false;
			}

		}

		//Отмена добавления новости
		function cancel_add_new(){
			$('#add_new_title').html('');
			$('#add_new_title').attr('contenteditable',false);
			$('#add_new_description').html('');
			$('#add_new_description').attr('contenteditable',false);
			$('#add_new_btn').html('');
		}

		// Добавить новость
		function add_new() {
			$('#add_new_title').html('Введите заголовок новости...');
			$('#add_new_title').attr('contenteditable',true);
			$('#add_new_description').html('Введите описание новости...');
			$('#add_new_description').attr('contenteditable',true);
			$('#add_new_btn').html('<button class="btn btn-dark my-1" onclick="cancel_add_new()"><i class="fa fa-times" aria-hidden="true"></i> Отмена</button> <button class="btn btn-dark" onclick="save_add_new()"><i class="fa fa-floppy-o" aria-hidden="true"></i> Сохранить изменения</button>');
		}

		// Добавить новость в БД
		function save_add_new() {
		let d = $('#add_new_description').html(),
			t = $('#add_new_title').html();
	alert(d+t);

			$.post('/ajax/news.php', {id: null, data: d, title: t, action: 'add'},
				function(result){
					if (result != 777) {
							alert('Не удалось добавить запись!');
							return false;
						}
					}
			);
			window.location.href ='/?admin&nav_news=true';

		}

		// Импорт из файла данных в БД
		function import_bd(f, i){
			if (confirm("Вы действительно хотите записать информацию из файла:"+f+" в базу данных?")) {
				$('#locker').attr('hidden', false);
				$.post('/ajax/import.php', {f: f},
					function(result){
						$('#locker').attr('hidden', true);
						if (result != 777) {
								alert('Не удалось добавить записи!');
								$('#tdl_'+i).html('Не загружен');
								return false;
							} else {
								//window.location.href ='/?admin&nav_import=true';
								$('#tdl_'+i).html('Загружен');
							}
					}
				);
			} else {
				return false;
			}
		}

	// Изменение в данных о загрузках
	function sber (f, s){
		if ( f.length > 2) {
			let l = $('#record_select').val();

			$.ajax({
				url: '/ajax/sberbank.php',
				method: 'post',
				data: { f: f, s: s, l: l},
				dataType: 'html',
				async: false,
				success: function(result){
					$('#sber_table').html(result);
				}
			});

		} else {
			$('#sber_table').html('');
		}
	}

	$('#month_select').on('change', function() {
		let f = this.value,
			s = $('#search_txt_id').val();

			sber (f, s);

	});

	$('#record_select').on('change', function() {
		let f = $('#month_select').val(),
			s = $('#search_txt_id').val();

			sber (f, s);

	});

   $("#search_txt_id").keyup(function (e)
    {
		let s = $('#search_txt_id').val(),
			f = $('#month_select').val();

		if ( s.length > 1) {
			sber (f, s);
		}
    });

	// Таблица списка пользователей
	function users() {
		let l = $('#user_record_select').val(),
			s = $('#user_search_txt_id').val()
			/*m = $('#user_month_select').val()*/;
/*
		if (m == 0) {
			alert('Не выбран файл выгрузки!');
			return false;
		}
*/
			$.ajax({
				url: '/ajax/users.php',
				method: 'post',
				data: { s: s, l: l},
				dataType: 'html',
				async: false,
				success: function(result){
					$('#users_table').html(result);
				}
			});
	}

	// Изменение при приеме показаний вкл / выкл
	function change_pokazanie(pribor) {

		if($('#act_chg_pok_'+pribor).attr('data-val') == 0) {
			if (!confirm("Вы действительно хотите отменить принятые ранее показания?")){
				return false;
			}
		}

		let v = $('#act_chg_pok_'+pribor).attr('data-val') == 1 ? 0 : 1;

		$.post('/ajax/pokazanie.php', {pribor: pribor, data: v},
			function(result){
				if (result != 777) {
						alert('Не удалось изменить видимость');
						return false;
					}
				}
		);

		$('#act_chg_pok_'+pribor).html('<i class="fa fa-toggle-'+(v?'on':'off')+' fa-2x" aria-hidden="true" title="Показание '+(v?'не принято':'принято')+'"></i>');
		$('#act_chg_pok_'+pribor).attr('data-val',v);
	}

	// Отправка сообщения пользователю
	function send_mail(){
		let email = $('#btn_email').val(),
			message = $('#suser').val();
		if (message.length < 5) {
			alert('Сообщение слишком короткое!');
			return false;
		}
		$.post('/ajax/send_mail.php', {email: email, message: message},
			function(result){
				if (result != 777) {
						alert('Не удалось отправить письмо');
						return false;
					}
				}
		);
		$('#modal-send').modal('toggle');
		return true;
	}

	// При открытии модального окна определим кому пишем
	$('#modal-send').on('shown.bs.modal',
	function (event) {
		let button = $(event.relatedTarget);
		let email = button.data('email');

		let modal = $(this);//btn_email
		modal.find('#send_user').html(email);
		modal.find('#suser').val('');
		modal.find('#btn_email').val(email);
	});


	// Изменение при приеме показаний номера прибора учета
	function change_pribor(pribor, ls) {
		if (confirm("Вы действительно хотите изменить номер прибора учета?")) {
			let new_pribor = $("#pribor"+pribor).val();
			$.post('/ajax/change_pribor.php', {old_pribor: pribor, new_pribor: new_pribor, ls: ls},
				function(result){
					if (result != 777) {
							alert('Номер прибора учета не изменен.');
							return false;
						} else {
							// ok
							alert('Номер прибора учета успешно изменен.');
						}
					}
			);
		} else {
			return false;
		}
	}

	// При открытии модального окна Изменение показаний прибора учета
	$('#modal-change').on('shown.bs.modal',
	function (event) {
		let button = $(event.relatedTarget),
			pu = button.data('pu'),
			pk = button.data('pk'),
			modal = $(this);

		modal.find('#pu').html(pu);
		modal.find('#pk').val(pk);
	});
	// Отправка Изменение показаний прибора учета
	function change_pk(){
		let pu = $('#pu').html(),
			pk = $('#pk').val();

		$.post('/ajax/change_pk.php', {pk: pk, pu: pu},
			function(result){
				if (result != 777) {
						alert('Не удалось изменить показания');
						return false;
					}
				}
		);
		$('#modal-change').modal('toggle');
		$('#btn_see').trigger( "click" );
		return true;
	}

</script>