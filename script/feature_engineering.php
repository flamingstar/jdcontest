<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 17/5/8
 * Time: 下午7:25
 */

ini_set('memory_limit','2048M');
ini_set('date.timezone','Asia/Shanghai');

require_once './util.php';

/**
 * 正负样本特征提取
 * 特征维度1：行为维度，距离购买时间1天内、3天内、5天内，各类行为的发生次数
 *
 * 特征维度2：用户维度
 *
 * 特征维度3：商品维度，信息，用户评论数
 *
 * onehot编码
 */

$base_path = '/Users/baidu/develop/projects/contest/jingdong/codes/JData/data/';

$user_path = $base_path.'JData_User_New.csv';
$product_path = $base_path.'JData_Product.csv';
$comment_path = $base_path.'JData_Comment.csv';

/**
 * 用户信息
 */
$userinfo = array();
$file_user = file_get_contents($user_path);
$usercolstr = 'user_id,age,sex,user_lv_cd,user_reg_tm,user_reg_diff';
$usercols = explode(',',$usercolstr);
$lines = explode(PHP_EOL,$file_user);
$index=0;
foreach($lines as $line){
    $line = str_replace(PHP_EOL,'',$line);
    if(empty($line)){
        continue;
    }

    $item = array();
    $infos = explode(',',$line);
    if($infos[0] == 'user_id'){
        continue;
    }
    $infos[2] = intval($infos[2]);
    for($i=0;$i<count($usercols);$i++){
        $item[$usercols[$i]] = $infos[$i];
    }

    $userinfo[$item['user_id']] = $item;
    $index++;
}

/**
 * 产品信息
 */
$colstr = 'sku_id,a1,a2,a3,cate,brand';
$products = Util::getFile2Arr($product_path,$colstr,'sku_id');

//$colstr = 'dt,sku_id,comment_num,has_bad_comment,bad_comment_rate';
//$comments = Util::getFile2Arr($comment_path,$colstr,'sku_id');
//foreach($products as $id=>$info){
//    echo $id.'  '.json_encode($info).PHP_EOL;
//    break;
//}



$path_action_201602 = '../tmpdata/sample_positive_201602.txt';
$path_action_201602 = '../tmpdata/sample_negtive_201602.txt';

$colstr = 'user_id,sku_id,time,model_id,type,cate,brand';
$cols = explode(',',$colstr);
$index = 0;
$last_userid = '';
$out_line = '';
$out_item = array();


/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $day1
 * @param string $day2
 * @return number
 */
function diffBetweenTwoDays ($day1, $day2)
{
    $second1 = strtotime($day1);
    $second2 = strtotime($day2);

    if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
    }
    return ($second1 - $second2) / 86400;
}
//$day1 = "2013-07-27 23:53:09";
//$day2 = "2013-07-28 22:53:09";
//$diff = diffBetweenTwoDays($day1, $day2);
//echo $diff."\n";


//正负样本特征提取
$handle = fopen($path_action_201602,'r') or exit("Unable to open file!");
while(!feof($handle)){
    $line = fgets($handle);
    $line = str_replace(PHP_EOL,'',$line);
    if(empty($line)){
        continue;
    }
//    echo $line.PHP_EOL;
    $userid_skuid_actions = explode("\t",$line);
    $user_id = $userid_skuid_actions[0];
    $sku_id = $userid_skuid_actions[1];
    $actions = json_decode($userid_skuid_actions[2],true);

    $buy_action = $actions[count($actions)-1];

    $types = array();
    for($i=1;$i<=6;$i++){
        $types[$i] = array(
            'dif_1'=>0,
            'dif_3'=>0,
            'dif_5'=>0,
        );
    }

    for($i=0;$i<count($actions)-1;$i++){
        $time = $actions[$i][0];
        $type = $actions[$i][1];

        $day_diff = diffBetweenTwoDays($time,$buy_action[0]);
        if($day_diff<=5){
            $types[$type]['dif_5'] +=1;
            if($day_diff<=3){
                $types[$type]['dif_3'] +=1;
                if($day_diff<=1){
                    $types[$type]['dif_1'] +=1;
                }
            }
        }

//        if($day_diff<=1){
//            $types[$type]['dif_1'] +=1;
//        }else if($day_diff<=3){
//            $types[$type]['dif_3'] +=1;
//        }else if($day_diff<=5){
//            $types[$type]['dif_5'] +=1;
//        }
    }

    //填充用户信息（age,sex,user_lv_cd,user_reg_tm） 和 商品信息（a1,a2,a3,cate,brand）
    //没有匹配时，-1填充
    $user_dim = "-1\t-1\t-1\t-1";
    if(!empty($userinfo[$user_id])){
        $uitem = $userinfo[$user_id];
        $user_dim = $uitem['age']."\t".$uitem['sex']."\t".$uitem['user_lv_cd']."\t".$uitem['user_reg_tm'];
    }

    $sku_dim = "-1\t-1\t-1\t-1";
    if(!empty($products[$sku_id])){
        $pitem = $products[$sku_id];
        $sku_dim = $pitem['a1']."\t".$pitem['a2']."\t".$pitem['a3']
            ."\t".$pitem['cate']."\t".$pitem['brand'];
    }

    $feature = "$user_id\t";
    $dim1 = "$sku_id\t";
    for($i=1;$i<=6;$i++){
        if($i>1){
            $dim1.="\t";
        }
//        $dim1.=$i."|".$types[$i]['dif_1'].','.$types[$i]['dif_3'].','.$types[$i]['dif_5'];
        $dim1.=$types[$i]['dif_1']."\t".$types[$i]['dif_3']."\t".$types[$i]['dif_5'];
    }

//    echo $dim1.PHP_EOL;
    $feature.=$user_dim."\t".$sku_dim."\t".$dim1;
    echo $feature.PHP_EOL;
    $index++;
//    break;
}
fclose($handle);