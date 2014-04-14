<?php

class BuildingBudgetExpended extends Eloquent {
	public static $timestamps = false;
	public static $table = "building_budget_expended";

	 public function buildingbudget()
	{
		return $this->belongs_to('BuildingBudget');
	}

	// public function expended()

}