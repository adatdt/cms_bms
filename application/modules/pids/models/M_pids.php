<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pids extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}
	

	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$shelter=$this->input->post("shelter");
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where a.id_seq is not null and a.status=1 ';

		if (!empty($shelter))
		{
			$where .="and (a.shelter_id=$shelter)";
		}

		if (!empty($search))
		{
			$where .="and(
						d.bus_name ilike '%".$search."%' or d.plate_number ilike '%".$search."%'
						or c.po_name ilike '%".$search."%' or b.airport_name ilike '%".$search."%'
						or e.type ilike '%".$search."%' 
						or a.route_info ilike '%".$search."%' or a.bus_status ilike '%".$search."%'
					 )";
		}

		$sql =  "
				select e.type, d.bus_name,d.plate_number, b.airport_name, c.po_name ,a.* from master.t_mtr_pids a
				left join master.t_mtr_airport b on a.airport_id=b.id_seq  
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join master.t_mtr_bus d on a.bus_id=d.id_seq
				left join master.t_mtr_bus_type e on d.bus_type_id=e.id_seq
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'pids','delete');
	    	$action = '';

	    	if($delete){
	    		$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    	};
	    	$r['action']=$action;
	    	$r['estimated_arrival_time']=format_dateTime($r['estimated_arrival_time']);

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