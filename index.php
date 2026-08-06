<?
if (!session_id()) {
    //session_save_path('/home/cr16038/tmp');
	session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once ($_SERVER['DOCUMENT_ROOT'].'/config.php');
include_once ($_SERVER['DOCUMENT_ROOT'].'/functions.php');

if (isset($_GET["exit"])) {
	$_SESSION = [];
	js('window.location.href = "/"');
}

?>

<!DOCTYPE html>
<html lang="ru-RU">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>ОКК | Обслуживание коммунального комплекса</title>
		<meta name="description" content="Обслуживание коммунального комплекса Большая Мурта" />
		<meta name="keywords" content= "ОКК,Большая Мурта,передать показания,вода,бмокк,Яковлев,обслуживание коммунального комплекса" />
		<meta property="og:type" content="site" />
		<meta property="og:title" content="ОКК" />
		<meta property="og:description" content="ОКК,Большая Мурта,передать показания,вода,бмокк,Яковлев,обслуживание коммунального комплекса" />

        <link href="/css/bootstrap.css" rel="stylesheet" type='text/css'>
        <link href="/css/style.css" rel="stylesheet" type='text/css'>
        <link rel="stylesheet" href="/font-awesome/css/font-awesome.min.css">
		<link href="/css/notification.css" rel="stylesheet" type='text/css'>
        <script src="/js/jquery-3.3.1.min.js"></script>
        <script src="/js/popper.min.js"></script>
        <script src="/js/bootstrap.js"></script>
		<script src="/js/jquery.notification.min.js"></script>
		<script src="/js/scripts.js"></script>
    </head>

    <body>
		<!-- Верхнее меню -->
			<div class="main-menu">
				<div class="logo nav-link">
                                    <a href="/" title = "Обслуживание коммунального комплекса"><img src="/favicon.ico" width="32px"><img src="/image/logo.png"></a>
				</div>
				<div class="hrefs">
					<?
					if (isset($_SESSION['user_right'])) {
						?>
							<a class="nav-link" href="/?exit">
								<i class="fa fa-sign-out" aria-hidden="true" title = "Выход"></i>
								<span class="href-description">
									Выход
								</span>
							</a>
						<?
					} else {
						/*

							<a class="nav-link" href="/?login">
								<i class="fa fa-sign-in" aria-hidden="true" title = "Личный кабинет"></i>
								<span class="href-description">
									Личный кабинет
								</span>
							</a>
						*/
					}
					?>
				</div>
					<?
						if (isset($_SESSION['user_right'])) {
							?>
								<div class="hrefs">
									<a class="nav-link" href="/?login">
										<i class="fa fa-user-circle" aria-hidden="true" title = "Личный кабинет"></i>
											<span class="href-description">
												<?=$_SESSION['user'];?>
											</span>
									</a>
								</div>
							<?
						}
					?>
				<div class="hrefs">
					<a class="nav-link" href="/?about">
						<i class="fa fa-users" aria-hidden="true" title = "О нас"></i>
						<span class="href-description">
							О нас
						</span>
					</a>
				</div>
				<div class="hrefs">
					<a class="nav-link" href="/?oi">
						<i class="fa fa-info-circle" aria-hidden="true" title = "Раскрытие информации"></i>
						<span class="href-description">
							Информация
						</span>
					</a>
				</div>
			</div>

			<footer class="footer">
				<div class="hrefs">
					<a class="nav-link" href="tel: 83919833605">
						<span class="phone">+7 (391) 9833605</span>
					</a>
				</div>

                            <div class="nav-link logo agreement" style="padding-top: 15px;">
					<? //<span class="color_grey">Используя наш сайт, вы даете согласие на обработку Cookies и других данных, в соответствии с <a href="/?agreement">Пользовательским соглашением</a>. Подробно в разделе <a href="/?questions">Помощь</a>.</span>?>
                                    Создание сайта и поддержка
                                    <a href="https://wmaster24.ru/" target="_blank" style="color: #000!important;">
                                        <b><span style="color: #f45740">WEB</span>MASTER<span style="color: #f45740">24</span></b>
                                    </a>

				</div>
			</footer>


        <?if (isset($_GET['formula4ru'])){?>
                <style>
                    p {
                        margin-top: 20px;
                        font-size: 13px;
                        text-align: left;
                    }
                    .im:hover {
                        .img-fluid {-webkit-filter: brightness(50%);}
                        .overlay {visibility: visible;}
                        cursor: pointer;
                    }
                    .overlay {
                        position: absolute; top: 40%; left: 50%;
                        transform: translate(-50%, -50%);
                        visibility: hidden;
                        color: #fff;
                    }
                </style>
                <?
                    $company = [
                      1 => 'компании ООО "Гиперион"',
                      2 => 'сетевязательной фабрики Sezus',
                      3 => 'компании OOO "Стройбург"'
                    ];
                ?>
                <div class="container">
                    <div class="row my-3">
                        <? for ($i = 1; $i < 7; $i++) {?>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center">
                            <div class="im">
                                <? $ii = ($i > 3) ? $i-3 : $i?>
                                <img src="image/f4/<?=$ii?>.jpg" class="img-fluid">
                                <span class="overlay">Смотреть проект <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                            </div>
                            <p><b><?='Корпоративный сайт '.$company[$ii]?></b></p>
                        </div>
                        <? } ?>
                    </div>
                </div>
        <? exit; ?>
        <?}?>


        <div class="container">
            <div class="row">

				<?
					$page = 'views/main';

					if (isset($_GET["about"])) {
						$page = 'views/about';
					} elseif (isset($_GET["news"])) {
						$page = 'views/news';
					} elseif (isset($_GET["oi"])) {
						$page = 'views/open';
					}

					include_once $_SERVER['DOCUMENT_ROOT'].'/'.$page.'.php';
				?>

            </div>
        </div>

<?
	if (!isset($_SESSION['user_right']) || (isset($_SESSION['user_right']) && $_SESSION['user_right'] != 'admin')) {


	// Отправим письмо если есть данные
		if (isset($_POST["memail"]) && isset($_POST["memailq"]) && isset($_POST["medig"])) {
			$m = trim($_POST["memailq"]);
			if (strlen($m) < 5) {
				js("$.notification.show('error','Ваше сообщение слишком короткое. Письмо не отправлено!')");
			} else if (!isset($_SESSION['captcha']) || $_POST["medig"] != $_SESSION['captcha']){
				js("$.notification.show('error','Вы не верно ввели число с картинки. Письмо не отправлено!')");
			} else if ($_POST["memail9"] != '999'){
				js("$.notification.show('error','Bot!')");
			} else {
				$m = 'Пользователь '.trim($_POST["memail"]).' отправил сообщение/вопрос с сайта: '.$m;
				send_mail("ooo_okk@mail.ru", $m);
				//send_mail("antonb770@mail.ru", $m);
				unset($_SESSION['captcha']);
			}
		}
?>
		<div data-placement="top" class="callbutton" data-toggle="modal" data-target="#modal-phone">
			<div data-toggle="tooltip" title="Напишите нами!">
				<div class="back-circle"></div>
				<div class="button"></div>
				<div class="front-circle"></div>
				<i class="fa fa-envelope phone"></i>
			</div>
		</div>

		<div data-placement="top" class="callbutton-small">
			<a href="tel:+73919833565">
				<div class="button-small"></div>
				<div class="front-circle-small"></div>
				<i class="fa fa-phone phone-small"></i>
			</a>
		</div>

		<!-- HTML-код модального окна ОТПРАВИТЬ начало-->
		<div id="modal-phone" tabindex="-1" class="modal fade" role="dialog">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<div class="card-title text-center phone">
						<i class="fa fa-envelope" aria-hidden="true"></i> Напишите нам или задайте вопрос
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
					<form action="#" method="post" id="contactForm">
						<div class="text-center">Введите свои данные в эту форму и мы свяжемся с Вами в ближайшее время.</div>
							<div class="mb-3 my-3">
								<?
									$v = (isset($_SESSION['user']) && strpos($_SESSION['user'],'@')) ? $_SESSION['user'] : '';
								?>
								<label for="minputEmail" class="form-label">Введите Ваш e-mail</label>
								<input type="email" value="<?=$v?>" name="memail" class="form-control" id="minputEmail" placeholder="Введите Ваш e-mail" required autofocus>
								<input type="text" value="999" name="memail9" hidden="true">
							</div>
							<div class="mb-3">
								<label class="form-label">Введите сообщение</label>
								<textarea name="memailq" class="form-control" id="minputEmailq" rows="4" required style="background: #f4f4f5 !important;"></textarea>
							</div>
							<div class="mb-3">
								<label for="minputdig" class="form-label">Введите число с картинки</label>
								<img src="<?='/views/captcha.php'?>">
								<input type="text" value="" name="medig" class="form-control my-1" placeholder="Введите число" required>
							</div>
							<div class="mb-3 text-center">
								<span class="color_grey">
									Если вы долго не получаете ответа, то проверьте папку спам. Ответ приходит с адреса почты: support@bmokk.ru
								</span>
							</div>
							<div class="mb-3 text-center">
								<button class="btn btn-dark" type="submit">
									<i class="fa fa-envelope" aria-hidden="true"></i> Отправить
								</button>
							</div>
					</form>
				</div>
			</div>
		  </div>
		</div>
<?
	}
	?>
<!-- Закоментировать когда снежинки не понадобятся. НЕ УДАЛЯТЬ!!! -->
<!--
<script>
// количество снежинок, которое будет на экране одновременно.
let snowmax=120

// Цвета для снежинок. Для каждой конкретной снежинки цвет выбирается случайно из этого массива.
let snowcolor=new Array("#b9dff5","#7fc7ff","#7fb1ff","#7fc7ff","#b9dff5")

// Шрифт для снежинок
let snowtype=new Array("Times")

// Символ (*) и есть снежинка, в место нее можно вставить любой другой символ.
let snowletter="&#10052;"

// Скорость движения снежинок (от 0.3 до 2)
let sinkspeed=1

// Максимальный размер для снежинок
let snowmaxsize=27

// Минимальный размер для снежинок
let snowminsize=6

// Зона для снежинок
// 1 для всей страницы, 2 в левой части страницы
// 3 в центральной части, 4 в правой части страницы
let snowingzone=1

////////////////////////
///////// Конец настроек
////////////////////////

let snow=new Array()
let marginbottom
let marginright
let timer
let i_snow=0
let x_mv=new Array();
let crds=new Array();
let lftrght=new Array();
function randommaker(range) {
    rand=Math.floor(range*Math.random())
    return rand
}
window.addEventListener('scroll', function() {
  marginbottom = document.documentElement.scrollHeight
});
function initsnow() {
    //marginbottom = document.documentElement.clientHeight+50
    //marginbottom = document.body.clientHeight-snowmaxsize
    marginbottom = document.documentElement.scrollHeight


    marginright = document.body.clientWidth-15
    let snowsizerange=snowmaxsize-snowminsize
    for (i=0;i<=snowmax;i++) {
        crds[i] = 0;
        lftrght[i] = Math.random()*15;
        x_mv[i] = 0.03 + Math.random()/10;
        snow[i]=document.getElementById("s"+i)
        snow[i].style.fontFamily=snowtype[randommaker(snowtype.length)]
        snow[i].size=randommaker(snowsizerange)+snowminsize
        snow[i].style.fontSize=snow[i].size+'px';
        snow[i].style.color=snowcolor[randommaker(snowcolor.length)]
        snow[i].style.zIndex=1000
        snow[i].sink=sinkspeed*snow[i].size/5
        if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size)}
        if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size)}
        if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/4}
        if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/2}
        snow[i].posy=randommaker(2*marginbottom-marginbottom-2*snow[i].size)
        snow[i].style.left=snow[i].posx+'px';
        snow[i].style.top=snow[i].posy+'px';
    }
    movesnow()
}
function movesnow() {
    for (i=0;i<=snowmax;i++) {
        crds[i] += x_mv[i];
        snow[i].posy+=snow[i].sink
        snow[i].style.left=snow[i].posx+lftrght[i]*Math.sin(crds[i])+'px';
        snow[i].style.top=snow[i].posy+'px';

        if (snow[i].posy>=marginbottom-2*snow[i].size || parseInt(snow[i].style.left)>(marginright-3*lftrght[i])){
            if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size)}
            if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size)}
            if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/4}
            if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/2}
            snow[i].posy=0
        }
    }
    let timer=setTimeout("movesnow()",50)
}

for (i=0;i<=snowmax;i++) {
    document.body.insertAdjacentHTML('beforeend', "<span id='s"+i+"' style='user-select:none;position:absolute;top:-"+snowmaxsize+"'>"+snowletter+"</span>")
}
window.onload=initsnow
</script>
-->
<!-- Закоментировать когда снежинки не понадобятся. НЕ УДАЛЯТЬ!!! -->
    </body>
</html>
