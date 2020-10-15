<?php
/*********************************************************************
    main.inc.php

    Master include file which must be included at the start of every file.
    The brain of the whole sytem. Don't monkey with it.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006,2007,2008 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/    
    
    #Disable direct access.
    if(!strcasecmp(basename($_SERVER['SCRIPT_NAME']),basename(__FILE__))) die('kwaheri rafiki!');

    /************ START SECURITY SETTINGS *****************/
    #Disable Globals if enabled....before loading config info
    if(ini_get('register_globals')) {
       ini_set('register_globals',0);
       foreach($_REQUEST as $key=>$val)
           if(isset($$key))
               unset($$key);
    }
    #Disable url fopen && url include
    ini_set('allow_url_fopen', 0);
    ini_set('allow_url_include', 0);
    #Disabling magic quotes...we will habdle the cleanup via db_input ( avoiding false system security).
    ini_set('magic_quotes_gpc', 0);
    #Disable session ids on url.
    ini_set('session.use_trans_sid', 0);
    #No cache
    ini_set('session.cache_limiter', 'nocache');
    #Error reporting...Good idea to ENABLE error reporting to a file. i.e display_errors should be set to false
    error_reporting(E_ALL ^ E_NOTICE); //Respect whatever is set in php.ini (sysadmin knows better??)
    #Don't display errors
    ini_set('display_errors',0);
    ini_set('display_startup_errors',0);
       
    /******************END SECURITY *********************/

    //Start the session
    session_start();
    
    if(!defined('ROOT_PATH')) define('ROOT_PATH','./'); //root path. Damn directories
   
    #load config info
    require('ostconfig.php');
    
    #make sure setup dir is removed (not a must do...but for good measure do forced delete?) 
    if(0 && file_exists(str_replace('\\\\', '/', realpath(dirname(__FILE__))).'/setup/')){
        die("<b>Setup dir must be deleted following install<b>");
    }
    
   #Set Dir constants
   define('ROOT_DIR',str_replace('\\\\', '/', realpath(dirname(__FILE__))).'/'); #Get real path for root dir ---linux and windows
   define('INCLUDE_DIR',ROOT_DIR.'include/'); //Change this if include is moved outside the web path.
   define('PEAR_DIR',INCLUDE_DIR.'pear/');
   
   
    /*--------Do NOT monkey with anything else beyond this point ----------*/
   //Path separator
    if(!defined('PATH_SEPARATOR')){
        if(strpos($_ENV['OS'],'Win')!==false || (strtoupper(substr(PHP_OS, 0, 3))==='WIN'))
            define('PATH_SEPARATOR', ';' ); //Windows
        else 
            define('PATH_SEPARATOR',':'); //Linux
    }
    //Set include paths. Mainly useful for pear packages..
    ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).PATH_SEPARATOR.INCLUDE_DIR.PATH_SEPARATOR.PEAR_DIR);

   
    #CURRENT EXECUTING SCRIPT. TODO: Improve...it can be more complicated!
    define('THISPAGE',basename($_SERVER['SCRIPT_NAME']));

    #include required files
    require(INCLUDE_DIR.'mysql.php');
    require(INCLUDE_DIR.'class.usersession.php');
    require(INCLUDE_DIR.'class.pagenate.php'); //Pagenate helper!
    require(INCLUDE_DIR.'class.config.php'); //Config helper
    require(INCLUDE_DIR.'class.misc.php');
    require(INCLUDE_DIR.'class.http.php');
    require(INCLUDE_DIR.'class.format.php'); //format helpers
    require(INCLUDE_DIR.'class.validator.php'); //Class to help with basic form input validation...please help improve it.
   
    #pagenation default
    define('PAGE_LIMIT',20);

    #Session related
    define('SESSION_SECRET', TABLE_PREFIX.$_SERVER['REMOTE_ADDR']); //useless crap..TODO: remove hash 
    define('SESSION_TTL', 86400); // Default 24 hours
   
    define('DEFAULT_PRIORITY_ID',1);
    define('EXT_TICKET_ID_LEN',6); //increate it when you start getting collisions. Applies only on random ticket ids.

    #Tables being used sytem wide
    define('CONFIG_TABLE',TABLE_PREFIX.'config');
    define('STAFF_TABLE',TABLE_PREFIX.'staff');
    define('DEPT_TABLE',TABLE_PREFIX.'department');
    define('TOPIC_TABLE',TABLE_PREFIX.'help_topic');
    define('GROUP_TABLE',TABLE_PREFIX.'groups');
   
    define('TICKET_TABLE',TABLE_PREFIX.'ticket');
    define('TICKET_NOTE_TABLE',TABLE_PREFIX.'ticket_note');
    define('TICKET_MESSAGE_TABLE',TABLE_PREFIX.'ticket_message');
    define('TICKET_RESPONSE_TABLE',TABLE_PREFIX.'ticket_response');
    define('TICKET_ATTACHMENT_TABLE',TABLE_PREFIX.'ticket_attachment');
    define('TICKET_PRIORITY_TABLE',TABLE_PREFIX.'ticket_priority');
    define('TICKET_LOCK_TABLE',TABLE_PREFIX.'ticket_lock');
  
    define('EMAIL_TABLE',TABLE_PREFIX.'email');
    define('POP3_TABLE',TABLE_PREFIX.'email_pop3');
    define('EMAIL_TEMPLATE_TABLE',TABLE_PREFIX.'email_template');
    define('BANLIST_TABLE',TABLE_PREFIX.'email_banlist');
  
   
    define('TIMEZONE_TABLE',TABLE_PREFIX.'timezone'); 
   
    #Connect to the DB && get configuration from database
    $ferror=null;
    $cfg= new Config();
    if (!db_connect(DBHOST,DBUSER,DBPASS) || !db_select_database(DBNAME)) {
        $ferror='Unable to connect to the DB';
    }elseif(!$cfg->load(1)){
        $ferror='Unable to load config info';
    }

    if($ferror){ //Fatal error
        if(defined(ADMIN_EMAIL) && Validator::is_email(ADMIN_EMAIL))
            Misc::sendmail(ADMIN_EMAIL,'Fatal DB Error',$ferror,ADMIN_EMAIL);
        die("<b>Fatal Error:</b> Contact site admin.");
        exit;
    }
    //Set default timezone...staff will overwrite it.
    list($mysqltz)=db_fetch_row(db_query('SELECT @@session.time_zone '));
    $cfg->setMysqlTZ($mysqltz);
    $_SESSION['TZ_OFFSET']=$cfg->getTZoffset();
    $_SESSION['daylight']=$cfg->observeDaylightSaving();
?>
