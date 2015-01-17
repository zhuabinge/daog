<?php
$config = array();

$config['debug'] = true;

$config['hostname'] = 'dev.ec61.com';
$config['session.cookie_domain'] = '.ec61.com';
$config['static.url'] = 'http://files.ttgg.com';

$config['service.url'] = 'http://10.16.255.66:8089/';
$config['service.token'] = '12345678';

$config['routers'] = array(
  'item' => 'product/item',
  'search' => 'product/search',
  'cate' => 'product/category',
  'topic' => 'product/channel',
  'tag' => 'product/tag',
  'adclick' => 'default/adclick',
  'help.html' => 'page/help',
  'contact.html' => 'page/contact',
  'about.html' => 'page/about',
  'co.html' => 'page/co',
  'job.html' => 'page/job',
  'service.html' => 'page/service',
  'sitemap.xml' => 'default/sitemap',
  'rssfeed.xml' => 'default/rss',
  'categories.html' => 'categories',
);

$config['merchant.rid'] = 25;

$config['domains'] = array(
 // '192.168.88.127' => array(
  'm.dev.ec61.com' => array(
    'theme' => 'mobile',
  ),
);
