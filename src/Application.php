<?php

namespace QiuQiuX\SerialNo;

use QiuQiuX\BaseConvert\BaseConvert;

class Application
{

    protected $id;

    protected $amount;

    protected $minLength;

    protected $hashLength;

    protected $serialLength;

    protected $key;

    protected $padding;

    /**
     * @param int $id           唯一标识（建议使用数据库ID）
     * @param int $amount       生成的数量
     * @param int $minLength    最小生成长度,必须大于 strlen($amount)+$hashLength+$serialLength
     * @param int $hashLength   hash长度（可以理解为随机字符串）
     * @param int $serialLength 序列号长度,序列号长度可生成的序列号数量必须大于等于生成数量
     * （如：$amount为五百万，则$serialLength最少设置为8；$amount为一百万，则$serialLength最少可设置为7）
     * @param string $key       加密用的salt
     * @param string $padding   填充字符
     */
    function __construct($id, $amount, $minLength, $hashLength, $serialLength, $key, $padding = '0')
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->minLength = $minLength;
        $this->hashLength = $hashLength;
        $this->serialLength = $serialLength;
        $this->key = $key;
        $this->padding = $padding;
    }

    public function digital($serial)
    {
        $hashNumber = $this->getHashNumber($this->id . $serial);

//        $pseudoRandom = (new PseudoRandom())
//            ->generate($serial, $this->amount, $this->serialLength, $this->padding);
        $pseudoRandom = (new PseudoRandom())
            ->generate($serial, $this->amount, $this->serialLength, $this->padding);

        $idLength = $this->minLength - strlen(strval($hashNumber)) - strlen($pseudoRandom);

        $paddingId = str_pad($this->id, $idLength, $this->padding, STR_PAD_LEFT);

        return $this->confuse($paddingId, $pseudoRandom, $hashNumber);
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
        $hash = md5($id . $this->key);
        $subHash = substr($hash, 0, $this->hashLength);

        $converted = (new BaseConvert(strtoupper($subHash), 16))->convertTo(10);
        return substr($converted, 0, $this->hashLength);
    }

}
 