<?php
/**
 * Author: Tmc
 * User: Administrator
 * Date: 16-3-9
 * Time: 19:13
 */
namespace Think\Template\TagLib;
use Think\Template\TagLib;
/**
 * Html标签库驱动
 */
class Page extends TagLib{
    protected $tags=array(
        'setSize'=>array('attr'=>'pagesize','close'=>0),//attr指要传值的名字 close指标签类  1：不闭合 0：闭合
        'setOrder'=>array('attr'=>'order','close'=>0),//attr指要传值的名字 close指标签类  1：不闭合 0：闭合
    );
    public function _setSize($tag,$content){
        $pagesize = $tag['pagesize'];   //取得标签传过来的值

        $pagekey = 'p';
        $url = $urlTmp = $_SERVER[REQUEST_URI];
        if($position = strpos($urlTmp,'?')){
            $url = substr($urlTmp,0,$position);
            $strQuery = substr($urlTmp,$position+1);
            $arrQuery = array();
            parse_str($strQuery,$arrQuery);
            if(key_exists($pagekey,$arrQuery)){
                unset($arrQuery[$pagekey]);
            }
            if(key_exists('pagesize',$arrQuery)){
                unset($arrQuery['pagesize']);
            }
            $urlPostfix = http_build_query($arrQuery);
        }

        //组装url
        $urlString = $url.'?'.$urlPostfix;
        if($pagesize){
            $urlString = $urlString.'&pagesize='.$pagesize;
        }
        $urlString.= ('&'.$pagekey.'=1');
        $str = $urlString;
        return $str;
    }

    /**
     * 设置排序方式
     * @param $tag
     * @param $content
     * @return string
     */
    public function _setOrder($tag,$content){
        $order = $tag['order'];   //取得标签传过来的值

        $pagekey = 'p';
        $url = $urlTmp = $_SERVER[REQUEST_URI];
        if($position = strpos($urlTmp,'?')){
            $url = substr($urlTmp,0,$position);
            $strQuery = substr($urlTmp,$position+1);
            $arrQuery = array();
            parse_str($strQuery,$arrQuery);
            if(key_exists($pagekey,$arrQuery)){
                unset($arrQuery[$pagekey]);
            }
            if(key_exists('order',$arrQuery)){
                unset($arrQuery['order']);
            }
            $urlPostfix = http_build_query($arrQuery);
        }

        //组装url
        $urlString = $url.'?'.$urlPostfix;
        if($order){
            $urlString = $urlString.'&order='.$order;
        }
        $str = $urlString;
        return $str;
    }

}