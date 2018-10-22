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
//  $this->checkSettings();


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
// $out['SLACK_TEST']=SETTINGS_SLACK_APIURL;	


///////////
$cmd='ps -A|grep 433';


//$cmd='ps -A';
$answ=shell_exec($cmd);
$out['MSG_PS']=$answ;
//echo $answ;
$rez=substr_count  ($answ,'433' );
//echo $rez;
if ($rez=="1" ){
$out['APPRUN'] = 1;
} 
else {
$out['APPRUN'] = 0;
     } 

 $this->getConfig();
        if ((time() - gg('cycle_yandexweatherRun')) < 360*2 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}


////////////



//$cmd_rec = SQLSelectOne("SELECT VALUE FROM rtl433_config where parametr='JSON'");
//$out['MSG_DEBUG']=$cmd_rec['VALUE'];

$filename = ROOT.'cms/cached/rtl433'; // полный путь к нужному файлу

$a=shell_exec("tail -n 100 $filename");
//$a=shell_exec("cat $filename");
//$a=str_replace(chr(13),'<br>',$a);
//$a=str_replace ("\r\n", "<br>", $a);

$a =  str_replace( array("\r\n","\r","\n") , '<br>' , $a);
$out['MSG_DEBUG']=$a;





$mhdevices=SQLSelect("SELECT * FROM rtl433_devices");
if ($mhdevices[0]['ID']) {
 $out['DEVICES']=$mhdevices;

    }


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

//echo $this->tab;

if ($this->view_mode=='start') {
$this->start();
}  

if ($this->view_mode=='read') {
$this->readmyfile();
}  



if ($this->view_mode=='stop') {
$this->stop();
}  

if ($this->view_mode=='delete_devices') {
$this->delete_once($this->id);
}  

