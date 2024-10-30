<?php

require_once('../../../wp-load.php');
require_once('lib/php-ofc-library/open-flash-chart.php');

if ( !current_user_can('manage_options') ) {
  wp_die( __('You are not allowed to access this part of the site.') );
}

$unit = isset($_GET['unit']) ? $_GET['unit'] : 1;

if (!in_array($unit, array(1, 7, 31))) {
  $unit = 1;
}

$data = array();
$labels = array();

$max = 100;

$now = time();

switch ($unit) {
  # days
  case 1:

    $start_time = $now - 2592000;

    list($day_today, $this_month) = explode(' ', date('j n'));
    list($start_day, $start_month, $days_last_month, $start_year) = explode(' ', date('d m t Y', $start_time));

    for ($i = $start_day; $i <= $days_last_month; $i++) {
      $labels[] = $start_month . '-' . $i;
    }

    if (count($labels) < 31) {
      for ($i = 1; $i <= $day_today; $i++) {
        $labels[] = $this_month . '-' . $i;
      }
    }

    $query = "SELECT MONTH(FROM_UNIXTIME(`time_added`)) as `month`, DAY(FROM_UNIXTIME(`time_added`)) AS `day`, YEAR(FROM_UNIXTIME(`time_added`)) as `year`, COUNT(`id`) as `count`
              FROM `" . $wpdb->prefix . "invit0r_invitees`
              WHERE `time_added` >= UNIX_TIMESTAMP(" . $start_year . $start_month . $start_day . ")
              GROUP BY `year`, `month`, `day`
              ORDER BY `time_added` ASC";

    // echo $query;
    // die();

    $results = $wpdb->get_results($query);


    foreach ($labels as $index => $label) {

      $continue = false;
      foreach ($results as $result) {
        if ($result->count > $max) {
          $max = $result->count;
        }

        if ($label == $result->month . '-' . $result->day) {
          $data[$index] = (int)$result->count;
          $continue = true;
          break;
        }
      }

      if (!$continue) {
        $data[$index] = 0;
      }
    }

    break;

  # weeks
  case 7:

    $start_time = $now - 15724800;

    list($this_week, $this_year) = explode(' ', date('W Y'));
    list($start_day, $start_month, $start_week, $start_year) = explode(' ', date('d m W Y', $start_time));

    for ($year = $start_year; $year <= $this_year; $year++) {
      $w = $year == $start_year ? $start_week : 1;
      for ($week = $w; $week <= 53; $week++) {
        $labels[] = $year . '-' . $week;
        if ($year == $this_year && $week == $this_week) {
          break;
        }
      }
    }

    $query = "SELECT YEAR(FROM_UNIXTIME(`time_added`)) as `year`, WEEK(FROM_UNIXTIME(`time_added`)) AS `week`, COUNT(`id`) as `count`
              FROM `" . $wpdb->prefix . "invit0r_invitees`
              WHERE `time_added`  >= UNIX_TIMESTAMP(" . $start_year . $start_month . $start_day . ")
              GROUP BY `year`, `week`
              ORDER BY `time_added` ASC";

    $results = $wpdb->get_results($query);


    foreach ($labels as $index => $label) {

      $continue = false;
      foreach ($results as $result) {
        if ($result->count > $max) {
          $max = $result->count;
        }

        if ($label == $result->year . '-' . $result->week) {
          $data[$index] = (int)$result->count;
          $continue = true;
          break;
        }
      }

      if (!$continue) {
        $data[$index] = 0;
      }
    }

    break;


  # months
  case 31:

    $start_time = $now - 31536000;

    list($this_month, $this_year) = explode(' ', date('n Y'));
    list($start_day, $start_month, $start_year) = explode(' ', date('d m Y', $start_time));

    for ($year = $start_year; $year <= $this_year; $year++) {
      $m = $year == $start_year ? $start_month : 1;
      for ($month = $m; $month <= 12; $month++) {
        $labels[] = $year . '-' . $month;
        if ($year == $this_year && $month == $this_month) {
          break;
        }
      }
    }

    $query = "SELECT YEAR(FROM_UNIXTIME(`time_added`)) as `year`, MONTH(FROM_UNIXTIME(`time_added`)) AS `month`, COUNT(`id`) as `count`
              FROM `" . $wpdb->prefix . "invit0r_invitees`
              WHERE `time_added` >= UNIX_TIMESTAMP(" . $start_year . $start_month . $start_day . ")
              GROUP BY `year`, `month`
              ORDER BY `time_added` ASC";

    $results = $wpdb->get_results($query);


    foreach ($labels as $index => $label) {

      $continue = false;
      foreach ($results as $result) {
        if ($result->count > $max) {
          $max = $result->count;
        }

        if ($label == $result->year . '-' . $result->month) {
          $data[$index] = (int)$result->count;
          $continue = true;
          break;
        }
      }

      if (!$continue) {
        $data[$index] = 0;
      }
    }

    break;

}

$title = new title(' ');

$d = new hollow_dot();
$d->size(5)->halo_size(0)->colour('#3D5C56');

$line = new line();
$line->set_default_dot_style($d);
$line->set_values( $data );
$line->set_width( 2 );
$line->set_colour( '#3D5C56' );

$x_labels = new x_axis_labels();
$x_labels->rotate(300);
$x_labels->set_labels( $labels );

$x = new x_axis();
$x->set_labels( $x_labels );

$step = $max > 0 ? ceil($max / 5) : 0;

$y = new y_axis();
$y->set_range( 0, $step * 5,  $step);


$chart = new open_flash_chart();
$chart->set_title( $title );
$chart->add_element( $line );
$chart->set_x_axis( $x );
$chart->set_y_axis( $y );

echo $chart->toPrettyString();
