<?php
namespace app\common\util;

class Mcrypt
{
	/**
	 * [aes description]
	 * @Author   WirrorYin
	 * @DateTime 2016-10-26T15:15:22+0800
	 * @param    [type]                   $ostr      [description]
	 * @param    [type]                   $securekey [description]
	 * @param    string                   $type      [description]
	 * @param    string                   $mode      [description]
	 * @return   [type]                              [description]
	 */
	public static function aes($ostr, $securekey, $type='encrypt', $mode='ecb')
	{ 
		if($ostr=='')
		{ 
			return ''; 
		} 

		$key = $securekey;//substr(md5($securekey), 8, 16);//len16
        $iv = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        //$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, $mode), MCRYPT_RAND);

        $str = '';

        switch($type)
        { 
            case 'encrypt': 
                $ostr = self::pkcs0Pad($ostr, MCRYPT_RIJNDAEL_128, $mode);
                //$str = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $ostr, $mode, $iv);
                //$str = AesCtr::encrypt($ostr, $key, 256, 1);
                $ostr = trim($ostr, "\0");
                $str = Aes::encrypt($ostr, $key);
                $str = self::pkcs0Unpad($str);
                break; 

            case 'decrypt': 
				$ostr = self::pkcs0Pad($ostr, MCRYPT_RIJNDAEL_128, $mode);
                //$str = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $ostr, $mode, $iv);
                //$str = self::pkcs5Unpad($str);
                //$str = AesCtr::decrypt($ostr, $key, 256);
                $ostr = trim($ostr, "\0");
                $str = Aes::decrypt($ostr, $key);
                $str = self::pkcs0Unpad($str);
                break; 
        } 

		return $str; 
	} 

	/**
	 * [des description]
	 * @Author   WirrorYin
	 * @DateTime 2016-10-26T15:55:19+0800
	 * @param    [type]                   $ostr      [description]
	 * @param    [type]                   $securekey [description]
	 * @param    string                   $type      [description]
	 * @param    string                   $mode      [description]
	 * @return   [type]                              [description]
	 */
	public static function des($ostr, $securekey, $type='encrypt', $mode='ecb')
	{ 
		if($ostr=='')
		{ 
			return ''; 
		} 

		$td = mcrypt_module_open('des', '', $mode, '');
		$size = mcrypt_enc_get_iv_size($td);       //设置初始向量的大小
	    $iv = mcrypt_create_iv($size,MCRYPT_RAND); //创建初始向量

	    $key_size = mcrypt_enc_get_key_size($td);       //返回所支持的最大的密钥长度（以字节计算）
	    $salt = '';
	    $subkey = substr(md5(md5($securekey).$salt), 0,$key_size);//对key复杂处理，并设置长度
		
		mcrypt_generic_init($td, $subkey, $iv); 

		$str = ''; 

		switch($type)
		{ 
			case 'encrypt': 
			  $str = mcrypt_generic($td, $ostr);
			  break; 

			case 'decrypt': 
			  $str = mdecrypt_generic($td, $ostr);
			  break; 
		} 

		mcrypt_generic_deinit($td); 
		mcrypt_module_close($td);

		return $str; 
	}

    public static function pkcs0Pad($text, $mcrypt, $mode){  
        $blocksize = mcrypt_get_block_size($mcrypt, $mode);
        $pad = $blocksize - (strlen($text) % $blocksize);  
        return $text . str_repeat("\0", $pad);
    } 

    public static function pkcs0Unpad($text){
        return trim($text, "\0");
    } 

    public static function pkcs5Pad($text, $mcrypt, $mode){  
        $blocksize = mcrypt_get_block_size($mcrypt, $mode);
        $pad = $blocksize - (strlen($text) % $blocksize);  
        return $text . str_repeat(chr($pad), $pad);  
    }  

    public static function pkcs5Unpad($text){  
        $pad = ord($text{strlen($text) - 1});  
        if ($pad > strlen($text)) return false;  
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;  
        $ret = substr($text, 0, -1 * $pad);  
        return $ret;  
    }  

    public static function hex2bin($hex = false){  
        $ret = $hex !== false && preg_match('/^[0-9a-fA-F]+$/i', $hex) ? pack("H*", $hex) : false;      
        return $ret;  
    }
}

