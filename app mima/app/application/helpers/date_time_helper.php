<?php

/**
 * get user's time zone offset 
 * 
 * @return active users timezone
 */
if (!function_exists('get_timezone_offset')) {

    function get_timezone_offset($project_id = NULL) {
        if($project_id){
            $timeZone = new DateTimeZone(get_setting_mimasoft($project_id, "timezone"));
            $dateTime = new DateTime("now", $timeZone);
            return $timeZone->getOffset($dateTime);
        }else{
            $timeZone = new DateTimeZone(get_setting("timezone"));
            $dateTime = new DateTime("now", $timeZone);
            return $timeZone->getOffset($dateTime);
        }
        
    }

}

/**
 * convert a local time to UTC 
 * 
 * @param string $date
 * @param string $format
 * @return utc date
 */
if (!function_exists('convert_date_local_to_utc')) {

    function convert_date_local_to_utc($date = "", $format = "Y-m-d H:i:s") {
        if (!$date) {
            return false;
        }
        //local timezone
        $time_offset = get_timezone_offset() * -1;

        //add time offset
        return date($format, strtotime($date) + $time_offset);
    }

}

/**
 * get current utc time
 * 
 * @param string $format
 * @return utc date
 */
if (!function_exists('get_current_utc_time')) {

    function get_current_utc_time($format = "Y-m-d H:i:s") {
        $d = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        $d->setTimeZone(new DateTimeZone("UTC"));
        return $d->format($format);
    }

}

/**
 * convert a UTC time to local timezon as defined on users setting
 * 
 * @param string $date_time
 * @param string $format
 * @return local date
 */
if (!function_exists('convert_date_utc_to_local')) {

    function convert_date_utc_to_local($date_time, $format = "Y-m-d H:i:s", $project_id= NULL) {
		
		if($project_id){
			$timezoneformat = get_setting_mimasoft($project_id, "timezone");
        	$date = new DateTime($date_time . ' +00:00');
       		$date->setTimezone(new DateTimeZone($timezoneformat));
		}else{
			$timezoneformat = get_setting_mimasoft($project_id, "timezone");
        	$date = new DateTime($date_time . ' +00:00');
        	$date->setTimezone(new DateTimeZone(get_setting('timezone')));
		}
		
        return $date->format($format);
    }

}

/**
 * get current users local time
 * 
 * @param string $format
 * @return local date
 */
if (!function_exists('get_my_local_time')) {

    function get_my_local_time($format = "Y-m-d H:i:s") {
        return date($format, strtotime(get_current_utc_time()) + get_timezone_offset());
    }

}

/**
 * convert time string to 24 hours format 
 * 01:00 AM will be converted as 13:00:00 
 * 
 * @param string $time  required time format = 01:00 AM/PM
 * @return 24hrs time
 */
if (!function_exists('convert_time_to_24hours_format')) {

    function convert_time_to_24hours_format($time = "00:00 AM") {
        if (!$time)
            $time = "00:00 AM";

        if (strpos($time, "AM")) {
            $time = trim(str_replace("AM", "", $time));
            $check_time = explode(":", $time);
            if ($check_time[0] == 12) {
                $time = "00:" . get_array_value($check_time, 1);
            }
        } else if (strpos($time, "PM")) {
            $time = trim(str_replace("PM", "", $time));
            $check_time = explode(":", $time);
            if ($check_time[0] > 0 && $check_time[0] < 12) {
                $time = $check_time[0] + 12 . ":" . get_array_value($check_time, 1);
            }
        }
        $time.=":00";
        return $time;
    }

}

/**
 * convert time string to 12 hours format 
 * 13:00:00 will be converted as 01:00 AM
 * 
 * @param string $time  required time format =  00:00:00
 * @return 12hrs time
 */
