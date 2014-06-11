<?php

abstract class FileManipulate {

    protected $tmpDir = "temp";
    //путь к тмп файлу
    protected $tmpFilePath;
    //Флаг создан тпм файл или нет
    protected $createTmpFile = false;
    protected $results;

    protected function init() {
        $this->results = null;
        if($this->createTmpFile) {
            if(file_exists($this->tmpFilePath)) {
                $this->delTmpFile();
                $this->tmpFilePath = null;
                $this->createTmpFile = false;
            }
        }
    }

    /**
     * @return boolean
     */
    public final function getCreateTmpFile() {
        return $this->createTmpFile;
    }

    /**
     * @return mixed
     */
    public final function getTmpFilePath() {
        return $this->tmpFilePath;
    }

    public final function setTmpFilePath($name) {
        //Получаем расширение файла из юрл
        $typeFile = $this->getExtension($name);
        //Формируем имя tmp-file
        $tmpName = $this->generateName($name);
        //создаем временный файл
        $this->tmpFilePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->getTmpDir() . "/" . $tmpName . "." . $typeFile;
    }

    public final function getTmpDir() {
        return $this->tmpDir;
    }

    public final function setTmpDir($path) {
        $this->tmpDir = $path;
    }

    /**
     * Удаляем временный файл, если он создан
     */
    public function delTmpFile() {
        if($this->createTmpFile) {
            unlink($this->tmpFilePath);
            $this->createTmpFile = false;
        }
    }

    public final function getResults() {
        return $this->results;
    }

    public function setResults($result) {
        $this->results = $result;
    }

    /**
     * Сохранение файла в поле таблицы
     * @param $tableID айди таблицы
     * @param $fieldID айди поля
     * @param $lineID айди строки
     */
    public function saveToCb($tableID, $fieldID, $lineID) {
        $tmpFile = $this->getTmpFilePath();

        $nameFile = basename($tmpFile);
        //Определяем путь к загружаему файлу в КБ
        $file_path = $this->getFilePath($fieldID, $lineID, $nameFile);

        //Создаем необходимую структуру директорий
        create_data_file_dirs($fieldID, $lineID, $nameFile);

        //Копируем скаченный файл
        copy($tmpFile, $file_path);

        //Обновляем значение в поле
        if(data_update($tableID, array('f' . $fieldID => $nameFile), "`id` = ", $lineID)) //удаляем временный файл
        {
            $this->delTmpFile();
        }

//// Формируем предпросмотр изображения в папке cache
//        $cur_line = $line;
//        $cur_table = $table;
//        $cur_field = get_table_fields($table);
//        $cur_field = $cur_field[491];
//
//        $t = form_display_type($cur_field, $cur_line);
    }

    /**
     * Создаем временный файл, метод вызывается после getImgFromUrl
     */
    protected final function saveTmpFile($name) {
        //создаем временный файл
        $this->setTmpFilePath($name);
        //Создаем временный файл
        $fp = fopen($this->getTmpFilePath(), 'w+');
        if($fp) {
            fwrite($fp, $this->getResults());
            if(fclose($fp)) {
                $this->createTmpFile = true;
            }
        }

    }

    public final function getExtension($name) {
        //Получаем расширение файла из юрл
        return trim(substr($name, strrpos($name, ".") + 1));
    }

    public final function generateName($name) {
        //Формируем имя tmp-file
        return sha1(rand(0, 333) . $name);
    }

    public function getFilePath($fieldID, $lineID, $nameFiles) {
        $file_path = get_file_path($fieldID, $lineID, $nameFiles);
        return $file_path;
    }

    abstract function getFile();

    abstract function saveFile();


}