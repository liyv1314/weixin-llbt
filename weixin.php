<?php
/**
  * wechat php test
  */
//define your token
header("Content-type: text/html; charset=utf-8"); 
define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid(); 
//ע�͵���֤����
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
                //��γ��
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
                    $contentStr="��ӭ��ע�����������������������^_^���ظ���?����������H����h����help���򡾰�������ȡ�������";                   
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
               }
            
            switch($type)
              {
                    //ͼƬ��Ϣ
                    case "image":
                        $contentStr="������!";
                        break;
                    //������Ϣ
                    case "voice":
                        $contentStr="�����������!";        
                        break;
                    //��Ƶ��Ϣ
                    case "video":
                        $contentStr="��Ƭ���Ҹ�����!";     
                        break;
                    //����λ����Ϣ
                    case "location":
                        $contentStr="���γ����{$weiDu}��������{$jingDu}������׼��!";   
                        break;
                    //�ı���Ϣ
                    case "text":
                      {
                        if(!empty($keyword))
                          {
                            if($keyword == "?"||$keyword == "��"||$keyword == "H"||$keyword == "h"||$keyword == "help"||$keyword == "����")
                              {
                               $contentStr = "�˽�Ͷ�巽ʽ��ظ���Ͷ�塿\n�������������ظ������ڡ�\nlol��ѯ��ظ���lol����LOL��";
                               $msgType = "text";
                               $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                               echo $resultStr;
                              }
                            elseif($keyword== "lol"||$keyword == "LOL"||$keyword == "Ӣ������")
                              {
                               $contentStr = "<a href='http://www.lolhelper.cn/word.php'>lol�ؼ��ʲ�ѯ</a>\n\n<a href='http://www.lolhelper.cn/'>lol���طֲ�ѯ</a>\n\n<a href='http://www.lolhelper.cn/ability.php'>lol�ۺ�ʵ����ѯ</a>\n\n<a href='http://www.lolhelper.cn/kengdie.php'>lol�ӵ�������ѯ</a>";
                               $msgType = "text";
                               $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                               echo $resultStr;
                              }
                            elseif($keyword== "ivim"||$keyword == "IVIM"||$keyword == "����")
                              {
                               $contentStr = "<a href='http://ivim.sinaapp.com'>IVIM����</a>";
                               $msgType = "text";
                               $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                               echo $resultStr;
                              }
                            elseif($keyword == "����"||$keyword == "Ͷ��")
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
         //��Ӧif (!empty($postStr))
         }
    //��Ӧpublic function responseMsg() 
    }
    
    
    
    //��ͼ�Ļظ�
    function replyNews($fromUsername, $toUsername, $time, $keyword){
          if($keyword=="����")
            {
               $title="�������������Щ���ഺ�Ͱ�ȥ���ĸ�";
               $description="��Ȼÿ�춼��������ͬ����ͬ��������֣�����ͬ��ȴ�����ҵ���ͬ��ĵ�Ӱ��������Щ���˷ܽ���Я��һ��СС���˵��������ɣ���Щ�������������������������������������ĳһƬ������Ƭ�С�";
               $picurl="http://mmbiz.qpic.cn/mmbiz/EaNCvycrfVTyQ4gdCSk2E4I8EVXzITich3Clc6miaQ0oZXVPa5tBBibibm6GM4EhmzSIQLjk9fOIz3Ent4bdkT0PwQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5";
               $url="http://mp.weixin.qq.com/s?__biz=MzAwMjI5MDg1MQ==&mid=204677034&idx=1&sn=4c5c983a7e0ec85234cd918dbc445c10#rd";
             }
          if($keyword=="Ͷ��")
            {
              $title="����@�ң���С�����";
              $description="�����и�����ϢҪ���ߴ�ң�΢�ź�̨�ѳɹ�����ͼ������ˣ����ĵ�ʱ�����������Ŷ����ҳ����֮�󻹻�Ϊ����������������Ȥ�Ĺ��ܣ������ڴ��ϡ�";
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

    
    // ͼ�������
    /*function tuling($keyword) {
        header("Content-type: text/html; charset=utf-8"); 
        //$key = "e91017d20d9db8e0b3e3251dffa75a64";
        $key = "d24ad2cd4f8203991ab58de1b36f2b52";
        $api_url = "http://www.tuling123.com/openapi/api?key=".$key."&info=".$keyword;
        $result = file_get_contents ( $api_url );
        //��ѡ,Ĭ�� false תΪ����,true תΪ����.
        $result = json_decode ( $result,true ); 
        if($result['code'] == 100000)
          return $result['text'];
    } */
        // ͼ�������
    function tuling($keyword) {
        $key="d24ad2cd4f8203991ab58de1b36f2b52";
        $api_url = "http://www.tuling123.com/openapi/api?key=".$key."&info=".$keyword;
        $result = file_get_contents ( $api_url );
        $result = json_decode ( $result, true );
        
        switch ($result ['code']) {
          case '200000' :
            $text = $result ['text'] . ',<a href="' . $result ['url'] . '">�������</a>';
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