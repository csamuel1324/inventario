<?php
require_once('includes/load.php');

function find_dispatch_by_dates($start_date, $end_date) {
  global $db;
  $sql  = "SELECT s.date, s.qty, s.price, s.destination, p.name ";
  $sql .= "FROM sales s ";
  $sql .= "LEFT JOIN products p ON s.product_id = p.id ";
  $sql .= "WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}' ";
  $sql .= "ORDER BY s.date ASC";
  return find_by_sql($sql);
}
?>
