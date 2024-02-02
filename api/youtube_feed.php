<?php

// https://stackoverflow.com/a/42189064

function json_feed($feed) {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($feed);
}

$youtube_feed_string = file_get_contents('https://www.youtube.com/feeds/videos.xml?channel_id=UCJGsHxYVN2cwmA9ds13vmyw');
$youtube_feed_xml = simplexml_load_string($youtube_feed_string);

$latest_feeds = [];
foreach($youtube_feed_xml->entry as $item) {
  $title = (string)$item->title;
  if (strpos(mb_strtolower($title), 'на живо') !== false) {
    // Skip live stream URLs
    continue;
  }

  $media_group = $item->children('media', true);
  $thumbnail = (string)$media_group->group->thumbnail->attributes()->url;

  $yt_group = $item->children('yt', true);
  $video_id = (string)$yt_group->videoId;

  $latest_feeds[] = [
    'title' => $title,
    'link' => (string)$item->link->attributes()->href,
    'videoId' => $video_id,
    'thumbnail' => $thumbnail,
    'published' => (string)$item->published,
  ];
}


json_feed($latest_feeds);