class Aes 
{
    public static function encrypt($str, $key)
    {
		for ($i=0; $i<16; $i++) $keyBytes[$i] = ord(substr($key,$i,1)) & 0xff;
		$w = self::keyExpansion($keyBytes);
		
		$length = strlen($str);
        $resultBytes = [];
        for($i=0; $i<$length; $i+=16)
        {
			$subStr = substr($str, $i, 16);
			for ($j=0; $j<16; $j++) $strBytes[$j] = ord(substr($subStr,$j,1)) & 0xff;
            $resultBytes = array_merge($resultBytes, self::cipher($strBytes, $w));
        }
        return Bytes::toStr($resultBytes);
    }

    public static function decrypt($str, $key)
    {
        for ($i=0; $i<16; $i++) $keyBytes[$i] = ord(substr($key,$i,1)) & 0xff;
        $w = self::keyExpansion($keyBytes);

		$length = strlen($str);
        $resultBytes = [];
        for($i=0; $i<$length; $i+=16)
        {
            $subStr = substr($str, $i, 16);
            for ($j=0; $j<16; $j++) $strBytes[$j] = ord(substr($subStr,$j,1)) & 0xff;
            $resultBytes = array_merge($resultBytes, self::invCipher($strBytes, $w));
        }

        return Bytes::toStr($resultBytes);
    }

    public static function cipher($str, $w)
    {
        $state = [];
        for($r=0; $r<4; $r++)
        {
            for($c=0; $c<4; $c++)
            {
                $state[$r][$c] = $str[$c*4+$r];
            }
        }

        $state = self::addRoundKey($state, $w[0]);

        for($i=1; $i<=10; $i++)
        {
            $state = self::subBytes($state);
            $state = self::shiftRows($state);
            if($i!=10)
            {
                $state = self::mixColumns($state);
            }

            $state = self::addRoundKey($state,$w[$i]);
        }

        for($r=0; $r<4; $r++)
        {
            for($c=0; $c<4; $c++)
            {
                $str[$c*4+$r] = $state[$r][$c];
            }
        }

        return $str;
    }

    public static function invCipher($str, $w)
    {
        $state = [];
        for($r=0; $r<4; $r++)
        {
            for($c=0; $c<4 ;$c++)
            {
                $state[$r][$c] = $str[$c*4+$r];
            }
        }

        $state = self::addRoundKey($state, $w[10]);
        for($i=9; $i>=0; $i--)
        {
            $state = self::invShiftRows($state);
            $state = self::invSubBytes($state);
            $state = self::addRoundKey($state, $w[$i]);
            if($i)
            {
                $state = self::invMixColumns($state);
            }
        }

        for($r=0; $r<4; $r++)
        {
            for($c=0; $c<4 ;$c++)
            {
                $str[$c*4+$r] = $state[$r][$c];
            }
        }

        return $str;
    }

    /**
     * [addRoundKey xor Round Key into state S [§5.1.4] ]
     * @Author   WirrorYin
     * @DateTime 2016-10-27T16:46:18+0800
     * @param    [type]                   $state [description]
     * @param    [type]                   $k     [description]
     */
    private static function addRoundKey($state, $k) 
    {
        for ($c=0; $c<4; $c++) 
        { 
            for ($r=0; $r<4; $r++)
			{
				$state[$r][$c] ^= $k[$r][$c];
			}
        } 

        return $state; 
    } 

    /**
     * [subBytes apply SBox to state S [§5.1.1] ]
     * @Author   WirrorYin
     * @DateTime 2016-10-27T16:47:12+0800
     * @param    [type]                   $s [description]
     * @return   [type]                      [description]
     */
    private static function subBytes($state) 
    { 
        for ($r=0; $r<4; $r++) 
        { 
            for ($c=0; $c<4; $c++)
            {
                $state[$r][$c] = self::$sBox[$state[$r][$c]]; 
            } 
        } 
        return $state; 
    } 

    /**
     * [shiftRows shift row r of state S left by r bytes [§5.1.2] ]
     * @Author   WirrorYin
     * @DateTime 2016-10-27T16:49:25+0800
     * @param    [type]                   $state [description]
     * @return   [type]                          [description]
     */
    private static function shiftRows($state) 
    {
        $t = array(4); 
        for ($r=1; $r<4; $r++) 
        { 
            for ($c=0; $c<4; $c++) $t[$c] = $state[$r][($c+$r)%4]; // shift into temp copy 
            for ($c=0; $c<4; $c++) $state[$r][$c] = $t[$c];      // and copy back 
        } 
        return $state; // see fp.gladman.plus.com/cryptography_technology/rijndael/aes.spec.311.pdf  
    } 

