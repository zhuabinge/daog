<?php
/**
 * 邮件服务类
 * @author Bun <bunwong@qq.com>
 */
class MailModel extends BpfModel
{
  /**
   * 发送
   * @param mixed $to 收件人
   * @param string $subject 邮件标题
   * @param string $body HTML 正文内容
   * @param string $text 纯文本正文内容
   * @return bool
   */
  public function send($to, $subject, $body, $text = '')
  {
    $toList = array();
    if (!is_array($to)) {
      $to = array($to);
    }
    foreach ($to as $mail => $value) {
      if (is_int($mail)) {
        $toList[] = $value;
      } else {
        $toList[] = $value . ' <' . $mail . '>';
      }
    }
    $params = array(
      'to' => implode(', ', $toList),
      'subject' => $subject,
      'body' => $body,
      'text' => $text,
    );
    $url = $this->serviceUrl . '/send';
    $result = $this->post($url, $params);
    return $result && is_object($result) && isset($result->result) ? $result->result : false;
  }
}