if (!function_exists('convert_time_to_12hours_format')) {

    function convert_time_to_12hours_format($time = "") {
        if ($time) {
            $am = " AM";
            $pm = " PM";
            if (get_setting("time_format") === "small") {
                $am = " am";
                $pm = " pm";
            }
            $check_time = explode(":", $time);
            $hour = $check_time[0] * 1;
            $minute = get_array_value($check_time, 1) * 1;
            $minute = ($minute < 10) ? "0" . $minute : $minute;

            if ($hour == 0) {
                $time = "12:" . $minute . $am;
            } else if ($hour == 12) {
                $time = $hour . ":" . $minute . $pm;
            } else if ($hour > 12) {
                $hour = $hour - 12;
                $hour = ($hour < 10) ? "0" . $hour : $hour;
                $time = $hour . ":" . $minute . $pm;
            } else {
                $hour = ($hour < 10) ? "0" . $hour : $hour;
                $time = $hour . ":" . $minute . $am;
            }
            return $time;
        }
    }

}

/**
 * prepare a decimal value from a time string
 * 
 * @param string $time  required time format =  00:00:00
 * @return number
 */
if (!function_exists('convert_time_string_to_decimal')) {

    function convert_time_string_to_decimal($time = "00:00:00") {
        $hms = explode(":", $time);
        return $hms[0] + (get_array_value($hms, "1") / 60) + (get_array_value($hms, "2") / 3600);
    }

}

/**
 * prepare a human readable time format from a decimal value of seconds
 * 
 * @param string $seconds
 * @return time
 */
if (!function_exists('convert_seconds_to_time_format')) {

    function convert_seconds_to_time_format($seconds = 0) {
        $is_negative = false;
        if ($seconds < 0) {
            $seconds = $seconds * -1;
            $is_negative = true;
        }
        $seconds = $seconds * 1;
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours * 3600)) / 60);
        $secs = floor($seconds % 60);

        $hours = ($hours < 10) ? "0" . $hours : $hours;
        $mins = ($mins < 10) ? "0" . $mins : $mins;
        $secs = ($secs < 10) ? "0" . $secs : $secs;

        $string = $hours . ":" . $mins . ":" . $secs;
        if ($is_negative) {
            $string = "-" . $string;
        }
        return $string;
    }

}

/**
 * get seconds form a given time string
 * 
 * @param string $time
 * @return seconds
 */
if (!function_exists('convert_time_string_to_second')) {

    function convert_time_string_to_second($time = "00:00:00") {
        $hms = explode(":", $time);
        return $hms[0] * 3600 + ($hms[1] * 60) + ($hms[2]);
    }

}


/**
 * convert a datetime string to relative time 
 * ex: $date_time = "2015-01-01 23:10:00" will return like this: Today at 23:10 PM
 * 
 * @param string $date_time .. it will be considered as UTC time.
 * @param string $convert_to_local .. to prevent conversion, pass $convert_to_local=false 
 * @return date time
 */
if (!function_exists('format_to_relative_time')) {

    function format_to_relative_time($date_time, $convert_to_local = true, $is_short_date = false) {
        if ($convert_to_local) {
            $date_time = convert_date_utc_to_local($date_time);
        }

        $target_date = new DateTime($date_time);
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(get_setting('timezone')));
        $today = $now->format("Y-m-d");
        $date = "";
        $short_date = "";
        if ($now->format("Y-m-d") == $target_date->format("Y-m-d")) {
            $date = lang("today_at");   //today
            $short_date = lang("today");
        } else if (date('Y-m-d', strtotime(' -1 day', strtotime($today))) === $target_date->format("Y-m-d")) {
            $date = lang("yesterday_at"); //yesterday
            $short_date = lang("yesterday");
        } else {
            $date = format_to_date($date_time);
            $short_date = format_to_date($date_time);
        }
        if ($is_short_date) {
            return $short_date;
        } else {
            if (get_setting("time_format") == "24_hours") {
                return $date . " " . $target_date->format("H:i");
            } else {
                return $date . " " . convert_time_to_12hours_format($target_date->format("H:i:s"));
            }
        }
    }

}

