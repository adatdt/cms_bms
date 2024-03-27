<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shelter extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_shelter');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Shelter";
		$data['content'] = "airport/shelter/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_shelter->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Shelter";
		$data['airport'] = $this->m_shelter->get_master('master.t_mtr_airport');
		$this->load->view("airport/shelter/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$airport_id=decode($this->input->post('airport_id'));
		$shelter_name=$this->input->post('shelter_name');

		$this->form_validation->set_rules('airport_id', 'Airport', 'required');
		$this->form_validation->set_rules('shelter_name', 'Name', 'required');

		$data=array(
			'airport_id'=>$airport_id,
			'shelter_code'=>$this->createShelter(),
			'shelter_name'=>$shelter_name,
			'created_by'=>$this->session->userdata('username'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field');
        }
        else
        {
        	$this->db->trans_begin();
        	$idShelter=$this->m_shelter->insert("master.t_mtr_shelter",$data);

        	$selectPo=$this->m_global->getData("master.t_mtr_po","where status=1");
        	foreach ($selectPo as $key => $value) {
	            $data2[] = array(
	                'po_id' =>$value->id_seq, 
	                'airport_id'=>$airport_id,
	                'shelter_id' =>$idShelter,
	                'queue_number'=>0,
	                'status'=>1,
	                'created_by'=>$this->session->userdata('username'),
	                );
        	}
        	$this->m_shelter->insertQueue($data2);

			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				echo $rest=$this->msg_error('Failed add data');
			}
			else
			{
				$this->db->trans_commit();
				echo $rest=$this->msg_success('Success add data');
			}
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'airport/shelter/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['airport'] = $this->m_shelter->get_master('master.t_mtr_airport');
		$data['shelter'] = $this->m_shelter->get_edit($id);
		$data['id'] = encode($id);
		$data['title'] = "Edit Shelter";
		$this->load->view("airport/shelter/edit",$data);
	}
	
	function action_edit()
	{
		validateAjax();
		$id = decode($this->input->post('id'));
		$airport_id=$this->input->post('airport_id');
		$shelter_name=$this->input->post('shelter_name');

		$this->form_validation->set_rules('airport_id', 'Airport', 'required');
		$this->form_validation->set_rules('shelter_name', 'Name', 'required');

		$data=array(
			'airport_id'=>$airport_id,
			// 'shelter_code'=>$shelter_code,
			'shelter_name'=>$shelter_name,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		//check transaction data
		$checkAccessBoarding = $this->m_global->getDataById("trx.t_trx_access_boarding", "shelter_id=$id")->num_rows();
		$checkBoarding = $this->m_global->getDataById("trx.t_trx_boarding", "shelter_id=$id")->num_rows();
		$checkBookingDetail = $this->m_global->getDataById("trx.t_trx_booking_detail", "shelter_id=$id")->num_rows();
		$checkExit = $this->m_global->getDataById("trx.t_trx_check_exit", "shelter_id=$id")->num_rows();
		$checkIn = $this->m_global->getDataById("trx.t_trx_check_in", "shelter_id=$id")->num_rows();
		$checkOut = $this->m_global->getDataById("trx.t_trx_check_out", "shelter_id=$id")->num_rows();
		$checkJourney = $this->m_global->getDataById("trx.t_trx_journey_cycle", "shelter_id=$id")->num_rows();
		$checkPayment = $this->m_global->getDataById("trx.t_trx_payment", "shelter_id=$id")->num_rows();


		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field');
        }
        // else if($checkAccessBoarding>0 or $checkBoarding>0 or $checkBookingDetail>0 or $checkExit>0 or $checkIn>0 or $checkOut>0 or $checkJourney>0 or $checkPayment>0)
        // {
        // 	echo $rest=$this->msg_error('Cannot update, user in transaction');
        // }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_shelter",$data,"id_seq='".$id."'");
			if ($update)
			{
				echo $rest=$this->msg_success('Success update data');
			}
			else
			{
				echo $rest=$this->msg_error('Failed update data');
			}
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'airport/shelter/action_edit';
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

		//check transaction data
		$checkAccessBoarding = $this->m_global->getDataById("trx.t_trx_access_boarding", "shelter_id=$id")->num_rows();
		$checkBoarding = $this->m_global->getDataById("trx.t_trx_boarding", "shelter_id=$id")->num_rows();
		$checkBookingDetail = $this->m_global->getDataById("trx.t_trx_booking_detail", "shelter_id=$id")->num_rows();
		$checkExit = $this->m_global->getDataById("trx.t_trx_check_exit", "shelter_id=$id")->num_rows();
		$checkIn = $this->m_global->getDataById("trx.t_trx_check_in", "shelter_id=$id")->num_rows();
		$checkOut = $this->m_global->getDataById("trx.t_trx_check_out", "shelter_id=$id")->num_rows();
		$checkJourney = $this->m_global->getDataById("trx.t_trx_journey_cycle", "shelter_id=$id")->num_rows();
		$checkPayment = $this->m_global->getDataById("trx.t_trx_payment", "shelter_id=$id")->num_rows();

		if($checkAccessBoarding>0 or $checkBoarding>0 or $checkBookingDetail>0 or $checkExit>0 or $checkIn>0 or $checkOut>0 or $checkJourney>0 or $checkPayment>0)
		{
			echo $rest=$this->msg_error('Cannot delete, user in transaction');
		}
		else
		{
			$this->db->trans_begin();
	    	$this->m_global->update("master.t_mtr_shelter",$data,"id_seq=$id");
	    	$this->m_global->update("master.t_mtr_po_queue",$data,"shelter_id=$id");

			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				echo $rest=$this->msg_error('Failed delete data');
			}
			else
			{
				$this->db->trans_commit();
				echo $rest=$this->msg_success('Success delete data');
			}
		}


		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'airport/shelter/delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

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

	function createShelter()
	{

		$max=$this->db->query("select max (shelter_code) as max_code from master.t_mtr_shelter")->row();
		$kode=$max->max_code;
		$noUrut = (int) substr($kode, 2, 3);
		$noUrut++;
		$char = "SH";
		$shelterCode = $char . sprintf("%03s", $noUrut);
		return $shelterCode;
	}

}