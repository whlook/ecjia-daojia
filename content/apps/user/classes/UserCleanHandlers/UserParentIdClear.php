<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/12
 * Time: 14:04
 */

namespace Ecjia\App\User\UserCleanHandlers;

use Ecjia\App\User\UserCleanAbstract;
use RC_Uri;
use RC_DB;
use RC_Api;
use ecjia_admin;

class UserParentIdClear extends UserCleanAbstract
{

    /**
     * 代号标识
     * @var string
     */
    protected $code = 'user_parent_id_clear';

    /**
     * 排序
     * @var int
     */
    protected $sort = 91;

    public function __construct($user_id)
    {
        $this->name = __('会员父级ID', 'user');

        parent::__construct($user_id);
    }

    /**
     * 数据描述及输出显示内容
     */
    public function handlePrintData()
    {
        $text = __('会员推荐注册时绑定的会员父级ID', 'user');

        return <<<HTML

<span class="controls-info">{$text}</span>

HTML;

    }

    /**
     * 获取数据统计条数
     *
     * @return mixed
     */
    public function handleCount()
    {
        $user = RC_Api::api('user', 'user_info', array('user_id' => $this->user_id));

        return !empty($user['parent_id']) ? 1 : 0;
    }


    /**
     * 执行清除操作
     *
     * @return mixed
     */
    public function handleClean()
    {
        $count = $this->handleCount();
        if (empty($count)) {
            return true;
        }
        
        $result = RC_DB::table('users')->where('user_id', $this->user_id)->update(array('parent_id' => 0));

        if ($result) {
            $this->handleAdminLog();
        }

        return $result;
    }

    /**
     * 返回操作日志编写
     *
     * @return mixed
     */
    public function handleAdminLog()
    {
        \Ecjia\App\User\Helper::assign_adminlog_content();

        $user_info = RC_Api::api('user', 'user_info', array('user_id' => $this->user_id));

        $user_name = !empty($user_info) ? sprintf(__('用户名是%s', 'user'), $user_info['user_name']) : sprintf(__('用户ID是%s', 'user'), $this->user_id);

        ecjia_admin::admin_log($user_name, 'clean', 'user_parent_id');
    }

    /**
     * 是否允许删除
     *
     * @return mixed
     */
    public function handleCanRemove()
    {
        return !empty($this->handleCount()) ? true : false;
    }


}