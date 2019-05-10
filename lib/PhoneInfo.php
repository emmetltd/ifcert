<?php
namespace Emmetltd\ifcert\lib;

/**
 * Created by PhpStorm.
 * User: weifuqing
 * Date: 2018/8/21
 * Time: 下午4:44
 */




class PhoneInfo{

    function getPhoneArea($phone){
        $myfile = fopen(dirname(__FILE__)."/phone_area.txt", "r") or die("Unable to open file!");
        $fileString = fread($myfile,filesize(dirname(__FILE__)."/phone_area.txt"));

        $arr1 = explode(";",$fileString);

        for ($x=0;$x<count($arr1);$x++){

            $key = explode(',',$arr1[$x])[0];
            $value = explode(',',$arr1[$x])[1];
            $arr2[$key] = $value;
        }
        fclose($myfile);
        return $arr2[substr($phone,0,7)];
    }

}