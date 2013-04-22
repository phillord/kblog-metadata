<?php

class kblog_date{
    var $kblog_opt_short_date = "_kblog_date_short_date";
    var $date;
    function __construct(){
        add_shortcode('date',array($this,'date_shortcode'));
        add_filter('the_content',array($this,'process_date_results'),12);
    }

    function date_shortcode($atts,$content){
        $this->date = $content;
        return "";
    }

    function process_date_results($content){
        // this isn't working properly
        $this->store_short_date_as_meta(strtotime($this->date));
        $this->date = NULL;
        return $content;
    }

    function store_short_date_as_meta($date){
        $this->store_date_as_meta($this->kblog_opt_short_date,$date);
    }

    function store_date_as_meta($slug,$date){
        $postid = get_the_ID();
        $stored_date = $this->get_date_from_meta($slug,$postid);
        if($date==$stored_date){
            return;
        }
        delete_post_meta($postid,$slug);
        add_post_meta($postid,$slug,$date);

    }
    function get_short_date_from_meta($postid){
        return $this->get_date_from_meta($this->kblog_opt_short_date,$postid);
    }
    function get_date_from_meta($slug,$postid){
        // true -- single value, which in practice means a deserialized array.
        $date = get_post_meta( $postid, $slug, true );
        return $date;
    }

}

/*
 * Returns the time of the post. Must be used inside the loop
 */
function kblog_date_get_the_time($format){
    global $kblog_date;
    //short authors first
    if($kblog_date->get_short_date_from_meta(get_the_id())){
        return date($format,$kblog_date->
                    get_short_date_from_meta(get_the_id()));
    }
    // all the fall back stuff here
    return get_the_time($format);
}

global $kblog_date;
$kblog_date=new kblog_date();

?>