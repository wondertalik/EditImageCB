<?php

/**
 * Class FileManipulateFromLink
 */
class FileManipulateFromLink extends FileManipulate {

    private $imgUrl;
    private $snoopy;


    public function __construct($url) {
        $this->imgUrl = htmlspecialchars($url);
        $this->init();
    }

    /**
     * Инициализация
     */
    public function init() {
        parent::init();
        $this->snoopy = new Snoopy();
        $this->tmpDir = "temp";
    }


    /**
     * Создаем временный файл, метод вызывается после getImgFromUrl
     */
    public function saveFile() {
        $name = substr($this->imgUrl, strrpos($this->imgUrl, "/") + 1);
        //создаем временный файл
        parent::saveTmpFile($name);
    }

    /**
     * Скачиваем фото по ссылке
     * @return bool true если скачалось успешно
     * @throws Exception
     */
    public function getFile() {
        $this->snoopy->fetch($this->imgUrl);
        if($this->snoopy->status == 200) {
            $this->setResults($this->snoopy->results);
        }
        else {
            throw new Exception("Image from url " . $this->imgUrl . " not found");
        }

        return true;
    }

}