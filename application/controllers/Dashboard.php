<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
  public function __construct(){
    parent::__construct();
    if (!$this->session->userdata('user_id')) {
      redirect('auth/login');
    }
    $this->load->model('Project_model');
    $this->load->model('Task_model');
    $this->load->helper('url');
  }

  public function index(){
    $data['projects'] = $this->Project_model->get_all();
    $data['tasks'] = $this->Task_model->get_recent(10);
    $this->load->view('templates/header');
    $this->load->view('dashboard/index', $data);
    $this->load->view('templates/footer');
  }
}
