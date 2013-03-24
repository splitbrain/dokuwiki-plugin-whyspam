<?php
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'admin.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_whyspam extends DokuWiki_Admin_Plugin {

    /**
     * access for managers
     */
    function forAdminOnly(){
        return false;
    }

    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 71;
    }

    /**
     * handle user request
     */
    function handle() {
    }

    /**
     * output appropriate html
     */
    function html() {
        global $config_cascade;

        echo $this->locale_xhtml('intro');
        echo '<form method="post" action="" class="whyspam">';
        echo '<fieldset>';
        echo '<legend>'.$this->getLang('paste').'</legend>';
        echo '<textarea name="whyspam" class="edit"></textarea><br />';
        echo '<input type="submit" class="button" />';
        echo '</fieldset>';
        echo '</form>';

        $found = array();
        if($_REQUEST['whyspam']){
            foreach($config_cascade['wordblock'] as $bla => $files){
                foreach($files as $file){
                    $found = array_merge($found,$this->_checkit($file,$_REQUEST['whyspam']));
                }
            }

            echo '<div class="level2">';
            if(count($found)){
                echo '<p>'.$this->getLang('found').'</p>';
                echo '<ul>';
                foreach($found as $f){
                    echo '<li><div class="li">'.hsc($f[0]).':'.$f[1].'<br /><code>'.hsc($f[2]).'</code><br /><i>'.hsc($f[3]).'</i></div></li>';
                }
                echo '</ul>';
            }else{
                echo '<p>'.$this->getLang('notfound').'</p>';
            }
            echo '</div>';
        }

    }


    function _checkit($file,&$text){
        $found = array();

        $blockfile = (array) @file($file);
        $i=0;
        $text = preg_replace('!(\b)(www\.[\w.:?\-;,]+?\.[\w.:?\-;,]+?[\w/\#~:.?+=&%@\!\-.:?\-;,]+?)([.:?\-;,]*[^\w/\#~:.?+=&%@\!\-.:?\-;,])!i','\1http://\2 \2\3',$text);
        foreach($blockfile as $block){
            $i++;
            $block = preg_replace('/#.*$/','',$block);
            $block = trim($block);
            if(empty($block)) continue;
            if(preg_match('#('.$block.')#si',$text,$matches)){
                $pos = strpos($text,$matches[0]);
                $s = max(0,$pos - 15);
                $l = strlen($matches[0])+30;
                $snip = substr($text,$s,$l);

                $found[] = array($file, $i, $block, $snip);
            }
        }
        return $found;
    }


}
//Setup VIM: ex: et ts=4 :
