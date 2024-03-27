<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Terminal extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_terminal');
	}

	public function index()
	{
		$data['title'] = "Device Terminal";
		$data['content'] = "device/terminal/index";
		$data['shelter'] = $this->m_terminal->get_master('master.t_mtr_shelter');
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_terminal->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Terminal";
		$data['terminal_type'] = $this->m_terminal->get_master('master.t_mtr_device_terminal_type');
		$data['shelter'] = $this->m_terminal->get_master('master.t_mtr_shelter');
		$data['airport'] = $this->m_terminal->get_master('master.t_mtr_airport');
		$this->load->view("device/terminal/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$airport_id=$this->input->post('airport_id');
		// $terminal_code=$this->input->post('terminal_code');
		$terminal_name=$this->input->post('terminal_name');
		$terminal_type_id=$this->input->post('terminal_type_id');
		$shelter_id=$this->input->post('shelter_id');
		$imei=$this->input->post('imei');

		$this->form_validation->set_rules('airport_id', 'Airport', 'required');
		// $this->form_validation->set_rules('terminal_code', 'Code', 'required');
		$this->form_validation->set_rules('terminal_name', 'Name', 'required');
		$this->form_validation->set_rules('terminal_type_id', 'Type', 'required');
		$this->form_validation->set_rules('shelter_id', 'Shelter', 'required');
		// $this->form_validation->set_rules('imei', 'Imei', 'required');


		// creating code to 1 -> 01
		if(strlen($terminal_type_id)<=1)
		{
			$terminalId="0".$terminal_type_id;
		}
		else
		{
			$terminalId=$terminal_type_id;
		}

		// creating code to 1 -> 01
		if (strlen($shelter_id)<=1)
		{
			$shelterId="0".$shelter_id;
		}
		else
		{
			$shelterId=$shelter_id;
		}

		$code=$terminalId.$shelterId;

		$deviceCode=$this->m_terminal->generateCode($code);


		$data=array(
			'terminal_code'=>$deviceCode,
			'terminal_name'=>$terminal_name,
			'terminal_type_id'=>$terminal_type_id,
			'shelter_id'=>$shelter_id,
			'imei'=>$imei,
			'airport_id'=>$airport_id,
			'created_by'=>$this->session->userdata('username'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field');
        }
        else
        {
        	$insert=$this->m_global->insert("master.t_mtr_device_terminal",$data);
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
		$data['terminal'] = $this->m_terminal->get_edit($id);
		$data['terminal_type'] = $this->m_terminal->get_master('master.t_mtr_device_terminal_type');
		$data['shelter'] = $this->m_terminal->get_master('master.t_mtr_shelter');
		$data['airport'] = $this->m_terminal->get_master('master.t_mtr_airport');
		$data['id'] = encode($id);
		$data['title'] = "Edit Terminal";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("device/terminal/edit",$data);

	}
	function action_edit()
	{
		validateAjax();
		$terminal_name=$this->input->post('terminal_name');
		$imei=$this->input->post('imei');
		$id = decode($this->input->post('id'));
		$this->form_validation->set_rules('terminal_name', 'Name', 'required');

		$data=array(
			'terminal_name'=>$terminal_name,
			'imei'=>$imei,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field');
        }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_device_terminal",$data,"id_seq='".$id."'");
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

    	$delete=$this->m_global->update("master.t_mtr_device_terminal",$data,"id_seq=$id");
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