    /**
     * [mixColumns combine bytes of each col of state S [§5.1.3] ]
     * @Author   WirrorYin
     * @DateTime 2016-10-27T16:51:47+0800
     * @param    [type]                   $state [description]
     * @return   [type]                          [description]
     */
    private static function mixColumns($state) 
    {
        $t = [];
        for ($c=0; $c<4; $c++) 
        { 
            for ($r=0; $r<4; $r++) 
            { 
                $t[$r] = $state[$r][$c]; 
            } 

            for ($r=0; $r<4; $r++) 
            { 
                $state[$r][$c] = self::fFmul(0x02, $t[$r]) 
                    ^ self::fFmul(0x03, $t[($r+1)%4]) 
                    ^ self::fFmul(0x01, $t[($r+2)%4]) 
                    ^ self::fFmul(0x01, $t[($r+3)%4]);
            } 
        } 
        return $state; 
    }

    /**
     * [fFmul description]
     * @Author   WirrorYin
     * @DateTime 2016-10-27T16:54:06+0800
     * @param    [type]                   $a [description]
     * @param    [type]                   $b [description]
     * @return   [type]                      [description]
     */
    private static function fFmul($a, $b)
    {
        $bw = [$b];
        $res=0;
        for($i=1; $i<4; $i++)
        {
            $bw[$i] = ($bw[$i-1]<<1) & 0xFF;
            if($bw[$i-1]&0x80)
            {
                $bw[$i]^=0x1b;
            }
        }
        
        for($i=0; $i<4; $i++)
        {
            if(($a>>$i)&0x01)
            {
                $res ^= $bw[$i];
            }
        }
        return $res;
    }

    private static function invSubBytes($state)
    {
        for($r=0; $r<4; $r++)
        {
            for($c=0; $c<4; $c++)
            {
                $state[$r][$c] = self::$invsBox[$state[$r][$c]];
            }
        }

        return $state;
    }

    private static function invShiftRows($state)
    {
        $t = [];
        for($r=1; $r<4; $r++)
        {
            for($c=0; $c<4; $c++)
            {
                $t[$c] = $state[$r][($c-$r+4)%4];
            }
            for($c=0; $c<4; $c++)
            {
                $state[$r][$c] = $t[$c];
            }
        }

        return $state;
    }

    private static function invMixColumns($state)
    {
        $t = [];
        for($c=0; $c< 4; $c++)
        {
            for($r=0; $r<4; $r++)
            {
                $t[$r] = $state[$r][$c];
            }
            for($r=0; $r<4; $r++)
            {
                $state[$r][$c] = self::fFmul(0x0e, $t[$r])
                    ^ self::fFmul(0x0b, $t[($r+1)%4])
                    ^ self::fFmul(0x0d, $t[($r+2)%4])
                    ^ self::fFmul(0x09, $t[($r+3)%4]);
            }
        }

        return $state;
    }


    /**
     * [keyExpansion generate Key Schedule from Cipher Key [§5.2] ]
     * @Author   WirrorYin
     * @DateTime 2016-10-27T17:08:13+0800
     * @param    [type]                   $key [description]
     * @return   [type]                        [description]
     */
    public static function keyExpansion($key) 
    {
        $w = [];
        $rc= [0x01, 0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1b, 0x36];
        for($r=0; $r<4; $r++)
        {
            for($c=0; $c<4; $c++)
            {
                $w[0][$r][$c] = $key[$r+$c*4];
            }
        }
        for($i=1; $i<=10; $i++)
        {
            for($j=0; $j<4; $j++)
            {
                $t = [];
                for($r=0; $r<4; $r++)
                {
                    $t[$r] = $j ? $w[$i][$r][$j-1] : $w[$i-1][$r][3];
                }
                if($j == 0)
                {
                    $temp = $t[0];
                    for($r=0; $r<3; $r++)
                    {
                        $t[$r] = self::$sBox[$t[($r+1)%4]];
                    }
                    $t[3] = self::$sBox[$temp];
                    $t[0] ^= $rc[$i-1];
                }
                for($r=0; $r<4; $r++)
                {
                    $w[$i][$r][$j] = $w[$i-1][$r][$j] ^ $t[$r];
                }
            }
        }

        return $w; 
    } 

    private static function subWord($w) {  // apply SBox to 4-byte word w 
        for ($i=0; $i<4; $i++) $w[$i] = self::$sBox[$w[$i]]; 
        return $w; 
    } 

    private static function rotWord($w) {  // rotate 4-byte word w left by one byte 
        $tmp = $w[0]; 
        for ($i=0; $i<3; $i++) $w[$i] = $w[$i+1]; 
        $w[3] = $tmp; 
        return $w; 
    } 

