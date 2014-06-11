<?php


class SetBorderBottomTextOnFoto {

    private $settings;
    private $fileManipulate;
    private $imageSize;
    private $dest;
    private $params = array();
    private $text = array();

    public function __construct(FileManipulate $file, $set) {
        $this->init($file, $set);
    }

    public function init(FileManipulate $file, $set) {
        $this->fileManipulate = $file;
        $this->settings = $set;
    }

    /**
     * Загружаем файл
     */
    public function loadImg() {
        //Если изображение загружено успешно, сохраняем в временный файл
        if($this->fileManipulate->getFile()) {
            $this->fileManipulate->saveFile();
            //Получаем инфу о фото
            $this->getImageSize($this->getTmpFilePath());
            //определяем доступную ширину текста
            $this->params['width'] = round($this->imageSize[0] - $this->settings->getMinMarginRight() - $this->settings->getMinMarginLeft(), 2);
            //определяем необходимую высоту текста
            $this->params['height'] = round($this->imageSize[1] - $this->settings->getMinMarginTop() - $this->settings->getMinMarginBottom(), 2);

            return true;
        }
        return false;
    }

    public function getTmpFilePath() {
        if($this->fileManipulate->getCreateTmpFile()) {
            return $this->fileManipulate->getTmpFilePath();
        }
        return false;
    }

    public function delTmpFile() {
        if($this->fileManipulate->getCreateTmpFile()) {
            return $this->fileManipulate->delTmpFile();
        }
    }

    public function clear() {
        $this->fileManipulate = null;
    }

    public function saveToCb($tableID, $fieldID, $lineID) {
        $nameFile = basename($this->getTmpFilePath());
        //Определяем путь к загружаему файлу в КБ
        $file_path = $this->fileManipulate->getFilePath($fieldID, $lineID, $nameFile);

        //Создаем необходимую структуру директорий
        create_data_file_dirs($fieldID, $lineID, $nameFile);

        if($this->imageSize[2] == 2) {
            ImageJPEG($this->dest, $file_path, 100);
        }
        elseif($this->imageSize[2] == 1) {
            ImageGIF($this->dest, $file_path);
        }
        else {
            ImagePNG($this->dest, $file_path);
        }

        //Обновляем значение в поле
        if(data_update($tableID, array('f' . $fieldID => $nameFile), "`id` = ", $lineID)) //удаляем временный файл
        {
            $this->delTmpFile();
            return true;
        }
        return false;

//// Формируем предпросмотр изображения в папке cache
//        $cur_line = $line;
//        $cur_table = $table;
//        $cur_field = get_table_fields($table);
//        $cur_field = $cur_field[491];
//
//        $t = form_display_type($cur_field, $cur_line);

    }

    /**
     * Определяем размер и тип для загруженного изображения
     * @return bool
     */
    private function getImageSize() {
        $tmps = $this->getTmpFilePath();
        if(!empty($tmps)) {
            $this->imageSize = getimagesize($tmps);
            return true;
        }
        return false;
    }

    private function createImageFrom($filePath) {
        if($this->imageSize[2] == 2) {
            return imagecreatefromjpeg($filePath);
        }
        elseif($this->imageSize[2] == 1) {
            return imagecreatefromgif($filePath);
        }
        elseif($this->imageSize[2] == 3) {
            return imagecreatefrompng($filePath);
        }
        else {
            return false;
        }
    }

    /**
     * Метод для получения цвета из html в rgb
     * @param $color цвет в html
     * @return array|bool
     */
    protected function htmltorgb($color) {
        if($color[0] == '#') {
            $color = substr($color, 1);
        }
        if(strlen($color) == 6) {
            list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        }
        elseif(strlen($color) == 3) {
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        }
        else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return array($r, $g, $b);
    }

    //Редактируем загруженное изображение
    public function editImg($text) {

        //Если получили инфу о фото
        if($this->getImageSize()) {
            if(($src = $this->createImageFrom($this->getTmpFilePath())) != false) {
                //Подготавливаем текст
                $this->prepareText($text);
                //расчитываем новую высоту изображения исходя из высоты нижней рамки

                //нижняя рамка расчитывается динамически в зависимости от текста
                $height = $this->imageSize[1] + $this->calculateMinBottomHeight();

                //Цвет фона рамки
                $col = $this->htmltorgb($this->settings->getBackgroundColor());
                $this->dest = imagecreatetruecolor($this->imageSize[0], $height);

                $color = imagecolorallocate($this->dest, $col[0], $col[1], $col[2]);
                imagefilledrectangle($this->dest, 0, 0, ($this->imageSize[0] - 1), ($height - 1), $color);


                imagecopy($this->dest, $src, 0, 0, 0, 0, $this->imageSize[0], $this->imageSize[1]);


//WonDebug::prt($this->text);
                foreach($this->text as $key => $value) {
                    //Ширина текста
                    $size = imagettfbbox($this->settings->getFontSize(), 0, $this->settings->getFontType(), $value);
                    //Определяем ширину фразы
                    $w_value = $size[2] - $size[0];
                    //Начальная координата х
                    $x_start = round(($this->imageSize[0] - $w_value - $this->settings->getMinMarginLeft() - $this->settings->getMinMarginRight()) / 2, 2);
                    //Начальная координата y
                    if($key == 0) {
                        $y_start = $this->imageSize[1] + $this->settings->getMinMarginTop() + $this->settings->getFontSize();
                    }
                    else {
                        $y_start = $this->imageSize[1] + $this->settings->getMinMarginTop() + $this->settings->getFontSize() * ($key + 1) + $this->settings->getLineInterval() * $key;
                    }

                    $this->prints($value, $this->settings->getFontType(), $this->settings->getFontColor(), $this->settings->getFontSize(), 0, $x_start, $y_start);
                }
            }
        }
        return false;
    }

