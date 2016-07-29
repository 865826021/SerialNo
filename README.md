# SerialNo

用于生成全站性的唯一序列号批次（类似优惠券），暂时只提供生成纯数字版本


#### Examples:

	$conf = [
        'amount'        => 1000,
        'minLength'     => 11,
        'hashLength'    => 4,
        'key'           => 'test',
        'serialLength'  => 3,
        'padding'       => '0',
        'mulFactor'     => '101',
        'diffFactor'    => '641'
    ];
    
    $total = [];
    $a = [];
    
    for ($x = 1; $x <= 10; ++$x) {
        $app = new Application($x, $conf);
        for ($i = 1; $i <= 1000; ++$i) {
            $a[] = $app->digital($i);
        }
        $total[] = $a;
        $a = [];
    }
    count(array_intersect($total[0], $total[1], $total[2], $total[3], $total[4], $total[5], $total[6], $total[7], $total[8], $total[9]));  // 0
    var_export($total[0]);
	01227004214
	01358004368
	01649004462
	01400004530
	01491004617
	01392004774
	01233004863
	01834004993
	01275005074
	01976005182
	...


## 参数说明

    id  唯一标识，也可以看作是一个生成的前缀，用于确保各批生成的序列号的唯一性，建议使用数据库ID

    [
          'amount',  
          'minLength',  
          'hashLength',  
          'key',  
          'serialLength',  
          'padding',  
          'mulFactor',  
          'diffFactor',  
    ]

* amount：生成数量，对于生成数量并不需要很大的批次可考虑预先设定一个比较大的数量，
    这样可以稍微增加扩展性

* minLength：序列号生成最小长度，假设idLength+serialLength+hashLength=sum，
    如果设置的最小长度小于sum则会已sum长度为生成长度，
    如果设置的最小长度大于sum则会以id来进行填充至设置的长度

* hashLength：hash长度，默认为4，可以看作是随机字符串长度，推荐长度最小设置为2

* key：用户生成hash字符串的key

* serialLength：序列号长度，默认为6，类似1~n的序列号，这里的序列号并不是完全按顺序递增的，
    即生成的是伪随机数，既保证了数字最大的使用率又不会完全按顺序递增，
    例如serialLength设置为6则最多可以生成一百万个序列号0~999999，
    生成的规律依据下面的生成系数而定，推荐最少使用5位序列号长度，
    如果amount生成的数量比serialLength可容纳的数量多则不能保证序列号的唯一性

* padding：填充字符，默认为0，当生成的序列不足设置的长度时用来填充的字符，
    以下两种情况会用到该设置：  
        1. serialLength设置长度比实际生成的序列号要大时，
            例如：serialLength设置为6而当前生成的序列号只有4位，
            则不足的两位会以该字符作为填充。  
        2.minLength比idLength+serialLength+hashLength要大时，此时会用该字符填充id到相应的长度，
            例如minLength为14、id为2（即idLength为1）、serialLength为6、hashLength为4时，
            idLength+serialLength+hashLength=11，此时会用该字符将id长度填充至4位使总长度为14位

* mulFactor：伪随机数生成因子1，生成因子1必须为质数，
	可从src/Data/PrimeNumbers.php中挑选，且mulFactor必须小于amount

* diffFactor：伪随机数生成因子2，生成因子2必须为质数，
	从src/Data/PrimeNumbers.php中挑选，且mulFactor必须小于amount，
	diffFactor不应该与mulFactor相同