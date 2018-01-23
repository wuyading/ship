<?php
/**
 * Created by PhpStorm.
 * User: lilei
 * Date: 16-11-10
 * Time: 下午6:10
 */

namespace DebugBar;


use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\Storage\FileStorage;

class ZilfDebugbar
{

    /**
     * @var $debugBar DebugBar
     */
    static private $debugBar;
    static public $is_debug = false;

    /**
     * 初始化
     *
     * @return StandardDebugBar
     */
    static public function instance(){
        if(!self::$debugBar){
            self::$debugBar = new StandardDebugBar();
        }

        return self::$debugBar;
    }

    /**
     * 添加信息
     *
     * @param $message
     * @param string $collector
     */
    static public function info($message,$collector='messages'){
        self::debug($message,'info',$collector,false);
    }

    /**
     * 添加警告信息
     *
     * @param $message
     * @param string $collector
     */
    static public function warning($message,$collector='messages'){
        self::debug($message,'warning',$collector,false);
    }

    /**
     * 添加错误信息
     *
     * @param $message
     * @param string $collector
     */
    static public function error($message,$collector='messages'){
        self::debug($message,'error',$collector,false);
    }

    /**
     * 添加ajax信息，ajax请求的时候显示的日志信息
     *
     * @param $message
     * @param string $type
     * @param string $collector
     */
    static public function ajax($message,$type='info',$collector='messages'){
        self::debug($message,$type,$collector,true);
    }

    /**
     * 添加debug调试信息
     *
     * @param $message
     * @param string $type
     * @param string $collector
     * @param bool $isSave
     */
    static public function debug($message,$type='info',$collector='messages',$isSave=false){
        if(self::$is_debug){
            $type = $type ? $type : 'info';
            self::instance()->getCollector($collector)->addMessage($message,$type);
            if($isSave){
                self::$debugBar->collect();
            }
        }
    }

    /**
     * 设置存储信息
     * @param $directory
     */
    static function storage($directory){
        if(self::$is_debug){
            if(!file_exists($directory) || !is_dir($directory)){
                mkdir($directory,0777,true);
            }

            self::instance()->setStorage(new FileStorage($directory));
        }
    }

    /**
     * 根据名字获取搜集器 getCollector
     *
     * @param string $name
     * @return MessagesCollector|TimeDataCollector
     */
    static function get($name='messages'){
        return self::instance()->getCollector($name);
    }
}