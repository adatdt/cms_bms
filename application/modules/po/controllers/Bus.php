<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bus extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_bus');
		$this->load->model('m_global');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Master Bus";
		$data['content'] = "po/bus/index";
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['type']=$this->m_global->getData("master.t_mtr_bus_type","where status=1 order by type asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_bus->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['title'] = "Add Bus";
		$data['po']=$this->m_global->getdata("master.t_mtr_po","where status=1 order by po_name asc");
		$data['type']=$this->m_global->getdata("master.t_mtr_bus_type","where status=1 order by type asc");
		// $data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/bus/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$busName=trim($this->input->post('busName'));
		$plateNumber=strtoupper(str_replace(' ', '',$this->input->post('plateNumber')));
		$po=trim($this->input->post('po'));
		$type=trim($this->input->post('type'));
		$seat=trim($this->input->post('seat'));

		$data=array(
			'po_id'=>$po,
			'bus_type_id'=>$type,
			'bus_name'=>$busName,
			'plate_number'=>$plateNumber,
			'total_seat'=>$seat,
			'status'=>1,
			'created_by'=>$this->session->userdata('username'),
			'created_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('busName', 'Bus Name', 'required');
		$this->form_validation->set_rules('plateNumber', 'Plate Number', 'required');
		$this->form_validation->set_rules('po', 'PO Bus', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('seat', 'Seat', 'required');

		$checkPlate=$this->m_global->getDataById('master.t_mtr_bus',"plate_number='".$plateNumber."' and status=1")->num_rows();

		if ($checkPlate>0)
		{
			echo $rest=$this->msg_error('Plate number already exist');
		}

		else if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }

        else
        {
	       $insert=$this->m_global->insert("master.t_mtr_bus",$data);

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
        $logUrl      = site_url().'po/bus/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	function edit($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$id = decode($id);
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['type']=$this->m_global->getData("master.t_mtr_bus_type","where status=1 order by type asc");
		$data['bus']=$this->m_global->getDataById("master.t_mtr_bus","id_seq=$id")->row();
		$data['title'] = "Edit Bus";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));

		$getBus=$this->m_global->getDataById("master.t_mtr_bus","id_seq=$id")->row();

		//pengecekan jika pernah transaksi
		$getTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","upper(plate_number)=upper('".$getBus->plate_number."') ")->num_rows();

		if($getTapIn>0)
		{
			$this->load->view("po/bus/edit2",$data);
		}
		else
		{
			$this->load->view("po/bus/edit",$data);
		}

	}
	function action_edit()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$busName=$this->input->post('busName');
		$id=$this->input->post('id');
		$plateNumber=strtoupper(str_replace(' ', '',$this->input->post('plateNumber')));
		$po=$this->input->post('po');
		$type=$this->input->post('type');
		$seat=$this->input->post('seat');

		$data=array(
			'po_id'=>$po,
			'bus_type_id'=>$type,
			'bus_name'=>$busName,
			'plate_number'=>$plateNumber,
			'total_seat'=>$seat,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('busName', 'Bus Name', 'required');
		$this->form_validation->set_rules('plateNumber', 'Plate Number', 'required');
		$this->form_validation->set_rules('po', 'PO Bus', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('seat', 'Seat', 'required');

		$checkPlate=$this->m_global->getDataById('master.t_mtr_bus',"upper(plate_number)=upper('".$plateNumber."') and id_seq !=$id and status=1")->num_rows();

		$checkTapOut=$this->m_global->getDataById('trx.t_trx_tap_out',"upper(plate_number)=upper('".$plateNumber."') ")->num_rows();

		$checkTapIn=$this->m_global->getDataById('trx.t_trx_tap_in',"upper(plate_number)=upper('".$plateNumber."') ")->num_rows();

		if ($checkPlate>0)
		{
			echo $rest=$this->msg_error('Plate number already exist');
		}
		else if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else if ($checkTapIn>0 or $checkTapOut>0)
        {
            echo $rest=$this->msg_error('Failed edit data, bus in transaction');
        }
        else
        {
        	$data2=array(
        		'po_id'=>$po,
        		'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date('Y-m-d H:i:s'),
        	);

        	$this->db->trans_begin();
        	$this->m_bus->update('master.t_mtr_bus',$data,"id_seq=$id");
        	$this->m_bus->update('master.t_mtr_driver',$data2,"bus_id=$id");

        	if ($this->db->trans_status() === FALSE)
	        {
	            $this->db->trans_rollback();
	            echo $rest=$this->msg_error('failed edit data');
	            
	        }
	        else
	        {
	            $this->db->trans_commit();
	            echo $rest=$this->msg_success('Success edit data');
	           
	        }
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/bus/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);				
	}
	
	function action_edit2()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$busName=$this->input->post('busName');
		$id=$this->input->post('id');
		$seat=$this->input->post('seat');

		$data=array(
			'bus_name'=>$busName,
			'total_seat'=>$seat,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('busName', 'Bus Name', 'required');
		$this->form_validation->set_rules('seat', 'Seat', 'required');

		$getBus=$this->m_global->getDataById("master.t_mtr_bus","id_seq=$id")->row();
		$checkTrx=$this->m_global->getDataById('trx.t_trx_tap_out',"upper(plate_number)=upper('".$getBus->plate_number."') and status=1 ")->num_rows();

		$checkTrx2=$this->m_global->getDataById('trx.t_trx_tap_in',"upper(plate_number)=upper('".$getBus->plate_number."') and status=1 ")->num_rows();

		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else if ($checkTrx>0 or $checkTrx2>0 )
        {
            echo $rest=$this->msg_error('Failed edit data, bus in transaction');
        }
        else
        {

        	$this->db->trans_begin();
        	$this->m_bus->update('master.t_mtr_bus',$data,"id_seq=$id");

        	if ($this->db->trans_status() === FALSE)
	        {
	            $this->db->trans_rollback();
	            echo $rest=$this->msg_error('failed edit data');
	            
	        }
	        else
	        {
	            $this->db->trans_commit();
	            echo $rest=$this->msg_success('Success edit data');
	           
	        }
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/bus/action_edit';
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

		$getBus=$this->m_global->getDataById("master.t_mtr_bus","id_seq=$id")->row();

		$checkTrx=$this->m_global->getDataById('trx.t_trx_tap_out',"upper(plate_number)=upper('".$getBus->plate_number."') ")->num_rows();

		$checkTrxIn=$this->m_global->getDataById('trx.t_trx_tap_in',"upper(plate_number)=upper('".$getBus->plate_number."') ")->num_rows();

		$checkDriver=$this->m_global->getDataById('master.t_mtr_driver',"bus_id=".$getBus->id_seq." and status=1")->num_rows();

		if($checkTrx>0 or $checkTrxIn>0 )
		{
			echo $rest=$this->msg_error('Cannot delete, bus in transaction');
		}
		else if($checkDriver>0)
		{
			echo $rest=$this->msg_error('Cannot delete, bus already paired to driver ');
		}

		else
		{
			$delete=$this->m_global->update("master.t_mtr_bus",$data,"id_seq=$id");
			if ($delete)
			{
				echo $rest=$this->msg_success('Success delete data');
			}
			else
			{
				echo $rest=$this->msg_error('Failed delete data');
			}
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/bus/delete';
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

		$disabled = $this->m_global->masterDisable('master.t_mtr_bus',$id,$data);

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

		$disabled = $this->m_global->masterDisable('master.t_mtr_bus',$id,$data);

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