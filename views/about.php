<?
	$content = get_content('about');
?>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>

<div class="col">
	<div class="row">
		<div class="col">
			<h2>О компании</h2>
			<?=$content[0]['description']?>
			<p>
				<strong>Режим работы:</strong>
				с <time datetime="07:30">07:30</time> до <time datetime="17:00">17:00</time>,
				обед с <time datetime="11:30">11:30</time> до <time datetime="13:00">13:00</time>.
			</p>
			<aside class="max-contact" aria-label="Связаться с нами в MAX">
				<div class="max-contact__content">
					<span class="max-contact__eyebrow">Мессенджер MAX</span>
					<h3>Напишите нам в MAX</h3>
					<p>Отсканируйте QR-код или перейдите в чат по кнопке.</p>
					<a class="max-contact__link" href="<?=MAX_CHAT_URL?>" target="_blank" rel="noopener noreferrer">Открыть MAX <i class="fa fa-external-link" aria-hidden="true"></i></a>
				</div>
				<a class="max-contact__qr" href="<?=MAX_CHAT_URL?>" target="_blank" rel="noopener noreferrer" aria-label="Открыть чат MAX">
					<img src="/image/qr_max.png" alt="QR-код для перехода в мессенджер MAX">
				</a>
			</aside>
		</div>
	</div>
	<div class="row">
		<div class="col-12 my-2">
			<?=$content[1]['description']?>
		</div>
		<div class="col-12 my-2">
			<div id="map" style="width: 100%; height: 400px;">
				<div style="padding-top: 180px; padding-left: 48%">
					<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
					<span class="sr-only">Загрузка...</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Создаём карту -->
<script type="text/javascript">
    // Функция ymaps.ready() будет вызвана, когда
    // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
    ymaps.ready(init);

    function init() {
		$("#map").html('');
        // Создание карты.
        // https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/map-docpage/
        var myMap = new ymaps.Map("map", {
            // Координаты центра карты.
            // Порядок по умолчнию: «широта, долгота».
            center: [56.893874, 93.171249],
            // Уровень масштабирования. Допустимые значения:
            // от 0 (весь мир) до 19.
            zoom: 12,
            // Элементы управления
            // https://tech.yandex.ru/maps/doc/jsapi/2.1/dg/concepts/controls/standard-docpage/
            controls: [

                'zoomControl', // Ползунок масштаба
                'rulerControl', // Линейка
                'routeButtonControl', // Панель маршрутизации
                /*'trafficControl', // Пробки*/
                'typeSelector', // Переключатель слоев карты
                'fullscreenControl', // Полноэкранный режим

                // Поисковая строка
				/*
                new ymaps.control.SearchControl({
                    options: {
                        // вид - поисковая строка
                        size: 'large',
                        // Включим возможность искать не только топонимы, но и организации.
                        provider: 'yandex#search'
                    }
                })
				*/

            ]
        });

        // Добавление метки
        // https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/Placemark-docpage/
        var myPlacemark = new ymaps.Placemark([56.89388474316639,93.1712935694023], {
            // Хинт показывается при наведении мышкой на иконку метки.
            hintContent: 'ООО "ОКК"',
            // Балун откроется при клике по метке.
            balloonContent: 'пгт. Большая Мурта, ул. Советская, д. 164<br>Телефон: +7 (391) 9833663<br>Диспетчерская служба: +7 (391) 9833565<br>'
        });

        // После того как метка была создана, добавляем её на карту.
        myMap.geoObjects.add(myPlacemark);

    }
</script>
