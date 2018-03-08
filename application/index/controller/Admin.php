<?php
namespace app\index\controller;
use Yunpian\Sdk\YunpianClient;
use app\index\model\User;
use app\index\model\GameBest;

class Admin {

    public function login() {
    	$phone = input("post.phone");
    	$passwd = input("post.passwd");
    	$user = User::where("phone", $phone)->where("passwd", $passwd)->find();
    	if (!$user) {
    		return json([
    			"error"  =>  1,
    			"msg"    =>  "账号或密码错误"
    		]);
    	}
    	session("user_id", $user->id);
        $data = GameBest::field([
            "game_id", "score"
        ])->where("user_id", $user->id)->select();
    	return json([
			"error"  =>  0,
			"msg"    =>  "登陆成功",
            "user"   =>  [
                "nickname"  =>  $user->nickname
            ],
            "data"   =>  $data
		]);
    }

    public function register() {
    	$phone = input("post.phone");
    	if (session("phone_msg_code") != input("post.code")) {
    		if (!session("?phone_msg_code_wrong_times"))
    			session("phone_msg_code_wrong_times", 1);
    		else
    			session("phone_msg_code_wrong_times", session("phone_msg_code_wrong_times") + 1);
    		if (session("phone_msg_code_wrong_times") >= 5) {
    			session("phone_msg_code", null);
    			return json([
	    			"error"  =>  1,
	    			"msg"    =>  "验证码错误次数过多，请重新发送验证码"
	    		]);
    		}
    		return json([
    			"error"  =>  1,
    			"msg"    =>  "验证码错误"
    		]);
    	}
    	if (User::where("phone", $phone)->find()) {
    		return json([
    			"error"  =>  1,
    			"msg"    =>  "该手机号已被注册"
    		]);
    	}
    	$user = new User;
    	$user->phone = $phone;
        $user->nickname = $phone;
    	$user->passwd = input("post.passwd");
    	$user->save();
    	session("phone_msg_code", null);
    	session("phone_msg_code_wrong_times", null);
    	return json([
			"error"  =>  0,
			"msg"    =>  "注册成功"
		]);
    }

    public function reset_passwd() {
        $phone = input("post.phone");
        if (session("phone_msg_code") != input("post.code")) {
            if (!session("?phone_msg_code_wrong_times"))
                session("phone_msg_code_wrong_times", 1);
            else
                session("phone_msg_code_wrong_times", session("phone_msg_code_wrong_times") + 1);
            if (session("phone_msg_code_wrong_times") >= 5) {
                session("phone_msg_code", null);
                return json([
                    "error"  =>  1,
                    "msg"    =>  "验证码错误次数过多，请重新发送验证码"
                ]);
            }
            return json([
                "error"  =>  1,
                "msg"    =>  "验证码错误"
            ]);
        }
        $user = User::where("phone", $phone)->find();
        if (!$user) {
            return json([
                "error"  =>  1,
                "msg"    =>  "该手机号未注册过"
            ]);
        }
        $user->passwd = input("post.passwd");
        $user->save();
        session("phone_msg_code", null);
        session("phone_msg_code_wrong_times", null);
        return json([
            "error"  =>  0,
            "msg"    =>  "重置密码成功"
        ]);
    }

    public function request_message() {
    	$token = config("token.yunpian_token");
    	if (input("post.token") != $token) {
    		return json([
				"error"  =>  1,
				"msg"    =>  "wrong token"
			]);
    	}
    	if (session("?phone_msg_code"))
    		$code = session("phone_msg_code");
    	else {
    		$code = rand(100000,999999);
	    	session("phone_msg_code", $code);
	    	session("phone_msg_code_wrong_times", null);
    	}
		$clnt = YunpianClient::create(config('yunpian.apikey'));
		$param = [
			YunpianClient::MOBILE => input("post.phone"),
			YunpianClient::TEXT => "【梦的天空之城】您的验证码是{$code}。如非本人操作，请忽略本短信"
		];
		$r = $clnt->sms()->single_send($param);
		if ($r->isSucc()) {
			$data = $r->data();
			return json([
				"error"  =>  0
			]);
		} else {
			return json([
				"error"  =>  1,
				"msg"    =>  $r->msg(),
				"detail" =>  $r->detail()
			]);
		}
    }

    public function register_request_message() {
        $user = User::where("phone", input("post.phone"))->find();
        if ($user) {
            return json([
                "error"  =>  1,
                "msg"    =>  "该手机号已被注册"
            ]);
        }
        return $this->request_message();
    }

    public function reset_passwd_request_message() {
    	$user = User::where("phone", input("post.phone"))->find();
    	if (!$user) {
    		return json([
				"error"  =>  1,
				"msg"    =>  "该手机号未注册过"
			]);
    	}
    	return $this->request_message();
    }

    public function upload_score() {
        $token = config("token.score_token");
        if (input("post.token") != $token) {
            return json([
                "error"  =>  1,
                "msg"    =>  "wrong token"
            ]);
        }
        if (!session("?user_id")) {
            return json([
                "error"  =>  1,
                "msg"    =>  "请先登陆"
            ]);
        }
        $game = GameBest::where("game_id", input("post.game"))->where("user_id", session("user_id"))->find();
        if ($game) {
            if ($game->score < (int) input("post.score"))
                $game->score = (int) input("post.score");
            $game->game_name = input("post.name");
            $game->save();
        } else {
            $game = new GameBest();
            $game->user_id = session("user_id");
            $game->game_id = input("post.game");
            $game->game_name = input("post.name");
            $game->score = (int) input("post.score");
            $game->save();
        }
        return json([
            "error"  =>  0
        ]);
    }

    public function test() {
    	
    }
    
}
