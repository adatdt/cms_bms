<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fare extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_fare');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Master Fare";
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['busType']=$this->m_global->getData("master.t_mtr_bus_type","where status=1 order by type asc");
		$data['content'] = "po/fare/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_fare->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['title'] = "Add Fare";
		$data['po'] = $this->m_global->getData('master.t_mtr_po',"where status=1 order by po_name asc");
		$data['type'] = $this->m_global->getData('master.t_mtr_bus_type',"where status=1 order by type asc");
		$data['route'] = $this->m_global->getData('master.t_mtr_route',"where status=1 order by route_info asc");
		$this->load->view("po/fare/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$po_id=decode($this->input->post('po_id'));
		$bus_type_id=decode($this->input->post('bus_type_id'));
		$route_id=decode($this->input->post('route_id'));
		$price=trim($this->input->post('price'));

		$this->form_validation->set_rules('po_id', 'Po', 'required');
		$this->form_validation->set_rules('bus_type_id', 'Bus', 'required');
		$this->form_validation->set_rules('route_id', 'Route', 'required');
		$this->form_validation->set_rules('price', 'Price', 'required');

		$checkFare=$this->m_global->getDataById("master.t_mtr_fare","po_id=$po_id and bus_type_id=$bus_type_id and route_id=$route_id and status=1")->num_rows();

		$data=array(
			'po_id'=>$po_id,
			'bus_type_id'=>$bus_type_id,
			'route_id'=>$route_id,
			'price'=>$price,
			'created_by'=>$this->session->userdata('username'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field');
        }
        else if($checkFare>0)
        {
        	echo $rest=$this->msg_error('Fare for this route already in use');	
        }
        else
        {
        	$insert=$this->m_global->insert("master.t_mtr_fare",$data);
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
        $logUrl      = site_url().'po/fare/action_add';
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
		$data['fare'] = $this->m_fare->get_edit($id);
		$data['po'] = $this->m_global->getData('master.t_mtr_po',"where status=1 order by po_name asc");
		$data['type'] = $this->m_global->getData('master.t_mtr_bus_type',"where status=1 order by type asc");
		$data['route'] = $this->m_global->getData('master.t_mtr_route',"where status=1 order by route_info asc");
		$data['id'] = encode($id);
		$data['title'] = "Edit Fare";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));

		$fare=$this->m_global->getDataById("master.t_mtr_fare","id_seq=$id ")->row();
		$checkTrx=$this->m_fare->checkTrx($fare->po_id,$fare->bus_type_id,$fare->route_id)->num_rows();

		if($checkTrx>0)
		{
			$this->load->view("po/fare/edit2",$data);
		}
		else
		{
			$this->load->view("po/fare/edit",$data);
		}
	}
	function action_edit()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$po_id=decode($this->input->post('po_id'));
		$bus_type_id=decode($this->input->post('bus_type_id'));
		$route_id=decode($this->input->post('route_id'));
		$price=trim($this->input->post('price'));
		$id=decode($this->input->post('id'));

		$this->form_validation->set_rules('po_id', 'Po', 'required');
		$this->form_validation->set_rules('bus_type_id', 'Bus', 'required');
		$this->form_validation->set_rules('route_id', 'Route', 'required');
		$this->form_validation->set_rules('price', 'Price', 'required');

		$checkFare=$this->m_global->getDataById("master.t_mtr_fare","po_id=$po_id and bus_type_id=$bus_type_id and route_id=$route_id and status=1 and id_seq !=$id ")->num_rows();

		$fare=$this->m_global->getDataById("master.t_mtr_fare","id_seq=$id ")->row();
		$checkTrx=$this->m_fare->checkTrx($fare->po_id,$fare->bus_type_id,$fare->route_id)->num_rows();

		$data=array(
			'po_id'=>$po_id,
			'bus_type_id'=>$bus_type_id,
			'route_id'=>$route_id,
			'price'=>$price,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field');
        }
        else if($checkFare>0)
        {
        	echo $rest=$this->msg_error('Fare for this route already in use');	
        }
        else if($checkTrx>0)
        {
        	echo $rest=$this->msg_error('Failed edit fare, PO fare in transaction');
        }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_fare",$data,"id_seq='".$id."'");
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
        $logUrl      = site_url().'po/fare/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);		
	}

	function action_edit2()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$price=trim($this->input->post('price'));
		$id=decode($this->input->post('id'));

		$this->form_validation->set_rules('price', 'Price', 'required');

		$fare=$this->m_global->getDataById("master.t_mtr_fare","id_seq=$id ")->row();
		$checkTrx=$this->m_fare->checkTrx($fare->po_id,$fare->bus_type_id,$fare->route_id,"and a.status='1' ")->num_rows();

		$data=array(
			'price'=>$price,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field');
        }
        else if($checkTrx>0)
        {
        	echo $rest=$this->msg_error('Failed edit fare, PO fare in transaction');
        }
        else
        {
        	// jika harga yang di inputkan sama dengan di data base
        	if($price==$fare->price)
        	{
        		$update=$this->m_global->update("master.t_mtr_fare",$data,"id_seq='".$id."'");
				if ($update)
				{
					echo $rest=$this->msg_success('Success update data');
				}
				else
				{
					echo $rest=$this->msg_error('Failed update data');
				}

        	}
        	else
        	{
        		$this->db->trans_begin();

        		$dataDelete=array(
        			'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date('Y-m-d H:i:s'),
					'status'=>-5,
        		);

        		$dataInsert=array(
					'po_id'=>$fare->po_id,
					'bus_type_id'=>$fare->bus_type_id,
					'route_id'=>$fare->route_id,
					'price'=>$price,
					'created_by'=>$this->session->userdata('username'),
				);

        		$this->m_fare->update("master.t_mtr_fare",$dataDelete,"id_seq='".$id."'");
        		$this->m_fare->insert("master.t_mtr_fare",$dataInsert);

    		    if($this->db->trans_status() === FALSE) {
		            $this->db->trans_rollback();
		            echo $rest=$this->msg_error('Failed update data');
		        } 
		        else 
		        {
		            $this->db->trans_commit();
		            echo $rest=$this->msg_success('Success update data');
		        }

        	}

        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/fare/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);		
	}
	
	public function delete($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'delete');
		$id = decode($id);

		$data=array(
			'status'=>-5,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$fare=$this->m_global->getDataById("master.t_mtr_fare","id_seq=$id")->row();
		$checkTrx=$this->m_fare->checkTrx($fare->po_id,$fare->bus_type_id,$fare->route_id,"and a.status=1")->num_rows();
		$checkTrx2=$this->m_fare->checkTrx($fare->po_id,$fare->bus_type_id,$fare->route_id)->num_rows();

		if($checkTrx>0)
		{
			echo $rest=$this->msg_error('Failed delete fare, PO fare in transaction');
		}
		else
		{
			$delete=$this->m_global->update("master.t_mtr_fare",$data,"id_seq=$id");
			if ($delete)
			{
				echo $rest=$this->msg_success('Success delete data');
			}
			else
			{
				echo $rest=$this->msg_error('Failed delete data');
			}	
		}
    	

		$createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/fare/delete';
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

		$disabled = $this->m_global->masterDisable('master.t_mtr_fare',$id,$data);

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

		$disabled = $this->m_global->masterDisable('master.t_mtr_fare',$id,$data);

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