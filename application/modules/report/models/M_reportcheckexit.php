<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_reportcheckexit extends CI_Model {

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
		
		$where='where a.id_seq is not null ';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}

		if (!empty($search))
		{
			$where .="and(f.driver_name ilike '%".$search."%' or c.shelter_name ilike '%".$search."%' 
							or e.po_name ilike '%".$search."%' or d.plate_number ilike '%".$search."%'
							or d.route_info ilike '%".$search."%'
						)";
		}

		$sql =  "
		select f.driver_name, e.po_name, d.plate_number, d.route_info, c.shelter_name, b.terminal_name, a.* from trx.t_trx_check_exit a 
				left join master.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
				left join master.t_mtr_shelter c on a.shelter_id=c.id_seq
				left join trx.t_trx_tap_out d on a.tap_out_id=d.id_seq
				left join master.t_mtr_po e on d.po_id=e.id_seq
				left join master.t_mtr_driver f on d.uid=f.uid
				$where order by a.id_seq desc";

		return $this->db->query($sql);
	}


	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$sortFrom = trim($this->input->post('sortFrom'));
		$sortTo = trim($this->input->post('sortTo'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : "to_char(a.created_on,'YY-MM-DD')";
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where a.id_seq is not null ';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}

		if (!empty($search))
		{
			$where .="and( e.po_name ilike '%".$search."%' or d.route_info ilike '%".$search."%'
							or b.terminal_name ilike '%".$search."%'
						)";
		}

		$sql =  "select distinct e.po_name, b.terminal_name ,
				( select count (ab.po_id) from trx.t_trx_check_exit aa
				left join trx.t_trx_tap_out  ab on aa.tap_out_id=ab.id_seq
				where ab.po_id=e.id_seq
				)
				as total_po,
				d.route_info,
				to_char(a.created_on,'YY-MM-DD') as transaction_date
				from trx.t_trx_check_exit a 
				left join master.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
				left join master.t_mtr_shelter c on a.shelter_id=c.id_seq
				left join trx.t_trx_tap_out d on a.tap_out_id=d.id_seq
				left join master.t_mtr_po e on d.po_id=e.id_seq
				left join master.t_mtr_driver f on d.uid=f.uid
				$where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {
	    	$r['transaction_date']=format_date($r['transaction_date']);
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