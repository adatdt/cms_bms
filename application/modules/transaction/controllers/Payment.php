<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_payment');
	}

	public function index()
	{
		$data['title'] = "Payment";
		$data['content'] = "payment/index";
		$data['type'] = $this->m_global->getData("master.t_mtr_bus_type","where status=1 order by type asc");
		$data['payment_channel'] = $this->m_global->getData("master.t_mtr_payment_channel","where status=1 order by payment_channel asc");
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$user_po=$this->m_global->getDataById("master.t_mtr_user_po","user_id=".decode($this->session->userdata('user_id')));
		if ($user_po->num_rows() > 0)
		{
			$data['user_po'] = 1;
			$data['po'] = $this->db->query("SELECT * FROM master.t_mtr_po WHERE status=1 and id_seq=".$user_po->row()->po_id)->row();
		}
		else
		{
			$data['user_po'] = 0;
			$data['po'] = $this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		}
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function detail($id)
	{
		$data['title']='Ticket Detail';
		$detail=$this->m_payment->getData(decode($id));
		$data['detail']=$detail['rows'][0];
		$this->load->view('transaction/payment/detail',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_payment->getData();
		echo json_encode($list);
	}

	public function download()
	{
		ini_set('memory_limit','1024M');

		$this->load->library('exceldownload');
		$data = $this->m_payment->download()->result();
		$excel = new Exceldownload();
		// Send Header
		$excel->setHeader('Transaction_Payment.xls');
		$excel->BOF();

		$excel->writeLabel(0, 0, "No");
		$excel->writeLabel(0, 1, "Booking Code"); 
		$excel->writeLabel(0, 2, "Ticket Code");
		$excel->writeLabel(0, 3, "Transaction Date");
		$excel->writeLabel(0, 4, "Shelter");
		$excel->writeLabel(0, 5, "Route");
		$excel->writeLabel(0, 6, "PO Bus");
		$excel->writeLabel(0, 7, "Type");
		$excel->writeLabel(0, 8, "Fare");
		$excel->writeLabel(0, 9, "Payment Channel");
		$excel->writeLabel(0, 10, "Counter");

		$index=1;
		foreach ($data as $key => $value) {
			$excel->writeLabel($index,0, $index);
			$excel->writeLabel($index,1, $value->booking_code);
			$excel->writeLabel($index,2, $value->ticket_code);
			$excel->writeLabel($index,3, $value->created_on);
			$excel->writeLabel($index,4, $value->shelter_name);
			$excel->writeLabel($index,5, $value->route_info);
			$excel->writeLabel($index,6, $value->po_name);
			$excel->writeLabel($index,7, $value->type);
			$excel->writeLabel($index,8, $value->price);
			$excel->writeLabel($index,9, $value->payment_channel);
			$excel->writeLabel($index,10, $value->first_name);

			// $excel->writeLabel($index,9, $value->driver_phone);

		$index++;
		}
		 
		$excel->EOF();
		exit();
	}

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */