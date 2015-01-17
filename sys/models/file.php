<?php
/**
 * 文件存储类
 * @author Bun <bunwong@qq.com>
 */
class FileModel extends BpfModel
{
  /**
   * 获取目录下所有文件
   * @param string $type 目录
   * @param array $fileIds 文件ID数组
   * @return array
   */
  public function getFiles($type, $fileIds = null)
  {
    $params = array(
      'type' => $type,
    );
    if (is_array($fileIds)) {
      if (empty($fileIds)) {
        return array();
      }
      $params['file'] = json_encode(array_values($fileIds));
    }
    $url = $this->serviceUrl . '/files?' . http_build_query($params);
    $result = $this->get($url);
    return $result && is_object($result) && isset($result->result) ? $result->result : array();
  }

  /**
   * 获取文件信息
   * @param string $type 目录
   * @param string $fileId 文件ID
   * @return object
   */
  public function getFile($type, $fileId)
  {
    $url = $this->serviceUrl . '/file?' . http_build_query(array(
      'type' => $type,
      'file' => $fileId,
    ));
    $result = $this->get($url);
    return $result && is_object($result) && isset($result->file) ? $result->file : false;
  }

  /**
   * 读取文件内容
   * @param string $type 目录
   * @param string $fileId 文件ID
   * @param string $encoding 文件编码
   * @return string
   */
  public function read($type, $fileId, $encoding = null)
  {
    $url = $this->serviceUrl . '/read?' . http_build_query(array(
      'type' => $type,
      'file' => $fileId,
      'encoding' => $encoding,
    ));
    $result = $this->get($url);
    return $result && is_object($result) && isset($result->content) ? $result->content : false;
  }

  /**
   * 写入文件内容
   * @param string $type 目录
   * @param string $filename 文件名
   * @param string $content 文件内容
   * @return string/bool
   */
  public function write($type, $filename, $content)
  {
    $params = array(
      'type' => $type,
      'filename' => $filename,
      'content' => base64_encode($content),
      'created' => REQUEST_TIME,
      'updated' => REQUEST_TIME,
    );
    $url = $this->serviceUrl . '/file';
    $result = $this->post($url, $params, false);
    return $result && is_object($result) && isset($result->file) ? $result->file : false;
  }

  /**
   * 上传文件
   * @param string $type 目录
   * @param string $filename 源文件路径
   * @param string $subpath 子目录名
   * @return string/bool
   */
  public function upload($type, $filename, $subpath = null)
  {
    if (!is_file($filename) || !is_readable($filename)) {
      return false;
    }
    $content = file_get_contents($filename);
    $filename = trim(isset($subpath) ? $subpath : date('Ymd', REQUEST_TIME), '/') . '/' . basename($filename);
    return $this->write($type, $filename, $content);
  }

  /**
   * 删除文件
   * @param string $type 目录
   * @param array/string $fileIds 文件ID或文件ID数组
   * @return bool
   */
  public function delete($type, $fileIds)
  {
    if (!is_array($fileIds)) {
      $fileIds = array($fileIds);
    }
    if (empty($fileIds)) {
      return true;
    }
    $url = $this->serviceUrl . '/file?' . http_build_query(array(
      'type' => $type,
      'file' => json_encode(array_values($fileIds)),
    ));
    $result = $this->del($url);
    return $result && is_object($result) && isset($result->result) ? $result->result : false;
  }
}
