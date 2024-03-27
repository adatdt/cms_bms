<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_payment extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}
	
	public function  download()
	{
		$get = $this->input->get();

		$sortFrom= $get['sortFrom'];
		$sortTo= $get['sortTo'];
		$search= $get['search'];
		$po = decode($get['po']);
		$payment_channel= decode($get['payment_channel']);
		$type = decode($get['type']);
		$shelter = decode($get['shelter']);

		$where=' WHERE A.id_seq IS NOT NULL
		AND b.status IN(2,3)';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}
		if(!empty($type))
		{
			$where .= "and b.bus_type_id =".$type." ";
		}

		if(!empty($po))
		{
			$where .= "and b.po_id =".$po." ";
		}

		if(!empty($shelter))
		{
			$where .= "and b.shelter_id =".$shelter." ";
		}

		if(!empty($payment_channel))
		{
			$where .= "and b.payment_channel_id =".$payment_channel." ";
		}

		if (!empty($search))
		{
			$where .="and(f.first_name ilike '%".$search."%' or e.shelter_name ilike '%".$search."%'
						or a.ticket_code ilike '%".$search."%' or c.booking_code ilike '%".$search."%'
						or b.route_info ilike '%".$search."%' or d.po_name ilike '%".$search."%'

					)";
		}

		$sql="SELECT
		DISTINCT(A.id_seq),
		f.first_name,
		e.shelter_name,
		C.booking_code,
		pc.payment_channel,
		d.po_name,
		G.TYPE,
		b.route_info,
		A.ticket_code,
		A.created_on,
		A.price
		FROM
		trx.t_trx_payment A
		LEFT JOIN trx.t_trx_booking_detail b ON A.ticket_code = b.ticket_code
		LEFT JOIN trx.t_trx_booking C ON b.booking_id = C.id_seq
		LEFT JOIN master.t_mtr_po d ON b.po_id = d.id_seq
		LEFT JOIN master.t_mtr_shelter e ON b.shelter_id = e.id_seq
		LEFT JOIN core.t_mtr_user f ON A.created_by = f.username
		LEFT JOIN master.t_mtr_bus_type G ON b.bus_type_id = G.id_seq
		LEFT JOIN master.t_mtr_payment_channel pc ON pc.id_seq = b.payment_channel_id
		LEFT JOIN trx.t_trx_linkaja la ON la.booking_code = C.booking_code AND la.trx_type = '022'
				$where ORDER BY a.id_seq desc";

		return $this->db->query($sql);
	}


	public function getData($ticket_code = "")
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$sortFrom = trim($this->input->post('sortFrom'));
		$sortTo = trim($this->input->post('sortTo'));
		$po = decode($this->input->post('po'));
		$type = decode($this->input->post('type'));
		$shelter= decode($this->input->post('shelter'));
		$payment_channel= decode($this->input->post('payment_channel'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'payment.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where = ' WHERE payment.id_seq IS NOT NULL AND  booking_detail.status IN(2,3) ';

		if ($ticket_code != "") {
			$where .= " AND payment.ticket_code = '$ticket_code'";
		}

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(payment.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' ) ";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(payment.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' ) ";
		}

		if(!empty($type))
		{
			$where .= "and booking_detail.bus_type_id =".$type." ";
		}

		if(!empty($po))
		{
			$where .= "and booking_detail.po_id =".$po." ";
		}

		if(!empty($shelter))
		{
			$where .= "and booking_detail.shelter_id =".$shelter." ";
		}

		if(!empty($payment_channel))
		{
			$where .= "and booking_detail.payment_channel_id =".$payment_channel." ";
		}

		if (!empty($search))
		{
			$where .="and(users.first_name ilike '%".$search."%' or shelter.shelter_name ilike '%".$search."%'
						or payment.ticket_code ilike '%".$search."%' or booking.booking_code ilike '%".$search."%'
						or booking_detail.route_info ilike '%".$search."%' or po.po_name ilike '%".$search."%') ";
		}

		$sql="SELECT 
				DISTINCT(payment.ticket_code),
				payment_channel.payment_channel,
				prepaid.bank,
				link_aja.trx_id,
				payment.id_seq,
				users.first_name,
				shelter.shelter_name,
				booking.booking_code,
				booking.created_on AS booking_date,
				po.po_name,
				bus_type.TYPE,
				booking_detail.route_info,
				payment.created_on,
				payment.price,
				booking_detail.payment_channel_id 
			FROM trx.t_trx_payment payment 
			LEFT JOIN trx.t_trx_booking_detail booking_detail ON payment.ticket_code = booking_detail.ticket_code
			LEFT JOIN trx.t_trx_booking booking ON booking_detail.booking_id = booking.id_seq
			LEFT JOIN master.t_mtr_po po ON booking_detail.po_id = po.id_seq
			LEFT JOIN master.t_mtr_shelter shelter ON booking_detail.shelter_id = shelter.id_seq
			LEFT JOIN core.t_mtr_user users ON payment.created_by = users.username
			LEFT JOIN master.t_mtr_bus_type bus_type ON booking_detail.bus_type_id = bus_type.id_seq
			LEFT JOIN master.t_mtr_payment_channel payment_channel ON payment_channel.id_seq = booking_detail.payment_channel_id
			LEFT JOIN trx.t_trx_linkaja link_aja ON link_aja.booking_code = booking.booking_code AND link_aja.trx_type = '022'
			LEFT JOIN trx.t_trx_prepaid prepaid ON booking.booking_code = prepaid.booking_code
			$where 
			ORDER BY $sort $order";
		// echo $sql;
		// exit;

		// $sql =  "select c.shelter_name, b.terminal_name, a.* from trx.t_trx_payment a 
		// 		left join master.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
		// 		left join master.t_mtr_shelter c on a.shelter_id=c.id_seq
		// 		$where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {
	    	$detail=$this->m_global->menuAccess($this->session->userdata('user_group_id'),'transaction/payment','detail');
	    	$action="";

	    	if ($detail)
	    	{
				$action .= "<button type='button' class='btn bg-angkasa2 btn-icon btn-xs btn-dtgrid' title='Detail' onclick='clickDetail(".'"'.encode($r['ticket_code']).'"'.")' >Detail</button> ";
			}

	    	$r['created_on'] = format_dateTimeDetik($r['created_on']);
	    	$r['price'] = idr_currency($r['price']);
	    	$r['detail'] = $action;
	    	
			if(!empty($r['bank'])){
				$r['payment_channel'] = $r['payment_channel']." (".$r['bank'].")";
			}

			$data_rows[] = $r;
	    }

	    $data['total'] = $total_rows;
		$data['rows'] = $data_rows;
		return $data;
	}


	public function select($table,$order)
	{
		$this->db->query("select * from ".$table." order by ".$order);
	}

}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */