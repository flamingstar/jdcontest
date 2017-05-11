<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 17/5/8
 * Time: 下午7:25
 */

ini_set('memory_limit','2048M');
ini_set('date.timezone','Asia/Shanghai');

class Util{
    /**
     * @brif 文件内容提取为数组
     * @param $user_path
     */
    public static function getFile2Arr($user_path,$colstr,$key='user_id'){
        $userinfo = array();
        $file_user = file_get_contents($user_path);
//        $usercolstr = 'user_id,age,sex,user_lv_cd,user_reg_tm,user_reg_diff';
        $usercols = explode(',',$colstr);
        $lines = explode(PHP_EOL,$file_user);
        $index=0;
        foreach($lines as $line){
            $line = str_replace(PHP_EOL,'',$line);
            if(empty($line)){
            continue;
            }

            $item = array();
            $infos = explode(',',$line);
            if($infos[0] == $key){
                continue;
            }
    //        $infos[2] = intval($infos[2]);
            for($i=0;$i<count($usercols);$i++){
                $item[$usercols[$i]] = $infos[$i];
            }

            $userinfo[$item[$key]] = $item;
            $index++;
        }
        return $userinfo;
    }
}

