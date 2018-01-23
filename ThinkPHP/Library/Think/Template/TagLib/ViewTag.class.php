<?php
/**
 * User: Administrator
 * Date: 16-3-9
 * Time: 19:10
 */
namespace Think\Template\TagLib;
use Think\Template\TagLib;
/**
 * Html标签库驱动
 */
class ViewTag extends TagLib{
    protected $tags=array(
        'ueditor'=>array('attr'=>'style,name,content,width,height,water','close'=>0),//attr指要传值的名字 close指标签类  1：不闭合 0：闭合
        'ckeditor'=> array('attr'=>'style,name,content,width,height,attr', 'close'=>0),
        'uMeditor'=> array('attr'=>'style,name,content,width,height,attr', 'close'=>0),

    );

    //百度编辑器
    public function _ueditor($attr, $content)
    {
        $style   = isset($attr['style']) ? $attr['style'] : C("editor_style"); //1 完整  2精简 3自定义
        $name    = isset($attr['name']) ? $attr['name'] : "content";
        $content = isset($attr['content']) ? $attr['content'] : ''; //初始化编辑器的内容
        $width   = isset($attr['width']) ? intval($attr['width']) : C("editor_width"); //编辑器宽度
        $width   = '"' . $width . '"';
        $height  = isset($attr['height']) ? intval($attr['height']) : C("editor_height"); //编辑器高度
        $height  = '"' . $height . '"';
        $water   = isset($attr['water']) ? $attr['water'] : C('editor_image_water');
        $get     = $_GET;
        /*unset($get['a']);
        $phpScript = isset($attr['php']) ? $attr['php'] : __WEB__ . '?' . http_build_query($get) . '&a=ueditor_upload'; //PHP处理文件*/
        //图片按钮
        if ($style == 3) {
            $toolbars = " [['fullscreen', 'source', '|', 'undo', 'redo', '|', 'simpleupload', 'fullscreen','template','forecolor','fontsize','justifyleft','justifyright', 'justifycenter',  'justifyjustify','pasteplain','bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', 'autotypeset']]";
        } else if ($style == 2) {
            $toolbars = "[['FullScreen', 'simpleupload','template',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', 'autotypeset',
            ]]";
        } else {
            $toolbars = "[
            ['fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', 'autotypeset',
            '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|',
            'lineheight', '|','paragraph', 'fontfamily', 'fontsize', '|',
             'indent','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
            'link', 'unlink', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload',  'emotion',   'map',  'insertcode',  'pagebreak','template','horizontal', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow',  'insertcol',  'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols'
            ]
            ]";
        }

        $str = '';
        $str .= '<script id="xhy_'.$name.'" name="'.$name.'" type="text/plain">'.$content.'</script>';
        if(! defined('XHY_UEDITOR')){
            $str .= '<script type="text/javascript" src="' . __XHY_EXTEND__ . '/Org/ueditor1_4_3/ueditor.config.js"></script>';
            $str .= '<script type="text/javascript" src="' . __XHY_EXTEND__ . '/Org/ueditor1_4_3/ueditor.all.js"></script>';
            define('XHY_UEDITOR',true);
        }
        $str .= "
          <script type='text/javascript'>
                var ue = UE.getEditor('xhy_{$name}',{
                    zIndex : 1000
                    ,initialFrameWidth:{$width} //宽度1000
                    ,initialFrameHeight:{$height} //宽度1000
                    ,imagePath:''//图片修正地址
                    ,autoHeightEnabled:false //是否自动长高,默认true
                    ,autoFloatEnabled:false //是否保持toolbar的位置不动,默认true
                    ,toolbars:$toolbars//工具按钮
                    ,enableAutoSave: true//关闭自动保存
                    ,initialStyle:'p{line-height:1em; font-size: 14px; }'
                });
            </script>
        ";
        return $str;
    }

    public function _ckeditor($attr, $content)
    {
        $style = isset($attr['style']) ? $attr['style'] : '1';
        $name = isset($attr['name']) ? $attr['name'] : "content";
        $content = isset($attr['content']) ? $attr['content'] : ''; //初始化编辑器的内容
        $width = isset($attr['width']) ? intval($attr['width']) : C("editor_width"); //编辑器宽度
        $width = '"' . $width . '"';
        $height = isset($attr['height']) ? intval($attr['height']) : C("editor_height"); //编辑器高度
        $height = '"' . $height . '"';

        $str = '';
        if(! defined('XHY_CKEDITOR')){
            $str .= '<script type="text/javascript" src="__XHY_EXTEND__/Org/ckeditor/ckeditor.js"></script>';
            define('XHY_CKEDITOR',true);
        }

        $str .= <<<EOT
                <textarea class="ckeditor" cols="20" id="{$name}" style="width:200px;" name="{$name}" width="200" height="50" rows="10">{$content}</textarea>

                <script type="text/javascript">
                    CKEDITOR.replace( '{$name}',
                        {
                            toolbar :
                            [
                                ['Source', '-', 'Save','NewPage','-','Undo','Redo','Templates'],
                                ['Find','Replace','-','SelectAll','RemoveFormat'],
                                ['Link', 'Unlink', 'Image'],
                                ['FontSize', 'Bold', 'Italic','Underline'],
                                ['NumberedList','BulletedList','-','Blockquote'],
                                ['TextColor', '-', 'Smiley','SpecialChar', '-', 'Maximize']
                            ],
                            width : $width,
                            height : $height,
                        });
                </script>

EOT;
        return $str;
    }

    //UM编辑器
    public function _uMeditor($attr){
        $name    = isset($attr['name']) ? $attr['name'] : "content";
        $content = isset($attr['content']) ? $attr['content'] : ''; //初始化编辑器的内容
        $width   = isset($attr['width']) ? intval($attr['width']) : 500; //编辑器宽度
        $height  = isset($attr['height']) ? intval($attr['height']) : 300; //编辑器高度
        $str = '';
        $str .='<script type="text/javascript" charset="utf-8" src="__PUBLIC__/lib/jquery.js"></script>';
        $str .='<link rel="stylesheet" type="text/css" href="' . __XHY_EXTEND__ . '/Org/umeditor/themes/default/css/umeditor.min.css" />';
        $str .='<script type="text/javascript" charset="utf-8" src="' . __XHY_EXTEND__ . '/Org/umeditor/umeditor.config.js"></script>';
        $str .='<script type="text/javascript" charset="utf-8" src="' . __XHY_EXTEND__ . '/Org/umeditor/umeditor.min.js"></script>';
        $str .='<script type="text/javascript" charset="utf-8" src="' . __XHY_EXTEND__ . '/Org/umeditor/lang/zh-cn/zh-cn.js"></script>';
        $str .= '<script type="text/javascript" charset="utf-8">';
        $str .='var redirectUrl = "/interface/upload_empty.html"';
        $str .='</script>';
        $str .= <<<EOT
         <script type="text/plain" id="{$name}" name="{$name}" style="width:{$width}px;height:{$height}px;">{$content}</script>
        <script type="text/javascript" charset="utf-8">
            var um = UM.getEditor('{$name}',{
                autoHeightEnabled: false  //垂直滚动条
            });
        </script>
EOT;
        return $str;
    }



}