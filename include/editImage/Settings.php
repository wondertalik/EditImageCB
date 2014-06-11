<?php

/**
 * Класс настроек для формирования текста и нижней рамки в изображении
 * Class Settings
 */
class Settings {

    //Настройки нижней рамки
    //высота нижней рамки. Если текст не помещается, автоматически расчитывается новая высота.
    //Если помещается, высота остается указанная пользователем
    private $backgroundBottomHeight = 50; //высота нижней рамки
    private $backgroundColor = "#CD853F"; //цвет нижней рамки


    //Настройки шрифта
    private $fontType; //путь к шрифту
    private $fontSize = 15; //размер шрифта
    private $fontColor = "#ffffff"; //цвет шрифта

    //отступы
    private $minMarginTop = 10; //нижний
    private $minMarginRight = 10; //справа
    private $minMarginBottom = 10; //слево
    private $minMarginLeft = 10; //внизу

    //Междустрочный интервал
    private $line_interval = 5;

    /**
     * @param $document_root корневой каталог
     */
    public function __construct($document_root) {
        $this->fontType = $document_root . "/include/font/arial.ttf";
    }


    public function setFontType($path) {
        $this->fontType = $path;
    }

    public function getFontType() {
        return $this->fontType;
    }

    /**
     * @param int $backgroundBottomHeight
     */
    public function setBackgroundBottomHeight($backgroundBottomHeight) {
        $this->backgroundBottomHeight = $backgroundBottomHeight;
    }

    /**
     * @return int
     */
    public function getBackgroundBottomHeight() {
        return $this->backgroundBottomHeight;
    }

    /**
     * @param string $backgroundColor
     */
    public function setBackgroundColor($backgroundColor) {
        $this->backgroundColor = $backgroundColor;
    }

    /**
     * @return string
     */
    public function getBackgroundColor() {
        return $this->backgroundColor;
    }

    /**
     * @param string $fontColor
     */
    public function setFontColor($fontColor) {
        $this->fontColor = $fontColor;
    }

    /**
     * @return string
     */
    public function getFontColor() {
        return $this->fontColor;
    }

    /**
     * @param int $fontSize
     */
    public function setFontSize($fontSize) {
        $this->fontSize = $fontSize;
    }

    /**
     * @return int
     */
    public function getFontSize() {
        return $this->fontSize;
    }

    /**
     * @param int $line_interval
     */
    public function setLineInterval($line_interval) {
        $this->line_interval = $line_interval;
    }

    /**
     * @return int
     */
    public function getLineInterval() {
        return $this->line_interval;
    }

    /**
     * @param int $minMarginBottom
     */
    public function setMinMarginBottom($minMarginBottom) {
        $this->minMarginBottom = $minMarginBottom;
    }

    /**
     * @return int
     */
    public function getMinMarginBottom() {
        return $this->minMarginBottom;
    }

    /**
     * @param int $minMarginLeft
     */
    public function setMinMarginLeft($minMarginLeft) {
        $this->minMarginLeft = $minMarginLeft;
    }

    /**
     * @return int
     */
    public function getMinMarginLeft() {
        return $this->minMarginLeft;
    }

    /**
     * @param int $minMarginRight
     */
    public function setMinMarginRight($minMarginRight) {
        $this->minMarginRight = $minMarginRight;
    }

    /**
     * @return int
     */
    public function getMinMarginRight() {
        return $this->minMarginRight;
    }

    /**
     * @param int $minMarginTop
     */
    public function setMinMarginTop($minMarginTop) {
        $this->minMarginTop = $minMarginTop;
    }

    /**
     * @return int
     */
    public function getMinMarginTop() {
        return $this->minMarginTop;
    }

}