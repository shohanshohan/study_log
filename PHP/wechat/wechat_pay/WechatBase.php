<?php
/**
 * Created by PhpStorm.
 * User: shohan
 * Date: 2018/3/12
 * Time: 18:53
 */

namespace app\game_api\controller;
/**
*微信支付基类
*/
class WechatBase extends Base
{
    private $key = "974a0bbae55f0dc9bb2270cbb63679b5"; //商户生成的密钥

    /**
     * 随机字符串生成，32位内
     * @return string max 32
     */
    protected function getNonceStr()
    {
        $str = time().'myWechatStr'.mt_rand(1,1000000);
        return substr(md5($str),2);
    }

    /**
     * 签名算法
     * @param array $data
     * @return string
     */
    protected function getSign($data=[])
    {
        //签名步骤一：按字典序排序参数
        $String = $this->formatParaMap($data);
        //签名步骤二：在string后加入KEY
        $String .= "&key=".$this->key;
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $signStr = strtoupper($String);
        return $signStr;
    }

    /**
     * 验证签名
     */
    protected function verifySign($data=[])
    {
        $result = false;
        if(!empty($data) && isset($data['sign'])){
            $sign = $data['sign'];
            unset($data['sign']);
            $verifySign = $this->getSign($data);
            if($verifySign==$sign){
                $result = true;
            }
        }
        return $result;
    }

    /**
     * 格式化参数，签名过程需要使用
     * @param array $paraMap
     * @return string
     */
    protected function formatParaMap($paraMap=[])
    {
        if(!empty($paraMap)){
            ksort($paraMap);
            foreach ($paraMap as $key=>$val){
                if(!empty($val)){
                    $data[] = "$key=$val";
                }
            }
            $str = implode('&',$data);
            return $str;
        }
        return '';
    }


    /**
     * array转xml
     * 拼装请求的数据
     * @return  String 拼装完成的数据
     */
    protected function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            //$xml.="<".$key.">".$val."</".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 	将xml转为array
     * @return array
     */
    protected function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     * 	作用：以post方式提交xml到对应的接口url
     */
    protected function postXmlCurl($xml,$url,$second=30,$useCert=false)
    {
        //初始化curl
        $ch = curl_init();
        //设置超时
        // curl_setopt($ch, CURLOP_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, 'path文件');
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, 'path文件');
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data)
        {
            curl_close($ch);
            return $this->xmlToArray($data);
        }
        else
        {
            $error = curl_errno($ch);
            curl_close($ch);
            return $this->apiInfo(400,"curl出错，错误码:$error");
        }
    }

    /**
     * 接收回调
     */
    protected function getMsg()
    {
        //接收传送的数据
        $fileContent = file_get_contents("php://input");
        //转换为simplexml对象
        //$xmlResult = simplexml_load_string($fileContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $this->xmlToArray($fileContent);
    }

    /**获取用户真实的IP
     * @return array|false|string
     */
    protected function get_ip(){
        //判断服务器是否允许$_SERVER
        if(isset($_SERVER)){
            if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            }else{
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        }else{
            //不允许就使用getenv获取
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            }else{
                $realip = getenv("REMOTE_ADDR");
            }
        }

        return $realip;
    }


}