/**
 * convert a datetime string to date format as defined on settings
 * ex: $date_time = "2015-01-01 23:10:00" will return like this: Today at 23:10 PM
 * 
 * @param string $date_time .. it will be considered as UTC time.
 * @param string $convert_to_local .. to prevent conversion, pass $convert_to_local=false 
 * @return date
 */
if (!function_exists('format_to_date')) {

    function format_to_date($date_time, $convert_to_local = true) {
        if (!$date_time) {
            return "";
        }

        if ($convert_to_local) {
            $date_time = convert_date_utc_to_local($date_time);
        }
        $target_date = new DateTime($date_time);
        return $target_date->format(get_setting('date_format'));
    }

}

/**
 * convert a datetime string to 12 hours time format
 * 
 * @param string $date_time .. it will be considered as UTC time.
 * @param string $convert_to_local .. to prevent conversion, pass $convert_to_local=false 
 * @return time
 */
if (!function_exists('format_to_time')) {

    function format_to_time($date_time, $convert_to_local = true) {
        if ($convert_to_local) {
            $date_time = convert_date_utc_to_local($date_time);
        }
        $target_date = new DateTime($date_time);

        if (get_setting("time_format") == "24_hours") {
            return $target_date->format("H:i");
        } else {
            return convert_time_to_12hours_format($target_date->format("H:i:s"));
        }
    }

}

/**
 * convert a datetime string to datetime format as defined on settings
 * 
 * @param string $date_time .. it will be considered as UTC time.
 * @param string $convert_to_local .. to prevent conversion, pass $convert_to_local=false 
 * @return date time
 */
if (!function_exists('format_to_datetime')) {

    function format_to_datetime($date_time, $convert_to_local = true) {
        if ($convert_to_local) {
            $date_time = convert_date_utc_to_local($date_time);
        }
        $target_date = new DateTime($date_time);
        $date = $target_date->format(get_setting('date_format'));

        if (get_setting("time_format") == "24_hours") {
            return $date . " " . $target_date->format("H:i");
        } else {
            return $date . " " . convert_time_to_12hours_format($target_date->format("H:i:s"));
        }
    }

}



/**
 * return users local time (today)
 * 
 * @return date
 */
if (!function_exists('get_today_date')) {

    function get_today_date() {
        return date("Y-m-d", strtotime(get_my_local_time()));
    }

}


/**
 * return users local time (tomorrow)
 * 
 * @return date
 */
if (!function_exists('get_tomorrow_date')) {

    function get_tomorrow_date() {
        $today = get_today_date();
        return date('Y-m-d', strtotime($today . ' + 1 days'));
    }

}

/**
 * add days with a given date
 * 
 * $date should be Y-m-d
 * $period_type should be days/months/years/weeks
 * 
 * @return date
 */
if (!function_exists('add_period_to_date')) {

    function add_period_to_date($date, $no_of = 0, $period_type = "days") {
        return date('Y-m-d', strtotime("+$no_of $period_type", strtotime($date)));
    }

}


/**
 * get date difference in days
 * 
 * $start_date && $end_date should be Y-m-d format
 * 
 * @return difference in days
 */
if (!function_exists('get_date_difference_in_days')) {

    function get_date_difference_in_days($start_date, $end_date) {

        $start = new DateTime($start_date);
        $end = new DateTime($end_date);

        return $end->diff($start)->format("%a");
    }

}
if (!function_exists('convert_date_utc_to_local_mimasoft')) {

    function convert_date_utc_to_local_mimasoft($date_time, $format = "Y-m-d H:i:s", $project_id) {
        $date = new DateTime($date_time . ' +00:00');
		
        $date->setTimezone(new DateTimeZone(get_setting_mimasoft($project_id, 'timezone')));
		//var_dump($date->format("Y-m-d H:i:s"));
        return $date->format($format);
    }

}



