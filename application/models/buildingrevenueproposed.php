<?php

class BuildingRevenueProposed extends Eloquent {
	public static $timestamps = false;
	public static $table = "building_revenue_proposed";

	 public function buildingbudget()
	{
		return $this->belongs_to('BuildingRevenue');
	}

	// public function expended()

}
