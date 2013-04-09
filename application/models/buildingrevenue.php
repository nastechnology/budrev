<?php

class BuildingRevenue extends Eloquent {
	public static $timestamps = false;
	public static $table = "building_revenue";

	public function building()
    {
    	return $this->belongs_to('Building');
    }

    public function expended()
	{
		return $this->has_many('BuildingRevenueExpended');
	}
}