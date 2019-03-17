<?php
/*
	Bitrix24 REST API tutorial: https://dev.1c-bitrix.ru/rest_help/

	Realized methods:
	
	batch
	task.item.add
	task.item.getdata
	task.item.update
	task.checklistitem.add
	task.dependence.add
	im.message.add
	lists.element.get
	lists.element.update
	lists.field.get
	lists.field.update
	department.get
	user.get
	crm.lead.add
*/

class B24API {
	private static $domain = '';
	protected static $adminId = '';
	private static $tokenIn = '';
	private static $url = '';

	protected static $batchData = array();
	protected static $responseData = array();

	protected function __construct($domain, $adminId, $tokenIn) {
		if (empty($domain) || empty($adminId) || empty($tokenIn)) return false;

		self::$domain = $domain;
		self::$adminId = $adminId;
		self::$tokenIn = $tokenIn;
		self::$url = 'https://'.$domain.'/rest/'.$adminId.'/'.$tokenIn.'/';

		if(isset($_REQUEST['auth']['application_token']) && (
			!isset($_REQUEST['tokenOut']) ||
			$_REQUEST['auth']['application_token'] != $_REQUEST['tokenOut']
		)) return false;

		return true;
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/general/batch.php
	*/
	protected function b24_batch() {
		self::$responseData = array();

		self::$batchData = array_chunk(self::$batchData, 50, true);
		if (empty(self::$url) || (!empty(self::$batchData) && !is_array(self::$batchData))) return false;

		for ($i=0; $i < count(self::$batchData); $i++) {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_POST => 1,
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => self::$url.'batch.json',
				CURLOPT_POSTFIELDS => http_build_query(array('cmd' => self::$batchData[$i])),
			));

			$response = curl_exec($curl);
			curl_close($curl);

			if (!empty($response)) self::$responseData[] = json_decode($response, true);
		}

