<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lane extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_lane');
	}

	public function index()
	{
		$data['title'] = "Lane";
		$data['content'] = "airport/lane/index";
		$data['shelter'] = $this->m_lane->get_master('master.t_mtr_shelter');
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_lane->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['title'] = "Add Lane";
		$data['shelter'] = $this->m_lane->get_master('master.t_mtr_shelter');
		$this->load->view("airport/lane/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$shelter_id=$this->input->post('shelter_id');
		$lane_name=trim($this->input->post('lane_name'));

		$this->form_validation->set_rules('shelter_id', 'Shelter', 'required');
		$this->form_validation->set_rules('lane_name', 'Name', 'required');

		$data=array(
			'shelter_id'=>$shelter_id,
			'lane_name'=>$lane_name,
			'created_by'=>$this->session->userdata('username'),
		);
		
		$checkLane=$this->m_global->getDataById("master.t_mtr_lane","shelter_id=$shelter_id and upper(lane_name)=upper('".$lane_name."') and status='1'")->num_rows();	

		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field');
        }
        else if($checkLane>0)
        {
        	echo $this->msg_error('Lane already in use');
        }
        else
        {
        	$insert=$this->m_global->insert("master.t_mtr_lane",$data);
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
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$data['shelter'] = $this->m_lane->get_master('master.t_mtr_shelter');
		$data['lane'] = $this->m_lane->get_edit($id);
		$data['id'] = encode($id);
		$data['title'] = "Edit Lane";
		$this->load->view("airport/lane/edit",$data);

	}
	function action_edit()
	{
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		validateAjax();
		$id = decode($this->input->post('id'));
		// $shelter_id=decode($this->input->post('shelter_id'));
		$lane_name=trim($this->input->post('lane_name'));

		// $this->form_validation->set_rules('shelter_id', 'Shelter', 'required');
		$this->form_validation->set_rules('lane_name', 'Name', 'required');

		$lane_shelter_id=$this->m_global->getDataById("master.t_mtr_lane","id_seq=$id")->row();

		$data=array(
			// 'shelter_id'=>$shelter_id,
			'lane_name'=>$lane_name,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$checkLane=$this->m_global->getDataById("master.t_mtr_lane","shelter_id=$lane_shelter_id->shelter_id and lane_name='".$lane_name."' and status='1' and id_seq !=$id")->num_rows();
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field');
        }
        else if ($checkLane>0)
        {
        	echo $this->msg_error('Lane already in use');
        }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_lane",$data,"id_seq='".$id."'");
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

		// mencari jika data sudah di pairing di user manless shelter
		$checkManlessGate=$this->m_global->getDataById("master.t_mtr_user_manless_gate","lane_id=".$id." and status not in (1,-1)")->num_rows();

		if($checkManlessGate>0)
		{
			echo $this->msg_error('Cannot delete, lane already paired to user manless shelter');
		}
		else
		{
			$delete=$this->m_global->update("master.t_mtr_lane",$data,"id_seq=$id");
			if ($delete)
			{
				echo $this->msg_success('Success delete data');
			}
			else
			{
				echo $this->msg_error('Failed delete data');
			}
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