    // sBox is pre-computed multiplicative inverse in GF(2^8) used in subBytes and keyExpansion [§5.1.1] 
    private static $sBox = array( 
        0x63,0x7c,0x77,0x7b,0xf2,0x6b,0x6f,0xc5,0x30,0x01,0x67,0x2b,0xfe,0xd7,0xab,0x76, 
        0xca,0x82,0xc9,0x7d,0xfa,0x59,0x47,0xf0,0xad,0xd4,0xa2,0xaf,0x9c,0xa4,0x72,0xc0, 
        0xb7,0xfd,0x93,0x26,0x36,0x3f,0xf7,0xcc,0x34,0xa5,0xe5,0xf1,0x71,0xd8,0x31,0x15, 
        0x04,0xc7,0x23,0xc3,0x18,0x96,0x05,0x9a,0x07,0x12,0x80,0xe2,0xeb,0x27,0xb2,0x75, 
        0x09,0x83,0x2c,0x1a,0x1b,0x6e,0x5a,0xa0,0x52,0x3b,0xd6,0xb3,0x29,0xe3,0x2f,0x84, 
        0x53,0xd1,0x00,0xed,0x20,0xfc,0xb1,0x5b,0x6a,0xcb,0xbe,0x39,0x4a,0x4c,0x58,0xcf, 
        0xd0,0xef,0xaa,0xfb,0x43,0x4d,0x33,0x85,0x45,0xf9,0x02,0x7f,0x50,0x3c,0x9f,0xa8, 
        0x51,0xa3,0x40,0x8f,0x92,0x9d,0x38,0xf5,0xbc,0xb6,0xda,0x21,0x10,0xff,0xf3,0xd2, 
        0xcd,0x0c,0x13,0xec,0x5f,0x97,0x44,0x17,0xc4,0xa7,0x7e,0x3d,0x64,0x5d,0x19,0x73, 
        0x60,0x81,0x4f,0xdc,0x22,0x2a,0x90,0x88,0x46,0xee,0xb8,0x14,0xde,0x5e,0x0b,0xdb, 
        0xe0,0x32,0x3a,0x0a,0x49,0x06,0x24,0x5c,0xc2,0xd3,0xac,0x62,0x91,0x95,0xe4,0x79, 
        0xe7,0xc8,0x37,0x6d,0x8d,0xd5,0x4e,0xa9,0x6c,0x56,0xf4,0xea,0x65,0x7a,0xae,0x08, 
        0xba,0x78,0x25,0x2e,0x1c,0xa6,0xb4,0xc6,0xe8,0xdd,0x74,0x1f,0x4b,0xbd,0x8b,0x8a, 
        0x70,0x3e,0xb5,0x66,0x48,0x03,0xf6,0x0e,0x61,0x35,0x57,0xb9,0x86,0xc1,0x1d,0x9e, 
        0xe1,0xf8,0x98,0x11,0x69,0xd9,0x8e,0x94,0x9b,0x1e,0x87,0xe9,0xce,0x55,0x28,0xdf, 
        0x8c,0xa1,0x89,0x0d,0xbf,0xe6,0x42,0x68,0x41,0x99,0x2d,0x0f,0xb0,0x54,0xbb,0x16
    ); 