		self::$batchData = array();
		return self::$responseData;
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/tasks/task/item/add.php
	*/
	protected function b24_taskItemAdd(
		$title = '',
		$responsible_id = '',
		$description = '',
		$deadline = '',
		$start_date_plan = '',
		$end_date_plan = '',
		$priority = '',
		$accomplices = '',
		$auditors = '',
		$tags = '',
		$allow_change_deadline = '',
		$task_control = '',
		$parent_id = '',
		$depends_on = '',
		$group_id = '',
		$time_estimate = '',
		$created_by = '',
		$decline_reason = '',
		$status = '',
		$duration_plan = '',
		$duration_type = '',
		$mark = '',
		$allow_time_tracking = '',
		$add_in_report = '',
		$site_id = '',
		$match_work_time = '',
		$batch_i = ''
	) {
		if (empty($title) || empty($responsible_id) || $responsible_id <= 0) return false;

		$taskData['TITLE'] = $title;

		$taskData['RESPONSIBLE_ID'] = $responsible_id;

		if (!empty($description)) $taskData['DESCRIPTION'] = $description;

		if (!empty($deadline)) {
			$deadline = new DateTime($deadline);
			$taskData['DEADLINE'] = $deadline->format(DateTime::ATOM);
		}

		if (!empty($start_date_plan)) {
			$start_date_plan = new DateTime($start_date_plan);
			$taskData['START_DATE_PLAN'] = $start_date_plan->format(DateTime::ATOM);
		}

		if (!empty($end_date_plan)) {
			$end_date_plan = new DateTime($end_date_plan);
			$taskData['END_DATE_PLAN'] = $end_date_plan->format(DateTime::ATOM);
		}

		if (!empty($priority) && $priority > 0) $taskData['PRIORITY'] = $priority;

		if (is_array($accomplices) && count($accomplices) > 0) $taskData['ACCOMPLICES'] = $accomplices;

		if (is_array($auditors) && count($auditors) > 0) $taskData['AUDITORS'] = $auditors;

		if (is_array($tags) && count($tags) > 0) $taskData['TAGS'] = $tags;

		if (!empty($allow_change_deadline) && $allow_change_deadline == 1) $taskData['ALLOW_CHANGE_DEADLINE'] = 'Y';

		if (!empty($task_control) && $task_control == 1) $taskData['TASK_CONTROL'] = 'Y';

		if (!empty($parent_id) && $parent_id > 0) $taskData['PARENT_ID'] = $parent_id;

		if (!empty($depends_on) && $depends_on > 0) $taskData['DEPENDS_ON'] = $depends_on;

		if (!empty($group_id)) $taskData['GROUP_ID'] = $group_id;

		/*$time_estimate = '';*/

		if (!empty($created_by) && $created_by > 0) $taskData['CREATED_BY'] = $created_by;

		/*$decline_reason = '';*/

		/*$status = '';*/

		if (!empty($duration_plan) && $duration_plan > 0) {
			$taskData['DURATION_PLAN'] = $duration_plan;
			$taskData['DURATION_TYPE'] = $duration_type == 'hours' ? $duration_type : 'days';
		}

		if (!empty($mark) && $mark == 1) $taskData['MARK'] = 'P';
		else $taskData['MARK'] = '';

		if (!empty($allow_time_tracking) && $allow_time_tracking == 1) $taskData['ALLOW_TIME_TRACKING'] = 'Y';

		if (!empty($add_in_report) && $add_in_report == 1) $taskData['ADD_IN_REPORT'] = 'Y';

		if (!empty($site_id)) $taskData['SITE_ID'] = $site_id;

		if (!empty($match_work_time) && $match_work_time == 1) $taskData['MATCH_WORK_TIME'] = 'Y';

		if (empty($taskData)) return false;

		if (empty($batch_i)) self::$batchData[] = 'task.item.add?' . http_build_query(array('TASKDATA' => $taskData));
		else self::$batchData[$batch_i] = 'task.item.add?' . http_build_query(array('TASKDATA' => $taskData));
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/tasks/task/item/getdata.php
	*/
	protected function b24_taskItemGetData($taskId) {
		if (empty($taskId)) return false;

		$data['TASKID'] = $taskId;

		if (empty($data)) return false;
		self::$batchData[] = 'task.item.getdata?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/tasks/task/item/update.php
	*/
	protected function b24_taskItemUpdate($taskId, $taskData = array()) {
		if (empty($taskId) || empty($taskData)) return false;

		$data['TASKID'] = $taskId;
		$data['TASKDATA'] = $taskData;

		if (empty($data)) return false;
		self::$batchData[] = 'task.item.update?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/tasks/task/checklistitem/add.php
	*/
	protected function b24_taskChecklistItemAdd($taskId, $title, $sort_index = '', $is_complete = '') {
		if (empty($taskId) || empty($title)) return false;

		$data['TASKID'] = $taskId;
		$data['FIELDS']['TITLE'] = $title;
		if (!empty($sort_index)) $data['FIELDS']['SORT_INDEX'] = $sort_index;
		if (!empty($is_complete)) $data['FIELDS']['IS_COMPLETE'] = $is_complete;

		if (empty($data)) return false;
		self::$batchData[] = 'task.checklistitem.add?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/tasks/task/dependence/task_dependence_add.php
	*/
	protected function b24_taskDependenceAdd($idFrom, $idTo, $linkType = '00') {
		if (empty($idFrom) || empty($idTo) || $idFrom < 0 || $idTo < 0) return false;

		$data['taskIdFrom'] = $idFrom;
		$data['taskIdTo'] = $idTo;
		$data['linkType'] = in_array($linkType, array('00', '01', '02', '03')) ? $linkType : '00';

		if (empty($data)) return false;
		self::$batchData[] = 'task.dependence.add?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=93&LESSON_ID=7691#im_message_add
	*/
	protected function b24_imMessageAdd($user_id, $msg) {
		if (empty($user_id) || empty($msg)) return false;

		$data = array(
			"USER_ID" => $user_id,
			"MESSAGE" => $msg
		);

		if (empty($data)) return false;
		self::$batchData[] = 'im.message.add?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/lists/elements/lists_element_get.php
	*/
	protected function b24_listsElementGet($iblock_type_id, $iblock_code, $element_order = array(), $element_id = '', $filter = array()) {
		if (empty($iblock_type_id) || empty($iblock_code)) return false;

		$data['IBLOCK_TYPE_ID'] = $iblock_type_id;
		$data['IBLOCK_ID'] = $iblock_code;
		if (!empty($element_order) && is_array($element_order)) $data['ELEMENT_ORDER'] = $element_order;
		if (!empty($element_id)) $data['ELEMENT_ID'] = $element_id;
		if (!empty($filter) && is_array($filter)) $data['FILTER'] = $filter;

		if (empty($data)) return false;
		self::$batchData[] = 'lists.element.get?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/lists/elements/lists_element_update.php
	*/
	protected function b24_listsElementUpdate($iblock_type_id, $iblock_code, $element_id, $fields = array()) {
		if (empty($iblock_type_id) || empty($iblock_code) || empty($element_id)) return false;

		$data['IBLOCK_TYPE_ID'] = $iblock_type_id;
		$data['IBLOCK_ID'] = $iblock_code;
		$data['ELEMENT_ID'] = $element_id;
		if (!empty($fields) && is_array($fields)) $data['FIELDS'] = $fields;

		if (empty($data)) return false;
		self::$batchData[] = 'lists.element.update?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/lists/fields/lists_field_get.php
	*/
	protected function b24_listsFieldGet($iblock_type_id, $iblock_code, $field_id) {
		if (empty($iblock_type_id) || empty($iblock_code) || empty($field_id)) return false;

		$data['IBLOCK_TYPE_ID'] = $iblock_type_id;
		$data['IBLOCK_ID'] = $iblock_code;
		$data['FIELD_ID'] = $field_id;

		if (empty($data)) return false;
		self::$batchData[] = 'lists.field.get?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/lists/fields/lists_field_update.php
	*/
	protected function b24_listsFieldUpdate($iblock_type_id, $iblock_code, $field_id, $fields_update) {
		if (empty($iblock_type_id) || empty($iblock_code) || empty($field_id) || empty($fields_update)) return false;

		$data['IBLOCK_TYPE_ID'] = $iblock_type_id;
		$data['IBLOCK_ID'] = $iblock_code;
		$data['FIELD_ID'] = $field_id;
		$data['FIELDS'] = $fields_update;

		if (empty($data)) return false;
		self::$batchData[] = 'lists.field.update?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/departments/department_get.php
	*/
	protected function b24_departmentGet($sort = '', $order = '', $id = '', $name = '', $parent = '', $uf_head = '') {
		if (!empty($sort)) $data['sort'] = $sort;
		if (!empty($order)) $data['order'] = $order;
		if (!empty($id)) $data['ID'] = $id;
		if (!empty($name)) $data['NAME'] = $name;
		if (!empty($parent)) $data['PARENT'] = $parent;
		if (!empty($uf_head)) $data['UF_HEAD'] = $uf_head;

		self::$batchData[] = 'department.get?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/users/user_get.php
	*/
	protected function b24_userGet($sort = '', $order = '', $filter = array(), $admin_mode = '') {
		if (!empty($sort)) $data['sort'] = $sort;
		if (!empty($order)) $data['order'] = $order;
		if (!empty($filter) && is_array($filter)) $data['filter'] = $filter;
		if (!empty($admin_mode)) $data['admin_mode'] = $admin_mode;

		self::$batchData[] = 'user.get?' . http_build_query($data);
	}

	/*
		https://dev.1c-bitrix.ru/rest_help/crm/leads/crm_lead_add.php
	*/
	protected function b24_crmLeadAdd($fields = array(), $params = 'N') {
		if (!empty($fields) && is_array($fields)) $data['fields'] = $fields;

		$data['params']['REGISTER_SONET_EVENT'] = 'Y';
		if (!empty($params) || $params != 'N') $data['params']['REGISTER_SONET_EVENT'] = 'Y';

		self::$batchData[] = 'crm.lead.add?' . http_build_query($data);
	}

	private function __clone() {}
	private function __wakeup() {}
}

/*
	Example use this driver:

	require_once 'B24API.php';

	class SomeClass extends B24API {
		function __construct($domain, $adminId, $tokenIn) {
			parent::__construct($domain, $adminId, $tokenIn);
		}

		function message($to, $msg) {
			if (self::b24_imMessageAdd($to, $msg) === false) return false;
			return self::b24_batch();
		}
	}

	$someObj = @new SomeClass($_REQUEST['domain'], $_REQUEST['adminId'], $_REQUEST['tokenIn']);
	$someObj->message(1, 'Test message!');
*/
