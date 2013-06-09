<?php

interface DBObject {
	
	public function save();
	
	public function delete();
	
	public static function find($id);
	
	public static function getAll();
	
}