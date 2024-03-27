<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manless_pengendapan extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_manless_pengendapan');
		$this->load->library('bcrypt');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Manless Pengendapan";
		$data['content'] = "device/manless_pengendapan/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_manless_pengendapan->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['title'] = "Add Manless Pengendapan";
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$this->load->view("device/manless_pengendapan/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$name=trim($this->input->post('name'));
		$username=trim($this->input->post('username'));
		$password=trim($this->input->post('password'));		
		$airport=decode($this->input->post('airport'));

		$this->form_validation->set_rules('name','Name','required');
		$this->form_validation->set_rules('password','Password','required');
		$this->form_validation->set_rules('airport','Airport','required');

		$dataCore=array(
			'username'=>$username,
			'user_group_id'=>8, // hard code user group denga manles pengendapan =8
			'password'=>$this->bcrypt->hash(strtoupper(md5($password))),
			'first_name'=>$name,
			'status'=>1,
			'created_by'=>$this->session->userdata('username'),
			'created_on'=>date('Y-m-d H:i:s'),
			);

		$checkUsername=$this->m_global->getDataById("core.t_mtr_user","upper(username)=upper('".$username."')")->num_rows();

		if($this->form_validation->run() == FALSE)
		{
			echo $res=$this->msg_error('Please input the field');
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

			$user_id=$this->m_manless_pengendapan->insert("core.t_mtr_user",$dataCore);

			$shelter="00"; //hardcode code shelter untuk manless pengendapan
			$code="07".$shelter; //hardcode code manless pengendapan device_terminal_type
			$deviceCode=$this->m_manless_pengendapan->generateCode($code);
		
			$dataTerminalDevice=array(				
				'terminal_code'=>$deviceCode,
				'airport_id'=>$airport,
				'terminal_name'=>$name,
				'terminal_type_id'=>7, //hardcode terminal type id
				'shelter_id'=>0,
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);

			$terminal_id=$this->m_manless_pengendapan->insert("master.t_mtr_device_terminal",$dataTerminalDevice);

			$dataMaster=array(
				'user_id'=>$user_id,
				'terminal_id'=>$terminal_id, 
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);
			$this->m_manless_pengendapan->insert("master.t_mtr_user_manless_gate",$dataMaster);

			if($this->db->trans_status() === FALSE) {
            	$this->db->trans_rollback();

            	echo $res=$this->msg_error("Failed add data");
	        } 
	        else 
	        {
	            $this->db->trans_commit();
	            echo $res=$this->msg_Success("Success add data");
	        }

	        $data=array($dataCore,$dataTerminalDevice,$dataMaster);
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/manless_pengendapan/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

	}

	function edit($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$id = decode($id);
		$data['id'] = encode($id);
		$data['title'] = "Edit Manless Pengendapan";		
		$data['detail']=$this->m_manless_pengendapan->detail($id);
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$this->load->view("device/manless_pengendapan/edit",$data);

		// echo print_r();
	}

	function action_edit()
	{
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$name=trim($this->input->post('name'));
		$user_id=decode($this->input->post('id'));
		$username=trim($this->input->post('username'));	
		$airport=decode($this->input->post('airport'));

		$this->form_validation->set_rules('name','Name','required');
		$this->form_validation->set_rules('username','Username','required');
		$this->form_validation->set_rules('airport','Airport','required');


		$dataCore=array('first_name'=>$name,
						'username'=>$username,
						'updated_by'=>$this->session->userdata('username'),
						'updated_on'=>date("Y-m-d H:i:s"),
						);

		if($this->form_validation->run() == FALSE)
		{
			echo $res=$this->msg_error('Please input the field');

			$data=$dataCore;
		}
		else
		{
			$checkTapIn = $this->db->query("SELECT a.*, b.id_seq, b.username FROM trx.t_trx_tap_in a JOIN core.t_mtr_user b ON a.created_by=b.username where b.id_seq = '".$user_id."'")->num_rows();

			if($checkTapIn > 0)
			{
				echo $res=$this->msg_error("Cannot update, user in transaction");
				$data=$dataCore;
			}
			else
			{
				$getUsernameOld=$this->m_global->getDataById("core.t_mtr_user","id_seq=$user_id")->row();

				//check username exist with current and old username
				$checkUsername=$this->m_global->getDataById("core.t_mtr_user","upper(username)=upper('".$username."') and upper(username)<>upper('".$getUsernameOld->username."')")->num_rows();
				if($checkUsername > 0)
				{
					echo $res=$this->msg_error('Username already in use');
					$data=$dataCore;
				}
				else
				{

					$this->db->trans_begin();
					$this->m_manless_pengendapan->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

					$getDeviceTerminal=$this->m_global->getDataById("master.t_mtr_user_manless_gate","user_id=$user_id")->row();

					$dataMaster=array(
						'user_id'=>$user_id,
						'terminal_id'=>$getDeviceTerminal->terminal_id, 
						'updated_by'=>$this->session->userdata('username'),
						'updated_on'=>date("Y-m-d H:i:s"),
						);

					$this->m_manless_pengendapan->update("master.t_mtr_user_manless_gate",$dataMaster,"user_id=$user_id");				

					$dataTerminalDevice=array(
						'airport_id'=>$airport,
						'terminal_name'=>$name,		
						'updated_by'=>$this->session->userdata('username'),
						'updated_on'=>date("Y-m-d H:i:s"),
						);

					$this->m_manless_pengendapan->update("master.t_mtr_device_terminal",$dataTerminalDevice,"id_seq=".$getDeviceTerminal->terminal_id);

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

			        $data=array($dataCore,$dataTerminalDevice,$dataMaster);
			    }
		    }
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/manless_pengendapan/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

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
		$this->m_manless_pengendapan->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

		$dataMaster=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);
		$this->m_manless_pengendapan->update("master.t_mtr_user_manless_gate",$dataMaster,"user_id=$user_id");

		$getDeviceTerminal=$this->m_global->getDataById("master.t_mtr_user_manless_gate","user_id=$user_id")->row();

		$dataTerminalDevice=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);

		$this->m_manless_pengendapan->update("master.t_mtr_device_terminal",$dataTerminalDevice,"id_seq=".$getDeviceTerminal->terminal_id);

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

        $data=array($dataCore,$dataTerminalDevice,$dataMaster);

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/manless_pengendapan/update_password';
        $logMethod   = 'RESET PASSWORD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function delete($id,$msg)
	{
		validateAjax();
		$id = decode($id);

		$getUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=$id")->row();
		$getUsermalessGate=$this->m_global->getDataById("master.t_mtr_user_manless_gate","user_id=$id")->row();

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
		$this->m_manless_pengendapan->update("core.t_mtr_user",$data,"id_seq=$id");
		$this->m_manless_pengendapan->update("master.t_mtr_user_manless_gate",$data,"user_id=$id");
		$this->m_manless_pengendapan->update("master.t_mtr_device_terminal",$data,"id_seq=".$getUsermalessGate->terminal_id." ");

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