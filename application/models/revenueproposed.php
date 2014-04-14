<?php

class RevenueProposed extends Eloquent {
	public static $timestamps = false;
	public static $table = "revenue_proposed";

	public function revenue()
	{
		return $this->belongs_to('Revenue');
	}
}