<?php

namespace utils;

use DateTime;
use \ReflectionException;

class DateUtil {

    /**
     * Retorna a data de início e fim do mês em um array
     */
	static function startEndMonth($month, $year) {
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $inicio = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
        $fim = date('Y-m-d', mktime(0, 0, 0, $month, $dias_mes, $year));
        
        return [
            "start"=>$inicio,
            "end"=>$fim
        ];
    }

}
