<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/12
 * Time: 10:23
 */

namespace app\api\controller;

use think\Db;
use EasyWeChat\Factory;
use think\Exception;

class Activity extends Common
{

    public function activityList()
    {
        $list = [
            [
                'id' => 1,
                'cover' => 'static/uploads/activity/ac01.png',
                'title' => '文创代言现金红包',
                'end' => 0,
            ],
            [
                'id' => 2,
                'cover' => 'static/uploads/activity/ac02.png',
                'title' => '集齐卡片获精美礼品',
                'end' => 0,
            ],
//            [
//                'id' => 3,
//                'cover' => 'static/uploads/activity/test.jpg',
//                'title' => '揭开文创面纱',
//                'end' => 0,
//            ]
        ];
        return ajax($list);
    }

    public function getQrcode()
    {
        $uid = $this->myinfo['uid'];
        $app = Factory::miniProgram($this->mp_config);
        $response = $app->app_code->getUnlimit($uid, [
            'page' => 'pages/auth/auth',
            'width' => '300'
        ]);
        $png = $uid . '.png';
        $save_path = 'static/uploads/appcode/';
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs($save_path, $png);
        } else {
            return ajax($response, -1);
        }
        return ajax($save_path . $png);
    }

    public function getInviteList()
    {
        try {
            $where = [
                ['i.inviter_id', '=', $this->myinfo['uid']]
            ];
            $list = Db::table('mp_user')->alias('u')
                ->join("mp_invite i", "u.id=i.to_uid", "left")
                ->where($where)
                ->field("u.nickname,i.*")
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $rule = "新手任务中有多个任务，好友需根据要求，完成所有任务并全部获得奖励后，才会被计算为一个有效好友。";
        $data['list'] = $list;
        $data['rule'] = $rule;
        return ajax($data);
    }

    // 获取卡片列表
    public function getCardList()
    {
        try {
            $list = Db::table('mp_card')->alias('c')
                ->join('mp_user_card uc', 'uc.cid = c.id AND uc.uid = ' . $this->myinfo['uid'], 'left')
                ->group('c.id')
                ->field('c.id, c.title, c.pic, c.pic_back, count(uc.id) as card_amount')
                ->select();

            // 剩余抽奖次数
            $lucky_draw_times = Db::table('mp_user')
                ->where([['id', '=', $this->myinfo['uid']]])
                ->value('lucky_draw_times');

            // 获取用户身份
            $my = Db::table('mp_user')
                ->where([['id', '=', $this->myinfo['uid']]])
                ->find();
            if ($my['auth'] != 2) {
                $role = 0;
            } else {
                $role = $my['role'];
            }

            $data = [
                'list' => $list,
                'lucky_draw_times' => $lucky_draw_times,
                'role' => $role
            ];
            return ajax($data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }

    // 抽卡
    public function getCard()
    {
        try {
            $lucky_draw_times = Db::table('mp_user')
                ->where([['id', '=', $this->myinfo['uid']]])
                ->value('lucky_draw_times');

            if (!$lucky_draw_times) {
                return ajax('您已经没有抽卡机会了', 49);
            } else {
                $cid = $this->get_rand_card();

                Db::startTrans();
                $data = [
                    'cid' => $cid,
                    'uid' => $this->myinfo['uid'],
                    'create_time' => time()
                ];
                Db::table('mp_user_card')->insert($data);

                Db::table('mp_user')
                    ->where([['id', '=', $this->myinfo['uid']]])
                    ->setDec('lucky_draw_times', 1);

                if ($cid == 5) {
                    // 查看是否得到过现金奖励，未获得过则获得现金奖励
                    $temp_get_gift = $this->check_gift($this->myinfo['uid'], 1);
                    $get_gift = $temp_get_gift ? $temp_get_gift : 0;
                } else {
                    // 查看是否获得过文创礼品，没有获得过文创礼品查看是否得到全部8张卡片，如果集齐则获得文创礼品
                    $temp_get_gift = $this->check_gift($this->myinfo['uid'], 2);
                    $get_gift = $temp_get_gift ? $temp_get_gift : 0;
                }
                Db::commit();
            }

            $data = [
                'card_id' => $cid,
                'get_gift' => $get_gift
            ];
            return ajax($data);
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
    }

    // 生成卡片码（赠送用）
    public function createCardCode()
    {
        $post['cid'] = input('post.cid');
        $this->checkPost($post);

        $cid = $post['cid'];

        // 合法的卡号
        if (!preg_match('/^[1-9]$/', $cid)) {
            return ajax('卡片id不合法', 49);
        }

        try {
            $w = [
                ['uid', '=', $this->myinfo['uid']],
                ['cid', '=', $cid]
            ];
            $card_count = Db::table('mp_user_card')
                ->where($w)
                ->count();

            if ($card_count < 2) {
                return ajax('您需要有两张及以上的卡才可以赠送', 49);
            }

            $card = Db::table('mp_user_card')
                ->where($w)
                ->find();

            $card_code = md5($this->myinfo['uid'] . rand(1, 1000000000));
            $data = [
                'card_code' => $card_code
            ];

            Db::table('mp_user_card')
                ->where([['id', '=', $card['id']]])
                ->update($data);

            return ajax($card_code);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }

    // 通过卡片码获得卡片
    public function getGiftCard()
    {
        $post['card_code'] = input('post.card_code');
        $this->checkPost($post);

        try {
            $card = Db::table('mp_user_card')
                ->where([['card_code', '=', $post['card_code']]])
                ->find();

            if (!$card) {
                return ajax('卡片不存在或已被领取', 49);
            } else if ($card['uid'] == $this->myinfo['uid']) {
                return ajax('不能领取自己的卡片', 49);
            }

            $w = [
                'uid' => $card['uid'],
                'cid' => $card['cid']
            ];
            $this_card_count = Db::table('mp_user_card')
                ->where($w)
                ->count();

            if ($this_card_count < 2) {
                return ajax('用户此张卡片已不足两张，无法领取了', 49);
            }

            Db::startTrans();
            $data = [
                'uid' => $this->myinfo['uid'],
                'card_code' => ''
            ];
            Db::table('mp_user_card')
                ->where([['id', '=', $card['id']]])
                ->update($data);

            if ($card['cid'] == 5) {
                // 查看是否得到过现金奖励，未获得过则获得现金奖励
                $temp_get_gift = $this->check_gift($this->myinfo['uid'], 1);
                $get_gift = $temp_get_gift ? $temp_get_gift : 0;
            } else {
                // 查看是否获得过文创礼品，没有获得过文创礼品查看是否得到全部8张卡片，如果集齐则获得文创礼品
                $temp_get_gift = $this->check_gift($this->myinfo['uid'], 2);
                $get_gift = $temp_get_gift ? $temp_get_gift : 0;
            }
            Db::commit();

            $data = [
                'card_id' => $card['cid'],
                'get_gift' => $get_gift
            ];
            return ajax($data);
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
    }

    // 文创礼品核销
    public function culVerify()
    {
        $post = [
            'uid' => input('post.uid'),  // 这个是核销用户的uid

        ];
        $this->checkPost($post);
        $post['remark'] = input('post.remark', '');

        try {
            $cul_gift = Db::table('mp_card_cul_gift')
                ->where([['uid', '=', $post['uid']]])
                ->find();

            if (!$cul_gift) {
                return ajax('该用户并未获得文创礼品', 49);
            } else if ($cul_gift['status'] == 1) {
                return ajax('该用户的文创礼品已核销', 49);
            }

            $my = Db::table('mp_user')
                ->where([['id', '=', $this->myinfo['uid']]])
                ->find();

            if (!($my['auth'] == 2 && $my['role'] == 1)) {
                return ajax('只有认证博物馆才能核销文创礼品', 49);
            }

            $data = [
                'm_uid' => $this->myinfo['uid'],
                'remark' => $post['remark'],
                'verify_time' => time(),
                'status' => 1
            ];
            Db::table('mp_card_cul_gift')
                ->where([['id', '=', $cul_gift['id']]])
                ->update($data);

            return ajax();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }

    // 用户文创礼品详情
    public function getMyCul()
    {
        try {
            $cul_gift = Db::table('mp_card_cul_gift')->alias('ccg')
                ->join('mp_user u', 'u.id = ccg.m_uid', 'left')
                ->where([['ccg.uid', '=', $this->myinfo['uid']]])
                ->field('ccg.id, ccg.uid, ccg.m_uid, ccg.remark, ccg.create_time, ccg.verify_time, ccg.qrcode, ccg.status, u.nickname')
                ->find();

            if (!$cul_gift) {
                $cul_gift['status'] = -1;
            }

            // 卡牌列表
            $card_list = Db::table('mp_card')
                ->where([['id', '<>', 5]])
                ->select();

            $data = [
                'cul_gift' => $cul_gift,
                'card_list' => $card_list
            ];

            return ajax($data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }

    /*
     * 返回文创礼品核销状态
     *
     * （循环检测）
     */
    public function checkCulStatus()
    {
        try {
            $cul_gift = Db::table('mp_card_cul_gift')
                ->where([['uid', '=', $this->myinfo['uid']]])
                ->find();

            if (!$cul_gift) {
                $cul_gift_status = -1;
            } else {
                $cul_gift_status = $cul_gift['status'];
            }

            return ajax($cul_gift_status);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
    }

    // 测试
    public function test()
    {
    }

    // 测试检查token版
    public function test_user()
    {
    }

    /* 工具方法、私有方法 */
    // 测试卡牌概率，只是测试的时候有用
    private function test_card($times = 10000)
    {
        $card_count = [
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
            '7' => 0,
            '8' => 0,
            '9' => 0
        ];
        for ($i = 0; $i < $times; $i++) {
            $cid = $this->get_rand_card();
            $card_count[$cid]++;
        }
        print_r($card_count);
    }

    // 定义卡牌数组，调用get_rand返回指定卡牌
    private function get_rand_card()
    {
        $prize_arr = [
            ['id' => 1, 'probability' => 200],
//            ['id' => 1, 'probability' => 1],
            ['id' => 2, 'probability' => 150],
            ['id' => 3, 'probability' => 100],
            ['id' => 4, 'probability' => 150],
            ['id' => 5, 'probability' => 1],
//            ['id' => 5, 'probability' => 200],
            ['id' => 6, 'probability' => 199],
            ['id' => 7, 'probability' => 50],
            ['id' => 8, 'probability' => 50],
            ['id' => 9, 'probability' => 100]
        ];

        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val['probability'];
        }

        return $this->get_rand($arr);
    }

    // 根据卡牌数组返回指定卡牌
    private function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }

    /**
     * 检查用户是否得到过特定类型奖励，未获得过则获得
     * $type: 1.文创产品 2.获得奖金
     *
     * @throws Exception
     */
    private function check_gift($uid, $type) {
        if ($type == 1) {
            try {
                $cash_gift = Db::table('mp_card_cash_gift')
                    ->where([['uid', '=', $uid]])
                    ->find();

                if (!$cash_gift) {
                    $data = [
                        'uid' => $uid,
                        'money' => number_format(config('card_gift_cash'), 2),
                        'create_time' => time()
                    ];
                    Db::table('mp_card_cash_gift')->insert($data);

                    Db::table('mp_user')
                        ->where([['id', '=', $uid]])
                        ->setInc('balance', config('card_gift_cash'));

                    return 2;
                }
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            try {
                $cul_gift = Db::table('mp_card_cul_gift')
                    ->where([['uid', '=', $uid]])
                    ->find();

                if (!$cul_gift) {
                    $w = [
                        ['uc.uid', '=', $uid],
                        ['c.id', '<>', 5]
                    ];
                    $user_has_list = Db::table('mp_card')->alias('c')
                        ->join('mp_user_card uc', 'uc.cid = c.id')
                        ->where($w)
                        ->group('c.id')
                        ->field('c.id')
                        ->select();

                    // 如果是8张则为集齐
                    if (count($user_has_list) == 8) {
                        // 生成二维码图片
                        $qr_code_path = 'static/uploads/activity/gift_qrcode/' . $uid . '_' . md5($uid . rand(1, 1000000000)) . '.png';
                        $qr_code = create_qrcode($uid, $qr_code_path);
                        if (!$qr_code) {
                            throw new Exception('生成二维码失败');
                        }

                        $data = [
                            'uid' => $uid,
                            'create_time' => time(),
                            'qrcode' => $qr_code_path
                        ];
                        Db::table('mp_card_cul_gift')->insert($data);

                        return 1;
                    }
                }
            } catch (\Exception $e) {
                // 如果已经生成了二维码图片则删除它
                if ($qr_code_path) {
                    @unlink($qr_code_path);
                }
                throw new Exception($e->getMessage());
            }
        }
    }
}