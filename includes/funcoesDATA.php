<?php

/*****************************************************************************************/
/* calcula idade em anos, meses e dias */
/*****************************************************************************************/

function date_delta($ts_start_date, $ts_end_date) {
  $secs_in_day = 86400;

  $i_years = gmdate('Y', $ts_end_date) - gmdate('Y', $ts_start_date);
  $i_months = gmdate('m', $ts_end_date) - gmdate('m', $ts_start_date);
  $i_days = gmdate('d', $ts_end_date) - gmdate('d', $ts_start_date);
//  if ($i_days < 0)
//    $i_months--;
  if ($i_months < 0) {
    $i_years--;
    $i_months += 12;
  }
  if ($i_days < 0) {
    $i_days = gmdate('d', gmmktime(0, 0, 0,
      gmdate('m', $ts_start_date)+1,
      0,
      gmdate('Y', $ts_start_date))) -
      gmdate('d', $ts_start_date);
    $i_days += gmdate('d', $ts_end_date);
  }

  # calculate HMS delta
  $f_delta = $ts_end_date - $ts_start_date;
  $f_secs = $f_delta % $secs_in_day;
  $f_secs -= ($i_secs = $f_secs % 60);
  $i_mins = intval($f_secs/60)%60;
  $f_secs -= $i_mins * 60;
  $i_hours = intval($f_secs/3600);

  return array($i_years, $i_months, $i_days,
               $i_hours, $i_mins, $i_secs);
}

function calculate_age($s_start_date,
    $s_end_date = '',
    $b_show_all = 0) {
  $b_show_time = strlen($s_start_date > 8);
  $ts_start_date =
    mktime(substr($s_start_date, 8, 2),
      substr($s_start_date, 10, 2),
      substr($s_start_date, 12, 2),
      substr($s_start_date, 4, 2),
      substr($s_start_date, 6, 2),
      substr($s_start_date, 0, 4));
  if ($s_end_date) {
    $ts_end_date =
      mktime(substr($s_end_date, 8, 2),
        substr($s_end_date, 10, 2),
        substr($s_end_date, 12, 2),
        substr($s_end_date, 4, 2),
        substr($s_end_date, 6, 2),
        substr($s_end_date, 0, 4));
  } else {
    $ts_end_date = time();
  }

  list ($i_age_years, $i_age_months, $i_age_days,
        $i_age_hours, $i_age_mins, $i_age_secs) =
       date_delta($ts_start_date, $ts_end_date);

  # output
  $s_age = '';
  
  
  if ($i_age_years)
    //$s_age .= "$i_age_years ano".
      //(abs($i_age_years)>1?'s':'');
    $s_age .= "$i_age_years A";
    

  else if ( $i_age_months)
    $s_age .= ($s_age?',':'').
      "$i_age_months M ".
      (abs($i_age_months)>1?'':'');  

//    $s_age .= ($s_age?',':'').
//      "$i_age_months mes".
//      (abs($i_age_months)>1?'es':'');

/*  
  if ($b_show_all && $i_age_days )
    $s_age .= ($s_age?',':'').
      "$i_age_days dia".
      (abs($i_age_days)>1?'s':'');

/* nao mostra horas, min, segs
  
  if ($b_show_time && $i_age_hours)
    $s_age .= ($s_age?', ':'').
      "$i_age_hours hora".
      (abs($i_age_hours)>1?'s':'');
      
  if ($b_show_time && $i_age_mins)
    $s_age .= ($s_age?', ':'').
      "$i_age_mins minuto".
      (abs($i_age_mins)>1?'s':'');
  if ($b_show_time && $i_age_secs)
    $s_age .= ($s_age?', ':'').
      "$i_age_secs segundo".
      (abs($i_age_secs)>1?'s':'');
      
*/      
  return $s_age;
}



?>
