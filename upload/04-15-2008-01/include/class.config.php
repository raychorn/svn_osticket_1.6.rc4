<?php
/*********************************************************************
    class.config.php

    osTicket config info manager. 

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006,2007,2008 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/

class Config {
    
    var $id=0;
    var $mysqltzoffset=0;
    var $config=array();

    var $defaultDept;   //Default Department    
    var $defaultEmail;  //Default Email 

    function Config() { 
    }

    function load($id) {
        if($id && is_numeric($id)):
         $this->id=$id;
         $this->config=array();
         $res=db_query('SELECT * FROM '.CONFIG_TABLE.' WHERE id='.$id);
         if($res && db_num_rows($res))
            $this->config=db_fetch_array($res); 
        endif;
        return $this->config?true:false;
    }
    
    function reload() {
        $this->load($this->id);
    }

    function isHelpDeskOffline() {
        return $this->config['isonline']?false:true;
    }

    function getAPIKey() {
        return $this->config['api_key'];
    }

    function isKnownHost($ip) {
        $allowed=$this->config['api_whitelist']?array_filter(explode(',',ereg_replace(' ','',$this->config['api_whitelist']))):null;
        return ($ip && is_array($allowed) && in_array($ip,array_map('trim',$allowed)))?TRUE:FALSE;
    }

    function setMysqlTZ($tz){
        //TODO: Combine the 2 replace regex 
        $this->mysqltzoffset=($tz=='SYSTEM')?preg_replace('/([+-]\d{2})(\d{2})/','\1',date('O')):preg_replace('/([+-]\d{2})(:)(\d{2})/','\1',$tz);
    }
    
    function getMysqlTZoffset() {
        return $this->mysqltzoffset;
    }

    /* Date & Time Formats */
    function observeDaylightSaving() {
        return $this->config['enable_daylight_saving']?true:false;
    }
    function getTimeFormat(){
        return $this->config['time_format'];
    }
    function getDateFormat(){
        return $this->config['date_format'];
    }

    function getDateTimeFormat(){
        return $this->config['datetime_format'];
    }

    function getDayDateTimeFormat(){
        return $this->config['daydatetime_format'];
    }

    function getId() {
        return $this->config['id'];
    }
   
    function getTitle() {
        return $this->config['helpdesk_title'];
    }
    
    function getUrl() {
        return $this->config['helpdesk_url'];        
    }
    
    function getBaseUrl(){ //Same as above with no trailing slash.
        return rtrim($this->getUrl(),'/');
    }

    function getConfig() {
        return $this->config;
    }

    function getTZOffset(){
        return $this->config['timezone_offset'];
    }

    function getPageSize() {
        return $this->config['max_page_size'];
    }

    function getGracePeriod() {
        return $this->config['overdue_grace_period'];
    }
    
    function getClientTimeout() {
        return $this->config['client_session_timeout']*60;
    }

    function getStaffTimeout() {
        return $this->config['staff_session_timeout']*60;
    }


    function getLockTime() {
        return $this->config['autolock_minutes'];
    }

    function getDefaultDeptId(){
        return $this->config['default_dept'];
    }

    function getDefaultDept(){

        if(!$this->defaultDept && $this->getDefaultDeptId())
            $this->defaultDept= new Dept($this->getDefaultDeptId());
        return $this->defaultDept;
    }   

    function getDefaultEmailId(){
        return $this->config['default_dept'];
    }

    function getDefaultEmail(){

        if(!$this->defaultEmail && $this->getDefaultEmailId())
            $this->defaultEmail= new Email($this->getDefaultEmailId());
        return $this->defaultEmail;
    }

    function getDefaultEmailAddress() {
        $email=$this->getDefaultEmail();
        
        return $email?$email->getAddress():$this->getNoReplyEmail();
    }

    function getDefaultPriorityId(){
        return $this->config['default_priority'];
    }

    function getDefaultTemplateId() {
        return $this->config['default_template'];
    }

    function getMaxOpenTickets() {
         return $this->config['max_open_tickets'];
    }

    function getMaxFileSize(){
        return $this->config['max_file_size'];
    }

    function clickableURLS() {
        return $this->config['clickable_urls']?true:false;
    }
    
    
    function popAutoFetch() {
        return $this->config['enable_pop3_fetch']?true:false;
    }


    function enableAutoCron() {
        return $this->config['enable_auto_cron']?true:false;
    }
        

    function enableEmailPiping() {
        return $this->config['enable_email_piping']?true:false;
    }

    function allowPriorityChange() {
        return $this->config['allow_priority_change']?true:false;
    }

        
    function useEmailPriority() {
        return $this->config['use_email_priority']?true:false;
    }

    function getNoReplyEmail(){
         return $this->config['noreply_email'];
    }

    function getAlertEmail(){
         return $this->config['alert_email'];
    }

    function getAdminEmail(){
         return $this->config['admin_email'];
    }


    function getReplySeparator() {
        return $this->config['reply_separator'];
    }
  
    function stripQuotedReply() {
        return $this->config['strip_quoted_reply']?true:false;
    }

    function saveEmailHeaders() {
        return $this->config['save_email_headers']?true:false;
    }
    
    function useRandomIds() {
        return $this->config['random_ticket_ids']?true:false;
    }

    /* autoresponders  & Alerts */
    function autoRespONNewTicket() {
        return $this->config['ticket_autoresponder']?true:false;
    }
    
    function autoRespONNewMessage() {
        return $this->config['message_autoresponder']?true:false;
    }
    function alertONNewMessage() {
        return $this->config['message_alert_active']?true:false;
    }

    function alertLastRespondentONNewMessage() {
        return $this->config['message_alert_laststaff']?true:false;
    }
   
    function alertAssignedONNewMessage() {
        return $this->config['message_alert_assigned']?true:false;
    }
    
    function alertDeptManagerONNewMessage() {
        return $this->config['message_alert_dept_manager']?true:false;
    }
        
    function alertONNewTicket() {
        return $this->config['ticket_alert_active']?true:false;
    }

    function alertAdminONNewTicket() {
        return $this->config['ticket_alert_admin']?true:false;
    }
     
    function alertDeptManagerONNewTicket() {
        return $this->config['ticket_alert_dept_manager']?true:false;
    }

    function alertDeptMembersONNewTicket() {
        return $this->config['ticket_alert_dept_members']?true:false;
    }

    function alertONOverdueTicket() {
        return $this->config['overdue_alert_active']?true:false;
    }

    function alertAssignedONOverdueTicket() {
        return $this->config['overdue_alert_assigned']?true:false;
    }

    function alertDeptManagerONOverdueTicket() {
        return $this->config['overdue_alert_dept_manager']?true:false;
    }

    function alertDeptMembersONOverdueTicket() {
        return $this->config['overdue_alert_dept_members']?true:false;
    }

    function autoAssignReopenedTickets() {
        return $this->config['auto_assign_reopened_tickets']?true:false;
    }

    function showAssignedTickets() {
        return $this->config['show_assigned_tickets']?true:false;
    }
    
    function sendOverLimitNotice() {
        return $this->config['overlimit_notice_active']?true:false;
    }
        
    /* Error alerts sent to admin email when enabled */
    function alertONSQLError() {
        return $this->config['send_sql_errors']?true:false;                    
    }
    function alertONLoginError() {
        return $this->config['send_login_errors']?true:false;
    }

    function alertONMailParseError() {
        return $this->config['send_mailparse_errors']?true:false;
    }

    

    /* Attachments */

    function emailAttachments() {
        return $this->config['email_attachments']?true:false;
    }

    function allowAttachments() {
        return $this->config['allow_attachments']?true:false;
    }

    function allowOnlineAttachments() {
        return ($this->allowAttachments() && $this->config['allow_online_attachments'])?true:false;
    }

    function allowAttachmentsOnlogin() {
        return ($this->allowOnlineAttachments() && $this->config['allow_online_attachments_onlogin'])?true:false;
    }
    
    function allowEmailAttachments() {
        return ($this->allowAttachments() && $this->config['allow_email_attachments'])?true:false;
    }

    function getUploadDir() {
        return $this->config['upload_dir'];
    }
    
    //simply checking if destination dir is usable..nothing to do with permission to upload!
    function canUploadFiles() {   
        $dir=$this->config['upload_dir'];
        return ($dir && is_writable($dir))?TRUE:FALSE;
    }

    function canUploadFileType($filename) {       
        $ext = strtolower(preg_replace("/.*\.(.{3,4})$/", "$1", $filename));
        $allowed=$this->config['allowed_filetypes']?array_map('trim',explode(',',strtolower($this->config['allowed_filetypes']))):null;
        return ($ext && is_array($allowed) && in_array(".$ext",$allowed))?TRUE:FALSE;
    }

    function updatePref($var,&$errors) {
      
        if(!$var || $errors)
            return false;
        
        $f=array();
        $f['helpdesk_url']=array('type'=>'string',   'required'=>1, 'error'=>'Helpdesk URl required'); //TODO: Add url validation
        $f['helpdesk_title']=array('type'=>'string',   'required'=>1, 'error'=>'Helpdesk title required');
        $f['default_dept']=array('type'=>'int',   'required'=>1, 'error'=>'Default Dept. required');
        $f['default_email']=array('type'=>'int',   'required'=>1, 'error'=>'Default email required');
        $f['default_template']=array('type'=>'int',   'required'=>1, 'error'=>'You must select template.');
        $f['staff_session_timeout']=array('type'=>'int',   'required'=>1, 'error'=>'Enter idle time in minutes');
        $f['client_session_timeout']=array('type'=>'int',   'required'=>1, 'error'=>'Enter idle time in minutes');
        $f['time_format']=array('type'=>'string',   'required'=>1, 'error'=>'Time format required'); //TODO: Add date format validation  
        $f['date_format']=array('type'=>'string',   'required'=>1, 'error'=>'Date format required');
        $f['datetime_format']=array('type'=>'string',   'required'=>1, 'error'=>'Datetime format required');
        $f['daydatetime_format']=array('type'=>'string',   'required'=>1, 'error'=>'Day, Datetime format required');
        $f['noreply_email']=array('type'=>'email',   'required'=>1, 'error'=>'Valid email required');
        $f['alert_email']=array('type'=>'email',   'required'=>1, 'error'=>'Valid email required');
        $f['admin_email']=array('type'=>'email',   'required'=>1, 'error'=>'Valid email required');
        $f['autolock_minutes']=array('type'=>'int',   'required'=>1, 'error'=>'Enter lock time in minutes');
        //TODO: check option fields for validity.

        //do the validation.
        $val = new Validator();        
        $val->setFields($f);
        if(!$val->validate($var)){
            $errors=array_merge($errors,$val->errors());                                        
        }
                        
        if($_POST['ticket_alert_active'] 
                && (!isset($_POST['ticket_alert_admin']) 
                    && !isset($_POST['ticket_alert_dept_manager'])
                    && !isset($_POST['ticket_alert_dept_members']))){        
            $errors['ticket_alert_active']='No target recipient(s) selected';
        }       
        if($_POST['message_alert_active']
                && (!isset($_POST['message_alert_laststaff'])
                    && !isset($_POST['message_alert_assigned'])
                    && !isset($_POST['message_alert_dept_manager']))){
            $errors['message_alert_active']='No target recipient(s) selected';
        }
        if($_POST['strip_quoted_reply'] && !$_POST['reply_separator'])
            $errors['reply_separator']='Reply separator required (?)';

        if($errors) return false; //No go! 

        //We are good to go...blanket update!
        $sql= 'UPDATE '.CONFIG_TABLE.' SET isonline='.db_input($var['isonline']).
            ',timezone_offset='.db_input($var['timezone_offset']).
            ',enable_daylight_saving='.db_input(isset($var['enable_daylight_saving'])?1:0).
            ',staff_session_timeout='.db_input($var['staff_session_timeout']).
            ',client_session_timeout='.db_input($var['client_session_timeout']).
            ',max_page_size='.db_input($var['max_page_size']).
            ',max_open_tickets='.db_input($var['max_open_tickets']).
            ',autolock_minutes='.db_input($var['autolock_minutes']).
            ',overdue_grace_period='.db_input($var['overdue_grace_period']).
            ',default_email='.db_input($var['default_email']).
            ',default_dept='.db_input($var['default_dept']).
            ',default_priority='.db_input($var['default_priority']).
            ',default_template='.db_input($var['default_template']).
            ',clickable_urls='.db_input(isset($var['clickable_urls'])?1:0).
            ',allow_priority_change='.db_input(isset($var['allow_priority_change'])?1:0).
            ',use_email_priority='.db_input(isset($var['use_email_priority'])?1:0).
            ',enable_auto_cron='.db_input(isset($var['enable_auto_cron'])?1:0).
            ',enable_pop3_fetch='.db_input(isset($var['enable_pop3_fetch'])?1:0).
            ',enable_email_piping='.db_input(isset($var['enable_email_piping'])?1:0).
            ',send_sql_errors='.db_input(isset($var['send_sql_errors'])?1:0).
            ',send_mailparse_errors='.db_input(isset($var['send_mailparse_errors'])?1:0).
            ',send_login_errors='.db_input(isset($var['send_login_errors'])?1:0).
            ',save_email_headers='.db_input(isset($var['save_email_headers'])?1:0).
            ',strip_quoted_reply='.db_input(isset($var['strip_quoted_reply'])?1:0).
            ',email_attachments='.db_input(isset($var['email_attachments'])?1:0).
            ',ticket_autoresponder='.db_input($var['ticket_autoresponder']).
            ',message_autoresponder='.db_input($var['message_autoresponder']).
            ',ticket_alert_active='.db_input($var['ticket_alert_active']).
            ',ticket_alert_admin='.db_input(isset($var['ticket_alert_admin'])?1:0).
            ',ticket_alert_dept_manager='.db_input(isset($var['ticket_alert_dept_manager'])?1:0).
            ',ticket_alert_dept_members='.db_input(isset($var['ticket_alert_dept_members'])?1:0).
            ',message_alert_active='.db_input($var['message_alert_active']).
            ',message_alert_laststaff='.db_input(isset($var['message_alert_laststaff'])?1:0).
            ',message_alert_assigned='.db_input(isset($var['message_alert_assigned'])?1:0).
            ',message_alert_dept_manager='.db_input(isset($var['message_alert_dept_manager'])?1:0).
            ',overdue_alert_active='.db_input($var['overdue_alert_active']).
            ',overdue_alert_assigned='.db_input(isset($var['overdue_alert_assigned'])?1:0).
            ',overdue_alert_dept_manager='.db_input(isset($var['overdue_alert_dept_manager'])?1:0).
            ',overdue_alert_dept_members='.db_input(isset($var['overdue_alert_dept_members'])?1:0).
            ',auto_assign_reopened_tickets='.db_input(isset($var['auto_assign_reopened_tickets'])?1:0).
            ',show_assigned_tickets='.db_input(isset($var['show_assigned_tickets'])?1:0).
            ',overlimit_notice_active='.db_input(isset($var['overlimit_notice_active'])?1:0).
            ',random_ticket_ids='.db_input($var['random_ticket_ids']).
            ',time_format='.db_input($var['time_format']).
            ',date_format='.db_input($var['date_format']).
            ',datetime_format='.db_input($var['datetime_format']).
            ',daydatetime_format='.db_input($var['daydatetime_format']).
            ',reply_separator='.db_input($var['reply_separator']).
            ',noreply_email='.db_input($var['noreply_email']).
            ',alert_email='.db_input($var['alert_email']).
            ',admin_email='.db_input($var['admin_email']).
            ',helpdesk_title='.db_input($var['helpdesk_title']).
            ',helpdesk_url='.db_input($var['helpdesk_url']).
            ' WHERE id='.$this->getId();
        //echo $sql;
        return (db_query($sql))?TRUE:FALSE;
      
    }
    
}
?>
