<?php

include_once ("include/editImage/FileManipulate.php");

class FileManipulateFromCB extends FileManipulate {

    private $tableID;
    private $fieldID;
    private $lineID;
    private $nameFiles;

    public function __construct($tableID, $fieldID, $lineID) {
        $this->setTableID($tableID);
        $this->setFieldID($fieldID);
        $this->setLineID($lineID);
    }

    public function init() {
        parent::init();
        $this->setTableID(null);
        $this->setFieldID(null);
        $this->setLineID(null);
        $this->nameFiles = null;
    }

    /**
     * @param mixed $lineID
     */
    public function setLineID($lineID) {
        $this->lineID = $lineID;
    }

    /**
     * @return mixed
     */
    public function getLineID() {
        return $this->lineID;
    }

    /**
     * @param mixed $tableID
     */
    public function setTableID($tableID) {
        $this->tableID = $tableID;
    }

    /**
     * @return mixed
     */
    public function getTableID() {
        return $this->tableID;
    }

    /**
     * @param mixed $fieldID
     */
    public function setFieldID($fieldID) {
        $this->fieldID = $fieldID;
    }

    /**
     * @return mixed
     */
    public function getFieldID() {
        return $this->fieldID;
    }

    public function setNameFiles($file) {
        $this->nameFiles = trim($file);
    }

    public function getNameFiles() {
        return $this->nameFiles;
    }

    public function getFile() {
        //Определяем путь к загружаемому файлу в КБ
        $file_path = $this->getFilePath($this->getFieldID(), $this->getLineID(), $this->getNameFiles());

        if(file_exists($file_path)) {
            $fp = fopen($file_path, "r");
            $this->setResults(fread($fp, filesize($file_path)));
            fclose($fp);
        } else {
            throw new Exception("File " . $file_path . " not found");
        }

        return true;
    }

    public function saveFile() {
        $name = $this->getNameFiles();
        parent::saveTmpFile($name);
    }

}
