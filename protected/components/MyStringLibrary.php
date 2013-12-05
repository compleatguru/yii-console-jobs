<?php
/**
 * Description of MyStringLibrary
 *
 * @author David
 */
class MyStringLibrary {
    // buggy
    public static function minify($str,$minify=true){
        if(!$minify) return $str;
        return str_replace('  ', '',str_replace(array("\n","\r"), '', $str));
    }

    public static function mtrim($item){
        if(is_string($item)) return trim($item);
        return $item;
    }
}
