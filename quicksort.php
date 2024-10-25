<?php function quicksort($array, $key) {
    if (count($array) < 2) {
        return $array;
    }

    $pivot = $array[0];
    $left = [];
    $right = [];

    for ($i = 1; $i < count($array); $i++) {
        if ($array[$i][$key] < $pivot[$key]) {
            $left[] = $array[$i];
        } else {
            $right[] = $array[$i];
        }
    }

    return array_merge(quicksort($left, $key), [$pivot], quicksort($right, $key));
}
?>


146275