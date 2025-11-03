<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends CI_Controller {
  public function __construct(){
    parent::__construct();
    if (!$this->session->userdata('user_id')) {
      redirect('auth/login');
    }
    $this->load->model('Task_model');
    $this->load->model('Task_comment_model');
    $this->load->model('Time_log_model');
    $this->load->helper(['url','form']);
  }

  public function create(){
    if ($this->input->post()) {
      $payload = [
        'project_id' => $this->input->post('project_id', true),
        'title' => $this->input->post('title', true),
        'description' => $this->input->post('description', true),
        'status' => $this->input->post('status', true)?:'todo',
        'priority' => $this->input->post('priority', true)?:'medium',
        'type' => $this->input->post('type', true)?:'task',
        'assignee_id' => $this->input->post('assignee_id', true)?:$this->session->userdata('user_id'),
        'reporter_id' => $this->session->userdata('user_id')
      ];
      $id = $this->Task_model->insert($payload);
      redirect('projects/view/'.$payload['project_id']);
    }
  }

  public function update_status_ajax(){
    $task_id = $this->input->post('task_id');
    $status = $this->input->post('status');
    if (!$task_id || !$status) {
      echo json_encode(['ok'=>false,'error'=>'missing']);
      return;
    }
    $this->Task_model->update($task_id, ['status'=>$status, 'updated_at'=>date('Y-m-d H:i:s')]);
    echo json_encode(['ok'=>true]);
  }

  public function add_comment(){
    $task_id = $this->input->post('task_id');
    $comment = $this->input->post('comment');
    $user_id = $this->session->userdata('user_id');
    if ($task_id && $comment) {
      $this->Task_comment_model->insert(['task_id'=>$task_id,'user_id'=>$user_id,'comment'=>$comment]);
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function add_time(){
    $task_id = $this->input->post('task_id');
    $minutes = (int)$this->input->post('minutes');
    $note = $this->input->post('note', true);
    $user_id = $this->session->userdata('user_id');
    if ($task_id && $minutes>0) {
      $this->Time_log_model->insert(['task_id'=>$task_id,'user_id'=>$user_id,'started_at'=>date('Y-m-d H:i:00'),'stopped_at'=>date('Y-m-d H:i:00', strtotime("+{$minutes} minutes")),'minutes'=>$minutes,'note'=>$note]);
      // update task spent_minutes
      $task = $this->Task_model->get($task_id);
      $new = ($task->spent_minutes ?: 0) + $minutes;
      $this->Task_model->update($task_id, ['spent_minutes'=>$new]);
    }
    redirect($_SERVER['HTTP_REFERER']);
  }
}
