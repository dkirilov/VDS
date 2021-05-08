<?php

namespace VDS;

interface iClient{
	public function getStream($file_path);
	public function getFilesList($dir_path);
	public function upload(string $file_name, string $source_file_path);
	public function getFreeSpace();
}