<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 17/5/8
 * Time: 下午7:25
 */

ini_set('memory_limit','2048M');

$basepath = '../data/';
$path_format_action_201602 = '../tmpdata/userid_action_201602';

$path_format_negtive_201602 = '../tmpdata/negtive_action_201602';

//$file = file_get_contents($path_action_201602);
//$lines = explode(PHP_EOL,$file);
//foreach($lines as $line){
//    echo $line.PHP_EOL;
//
//    break;
//}

function dealPositive($path){
    $colstr = 'user_id,sku_id,time,model_id,type,cate,brand';
    $cols = explode(',',$colstr);
    $index = 0;
    $last_userid = '';
    $out_line = '';
    $out_item = array();

    $handle = fopen($path,'r') or exit("Unable to open file!");
    while(!feof($handle)){
        $line = fgets($handle);
        $line = str_replace(PHP_EOL,'',$line);

        $userid_actions = explode("\t",$line);
        $user_id = $userid_actions[0];
        $actions = explode("|",$userid_actions[1]);

        for($j=0;$j<count($actions);$j++){
            $infos = explode(',',$actions[$j]);
            $item = array();
            for($i=0;$i<count($cols);$i++){
                $item[$cols[$i]] = $infos[$i];
            }
//        echo json_encode($item).PHP_EOL;
            if($item['type'] == 4){
                echo $line.PHP_EOL;
                return;
            }
        }
        $index++;
    }

    fclose($handle);
}

function dealNegtive($path){
    $colstr = 'time,type';
    $cols = explode(',',$colstr);
    $index = 0;
    $last_userid = '';
    $out_line = '';
    $out_item = array();

    $handle = fopen($path,'r') or exit("Unable to open file!");
    while(!feof($handle)){
        $line = fgets($handle);
        $line = str_replace(PHP_EOL,'',$line);

        $userid_actions = explode("\t",$line);
        $user_id = $userid_actions[0];
        $actions = explode("|",$userid_actions[1]);

        $flag = false;
        for($j=0;$j<count($actions);$j++){
            $infos = explode(',',$actions[$j]);
            $item = array();
            for($i=0;$i<count($cols);$i++){
                $item[$cols[$i]] = $infos[$i];
            }

            if($item['type'] == 4){
//                echo $line.PHP_EOL;
//                return;
                $flag = true;
            }
        }

        if($flag == false){
            echo $line.PHP_EOL;
            break;
        }
        $index++;
//        break;
    }

    fclose($handle);
}

//dealPositive($path_format_action_201602);
dealNegtive($path_format_negtive_201602);