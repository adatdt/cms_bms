<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manless_gate extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_manlessgate');
		$this->load->library('bcrypt');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Manless Shelter";
		$data['content'] = "device/manless_gate/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_manlessgate->getData();
		echo json_encode($list);
	}

	public function getlane()
	{
		$shelter_id=decode($this->input->post("shelter_id"));

		$getData=$this->m_global->getData("master.t_mtr_lane","where shelter_id=$shelter_id and status=1 order by lane_name asc");

		$x=array();
		foreach ($getData as $key => $value) {
			
			$x['id_seq']=encode($value->id_seq);
			$x['lane_name']=$value->lane_name;

			$data[]=$x;
		}
		echo json_encode($data);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['title'] = "Add Manless Shelter";
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$this->load->view("device/manless_gate/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$name=trim($this->input->post('name'));
		$username=trim($this->input->post('username'));
		$password=trim($this->input->post('password'));
		$shelter=decode($this->input->post('shelter'));
		$airport=decode($this->input->post('airport'));
		$lane=decode($this->input->post('lane'));

		$this->form_validation->set_rules('name','Name','required');
		$this->form_validation->set_rules('password','Password','required');
		$this->form_validation->set_rules('shelter','Shelter','required');
		$this->form_validation->set_rules('airport','Airport','required');
		$this->form_validation->set_rules('lane','Lane','required');

		$dataCore=array(
			'username'=>$username,
			'user_group_id'=>7, // hard cord user group denga manles shelter =7
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

			$user_id=$this->m_manlessgate->insert("core.t_mtr_user",$dataCore);

			// cek kode jika kode bukan belasan 
			if(strlen($shelter)<=1)
			{
				$code="050".$shelter; //hardcord code kiosk di device terminal type =5
			}
			else
			{
				$code="05".$shelter;
			}

			$deviceCode=$this->m_manlessgate->generateCode($code);

			$dataTerminalDevice=array(
				'terminal_code'=>$deviceCode,
				'shelter_id'=>$shelter,
				'airport_id'=>$airport,
				'terminal_name'=>$name,
				'terminal_type_id'=>5, //hardcord terminal type id
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);

			$terminal_id=$this->m_manlessgate->insert("master.t_mtr_device_terminal",$dataTerminalDevice);

			$dataMaster=array(
				'user_id'=>$user_id,
				'shelter_id'=>$shelter,
				'terminal_id'=>$terminal_id, 
				'lane_id'=>$lane,
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);
			$this->m_manlessgate->insert("master.t_mtr_user_manless_gate",$dataMaster);

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
        $logUrl      = site_url().'device/manless_gate/action_add';
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

		$getManlessGate=$this->m_global->getDataById("master.t_mtr_user_manless_gate","user_id=$id")->row();

		$data['id'] = encode($id);
		$data['title'] = "Edit Manless Shelter";
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['lane']=$this->m_global->getData("master.t_mtr_lane","where status=1 and shelter_id=".$getManlessGate->shelter_id." order by lane_name asc");
		$data['detail']=$this->m_manlessgate->detail($id);
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$this->load->view("device/manless_gate/edit",$data);

		// echo print_r();
	}

	function action_edit()
	{
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$name=trim($this->input->post('name'));
		$shelter=decode($this->input->post('shelter'));
		$lane=decode($this->input->post('lane'));
		$user_id=decode($this->input->post('id'));

		$this->form_validation->set_rules('name','Name','required');

		//untuk mengammbil data usernya
		$dataUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=$user_id")->row();

		//check datanya jika ada yang pernah transaksi
		$checkCheckIn=$this->m_global->getDataById("trx.t_trx_check_in","upper(created_by)=upper('".$dataUser->username."')")->num_rows();
		$checkCheckOut=$this->m_global->getDataById("trx.t_trx_check_out"," upper(created_by)=upper('".$dataUser->username."')")->num_rows();
		$checkCheckExit=$this->m_global->getDataById("trx.t_trx_check_exit"," upper(created_by)=upper('".$dataUser->username."')")->num_rows();

		// cek kode jika kode bukan belasan 
		if(strlen($shelter)<=1)
		{
			$code="050".$shelter; //hardcord code kiosk di device terminal type =5
		}
		else
		{
			$code="05".$shelter;
		}

		$deviceCode=$this->m_manlessgate->generateCode($code);

		$dataCore=array('first_name'=>$name,
						'updated_by'=>$this->session->userdata('username'),
						'updated_on'=>date("Y-m-d H:i:s"),
						);

		if($this->form_validation->run() == FALSE)
		{
			echo $res=$this->msg_error('Please input the field');

			$data=$dataCore;
		}
		else if($checkCheckIn>0 or $checkCheckOut>0 or $checkCheckExit>0)
		{
			echo $res=$this->msg_error('Cannot edit, user in transaction');

			$data=$dataCore;	
		}
		else
		{
			$this->db->trans_begin();
			$this->m_manlessgate->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

			$dataMaster=array(
				'shelter_id'=>$shelter,
				'lane_id'=>$lane,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date("Y-m-d H:i:s"),
				);

			$this->m_manlessgate->update("master.t_mtr_user_manless_gate",$dataMaster,"user_id=$user_id");

			$getDeviceTerminal=$this->m_global->getDataById("master.t_mtr_user_manless_gate","user_id=$user_id")->row();

			if($getDeviceTerminal==$shelter)
			{
				$dataTerminalDevice=array('terminal_name'=>$name,
				'shelter_id'=>$shelter,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date("Y-m-d H:i:s"),
				);	
			}
			else
			{
				$dataTerminalDevice=array('terminal_name'=>$name,
				'shelter_id'=>$shelter,
				'terminal_code'=>$deviceCode,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date("Y-m-d H:i:s"),
				);
			}

			$this->m_manlessgate->update("master.t_mtr_device_terminal",$dataTerminalDevice,"id_seq=".$getDeviceTerminal->terminal_id);

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

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/kiosk/action_edit';
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
		$this->m_manlessgate->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

		$dataMaster=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);
		$this->m_manlessgate->update("master.t_mtr_user_manless_gate",$dataMaster,"user_id=$user_id");

		$getDeviceTerminal=$this->m_global->getDataById("master.t_mtr_user_manless_gate","user_id=$user_id")->row();

		$dataTerminalDevice=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);

		$this->m_manlessgate->update("master.t_mtr_device_terminal",$dataTerminalDevice,"id_seq=".$getDeviceTerminal->terminal_id);

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
        $logUrl      = site_url().'device/manless_gate/update_password';
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
				'status'=>-1, // -1 enable data
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
		$this->m_manlessgate->update("core.t_mtr_user",$data,"id_seq=$id");
		$this->m_manlessgate->update("master.t_mtr_user_manless_gate",$data,"user_id=$id");
		$this->m_manlessgate->update("master.t_mtr_device_terminal",$data,"id_seq=".$getUsermalessGate->terminal_id." ");

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