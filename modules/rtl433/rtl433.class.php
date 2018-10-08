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
$out['CYCLERUN'] = 1;
} 
else {
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
$parametrs='-R 19 -R 1 -R 30 ';

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
tail -n 5 $filename");
//echo $a;

$aray =explode("}", $a);
foreach ($aray as $val)
{
$json=$val." }";
//echo $json;

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
foreach ($src as $key=> $value ) {   
if ($key=='id' ) {  $param=$key.'dev';} else  {$param=$key;}
$par[$param] = $value;
}     
$par['json']=$json;

$model=$par['model'];
$channel=$par['channel'];
$sql="SELECT * FROM rtl433_devices where model='$model' and  channel='$channel' ";
//echo $sql;
$new=SQLSelect($sql);
if ($new[0]['ID']) {
//update
SQLUpdate('rtl433_devices',$par); 

} else {
//newrecord
if ($par['model']<>'') {SQLInsert('rtl433_devices', $par);	}			
}}


}



}

function readmyfile5() {

$filename = ROOT.'cms/cached/rtl433'; // полный путь к нужному файлу



define("TEXT_FILE", $filename);
// number of lines to read from the end of file
define("LINES_COUNT", 10);
 
 
function read_file($file, $lines) {
    //global $fsize;
    $handle = fopen($file, "r");
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if(fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true; 
                break; 
            }
            $t = fgetc($handle);
            $pos --;
        }
        $linecounter --;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines-$linecounter-1] = fgets($handle);
        if ($beginning) break;
    }
    fclose ($handle);
    return array_reverse($text);
}
 
$fsize = round(filesize(TEXT_FILE)/1024/1024,2);
$lines = read_file(TEXT_FILE, LINES_COUNT);
foreach ($lines as $line) {
//    echo $line;
if (substr($line,1,1)=="{")
{ $json=$line;
echo $json;
SQLexec("update rtl433_config set VALUE='$json' where parametr='JSON'");

$src=json_decode($json,true);
$par=array();
foreach ($src as $key=> $value ) {   
if ($key=='id' ) {  $param=$key.'dev';} else  {$param=$key;}
$par[$param] = $value;
}     
$par['json']=$json;

$model=$par['model'];
$channel=$par['channel'];

$new=SQLSelect("SELECT * FROM rtl433_devices where model='$model' and  channel='$channel' ");
if ($new[0]['ID']) {
//update
SQLUpdate('rtl433_devices',$par); 

} else {
//newrecord
if ($par['model']<>'') {SQLInsert('rtl433_devices', $par);	}			
}}


}                   	


}




function readmyfile3(){

$filename = ROOT.'cms/cached/rtl433'; // полный путь к нужному файлу

$fp = fopen($filename, "r");
fseek($fp, -500, SEEK_END); // 500 bytes back

// get the last 10 lines
$line_buffer = array();
while (!feof($fp)) {
    $line = fgets($fp, 1024);
    $line_buffer[] = $line;
    $line_buffer = array_slice($line_buffer, -10, 10);
}
// the above can be made to work quicker, but it'd use more memory

// print those lines
foreach ($line_buffer as $line) {
    echo $line;
}

// print new changes
while (true) {
    $line = fgets($fp, 1024); // blocking
    echo $line;
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
  $this->redirect("?");
 }



 function uninstall() {
 
  SQLExec('DROP TABLE IF EXISTS rtl433_devices');
  SQLExec('DROP TABLE IF EXISTS rtl433_config');
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
 rtl433_devices: channel varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: temperature_C varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: humidity varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: FIND varchar(100) NOT NULL DEFAULT ''
 rtl433_devices: json varchar(100) NOT NULL DEFAULT ''
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
 rtl433_devicelist: ID int(10) unsigned NOT NULL auto_increment
 rtl433_devicelist: ENABLE int(10) unsigned NOT NULL auto_increment
 rtl433_devicelist: NAME varchar(100) NOT NULL DEFAULT ''
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