if(!function_exists('time_date_zone_format')){
	
	function time_date_zone_format($date, $project_id){
		
		$dateformat = get_setting_mimasoft($project_id, "date_format");
		$timeformat = get_setting_mimasoft($project_id, "time_format");
		//$timezoneformat = get_setting_mimasoft($project_id, "timezone");

		if($timeformat == '24_hours'){
			$t_format = 'H:i:s';	
		}
		elseif($timeformat == 'capital'){
			$t_format = 'h:i:s A';
		}
		else{
			$t_format = 'h:i:s a';
		}
		$f = convert_date_utc_to_local_mimasoft($date, $format = $dateformat.' '.$t_format, $project_id);
		return $f;	
		
	}
}

/**
* get the date and modify the format.
* @param string date.. in any format.
* @retrurn date in specific format.
*/
if(!function_exists('get_date_format')){
	
	function get_date_format($date, $project_id, $only_format = false){
		$format = get_setting_mimasoft($project_id, "date_format");
		//return date($format, strtotime(get_current_utc_time()) + get_timezone_offset());
		if($only_format){
			return $format;
		}else{
			return date($format, strtotime($date));
		}
		
		
	}
}

/**
* get the time .
* @param string date.. in any format.
* @retrurn date in specific format.
*/
if(!function_exists('get_time_format')){
	
	function get_time_format($project_id){
		$format = get_setting_mimasoft($project_id, "time_format");
		return $format;
	}
}


/*
*set time format
*@param $project_id
*@return time in specific format.
*/
if(!function_exists('set_time_format')){
	
	function set_time_format($project_id){
		$timeformat = get_setting_mimasoft($project_id, "time_format");
		if($timeformat == '24_hours'){
			return 'H:i:s';	
		}
		elseif($timeformat == 'capital'){
			return 'h:i:s A';
		}
		else{
			return 'h:i:s a';
		}
	}
	
}

/*
* Set timezone.
*@param $project_id
*@return timezone in specific format.
*/

if(!function_exists('modify_time_zone')){
	
	function modify_time_zone($project_id){
		
		$timeformat = set_time_format($project_id);
		
		$timezoneformat = get_setting_mimasoft($project_id, "timezone");
		$datetime = new DateTime();
		$newtimezone = new DateTimeZone($timezoneformat);
		$datetime->setTimezone($newtimezone);
		return $datetime->format($timeformat);
	}
	
}

if(!function_exists('convert_to_general_settings_time_format')){
  
  function convert_to_general_settings_time_format($id_proyecto = 0, $time){
		$ci = & get_instance();
		$proyecto = $ci->Projects_model->get_one($id_proyecto);
		$id_cliente = $proyecto->client_id;
		$general_settings = $ci->General_settings_model->get_one_where(array("id_cliente" => $id_cliente, "id_proyecto" => $id_proyecto, "deleted" => 0));
		  $time_format =  $general_settings->time_format;
		  if($time_format == 'capital'){
			  $valor_campo = date('h:i:s A',strtotime($time));
		  }else if($time_format == 'small'){
			  $valor_campo = date('h:i:s a',strtotime($time));				
		  }else{
			  $valor_campo = $time;
		  }
		  return $valor_campo;
	  }
}


/**
 * return an array of dates in format Y-m-d ordered
 * 
 * @param array $array
 * @return array ordered
 */
if(!function_exists('order_array_of_dates')){
	function order_array_of_dates($array = array()){
		uasort($array, "date_sort");
		return $array;
	}
}

function date_sort($a, $b) {
    return strtotime($a) - strtotime($b);
}

if(!function_exists('usort_array_of_dates')){
	function usort_array_of_dates($array = array()){
		usort($array, "date_sort");
		return $array;
	}
}

/**
 * Recibe un datetime (string) y devuelve un texto con la fecha y hora formateada en base a la configuración de un proyecto en específico
 * Ejemplo: $date_time = "2015-01-01 23:10:00" retornará algo como: Hoy a las 23:10 PM
 *
 * @param string $date_time .. it will be considered as UTC time.
 * @param $project_id .. el id de un proyecto
 * @param $is_short_date .. si es true, devuelve la fecha en formato corto
 * @return date time
 */
