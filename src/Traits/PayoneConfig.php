<?php

namespace Smbear\Payone\Traits;

use Smbear\Payone\Enums\PayoneEnums;
use Smbear\Payone\Exceptions\ConfigException;

trait PayoneConfig
{
    /**
     * @var array 配置文件
     */
    public array $config = [];

    /**
     * @var string 环境变量
     */
    public string $environment = 'sandbox';

    /**
     * @Notes:设置 environment
     * 默认的情况下，采用沙箱环境
     * @param string $environment
     * @Author: smile
     * @Date: 2021/6/8
     * @Time: 18:53
     */
    public function setEnvironment(string $environment = '')
    {
        $this->environment = $environment ?: config('paypal.environment');
    }

    /**
     * @Notes:获取到指定模型的配置数据
     *
     * @param array $dependencies
     * @Author: smile
     * @Date: 2021/6/30
     * @Time: 17:58
     * @throws ConfigException
     */
    public function getConfig(array $dependencies) : array
    {
        if (empty($this->config)) {
            $environment = $this->environment;

            array_walk($dependencies,function ($item) use ($environment) {
                if (empty(config(PayoneEnums::CONFIG.'.'.$environment.'.'.$item))){
                    throw new ConfigException(PayoneEnums::CONFIG. $environment .'.'.$item.' 参数为空');
                }
            });

            $this->config = config(PayoneEnums::CONFIG.'.'.$environment);
        }

        return $this->config;
    }
}