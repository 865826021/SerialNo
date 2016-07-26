<?php

namespace QiuQiuX\SerialNo;


class PseudoRandom
{

    public function generate($serial, $amount, $length = '', $padding)
    {
        // TODO 暂时写死
        $num = (101 * $serial + 499) % $amount;
        if ($length) {
            return str_pad($num, $length, $padding, STR_PAD_LEFT);
        }
        return $num;
    }

}

 