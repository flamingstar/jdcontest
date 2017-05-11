<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 17/5/8
 * Time: 下午7:25
 */

ini_set('memory_limit','2048M');

$basepath = '../data/';
$path_action_201602 = $basepath.'sort_JData_Action_201602.csv';
$path_action_201603 = $basepath.'JData_Action_201603.csv';
$path_action_201604 = $basepath.'JData_Action_201604.csv';

//$file = file_get_contents($path_action_201602);
//$lines = explode(PHP_EOL,$file);
//foreach($lines as $line){
//    echo $line.PHP_EOL;
//
//    break;
//}

$colstr = 'user_id,sku_id,time,model_id,type,cate,brand';
$cols = explode(',',$colstr);
$index = 0;
$last_userid = '';
$out_line = '';
$out_item = array();

$handle = fopen($path_action_201602,'r') or exit("Unable to open file!");
while(!feof($handle)){
    $line = fgets($handle);
    $line = str_replace(PHP_EOL,'',$line);
    if(empty($line)){
        continue;
    }
//    echo $line.PHP_EOL;

    if($index == 0){
        $index++;
        continue;
    }
    $infos = explode(',',$line);
    $infos[0] = intval($infos[0]);
    $item = array();
    for($i=0;$i<count($cols);$i++){
        $item[$cols[$i]] = $infos[$i];
    }

//    echo 'line = '.$line.PHP_EOL;
    if(empty($last_userid)){
//        $out_line = json_encode($item);
        array_push($out_item,$item);
        $last_userid = $item['user_id'];
    }else if($last_userid == $item['user_id']){
        array_push($out_item,$item);
    }else{
//        echo json_encode($out_item).PHP_EOL;
        echo $last_userid."\t";
        $j = 0;
        foreach($out_item as $it){
            if($j>0){
                echo "|";
            }
            echo implode(',',$it);
            $j++;
        }
        echo PHP_EOL;

        $last_userid = $item['user_id'];
        $out_item = array();
        array_push($out_item,$item);
    }
//    echo json_encode($item).PHP_EOL;
//    if($index>5){
//        break;
//    }
    $index++;
//    break;
}
//echo json_encode($out_item).PHP_EOL;
echo $last_userid."\t";
$j = 0;
foreach($out_item as $it){
    if($j>0){
        echo "\t";
    }
    echo implode(',',$it);
    $j++;
}
echo PHP_EOL;

fclose($handle);