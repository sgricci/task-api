<?php

class objList extends \Illuminate\Database\Eloquent\Model {
	protected $table = "list";
	public $timestamps = false;

	public function tasks() {
		return $this->hasMany('Task');
	}
}