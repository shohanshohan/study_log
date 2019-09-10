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


# 现在使用公钥私钥来加解密操作
/**
* 用公钥进行加密，然后再用私钥解密，或者调换一下也可以
* 生成加密密钥的工具下载地址：支付宝RAS密钥生成器SHAwithRSA2048_V1.0
* （windows）https://os.alipayobjects.com/download/secret_key_tools_RSA_win.zip
* (MAC OSX) https://os.alipayobjects.com/download/secret_key_tools_RSA_macosx.zip
**/
$public_key = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzm++O4gNxBTYoNA3GcLn
4E5yVWJvNVAuWXAeFjQPihUZv5Rqjv3tg5UgNOLs0Ier6rvhGFbPjGxk1uc5mLWU
zu4m4P1Yz6f9/ivhJRv+BO430y+WW6aTT8Si9VG+sIMlZHd+2nGixLe63yzCzx0F
TAZUQSN9uRviDp2kQStoGLpGzDEwQvoVczD0skqv5Ptcgb/NC+JHW8WcrG2PZKe0
ij8Igls92q1uVzXD17grOEASwwB+dp5+uqO11nIm4O3MsizeIxMps/Cu1/Un+Znc
1dtOcGMy4Pz60A58ah8Uozzcs70hEbhfoOYKi2dI6TYGAYP8HUHhyTNYeKCpP4t6
gQIDAQAB
-----END PUBLIC KEY-----';

$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAzm++O4gNxBTYoNA3GcLn4E5yVWJvNVAuWXAeFjQPihUZv5Rq
jv3tg5UgNOLs0Ier6rvhGFbPjGxk1uc5mLWUzu4m4P1Yz6f9/ivhJRv+BO430y+W
W6aTT8Si9VG+sIMlZHd+2nGixLe63yzCzx0FTAZUQSN9uRviDp2kQStoGLpGzDEw
QvoVczD0skqv5Ptcgb/NC+JHW8WcrG2PZKe0ij8Igls92q1uVzXD17grOEASwwB+
dp5+uqO11nIm4O3MsizeIxMps/Cu1/Un+Znc1dtOcGMy4Pz60A58ah8Uozzcs70h
EbhfoOYKi2dI6TYGAYP8HUHhyTNYeKCpP4t6gQIDAQABAoIBAQDDRFGV8BjNW8aZ
PrqQBJvewXVGrMhyjnh5IyNibzYvr8veOp4cybZr25hOTkQg7+Q/Mh42Lo89zZ4I
5UjG2SN8JSrjcOEb81S+onTh7Dt4IHjvqzi3UrZPIrneAMe2IScyhBGawRhsqTn7
NlTDIjlLc0UpJFAc03IdzyF6f3uEvqMFfyxLYdUe9L1RmgQdXthoUs/wsgtbRCFQ
3GsP5BTUSRMZf3EvEZegemzKlBDGcjS0xUWZUmxrW8vCngQI5gS8q46FaLfpJdrV
2dSHGgixhD2Pgk1c0d/gkfG7PcLYf0Oh5bHlWfXo3ECHUf8KMDqUxaEHVdYShXTe
ZI39dNfRAoGBAPKm9t99IXreAo4cqIqLxsS7HaqWzVKbN+8+31geOL0X0c++OTf0
FdLZwTBVhilWbEkC+MrOMj4I4K4z/5sUGVOowLLz5uUD8h7fjUnYwqE4KhCw0TW1
VysfFdbU6HZCIC0+ayNkQTdooNBbmyOLNQjJvr8UpdG1w3TeI/FdeE61AoGBANnK
yb4cXZFRFH+gDAOpVTWp1iVuxqZXcJ1lkeZduVeoWg31zDkW7SVMubrbDq5Xefjb
X1oIwCSjePCKOmN/2Fw74ZxJaV+GySdnt5rtVFyPVoiVGPqX1bZPCa0ZGGGS1iR5
kG6LXAMP5xDiRyhvTDLthu5tHf3hGUsQUgIQSlAdAoGBALSQiiTEeKV1j3Ew4jo2
yTKcCvdmQGO4HWdq4cgwtQgBMZ/lba61c4fzgk71VWBtbybljz1bMwf2kzsOdqZv
zmjyqnKADNFenk1bDUhmhG5z3NmOuh+9UaBFAXtb86AMlOUWDaPIdr9EnGZvxIIu
lMh1V/vY4YusNkZBB34Uj/IBAoGAfMkyGllKSy63ngBnodlt1xd+eWglLb2/6o79
r45X2Z++KW2CG9vE+LAjYqqMmNvKKQoYcg+9d8CqVLf+iHAB2ab9t3xggblKm+dT
TdNZmMcP/6Xd8Ab7Dj/u/Vz5mT52r+NvG8eBase7zvK3brWGZU+vFgEzQ/mrclTh
iAkpawkCgYBqrVZJ9d1libMtK5WH6EN2iJXiZwqkNvcvJlhTcm8NWeSOTLfskC0c
MWxVo6YUExh6kimVr2M5oNZSo9TDxDtM8QPwcgChoOKRZj+crmYw7juOFKa2wD61
0HVBicqWyLaz1Y2RAV5e4KVo4L9NtnNKSxlcA4VhfrW1JuUCfomeKg==
-----END RSA PRIVATE KEY-----';

function pub_encrypt($public_key)
{
  $data = base64_encode(json_encode(['type'=>'pub-encrypt-value']));
  //openssl_get_publickey($public_key);
  openssl_public_encrypt($data,$crypttext,$public_key); 
  
  return $crypttext;
}

function pri_decrypt($private_key, $encrypt_str)
{
  //openssl_get_privatekey($private_key);
  openssl_private_decrypt($encrypt_str, $decrypted, $private_key, OPENSSL_PKCS1_PADDING);
  var_dump(base64_decode($decrypted));
}

$encrypt_str = pub_encrypt($public_key);
var_dump($encrypt_str);
echo '<br>';
pri_decrypt($private_key, $encrypt_str);
