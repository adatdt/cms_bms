<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Booking extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_booking');
	}

	public function index()
	{
		// check apakah user po
		$user_id=decode($this->session->userdata('user_id'));
		$check=$this->m_global->getDataById("master.t_mtr_user_po","user_id=".$user_id);

		$data['title'] = "Booking";
		$data['content'] = "booking/index";
		if($check->num_rows()>0)
		{
			$data['user_po']=1;
			$data['po']=$this->m_global->getDataById("master.t_mtr_po","id_seq=".$check->row()->po_id)->row();
		}
		else
		{
			$data['po']= $this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
			$data['user_po']=0;
		}
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['status']=$this->m_global->getData("master.t_mtr_ticket_status","where status=1 order by status_name asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		// validateAjax();
		$list = $this->m_booking->getData();
		echo json_encode($list);
	}

	public function detail($id='')
	{
		if ($id === '') {
			show_404();
			return false;
		}

		$id = decode($id);
		$data['title'] = "Booking Detail";
		$data['booking_code'] = $this->m_booking->get_booking_code($id);
		$data['content'] = "booking/detail";
		$data['detail'] = $this->m_booking->getDetail($id);
		$this->load->view('booking/detail',$data);
	}

	public function download()
	{
		ini_set('memory_limit','1024M');

		$this->load->library('exceldownload');
		$data = $this->m_booking->download()->result();
		$excel = new Exceldownload();
		// Send Header
		$excel->setHeader('Transaction_Booking.xls');
		$excel->BOF();

		

		$excel->writeLabel(0, 0, "No");
		$excel->writeLabel(0, 1, "Transaction Date"); 
		$excel->writeLabel(0, 2, "Booking Code"); 
		$excel->writeLabel(0, 3, "Ticket Number");
		$excel->writeLabel(0, 4, "Po Bus");
		$excel->writeLabel(0, 5, "Shelter");
		$excel->writeLabel(0, 6, "Type/ Class");
		$excel->writeLabel(0, 7, "Terminal");
		$excel->writeLabel(0, 8, "Route");
		$excel->writeLabel(0, 9, "Price");
		$excel->writeLabel(0, 10, "Status");


		$index=1;
		foreach ($data as $key => $value) {

			empty($value->terminal_name)?$terminal_name="B2B":$terminal_name=$value->terminal_name;
			
			$excel->writeLabel($index,0, $index);
			$excel->writeLabel($index,1, $value->trx_date);
			$excel->writeLabel($index,2, $value->booking_code);
			$excel->writeLabel($index,3, $value->ticket_code);
			$excel->writeLabel($index,4, $value->po_name);
			$excel->writeLabel($index,5, $value->shelter_name);
			$excel->writeLabel($index,6, $value->type);
			$excel->writeLabel($index,7, $terminal_name);
			$excel->writeLabel($index,8, $value->route_info);
			$excel->writeLabel($index,9, $value->price);
			$excel->writeLabel($index,10, $value->status_name);


			// $excel->writeLabel($index,9, $value->driver_phone);

		$index++;
		}
		 
		$excel->EOF();
		exit();
	}

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */