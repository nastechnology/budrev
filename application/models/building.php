<?php

class Building extends Eloquent{
	public static $timestamps = false;

	public function buildingBudgets()
	{
		return $this->has_many('BuildingBudget');
	}

	public function buildingBudgetAmount()
	{
		return $this->belongs_to('BuildingBudgetAmount');
	}

	public function buildingRevenues()
	{
		return $this->has_many('BuildingRevenue');
	}
}