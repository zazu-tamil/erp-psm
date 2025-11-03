<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TaskViewHelper extends CI_Controller {
  public function __construct(){
    parent::__construct();
    if (!$this->session->userdata('user_id')) redirect('auth/login');
    $this->load->model('Task_model');
    $this->load->model('Task_comment_model');
    $this->load->model('Time_log_model');
  }

  // Internal helper you can call via route tasks/view/(:num) mapped here
  public function view($id){
    $task = $this->Task_model->get($id);
    if (!$task) show_404();
    $comments = $this->Task_comment_model->get_by_task($id);
    // attach user names if possible
    foreach($comments as &$c){
      $u = $this->db->where('id',$c->user_id)->get('users')->row();
      $c->name = $u->name ?? 'User';
    }
    $timelogs = $this->Time_log_model->get_by_task($id);
    $this->load->view('templates/header');
    $this->load->view('tasks/view', ['task'=>$task,'comments'=>$comments,'timelogs'=>$timelogs]);
    $this->load->view('templates/footer');
  }
}
