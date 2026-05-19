<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/classes/Functions.php';

class Settings {
    private $db;
    private $functions;
    private static $cache = [];

    public function __construct() {
        $this->db = Database::getInstance();
        $this->functions = Functions::getInstance();
    }

    public function get($key, $default = null) {
        if (isset(self::$cache[$key])) { return self::$cache[$key]; }
        $sql = "SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1";
        $stmt = $this->db->query($sql, ['key' => $key]);
        $result = $stmt->fetch();
        $value = $result ? $result['setting_value'] : $default;
        self::$cache[$key] = $value;
        return $value;
    }

    public function set($key, $value, $group = 'general', $autoload = true) {
        $sql = "INSERT INTO settings (setting_key, setting_value, setting_group, is_autoload, created_at, updated_at) VALUES (:key, :value, :group, :autoload, NOW(), NOW()) ON DUPLICATE KEY UPDATE setting_value = :value2, setting_group = :group2, updated_at = NOW()";
        $result = $this->db->query($sql, [
            'key' => $key, 'value' => $value, 'group' => $group, 'autoload' => $autoload ? 1 : 0,
            'value2' => $value, 'group2' => $group
        ]);
        self::$cache[$key] = $value;
        return $result;
    }

    public function getAll($group = null) {
        $sql = "SELECT * FROM settings";
        $params = [];
        if ($group) { $sql .= " WHERE setting_group = :group"; $params['group'] = $group; }
        $sql .= " ORDER BY setting_group, setting_key";
        return $this->db->query($sql, $params)->fetchAll();
    }

    public function getGroups() {
        $sql = "SELECT DISTINCT setting_group FROM settings ORDER BY setting_group";
        return array_column($this->db->query($sql)->fetchAll(), 'setting_group');
    }

    public function delete($key) {
        unset(self::$cache[$key]);
        return $this->db->query("DELETE FROM settings WHERE setting_key = :key", ['key' => $key]);
    }
}
