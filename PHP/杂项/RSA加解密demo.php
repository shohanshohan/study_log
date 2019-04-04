<?php
$pub_key = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCihQr6eL6U3PCfcS8UX2C9o2jN
XHCxp1hsJAClhexZ54wsgWHlQM11sAgjwX5aZRkE8v+o6gHt9Ua+MtuLt3t4MJW5
bi/ZnJlI1vYLvd66v9UNjTa4qxM9SJjgR5gA6bLnhld8WM8jqsXqCNxIwV4wfosJ
dURcRrUWesqyTPDP/wIDAQAB
-----END PUBLIC KEY-----";

$priv_key = "-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQCihQr6eL6U3PCfcS8UX2C9o2jNXHCxp1hsJAClhexZ54wsgWHl
QM11sAgjwX5aZRkE8v+o6gHt9Ua+MtuLt3t4MJW5bi/ZnJlI1vYLvd66v9UNjTa4
qxM9SJjgR5gA6bLnhld8WM8jqsXqCNxIwV4wfosJdURcRrUWesqyTPDP/wIDAQAB
AoGAH8gErZaPLm1GYOexXTqJoIwkIoTBRPyEviEMP1JBNhOhxdJHYp2ZkDosXVGp
c+PxUJZ0iIO6fN5KR0eKx3fKHRbluJEyiiMiFysZ6fAcgSqZLNovY0UIAithGx4n
JqHDQG+MxuscWHhlPY5YEed4YLhJVxNdDrzKLGIyNCu8TYECQQC9lgN0+MTvMiPg
IcClqDgjEeNAAg9FMoha/NGuvTvYwtkLWJcg42NDFd8/Rs1xuBhugRmUO6aMwNmm
R3+I5eS9AkEA23O8jAJwm+Z1j0HQzpN9CYogpX2U2vIkFNrjd0BOosw3pyf+oQEX
4YBNgyjQA7K25A13IsoGtU05EUPKTC7ZawJBAJahpXd/3MEMPpLrXmyAnrHGmZ+W
w1lAkDRy4YsL0YwlLFwmdFVuNcTskOdusvcixU6vhopPZsI4y/Wgo0U7G6UCQQCq
XpTPYYmJDwbPe/oelhQplsm3kOJChGAMrM6RIySpcL/4Dq240z91+wSyQboUVOd1
7xpBsPQ6RsR6KdXhBTIXAkEAiviJSJYIin2NTMAgDjXHgzpuWKZNcbPRhZBf8C39
CaHxw4kFtxwJeABsTMovlOns3Qtiw5B767rUbpEjpjIhnA==
-----END RSA PRIVATE KEY-----";

$pu_key = openssl_get_publickey($pub_key);
$pi_key = openssl_pkey_get_private($priv_key);

$data = '{"id":"123456", "name":"test"}'; //要加密的字符
//加密
openssl_public_encrypt($data,$crypttext,$pu_key);
echo "String crypted: $crypttext <br />";
$encrypted = base64_encode($crypttext);
echo $encrypted . '<br />';


//解密
openssl_private_decrypt(base64_decode($encrypted),$decryped,$pi_key);
echo "String decrypt : $decryped <br>";
