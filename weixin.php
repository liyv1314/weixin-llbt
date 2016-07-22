<?php
/**
  * wechat php test
  */
//define your token
header("Content-type: text/html; charset=utf-8"); 
define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid(); 
//注释掉验证函数
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
            
                $type =$postObj->MsgType;
                $customevent =$postObj->Event;
                //经纬度
                $weiDu = $postObj->Location_X;
                $jingDu= $postObj->Location_Y;
            
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
                
            if($type=="event" and $customevent=="subscribe")
              {
                    $msgType = "text";
                    $contentStr="欢迎关注浪里白条，常来和我聊天呦^_^，回复【?】【？】【H】【h】【help】或【帮助】获取更多帮助";                   
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
               }
            
            switch($type)
              {
                    //图片消息
                    case "image":
                        $contentStr="不错诶!";
                        break;
                    //语音消息
                    case "voice":
                        $contentStr="你声音真好听!";        
                        break;
                    //视频消息
                    case "video":
                        $contentStr="这片子我给满分!";     
                        break;
                    //地理位置消息
                    case "location":
                        $contentStr="你的纬度是{$weiDu}，经度是{$jingDu}，导弹准备!";   
                        break;
                    //文本消息
                    case "text":
                      {
                        if(!empty($keyword))
                          {
                            if($keyword == "?"||$keyword == "？"||$keyword == "H"||$keyword == "h"||$keyword == "help"||$keyword == "帮助")
                              {
                               $contentStr = "了解投稿方式请回复【投稿】\n浏览往期内容请回复【往期】\nlol查询请回复【lol】【LOL】";
                               $msgType = "text";
                               $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                               echo $resultStr;
                              }
                            elseif($keyword== "lol"||$keyword == "LOL"||$keyword == "英雄联盟")
                              {
                               $contentStr = "<a href='http://www.lolhelper.cn/word.php'>lol关键词查询</a>\n\n<a href='http://www.lolhelper.cn/'>lol隐藏分查询</a>\n\n<a href='http://www.lolhelper.cn/ability.php'>lol综合实力查询</a>\n\n<a href='http://www.lolhelper.cn/kengdie.php'>lol坑爹能力查询</a>";
                               $msgType = "text";
                               $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                               echo $resultStr;
                              }
                            elseif($keyword== "ivim"||$keyword == "IVIM"||$keyword == "博客")
                              {
                               $contentStr = "<a href='http://ivim.sinaapp.com'>IVIM博客</a>";
                               $msgType = "text";
                               $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                               echo $resultStr;
                              }
                            elseif($keyword == "往期"||$keyword == "投稿")
                               $this ->replyNews($fromUsername, $toUsername, $time, $keyword);
                            else
                                {
                                 $contentStr = $this ->tuling($keyword);
                            
                                 if(!is_array($contentStr)) 
                                   {
                                     $contentStr = str_replace('<br>', '', $contentStr);
                                    $msgType = "text";
                                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                                    echo $resultStr;
                                    }
                                 else
                                   $this -> replyNews1($fromUsername, $toUsername, $time, $contentStr);
                                 }
                           } 
                       }
                     break;  
                }
             $msgType = "text";
             $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
             echo $resultStr;
         //对应if (!empty($postStr))
         }
    //对应public function responseMsg() 
    }
    
    
    
    //单图文回复
    function replyNews($fromUsername, $toUsername, $time, $keyword){
          if($keyword=="往期")
            {
               $title="【浪里白条】那些叫青春和爱去死的歌";
               $description="虽然每天都是在听不同领域不同地域的音乐，看不同的却又能找到相同点的电影，正如那些令人奋进又携带一点小小悲伤的优雅旋律，那些跳动的音符总是能钻进你的心里，深深扎根在你某一片记忆碎片中。";
               $picurl="http://mmbiz.qpic.cn/mmbiz/EaNCvycrfVTyQ4gdCSk2E4I8EVXzITich3Clc6miaQ0oZXVPa5tBBibibm6GM4EhmzSIQLjk9fOIz3Ent4bdkT0PwQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5";
               $url="http://mp.weixin.qq.com/s?__biz=MzAwMjI5MDg1MQ==&mid=204677034&idx=1&sn=4c5c983a7e0ec85234cd918dbc445c10#rd";
             }
          if($keyword=="投稿")
            {
              $title="有人@我：致小伙伴书";
              $description="首先有个好消息要告诉大家，微信后台已成功接入图灵机器人，无聊的时候多找他玩玩哦。主页菌在之后还会为白条君加许许多多有趣的功能，敬请期待呦。";
              $picurl="http://mmbiz.qpic.cn/mmbiz/EaNCvycrfVRw27nqC33qAp2bwnxkmWicVI23NAF7ePfFLRtjoicFMfhGl6UiaUu2NxbicwoRKbpztoZM0nUibH4rKCQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5";
              $url="http://mp.weixin.qq.com/s?__biz=MzAwMjI5MDg1MQ==&mid=400145039&idx=1&sn=4fa0d8cf70eff234ebb2a972fb0c2fb7#wechat_redirect";
             }
              $newsTpl="<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                        <Articles>
                        <item>
                        <Title><![CDATA[$title]]></Title> 
                        <Description><![CDATA[$description]]></Description>
                        <PicUrl><![CDATA[$picurl]]></PicUrl>
                        <Url><![CDATA[$url]]></Url>
                        </item>
                        </Articles>
                        </xml> ";
          $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time);
          echo $resultStr;
    }

    function replyNews1($fromUsername, $toUsername, $time, $contentStr){
            for($i=0;$i<5;$i++)
                 {
                  $title[$i] = $contentStr[$i][0];
                  $description[$i] = $contentStr[$i][1];
                  $picurl[$i] = $contentStr[$i][2];
                  $url[$i] = $contentStr[$i][3];
                  }
              $newsTpl="<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>5</ArticleCount>
                        <Articles>
                        <item>
                        <Title><![CDATA[$title[0]]]></Title> 
                        <Description><![CDATA[$description[0]]]></Description>
                        <PicUrl><![CDATA[$picurl[0]]]></PicUrl>
                        <Url><![CDATA[$url[0]]]></Url>
                        </item>
                        <item>
                        <Title><![CDATA[$title[1]]]></Title> 
                        <Description><![CDATA[$description[1]]]></Description>
                        <PicUrl><![CDATA[$picurl[1]]]></PicUrl>
                        <Url><![CDATA[$url[1]]]></Url>
                        </item>
                        <item>
                        <Title><![CDATA[$title[2]]]></Title> 
                        <Description><![CDATA[$description[2]]]></Description>
                        <PicUrl><![CDATA[$picurl[2]]]></PicUrl>
                        <Url><![CDATA[$url[2]]]></Url>
                        </item>
                        <item>
                        <Title><![CDATA[$title[3]]]></Title> 
                        <Description><![CDATA[$description[3]]]></Description>
                        <PicUrl><![CDATA[$picurl[3]]]></PicUrl>
                        <Url><![CDATA[$url[3]]]></Url>
                        </item>
                        <item>
                        <Title><![CDATA[$title[4]]]></Title> 
                        <Description><![CDATA[$description[4]]]></Description>
                        <PicUrl><![CDATA[$picurl[4]]]></PicUrl>
                        <Url><![CDATA[$url[4]]]></Url>
                        </item>
                        </Articles>
                        </xml> ";
        /*$title = $contentStr[0][0];
              $description = $contentStr[0][1];
              $picurl = $contentStr[0][2];
              $url = $contentStr[0][3];
              $newsTpl="<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                        <Articles>
                        <item>
                        <Title><![CDATA[$title]]></Title> 
                        <Description><![CDATA[$description]]></Description>
                        <PicUrl><![CDATA[$picurl]]></PicUrl>
                        <Url><![CDATA[$url]]></Url>
                        </item>
                        </Articles>
                        </xml> ";*/
          $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time);
          echo $resultStr;
    }

    
    // 图灵机器人
    /*function tuling($keyword) {
        header("Content-type: text/html; charset=utf-8"); 
        //$key = "e91017d20d9db8e0b3e3251dffa75a64";
        $key = "d24ad2cd4f8203991ab58de1b36f2b52";
        $api_url = "http://www.tuling123.com/openapi/api?key=".$key."&info=".$keyword;
        $result = file_get_contents ( $api_url );
        //可选,默认 false 转为对象,true 转为数组.
        $result = json_decode ( $result,true ); 
        if($result['code'] == 100000)
          return $result['text'];
    } */
        // 图灵机器人
    function tuling($keyword) {
        $key="d24ad2cd4f8203991ab58de1b36f2b52";
        $api_url = "http://www.tuling123.com/openapi/api?key=".$key."&info=".$keyword;
        $result = file_get_contents ( $api_url );
        $result = json_decode ( $result, true );
        
        switch ($result ['code']) {
          case '200000' :
            $text = $result ['text'] . ',<a href="' . $result ['url'] . '">点击进入</a>';
            return $text;
            break;
          case '302000' :
            $length = count($result['list']) > 9 ? 9 :count($result['list']);
            for($i= 0;$i<$length;$i++){
                $articles [$i] = array (
                    $result['list'][$i]['article'],
                    $result['list'][$i]['source'],
                    $result['list'][$i]['icon'],
                    $result['list'][$i]['detailurl']
                );
            }
            return $articles;
            break;
            /*case '305000' :
            $length = count($result['list']) > 9 ? 9 :count($result['list']);
            for($i= 0;$i<$length;$i++){
                $articles [$i] = array (
                    $result['list'][$i]['start'] . '--' . $result['list'][$i]['terminal'],
                    $result['list'][$i]['starttime'] . '--' . $result['list'][$i]['endtime'],
                    $result['list'][$i]['icon'],
                    $result['list'][$i]['detailurl']
                );
            }
            return $articles;
            break;
          case '306000' :
            $length = count($result['list']) > 9 ? 9 :count($result['list']);
            for($i= 0;$i<$length;$i++){
                $articles [$i] = array (
                    $result['list'][$i]['flight'] . '--' . $result['list'][$i]['route'],
                    $result['list'][$i]['starttime'] . '--' . $result['list'][$i]['endtime'],
                    $result['list'][$i]['icon'],
                    $result['list'][$i]['detailurl']
                );
            }
            return $articles;
            break;*/
            case '308000' :
            $length = count($result['list']) > 9 ? 9 :count($result['list']);
            for($i= 0;$i<$length;$i++){
                $articles [$i] = array (
                    $result['list'][$i]['name'],
                    $result['list'][$i]['info'],
                    $result['list'][$i]['icon'],
                    $result['list'][$i]['detailurl']
                );
            }
            return $articles;
            break;
          default:
            if (empty ( $result ['text'] )) {
                return false;
            } else {
                return $result ['text'] ;
            }
        }
        
    } 
    
        
    
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
        
	}
    
   
    
}

?>