<?php
/**
* rtl433
* @package project
* @author Wizard <sannikovdi@yandex.ru>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 13:03:10 [Mar 13, 2016])
*/
//
//
class rtl433 extends module {
/**
*
* Module class constructor
*
* @access private
*/
function rtl433() {
  $this->name="rtl433";
  $this->title="RTL433";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $this->checkSettings();


  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 $out['ACCESS_KEY']=$this->config['ACCESS_KEY'];
 $out['DISABLED']=$this->config['DISABLED'];
 $out['SLACK_TEST']=SETTINGS_SLACK_APIURL;	

 if ($this->view_mode=='update_settings') {
   global $access_key;
//   $this->config['ACCESS_KEY']=$access_key;
 	global $speaker;
//   $this->config['SPEAKER']=$speaker;
	global $emotion;
//   $this->config['EMOTION']=$emotion;
   global $disabled;
//   $this->config['DISABLED']=$disabled;
   $this->saveConfig();
   $this->redirect("?ok=1");
 }

 if ($_GET['ok']) {
  $out['OK']=1;
 }
}

/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
function checkSettings() {

 // Здесь задаются нужные нам параметры - пример взят из календаря, как раз есть текстбокс и радиобуттон 
  $settings=array(
   array(
    'NAME'=>'RTL433_APIURL', 
    'TITLE'=>'Incoming Webhook API Url: (*)', 
    'TYPE'=>'text',
    'DEFAULT'=>'https://hooks.slack.com/services/xxxx/'
    ),
  
  array(
    'NAME'=>'RTL433_ENABLE', 
    'TITLE'=>'Enable',
    'TYPE'=>'yesno',
    'DEFAULT'=>'1'
    )
   );
   foreach($settings as $k=>$v) {
    $rec=SQLSelectOne("SELECT ID FROM settings WHERE NAME='".$v['NAME']."'");
    if (!$rec['ID']) {
     $rec['NAME']=$v['NAME'];
     $rec['VALUE']=$v['DEFAULT'];
     $rec['DEFAULTVALUE']=$v['DEFAULT'];
     $rec['TITLE']=$v['TITLE'];
     $rec['TYPE']=$v['TYPE'];
     $rec['DATA']=$v['DATA'];
     $rec['ID']=SQLInsert('settings', $rec);
     Define('SETTINGS_'.$rec['NAME'], $v['DEFAULT']);
    }
   }
}
	
 

/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {

  parent::install();
 }

 function uninstall() {
   parent::uninstall();
  SQLExec('DROP TABLE IF EXISTS rtl433_devices');
  SQLExec('delete from settings where NAME like "%RTL433%"');

 }



 function dbInstall($data = '') {

 $data = <<<EOD
 rtl433_devices: ID int(10) unsigned NOT NULL auto_increment
 rtl433_devices: TITLE varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: IP varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: PORT varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: MAC varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: ONLINE varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: LASTPING varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: CURRENTCOLOR varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: FIND varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);

}


// --------------------------------------------------------------------



}
/*
*
* TW9kdWxlIGNyZWF0ZWQgTWFyIDEzLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
