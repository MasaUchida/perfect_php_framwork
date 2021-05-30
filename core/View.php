<?php

class View
{
    protected $base_dir;
    protected $defaults;
    protected $layout_variables = array();

    public function __construct($base_dir, $defaults = array())
    {
        $this->base_dir = $base_dir;
        $this->defaults = $defaults;
    }

    public function setLayoutVar($name, $value)
    {
        $this->layout_variables[$name] = $value;
    }

    public function render($_path, $_variables = array(), $_layout = false)
    {
        $_file = $this->base_dir . '/' . $_path . '.php';

        //extract は　連想配列のkeyを変数名にvalueを値として変換する関数
        extract(array_merge($this->defaults, $_variables));

        //アウトプットバッファリング　展開される値を全て文字列としてバッファに取り込んで、あとで一気に出す
        ob_start();

        //バッファフラッシュを止める　バッファフラッシュがONだと、値がバッファ要領を超えた際に自動で出力してしまう
        ob_implicit_flush(0);

        //ここでviewファイルが呼ばれているが、こいつが全てバッファされる
        require $_file;

        //バッファ内容の取り出し
        $content = ob_get_clean();

        if($_layout){
            $content = $this->render(
                $_layout,
                array_merge($this->layout_variables, array('_content' => $content)),
            );
        }

        return $content;
    }

    public function escape($string)
    {
        return htmlspecialchars($string,ENT_QUOTES,'utf-8');
    }
}