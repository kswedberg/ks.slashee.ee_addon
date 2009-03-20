<?php

/*
=====================================================
 Plugin Slashee
-----------------------------------------------------
Author: Karl Swedberg for Fusionary Media
http://www.fusionary.com/
--------------------------------------------------------
You may use this Plugin for free as long as this
header remains intact.
========================================================
File: pi.slashee.php
--------------------------------------------------------
Purpose: Adds or removes beginning/ending slashes to a string.
=====================================================

*/


$plugin_info = array(
                 'pi_name'          => 'Slashee',
                 'pi_version'       => '0.6',
                 'pi_author'        => 'Karl Swedberg',
                 'pi_author_url'    => 'http://www.fusionary.com/',
                 'pi_description'   => 'Adds or removes beginning/ending slashes for a given string',
                 'pi_usage'         => Slashee::usage()
               );


class Slashee {

    var $return_data;

    

    function Slashee()
    {
        global $TMPL;                
        // fetch params
          $start = ($TMPL->fetch_param('start')) ? $TMPL->fetch_param('start') : false;
          $end = ($TMPL->fetch_param('end')) ? $TMPL->fetch_param('end') : false;

          $haystack = $TMPL->tagdata;
          $slasheed = trim(preg_replace('/&#47;/','/',$haystack));
          $test = $slasheed;
           
          if ($start == 'remove' && substr($slasheed,0,1)  == '/') {
            $slasheed = substr($slasheed,1);
          } elseif ($start == 'add' && !preg_match('@^(/|mailto:|callto:|http(s)?:|#)@i', $slasheed)) {
            $slasheed = '/' . $slasheed;
          }

          if ($end == 'remove' && substr($slasheed,-1) == '/') {
            $slasheed = substr($slasheed,0,-1);
          } 
          // if the user:
          // a. wants to add a slash
          // b. there isn't one at the end already, 
          // c. the string doesn't start with mailto, callto, or # ...
          elseif (  $end == 'add' && 
              substr($slasheed,-1) != '/' && 
              !preg_match('@^(mailto:|callto:|#)@i', $slasheed) 
            ) {
          // add a slash if it doesn't end with a dot plus a 2-4 letter extension
            if (!preg_match('@\.\w{2,4}$@i', $slasheed)) {
              $slasheed .= '/';
          //otherwise, add a slash if it starts with http: (or https:) and it has only two slashes
            } elseif (preg_match('@^https?:@i', $slasheed) && substr_count($slasheed, '/') == 2) {
              $slasheed .= '/' . 'http';
            }
          }
          
          $this->return_data = $slasheed;
    }
    // END
    
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.

function usage()
{
ob_start(); 
?>
This plugin will either add or remove the leading or trailing slash from a string. It's most useful when dealing with URLs. It has 2 paramaters: start and end. Each one can take either "add" or "remove" as its value.

*** EXAMPLES ***

# Remove leading slash:

{exp:slashee start="remove"}
  /path/to/file
{/exp:slashee}

Result: path/to/file

# Add leading slash and remove trailing slash:

{exp:slashee start="add" end="remove"}
  path/to/file/
{/exp:slashee}

Result: /path/to/file

# Add leading and trailing slashes:

{exp:slashee start="add" end="add"}
  path/to/file/
{/exp:slashee}

Result: /path/to/file/




<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
// END

}
// END CLASS


?>