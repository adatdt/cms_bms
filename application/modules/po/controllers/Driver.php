<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Driver extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_driver');
		$this->load->model('m_global');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Master Driver";
		$data['content'] = "po/driver/index";
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);

	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_driver->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Driver";
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['bus']=$this->m_global->getData("master.t_mtr_bus","where status=1 order by bus_name asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/driver/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/driver','add');
		$uid=strtoupper(str_replace(' ', '',$this->input->post('uid')));
		$driver=trim($this->input->post('driver'));
		$busId=decode($this->input->post('bus'));
		$poId=$this->input->post('poId');
		$phone=trim($this->input->post('phone'));		

		if(substr($phone,0,2)=='62')
		{
			$phoneNo="0".substr($phone,2);
		}

		else if(substr($phone,0,3)=='+62')
		{
			$phoneNo="0".substr($phone,3);
		}
		else
		{
			$phoneNo=$phone;	
		}

		$data=array(
			'uid'=>$uid,
			'po_id'=>$poId,
			'bus_id'=>$busId,
			'driver_name'=>$driver,
			'driver_phone'=>$phoneNo,
			'status'=>1,
			'created_by'=>$this->session->userdata('username'),
		);

		$this->form_validation->set_rules('uid', 'UID', 'required');
		$this->form_validation->set_rules('driver', 'Driver', 'required');
		$this->form_validation->set_rules('bus', 'Bus', 'required');
		$this->form_validation->set_rules('poId', 'PO', 'required');
		$this->form_validation->set_rules('phone', 'Phone', 'required');

		$checkUid=$this->m_global->getDataById("master.t_mtr_driver","upper(uid)=upper('".$uid."') and status =1 ")->num_rows();

		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else if($checkUid>0)
        {
        	echo $rest=$this->msg_error('Uid already in use');	
        }
        else if(!is_numeric($phone))
        {
        	echo $rest=$this->msg_error('Phone number must be numerik !');
        }
        else
        {
        	$insert=$this->m_global->insert('master.t_mtr_driver',$data);
        	if($insert)
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
        $logUrl      = site_url().'po/driver/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	function edit($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/driver','edit');
		$id = decode($id);
		$data['title'] = "Edit Driver";
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['bus']=$this->m_global->getData("master.t_mtr_bus","where status=1 order by bus_name asc");
		$data['detail']=$this->m_driver->getDetail($id);
		// $data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));

		$getDriver=$this->m_global->getDataById("master.t_mtr_driver","id_seq=$id")->row();

		// cek jika sudah pernah transaksi
		$getTrx=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$getDriver->uid."')")->num_rows();
		$getTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","upper(uid)=upper('".$getDriver->uid."')")->num_rows();	
		
		if($getTrx >0 or $getTapIn>0)
		{
			$this->load->view("po/driver/edit2",$data);
		}
		else
		{
			$this->load->view("po/driver/edit",$data);
		}
		
	}

	function action_edit()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/driver','edit');
		// $uid=strtoupper(str_replace(' ', '',$this->input->post('uid')));
		$id=decode($this->input->post('id'));
		$driver=trim($this->input->post('driver'));
		$busId=decode($this->input->post('bus'));
		$poId=$this->input->post('poId');
		$phone=trim($this->input->post('phone'));		

		if(substr($phone,0,2)=='62')
		{
			$phoneNo="0".substr($phone,2);
		}

		else if(substr($phone,0,3)=='+62')
		{
			$phoneNo="0".substr($phone,3);
		}
		else
		{
			$phoneNo=$phone;	
		}

		$data=array(
			'po_id'=>$poId,
			'bus_id'=>$busId,
			'driver_name'=>$driver,
			'driver_phone'=>$phoneNo,
			'updated_on'=>date("Y-m-d H:i:s"),
			'updated_by'=>$this->session->userdata('username'),
		);

		// $this->form_validation->set_rules('uid', 'UID', 'required');
		$this->form_validation->set_rules('driver', 'Driver', 'required');
		$this->form_validation->set_rules('bus', 'Bus', 'required');
		$this->form_validation->set_rules('poId', 'PO', 'required');
		$this->form_validation->set_rules('phone', 'Phone', 'required');

		// $checkUid=$this->m_global->getDataById("master.t_mtr_driver","upper(uid)=upper('".$uid."') and id_seq !=$id and status=1 ")->num_rows();

		$getDriver=$this->m_global->getDataById("master.t_mtr_driver","id_seq='".$id."' and status=1")->row();

		$checkTrx=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$getDriver->uid."') ")->num_rows();
		$getTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","upper(uid)=upper('".$getDriver->uid."')")->num_rows();

		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        // else if($checkUid>0)
        // {
        // 	echo $rest=$this->msg_error('Uid already in use');	
        // }
        else if(!is_numeric($phone))
        {
        	echo $rest=$this->msg_error('Phone number must be numerik !');
        }
        else if ($checkTrx>0 or $getTapIn>0)
        {
        	echo $rest=$this->msg_error('Failed edit driver, driver in transaction');	
        }
        else
        {
        	$update=$this->m_global->update('master.t_mtr_driver',$data,"id_seq=$id");
        	if($update)
        	{
        		echo $rest=$this->msg_success('Success edit data');
        	}
        	else
        	{
        		echo $rest=$this->msg_error('Failed edit data');
        	}

        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/driver/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);			
	}

	function action_edit2()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/driver','edit');
		// $uid=strtoupper(str_replace(' ', '',$this->input->post('uid')));
		$id=decode($this->input->post('id'));
		// $driver=trim($this->input->post('driver'));
		$phone=trim($this->input->post('phone'));		

		if(substr($phone,0,2)=='62')
		{
			$phoneNo="0".substr($phone,2);
		}

		else if(substr($phone,0,3)=='+62')
		{
			$phoneNo="0".substr($phone,3);
		}
		else
		{
			$phoneNo=$phone;	
		}

		$data=array(
			'driver_phone'=>$phoneNo,
			'updated_on'=>date("Y-m-d H:i:s"),
			'updated_by'=>$this->session->userdata('username'),
		);

		$this->form_validation->set_rules('phone', 'Phone', 'required');


		$getDriver=$this->m_global->getDataById("master.t_mtr_driver","id_seq='".$id."' and status=1")->row();

		// cek jika sedang melakukan trasangki
		$checkTrx=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$getDriver->uid."') and status=1")->num_rows();
		$getTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","upper(uid)=upper('".$getDriver->uid."') and status=1")->num_rows();

		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        
        else if(!is_numeric($phone))
        {
        	echo $rest=$this->msg_error('Phone number must be numerik !');
        }
        else if ($checkTrx>0 or $getTapIn>0)
        {
        	echo $rest=$this->msg_error('Failed edit driver, driver in transaction');	
        }
        else
        {
        	$update=$this->m_global->update('master.t_mtr_driver',$data,"id_seq=$id");
        	if($update)
        	{
        		echo $rest=$this->msg_success('Success edit data');
        	}
        	else
        	{
        		echo $rest=$this->msg_error('Failed edit data');
        	}

        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/driver/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);			
	}
	
	public function delete($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/driver','delete');
		$id = decode($id);

		$data=array(
			'status'=>-5,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$getData=$this->m_global->getDataById("master.t_mtr_driver","id_seq=$id ")->row();

		// cek data jika sudah pernah transaksi
		$checkData=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$getData->uid."') ")->num_rows();
		$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","upper(uid)=upper('".$getData->uid."') ")->num_rows();

		if($checkData>0 or $checkTapIn>0)
		{
			echo $rest=$this->msg_error('Failed delete data, driver in transaction');
		}
		else
		{
	    	$delete=$this->m_global->update("master.t_mtr_driver",$data,"id_seq=$id");
			if ($delete)
			{
				echo $rest=$this->msg_success('Success delete data');
			}
			else
			{
				echo $rest=$this->msg_error('Failed delete data');
			}
		}

		// create log
		$createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/driver/delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function disableData($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/driver','delete');
		$id = decode($id);

		$data=array(
			'status'=>-1,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$getData=$this->m_global->getDataById("master.t_mtr_driver","id_seq=$id")->row();

		// cek data sedang transaksi
		$checkData=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$getData->uid."') and status=1")->num_rows();
		$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","upper(uid)=upper('".$getData->uid."') and status=1 ")->num_rows();

		if($checkData>0 or $checkTapIn>0)
		{
			echo $res=$this->msg_error("Cannot disable, driver in transaction");
		}
		else
		{
			$delete=$this->m_global->update("master.t_mtr_driver",$data,"id_seq=$id");
			if ($delete)
			{
				echo $res=$this->msg_success('Success disable data');
			}
			else
			{
				echo $res=$this->msg_error('Failed disable data');
			}	
		}

		// create log
		$createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/driver/disableData';
        $logMethod   = 'DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

	}


	public function enableData($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/driver','delete');
		$id = decode($id);

		$data=array(
			'status'=>1,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$getData=$this->m_global->getDataById("master.t_mtr_driver","id_seq=$id")->row();

		// cek data sedang transaksi
		$checkData=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$getData->uid."') and status=1")->num_rows();
		$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","upper(uid)=upper('".$getData->uid."') and status=1 ")->num_rows();

		// check jika ada uid yang sama di driver
		$checkUid=$this->m_global->getDataById("master.t_mtr_driver","upper(uid)=upper('".$getData->uid."') and status=1 ")->num_rows();

		// if($checkData>0 or $checkTapIn>0)
		// {
		// 	echo $res=$this->msg_error("Cannot disable, driver in transaction");
		// }
		if($checkUid>0)
		{
			echo $res=$this->msg_error("Cannot enable, UID already active");	
		}
		else
		{
			$active=$this->m_global->update("master.t_mtr_driver",$data,"id_seq=$id");
			if ($active)
			{
				echo $res=$this->msg_success('Success enable data');
			}
			else
			{
				echo $res=$this->msg_error('Failed disable data');
			}	
		}

		// create log
		$createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/driver/enableData';
        $logMethod   = 'ENABLE';
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

	
	function getPo()
	{
		$poId=decode($this->input->post('id'));

		if(empty($poId))
		{
			$id=0;
		}
		else
		{
			$id=$poId;			
		}

		$data=$this->m_driver->getPo($id);
		echo json_encode($data);
	}

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */