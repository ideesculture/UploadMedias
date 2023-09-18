<?php
/**
 * UploadMedias plugin
 */

class UploadController extends ActionController
{
	# -------------------------------------------------------
	protected $opo_config; // plugin configuration file
	# -------------------------------------------------------
	#
	# -------------------------------------------------------
	public function __construct(&$po_request, &$po_response, $pa_view_paths = null) {
		parent::__construct($po_request, $po_response, $pa_view_paths);

		/*if (!$this->request->user->canDoAction('can_use_upload_medias_plugin')) {
			$this->response->setRedirect($this->request->config->get('error_display_url') . '/n/2320?r=' . urlencode($this->request->getFullUrlPath()));
			return;
		}*/
		$ps_plugin_path = __CA_BASE_DIR__ . "/app/plugins/UploadMedias";

		if (file_exists($ps_plugin_path . '/conf/local/UploadMedias.conf')) {
			$this->opo_config = Configuration::load($ps_plugin_path . '/conf/local/UploadMedias.conf');
		} else {
			$this->opo_config = Configuration::load($ps_plugin_path . '/conf/UploadMedias.conf');
		}
	}


	# -------------------------------------------------------
	public function Index() {
		$this->render('index_html.php');
	}

	public function Uploading() {
		if (!empty($_FILES)) {
		    $tempFile = $_FILES['file']['tmp_name'];          //3             
		    $targetPath = __CA_BASE_DIR__."/import/";  //4
		    $targetFile =  $targetPath. $_FILES['file']['name'];  //5
		    move_uploaded_file($tempFile,$targetFile); //6
		}
		var_dump($targetFile);
		die("ici");
		$this->render('uploading_html.php');
	}

	public function DeleteMedia() {
		header('Content-type: application/json');
		$name = $this->request->getParameter("name", pString);
		$targetPath = __CA_BASE_DIR__."/import/";  //4
		if(is_file($targetPath.$name)) {
			unlink($targetPath.$name);
			return json_encode(["success" => true]);
		} else {
			return json_encode(["success" => false]);
		}
		exit();
	}

	public function AjaxGetMedias() {
		// Header for JSON output
		header('Content-type: application/json');

		$targetPath = __CA_BASE_DIR__."/import/";  //4
		$files = scandir($targetPath);
		$files = array_diff(scandir($targetPath), array('.', '..'));
		$files = array_values($files);
		$files = array_map(function($file) use ($targetPath) {
			return [
				'name' => $file,
				'size' => filesize($targetPath.$file),
				'url' => __CA_URL_ROOT__."/import/".$file
			];
		}, $files);
		echo json_encode($files);
		exit();
	}
}