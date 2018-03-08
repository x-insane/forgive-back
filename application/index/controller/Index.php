<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\User;
use app\index\model\GameBest;

class Index extends Controller {

    public function index() {
        $ids = GameBest::field(["game_id"])->distinct(true)->order("rand()")->select();
        $games = [];
        foreach ($ids as $item) {
            $games[] = GameBest::field([
                "game_id",
                "game_name",
                "(select nickname from user where user.id=user_id)" => "nickname",
                "score"
            ])->where("game_id", $item["game_id"])->order("score desc")->limit(1)->find();
        }
    	$this->assign("list", $games);
        return $this->fetch();
    }
    
}
