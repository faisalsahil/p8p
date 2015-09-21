<?php
class Reminders extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
      $this->load->library('input');
      $this->load->model('cron_model');
  }
  public function index()
  {
    $date =  date('Y-m-d');   //var_dump($arrayCoupons);
    echo $date;
  }

}