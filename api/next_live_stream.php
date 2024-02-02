<?php

// https://stackoverflow.com/a/42189064

function json_response($feed) {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($feed);
}

$youtube_feed_string = file_get_contents('https://www.youtube.com/feeds/videos.xml?channel_id=UCJGsHxYVN2cwmA9ds13vmyw');
$youtube_feed_xml = simplexml_load_string($youtube_feed_string);

$next_live_stream = [
  'title' => '',
  'url' => '',
  'embedUrl' => '',
  'videoId' => '',
  'published' => '',
];
foreach($youtube_feed_xml->entry as $item) {
  $title = (string)$item->title;
  $yt_group = $item->children('yt', true);
  $video_id = (string)$yt_group->videoId;

  if (strpos(mb_strtolower($title), 'на живо') !== false) {
    $next_live_stream = [
      'title' => $title,
      'url' => (string)$item->link->attributes()->href,
      'embedUrl' => 'https://www.youtube-nocookie.com/embed/' . $video_id,
      'videoId' => $video_id,
      'published' => (string)$item->published,
    ];
    break;
  }
}


json_response($next_live_stream);
