<?php
header('Content-Type: application/json; charset=utf-8');
$url = filter_var($_GET['url'] ?? '', FILTER_VALIDATE_URL);
if (!$url) { echo json_encode(['ok'=>false]); exit; }

$ctx = stream_context_create(['http'=>['timeout'=>3, 'user_agent'=>'FunCodeLabBot/1.0']]);
$html = @file_get_contents($url, false, $ctx);
if (!$html) { echo json_encode(['ok'=>false]); exit; }

$meta = [
  'title' => '',
  'desc'  => '',
  'image' => ''
];
if (preg_match('/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)/i', $html, $m)) $meta['title']=$m[1];
if (preg_match('/<meta[^>]+property=["\']og:description["\'][^>]+content=["\']([^"\']+)/i', $html, $m)) $meta['desc']=$m[1];
if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)/i', $html, $m)) $meta['image']=$m[1];

echo json_encode(['ok'=>true,'meta'=>$meta]);