if ($this->view_mode=='clearall') {
$this->clearall();
}  

         if ($this->view_mode == 'edit_devices') {
            $this->edit_devices($out, $this->id);
         }



         if ($this->tab == 'configdevices') {
//echo '0';
            $this->configdevices($out, $this->id);
         }

 if ($this->view_mode=='config_check') {
//echo "echeck";
   $this->config_check($this->id);
 }
 if ($this->view_mode=='config_uncheck') {
   $this->config_uncheck($this->id);
 }


 if ($this->view_mode=='config_checkall') {
   $this->config_checkall();
 }

 if ($this->view_mode=='config_uncheckall') {
   $this->config_uncheckall();
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

	
 
   function edit_devices(&$out, $id)
   {
      require(DIR_MODULES . $this->name . '/devices_edit.inc.php');
   }

 function config_check($id) {
  $rec=SQLSelectOne("SELECT * FROM rtl433_devicelist WHERE ID=".$id);
//echo "<br>". implode( $id);
   $rec['ENABLE']=1;
SQLUpdate('rtl433_devicelist',$rec); 
} 

 function config_uncheck($id) {
  $rec=SQLSelectOne("SELECT * FROM rtl433_devicelist WHERE ID=".$id);
//echo "<br>". implode( $id);
   $rec['ENABLE']=0;
SQLUpdate('rtl433_devicelist',$rec); 
} 

 function config_uncheckall() {
  $rec=SQLexec("update rtl433_devicelist set ENABLE=0 ");

} 

 function config_checkall() {
  $rec=SQLexec("update rtl433_devicelist set ENABLE=1 ");

} 





   function configdevices(&$out, $id)
   {
//echo '1';
   require(DIR_MODULES . $this->name . '/configdevices.inc.php');
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

//$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
$mdlogin=EXT_ACCESS_USERNAME;
$mdpassword=EXT_ACCESS_PASSWORD;


$res=exec('hostname -I');
$ipv6_regex='/(\w{4})/is';
$res = trim(preg_replace($ipv6_regex,'',$res));
$ipv6_regex='/:(\w+)/is';
$res = trim(preg_replace($ipv6_regex,'',$res));
$host = trim(str_replace(':','',$res));

//$host='192.168.1.39';


$rtlpath='/home/pi/rtl_433_rcswitch/build/src';
//$parametrs='-R 19 -R 1 -R 30 ';
$parametrs="";
$sql=sqlselect('select * from rtl433_devicelist where ENABLE=1');
$total = count($sql);
for ($i = 0; $i < $total; $i++)
{
$parametrs.=' -R'.$sql[$i]['ID'].' ';
}
//echo "parametrs:".$parametrs;



//сетевой вариант
//if (IsSet($mdlogin)) { 
//$cmd="$rtlpath/rtl_433 $parametrs -f 433920000 -s 250000 -F json| sed 's/ /%20/g;s/!/%21/g;s/\"/%22/g;s/#/%23/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g;'s,^,http://$mdlogin:$mdpassword@$host/rtl433.php?json=,|xargs wget -O rtl433.temp";
//} else {
//$cmd="$rtlpath/rtl_433 $parametrs -f 433920000 -s 250000 -F json| sed 's/ /%20/g;s/!/%21/g;s/\"/%22/g;s/#/%23/g;s/\&/%26/g;s/'\''/%27/g;s/(/%28/g;s/)/%29/g;s/:/%3A/g;'s,^,http://$host/rtl433.php?json=,|xargs wget -O rtl433.temp";
//}
//$answ=exec($cmd ." > /dev/null 2>&1 &");

//вариант через файл и tail
//$cmd="$rtlpath/rtl_433 $parametrs -f 433920000 -s 250000 -F json -W ".ROOT."cms/cached/rtl433";
unlink(ROOT."cms/cached/rtl433");
$cmd="$rtlpath/rtl_433 $parametrs -f 433920000 -s 250000 -F json >".ROOT."cms/cached/rtl433";
//echo $cmd;

//$cmd='rtl_433 -f 433920000 -s 250000 -F json|mosquitto_pub -h localhost -t /home/rtl_433  -l';
//$answ=shell_exec($cmd);
$answ=exec($cmd ."  2>&1 &");

SQLexec("update rtl433_config set VALUE='$answ' where parametr='WORK'");
//echo $answ;



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

////////////////////

function readmyfile() {
$filename = ROOT.'cms/cached/rtl433'; // полный путь к нужному файлу

$a=shell_exec("
tail -n 50 $filename");
//echo $a;

$aray =explode("}", $a);
foreach ($aray as $val)
{
$json=$val." }";
//echo $json;


if (substr($json,1,13)=="Signal caught") {$this->start();}
if (substr($json,1,1)=="{")
{ 
//$json=$line;
//echo "<br>";
//echo $json;
//SQLexec("update rtl433_config set VALUE='$json' where parametr='JSON'");
//$json='{"time" : "2018-10-08 21:32:57", "model" : "Nexus Temperature/Humidity", "id" : 56, "battery" : "OK", "channel" : 2, "temperature_C" : 26.200, "humidity" : 40}';
$src=json_decode(trim($json),true);

//print_r($src);
$par=array();

///общий
foreach ($src as $key=> $value ) {   
if ($key=='id' ) {$param=$key.'dev';} else  {$param=$key;}
$par[$param]=$value;
}     
$par['json']=$json;



$model=$par['model'];
$channel=$par['channel'];

///для devices
$par1=array();
$par1['model']=$par['model'];
//echo $par1['model'].":".par['model'].':'.$model;
$par1['json']=$par['json'];
if ($par['iddev']) $par1['iddev']=$par['iddev'];
$par1['channel']=$par['channel'];
$par1['time']=$par['time'];

SqlSelectOne('update ');

if ($par['battery']) $par1['battery']=$par['battery'];
if ($par['temperature_C']) $par1['temperature_C']=$par['temperature_C'];
if ($par['humidity']) $par1['humidity']=$par['humidity'];
if ($par['sid']) $par1['iddev']=$par['sid'];





$sql1="SELECT * FROM rtl433_devices where model='$model' and  channel='$channel' ";
//echo $sql1;
$new=SQLSelectOne($sql1);
//echo "newid=".$new['ID']."<br>";
//echo "<br>";

if (!$new['ID']) 
{
SQLInsert('rtl433_devices',$par1); 
}
else
{
SQLUpdate('rtl433_devices',$par1); 
}


$newdevices=SQLSelectOne("select * from rtl433_devices where model='$model' and  channel='$channel' ");
$newid=$newdevices['ID'];

$newcommands=SQLSelectOne("select * from rtl433_commands where DEVICE_ID='$newid'");
//$pr=print_r($par);
//echo "<br>Добавляем: ".$pr."<br>";
//sg('test.rtl433',print_r($par));
foreach ($par as $key=> $value){
//echo $key.":".$value;
//echo $key.":".$value." ";
$newcommands['TITLE']=$key;
$newcommands['VALUE']=$value;
$newcommands['DEVICE_ID']=$newid;
$newcommands['UPDATED']=date('Y-m-d H:i:s');
$sql2="select * from rtl433_commands where DEVICE_ID='$newid' and 'TITLE'='$key'";
//echo $sql2;
//echo $newcommands['ID'];
if (SQLSelectOne($sql2)['ID'])
{
echo "1";
SQLUpdate('rtl433_commands',$newcommands);
} 

if (!SQLSelectOne($sql2)['ID'])
{
echo "2";
SQLInsert('rtl433_commands',$newcommands); 
echo "21";
} 
}




 

 	}
}}






 function processCycle() {
   $this->getConfig();
   $every=$this->config['EVERY'];
   $tdev = time()-$this->config['LATEST_UPDATE'];
   $has = $tdev>$every*60;
   if ($tdev < 0) {
		$has = true;
   }
   
   if ($has) {  
$this->readmyfile();   

		 
	$this->config['LATEST_UPDATE']=time();
	//$this->saveConfig();
SQLexec("update rtl433_config set value=UNIX_TIMESTAMP() where parametr='LASTCYCLE_TS'");		   
SQLexec("update rtl433_config set value=now() where parametr='LASTCYCLE_TXT'");		   	   

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

function delete_once($id) {
  SQLExec("DELETE FROM rtl433_devices WHERE id=".$id);
  $this->redirect("?");
 }

function clearall() {
  SQLExec("DELETE FROM rtl433_devices");
  SQLExec("DELETE FROM rtl433_commands");
  $this->redirect("?");
 }



 function uninstall() {
 
  SQLExec('DROP TABLE IF EXISTS rtl433_devices');
  SQLExec('DROP TABLE IF EXISTS rtl433_config');
  SQLExec('DROP TABLE IF EXISTS rtl433_devicelist');
  SQLExec('DROP TABLE IF EXISTS rtl433_commands');
  SQLExec('delete from settings where NAME like "%RTL433%"');
  parent::uninstall();

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
 rtl433_devices: time varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: model varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: iddev varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: battery varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: unknown1 varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: unknown2 varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: channel varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: temperature_C varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: humidity varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: FIND varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: json varchar(700) NOT NULL DEFAULT ''
 rtl433_devices: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);


 $data = <<<EOD
 rtl433_commands: ID int(10) unsigned NOT NULL auto_increment
 rtl433_commands: TITLE varchar(100) NOT NULL DEFAULT ''
 rtl433_commands: VALUE varchar(255) NOT NULL DEFAULT ''
 rtl433_commands: DEVICE_ID int(10) NOT NULL DEFAULT '0'
 rtl433_commands: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 rtl433_commands: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 rtl433_commands: LINKED_METHOD varchar(100) NOT NULL DEFAULT '' 
 rtl433_commands: UPDATED datetime
EOD;
  parent::dbInstall($data);


 $data = <<<EOD
 rtl433_devicelist: ID int(10) 
 rtl433_devicelist: ENABLE int(10)  
 rtl433_devicelist: NAME varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);



  $data = <<<EOD
 rtl433_config: parametr varchar(300)
 rtl433_config: value varchar(10000)  
EOD;
   parent::dbInstall($data);

  $mhdevices=SQLSelect("SELECT *  FROM rtl433_config");
  if (!$mhdevices[0]['ID']) 
{
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



//////////
$par1['ID'] = 1;
$par1['ENABLE'] = 1;
$par1['NAME'] = "Silvercrest Remote Control";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 2;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Rubicson Temperature Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 3;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Prologue Temperature Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 4;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Waveman Switch Transmitter";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 5;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Steffen Switch Transmitter";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 6;
$par1['ENABLE'] = 0;
$par1['NAME'] = "ELV EM 1000";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 7;
$par1['ENABLE'] = 0  ;
$par1['NAME'] = "ELV WS 2000";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 8;
$par1['ENABLE'] = 1;
$par1['NAME'] = "LaCrosse TX Temperature / Humidity Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 9;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Template decoder";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 10;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Acurite 896 Rain Gauge";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 11;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Acurite 609TXC Temperature and Humidity Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 12;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Oregon Scientific Weather Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 13;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Mebus 433";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 14;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Intertechno 433";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 15;
$par1['ENABLE'] = 0;
$par1['NAME'] = "KlikAanKlikUit Wireless Switch";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 16;
$par1['ENABLE'] = 0;
$par1['NAME'] = "AlectoV1 Weather Sensor (Alecto WS3500 WS4500 Ventus W155/W044 Oregon)";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 17;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Cardin S466-TX2";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 18;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Fine Offset Electronics, WH2, WH5, Telldus Temperature/Humidity/Rain Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 19;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Nexus Temperature & Humidity Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 20;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Ambient Weather Temperature Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 21;
$par1['ENABLE'] = 1;
$par1['NAME'] = "Calibeur RF-104 Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 22;
$par1['ENABLE'] = 0;
$par1['NAME'] = "X10 RF";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 23;
$par1['ENABLE'] = 0;
$par1['NAME'] = "DSC Security Contact";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 24;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Brennenstuhl RCS 2044";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 25;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "GT-WT-02 Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 26;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Danfoss CFR Thermostat";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 27;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Energy Count 3000 (868.3 MHz)";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 28;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Valeo Car Key";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 29;
$par1['ENABLE'] = 0;
$par1['NAME'] = "EChuango Security Technology";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 30;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Generic Remote SC226x EV1527";		 
SQLInsert('rtl433_devicelist', $par1);						

//////////////////////

$par1['ID'] = 31;
$par1['ENABLE'] = 0;
$par1['NAME'] = "TFA-Twin-Plus-30.3049 and Ea2 BL999";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 32;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Fine Offset Electronics WH1080/WH3080 Weather Station";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 33;
$par1['ENABLE'] = 0;
$par1['NAME'] = "WT450";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 34;
$par1['ENABLE'] = 0;
$par1['NAME'] = "LaCrosse WS-2310 Weather Station";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 35;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Esperanza EWS";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 36;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Efergy e2 classic";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 37;
$par1['ENABLE'] = 0  ;
$par1['NAME'] = "Inovalley kw9015b, TFA Dostmann 30.3161 (Rain and temperature sensor)";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 38;
$par1['ENABLE'] = 1;
$par1['NAME'] = "Generic temperature sensor 1";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 39;
$par1['ENABLE'] = 0;
$par1['NAME'] = "WG-PB12V1";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 40;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Acurite 592TXR Temp/Humidity, 5n1 Weather Station, 6045 Lightning";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 41;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Acurite 986 Refrigerator / Freezer Thermometer";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 42;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "HIDEKI TS04 Temperature, Humidity, Wind and Rain Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 43;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Watchman Sonic / Apollo Ultrasonic / Beckett Rocket oil tank monitor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 44;
$par1['ENABLE'] = 0;
$par1['NAME'] = "CurrentCost Current Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 45 ;
$par1['ENABLE'] = 0;
$par1['NAME'] = "emonTx OpenEnergyMonitor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 46;
$par1['ENABLE'] = 0;
$par1['NAME'] = "HT680 Remote control";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 47;
$par1['ENABLE'] = 0;
$par1['NAME'] = "S3318P Temperature & Humidity Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 48;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Akhan 100F14 remote keyless entry";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 49 ;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Quhwa";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 50;
$par1['ENABLE'] = 0  ;
$par1['NAME'] = "OSv1 Temperature Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 51;
$par1['ENABLE'] = 1;
$par1['NAME'] = "Proove";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 52;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Bresser Thermo-/Hygro-Sensor 3CH";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 53;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Springfield Temperature and Soil Moisture";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 54;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Oregon Scientific SL109H Remote Thermal Hygro Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 55;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Acurite 606TX Temperature Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 56;
$par1['ENABLE'] = 0;
$par1['NAME'] = "TFA pool temperature sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 57;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Kedsum Temperature & Humidity Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 58;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "blyss DC5-UK-WH (433.92 MHz)";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 59;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Steelmate TPMS";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 60;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Schrader TPMS";		 
SQLInsert('rtl433_devicelist', $par1);						



///////

$par1['ID'] = 61;
$par1['ENABLE'] = 0;
$par1['NAME'] = "LightwaveRF";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 62;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Elro DB286A Doorbell";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 63;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Efergy Optical";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 64;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Honda Car Key";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 65;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Template decoder";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 66;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Template decoder";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 67;
$par1['ENABLE'] = 0  ;
$par1['NAME'] = "Radiohead ASK";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 68;
$par1['ENABLE'] = 1;
$par1['NAME'] = "Kerui PIR Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 69;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Kerui PIR Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 70;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Honeywell Door/Window Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 71;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Maverick ET-732/733 BBQ Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 72;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "RF-tech";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 73;
$par1['ENABLE'] = 0;
$par1['NAME'] = "LaCrosse TX141-Bv2/TX141TH-Bv2 sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 74;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Acurite 00275rm,00276rm Temp/Humidity with optional probe";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 75 ;
$par1['ENABLE'] = 0;
$par1['NAME'] = "LaCrosse TX35DTH-IT, TFA Dostmann 30.3155 Temperature/Humidity sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 76;
$par1['ENABLE'] = 0;
$par1['NAME'] = "LaCrosse TX29IT Temperature sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 77;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Vaillant calorMatic 340f Central Heating Control";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 78;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Fine Offset Electronics, WH25, WH24, HP1000 Temperature/Humidity/Pressure Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 79 ;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Fine Offset Electronics, WH0530 Temperature/Rain Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 80;
$par1['ENABLE'] = 0  ;
$par1['NAME'] = "IBIS beacon";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 81;
$par1['ENABLE'] = 1;
$par1['NAME'] = "Oil Ultrasonic STANDARD FSK";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 82;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Citroen TPMS";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 83;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Oil Ultrasonic STANDARD ASK";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 84;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Thermopro TP11 Thermometer";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 85;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Solight TE44";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 86;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Wireless Smoke and Heat Detector GS 558";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 87;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Generic wireless motion sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 88;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Toyota TPMS";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 89;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Ford TPMS";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 90;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Renault TPMS";		 
SQLInsert('rtl433_devicelist', $par1);						
/////////////////////////////
$par1['ID'] = 91;
$par1['ENABLE'] = 0;
$par1['NAME'] = "inFactory";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 92;
$par1['ENABLE'] = 0;
$par1['NAME'] = "FT-004-B Temperature Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 93;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Ford Car Key";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 94;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Philips outdoor temperature sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 95;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "Schrader TPMS EG53MA4";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 96;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Nexa";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 97;
$par1['ENABLE'] = 0  ;
$par1['NAME'] = "Thermopro TP08/TP12 thermometer";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 98;
$par1['ENABLE'] = 1;
$par1['NAME'] = "GE Color Effects";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 99;
$par1['ENABLE'] = 0;
$par1['NAME'] = "X10 Security";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 100;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Interlogix GE UTC Security Devices";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 101;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Dish remote 6.3";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 102;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "SimpliSafe Home Security System (May require disabling automatic gain for KeyPad decodes)";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 103;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Sensible Living Mini-Plant Moisture Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 104;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Wireless M-Bus, Mode C&T, 100kbps (-f 868950000 -s 1200000)";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 105 ;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Wireless M-Bus, Mode S, 32.768kbps (-f 868300000 -s 1000000)";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 106;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Wireless M-Bus, Mode R, 4.8kbps (-f 868330000)";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 107;
$par1['ENABLE'] = 0;
$par1['NAME'] = "Wireless M-Bus, Mode F, 2.4kbps";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 108;
$par1['ENABLE'] = 0 ;
$par1['NAME'] = "WS Temperature Sensor";		 
SQLInsert('rtl433_devicelist', $par1);						

$par1['ID'] = 109 ;
$par1['ENABLE'] = 0;
$par1['NAME'] = "WT0124 Pool Thermometer";		 
SQLInsert('rtl433_devicelist', $par1);						





}

/*

    [01]  Silvercrest Remote Control
    [02]  Rubicson Temperature Sensor
    [03]  Prologue Temperature Sensor
    [04]  Waveman Switch Transmitter
    [05]* Steffen Switch Transmitter
    [06]* ELV EM 1000
    [07]* ELV WS 2000
    [08]  LaCrosse TX Temperature / Humidity Sensor
    [09]* Template decoder
    [10]* Acurite 896 Rain Gauge
    [11]  Acurite 609TXC Temperature and Humidity Sensor
    [12]  Oregon Scientific Weather Sensor
    [13]* Mebus 433
    [14]* Intertechno 433
    [15]  KlikAanKlikUit Wireless Switch
    [16]  AlectoV1 Weather Sensor (Alecto WS3500 WS4500 Ventus W155/W044 Oregon)
    [17]  Cardin S466-TX2
    [18]  Fine Offset Electronics, WH2, WH5, Telldus Temperature/Humidity/Rain Sensor
    [19]  Nexus Temperature & Humidity Sensor
    [20]  Ambient Weather Temperature Sensor
    [21]  Calibeur RF-104 Sensor
    [22]* X10 RF
    [23]  DSC Security Contact
    [24]* Brennenstuhl RCS 2044
    [25]  GT-WT-02 Sensor
    [26]  Danfoss CFR Thermostat
    [27]* Energy Count 3000 (868.3 MHz)
    [28]* Valeo Car Key
    [29]  Chuango Security Technology
    [30]  Generic Remote SC226x EV1527
    [31]  TFA-Twin-Plus-30.3049 and Ea2 BL999
    [32]  Fine Offset Electronics WH1080/WH3080 Weather Station
    [33]  WT450
    [34]  LaCrosse WS-2310 Weather Station
    [35]  Esperanza EWS
    [36]  Efergy e2 classic
    [37]* Inovalley kw9015b, TFA Dostmann 30.3161 (Rain and temperature sensor)
    [38]  Generic temperature sensor 1
    [39]  WG-PB12V1
    [40]  Acurite 592TXR Temp/Humidity, 5n1 Weather Station, 6045 Lightning
    [41]  Acurite 986 Refrigerator / Freezer Thermometer
    [42]  HIDEKI TS04 Temperature, Humidity, Wind and Rain Sensor
    [43]  Watchman Sonic / Apollo Ultrasonic / Beckett Rocket oil tank monitor
    [44]  CurrentCost Current Sensor
    [45]  emonTx OpenEnergyMonitor
    [46]  HT680 Remote control
    [47]  S3318P Temperature & Humidity Sensor
    [48]  Akhan 100F14 remote keyless entry
    [49]  Quhwa
    [50]  OSv1 Temperature Sensor
    [51]  Proove
    [52]  Bresser Thermo-/Hygro-Sensor 3CH
    [53]  Springfield Temperature and Soil Moisture
    [54]  Oregon Scientific SL109H Remote Thermal Hygro Sensor
    [55]  Acurite 606TX Temperature Sensor
    [56]  TFA pool temperature sensor 
    [57]  Kedsum Temperature & Humidity Sensor
    [58]  blyss DC5-UK-WH (433.92 MHz)
    [59]  Steelmate TPMS
    [60]  Schrader TPMS
    [61]* LightwaveRF
    [62]  Elro DB286A Doorbell
    [63]  Efergy Optical
    [64]  Honda Car Key
    [65]* Template decoder
    [66]* Template decoder
    [67]  Radiohead ASK
    [68]  Kerui PIR Sensor
    [69]  Fine Offset WH1050 Weather Station
    [70]  Honeywell Door/Window Sensor
    [71]  Maverick ET-732/733 BBQ Sensor
    [72]* RF-tech
    [73]  LaCrosse TX141-Bv2/TX141TH-Bv2 sensor
    [74]  Acurite 00275rm,00276rm Temp/Humidity with optional probe
    [75]  LaCrosse TX35DTH-IT, TFA Dostmann 30.3155 Temperature/Humidity sensor
    [76]  LaCrosse TX29IT Temperature sensor
    [77]  Vaillant calorMatic 340f Central Heating Control
    [78]  Fine Offset Electronics, WH25, WH24, HP1000 Temperature/Humidity/Pressure Sensor
    [79]  Fine Offset Electronics, WH0530 Temperature/Rain Sensor
    [80]  IBIS beacon
    [81]  Oil Ultrasonic STANDARD FSK
    [82]  Citroen TPMS
    [83]  Oil Ultrasonic STANDARD ASK 
    [84]  Thermopro TP11 Thermometer
    [85]  Solight TE44
    [86]  Wireless Smoke and Heat Detector GS 558
    [87]  Generic wireless motion sensor
    [88]  Toyota TPMS
    [89]  Ford TPMS
    [90]  Renault TPMS
    [91]* inFactory
    [92]  FT-004-B Temperature Sensor
    [93]  Ford Car Key
    [94]  Philips outdoor temperature sensor
    [95]  Schrader TPMS EG53MA4
    [96]  Nexa
    [97]  Thermopro TP08/TP12 thermometer
    [98]  GE Color Effects
    [99]  X10 Security
    [100]  Interlogix GE UTC Security Devices
    [101]* Dish remote 6.3
    [102]* SimpliSafe Home Security System (May require disabling automatic gain for KeyPad decodes)
    [103]  Sensible Living Mini-Plant Moisture Sensor
    [104]* Wireless M-Bus, Mode C&T, 100kbps (-f 868950000 -s 1200000)
    [105]* Wireless M-Bus, Mode S, 32.768kbps (-f 868300000 -s 1000000)
    [106]* Wireless M-Bus, Mode R, 4.8kbps (-f 868330000)
    [107]* Wireless M-Bus, Mode F, 2.4kbps
    [108]  WS Temperature Sensor
    [109]  WT0124 Pool Thermometer

*/


}


// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgTWFyIDEzLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
