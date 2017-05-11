<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 17/5/8
 * Time: 下午7:25
 */

ini_set('memory_limit','2048M');

//$basepath = '../data/';
//$path_action_201602 = '../tmpdata/buyed_record_201602';
$path_action_201602 = '../tmpdata/buyed_record_201602';
//$path_action_201602 = "temp_buy";

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

    $userid_actions = explode("\t",$line);
    $user_id = $userid_actions[0];
    $actions = explode('|',$userid_actions[1]);

    $sku = array();
    foreach($actions as $action){
        $infos = explode(",",$action);
        $item = array();
        for($i=0;$i<count($cols);$i++){
            $item[$cols[$i]] = $infos[$i];
        }

//        echo json_encode($item).PHP_EOL;
        $sku_id = $item['sku_id'];
        if(empty($sku[$sku_id])){
            $sku[$sku_id] = array();
        }

        $detail = array(
            $item['time'],
//            $item['model_id'],
            $item['type'],
//            $item['cate'],$item['brand'],
        );
        array_push($sku[$sku_id],$detail);
    }
//    echo json_encode($sku).PHP_EOL;
    //提取实际购买的记录
    foreach($sku as $sku_id=>$si){
        $flag = false;
        $action_buy = array();
        for($i=0;$i<count($si);$i++){
//            echo $sku_id."\t".json_encode($si).PHP_EOL;
            array_push($action_buy,$si[$i]);
            if($si[$i][1] == 4 || $si[$i][1] == '4'){
                $flag = true;
                break;
            }
        }

        //正样本
//        if($flag==true){
//            echo $user_id."\t".$sku_id."\t".json_encode($action_buy).PHP_EOL;
//        }

        //负样本
        if($flag==false){
            echo $user_id."\t".$sku_id."\t".json_encode($action_buy).PHP_EOL;
        }

    }

//    if($index == 0){
//        $index++;
//        continue;
//    }
//    $infos = explode(',',$line);
//    $infos[0] = intval($infos[0]);
//    $item = array();
//    for($i=0;$i<count($cols);$i++){
//        $item[$cols[$i]] = $infos[$i];
//    }
//
////    echo 'line = '.$line.PHP_EOL;
//    if(empty($last_userid)){
////        $out_line = json_encode($item);
//        array_push($out_item,$item);
//        $last_userid = $item['user_id'];
//    }else if($last_userid == $item['user_id']){
//        array_push($out_item,$item);
//    }else{
////        echo json_encode($out_item).PHP_EOL;
//        echo $last_userid."\t";
//        $j = 0;
//        foreach($out_item as $it){
//            if($j>0){
//                echo "|";
//            }
//            echo implode(',',$it);
//            $j++;
//        }
//        echo PHP_EOL;
//
//        $last_userid = $item['user_id'];
//        $out_item = array();
//        array_push($out_item,$item);
//    }

//    echo json_encode($item).PHP_EOL;
//    if($index>5){
//        break;
//    }
    $index++;
//    break;
}

//echo $last_userid."\t";
//$j = 0;
//foreach($out_item as $it){
//    if($j>0){
//        echo "\t";
//    }
//    echo implode(',',$it);
//    $j++;
//}
//echo PHP_EOL;

fclose($handle);