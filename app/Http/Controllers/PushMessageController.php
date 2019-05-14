<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\GetMessageService;
use App\User;

class PushMessageController extends Controller
{
    public function push()
    {
        $user = User::find(1)->line()->first();
        $message = new GetMessageService();
        $message->pushSend($user->line_user_id);
        // $this->messageService->pushSend($user->line_user_id);
        // return response()->json(['data'=> $user->line_user_id], 200);
    }
}
