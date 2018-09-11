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


///////////
$cmd='ps -A|grep 433';
//$cmd='ps -A';
$answ=shell_exec($cmd);
//echo $answ;
$rez=substr_count  ($answ,'433' );
//echo $rez;
if ($rez=="1" ){
$out['CYCLERUN'] = 1;
} 
else {
$out['CYCLERUN'] = 0;
     } 
////////////


$cmd_rec = SQLSelectOne("SELECT VALUE FROM rtl433_config where parametr='JSON'");
$out['MSG_DEBUG']=$cmd_rec['VALUE'];


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

if ($this->view_mode=='start') {
$this->start();
}  

if ($this->view_mode=='stop') {
$this->stop();
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
	
 

 function start() {
$cmd='sudo modprobe -r dvb_usb_rtl28xxu';
$answ=shell_exec($cmd);
//echo $answ;

//$fname="/home/pi/433_".time().".log";

$cmd='sudo killall rtl_433';
$answ=shell_exec($cmd);
//echo $answ;

$cmd='sudo killall rtl_sdr';
$answ=shell_exec($cmd);
//echo $answ;


// {"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}
//  wget -O http://dmshome:662583abca@192.168.1.39/rtl433.php?json={"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}
//curl -X POST http://example.com/some/path -d
//$cmd='/home/pi/rtl_433_rcswitch/build/src/rtl_433 -R 19 -R 1 -R 30 -f 433920000 -s 250000 -F json|mosquitto_pub -h localhost -t /home/rtl_433  -l';
///$cmd2='wget -O http://dmshome:662583abca@192.168.1.39/rtl433.php?json=';
//$cmd='/home/pi/rtl_433_rcswitch/build/src/rtl_433 -R 19 -R 1 -R 30 -f 433920000 -s 250000 -F json|xargs '.$cmd2.';

//$cmd="/home/pi/rtl_433_rcswitch/build/src/rtl_433 -R 19 -R 1 -R 30 -f 433920000 -s 250000 -F json|xargs wget -O http://dmshome:662583abca@192.168.1.39/rtl433.php?json=";
//$cmd="/home/pi/rtl_433_rcswitch/build/src/rtl_433 -R 19 -R 1 -R 30 -f 433920000 -s 250000 -F json|xargs curl -X POST http://dmshome:662583abca@192.168.1.39/rtl433.php?json=";

//curl -X POST http://dmshome:662583abca@192.168.1.39/rtl433.php?json="{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}"
//wget -P  http://dmshome:662583abca@192.168.1.39/rtl433.php?json='{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}'

//curl -XPOST http://dmshome:662583abca@192.168.1.39/rtl433.php?json='{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}'

//echo {"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}|php -r "echo urlencode('$1');" |echo 


//echo {"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}|uri_escape(){ echo -E "$@" | sed 's/\\/\\\\/g;s/./&\n/g' | while read -r i; do echo $i | grep -q '[a-zA-Z0-9/.:?&=]' && echo -n "$i" || printf %%%x \'"$i" done }

//echo "{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}" | sed 's/ /%20/g;s/!/%21/g;s/"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g'|xargs wget -O http://dmshome:662583abca@192.168.1.39/rtl433.php?json=

//echo "{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}" | sed 's/ /%20/g;s/!/%21/g;s/"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g'|xargs wget   http://dmshome:662583abca@192.168.1.39/rtl433.php?json=
/////////////////// нижний работает
//echo "{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}" | sed 's/ /%20/g;s/!/%21/g;s/"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g'| echo http://dmshome:662583abca@192.168.1.39/rtl433.php?json="$@"

//echo "http://dmshome:662583abca@192.168.1.39/rtl433.php?json={"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}" | sed 's/ /%20/g;s/!/%21/g;s/"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g'

//echo "{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}"| sed 's,", ,g'
//echo "{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}" | sed 's/ /%20/g;s/!/%21/g;s/"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g;'s,^,http://dmshome:662583abca@192.168.1.39/rtl433.php?json=,

//curl -G -v "http://dmshome:662583abca@192.168.1.39/rtl433.php?json={"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}" --data-urlencode "msg=hello world" --data-urlencode "msg2=hello world2"

////////////

//echo "{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}" | sed 's/ /%20/g;s/!/%21/g;s/"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g'
//////////.......
//$cmd="/home/pi/rtl_433_rcswitch/build/src/rtl_433 -R 19 -R 1 -R 30 -f 433920000 -s 250000 -F json| sed 's/ /%20/g;s/!/%21/g;s/\"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g;'s,^,http://dmshome:662583abca@192.168.1.39/rtl433.php?json=,|xargs wget ";
$cmd="/home/pi/rtl_433_rcswitch/build/src/rtl_433 -R 19 -R 1 -R 30 -f 433920000 -s 250000 -F json| sed 's/ /%20/g;s/!/%21/g;s/\"/%22/g;s/#/%23/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g;'s,^,http://dmshome:662583abca@192.168.1.39/rtl433.php?json=,|xargs wget ";
//echo "{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}" | sed 's/ /%20/g;s/!/%21/g;s/"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g;'s,^,http://dmshome:662583abca@192.168.1.39/rtl433.php?json=,
//echo "{"time" : "2018-09-08 15:16:31", "model" : "Nexus Temperature/Humidity", "id" : 82, "battery" : "LOW", "channel" : 3, "temperature_C" : 31.700, "humidity" : 37}"  sed 's/ /%20/g;s/!/%21/g;s/"/%22/g;s/#/%23/g;s/\$/%24/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g;'s,^,http://dmshome:662583abca@192.168.1.39/rtl433.php?json=,|xargs wget 
//////////.......

//$cmd='rtl_433 -f 433920000 -s 250000 -F json|mosquitto_pub -h localhost -t /home/rtl_433  -l';
//$answ=shell_exec($cmd);
$answ=exec($cmd ." > /dev/null 2>&1 &");

SQLexec("update rtl433_config set VALUE='$answ' where parametr='WORK'");
//echo $answ;


//$url = BASE_URL . '/rtl433.php?json=' . gg($objn.'.lat');
//getURL($url, 0);



 }

 function stop() {
echo "stopping";
$cmd='sudo killall rtl_433 ';
$answ=shell_exec($cmd);
echo $answ;

$cmd='sudo killall rtl_sdr ';
$answ=shell_exec($cmd);
echo $answ;

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

  $data = <<<EOD
 rtl433_config: parametr varchar(300)
 rtl433_config: value varchar(10000)  
EOD;
   parent::dbInstall($data);



$par['parametr'] = 'EVERY';
$par['value'] = 30;		 
SQLInsert('rtl433_config', $par);				
	
$par['parametr'] = 'LASTCYCLE_TS';
$par['value'] = "0";		 
SQLInsert('rtl433_config', $par);						
		
$par['parametr'] = 'LASTCYCLE_TXT';
$par['value'] = "0";		 
SQLInsert('rtl433_config', $par);						

$par['parametr'] = 'DEBUG';
$par['value'] = "";		 
SQLInsert('rtl433_config', $par);						

$par['parametr'] = 'JSON';
$par['value'] = "";		 
SQLInsert('rtl433_config', $par);						

$par['parametr'] = 'WORK';
$par['value'] = "";		 
SQLInsert('rtl433_config', $par);						

}


// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgTWFyIDEzLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
