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

    /**
     * @throws Exceptions\ConfigException
     */
    public function __construct()
    {
        //设置初始化环境
        $this->setEnvironment();

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
     * @throws ParametersException
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
     * @throws ParametersException|MethodException
     */
    public function setInvoice(array $parameters) : self
    {
        if (empty($parameters)) {
            throw new ParametersException(__FUNCTION__.'：方法 参数异常');
        }

        //验证数据是否异常
        $this->checkParameters([
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
                'onlinebanktransfertype' => $this->config[$this->payoneMethod]['onlinebanktransfertype'],
                'iban'                   => $parameters['iban'],
            ];

            if ($this->payoneMethod == 'ideal') {
                $this->invoice = array_merge($this->invoice,[
                    'bankgrouptype' => $parameters['bankgrouptype'],
                ]);
            }
        }

        return $this;
    }

    /**
     * 验证参数是否异常
     * @param array $require
     * @param array $parameters
     * @param string $functionName
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
     * 核验方法是否已经全部调用
     * @param array $methods
     * @throws ParametersException
     */
    protected function checkMethods(...$methods)
    {
        array_walk($methods,function ($item){
            $attribute = $item['attribute'] ?? '';

            if (empty($attribute)) {
                return ;
            }

            if (!isset($this->$attribute)  || empty($this->$attribute)) {
                throw new ParametersException($item['method'].' 异常，请先调用');
            }
        });
    }

    /**
     * 初始化页面
     * 获取到url页面地址
     * @throws ParametersException
     */
    public function initCheckout(): array
    {
        //判断方法是否全部正常调用
        $this->checkMethods(
            [
                'method' => 'setPayoneMethod',
                'attribute' => 'payoneMethod'
            ],
            [
                'method' => 'setSignature',
                'attribute' => 'signature'
            ],
            [
                'method' => 'setPerson',
                'attribute' => 'person'
            ],
            [
                'method' => 'setInvoice',
                'attribute' => 'invoice'
            ]
        );

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
