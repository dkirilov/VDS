<?php

namespace VDS;

class CacheManager{
	public static function cache(string $name, array $contents, int $expires_on){
		$data = array(
			'contents' => $contents,
			'expires' => $expires_on
		);

		$fpath = CACHE_DIR . $name . '.vdsc';

		$caching_succeeded = file_put_contents($fpath, serialize($data)) !== FALSE;

		return $caching_succeeded;
	}

	public static function getCache(string $name, string $ckey = null){
		$fpath = CACHE_DIR . $name . '.vdsc';
		if(file_exists($fpath)){
			$data = unserialize(file_get_contents($fpath));
			if(!empty($data)){
				if(self::isOutdated($data['expires'])){
					return 'out-of-date';
				}

				if(!empty($ckey) && array_key_exists($ckey, $data['contents'])){
					return $data['contents'][$ckey];
				}

				return $data['contents'];
			}
		}

		return false;
	}

	public static function unCache(string $name, string $ckey = null){
		$fpath = CACHE_DIR . $name . '.vdsc';

		if(file_exists($fpath)){
			if(empty($ckey)){
				return unlink($fpath);
			}else{
				$data = file_get_contents($fpath);
				if(!empty($data) && array_key_exists($ckey, $data['contents'])){
					unset($data['contents'][$ckey]);

					$is_unset = !isset($data['contents'][$ckey]);
					$is_saved = self::cache($name, $data['contents'], $data['expires']);

					return $is_unset && $is_saved;
				}
			}
		}

		return false;
	}

	public static function cacheExists(string $name, string $ckey = null){
		$cache = self::getCache($name, $ckey);
		return !empty($cache) && $cache != 'out-of-date';
	}

	private static function isOutdated(int $timestamp){
		return $timestamp < time();
	}

}
