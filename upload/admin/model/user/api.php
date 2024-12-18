<?php
class ModelUserApi extends Model {
	public function addApi(array $data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "api` SET name = '" . $this->db->escape($data['name']) . "', `key` = '" . $this->db->escape($data['key']) . "', status = '" . (bool)$data['status'] . "', date_added = NOW(), date_modified = NOW()");

		$api_id = $this->db->getLastId();

		if (isset($data['api_ip'])) {
			foreach ($data['api_ip'] as $ip) {
				if ($ip) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "api_ip` SET api_id = '" . (int)$api_id . "', ip = '" . $this->db->escape($ip) . "'");
				}
			}
		}

		return $api_id;
	}

	public function editApi(int $api_id, array $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "api` SET name = '" . $this->db->escape($data['name']) . "', `key` = '" . $this->db->escape($data['key']) . "', status = '" . (bool)$data['status'] . "', date_modified = NOW() WHERE api_id = '" . (int)$api_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "api_ip WHERE api_id = '" . (int)$api_id . "'");

		if (isset($data['api_ip'])) {
			foreach ($data['api_ip'] as $ip) {
				if ($ip) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "api_ip` SET api_id = '" . (int)$api_id . "', ip = '" . $this->db->escape($ip) . "'");
				}
			}
		}
	}

	public function deleteApi(int $api_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api` WHERE api_id = '" . (int)$api_id . "'");
	}

	public function getApi(int $api_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE api_id = '" . (int)$api_id . "'");

		return $query->row;
	}

	public function getApis(array $data = []) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "api`";

		$sort_data = [
			'name',
			'status',
			'date_added',
			'date_modified'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalApis() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "api`");

		return (int)$query->row['total'];
	}

	public function addApiIp(int $api_id, string $ip) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_ip` SET `api_id` = '" . (int)$api_id . "', `ip` = '" . $this->db->escape($ip) . "'");
	}

	public function getApiIps(int $api_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_ip` WHERE `api_id` = '" . (int)$api_id . "'");

		return $query->rows;
	}

	public function getApiSessions(int $api_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_session` WHERE `api_id` = '" . (int)$api_id . "'");

		return $query->rows;
	}

	public function addApiSession(int $api_id, array $data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_session` SET `api_id` = '" . (int)$api_id . "', `token` = '" . $this->db->escape($data['token']) . "', `date_added` = NOW(), `date_modified` = NOW()");
	}

	public function deleteApiSession(int $api_session_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api_session` WHERE `api_session_id` = '" . (int)$api_session_id . "'");
	}
}
