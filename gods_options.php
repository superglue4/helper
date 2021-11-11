<?php


$gods = [
    ['opt1' => '바지', 'opt2' => '파랑', 'opt3' => 'S', 'inven' => 45, 'price' => 200],
    ['opt1' => '바지', 'opt2' => '파랑', 'opt3' => 'M', 'inven' => 45, 'price' => 200],
    ['opt1' => '바지', 'opt2' => '파랑', 'opt3' => 'L', 'inven' => 45, 'price' => 200],

    ['opt1' => '바지', 'opt2' => '노랑', 'opt3' => 'S', 'inven' => 45, 'price' => 200],
    ['opt1' => '바지', 'opt2' => '노랑', 'opt3' => 'M', 'inven' => 45, 'price' => 200],
    ['opt1' => '바지', 'opt2' => '노랑', 'opt3' => 'L', 'inven' => 45, 'price' => 200],

    ['opt1' => '바지', 'opt2' => '빨강', 'opt3' => 'S', 'inven' => 45, 'price' => 200],
    ['opt1' => '바지', 'opt2' => '빨강', 'opt3' => 'M', 'inven' => 45, 'price' => 200],
    ['opt1' => '바지', 'opt2' => '빨강', 'opt3' => 'L', 'inven' => 45, 'price' => 200],
];

$new_gods = [];
foreach ($gods as $opts) {
    if (!empty($opts['opt3'])) {
        $value = &$new_gods[$opts['opt1']][$opts['opt2']][$opts['opt3']];
    } else if (!empty($opts['opt2'])) {
        $value = &$new_gods[$opts['opt1']][$opts['opt2']];
    } else if (!empty($opts['opt1'])) {
        $value = &$new_gods[$opts['opt1']];
    }

    $value = [
        'inven' => $opts['inven'],
        'price' => $opts['price'],
    ];
}

/**
 * 옵션 계층 정열
 * @param array $array
 * @return array
 */
function po_list_option(array $array)
{
    $return = [];
    foreach ($array as $k => $v) {
        if (is_array($v) && !isset($v['inven'])) {
            $sum = 0;
            array_walk_recursive($v, function (&$val, $key) use (&$sum) {
                if ($key == 'inven') {
                    $sum += $val;
                }
            });
            $return[] = ['name' => $k, 'inven' => $sum, 'option' => po_list_option($v)];
        } else {
            $return[] = array_merge(['name' => $k], $v);
        }
    }
    return $return;
}

print_r(po_list_option($new_gods));