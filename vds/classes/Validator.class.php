<?php

namespace VDS;

class Validator{
    public static $valid_video_extensions = array('mp4', 'webm');
    public static $valid_video_types = array("video/mp4", "video/webm");

 	public static function validateVideoType(string $video_mime_type){
 		if(!in_array($video_mime_type, self::$valid_video_types)){
 			throw new \Exception("The video file that you're trying to upload has invalid MIME type <b>$video_mime_type</b>. Valid MIME types are: <b>". implode(",", $valid_mime_types) ."</b>.");
 		}
 	}

 	public static function validateVideoExtension(string $video_file_name){
 		if(!self::isValidVideoFile($video_file_name)){
 			throw new \Exception("The video file that you're trying  to upload has invalid extension <b>.$video_ext</b>! Valid extensions are: <b>". implode(",", $valid_extensions) . "</b>.");
 		}
 	}

    public static function isValidVideoFile(string $file_name){
        $ext = strtolower( pathinfo($file_name, PATHINFO_EXTENSION) );
        return in_array($ext, self::$valid_video_extensions);
    }
}
