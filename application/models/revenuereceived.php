<?php

class RevenueReceived extends Eloquent {
	public static $timestamps = false;
	public static $table = 'revenue_received';

	public function revenue()
	{
		return $this->belongs_to('Revenue');
	}
}