    private static $invsBox = array(
        0x52,0x09,0x6a,0xd5,0x30,0x36,0xa5,0x38,0xbf,0x40,0xa3,0x9e,0x81,0xf3,0xd7,0xfb, /*0*/
        0x7c,0xe3,0x39,0x82,0x9b,0x2f,0xff,0x87,0x34,0x8e,0x43,0x44,0xc4,0xde,0xe9,0xcb, /*1*/
        0x54,0x7b,0x94,0x32,0xa6,0xc2,0x23,0x3d,0xee,0x4c,0x95,0x0b,0x42,0xfa,0xc3,0x4e, /*2*/
        0x08,0x2e,0xa1,0x66,0x28,0xd9,0x24,0xb2,0x76,0x5b,0xa2,0x49,0x6d,0x8b,0xd1,0x25, /*3*/
        0x72,0xf8,0xf6,0x64,0x86,0x68,0x98,0x16,0xd4,0xa4,0x5c,0xcc,0x5d,0x65,0xb6,0x92, /*4*/
        0x6c,0x70,0x48,0x50,0xfd,0xed,0xb9,0xda,0x5e,0x15,0x46,0x57,0xa7,0x8d,0x9d,0x84, /*5*/
        0x90,0xd8,0xab,0x00,0x8c,0xbc,0xd3,0x0a,0xf7,0xe4,0x58,0x05,0xb8,0xb3,0x45,0x06, /*6*/
        0xd0,0x2c,0x1e,0x8f,0xca,0x3f,0x0f,0x02,0xc1,0xaf,0xbd,0x03,0x01,0x13,0x8a,0x6b, /*7*/
        0x3a,0x91,0x11,0x41,0x4f,0x67,0xdc,0xea,0x97,0xf2,0xcf,0xce,0xf0,0xb4,0xe6,0x73, /*8*/
        0x96,0xac,0x74,0x22,0xe7,0xad,0x35,0x85,0xe2,0xf9,0x37,0xe8,0x1c,0x75,0xdf,0x6e, /*9*/
        0x47,0xf1,0x1a,0x71,0x1d,0x29,0xc5,0x89,0x6f,0xb7,0x62,0x0e,0xaa,0x18,0xbe,0x1b, /*a*/
        0xfc,0x56,0x3e,0x4b,0xc6,0xd2,0x79,0x20,0x9a,0xdb,0xc0,0xfe,0x78,0xcd,0x5a,0xf4, /*b*/
        0x1f,0xdd,0xa8,0x33,0x88,0x07,0xc7,0x31,0xb1,0x12,0x10,0x59,0x27,0x80,0xec,0x5f, /*c*/
        0x60,0x51,0x7f,0xa9,0x19,0xb5,0x4a,0x0d,0x2d,0xe5,0x7a,0x9f,0x93,0xc9,0x9c,0xef, /*d*/
        0xa0,0xe0,0x3b,0x4d,0xae,0x2a,0xf5,0xb0,0xc8,0xeb,0xbb,0x3c,0x83,0x53,0x99,0x61, /*e*/
        0x17,0x2b,0x04,0x7e,0xba,0x77,0xd6,0x26,0xe1,0x69,0x14,0x63,0x55,0x21,0x0c,0x7d /*f*/
    ); 

    // rCon is Round Constant used for the Key Expansion [1st col is 2^(r-1) in GF(2^8)] [§5.2] 
    private static $rCon = array(  
        array(0x00, 0x00, 0x00, 0x00), 
        array(0x01, 0x00, 0x00, 0x00), 
        array(0x02, 0x00, 0x00, 0x00), 
        array(0x04, 0x00, 0x00, 0x00), 
        array(0x08, 0x00, 0x00, 0x00), 
        array(0x10, 0x00, 0x00, 0x00), 
        array(0x20, 0x00, 0x00, 0x00), 
        array(0x40, 0x00, 0x00, 0x00), 
        array(0x80, 0x00, 0x00, 0x00), 
        array(0x1b, 0x00, 0x00, 0x00), 
        array(0x36, 0x00, 0x00, 0x00) 
    );

}

class Bytes 
{
    public static function getBytes($string) {  
        $bytes = array();  
        for($i = 0; $i < strlen($string); $i++){  
             $bytes[] = ord($string[$i]);  
        }  
        return $bytes;  
    }  
   
    public static function toStr($bytes) {  
        $str = '';  
        foreach($bytes as $ch) {  
            $str .= chr($ch);  
        }  
   
           return $str;  
    }  
   
    public static function integerToBytes($val) {  
        $byt = array();  
        $byt[0] = ($val & 0xff);  
        $byt[1] = ($val >> 8 & 0xff);  
        $byt[2] = ($val >> 16 & 0xff);  
        $byt[3] = ($val >> 24 & 0xff);  
        return $byt;  
    }  
   
    public static function bytesToInteger($bytes, $position) {  
        $val = 0;  
        $val = $bytes[$position + 3] & 0xff;  
        $val <<= 8;  
        $val |= $bytes[$position + 2] & 0xff;  
        $val <<= 8;  
        $val |= $bytes[$position + 1] & 0xff;  
        $val <<= 8;  
        $val |= $bytes[$position] & 0xff;  
        return $val;  
    }  
   
   
    public static function shortToBytes($val) {  
        $byt = array();  
        $byt[0] = ($val & 0xff);  
        $byt[1] = ($val >> 8 & 0xff);  
        return $byt;  
    }  
   
    public static function bytesToShort($bytes, $position) {  
        $val = 0;  
        $val = $bytes[$position + 1] & 0xFF;  
        $val = $val << 8;  
        $val |= $bytes[$position] & 0xFF;  
        return $val;  
    }  
   
}  
