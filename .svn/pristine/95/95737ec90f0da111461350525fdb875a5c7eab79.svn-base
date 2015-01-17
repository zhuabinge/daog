<?php
/**
 * 应用配置服务类
 * @author Bun <bunwong@qq.com>
 */
class ConfigModel extends BpfModel
{
  private static $_values = null;
  private static $_set = array();

  private function _get()
  {
    if (!isset(self::$_values)) {
      $url = $this->serviceUrl . '/values';
      $result = parent::get($url);
      $values = $result && is_object($result) && isset($result->values) ? (array) $result->values : array();
      foreach ($values as &$v) {
        $v = unserialize($v);
      }
      self::$_values = $values;
    }
  }

  /**
   * 获取配置值
   * @param string $name 配置名
   * @param mixed $default 默认值
   * @return mixed
   */
  public function get($name = null, $default = null)
  {
    $this->_get();
    if (isset($name)) {
      return isset(self::$_values[$name]) ? self::$_values[$name] : $default;
    } else {
      return self::$_values;
    }
  }

  /**
   * 设置配置
   * @param string $name 配置名
   * @param mixed $value 配置值
   */
  public function set($name, $value)
  {
    $this->_get();
    self::$_values[$name] = $value;
    self::$_set[$name] = serialize($value);
  }

  /**
   * 保存配置
   * @return bool
   */
  public function save()
  {
    if (empty(self::$_set)) {
      return true;
    }
    $params = array(
      'values' => json_encode(self::$_set),
      'updated' => REQUEST_TIME,
    );
    self::$_set = array();
    $url = $this->serviceUrl . '/values';
    $result = $this->put($url, $params);
    return $result && is_object($result) && isset($result->result) ? $result->result : false;
  }
}
