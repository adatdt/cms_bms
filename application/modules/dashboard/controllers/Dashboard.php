<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Dashboard extends CI_Controller {

  public function __construct() {
    parent::__construct();
    getSession();
    $this->load->model('m_dashboard');
  }

  public function index() {
    $data['title'] = "Dashboard";
    $data['content'] = "dashboard";
    $data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
    $this->load->view('common/page', $data);
  }
  
  function get_summary() {
    validateAjax();
    $tap_in = $this->m_dashboard->get_tap_in();
    $exit_terminal = $this->m_dashboard->get_exit_terminal();
    $booking = $this->m_dashboard->get_booking();
    $boarding = $this->m_dashboard->get_boarding();
    
    $data = array(
        'tap_in' => $tap_in,
        'exit_terminal' => $exit_terminal,
        'booking' => $booking,
        'boarding' => $boarding
    );
    echo json_encode($data);
  }
  
  function get_booking_passanger() {
    validateAjax();
    $booking_passanger = $this->m_dashboard->get_booking_passanger();
    echo json_encode($booking_passanger);
  }

  public function po() {
    $data['title'] = "Waiting List Penumpang";
    $data['content'] = "dashboard_po";
    // $data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
    $user_po=$this->m_global->getDataById("master.t_mtr_user_po","user_id=".decode($this->session->userdata('user_id')));

    if ($user_po->num_rows() > 0)
    {
      $data['user_po'] = 1;
      $data['po'] = $this->db->query("SELECT * FROM master.t_mtr_po WHERE status=1 and id_seq=".$user_po->row()->po_id)->row();
    }
    else
    {
      $data['user_po'] = 0;
      $data['po'] = $this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
    }
    $data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
    $this->load->view('common/page', $data);
  }

  public function getListDashboardPo()
  {
    validateAjax();
    $getUserGroup = $this->m_global->getDataById("master.t_mtr_user_po", "user_id='".decode($this->session->userdata('user_id'))."'");
    if($getUserGroup->num_rows() > 0) {
      $list = $this->m_dashboard->getDataDashboardPo($getUserGroup->row()->po_id);
    }
    else
    {
      $list = $this->m_dashboard->getDataDashboardPo(0);
    }
    echo json_encode($list);
  }

  public function getListTracking()
  {
  	validateAjax();
    $getUserGroup = $this->m_global->getDataById("master.t_mtr_user_po", "user_id='".decode($this->session->userdata('user_id'))."'");
    if($getUserGroup->num_rows() > 0) {
      $list = $this->m_dashboard->getTracking($getUserGroup->row()->po_id);
    }
    else
    {
      $list = $this->m_dashboard->getTracking();
    }
    echo json_encode($list);
  }

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */