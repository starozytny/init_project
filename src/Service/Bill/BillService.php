<?php

namespace App\Service\Bill;

class BillService
{
    public function createNewNumero($i, $year, $prefix): string
    {
        $tab = array_map('intval', str_split($i));
        $nbZero = 6 - count($tab);

        $counter = $year . "-" . str_repeat("0", $nbZero);
        $counter .= $i;

        return $prefix . $counter;
    }
}
