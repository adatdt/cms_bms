<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bus_type extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_bustype');
		$this->load->model('m_global');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Master Bus Type";
		$data['content'] = "po/bus_type/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_bustype->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Bus type";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/bus_type/add",$data);
	}

	public function action_add()
	{
		$type=trim($this->input->post('type'));


		$data=array(
			'type'=>$type,
			'status'=>1,
			'created_by'=>$this->session->userdata('username'),
			'created_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('type', 'Bus Type', 'required');

		$check=$this->m_global->getDataById("master.t_mtr_bus_type","upper(type)=upper('".$type."') and status=1")->num_rows();


		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else if($check>0)
        {
        	echo $rest=$this->msg_error('Type already in use');	
        }
        else
        {
	       $insert=$this->m_global->insert("master.t_mtr_bus_type",$data);

			if ($insert)
			{
				echo $rest=$this->msg_success('Success add data');
			}
			else
			{
				 echo $rest=$this->msg_error('Failed add data');
			}
        }

                /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/bus_type/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['type']=$this->m_global->getDataById("master.t_mtr_bus_type","id_seq=$id")->row();
		$data['title'] = "Edit Bus Type";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/bus_type/edit",$data);

	}
	function action_edit()
	{
		$type=trim($this->input->post('type'));
		$id=decode($this->input->post('id'));

		$data=array(
			'type'=>$type,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('type', 'Type', 'required');
		//$checkFare=$this->m_global->getDataById("master.t_mtr_fare","bus_type_id=".$id)->num_rows();

		$checkFare=$this->m_global->getDataById("master.t_mtr_fare","status=1 and bus_type_id=".$id)->num_rows();

		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else if ($checkFare>0)
        {
        	 echo $rest=$this->msg_error('Cannot update, type already paired to fare');
        }
        else
        {
        	$update=$this->m_global->update('master.t_mtr_bus_type',$data,"id_seq=$id");

        	if ($update)
        	{
        		echo $rest=$this->msg_success('Success edit data');
        	}
        	else
        	{
        		echo $rest=$this->msg_error('failed edit data');
        	}
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/bus_type/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);				
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

		$checkFare2=$this->m_global->getDataById("master.t_mtr_fare","bus_type_id=".$id)->num_rows();

		$checkFare=$this->m_global->getDataById("master.t_mtr_fare","status=1 and bus_type_id=".$id)->num_rows();

    	$delete=$this->m_global->update("master.t_mtr_bus_type",$data,"id_seq=$id");

		if ($delete)
		{
			echo $rest=$this->msg_success('Success delete data');
		}
		else if($checkFare)
		{
			echo $rest=$this->msg_success('Cannot delete, type already paired to fare');	
		}
		else
		{
			echo $rest=$this->msg_error('Failed delete data');
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/bus_type/delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function disable($id)
	{
		$id = decode($id);
		$data = array(
			'id_seq' => $id,
			'status' => -1,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date('Y-m-d H:i:s'),
		);

		$disabled = $this->m_global->masterDisable('master.t_mtr_bus_type',$id,$data);

		if ($disabled) {
			json_disabled();
		}else{
			json_error();
		}
	}

	public function enable($id)
	{
		$id = decode($id);
		$data = array(
			'id_seq' => $id,
			'status' => 1,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date('Y-m-d H:i:s'),
		);

		$disabled = $this->m_global->masterDisable('master.t_mtr_bus_type',$id,$data);

		if ($disabled) {
			json_enabled();
		}else{
			json_error();
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

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */