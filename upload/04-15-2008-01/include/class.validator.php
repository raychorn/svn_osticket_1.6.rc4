<?php
/*********************************************************************
    class.validator.php

    Input validation helper. This class contains collection of functions used for data validation.
   

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006,2007,2008 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/
class Validator {

    var $input=array();
    var $fields=array();
    var $errors=array();

    function Validator($fields=null) {
        $this->setFields($fields);
    }
    function setFields(&$fields){

        if($fields && is_array($fields)):
            $this->fields=$fields;
            return (true);
        endif;
        
        return (false);
    }
   
   
    function validate($source){

        $this->errors=array();
        //Check the input and make sure the fields are specified.
        if(!$source || !is_array($source))
            $this->errors['err']='Invalid input';
        elseif(!$this->fields || !is_array($this->fields))
            $this->errors['err']='No fields setup';
        //Abort on error
        if($this->errors)
            return false;
      
        $this->input=$source;

        //Do the do.
        foreach($this->fields as $k=>$field){
            if(!$field['required'] && !$this->input[$k]) //NOT required...and no data provided...
                continue;
            
            if($field['required'] && !isset($this->input[$k]) || (!$this->input[$k] && $field['type']!='int')){ //Required...and no data provided...
                $this->errors[$k]=$field['error'];
                continue;
            }
            //Do the actual validation based on the type.
            switch(strtolower($field['type'])):
            case 'integer':
            case 'int':
                if(!is_numeric($this->input[$k]))
                     $this->errors[$k]=$field['error'];
                break;  
            case 'double':
                if(!is_numeric($this->input[$k]))
                $this->errors[$k]=$field['error'];
            break;
            case 'text':
            case 'string':
                break;
            case 'array':
                if(!$this->input[$k] || !is_array($this->input[$k]))
               $this->errors[$k]=$field['error'];
                break;
            case 'radio':
            if(!isset($this->input[$k]))
               $this->errors[$k]=$field['error'];
            break;
            case 'date': //TODO...make sure it is really in GNU date format..
                break;
            case 'time': //TODO...make sure it is really in GNU time format..
                break;
            case 'phone':
            case 'fax':
                if(!$this->is_phone($this->input[$k]))
                    $this->errors[$k]=$field['error'];
                break;
            case 'email':
                if(!$this->is_email($this->input[$k]))
                    $this->errors[$k]=$field['error'];
                break;
            case 'url':
                if(!$this->is_url($this->input[$k]))
                    $this->errors[$k]=$field['error'];
                break;
            case 'password':
                if(strlen($this->input[$k])<5)
                    $this->errors[$k]=$field['error'].' (5 chars min)';
                break;
            case 'username':
                if(strlen($this->input[$k])<3)
                    $this->errors[$k]=$field['error'].' (3 chars min)';
                break;
            case 'zipcode':
                if(!is_numeric($this->input[$k]) || (strlen($this->input[$k])!=5))
                    $this->errors[$k]=$field['error'];   
                break;
            default://If param type is not set...or handle..error out...
                $this->errors[$k]=$field['error'].' (type not set)';
            endswitch;
        }
        return ($this->errors)?(FALSE):(TRUE);
    }
   
    function iserror(){
        return $this->errors?true:false;
    }
   
    function errors(){
        return $this->errors;
    }
   
    /* Functione below can be called directly without class instance. Validator::func(var..); */
    function is_email($email) {
        return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$",trim($email));
    }
    function is_phone($phone) {
        $stripped=eregi_replace("(\(|\)|\-|\+)","",ereg_replace("([  ]+)","",$phone));
        return (!is_numeric($stripped) || ((strlen($stripped)<7) || (strlen($stripped)>13)))?false:true;
    }
    
    function is_url($url) { //Thanks to 4ice for the fix.
        $urlregex = "^(https?)\:\/\/";
        // USER AND PASS (optional) 
        $urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; 
        // HOSTNAME OR IP 
        $urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*";  // http://x = allowed (ex. http://localhost, http://routerlogin) 
        //$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)+";  // http://x.x = minimum 
        //$urlregex .= "([a-z0-9+\$_-]+\.)*[a-z0-9+\$_-]{2,3}";  // http://x.xx(x) = minimum 
        //use only one of the above 
        // PORT (optional) 
        $urlregex .= "(\:[0-9]{2,5})?"; 
        // PATH  (optional) 
        $urlregex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; 
        // GET Query (optional) 
        $urlregex .= "(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?"; 
        // ANCHOR (optional) 
        $urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?\$"; 
        
        return eregi($urlregex, $url)?true:false; 
    }


    function is_ip($ip) {
      
        if(!$ip or empty($ip))
            return false;
      
        $ip=trim($ip);
        if(ereg("^[0-9]{1,3}(.[0-9]{1,3}){3}$",$ip)) {
            foreach(explode(".", $ip) as $block)
                if($block<0 || $block>255 )
                    return false;
            return true;
        }
        return false;
    }
}
?>
