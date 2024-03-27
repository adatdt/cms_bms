<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_pos');
		$this->load->library('bcrypt');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Device POS";
		$data['content'] = "device/pos/index";
		// $data['shelter'] = $this->m_pos->get_master('master.t_mtr_shelter');
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_pos->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['title'] = "Add Device POS";
		$data['airport'] = $this->m_pos->get_master('master.t_mtr_airport');
		$this->load->view("device/pos/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$airport_id=decode($this->input->post('airport_id'));
		$terminal_name=trim($this->input->post('terminal_name'));
		$username = $this->input->post('username');
		$password=trim($this->input->post('password'));

		$this->form_validation->set_rules('airport_id', 'Airport', 'required');
		$this->form_validation->set_rules('terminal_name', 'Name', 'required');

		// creating code 02 id typenya 00 karna tidak ada shelter
		$deviceCode=$this->m_pos->generateCode('0200');

		$dataCore=array(
        		'username'=>$username,
				'user_group_id'=>4, // hard cord user group denga pos =4
				'password'=>$this->bcrypt->hash(strtoupper(md5($password))),
				'first_name'=>$this->input->post('first_name'),
				'last_name'=>$this->input->post('last_name'),
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
		);

		$data=array(
			'terminal_code'=>$deviceCode,
			'terminal_name'=>$terminal_name,
			'terminal_type_id'=>2, // hard cord type terminalnya 2
			'shelter_id'=>0, // hard code type pengendapan
			'airport_id'=>$airport_id,
			'created_by'=>$this->session->userdata('username'),
		);

		$checkUsername=$this->m_global->getDataById("core.t_mtr_user","upper(username)=upper('".$username."')")->num_rows();
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $res=$this->msg_error('Please input the field');
        }else if($checkUsername>0){
			echo $res=$this->msg_error('Username already in use');
			$data=$dataCore;
		}else{

        	$this->db->trans_begin();

			$user_id = $this->m_pos->insert_id("core.t_mtr_user",$dataCore);

        	$terminal_id =$this->m_pos->insert_id("master.t_mtr_device_terminal",$data);

        	$data_user_pos = array(
        		'user_id' => $user_id,
        		'terminal_id' => $terminal_id,
        		'firstname' => $this->input->post('first_name'),
        		'lastname' => $this->input->post('last_name'),
        		'status' => 1,
        		'created_by' => $this->session->userdata('username'),
        	);

        	$this->m_global->insert("master.t_mtr_user_pos",$data_user_pos);

        	if($this->db->trans_status() === FALSE) {
            	$this->db->trans_rollback();

            	echo $res=$this->msg_error("Failed add data");
	        } 
	        else 
	        {
	            $this->db->trans_commit();
	            echo $res=$this->msg_Success("Success add data");
	        }
			// if ($insert)
			// {
			// 	echo $res=$this->msg_success('Success add data');
			// }
			// else
			// {
			// 	echo $res=$this->msg_error('Failed add data');
			// }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/pos/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['terminal'] = $this->m_pos->get_edit($id);
		$data['airport'] = $this->m_pos->get_master('master.t_mtr_airport');
		$data['id'] = encode($id);
		$data['title'] = "Edit Device POS";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("device/pos/edit",$data);
	}

	function action_edit()
	{
		validateAjax();
		$terminal_name=trim($this->input->post('terminal_name'));
		$id = decode($this->input->post('id'));

		$this->form_validation->set_rules('terminal_name', 'Name', 'required');

		$data=array(
			'terminal_name'=>$terminal_name,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		$checkUserPos=$this->m_global->getDataById("master.t_mtr_user_pos","terminal_id=".$id)->num_rows();

		if ($this->form_validation->run() == FALSE)
        {
            echo $res=$this->msg_error('Please input the field');
        }
        // else if ($checkUserPos>0)
        // {
        // 	echo $res=$this->msg_error('Cannot update, device POS already paired to user');
        // }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_device_terminal",$data,"id_seq='".$id."'");
			if ($update)
			{
				echo $res=$this->msg_success('Success update data');
			}
			else
			{
				echo $res=$this->msg_error('Failed update data');
			}
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/pos/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

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

		$checkUserPos=$this->m_global->getDataById("master.t_mtr_user_pos","terminal_id=$id")->num_rows();

		if($checkUserPos>0)
		{
			echo $res=$this->msg_error('Cannot delete, device already paired to user');
		}
		else
		{
			$delete=$this->m_global->update("master.t_mtr_device_terminal",$data,"id_seq=$id");
			if ($delete)
			{
				echo $res=$this->msg_success('Success delete data');
			}
			else
			{
				echo $res=$this->msg_error('Failed delete data');
			}
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/pos/delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function disable($id)
	{
		$id = decode($id);
		$data = array(
			'status' => -1,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date('Y-m-d H:i:s'),
		);

		$disabled = $this->m_global->masterDisable('master.t_mtr_device_terminal',$id,$data);

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

		$disabled = $this->m_global->masterDisable('master.t_mtr_device_terminal',$id,$data);

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