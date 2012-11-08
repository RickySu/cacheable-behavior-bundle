<?php

namespace RickySu\CacheableBehaviorBundle\Renderer;

use \Twig_Loader_Filesystem;
use \Twig_Environment;

class Renderer
{
    static protected $Instance=null;
    static public function factory()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__.DIRECTORY_SEPARATOR.'templates',array(
            'autoescape' => false
        ));
        return new Twig_Environment($loader);
    }

    static public function getInstance()
    {
        if(self::$Instance!==null){
            return self::$Instance;
        }
        self::$Instance=self::factory();
        return self::$Instance;
    }
}