<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_menu extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function menu_action()
	{
		return $this->db->query('SELECT id_seq ,name FROM core.t_mtr_menu_action WHERE status =1')->result();
	}

	// public function icon()
	// {
	// 	return $this->db->query('SELECT * FROM core."t_mtr_icon"')->result();
	// }

	public function selected_icon()
	{
		return $this->db->query('SELECT icon FROM core.t_mtr_menu WHERE status=1')->result();
	}

	public function check_menu($parent,$order,$id='')
	{
		if ($id!= '') {
			return $this->db->query('SELECT * FROM core.t_mtr_menu WHERE parent_id='.$parent.' AND status=1 AND "menu_order" ='.$order.' AND  id_seq != '.$id.'')->result();
		}else{
			return $this->db->query('SELECT * FROM core.t_mtr_menu WHERE parent_id='.$parent.' AND status=1 AND "menu_order" ='.$order.'')->result();
		}
	}

	public function check_detail($menu_id)
	{
		return $this->db->query('SELECT action_id FROM core.t_mtr_menu_detail WHERE menu_id='.$menu_id.' AND status=1')->result();
	}

	public function select_detail_id($menu_id,$action_id)
	{
		$qry = $this->db->query('SELECT id_seq FROM core.t_mtr_menu_detail WHERE menu_id='.$menu_id.' AND action_id='.$action_id.' AND status=1')->row();
		if ($qry) {
			return $qry->id_seq;
		}
	}

	public function edit_detail($data)
	{
		return $this->db->update_batch('core.t_mtr_menu_detail', $data,'id_seq');
	}

	public function insert($data)
	{
		$this->db->insert('core.t_mtr_menu', $data);
		return $this->db->insert_id();
	}

	public function check_insert_deleted($menu_id,$action_id)
	{
		$qry = $this->db->query('SELECT id_seq FROM core.t_mtr_menu_detail WHERE menu_id='.$menu_id.' AND action_id='.$action_id.' AND status != 1')->row();
		if ($qry) {
			return $qry->id_seq;
		}
	}

	public function check_edit_privilege($menu_id,$detail)
	{
		$qry = $this->db->query('SELECT id_seq FROM core.t_mtr_privilege WHERE menu_id='.$menu_id.' AND menu_detail_id='.$detail.'')->row();
		if ($qry) {
			return $qry->id_seq;
		}
	}

	public function delete_privilege_menu($data)
	{
		return $this->db->update_batch('core.t_mtr_privilege', $data,'id_seq');
	}

	public function insert_detail($data)
	{
		return $this->db->insert_batch('core.t_mtr_menu_detail',$data);
	}

	public function update_menu($id,$data)
	{
		$this->db->where('id_seq', $id);
		return $this->db->update('core.t_mtr_menu', $data);
	}

	public function delete($id,$data)
	{
		$this->db->where('id_seq', $id);
		return $this->db->update('core.t_mtr_menu', $data);
	}

	public function getDetail()
	{
		$result=array();
		$items=array();

		$qry=$this->db->query("select * from core.t_mtr_menu where parent_id=0  and status=1 order by ".'"menu_order"'." asc");

		$query=$qry->result();

		if($query)
		{
			foreach ($query as $row)
			{
				$has_child=$this->check_parent($row->id_seq);

				if ($has_child)
				{
					$row->children =$this->get_list_children($row->id_seq);
					$row->state	  = 'closed';
				}

				$parent = $row->parent_id;

		        if($row->parent_id == '' || $row->parent_id = null){
		            $parent = 0;
		        }

	            $edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu','edit');
	            $delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu','delete');

	            $row->action = '';

	            if($edit){
	            	$row->action .= '<button onClick="master_edit(\''.site_url().'configuration/menu/edit/'.(encode($row->id_seq)).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button>&nbsp;';
	            }

	            if($delete){
	            	$row->action .= '<button onClick="master_delete_menu(\''.site_url().'configuration/menu/delete/'.(encode($row->id_seq)).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	            }

	          array_push($items, $row);  
			}
		}
		$result['rows']=$items;
		return $result;
	}

	function get_list_children($pi) {
		$items  = array();

		$sql 	= "select  * FROM core.t_mtr_menu 
				   WHERE parent_id = $pi and  status=1 
				   ORDER BY ".'"menu_order"'." ASC";
				   
		$query 	= $this->db->query($sql)->result();

		
		if($query){
			foreach ($query as $row){
				$has_child 	  = $this->check_parent($row->id_seq);
	            if($has_child){
					$row->children = $this->get_list_children($row->id_seq);
				}

				$parent = $row->parent_id;

		        if($row->parent_id == '' || $row->parent_id = null){
		            $parent = 0;
		        }

		        $edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu','edit');
	            $delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu','delete');

	            $row->action = '';
	            if($edit){
	            	$row->action .= '<button onClick="master_edit(\''.site_url().'configuration/menu/edit/'.(encode($row->id_seq)).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button>&nbsp;';
	            }

	            if($delete){
	            	$row->action .= '<button onClick="master_delete_menu(\''.site_url().'configuration/menu/delete/'.(encode($row->id_seq)).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	            }
					
				array_push($items, $row);
			}
		}
		
		return $items;
	}

	public function check_parent($parent_id)
	{
		$row=$this->db->query("select * from core.t_mtr_menu where parent_id=$parent_id")->num_rows();

		if($row>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function select_menu_detail($id,$parent)
	{
		$data=array();
		$qry="select d.status as privilege_status, d.id_seq as privilege_id, b.name as action_name, c.name as menu_name, * from core.t_mtr_menu_detail a
		left join core.t_mtr_menu_action b on a.action_id=b.id_seq
		left join core.t_mtr_menu c on a.menu_id=c.id_seq
		left join  core.t_mtr_privilege d on a.id_seq=d.menu_detail_id 
		where   a.menu_id=$id and c.parent_id=$parent and a.status=1 
		order by ".'"menu_order"'." asc";

		$html="<div>";
		$result=$this->db->query($qry)->result();

		if($result)
		{
			foreach($result as $row)
			{

					if ($row->privilege_status==1)
					{
						// $checkbox='<input type="checkbox" checked onClick="update(\''.$row->privilege_id.'\')" >';
						$checkbox='halah';
					}
					else
					{
						// $checkbox='<input type="checkbox"  onClick="update(\''.$row->privilege_id.'\')" >';	
						$checkbox='hilih';	
					}

					$html.=" ".$checkbox;					

				$data[]=$checkbox;
			}

		}
		$html.="</div>";

		return $html;
	}

	public function detail_edit($id)
	{
		$result=array();
		$items=array();

		$qry=$this->db->query("select * from core.t_mtr_menu where id_seq=$id and status=1");

		return $qry->row();
	}

	public function sub_detail($id)
	{
		return $this->db->query('SELECT
			MA.id_seq,MA.name
			FROM
			core.t_mtr_menu_detail MD
			JOIN core.t_mtr_menu_action MA ON MA.id_seq=MD.action_id
			JOIN core.t_mtr_menu M ON MD.menu_id=M.id_seq
			WHERE
			MD.menu_id ='.$id.' AND M.status=1 AND MD.status=1')->result();
	}

}

/* End of file M_menu.php */
/* Location: ./application/models/M_menu.php */