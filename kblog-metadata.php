<?php
/*
  Plugin Name: Kblog Metadata
  Plugin URI: http://www.knowledgeblog.org
  Description: Adds metadata about posts in lots of formats
  Version: 0.1
  Author: Phillip Lord
  Author URI: http://www.knowledgeblog.org
  Email: knowledgeblog@googlegroups.com
  
  Copyright 2011 Phillip Lord (phillip.lord@newcastle.ac.uk)
  Newcastle University

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Begin COINS section */

add_filter( "the_content", "kblog_metadata_coins_content");

function kblog_metadata_coins_content($content)
{
    $authors = kblog_metadata_get_authors();
    
    $first_sentinel = true;
    $author_string = "";
    foreach( $authors as $author ){
        // the insanity that is coins treats the first author and all subsequent authors differently. 
        // The first author can be split into first name/last name. All others can't. 
        if( $first_sentinel ){
            $first_sentinel = false;
            $author_string .= "rtf.aulast=".urlencode($author['lastname']).
                "&amp;rtf.aufirst=".urlencode($author['firstname']);
            
        }
        else{
            $author_string .= "&amp;rtf.au=".
                urlencode( kblog_metadata_concat_name($author) );
        }
        
    }
    $title = urlencode( get_the_title() );
    $blogtitle = urlencode( get_bloginfo('name') );
    $time = get_the_time( 'Y-m-d' );
    $permalink = urlencode( get_permalink() );
    
    return
        "<!-- coins metadata inserted by kblog-metadata -->\n" .
        '<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc&amp;rfr_id='. 
        urlencode( "kblog-metadata.php" ) .  
        "&amp;rft.title=$title&amp;rtf.source=$blogtitle&amp;rtf.date=$time&amp;rtf.identifier=$permalink&amp;" .
        $author_string . 
        '&amp;rtf.format=text&amp;rft.language=English"></span>' .
        $content;
}



// End COINS

// Google Scholar meta tags
add_action( "wp_head", "kblog_metadata_header_metatags" );

function kblog_metadata_header_metatags(){
    echo kblog_metadata_get_metatags();
}


function kblog_metadata_get_metatags(){
    
    $metadata = '<meta name="resource_type" content="knowledgeblog"/>' . "\n";
    $metadata .= '<meta name="citation_journal_title" content="'.
        htmlentities( get_bloginfo( 'name' ) ) . '"/>' . "\n";
    
    if( is_single() ){
        $authors = kblog_metadata_get_authors();
        foreach( $authors as $author ){
            $metadata .= '<meta name="citation_author" content="'.
                htmlentities(kblog_metadata_concat_name($author)) . 
                '"/>' . "\n";
        }
        
        $metadata .= '<meta name="citation_title" content="'.
            htmlentities( kblog_metadata_get_the_title() ) .
            '"/>' . "\n";
        
    }
    
    $time = get_the_time( 'Y/m/d' );
    $metadata .= '<meta name="citation_publication_date" content="' 
        .$time . '"/>' . "\n";
    return $metadata;
}


// End Google Scholar meta tags



// Generally useful functions

function kblog_metadata_concat_name( $author ){
    return $author["firstname"] . " " . $author[ "lastname" ];
}

/* 
 * Fetch the title
 */
function kblog_metadata_get_the_title(){
    $postID = false;
    if( !get_the_ID() ){
        global $wp_query;
        $postID = $wp_query->post->ID;
    }
    return get_the_title( $postID );
}


/*
 * Fetch the authors, as an array, each with firstname, lastname.
 * $authorID is optional if used inside the loop. 
 */
function kblog_metadata_get_authors()
{
    $authorID = false;

    if( !get_the_author() ){
        // we are outside the loop so get_the_id don't work. 
        global $wp_query;
        $authorID = $wp_query->post->post_author;
    }



    $authors = array();
    // currently just using wordpress API. Add coauthors, kblog_authors later.
    $author = array
        ( 'firstname'=>get_the_author_meta( "first_name", $authorID ),
          'lastname'=>get_the_author_meta( "last_name", $authorID )
          );
    $authors[] = $author;

    /*  $authors = array( */
    /* array( */
    /*                        'firstname'=>"Phillip", */
    /*                       'lastname'=>"Lord" */
    /*                        ), */
    /*                  array( */
    /*                        'firstname'=>"John", */
    /*                        'lastname'=>"Smith" */
    /*                        ), */
    /*                  ); */

    return $authors;
}


?>