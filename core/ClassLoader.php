<?php

class ClassLoader{

    protected $dirs;

    /**
     * newしたクラスが見つからない時にspl_autoload_registerが勝手に動くらしい
     * 動いた時は、new Nameの「Name」部分が引数としてコールバックに渡される
     */
    public function register()
    {
        spl_autoload_register(array($this,'loadClass'));
    }

    /**
     * @param str 読み込むディレクトリのfull path
     */
    public  function registerDir($dir)
    {
        $this->dirs[] = $dir;
    }

    //$class に入ってきた名前のphpファイルを呼び出す
    public function loadClass($class)
    {
        foreach( $this->dirs as $dir )
        {
            $file = $dir . '/' . $class .'.php';
            if (is_readable($file)){
                require $file;
                return;
            }
        }
    }

}
