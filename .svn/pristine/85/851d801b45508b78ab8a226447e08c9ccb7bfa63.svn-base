<?php
/**
 * Sitemap地图类
 * @author Satan <zhangxinhe91@qq.com>
 */
class SitemapxmlModel extends BpfModel
{
  /**
   * 获取整个Sitemap地图合集
   * @return string
   */
  public function getXML1()
  {
    $mysqlModel = $this->getModel('mysql');
    header('Content-Type: text/xml');
    $xml = '';
    $xml .= "<?xml version='1.0' encoding='UTF-8'?>";
    $xml .= "<sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";
    $xml .= $this->_getSitemapCeil(url('default/sitemap/cate/1', true));
    $count = $mysqlModel->getSqlBuilder()
        ->select('COUNT(0)')
        ->from('products')
        ->where('status', 1)
        ->query()
        ->field();
    $i = 0;
    for ( ; $count > 0; $count = $count - 2000) {
      $i++;
      $xml .= $this->_getSitemapCeil(url('default/sitemap/product/' . $i, true));
    }
    $xml .= "</sitemapindex>";
    return $xml;
  }

  /**
   * 获取某一类型的地图
   * @param string $type cate(分类),products(产品)
   * @param string $page 页码
   * @return string
   */
  public function getXML2($type, $page = 1)
  {
    $mysqlModel = $this->getModel('mysql');
    $setting = array(
      'changefreq' => 'weekly',
      'priority' => '1.0',
    );
    switch($type) {
      case 'cate' :
        if ($page != 1) {
          return BPF_NOT_FOUND;
        }
        $cate = $mysqlModel->getSqlBuilder()
            ->select('*')
            ->from('categories')
            ->where('status', 1)
            ->query()
            ->all();
        foreach ($cate as $sa) {
          $sa->link = urlCategory($sa);
        }
        return $this->getXML3($cate, $setting);
        
      case 'product' :
        $products = $mysqlModel->getSqlBuilder()
            ->select('*')
            ->from('products')
            ->where('status', 1)
            ->limitPage(2000, $page)
            ->orderby('updated DESC')
            ->query()
            ->all();
        if (!$products) {
          return BPF_NOT_FOUND;
        }
        foreach ($products as $sa) {
          $sa->link = urlProduct($sa);
          $sa->link_click = urlProduct($sa, 'click');
          $sa->created = $sa->updated;
        }
        return $this->getXML3($products, $setting);
        
        break; 
    }
  }
  
  /**
   * 生成子地图
   * @param object $arr 链接集
   * @param array $setting sitemap时间权重等参数
   * @return string
   */
  public function getXML3($arr, $setting)
  {
    $xml = '';
    header('Content-Type: text/xml');
    $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    foreach ($arr as $v) {
      $xml .= $this->_getUrlXML($v->link, $v->created, $setting);
    }
    $xml .= '</urlset>' . PHP_EOL;
    return $xml;
  }
  
  private function _getSitemapCeil($url)
  {
    $xml = "<sitemap>". PHP_EOL;
    $xml .= "<loc>" . $url . "</loc>". PHP_EOL;
    $xml .= "<lastmod>" . date('Y-m-d', REQUEST_TIME) ."</lastmod>". PHP_EOL;
    $xml .= "</sitemap>". PHP_EOL;
    return $xml;
  }

  private function _getUrlXML($url, $timestamp, $setting = null)
  {
    global $domainUrl;
    $url = strtr($url, array(
      ' ' => '-',
    ));
    $xml = '<url>' . PHP_EOL;
    $xml .= '<loc>' . url($url) . '</loc>' . PHP_EOL;
    $xml .= '<lastmod>' . date('Y-m-d', $timestamp) . '</lastmod>' . PHP_EOL;
    $xml .= '<changefreq>' . $setting['changefreq'] . '</changefreq>' . PHP_EOL;
    $xml .= '<priority>' . $setting['priority'] . '</priority>' . PHP_EOL;
    $xml .= '</url>' . PHP_EOL;
    return $xml;
  }
}