<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\User;
use app\index\model\GameBest;
use app\index\model\ChallengeBest;

class Index extends Controller {

    public function index() {
        $score_ids = GameBest::field(["game_id"])->distinct(true)->order("rand()")->select();
        $velocity_ids = ChallengeBest::field(["game_id"])->distinct(true)->order("rand()")->select();
        $score_games = [];
        $velocity_games = [];
        foreach ($score_ids as $item) {
            $score_games[] = GameBest::field([
                "game_id",
                "game_name",
                "(select nickname from user where user.id=user_id)" => "nickname",
                "score"
            ])->where("game_id", $item["game_id"])->order("score desc")->limit(1)->find();
        }
        foreach ($velocity_ids as $item) {
            $velocity_games[] = ChallengeBest::field([
                "game_id",
                "game_name",
                "(select nickname from user where user.id=user_id)" => "nickname",
                "velocity"
            ])->where("game_id", $item["game_id"])->order("velocity desc")->limit(1)->find();
        }
    	$this->assign("score_list", $score_games);
        $this->assign("velocity_list", $velocity_games);
        return $this->fetch();
    }
    
}
