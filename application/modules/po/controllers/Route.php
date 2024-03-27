<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Route extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_route');
		$this->load->model('m_global');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Master Route";
		$data['content'] = "po/route/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_route->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['title'] = "Add Route";
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$this->load->view("po/route/add",$data);

	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->getMenu($this->session->userdata('user_group_id'));
		$route=trim($this->input->post('route'));
		$airportId=$this->input->post('airportId');
		
		$this->form_validation->set_rules('route', 'route', 'required');
		$this->form_validation->set_rules('airportId', 'Airport', 'required');

		$data=array(
					'airport_id'=>$airportId,
					'route_info'=>$route,
					'route_code'=>$this->createRouteCode(),
					'status'=>1,
					'created_by'=>$this->session->userdata('username'),
		);

		$checkRoute=$this->m_global->getDataById("master.t_mtr_route","upper(route_info)=upper('".$route."') and status=1")->num_rows();

		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
     	else if ($checkRoute>0)
     	{
     		echo $rest=$this->msg_error('Route already in use');
     	}
        else
        {
        	$insert=$this->m_global->insert("master.t_mtr_route",$data);
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
        $logUrl      = site_url().'po/route/action_add';
        $logMethod   = 'ADD';
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

		$disabled = $this->m_global->masterDisable('master.t_mtr_route',$id,$data);

		if ($disabled) {
			echo json_encode(array(
				'code' => 200,
				'message' => 'Data successfully disabled',
			));
		}else{
			echo json_encode(array(
				'code' => 101,
				'message' => 'Please contact admin',
			));
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

		$disabled = $this->m_global->masterDisable('master.t_mtr_route',$id,$data);

		if ($disabled) {
			echo json_encode(array(
				'code' => 200,
				'message' => 'Data successfully enabled',
			));
		}else{
			echo json_encode(array(
				'code' => 101,
				'message' => 'Please contact admin',
			));
		}
	}

	function edit($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$id = decode($id);
		$data['title'] = "Edit Route";
		$data['route']=$this->m_global->getDataById("master.t_mtr_route","id_seq=$id")->row();
		$data['airport']=$this->m_global->getdata("master.t_mtr_airport","where status=1 order by airport_name asc");
		$this->load->view("po/route/edit",$data);

	}
	
	function action_edit()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$route=trim($this->input->post('route'));
		$airportId=$this->input->post('airportId');
		$routeId=decode($this->input->post('routeId'));

		$data=array(
			'airport_id'=>$airportId,
			'route_info'=>$route,
			'updated_on'=>date('Y-m-d H:i:s'),
			'updated_by'=>$this->session->userdata('username')
		);
		
		$checkRoute=$this->m_global->getDataById("master.t_mtr_route","upper(route_info)=upper('".$route."') and status=1 and id_seq!=$routeId")->num_rows();

		$checkTrx=$this->m_global->getDataById("trx.t_trx_tap_out","route_id=$routeId ")->num_rows();

		// $checkFare=$this->m_global->getDataById("master.t_mtr_fare","route_id=$routeId and status=1")->num_rows();

		$this->form_validation->set_rules('route', 'route', 'required');
		$this->form_validation->set_rules('airportId', 'Airport', 'required');
		$this->form_validation->set_rules('routeId', 'route', 'required');

		if ($this->form_validation->run() == FALSE)
        {
            echo $res=$this->msg_error('Please input the field!');
        }
        else if ($checkRoute>0)
        {
        	echo $res=$this->msg_error('Route already in use');
        }
        else if($checkTrx>0)
        {
        	echo $res=$this->msg_error('Failed edit route, route in transaction');	
        }
        // else if($checkFare>0)
        // {
        // 	echo $res=$this->msg_error('Failed edit route, route already paired');	
        // }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_route",$data,"id_seq=$routeId");
        	if ($update)
        	{
        		echo $res=$this->msg_success("Success edit data");
        	}
        	else
        	{
        		echo $res=$this->msg_error("Failed edit data");
        	}
        }
		
		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/route/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);			
	}
	
	public function delete($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');

		$id = decode($id);

		$data=array(
			'status'=>-5,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$checkTrx=$this->m_global->getDataById("trx.t_trx_tap_out","route_id=$id ")->num_rows();

		$checkFare=$this->m_global->getDataById("master.t_mtr_fare","route_id=$id and status=1")->num_rows();

		if($checkTrx>0)
		{
			echo $res=$this->msg_error('Failed delete route, route in transaction');
		}
		else if($checkFare>0)
		{
			echo $res=$this->msg_error('Failed delete route, route already paired');
		}
		else
		{
			$delete=$this->m_global->update("master.t_mtr_route",$data,"id_seq=$id");
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
        $logUrl      = site_url().'po/route/delete';
        $logMethod   = 'DELETE';
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

	function createRouteCode()
	{

		$max=$this->db->query("select max (route_code) as max_code from master.t_mtr_route")->row();
		$kode=$max->max_code;
		$noUrut = (int) substr($kode, 2, 4);
		$noUrut++;
		$char = "RT";
		$poCode = $char . sprintf("%04s", $noUrut);
		return $poCode;
	}
	

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */