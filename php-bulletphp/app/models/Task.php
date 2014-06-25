<?php

class Task extends \Illuminate\Database\Eloquent\Model {
	protected $table = "task";

	public $timestamps = false;

	public function objList() {
		return $this->belongsTo('objList');
	}
}