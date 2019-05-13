<?php

namespace App\Http\Services;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\IconComponentBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;

use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentType;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentIconSize;

use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

use App\LineUser;

class GetMessageService
{
    /**
     * @var LINEBot
     */
    private $bot;
    /**
     * @var HTTPClient
     */
    private $client;

    public function __construct() {
        $httpClient = new CurlHTTPClient(env('LINE_BOT_ACCESS_TOKEN'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_BOT_SECRET')]);
    }
    
    public function replySend($formData)
    {

        $eventObj = $formData['events'][0];

        $replyToken = $eventObj['replyToken'];
        $source_type = $eventObj['source']['type'];
        $message = (isset($eventObj['message']['text'])?$eventObj['message']['text']:NULL);
        $source_userId = (isset($eventObj['source']['userId'])?$eventObj['source']['userId']:NULL);
        $source_roomId = (isset($eventObj['source']['roomId'])?$eventObj['source']['roomId']:NULL);
        $source_groupId = (isset($eventObj['source']['groupId'])?$eventObj['source']['groupId']:NULL);

        // logger("request : ", $eventObj);
        $eventType = $eventObj['type'];
        if($eventType === 'follow'){
            $line_user = new LineUser;
            $line_user->source_type = $source_type;
            $line_user->line_user_id = $source_userId;
            $line_user->line_room_id = $source_roomId;
            $line_user->line_group_id = $source_groupId;
            $line_user->save();
        }else if($eventType === 'message'){
            $userMessage = strtolower($message);
            if($userMessage === 'สวัสดี'){
                $msg = "สวัสดีจ้า มีอะไรให้น้องส้มจี๊ดช่วยคะ กดส่งตัวเลขมาได้เลยค่ะ \n";
                $msg .= "กด 1 ดูรูปแบบห้องพักพร้อมราคา\n"; 
                $msg .= "กด 2 ดูห้องว่าง \n";
                $msg .= "กด 3 ขอตำแหน่งห้องพักผ่าน map \n";
                $msg .= "กด 4 โทรติดต่อเบอร์ห้องพัก\n";
                $response = $this->bot->replyText($replyToken, $msg);
            } else if($userMessage === '1'){
                $templateBuilder = new CarouselTemplateBuilder([
                    new CarouselColumnTemplateBuilder(
                        'ห้องพักรายเดือน',
                        'ค่าเช่า 6,500 บาท/เดือน 1 ห้องนอน 1 ห้องน้ำ 1 ห้องนั่งเล่น',
                        'https://f.hongpak.in.th/media/rooms/photos/18/0628/112018_2632.jpeg',
                        [
                            new PostbackTemplateActionBuilder('เช็คห้องว่าง', 'action=buy&itemid=111'),
                            new PostbackTemplateActionBuilder('จองเลย', 'action=add&itemid=111'),
                            new UriTemplateActionBuilder('ดูรายละเอียดเพิ่มเติม', 'http://example.com/page/111')
                        ]
                    ),
                    new CarouselColumnTemplateBuilder(
                        'ห้องพักรายวัน',
                        'ค่าเช่า 500 บาท/คืน มีฟิตเนสและสระว่ายน้ำให้บริการห้องเฟอร์ฯครบ',
                        'https://f.hongpak.in.th/media/rooms/photos/13/1129/20131129110015-972.JPG',
                        [
                            new PostbackTemplateActionBuilder('เช็คห้องว่าง', 'action=buy&itemid=111'),
                            new PostbackTemplateActionBuilder('จองเลย', 'action=add&itemid=111'),
                            new UriTemplateActionBuilder('ดูรายละเอียดเพิ่มเติม', 'http://example.com/page/111')
                        ]
                    )
                ]);
        
                return $this->push($source_userId, new TemplateMessageBuilder('this is a carousel template', $templateBuilder));
            } else if($userMessage === '2'){
                $templateBuilder = new ButtonTemplateBuilder(
                    'เช็คห้องว่าง',
                    'ห้องรายเดือนว่าง 3 ห้อง ห้องรายวันว่าง 2 ห้อง',
                    'https://images.unsplash.com/photo-1540518614846-7eded433c457?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1339&q=80',
                    [
                        new MessageTemplateActionBuilder('จองเลย', 'action=buy&itemid=123'),
                        // new PostbackTemplateActionBuilder('Buy', 'action=buy&itemid=123'),
                        new UriTemplateActionBuilder('Call', 'tel:0655878538')
                    ]
                );
                return $this->push($source_userId, new TemplateMessageBuilder('This is a buttons template', $templateBuilder));
            } else if($userMessage === '3'){
                $outputText = new LocationMessageBuilder("Eiffel Tower", "Champ de Mars, 5 Avenue Anatole France, 75007 Paris, France", 48.858328, 2.294750);
                $response = $this->bot->replyMessage($replyToken, $outputText);
            } else if($userMessage === '4'){
                $outputText = new TextMessageBuilder("เบอร์ติดต่อ 065-587-8538");
                $response = $this->bot->replyMessage($replyToken, $outputText);
            }
        }else if($eventType === 'join'){
            $line_user = new LineUser;
            $line_user->source_type = $source_type;
            $line_user->line_user_id = $source_userId;
            $line_user->line_room_id = $source_roomId;
            $line_user->line_group_id = $source_groupId;
            $line_user->save();
        }
        // logger("request : ", $eventObj); 
    }

    protected function push($to, $messageBuilder) {
        $response = $this->bot->pushMessage($to, $messageBuilder);
        if ($response->isSucceeded()) {
            return $this->sendResponse('Succeeded', 'Line push successfully.');
        }

        // Failed
        return $this->sendResponse(json_decode($response->getRawBody()), 'Line push error.');
    }
}