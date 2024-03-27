<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function url_api()
{
	return "https://demos.finnet.co.id/astra/index.php/api/registrasi?merchId=6600001&token=apitoken_/Lr63dMsrrrGOt8L8knDxrUMd1GohDbp";
}

function url_commit()
{
	return "https://demos.finnet.co.id/astra/index.php/api/commitReg?merchId=6600001&token=apitoken_/Lr63dMsrrrGOt8L8knDxrUMd1GohDbp";
}

function validateAjax()
{
	$ci =& get_instance();
	if (!$ci->input->is_ajax_request()) {
		  show_404();
		}
}

function json_added()
{
	echo json_encode(array(
		'code' => 200,
		'message' => 'Data berhasil ditambahkan',
	));
}

function json_edited()
{
	echo json_encode(array(
		'code' => 200,
		'message' => 'Success edit data',
	));
}

function json_deleted()
{
	echo json_encode(array(
		'code' => 200,
		'message' => 'Success delete data',
	));
}

function json_error()
{
	echo json_encode(array(
		'code' => 101,
		'message' => 'Please contact admin',
	));
}

function json_enabled()
{
	echo json_encode(array(
		'code' => 200,
		'message' => 'Data successfully enabled',
	));
}

function json_disabled()
{
	echo json_encode(array(
		'code' => 200,
		'message' => 'Data successfully disabled',
	));
}

function getSession()
{
	$ci =& get_instance();
	if ($ci->session->userdata('is_logged_in') != 1)
	{
		redirect('login','refresh');
		$ci->session->session_destroy();
	}
}

function listMenu($data,$parent_id = 0)
{
	$ci =& get_instance();
	if (isset($data[$parent_id])) {
		if($parent_id == 0){
			$ul = '<ul class="navigation navigation-main navigation-accordion">';
		}else{
			$ul = '<ul>';
		}

		$html  = "{$ul}";
		foreach ($data[$parent_id] as $v) {
			if($v->slug == null || $v->slug == ''){
				$link = '#';
			}else{
				$link = site_url().$v->slug;
			}

			$child  = listMenu($data, $v->menu_id);
			if ($v->slug == $ci->uri->uri_string()) {
				$html .= '<li class="active"><a href="'.$link.'" id="'.$v->menu_id.'"><i class="icon-'.$v->icon.'"></i> <span>'.$v->name.'</span></a>';
			}else{
				$html .= '<li><a href="'.$link.'" id="'.$v->menu_id.'"><i class="icon-'.$v->icon.'"></i> <span>'.$v->name.'</span></a>';
			}
			if($child){
				$html .= $child;
			}
			$html .= '</li>';
		}

		$html .= "</ul>";
		return $html;
	}else{
		return false;
	}
}

function checkUrlAccess($menu,$current_url){
	$CI     =& get_instance();
	$link   = array(
		'home'    => 'home',
		'profile' => 'profile'
	);

	foreach ($menu as $key => $val) {
		$data = array_filter($menu[$key], function ($item) {
			if ($item->slug == '#' || $item->slug == '') return false;
			return true; 
		});

		foreach ($data as $k => $v) {
			$link[$v->slug] = $v->slug;
		}
	}

	if(isset($link[''.$current_url.''])){
		return true;
	}else{
		return false;
	}
}

function btn_add($url)
{
	echo '<button type="button" onclick="open_modal_add(\''.$url.'\')" class="btn bg-astra btn-xs add" title="Add"><i class="icon-plus-circle2"></i> ADD</button>';
}

function create_btn($url,$title,$icon)
{
	echo '<button type="button" onclick="open_modal(\''.$url.'\')" class="btn bg-angkasa2 btn-xs" title="'.$title.'">'.$title.'</button>';
}

function btn_edit($url)
{
	echo '<button type="button" onclick="modal_edit(\''.$url.'\')" class="btn bg-info btn-xs" title="Edit"><i class="icon-pencil7"></i></button>';
}

function createBtnImport($url_import)
{
	// <a data-toggle="modal" href="remote.html" data-target="#modal">Click me</a>
	echo '<a href="'.$url_import.'" data-target="#modal_import" data-toggle="modal" class="btn bg-astra btn-xs import" title="Import"><i class="icon-cloud-upload2"></i> Import File</a>';
}

function createBtnAdd($urlAdd)
{
	echo '<a href="'.$urlAdd.'" data-target="#modal_add" data-toggle="modal" class="btn bg-astra btn-xs add" title="Tambah"><i class="icon-plus-circle2"></i> Tambah</a>';
}

function createBtnEdit($urlEdit)
{
	echo '<button link="'.$urlEdit.'" data-toggle="modal" class="btn btn-info btn-xs edit" title="Edit"><i class="icon-pencil7"></i></button>';
}

function createBtnChangePass($id)
{
	echo '<button link="'.$id.'" data-toggle="modal" class="btn btn-success btn-xs change_password" title="Change Password"><i class="icon-lock"></i></button>';
}

function createBtnDelete()
{
	echo '<button type="button" onclick="deleteItem()" class="btn bg-danger btn-xs" title="Delete"><i class="icon-trash"></i></button>';
}

function checkButton($current_url,$action,$menu){
	$button = array();

	foreach ($menu as $key => $val) {
		$data = array_filter($val{$key}->action, function ($item) {
			return true; 
		});

		foreach ($data as $k => $v) {
			$button[$v] = $v;
		}
	};

	if(isset($button[''.$current_url.''])){
		if(in_array($action, $button[''.$current_url.''], true)){
			return 1;
		}else{
			return 0;
		}
	}else{
		return 0;
	}
}

function adminOnly()
{
	$ci =& get_instance();
	if ($ci->session->userdata('level') != 1)
	{}
}

function encode($data){
  return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
}

function decode($base64){
  return base64_decode(strtr($base64, '-_', '+/'));
}

function dateIndo($tanggal)
{
		$bulan = array(
		1 =>  
		'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	$pecahkan = explode('/', $tanggal);
	
	return $bulan[(int)$pecahkan[1]].' '.$pecahkan[0];
}

function format_dateTimeDetik($date)
{
	return date("d F Y H:i:s", strtotime($date));
}