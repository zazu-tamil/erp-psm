<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {
  public function __construct(){
    parent::__construct();
    if (!$this->session->userdata('user_id')) redirect('auth/login');
    $this->load->model('Project_model');
    $this->load->model('Time_log_model');
    $this->load->helper('url');
  }

  public function time_by_project(){
    $projects = $this->Project_model->get_all();
    $data = [];
    foreach($projects as $p){
      $minutes = $this->Time_log_model->sum_minutes_by_project($p->id);
      $data[] = ['project'=>$p, 'minutes'=>$minutes];
    }
    $this->load->view('templates/header');
    $this->load->view('reports/time_by_project', ['data'=>$data]);
    $this->load->view('templates/footer');
  }
}
