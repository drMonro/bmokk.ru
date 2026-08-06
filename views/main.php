<link href="/css/timeTo.css" type="text/css" rel="stylesheet"/>
<script src="/js/jquery.time-to.min.js"></script>
<script>
	function getRelativeDate(days, hours, minutes) {
		var d = new Date(Date.now() + 60000 /* milisec */ * 60 /* minutes */ * 24 /* hours */ * days /* days */);
		d.setHours(hours || 0);
		d.setMinutes(minutes || 0);
		d.setSeconds(0);
		return d;
	}
</script>

<?
	$content = get_content('main');
?>
<style>
    .img-max {
        border: 1px #000 solid;
    }
    .img-max:hover {
        opacity: 0.8;
    }
</style>
<div class="col">
	<div class="row">
		<div class="col">
			<h2 class="text-center">Обслуживание коммунального комплекса</h2>
				<div class="row">
					<div class="col my-3 text-center">
                                            <!--
						Уважаемый клиент!<br>
						<br>
                                                Здесь Вы можете передать показания индивидуального прибора учета с 15 по 25 число каждого месяца (в декабре и феврале срок передачи показаний с 15 по 20 число), узнать начисления.
						Для передачи показаний с сайта необходимо <a href="/?login">войти</a> или <a href="/?register">зарегистрироваться</a>, указать лицевой счет, а так же приборы учета в личном кабинете.<br>
						Более полную информацию можно получить <a href="/?questions">здесь</a>.
                                            -->
                                            Уважаемые, абоненты!<br>
                                            Вы можете присоедениться в нашу группу, для своевременного получения информации:<br>
                                            <a href="https://max.ru/u/f9LHodD0cOJANDOQeyaPqIiNSjPWsp3ZNlgyX0llCOpHrothS5UacksdpZ4" target="_blank" title="Мы в мессенджере МАХ">
                                                <img src="image/max.png" class="img-fluid rounded-lg img-max">
                                            </a>
					</div>
				</div>
			<?=$content[0]['description']?>
		</div>
	</div>
	<div class="row my-2">
		<div class="col-sm-12 col-md-6 text-center">
			<!-- <img src="image/holidays/new_year.png" class="img-fluid"> -->
		<a href="https://onf.ru" target="_blank"><img src="image/qual4.jpg" class="img-fluid rounded-lg" style="height: 585px"></a>
		<br>
			<?=$content[1]['description']?>
		</div>
		<div class="col-sm-12 col-md-6">
			<h3>Новости <span class="color_grey"><a href="/?news">все новости</a></span></h3>
				<? include_once ($_SERVER['DOCUMENT_ROOT'].'/views/main_news.php'); ?>
						<div class="text-center card news" style="height: 200px;">
			<?
				// Провверяем текущее число и выводим счетчик
				$dtt = intval(date("d"));
				if (($dtt > 14 && $dtt < 26) ){
			?>
					<div style="padding: 20px 10px 0px 10px;">Осталось до окончания передачи показаний в текущем месяце</div>
					<div style="padding-bottom: 20px;"><a href="/?login">Передать показания</a></div>
					<div id="countdown-2"></div>
					<script>
						var date = getRelativeDate(<?=25-$dtt?>);
					</script>
			<?
				} else {
			?>
					<div style="padding: 20px 10px 0px 10px;">Осталось до начала приема показаний,</div>
					<div style="padding-bottom: 20px;">а сейчас можно <a href="/?login">узнать начисления</a></div>
					<div id="countdown-2"></div>
					<script>
						var date = getRelativeDate(<?=($dtt > 25) ? 31-$dtt+15 : 15-$dtt?>);
					</script>
			<?
				}
			?>
					<script>
						$('#countdown-2').timeTo({
							timeTo: date,
							displayCaptions: true,
							theme: "black",
							fontSize: 40,
							captionSize: 10,
							lang: 'ru'
						});
					</script>
			</div>
		</div>
	</div>
	<?// Госуслуги ДОМ ?>
	<div class="row my-2">
		<div class="col-sm-12 col-md-6 text-center" style="background-color: #fbfcff">
			<a href="https://www.gosuslugi.ru/mp_dom" target="_blank"><img src="image/dom_1.jpg" class="img-fluid"></a>
			<div class="row my-2 text-left">
				<div class="col-sm-12 col-md-4">
					<p><i class="fa fa-check-circle-o" aria-hidden="true"></i> Отправляйте показания и следите за расходами в одном окне</p>
					<p><i class="fa fa-check-circle-o" aria-hidden="true"></i> Передавайте заявки в управляющую организацию и получайте оперативные ответы</p>
				</div>
				<div class="col-sm-12 col-md-4">
					<p><i class="fa fa-check-circle-o" aria-hidden="true"></i> Просматривайте и оплачивайте счета за комунальные услуги</p>
					<p><i class="fa fa-check-circle-o" aria-hidden="true"></i> Участвуйте в юридически значимых общедомовых собраниях онлайн</p>
					<p><i class="fa fa-check-circle-o" aria-hidden="true"></i> Получайте новости от управляющей организации</p>
				</div>
				<div class="col-sm-12 col-md-4">
					<p><i class="fa fa-check-circle-o" aria-hidden="true"></i> Контролируйте график капитального ремонта и отчетность управляющей организации</p>
					<p><i class="fa fa-check-circle-o" aria-hidden="true"></i> Проверяйте, все ли услуги оказывает управляющая организация</p>
				</div>
			</div>
		</div>
		<div class="col-sm-12 col-md-6 text-center" style="background-color: #fbfcff"><a href="https://www.gosuslugi.ru/mp_dom" target="_blank"><img src="image/dom_2.jpg" class="img-fluid" style="padding-top: 15px; padding-bottom: 15px;"></a></div>
	</div>
    <div class="row my-2">
        <div class="col-sm-12 text-center">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="/video/rolic1.mp4" allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-sm-12 text-center my-2">
            <a href="https://max.ru/u/f9LHodD0cOJANDOQeyaPqIiNSjPWsp3ZNlgyX0llCOpHrothS5UacksdpZ4" target="_blank" title="Мы в мессенджере МАХ">
                <img src="image/max.png" class="img-fluid rounded-lg img-max">
            </a>
        </div>
    </div>
</div>
