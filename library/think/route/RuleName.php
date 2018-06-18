<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\route;

class RuleName
{
    protected $item = [];

    protected $rule = [];

    /**
     * 注册路由标识
     * @access public
     * @param  string   $name      路由标识
     * @param  string   $value     路由规则
     * @param  bool     $first     是否置顶
     * @return void
     */
    public function setName(string $name, $value, bool $first = false): void
    {
        if ($first && isset($this->item[$name])) {
            array_unshift($this->item[$name], $value);
        } else {
            $this->item[$name][] = $value;
        }
    }

    /**
     * 注册路由规则
     * @access public
     * @param  string   $rule      路由规则
     * @param  RuleItem $route     路由
     * @return void
     */
    public function setRule(string $rule, RuleItem $route): void
    {
        $this->rule[$route->getDomain()][$rule][$route->getRoute()] = $route;
    }

    /**
     * 根据路由规则获取路由对象（列表）
     * @access public
     * @param  string   $name      路由标识
     * @param  string   $domain   域名
     * @return array
     */
    public function getRule(string $rule, string $domain = null): array
    {
        return $this->rule[$domain][$rule] ?? [];
    }

    /**
     * 获取全部路由列表
     * @access public
     * @param  string   $domain   域名
     * @return array
     */
    public function getRuleList(string $domain = null): array
    {
        $list = [];

        foreach ($this->rule as $ruleDomain => $rules) {
            foreach ($rules as $rule => $items) {
                foreach ($items as $item) {
                    $val = [];

                    foreach (['method', 'rule', 'name', 'route', 'pattern', 'option'] as $param) {
                        $call        = 'get' . $param;
                        $val[$param] = $item->$call();
                    }

                    $list[$ruleDomain][] = $val;
                }
            }
        }

        if ($domain) {
            return $list[$domain] ?? [];
        }

        return $list;
    }

    /**
     * 导入路由标识
     * @access public
     * @param  array   $name      路由标识
     * @return void
     */
    public function import(array $item): void
    {
        $this->item = $item;
    }

    /**
     * 根据路由标识获取路由信息（用于URL生成）
     * @access public
     * @param  string   $name      路由标识
     * @param  string   $domain   域名
     * @return array
     */
    public function getName(string $name = null, string $domain = null): array
    {
        if (is_null($name)) {
            return $this->item;
        }

        $name   = strtolower($name);
        $result = [];

        if (isset($this->item[$name])) {
            if (is_null($domain)) {
                $result = $this->item[$name];
            } else {
                foreach ($this->item[$name] as $item) {
                    if ($item[2] == $domain) {
                        $result[] = $item;
                    }
                }
            }
        }

        return $result;
    }

}
