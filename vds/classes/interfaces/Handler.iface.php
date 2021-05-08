<?php

namespace VDS;

interface iHandler{
	public static function getInstance();
	public static function authorize($db_instance);
}