<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Controller {
  public function __construct(){
    parent::__construct();
    if (!$this->session->userdata('user_id')) {
      redirect('auth/login');
    }
    $this->load->model('Project_model');
    $this->load->helper(['url','form']);
    $this->load->library('form_validation');
  }

  public function index(){
    $data['projects'] = $this->Project_model->get_all();
    $this->load->view('templates/header');
    $this->load->view('projects/index', $data);
    $this->load->view('templates/footer');
  }

  public function create(){
    $data = [];
    if ($this->input->post()) {
      $this->form_validation->set_rules('name','Project Name','required');
      if ($this->form_validation->run() === TRUE) {
        $payload = [
          'name' => $this->input->post('name', true),
          'client' => $this->input->post('client', true),
          'description' => $this->input->post('description', true),
          'status' => $this->input->post('status', true),
          'start_date' => $this->input->post('start_date', true)?:null,
          'end_date' => $this->input->post('end_date', true)?:null,
          'created_by' => $this->session->userdata('user_id')
        ];
        $this->Project_model->insert($payload);
        redirect('projects');
      }
    }
    $this->load->view('templates/header');
    $this->load->view('projects/create', $data);
    $this->load->view('templates/footer');
  }

  public function view($id){
    $data['project'] = $this->Project_model->get($id);
    if (!$data['project']) show_404();
    $this->load->model('Task_model');
    $data['tasks_by_status'] = $this->Task_model->get_by_project_grouped($id);
    $this->load->view('templates/header');
    $this->load->view('projects/view', $data);
    $this->load->view('templates/footer');
  }
}
