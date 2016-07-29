<?php

namespace QiuQiuX\SerialNo;


class PseudoRandom
{

    protected $mulFactor;

    protected $diffFactor;

    protected $amount;

    protected $length;

    protected $padding;

    public function __construct($mulFactor, $diffFactor, $amount, $length, $padding)
    {
        $this->mulFactor = $mulFactor;
        $this->diffFactor = $diffFactor;
        $this->amount = $amount;
        $this->length = $length;
        $this->padding = $padding;
    }

    public function generate($serial)
    {
        $mul = gmp_mul(strval($this->mulFactor), strval($serial));
        $add = gmp_add($mul, $this->diffFactor);
        $mod = gmp_mod($add, strval($this->amount));
        $num = gmp_strval($mod);
//        $num = ($this->mulFactor * $serial + $this->diffFactor) % $this->amount;

        return str_pad($num, $this->length, $this->padding, STR_PAD_LEFT);
    }

}

 