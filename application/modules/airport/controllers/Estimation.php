<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Estimation extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_estimation');
	}

	public function index()
	{
		$data['title'] = "Estimation";
		$data['content'] = "airport/estimation/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_estimation->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Estimation";
		$data['airport'] = $this->m_estimation->get_master('master.t_mtr_airport');
		$data['shelter'] = $this->m_estimation->get_master('master.t_mtr_shelter');
		$this->load->view("airport/estimation/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$airport_id=$this->input->post('airport_id');
		$origin=$this->input->post('origin');
		$destination=$this->input->post('destination');
		$duration=$this->input->post('duration');

		$this->form_validation->set_rules('airport_id', 'airport_id', 'required');
		$this->form_validation->set_rules('origin', 'origin', 'required');
		$this->form_validation->set_rules('destination', 'destination', 'required');
		$this->form_validation->set_rules('duration', 'duration', 'required');

		$data=array(
			'airport_id'=>$airport_id,
			'origin'=>$origin,
			'destination'=>$destination,
			'duration_time'=>$duration,
			'created_by'=>$this->session->userdata('username'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field');
        }
        else
        {
        	$insert=$this->m_global->insert("master.t_mtr_estimated_arrival_time",$data);
			if ($insert)
			{
				echo $this->msg_success('Success add data');
			}
			else
			{
				echo $this->msg_error('Failed add data');
			}
        }

	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['airport'] = $this->m_estimation->get_master('master.t_mtr_airport');
		$data['shelter'] = $this->m_estimation->get_master('master.t_mtr_shelter');
		$data['estimation'] = $this->m_estimation->get_edit($id);
		$data['id'] = encode($id);
		$data['title'] = "Edit Estimation";
		$this->load->view("airport/estimation/edit",$data);

	}
	function action_edit()
	{
		validateAjax();
		$id = decode($this->input->post('id'));
		$airport_id=$this->input->post('airport_id');
		$origin=$this->input->post('origin');
		$destination=$this->input->post('destination');
		$duration=$this->input->post('duration');

		$this->form_validation->set_rules('airport_id', 'airport_id', 'required');
		$this->form_validation->set_rules('origin', 'origin', 'required');
		$this->form_validation->set_rules('destination', 'destination', 'required');
		$this->form_validation->set_rules('duration', 'duration', 'required');

		$data=array(
			'airport_id'=>$airport_id,
			'origin'=>$origin,
			'destination'=>$destination,
			'duration_time'=>$duration,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field');
        }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_estimated_arrival_time",$data,"id_seq='".$id."'");
			if ($update)
			{
				echo $this->msg_success('Success update data');
			}
			else
			{
				echo $this->msg_error('Failed update data');
			}
        }		
	}
	
	public function delete($id)
	{
		validateAjax();
		$id = decode($id);

		$data=array(
			'status'=>-5,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

    	$delete=$this->m_global->update("master.t_mtr_estimated_arrival_time",$data,"id_seq=$id");
		if ($delete)
		{
			echo $this->msg_success('Success delete data');
		}
		else
		{
			echo $this->msg_error('Failed delete data');
		}
	}

	function msg_error($message)
	{
			return	json_encode(array(
				'code' => 101, 
				'header' => 'Error',
				'message' => $message,
				'theme' => 'alert-styled-left bg-danger'));
	}

	function msg_success($message)
	{
			return	json_encode(array(
				'code' => 200, 
				'header' => 'Success',
				'message' => $message,
				'theme' => 'alert-styled-left bg-success'));
	}

}