<?php
 
/* 使用 mcrypt 扩展中的 mcrypt_encrypt() 和 mcrypt_decrypt() 对数据进行加密和解密 */
 
// 加密
//$algorithm = MCRYPT_BLOWFISH; // 加密算法
$algorithm = MCRYPT_RIJNDAEL_128; // 加密算法
$key = md5('mycryptkey'); // 加密密钥
$data = '{"name":"piter", "age":28}'; // 要加密或解密的数据
$mode = MCRYPT_MODE_CBC; // 加密或解密的模式
 
// 初始向量
$iv = mcrypt_create_iv(mcrypt_get_iv_size($algorithm, $mode), MCRYPT_DEV_URANDOM);
 
// 加密数据
$encrypted_data = mcrypt_encrypt($algorithm, $key, $data, $mode, $iv);
$plain_text = base64_encode($encrypted_data);
var_dump($plain_text);
// 每次都会变化的长度12的字符串，例如 vDJCatqAGdg=
 
echo '<br />';
 
// 解密
$encrypted_data = base64_decode($plain_text);
$decoded = mcrypt_decrypt($algorithm, $key, $encrypted_data, $mode, $iv);
var_dump(trim($decoded)); // trim删除末尾增加的NULL字节
