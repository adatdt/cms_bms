<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tap_out extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_tapout');
	}

	public function index()
	{
		// check untuk user po
		$user_id=decode($this->session->userdata('user_id'));
		$user_po=$this->m_global->getDataById("master.t_mtr_user_po","user_id=".$user_id);


		$data['title'] = "Tap Out";
		$data['content'] = "tap_out/index";

		if($user_po->num_rows()>0)
		{
			$data['user_po']=1;
			$data['po'] = $this->m_global->getDataById("master.t_mtr_po"," id_seq=".$user_po->row()->po_id )->row();
		}
		else
		{
			$data['user_po']=0;
			$data['po'] = $this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		}

		$data['busType'] = $this->m_global->getData("master.t_mtr_bus_type","where status=1 order by type asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_tapout->getData();
		echo json_encode($list);
	}

	
	public function download()
	{

		$this->load->library('exceldownload');
		$data = $this->m_tapout->download()->result();
		$excel = new Exceldownload();
		// Send Header
		$excel->setHeader('Transaction_Tap_Out.xls');
		$excel->BOF();
		
		$excel->writeLabel(0, 0, "No");
		$excel->writeLabel(0, 1, "Transaction Date"); 
		$excel->writeLabel(0, 2, "UID");
		$excel->writeLabel(0, 3, "PO Bus");
		$excel->writeLabel(0, 4, "Bus Name");
		$excel->writeLabel(0, 5, "Type");
		$excel->writeLabel(0, 6, "Plate Number");
		$excel->writeLabel(0, 7, "Route");
		$excel->writeLabel(0, 8, "Driver Name");
		$excel->writeLabel(0, 9, "Driver Phone");


		$index=1;
		foreach ($data as $key => $value) {
			$excel->writeLabel($index,0, $index);
			$excel->writeLabel($index,1, $value->created_on);
			$excel->writeLabel($index,2, $value->uid);
			$excel->writeLabel($index,3, $value->po_name);
			$excel->writeLabel($index,4, $value->bus_name);
			$excel->writeLabel($index,5, $value->type);
			$excel->writeLabel($index,6, $value->plate_number);
			$excel->writeLabel($index,7, $value->route_info);
			$excel->writeLabel($index,8, $value->driver_name);
			$excel->writeLabel($index,9, $value->driver_phone);

		$index++;
		}
		 
		$excel->EOF();
		exit();
	}

	public function detail($id='')
	{
		if ($id === '') {
			show_404();
			return false;
		}

		$id = decode($id);
		$data['title'] = "Tap Out Detail";
		$data['detail']=$this->m_tapout->getDetail($id);
		$data['detail2']=$this->m_tapout->getDetail2($id);
		$this->load->view('tap_out/detail',$data);
	}

	// public function clear($idTapout)
	// {
	// 	$id=decode($idTapout);
		
	// 	$data=array(
	// 		'status'=>0,
	// 		'updated_by'=>$this->session->userdata('username'),
	// 		'updated_on'=>date('Y-m-d H:i:s'),
	// 	);

	// 	$update=$this->m_global->update("trx.t_trx_tap_out",$data,"id_seq=$id");		
	// 	if($update)
	// 	{
	// 		$this->m_global->update("trx.t_trx_check_in",$data,"tap_out_id=$id");
	// 		$this->m_global->update("trx.t_trx_check_out",$data,"tap_out_id=$id");
	// 		$this->m_global->update("trx.t_trx_journey_cycle",$data,"tap_out_id=$id");
	// 		$busId=$this->m_tapout->getBusId($id);
	// 		$delete=$this->m_global->deleteData("master.t_mtr_pids","bus_id=".$busId->bus_id);
	// 		if($delete)
	// 		{
	// 			$curl = curl_init();

	// 			 curl_setopt_array($curl, array(
	// 			  CURLOPT_PORT => "3000",
	// 			  CURLOPT_URL => "http://119.110.87.74:3000/pids",
	// 			  CURLOPT_RETURNTRANSFER => true,
	// 			  CURLOPT_ENCODING => "",
	// 			  CURLOPT_MAXREDIRS => 10,
	// 			  CURLOPT_TIMEOUT => 30,
	// 			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 			  CURLOPT_CUSTOMREQUEST => "GET",
	// 			  CURLOPT_POSTFIELDS => "",
	// 			  CURLOPT_HTTPHEADER => array(
	// 			    "Postman-Token: caa08c67-a11c-4785-ae78-0c47f4b4c851",
	// 			    "cache-control: no-cache"
	// 			  ),
	// 			));

	// 			$response = curl_exec($curl);
	// 			$err = curl_error($curl);

	// 			curl_close($curl);

	// 			echo $this->msg_success("Success force exit");
	// 		}
	// 		else
	// 		{
	// 			echo $this->msg_error("failed force exit");
	// 		}	
	// 	}
	// 	else
	// 	{
	// 		echo $this->msg_error("failed force exit");
	// 	}
	// }

	public function clear($idTapout)
	{
		$id=decode($idTapout);
		$busId=$this->m_tapout->getBusId($id);

		$data=array(
			'status'=>0,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);


		$this->db->trans_begin();

		$this->m_tapout->update("trx.t_trx_tap_out",$data,"id_seq=$id");		
		$this->m_tapout->update("trx.t_trx_check_in",$data,"tap_out_id=$id");
		$this->m_tapout->update("trx.t_trx_check_out",$data,"tap_out_id=$id");
		$this->m_tapout->update("trx.t_trx_journey_cycle",$data,"tap_out_id=$id");
		$this->m_tapout->deleteData("master.t_mtr_pids","bus_id=".$busId->bus_id);

		if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $this->msg_error("Failed force exit");
        }
        else
        {
            $this->db->trans_commit();
        	
        	$portSocket = $this->config->item('port_socket_server');
        	$urlSocket  = $this->config->item('url_socket_server').":".$this->config->item('port_socket_server')."/pids";
        	$curl = curl_init();
			curl_setopt_array($curl, array(
					CURLOPT_PORT => $portSocket,
					CURLOPT_URL => $urlSocket,
					CURLOPT_RETURNTRANSFER => true,
				 	CURLOPT_ENCODING => "",
				  	CURLOPT_MAXREDIRS => 10,
				 	 CURLOPT_TIMEOUT => 30,
				 	 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  	CURLOPT_CUSTOMREQUEST => "GET",
				  	CURLOPT_POSTFIELDS => "",
				  	CURLOPT_HTTPHEADER => array(
				    "Postman-Token: caa08c67-a11c-4785-ae78-0c47f4b4c851",
				    "cache-control: no-cache"
			  	),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);

			echo $this->msg_success("Success force exit");
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

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		// $data['po'] = $this->m_global->getDataById('master.t_mtr_po',"id_seq=$id ")->row();

		$getPoId=$this->m_tapout->getEdit($id)->row();

		$data['detail'] = $this->m_tapout->getEdit($id)->row();
		$data['detail2'] = $this->m_tapout->getEdit2($id);
		$data['route'] = $this->m_tapout->getRoute("where po_id=$getPoId->po_id and bus_type_id=$getPoId->type_id and a.status=1 order by route_info asc ")->result();
		$data['shelter'] = $this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['title'] = "Edit Tap Out";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("transaction/tap_out/edit",$data);


	}

	function action_edit()
	{
		$tapOutId=decode($this->input->post('id'));
		$routeId=decode($this->input->post('route'));
		$busId=decode($this->input->post('busId'));
		$routeName=$this->input->post('routeName');

		$shelter=$this->input->post('shelter[]');
		$terminalCode=$this->input->post('terminalCode[]');
		// $tapOutId=$this->input->post('tapOutId[]');
		$shelterOrder=$this->input->post('shelterOrder[]');
		$status=$this->input->post('status[]');
		$createdBy=$this->input->post('createdBy[]');
		$createdOn=$this->input->post('createdOn[]');

		$this->form_validation->set_rules('id', 'Id', 'required');
		$this->form_validation->set_rules('route', 'Route Id', 'required');
		$this->form_validation->set_rules('routeName', 'Route Id', 'required');
		$this->form_validation->set_rules('busId', 'Bus Id', 'required');

		$dataTapout=array(
					'route_id'=>$routeId,
					'route_info'=>$routeName,
					'updated_on'=>date('Y-m-d H:i:s'),
					'updated_by'=>$this->session->userdata('username'),
		);

		$dataDelete=array(
			'status'=>-5,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field!');
        }
        else
        {
        	$updateTapout=$this->m_global->update("trx.t_trx_tap_out",$dataTapout,"id_seq=$tapOutId");
        	if($updateTapout)
        	{
        		$updatePids=$this->m_global->update("master.t_mtr_pids",$dataTapout,"bus_id=$busId");
        		if($updatePids)
        		{
        			$delete=$this->m_global->update("trx.t_trx_journey_cycle",$dataDelete,"tap_out_id=".$tapOutId);
        			if($delete)
        			{
        				$portSocket = $this->config->item('port_socket_server');
        				$urlSocket  = $this->config->item('url_socket_server').":".$this->config->item('port_socket_server')."/pids";
	        			$curl = curl_init();

						curl_setopt_array($curl, array(
						CURLOPT_PORT => $portSocket,
						CURLOPT_URL => $urlSocket,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "GET",
						CURLOPT_POSTFIELDS => "",
						CURLOPT_HTTPHEADER => array(
						    "Postman-Token: caa08c67-a11c-4785-ae78-0c47f4b4c851",
						    "cache-control: no-cache"
						  ),
						));

						$response = curl_exec($curl);
						$err = curl_error($curl);

						curl_close($curl);
						$countShelter=count($shelter);
						$order=1;
						for($i=0;$i<$countShelter;$i++)
						{
							if(!empty($shelter[$i]))
							{
									$data=array(
									'tap_out_id'=>$tapOutId,
									'terminal_code'=>$terminalCode[$i],
									'shelter_id'=>$shelter[$i],
									'shelter_order'=>$order,
									'status'=>$status[$i],
									'created_by'=>$createdBy[$i],
									'created_on'=>$createdOn[$i],
									);

									// echo print_r($data);

									$this->m_global->insert("trx.t_trx_journey_cycle",$data);
								$order++;
							}
						}

	        			echo $this->msg_success("Success edit data");
        			}
        		}
        		else
        		{
        			echo $this->msg_error("Failed edit Pids route");
        		}
        		
        	}
        	else
        	{
        		echo $this->msg_error("Failed edit tapout route");
        	}
        }
	}



	function getNameRoute()
	{
		$id=decode($this->input->post('id'));

		$data=$this->m_global->getDataById("master.t_mtr_route","id_seq=$id")->row();

		echo json_encode($data->route_info);
	}

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */
