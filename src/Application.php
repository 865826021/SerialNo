<?php

namespace QiuQiuX\SerialNo;

use QiuQiuX\BaseConvert\BaseConvert;

class Application
{

    protected $id;

    protected $config;

    protected $pseudoRandom;

    /**
     * @param int $id           唯一标识，用于区分各批生成的序列号，建议使用数据库ID
     * @param array $conf       conf包含以下元素
     *      amount：生成数量，对于生成数量并不需要很大的批次可考虑预先设定一个比较大的数量，
     *              这样可以稍微增加扩展性
     *
     *      minLength：序列号生成最小长度，假设idLength+serialLength+hashLength=sum，
     *              如果设置的最小长度小于sum则会已sum长度为生成长度，
     *              如果设置的最小长度大于sum则会以id来进行填充至设置的长度
     *
     *      hashLength：hash长度，默认为4，可以看作是随机字符串长度，推荐长度最小设置为2
     *
     *      key：用户生成hash字符串的key
     *
     *      serialLength：序列号长度，默认为6，类似1~n的序列号，这里的序列号并不是完全按顺序递增的，
     *              即生成的是伪随机数，既保证了数字最大的使用率又不会完全按顺序递增，
     *              例如serialLength设置为6则最多可以生成一百万个序列号0~999999，
     *              生成的规律依据下面的生成系数而定，推荐最少使用5位序列号长度，
     *              如果amount生成的数量比serialLength可容纳的数量多则不能保证序列号的唯一性
     *
     *      padding：填充字符，默认为0，当生成的序列不足设置的长度时用来填充的字符，
     *              以下两种情况会用到该设置：
     *              1. serialLength设置长度比实际生成的序列号要大时，
     *                      例如：serialLength设置为6而当前生成的序列号只有4位，
     *                      则不足的两位会以该字符作为填充。
     *              2.minLength比idLength+serialLength+hashLength要大时，此时会用该字符填充id到相应的长度，
     *                      例如minLength为14、id为2（即idLength为1）、serialLength为6、hashLength为4时，
     *                      idLength+serialLength+hashLength=11，此时会用该字符将id长度填充至4位使总长度为14位
     *
     *      mulFactor：伪随机数生成因子1，生成因子1必须为质数，
     *              可从src/Data/PrimeNumbers.php中挑选，且mulFactor必须小于amount
     *
     *      diffFactor：伪随机数生成因子2，生成因子2必须为质数，
     *              可从src/Data/PrimeNumbers.php中挑选，且mulFactor必须小于amount，
     *              diffFactor不应该与mulFactor相同
     */
    function __construct($id, $conf)
    {
        $this->id = $id;
        $this->config = $this->getConfig($conf);
    }

    public function digital($serial)
    {
        $hashNumber = $this->getHashNumber($this->id . $serial);

        $pseudoRandom = $this->getPseudoRandom(
            $this->config->mulFactor,
            $this->config->diffFactor,
            $this->config->amount,
            $this->config->serialLength,
            $this->config->padding
        )->generate($serial);

        $idLength = $this->config->minLength - strlen(strval($hashNumber)) - strlen($pseudoRandom);

        $paddingId = str_pad($this->id, $idLength, $this->config->padding, STR_PAD_LEFT);

        return $this->confuse($paddingId, $pseudoRandom, $hashNumber);
    }

    public function getConfig($conf)
    {
        if (!$this->config) {
            $this->config = new Config($conf);
        }

        return $this->config;
    }

    public function getPseudoRandom($mulFactor, $diffFactor, $amount, $length, $padding)
    {
        if (!$this->pseudoRandom) {
            $this->pseudoRandom = new PseudoRandom($mulFactor, $diffFactor, $amount, $length, $padding);
        }

        return $this->pseudoRandom;
    }

    protected function confuse($paddingId, $pseudoRandom, $hashNumber)
    {
        $leftIdLength = strlen($paddingId) >> 1;
        $leftPseudoRandomLength = strlen($pseudoRandom) >> 1;
        $leftHashLength = strlen($hashNumber) >> 1;

        $mixing = substr($paddingId, $leftIdLength)
            . substr($hashNumber,0, $leftHashLength)
            . substr($pseudoRandom, 0, $leftPseudoRandomLength)
            . substr($paddingId, 0, $leftIdLength)
            . substr($pseudoRandom, $leftPseudoRandomLength)
            . substr($hashNumber, $leftHashLength);

        return $mixing;
    }

    protected function getHashNumber($id)
    {
        $hash = md5($id . $this->config->key);
        $subHash = substr($hash, 0, $this->config->hashLength);

        $converted = (new BaseConvert(strtoupper($subHash), 16))->convertTo(10);
        return substr($converted, 0, $this->config->hashLength);
    }

}
 