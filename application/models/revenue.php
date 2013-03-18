<?php

class Revenue extends Eloquent {
	public static $timestamps = false;
	public static $table = 'revenue';

	public function received()
	{
		return $this->has_many('RevenueReceived');
	}

	public function proposed()
	{
		return $this->has_one('RevenueProposed');
	}
}