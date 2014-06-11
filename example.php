<?php

include("include/editImage/FileManipulateFromCB.php");
include("include/editImage/FileManipulateFromLink.php");
include("include/editImage/SetBorderBottomTextOnFoto.php");

//ссылка на фото
$url = "http://content.ideanomix.com/cooking/static/images/01-meat/ipad_retina/zharkoe_iz_svininy_v_gorshochke_step_1.jpg";
$text = "На раскаленной сковороде обжарить мясо в небольшом количестве оливкового масла до образования корочки. Выложить на тарелку.";


//191 - айди таблицы, 3441 - айди поля с изображением, 15 - айди строки
$file = new FileManipulateFromCB(191, 3441, 15);
$file->setTmpDir("nmd/temp");
//Имя изображения
$file->setNameFiles("34.jpg");

//
$file = new FileManipulateFromLink($url);

//Настройки
$settings = new Settings($_SERVER['DOCUMENT_ROOT']);
//цвет шрифта
$settings->setFontColor("#ffffff");
//Размер шрифта
$settings->setFontSize(15);
//высота нижней рамки. Если текст не помещается, автоматически расчитывается новая высота.
//Если помещается, высота остается указанная пользователем
$settings->setBackgroundBottomHeight(50);
//цвет нижней рамки
$settings->setBackgroundColor("#CD853F");
//междустрочный интервал
$settings->setLineInterval(5);

//Работаем с фото
$photo = new SetBorderBottomTextOnFoto($file, $settings);
//Загружаем фото
$photo->loadImg();
//Редактируем
$photo->editImg(text);
$photo->output();