if (!function_exists('format_to_relative_time_for_projects')) {

    function format_to_relative_time_for_projects($date_time, $project_id, $is_short_date = false) {

        $target_date = new DateTime($date_time);

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(get_setting_mimasoft($project_id, "timezone")));
        $today = $now->format("Y-m-d");
        $date = "";
        $short_date = "";
		
		$hour = convert_date_utc_to_local_mimasoft($date_time, $format = "H:i:s", $project_id);
		$hour = convert_to_general_settings_time_format($project_id, $hour);
			
        if ($now->format("Y-m-d") == $target_date->format("Y-m-d")) {
            $short_date = lang("today");
			$date = lang("today_at")." ".$hour; //today
        } else if (date('Y-m-d', strtotime(' -1 day', strtotime($today))) === $target_date->format("Y-m-d")) {
			$short_date = lang("yesterday");
			$date = lang("yesterday_at")." ".$hour; //yesterday
	    } else {
			$date = time_date_zone_format($date_time, $project_id);
			$short_date = time_date_zone_format($date_time, $project_id);
		}
		
        if ($is_short_date) {
            return $short_date;
        } else {
			return $date;
        }
    
	}

}


/**
 * Recibe un datetime (string) y devuelve un texto con la fecha y hora formateada en base a la configuración de un cliente en específico
 * Ejemplo: $date_time = "2015-01-01 23:10:00" retornará algo como: Hoy a las 23:10 PM
 *
 * @param string $date_time .. it will be considered as UTC time.
 * @param $client_id .. el id de un cliente
 * @param $is_short_date .. si es true, devuelve la fecha en formato corto
 * @return date time
 */
if (!function_exists('format_to_relative_time_for_clients')) {

    function format_to_relative_time_for_clients($date_time, $client_id, $is_short_date = false) {

        $target_date = new DateTime($date_time);

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(get_setting_client_mimasoft($client_id, "timezone")));
        $today = $now->format("Y-m-d");
        $date = "";
        $short_date = "";
		
		$hour = convert_date_utc_to_local_client_mimasoft($date_time, $format = "H:i:s", $client_id);
		$hour = convert_to_general_settings_client_time_format($client_id, $hour);
			
        if ($now->format("Y-m-d") == $target_date->format("Y-m-d")) {
            $short_date = lang("today");
			$date = lang("today_at")." ".$hour; //today
        } else if (date('Y-m-d', strtotime(' -1 day', strtotime($today))) === $target_date->format("Y-m-d")) {
			$short_date = lang("yesterday");
			$date = lang("yesterday_at")." ".$hour; //yesterday
	    } else {
			$date = time_date_zone_client_format($date_time, $client_id);
			$short_date = time_date_zone_client_format($date_time, $client_id);
		}
		
        if ($is_short_date) {
            return $short_date;
        } else {
			return $date;
        }
    
	}

}

if (!function_exists('convert_date_utc_to_local_client_mimasoft')) {

    function convert_date_utc_to_local_client_mimasoft($date_time, $format = "Y-m-d H:i:s", $client_id) {
        $date = new DateTime($date_time . ' +00:00');
		
        $date->setTimezone(new DateTimeZone(get_setting_client_mimasoft($client_id, 'timezone')));
		//var_dump($date->format("Y-m-d H:i:s"));
        return $date->format($format);
    }

}

if(!function_exists('convert_to_general_settings_client_time_format')){
  
	function convert_to_general_settings_client_time_format($id_cliente = 0, $time){
		$ci = & get_instance();
		$general_settings = $ci->General_settings_clients_model->get_one_where(array("id_cliente" => $id_cliente, "deleted" => 0));
		$time_format =  $general_settings->time_format;
		if($time_format == 'capital'){
			$valor_campo = date('h:i:s A',strtotime($time));
		}else if($time_format == 'small'){
			$valor_campo = date('h:i:s a',strtotime($time));				
		}else{
			$valor_campo = $time;
		}
		return $valor_campo;
	}
}

