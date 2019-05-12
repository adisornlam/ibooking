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
        $replyToken = $formData['events'][0]['replyToken'];
        $message = $formData['events'][0]['message']['text'];
        $userId = $formData['events'][0]['source']['userId'];
    
        // $response = $this->bot->replyText($replyToken, $message);
    
        if($message === 'สวัสดี'){
            $msg = "สวัสดีจ้า มีอะไรให้น้องส้มจี๊ดช่วยคะ กดส่งตัวเลขมาได้เลยค่ะ \n";
            $msg .= "กด 1 ดูรูปแบบห้องพักพร้อมราคา\n"; 
            $msg .= "กด 2 ดูห้องว่าง \n";
            $msg .= "กด 3 ขอตำแหน่งห้องพักผ่าน map \n";
            $msg .= "กด 4 โทรติดต่อเบอร์ห้องพัก\n";
            $response = $this->bot->replyText($replyToken, $msg);
        } else if($message === '1'){
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
    
            return $this->push($userId, new TemplateMessageBuilder('this is a carousel template', $templateBuilder));
        } else if($message === '2'){
            $templateBuilder = new ButtonTemplateBuilder(
                'Menu',
                'Please select',
                'https://firstblood.io/pages/wp-content/uploads/2018/07/dota-2-hero-guide-970x570.jpg',
                [
                    new MessageTemplateActionBuilder('Buy', 'action=buy&itemid=123'),
                    new PostbackTemplateActionBuilder('Buy', 'action=buy&itemid=123'),
                    new UriTemplateActionBuilder('Buy', 'http://example.com/page/123')
                ]
            );
            return $this->push($userId, new TemplateMessageBuilder('This is a buttons template', $templateBuilder));
        } else if($message === '3'){
            $outputText = new LocationMessageBuilder("Eiffel Tower", "Champ de Mars, 5 Avenue Anatole France, 75007 Paris, France", 48.858328, 2.294750);
            $response = $this->bot->replyMessage($replyToken, $outputText);
        }
        if ($response->isSucceeded()) {
            logger("reply success!!");
            return;
        }
    }

    public function buttonMessagePush(Request $request) {
        // return $this->sendResponse('Succeeded', 'Line push successfully.');
        $templateBuilder = new ButtonTemplateBuilder(
            'Menu',
            'Please select',
            'https://firstblood.io/pages/wp-content/uploads/2018/07/dota-2-hero-guide-970x570.jpg',
            [
                new MessageTemplateActionBuilder('Buy', 'action=buy&itemid=123'),
                new PostbackTemplateActionBuilder('Buy', 'action=buy&itemid=123'),
                new UriTemplateActionBuilder('Buy', 'http://example.com/page/123')
            ]
        );
        return $this->push($request->to, new TemplateMessageBuilder('This is a buttons template', $templateBuilder));
    }


    public function carouselColumnMessagePush(Request $request) {
        $templateBuilder = new CarouselTemplateBuilder([
            new CarouselColumnTemplateBuilder(
                'this is menu',
                'description',
                'https://firstblood.io/pages/wp-content/uploads/2018/07/dota-2-hero-guide-970x570.jpg',
                [
                    new PostbackTemplateActionBuilder('Buy', 'action=buy&itemid=111'),
                    new PostbackTemplateActionBuilder('Add to cart', 'action=add&itemid=111'),
                    new UriTemplateActionBuilder('View detail', 'http://example.com/page/111')
                ]
            ),
            new CarouselColumnTemplateBuilder(
                'this is menu',
                'description',
                'https://firstblood.io/pages/wp-content/uploads/2018/07/feature-970x570.jpg',
                [
                    new PostbackTemplateActionBuilder('Buy', 'action=buy&itemid=111'),
                    new PostbackTemplateActionBuilder('Add to cart', 'action=add&itemid=111'),
                    new UriTemplateActionBuilder('View detail', 'http://example.com/page/111')
                ]
            )
        ]);

        return $this->push($request->to, new TemplateMessageBuilder('this is a carousel template', $templateBuilder));
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