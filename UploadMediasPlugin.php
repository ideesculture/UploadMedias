<?php


class UploadMediasPlugin extends BaseApplicationPlugin
{
	# -------------------------------------------------------
	private $opo_config;
	private $ops_plugin_path;

	# -------------------------------------------------------
	public function __construct($ps_plugin_path)
	{
		$this->ops_plugin_path = $ps_plugin_path;
		$this->description = "";
		parent::__construct();
		$ps_plugin_path = __CA_BASE_DIR__ . "/app/plugins/UploadMedias";

		if (file_exists($ps_plugin_path . '/conf/local/UploadMedias.conf')) {
			$this->opo_config = Configuration::load($ps_plugin_path . '/conf/local/UploadMedias.conf');
		} else {
			$this->opo_config = Configuration::load($ps_plugin_path . '/conf/UploadMedias.conf');
		}
	}
	# -------------------------------------------------------
	/**
	 * Override checkStatus() to return true - the ampasFrameImporterPlugin plugin always initializes ok
	 */
	public function checkStatus()
	{
		return array(
			'description' => $this->getDescription(),
			'errors' => array(),
			'warnings' => array(),
			'available' => ((bool)$this->opo_config->get('enabled'))
		);
	}

	# -------------------------------------------------------
	/**
	 * Insert activity menu
	 */
	public function hookRenderMenuBar($pa_menu_bar)
	{
		//
		if ($o_req = $this->getRequest()) {
			
			$va_menu_items = array();				
			$va_menu_items['upload_medias'] = array(
				'displayName' => _t('Uload Medias'),
				"default" => array(
					'module' => 'UploadMedias',
					'controller' => 'Upload',
					'action' => 'Index'
				)
			);

			$pa_menu_bar["Import"]["navigation"]["upload_medias"] = $va_menu_items['upload_medias'];
		}
		//var_dump($pa_menu_bar["Import"]["navigation"]);die();
		return $pa_menu_bar;
	}

	# -------------------------------------------------------
	/**
	 * Get plugin user actions
	 */

	static public function getRoleActionList() {
		return array(
			'can_use_upload_medias_plugin' => array(
				'label' => "Can use UploadMedias plugin",
				'description' => "Can use UploadMedias plugin"
			),
		);
	}

	# -------------------------------------------------------
	/**
	 * Add plugin user actions
	 */
	public function hookGetRoleActionList($pa_role_list) {
		$pa_role_list['can_use_upload_medias_plugin'] = array(
			'label' => _t('Plugin UploadMedias'),
			'description' => _t('Actions pour le plugin UploadMedias'),
			'actions' => UploadMediasPlugin::getRoleActionList()
		);

		return $pa_role_list;
	}
}

?>