if(!function_exists('time_date_zone_client_format')){
	
	function time_date_zone_client_format($date, $client_id){
		
		$dateformat = get_setting_mimasoft($client_id, "date_format");
		$timeformat = get_setting_mimasoft($client_id, "time_format");
		//$timezoneformat = get_setting_mimasoft($project_id, "timezone");

		if($timeformat == '24_hours'){
			$t_format = 'H:i:s';	
		}
		elseif($timeformat == 'capital'){
			$t_format = 'h:i:s A';
		}
		else{
			$t_format = 'h:i:s a';
		}
		$f = convert_date_utc_to_local_client_mimasoft($date, $format = $dateformat.' '.$t_format, $client_id);
		return $f;	
		
	}
}

/*
*set time format
*@param $client_id
*@return time in specific format.
*/
if(!function_exists('set_time_format_client')){
	
	function set_time_format_client($client_id){
		$timeformat = get_setting_client_mimasoft($client_id, "time_format");
		if($timeformat == '24_hours'){
			return 'H:i:s';	
		}
		elseif($timeformat == 'capital'){
			return 'h:i:s A';
		}
		else{
			return 'h:i:s a';
		}
	}
	
}


if (!function_exists('format_to_datetime_clients')) {

    function format_to_datetime_clients($client_id, $date_time) {
		
		// Traer configuración del cliente
		$date_format = get_setting_client_mimasoft($client_id, "date_format");
		$time_format = get_setting_client_mimasoft($client_id, "time_format");
				
		// Convertir la fecha de timezone UTC según configuración del cliente
		$date = convert_date_utc_to_local_client_mimasoft($date_time, $date_format, $client_id);
		
		// Formato de hora según configuración del cliente
		$t_format = '';
		if($time_format == '24_hours'){
			$t_format = 'H:i:s';	
		}
		elseif($time_format == 'capital'){
			$t_format = 'h:i:s A';
		}
		else{
			$t_format = 'h:i:s a';
		}
		
		$time = convert_date_utc_to_local_client_mimasoft($date_time, $t_format, $client_id);
		
		$formatted_date = $date.' '.$time;
		return $formatted_date;
		
	}

}

if (!function_exists('format_to_date_clients')) {

    function format_to_date_clients($client_id, $date_time) {
		
		$date_format = get_setting_client_mimasoft($client_id, "date_format");
		$formatted_date = convert_date_utc_to_local_client_mimasoft($date_time, $date_format, $client_id);
		
		return $formatted_date;
		
	}

}

if (!function_exists('format_to_time_clients')) {

    function format_to_time_clients($client_id, $date_time) {
		
		$time_format = get_setting_client_mimasoft($client_id, "time_format");
		
		// Formato de hora según configuración del cliente
		$t_format = '';
		if($time_format == '24_hours'){
			$t_format = 'H:i:s';	
		}
		elseif($time_format == 'capital'){
			$t_format = 'h:i:s A';
		}
		else{
			$t_format = 'h:i:s a';
		}
		
		$formatted_time = convert_date_utc_to_local_client_mimasoft($date_time, $t_format, $client_id);
		
		return $formatted_time;
		
	}

}

/** date_to_timestamp_millis_utc
 * Genera un timestamp a partir de una fecha en string en formato Y-m-d H:i:s
 * @author: Christopher Mauricio Sam Venegas
 * @param str_date string con fecha en formato Y-m-d H:i:s
 * @return timestamp 
 */
if (!function_exists('date_to_timestamp_millis_utc')) {
    function date_to_timestamp_millis_utc($str_date){
        try{
            $fecha = new DateTime($str_date, new DateTimeZone('UTC'));
        }catch(Exception $e){
            return null;
        }
        return $fecha->getTimestamp() * 1000;
    }
}