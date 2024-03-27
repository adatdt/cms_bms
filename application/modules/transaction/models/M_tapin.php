<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_tapin extends CI_Model {

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
		$po= decode($get['po']);
		$type= $get['type'];
		
		$where='where a.id_seq is not null ';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' ) ";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' ) ";
		}

		if(!empty($po))
		{
			$where .="and(c.id_seq=$po) ";
		}

		if(!empty($type))
		{
			$where .="and(f.id_seq=$type) ";
		}

		if (!empty($search))
		{
			$where .="and(  a.plate_number ilike '%".$search."%'
						 or a.uid ilike '%".$search."%' or e.bus_name ilike '%".$search."%'
						)";
		}

		$sql =  "select f.type,e.bus_name, d.terminal_name, c.po_name, b.airport_name,a.* from trx.t_trx_tap_in a 
				left join master.t_mtr_airport b on a.airport_id=b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join master.t_mtr_device_terminal d on a.terminal_code=d.terminal_code 
				left join master.t_mtr_bus e on a.plate_number=e.plate_number
				left join master.t_mtr_bus_type f on e.bus_type_id=f.id_seq 
				$where order by a.id_seq desc";

		return $this->db->query($sql);
	}


	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$sortFrom = trim($this->input->post('sortFrom'));
		$sortTo = trim($this->input->post('sortTo'));
		$po= decode($this->input->post('po'));
		$type= $this->input->post('type');
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id_seq';
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

		if(!empty($po))
		{
			$where .="and(c.id_seq=$po)";
		}

		if(!empty($type))
		{
			$where .="and(f.id_seq=$type)";
		}

		if (!empty($search))
		{
			$where .="and(  a.plate_number ilike '%".$search."%'
						 or a.uid ilike '%".$search."%' or e.bus_name ilike '%".$search."%'
						)";
		}

		$sql =  "select f.type,e.bus_name, d.terminal_name, c.po_name, b.airport_name,a.* from trx.t_trx_tap_in a 
				left join master.t_mtr_airport b on a.airport_id=b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join master.t_mtr_device_terminal d on a.terminal_code=d.terminal_code 
				left join master.t_mtr_bus e on a.plate_number=e.plate_number
				left join master.t_mtr_bus_type f on e.bus_type_id=f.id_seq 
				$where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$clear=$this->m_global->menuAccess($this->session->userdata('user_group_id'),'transaction/tap_in','force_exit');

	    	$action="";

	    	if ($clear)
			{
				if($r['status']=='1')
				{
					$action .= "<button type='button' class='btn bg-angkasa2 btn-icon btn-xs btn-dtgrid' title='Force Exit' onclick='clearData(".'"'.encode($r['id_seq']).'"'.")' >Force Exit</button> ";
				}	
			}

	    	if($r['status']=='1')
	    	{
	    		$status='<span class="label label-flat border-success text-success-600">IN</span>';
	    	}
	    	else
	    	{
	    		$status='<span class="label label-flat border-danger text-danger-600">OUT</span>';	
	    	}

	    	$r['status']=$status;
	    	$r['created_on']=format_dateTime($r['created_on']);
	    	$r['action']=$action;

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

	public function getBusId($id)
	{
		return $this->db->query("
			select b.bus_id , a.* from trx.t_trx_tap_in a
			join master.t_mtr_driver b on a.uid=b.uid and b.status=1
			where a.id_seq=$id
			")->row();
	}

	public function update($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table,$data);
    }

}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */