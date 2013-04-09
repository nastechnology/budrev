<?php

class BuildingRevenueExpended extends Eloquent {
	public static $timestamps = false;
	public static $table = "building_revenue_expended";

	 public function buildingRevenue()
	{
		return $this->belongs_to('BuildingRevenue');
	}

	// public function expended()

}