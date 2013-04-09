<?php

class BuildingBudgetAmount extends Eloquent {
	public static $timestamps = false;
	public static $table = "building_budget_amount";

    public function building()
    {
    	return $this->has_many('Building');
    }
}