    /**
     * Расчитываем минимальную высоту нижней рамки
     * @return mixed
     */
    private function calculateMinBottomHeight() {
        $count_string = count($this->text);
        $height = $this->settings->getMinMarginBottom() + $this->settings->getMinMarginTop() + $this->settings->getLineInterval() * ($count_string - 1) + $this->settings->getFontSize() * $count_string;
        //Если указанная высота пользователем маловата для размещения текста
        return $height < $this->settings->getBackgroundBottomHeight() ? $this->settings->getBackgroundBottomHeight() : $height;
    }

    /**
     * Подготоавливаем текст, формируем строки из слайсов
     * @param $text
     */
    private function prepareText($text) {
        $strings_slice = $this->sliceText($this->cropText($text));
        $strings = array();
        foreach($strings_slice as $key => $value) {
            $strings[] = implode(" ", $value);
        }
        $this->text = $strings;
    }

    /**
     * Получаем срез индексов по строке.
     * Разбивает текст на строки в зависимости от доступной ширины для текста на фото
     * @param $text
     * @return array массив срезов индексов
     */
    private function cropText($text) {
        //Разбиваем строку по пробелу
        $elm = explode("\r\n", trim($text));
        foreach($elm as $key => $value)
            if(empty($value)) {
                unset($elm[$key]);
            }
        $text = implode(" ", $elm);

        $element = explode(" ", $text);
        $strings[] = 0;
        $tmp = '';

        $i = 0;
        $count_el = count($element);

        while($i < $count_el) {

            $tmp .= trim($element[$i]) . " ";

            if(!empty($tmp)) {
                $size = imagettfbbox($this->settings->getFontSize(), 0, $this->settings->getFontType(), $tmp);
                //Определяем ширину фразы
                $w = $size[2] - $size[0];
//                WonDebug::ech($w) . ": " . WonDebug::ech($tmp);

                //Если последняя строка
                if(($count_el - $i) == 1) {
                    $strings[] = $i;
//                    WonDebug::ech("last");
                    break;
                }
                elseif($w <= $this->params['width']) { //Если ширина фразы меньше чем доступная ширина для текста
                    $i++;
//                    WonDebug::ech("widht: " . $w . " for ".$this->params['width']." - iter: " . $i);
                    continue;
                }
                else {
                    $strings[] = $i;
//                    WonDebug::ech("widht: " . $w . " for ".$this->params['width']." - iter: " . $i);
                    $tmp = '';
                }
            }
            else {
                break;
            }
        }

        return array('strings' => $strings, 'element' => $element);
    }


    /**
     * Формируем слайсы массивов текста
     * @param array $input
     * @return array
     */
    private function sliceText(array $input) {

        //Считаю количество елементов в массиве
        $countStr = count($input['strings']);
//        WonDebug::prt($input);

        for($i = 0; $i < $countStr; $i++) {
            //Начальное значение
            $start = $input['strings'][$i];
            //Если последний елемент в массиве среза

            if(isset($input['strings'][$i + 1])) {
                //длина среза
                $length = $input['strings'][$i + 1] - $input['strings'][$i];
                $return_[] = array_slice($input['element'], $start, $length);
            }
            else {
                $last = array_pop($return_);
                array_push($last, $input['element'][$input['strings'][$i]]);
                array_push($return_, $last);
                break;

            }

        }

//        WonDebug::prt($return_);

        return $return_;
    }

// Наложение текста на изображение
// $text - текст, $fontfile - путь к файлу со шрифтами, $color цвет в виде #000000, по умолчанию #000000
// $size - размер шрифта , $angle - угол в градусах , $x - координата x - от куда печатать, $y - координата y, от куда печатать,
// $pr - прозрачность от 0-непрозрачно до 127 - обсалютно прозрачно
    private function prints($text, $fontfile, $color = '#000000', $size = 20, $angle = 0, $x = 10, $y = 10, $pr = 0) {
        $col = $this->htmltorgb($color);
        $color = imagecolorallocatealpha($this->dest, $col[0], $col[1], $col[2], $pr);
        imagettftext($this->dest, $size, $angle, $x, $y, $color, $fontfile, $text);
    }

    // Вывод изображения на экран
    public function output() {
        if($this->imageSize[2] == 2) {
            header("Content-Type: image/jpg");
            ImageJPEG($this->dest);
        }
        elseif($this->imageSize[2] == 1) {
            header("Content-Type: image/gif");
            ImageGIF($this->dest);
        }
        else {
            header("Content-Type: image/png");
            ImagePNG($this->dest);
        }
    }

    // Получаем расширение файла, метод необходим для автоматическом добавлении расширения файла используемом в методе save()
    public function extension() {
        if($this->imageSize[2] == 2) {
            return "jpg";
        }
        elseif($this->imageSize[2] == 1) {
            return "gif";
        }
        else {
            return "png";
        }
    }

}