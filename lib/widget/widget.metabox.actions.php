<?php


    // Include WordPress
    require('../../../../../wp/wp-load.php');
    define('WP_USE_THEMES', false);

    // Prevent access to non-logged in users
    if ( !is_user_logged_in() ) { exit("Access denied."); }

    // Set up XML document
    $xml = "";
    
    
    if (isset($_GET['submit']))
    {
        // Set up error array
        $errors = array("account_empty"=>array(0,"<strong>Account</strong> was left blank."),
                                "account_format"=>array(0,"<strong>Account</strong> was incorrectly formatted."),
                                "style_empty"=>array(0,"<strong>Style</strong> was left blank."),
                                "style_format"=>array(0,"<strong>Style</strong> was incorrectly formatted."),
                                "reset_empty"=>array(0,"<strong>Reset</strong> was left blank."),
                                "reset_format"=>array(0,"<strong>Reset</strong> was incorrect."),
                                "autoupdate_empty"=>array(0,"<strong>Autoupdate</strong> was left blank."),
                                "autoupdate_format"=>array(0,"<strong>Autoupdate</strong> was incorrectly formatted."),
                                "post_empty"=>array(0,"<strong>Post ID</strong> was left blank."),
                                "post_format"=>array(0,"<strong>Post ID</strong> was incorrectly formatted."));
        
        
        
        /*
         
            SET DEFAULT ACCOUNT
            
        */
        
        if (isset($_GET['account']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['account']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['account'])) == 1)
                    $account = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['account']))));
                else
                    $errors['account_format'][0] = 1;
            else
                $errors['account_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("Zotpress_DefaultAccount", $account);
                $xml .= "<result success='true' account='".$account."' />\n";
            }
        } // default account
        
        
        
        /*
         
            SET AUTOUPDATE
            
        */
        
        else if (isset($_GET['autoupdate']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['autoupdate']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['autoupdate'])) == 1)
                    $autoupdate = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['autoupdate']))));
                else
                    $errors['autoupdate_format'][0] = 1;
            else
                $errors['autoupdate_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("Zotpress_DefaultAutoUpdate", strtolower($autoupdate));
                $xml .= "<result success='true' autoupdate='".strtolower($autoupdate)."' />\n";
            }
        } // autoupdate
        
        
        
        /*
         
            SET RESET
            
        */
        
        else if (isset($_GET['reset']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['reset']) != '')
                if (get_option('ZOTPRESS_PASSCODE') == $_GET['reset'])
                    $reset = $_GET['reset'];
                else
                    $errors['reset_format'][0] = 1;
            else
                $errors['reset_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                global $wpdb;
                
                // Drop all tables except accounts/main
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_oauth;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroItems;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroCollections;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."zotpress_zoteroTags;");
                
                delete_option( 'ZOTPRESS_PASSCODE' );
                delete_option( 'Zotpress_DefaultAccount' );
                delete_option( 'Zotpress_LastAutoUpdate' );
                delete_option( 'Zotpress_DefaultStyle' );
                delete_option( 'Zotpress_StyleList' );
                delete_option( 'Zotpress_DefaultAutoUpdate' );
                delete_option( 'Zotpress_main_db_version' );
                delete_option( 'Zotpress_oauth_db_version' );
                delete_option( 'Zotpress_zoteroItems_db_version' );
                delete_option( 'Zotpress_zoteroCollections_db_version' );
                delete_option( 'Zotpress_zoteroTags_db_version' );
                
                $xml .= "<result success='true' reset='complete' />\n";
            }
        } // reset
        
        
        
        /*
         
            SET DEFAULT STYLE
            
        */
        
        else if (isset($_GET['style']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['style']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['style'])) == 1)
                    $style = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['style']))));
                else
                    $errors['style_format'][0] = 1;
            else
                $errors['style_empty'][0] = 1;
            
            // Only for post-specific
            if (isset($_GET['forpost']) && $_GET['forpost'] == "true")
                if (isset($_GET['post']) && trim($_GET['post']) != '')
                    if (preg_match('/^[\'0-9]+$/', stripslashes($_GET['post'])) == 1)
                        $post = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['post']))));
                    else
                        $errors['post_format'][0] = 1;
                else
                    $errors['post_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                // Update style list
                if (strpos(get_option("Zotpress_StyleList"), $style) === false)
                    update_option( "Zotpress_StyleList", get_option("Zotpress_StyleList") . ", " . $style);
                
                // Update default style
                if (isset($_GET['forpost']) && $_GET['forpost'] == "true")
                {
                    update_option("Zotpress_DefaultStyle_".$post, $style);
                    $xml .= "<result success='true' post='".$post."' style='".$style."' />\n";
                }
                else // Overal defaults
                {
                    update_option("Zotpress_DefaultStyle", $style);
                    $xml .= "<result success='true' style='".$style."' />\n";
                }
            }
        } // default style
        
        
        // DISPLAY ERRORS
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
        }
    
    } // isset(submit)
    
    
    
    /*
     
        DISPLAY XML
        
    */

    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    echo "<options>\n";
    echo $xml;
    echo "</options>";

?>
