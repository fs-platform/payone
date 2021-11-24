<?php

namespace Smbear\Payone;

use Illuminate\Support\Facades\Log;
use Smbear\Payone\Traits\PayoneConfig;
use Smbear\Payone\Services\PayoneRequest;
use Smbear\Payone\Exceptions\MethodException;
use Smbear\Payone\Exceptions\ParametersException;

class Payone
{
    use PayoneConfig;

    /**
     * @var array 用户信息
     */
    public array $person = [];

    /**
     * @var array 交易信息
     */
    public array $invoice = [];

    /**
     * @var array 配置文件
     */
    public array $config = [];

    /**
     * @var string 返回值的 signature
     */
    public string $signature;

    /**
     * @var string 支付方式
     */
    public string $payoneMethod;

    /**
     * @var object PayoneRequest
     */
    public object $payoneRequest;

    public function __construct()
    {
        //初始化配置信息
        if (empty($this->config)) {
            $this->config = $this->getConfig([
                'url',
                'aid',
                'mid',
                'portalid',
                'key',
                'api_version',
                'mode',
                'encoding'
            ]);
        }

        //初始化请求类
        $this->payoneRequest = new PayoneRequest();
    }

    /**
     * 设置payone支付方式
     * @param string $payoneMethod
     * @return $this
     * @throws ParametersException
     */
    public function setPayoneMethod(string $payoneMethod) : self
    {
        if (empty($payoneMethod)) {
            throw new ParametersException(__FUNCTION__.'：方法 参数异常');
        }

        $this->payoneMethod = $payoneMethod;

        return $this;
    }

    /**
     * 设置success的signature
     * @param string $signature
     * @return $this
     * @throws ParametersException
     */
    public function setSignature(string $signature) : self
    {
        if (empty($signature)) {
            throw new ParametersException(__FUNCTION__.'：方法 参数异常');
        }

        $this->signature = $signature;

        return $this;
    }

    /**
     * @Notes:设置用户信息
     *
     * @param array $parameters
     * @return self
     * @Author: smile
     * @Date: 2021/10/8
     * @Time: 14:57
     */
    public function setPerson(array $parameters) : self
    {
        if (empty($parameters)) {
            if (empty($payoneMethod)) {
                throw new ParametersException(__FUNCTION__.'：方法 参数异常');
            }
        }

        //验证数据是否异常
        $this->checkParameters([
            'firstname',
            'lastname',
            'country'
        ],$parameters,__FUNCTION__);

        if (empty($this->person)) {
            $this->person = [
                'firstname'  => $parameters['firstname'],
                'lastname'   => $parameters['lastname'],
                'street'     => $parameters['street'] ?? '',
                'zip'        => $parameters['zip'] ?? '',
                'city'       => $parameters['city'] ?? '',
                'country'    => $parameters['country'],
                'email'      => $parameters['email'] ?? '',
                'language'   => $parameters['language'] ?? '',
                'ip'         => $parameters['ip'] ?? ''
            ];
        }

        return $this;
    }

    /**
     * 设置账期信息
     * @param array $parameters
     * @return $this
     * @throws ParametersException
     */
    public function setInvoice(array $parameters)
    {
        if (empty($parameters)) {
            if (empty($payoneMethod)) {
                throw new ParametersException(__FUNCTION__.'：方法 参数异常');
            }
        }

        //验证数据是否异常
        $this->checkParameters([
            'bankcountry',
            'amount',
            'currency',
            'reference',
            'narrative_text'
        ],$parameters,__FUNCTION__);

        //判断setPayoneMethod方法是否被使用
        if (empty($this->payoneMethod)) {
            throw new MethodException('setPayoneMethod 方法调用异常');
        }

        //判断setSignature方法是否被使用
        if (empty($this->signature)) {
            throw new MethodException('setSignature 方法调用异常');
        }

        if (empty($this->invoice)) {
            $this->invoice = [
                'bankcountry'            => $parameters['bankcountry'],
                'amount'                 => $parameters['amount'],
                'currency'               => $parameters['currency'],
                'reference'              => $parameters['reference'],
                'narrative_text'         => $parameters['narrative_text'],
                'successurl'             => $this->config['successurl'].'&reference='.$parameters['reference'] . '&signature=' .$this->signature,
                'errorurl'               => $this->config['errorurl'],
                'backurl'                => $this->config['backurl'],
                'request'                => $this->config['request'],
                'clearingtype'           => $this->config['clearingtype'],
                'onlinebanktransfertype' => $this->config[$this->payoneMethod]['onlinebanktransfertype']
            ];
        }

        return $this;
    }

    /**
     * 验证参数是否异常
     * @param array $parameters
     * @param array $require
     * @throws ParametersException
     */
    protected function checkParameters(array $require,array $parameters,string $functionName = __FUNCTION__)
    {
        array_walk($require,function ($item) use ($parameters,$functionName) {
            if (!isset($parameters[$item]) || empty($parameters[$item])) {
                throw new ParametersException(($functionName ?: __FUNCTION__).'：方法 参数异常');
            }
        });
    }

    /**
     * 初始化页面
     * 获取到url页面地址
     */
    public function initCheckout()
    {
        $request = array_merge([
            'aid'         => $this->config['aid'],
            'mid'         => $this->config['mid'],
            'portalid'    => $this->config['portalid'],
            'key'         => $this->config['key'],
            'mode'        => $this->config['mode'],
            'api_version' => $this->config['api_version'],
            'encoding'    => $this->config['encoding']
        ],$this->invoice,$this->person);

        try {
            Log::channel(config('payone.channel'))
                ->info('初始化请求:'.json_encode($request));

            $result = $this->payoneRequest
                ->sendRequest($this->config['url'],$request);

            Log::channel(config('payone.channel'))
                ->info('初始化响应:'.json_encode($result));

            if ($result['Status'] == 'REDIRECT') {
                return payone_return_success('success',[
                    'url' => $result['RedirectUrl'] ?? ''
                ]);
            } else {
                return payone_return_error($result['Error']['ErrorMessage'] ?? '');
            }
        }catch (\Exception|\Error|\Throwable $exception) {
            report($exception);

            Log::channel(config('payone.channel'))
                ->info('初始化异常:'.$exception->__toString());

            return payone_return_error($exception->getMessage());
        }
    }
}
