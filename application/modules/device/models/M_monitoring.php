<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_monitoring extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function getDetail()
	{
		$result=array();
		$items=array();

		$qry=$this->db->query("SELECT id_seq,shelter_name FROM master.t_mtr_shelter WHERE status=1 ORDER BY shelter_name");

		// print_r($query=$qry->result_array());exit;
		$query=$qry->result_array();
		array_unshift($query, array('id_seq' => 0, 'shelter_name' => "PENGENDAPAN"));

		if($query)
		{
			foreach ($query as $row)
			{
				$has_child=$this->check_parent($row['id_seq']);

				if ($has_child)
				{
					$row['children'] =$this->get_list_children($row['id_seq']);
					$row['state']	  = 'open';
				}

	            $row['perangkat'] = $row['shelter_name'];
	            // $row->perangkat = "Pengendapan";
	            $row['iconCls'] = 'icon-bus';
	            // $row->perangkat = $row->terminal_type_name;


	          array_push($items, $row);
			}
		}
		$result['rows']=$items;
		$result['detail']=$this->getActive();
		return $result;
	}

	public function check_parent($shelter_id)
	{
		$row=$this->db->query("SELECT * FROM master.t_mtr_device_terminal where shelter_id=$shelter_id AND status=1")->num_rows();

		if($row>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function get_list_children($pi) {
		$items  = array();

		$sql = 'SELECT  \'c\' || DT.id_seq::varchar AS id_seq,TT.id_seq AS pembeda,TT.terminal_type_name,DT.terminal_name,DT.updated_on FROM master.t_mtr_device_terminal DT JOIN master.t_mtr_device_terminal_type TT ON TT.id_seq=DT.terminal_type_id
				   WHERE shelter_id ='.$pi.' AND DT.status=1 AND TT.status=1 AND TT.id_seq != 3 ORDER BY terminal_type_name,terminal_name';
				   
		$query 	= $this->db->query($sql)->result();
		
		if($query){
			foreach ($query as $row){
				// echo $this->select_status_active_device($row->updated_on);
	            $row->perangkat  = $row->terminal_type_name;

	            $row->waktu_updated = "";

	            if ($row->updated_on != "") {
	            	$row->waktu_updated  = $this->format_date_device($row->updated_on);
	            }
	            
	            $row->status_device  = $this->select_status_active($row->updated_on);
	            $row->status_active_device  = $this->select_status_active_device($row->updated_on);
	            // $row->state	  = 'closed';
	            if ($row->pembeda == 1) {
	            	$row->iconCls = 'icon-store';
	            }elseif($row->pembeda == 2){
	            	$row->iconCls = 'icon-store2';
	            }elseif($row->pembeda == 3){
	            	$row->iconCls = 'icon-mobile';
	            }elseif($row->pembeda == 4){
	            	$row->iconCls = 'icon-screen3';
	            }elseif($row->pembeda == 5){
	            	$row->iconCls = 'icon-box';
	            }elseif($row->pembeda == 6){
	            	$row->iconCls = 'icon-box';
	            }elseif($row->pembeda == 7){
	            	$row->iconCls = 'icon-city';
	            }else{
	            	$row->iconCls = 'icon-menu';
	            }
					
				array_push($items, $row);
			}
			// exit;
		}
		
		return $items;
	}

	private function select_status_active($updated_on)
	{
		$wk = $this->db->query("SELECT CURRENT_TIMESTAMP - interval '5 MINUTE' AS waktu")->row();
		$waktu_kurang = $wk->waktu;

		if ($updated_on > $waktu_kurang) {
			return '<span class="label label-flat border-success text-success-600">Active</span>';
		}else{
			return '<span class="label label-flat border-danger text-danger-600">Not Active</span>';
		}
	}

	private function select_status_active_device($updated_on)
	{
		$wk = $this->db->query("SELECT CURRENT_TIMESTAMP - interval '5 MINUTE' AS waktu")->row();
		$waktu_kurang = $wk->waktu;

		if ($updated_on > $waktu_kurang) {
			return 1;
		}else{
			return 0;
		}
	}

	private function format_date_device($date)
	{
		return date("d F Y H:i:s", strtotime($date));
	}

	public function countData($param = "")
	{
		$wk = $this->db->query("SELECT CURRENT_TIMESTAMP - interval '5 MINUTE' AS waktu")->row();
		$waktu_kurang = $wk->waktu;

		$tambahan_query = "";

		if ($param != "") {
			$tambahan_query = "AND DT.updated_on > TO_TIMESTAMP('$waktu_kurang', 'YYYY-MM-DD HH24:MI:SS')";
		}

		$query = $this->db->query("SELECT 
				DISTINCT(DT.id_seq),
					TT.terminal_type_name,
					DT.terminal_name,
					DT.updated_on
				FROM
					master.t_mtr_device_terminal DT
					JOIN master.t_mtr_device_terminal_type TT ON TT.id_seq = DT.terminal_type_id
					LEFT JOIN master.t_mtr_shelter S ON S.id_seq = DT.shelter_id
				WHERE
				TT.status = 1
				AND	DT.status = 1
				AND TT.id_seq != 3
				AND (S.status=1 OR DT.shelter_id = 0)
				$tambahan_query")->num_rows();
		return $query;
	}

	private function getActive()
	{
		$countData = $this->countData();
		$active = $this->countData(1);
		return array(
			'count_data' => $countData,
			'active_data' => $active,
			'not_active_data' => $countData - $active,
			'percent_active' => number_format($active/$countData * 100,1,",","."), 
			'percent_not_active' => number_format(($countData - $active)/$countData * 100 ,1,",",".")
		);
	}

}