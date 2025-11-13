<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Model\Admin as AdminModel;
use App\Core\Route as RouteAttr;
use App\Model\AdminRole;
use App\Model\Role;
use App\Model\Rule;
use Exception;
use ReflectionException;
use ReflectionMethod;
use Webman\Http\Request;
use Webman\Http\Response;

class Admin extends InitApp
{
    /**
     * 对外提供的鉴权中间件
     *
     * @param Request  $request
     * @param callable $handler
     *
     * @return Response
     * @throws Exception
     */
    public function process(Request $request, callable $handler): Response
    {
        $response = parent::process($request, $handler);
        $controller = $request->controller;
        $action = $request->action;

        $code = 0;
        $msg = '';

        if (!self::authorize($controller, $action, $code, $msg)) {
            if ($request->expectsJson()) {
                $response = json(['code' => $code, 'msg' => $msg, 'data' => []]);
            } else {
                if ($code === 401) {
                    return redirect(url('admin.account.login'));
                } else {
                    $response = view('error', [], 'public')->withStatus($code);
                }
            }
        } else {
            $response = $request->method() == 'OPTIONS' ? response() : $response;
        }

        return $response;
    }

    /**
     * 权限
     *
     * @param string $controller
     * @param string $action
     * @param int    $code
     * @param string $msg
     *
     * @return bool
     * @throws ReflectionException|Exception
     */
    protected static function authorize(string $controller, string $action, int &$code = 0, string &$msg = ''): bool
    {
        if (!$controller) {
            return true;
        }

        $ref = new ReflectionMethod($controller, $action);
        $attributes = $ref->getAttributes(RouteAttr::class);
        /** @var RouteAttr $routeAttr */
        $routeAttr = $attributes[0]->newInstance();
        $permissionCode = $routeAttr->permission ?? null;
        self::refreshSession();
        $admin = session('admin');
        if ($permissionCode) {
            if (!$admin) {
                // 获取登录信息
                $msg = trans('key5');
                $code = 401;
                return false;
            }

            // 验证权限
            $roles = $admin['roles'];
            // 当前管理员无角色
            if (!$roles) {
                $msg = '无权限';
                $code = 402;
                return false;
            }

            $rules = Role::whereIn('id', $roles)->pluck('rules');
            $rule_ids = [];
            foreach ($rules as $rule_string) {
                if (!$rule_string) {
                    continue;
                }
                $rule_ids = array_merge($rule_ids, explode(',', $rule_string));
            }
            // 角色没有规则
            if (!$rule_ids) {
                $msg = '无权限';
                $code = 402;
                return false;
            }

            // 超级管理员
            if (in_array('*', $rule_ids)) {
                return true;
            }

            // 如果action为index，规则里有任意一个以$controller开头的权限即可
            if (strtolower($action) === 'index') {
                $rule = Rule::where(function ($query) use ($controller, $action) {
                    $controller_like = str_replace('\\', '\\\\', $controller);
                    $query->where('key', 'like', "$controller_like@%")->orWhere('key', $controller);
                })->whereIn('id', $rule_ids)->first();
                if ($rule) {
                    return true;
                }
                $msg = '无权限';
                $code = 402;
                return false;
            }

            // 查询是否有当前控制器的规则
            $rule = Rule::where(function ($query) use ($controller, $action) {
                $query->where('key', "$controller@$action")->orWhere('key', $controller);
            })->whereIn('id', $rule_ids)->first();

            if (!$rule) {
                $msg = '无权限';
                $code = 402;
                return false;
            }
        }

        return true;
    }

    /**
     * 刷新会话
     *
     * @param bool $force
     *
     * @return void
     * @throws Exception
     */
    protected static function refreshSession(bool $force = false): void
    {
        $sessionKey = 'admin';
        $session = request()->session();
        $sessionData = session($sessionKey);
        $now = time();

        if (!$sessionData) {
            return;
        }

        $adminId = $sessionData['id'] ?? null;
        if (!$adminId) {
            $session->forget($sessionKey);
            return;
        }

        // 刷新session
        $lastUpdate = $sessionData['session_last_update_time'] ?? 0;
        $updateInterval = 2; // 秒
        if (!$force && ($now - $lastUpdate) < $updateInterval) {
            return;
        }

        $admin = AdminModel::find($adminId);
        if (!$admin) {
            $session->forget($sessionKey);
            return;
        }

        // 密码变更时使登录失效
        if (($sessionData['password'] ?? '') !== $admin->password) {
            $session->forget($sessionKey);
            return;
        }

        // 禁用状态处理
        if ($admin->status !== 0) {
            $session->forget($sessionKey);
            return;
        }

        $admin = $admin->toArray();
        // 更新权限与缓存时间
        $admin['roles'] = AdminRole::where('admin_id', $adminId)->pluck('role_id')->toArray();
        $admin['session_last_update_time'] = $now;;

        $session->set('admin', $admin);
    }

}