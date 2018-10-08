<?php

echo "asas";
$table_name='rtl433_devicelist';
  $devices=SQLSelect("SELECT * FROM $table_name");
  if ($devices[0]['ID']) {
   $out['CONFIGDEVICES']=$devices;


    }
