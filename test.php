<?php
for ($y = 0; $y < 5; $y++) {
$postDate = date('Y-m-d', strtotime('2020-04-01')); // set generic date of the month
$weekDayNum = date('w', strtotime($postDate)); // get php week day number 0 - 6 where 0 is sunday and 6 is saturday
$startDayWeek = date('Y-m-d', strtotime($postDate . ' -' . $weekDayNum . ' days'));
$dayCtr = date('Y-m-d', strtotime($startDayWeek . ' +' . $y . ' days'));

var_dump($dayCtr);
}

?>