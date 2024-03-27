<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Validator extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_validator');
		$this->load->model('m_global');
		$this->load->library('bcrypt');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Validator";
		$data['content'] = "device/validator/index";
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_validator->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Validator";
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("device/validator/add",$data);
	}

	public function action_add()
	{
		$name=trim($this->input->post('name'));
		$username=trim($this->input->post('username'));
		$password=trim($this->input->post('password'));
		$imei=trim($this->input->post('imei'));
		// $airport=decode($this->input->post('airport'));
		$po=decode($this->input->post('po'));
		$shelter=decode($this->input->post('shelter'));

		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('imei', 'Imei', 'required');
		// $this->form_validation->set_rules('airport', 'Airport', 'required');
		$this->form_validation->set_rules('po', 'PO', 'required');
		$this->form_validation->set_rules('shelter', 'Shleter', 'required');

		$checkUsername=$this->m_global->getDataById("core.t_mtr_user","upper(username)=upper('".$username."')")->num_rows();

		$dataCore=array(
				'user_group_id'=>5, // 5 untuk validator
				'username'=>$username,
				'password'=>$this->bcrypt->hash(strtoupper(md5($password))),
				'first_name'=>$name,
				'status'=>1,
				'created_on'=>date("Y-m-d H:i:s"),
				'created_by'=>$this->session->userdata('username'),
			);

		if($this->form_validation->run()==false)
		{
			echo $res=$this->msg_error('Please input the field!');

			$data=$dataCore;
		}
		else if($checkUsername>0)
		{
			echo $res=$this->msg_error('Username already in use');
			$data=$dataCore;
		}
		else
		{
			$this->db->trans_begin();

			$user_id=$this->m_validator->insert("core.t_mtr_user",$dataCore);

			$dataMaster=array(
				'user_id'=>$user_id,
				'po_id'=>$po,
				'shelter_id'=>$shelter,
				'imei'=>$imei,
				'status'=>1,
				'created_on'=>date("Y-m-d H:i:s"),
				'created_by'=>$this->session->userdata('username'),
			);

			$this->m_validator->insert("master.t_mtr_user_validator",$dataMaster);

			if($this->db->trans_status() === FALSE) {
	            $this->db->trans_rollback();
	            echo $res=$this->msg_error("Failed add data");
	        } 
	        else 
	        {
	           $this->db->trans_commit();
	           echo $res=$this->msg_success("Success add data");
	        }

	        $data=array($dataCore,$dataMaster);

    	}

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/validator/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$data['id']=encode($id);
		$data['detail']=$this->m_validator->getDetail($id)->row();
		$data['title'] = "Edit Validator";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("device/validator/edit",$data);
	}

	function action_edit()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'device/validator','edit');
		$name=trim($this->input->post('name'));
		$imei=trim($this->input->post('imei'));
		// $airport=decode($this->input->post('airport'));
		$po=decode($this->input->post('po'));
		$shelter=decode($this->input->post('shelter'));
		$id=decode($this->input->post('id'));

		$dataCore=array(
			'first_name'=>$name,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('name', 'name', 'required');
		$this->form_validation->set_rules('id', 'id', 'required');
		$this->form_validation->set_rules('imei', 'Imei', 'required');
		$this->form_validation->set_rules('po', 'PO', 'required');
		$this->form_validation->set_rules('shelter', 'Shelter', 'required');

		$getUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=".$id)->row();

		// check user apakah user sudah pernah melakukan transaksi
		$checkPayment=$this->m_global->getDataById("trx.t_trx_payment","upper(created_by)=upper('".$getUser->username."') ")->num_rows();

		if($this->form_validation->run()==false)
		{
			echo $res=$this->msg_error("Please input the field!");
			$data=$dataCore;
		}
		else if ($checkPayment>0)
		{
			echo $res=$this->msg_error("Cannot update, user in transaction");
			$data=$dataCore;	
		}
		else
		{
			$this->db->trans_begin();
			$this->m_validator->update("core.t_mtr_user",$dataCore,"id_seq=".$id);

			$dataMaster=array(
							'po_id'=>$po,
							'shelter_id'=>$shelter,
							'po_id'=>$po,
							'imei'=>$imei,
							'updated_by'=>$this->session->userdata('username'),
							'updated_on'=>date("Y-m-d H:i:s"),
						);

			$this->m_validator->update("master.t_mtr_user_validator",$dataMaster,"user_id=".$id);

			if ($this->db->trans_status() === FALSE)
	        {
	                $this->db->trans_rollback();
	                echo $res=$this->msg_error("Failed edit data");
	        }
	        else
	        {
	                $this->db->trans_commit();
	                echo $res=$this->msg_success("Success edit data");
	        }

	        $data=array($dataCore,$dataMaster);
		}

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/bus/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);				
	}
	
	// public function delete($id)
	// {
	// 	validateAjax();
	// 	$id = decode($id);

	// 	$data=array(
	// 		'status'=>-1, // - satu artinya disable
	// 		'updated_by'=>$this->session->userdata('username'),
	// 		'updated_on'=>date('Y-m-d H:i:s'),
	// 	);

 //    	$delete=$this->m_global->update("master.t_mtr_bus",$data,"id_seq=$id");
	// 	if ($delete)
	// 	{
	// 		echo $rest=$this->msg_success('Success delete data');
	// 	}
	// 	else
	// 	{
	// 		echo $rest=$this->msg_error('Failed delete data');
	// 	}

	// 	/* Fungsi Create Log */
 //        $createdBy   = $this->session->userdata('full_name');
 //        $logUrl      = site_url().'po/bus/delete';
 //        $logMethod   = 'DELETE';
 //        $logParam    = json_encode($data);
 //        $logResponse = $rest;

 //        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);					
	// }

	public function delete($id,$msg)
	{
		validateAjax();
		$id = decode($id);

		$getUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=$id")->row();
		$getValidator=$this->m_global->getDataById("master.t_mtr_user_validator","user_id=$id")->row();

		if(strtoupper($msg)==strtoupper('disable'))
		{
			$data=array(
				'updated_on'=>date("Y-m-d H:i:s"),
				'created_by'=>$this->session->userdata('username'),
				'status'=>-1,
			);
		}
		else
		{
			$data=array(
				'updated_on'=>date("Y-m-d H:i:s"),
				'created_by'=>$this->session->userdata('username'),
				'status'=>1,
			);
		}

		$this->db->trans_begin();
		$this->m_validator->update("core.t_mtr_user",$data,"id_seq=$id");
		$this->m_validator->update("master.t_mtr_user_validator",$data,"user_id=$id");

		if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=$this->msg_error("Failed ".$msg." user");
        }
        else
        {
            $this->db->trans_commit();
            echo $res=$this->msg_success("Success ".$msg." user");
        }			

	}

	public function update_password($id_seq)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'change_password');
		$user_id = decode($id_seq);
		$password = $this->bcrypt->hash(strtoupper(md5('admin123')));

		$dataCore = array(
			'password' => $password,
			'updated_by' => $this->session->userdata("username"),
			'updated_on' => date("Y-m-d H:i:s"),
		);

		$this->db->trans_begin();
		$this->m_validator->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

		$dataMaster=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);

		$this->m_validator->update("master.t_mtr_user_validator",$dataMaster,"user_id=$user_id");

		if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=$this->msg_error("Failed reset password");
        }
        else
        {
            $this->db->trans_commit();
            echo $res=$this->msg_success("Success reset password");
        }			

        $data=array($dataCore,$dataMaster);

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/manless_gate/update_password';
        $logMethod   = 'RESET PASSWORD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
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