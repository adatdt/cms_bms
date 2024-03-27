<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Type extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_type');
	}

	public function index()
	{
		$data['title'] = "Device Type";
		$data['content'] = "device/type/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_type->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Device Type";
		$this->load->view("device/type/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$terminal_type_name=$this->input->post('terminal_type_name');

		$this->form_validation->set_rules('terminal_type_name', 'Type', 'required');

		$data=array(
			'terminal_type_name'=>$terminal_type_name,
			'status'=>1,
			'created_by'=>$this->session->userdata('username'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field');
        }
        else
        {
        	$insert=$this->m_global->insert("master.t_mtr_device_terminal_type",$data);
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
		$data['device_type_name'] = $this->m_type->get_edit($id);
		$data['id'] = encode($id);
		$data['title'] = "Edit Device Type";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("device/type/edit",$data);

	}
	function action_edit()
	{
		$id=decode($this->input->post('id'));
		$terminal_type_name=$this->input->post('terminal_type_name');

		$this->form_validation->set_rules('terminal_type_name', 'Type', 'required');

		$data=array(
			'terminal_type_name'=>$terminal_type_name,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field');
        }
        else
        {
        	$checkExist = $this->m_global->getDataById("master.t_mtr_device_terminal", "terminal_type_id='".$id."' AND status=1");

        	if($checkExist->num_rows() > 0)
        	{
        		echo $this->msg_error('Failed update data, this device paired');
        	}
        	else
        	{
	        	$update=$this->m_global->update("master.t_mtr_device_terminal_type",$data,"id_seq='".$id."'");
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

		$checkExist = $this->m_global->getDataById("master.t_mtr_device_terminal", "terminal_type_id='".$id."' AND status=1");

    	if($checkExist->num_rows() > 0)
    	{
    		echo $this->msg_error('Failed delete data, this device paired');
    	}
    	else
    	{
	    	$delete=$this->m_global->update("master.t_mtr_device_terminal_type",$data,"id_seq=$id");
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

/* End of file Type.php */
/* Location: ./application/controllers/Type.php */