<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
  public function __construct(){
    parent::__construct();
    if (!$this->session->userdata('user_id') || $this->session->userdata('role')!='admin') {
      redirect('auth/login');
    }
    $this->load->model('User_model');
    $this->load->helper(['url','form']);
    $this->load->library('form_validation');
  }

  public function index(){
    $data['users'] = $this->User_model->get_all();
    $this->load->view('templates/header');
    $this->load->view('users/index', $data);
    $this->load->view('templates/footer');
  }

  public function create(){
    if ($this->input->post()) {
      $this->form_validation->set_rules('name','Name','required');
      $this->form_validation->set_rules('email','Email','required|valid_email');
      $this->form_validation->set_rules('password','Password','required|min_length[6]');
      if ($this->form_validation->run()===TRUE) {
        $payload = [
          'name'=>$this->input->post('name', true),
          'email'=>$this->input->post('email', true),
          'password'=>password_hash($this->input->post('password'), PASSWORD_DEFAULT),
          'role'=>$this->input->post('role', true)
        ];
        $this->User_model->insert($payload);
        redirect('users');
      }
    }
    $this->load->view('templates/header');
    $this->load->view('users/create', []);
    $this->load->view('templates/footer');
  }
}
