<?php

class Building extends Eloquent{
	public static $timestamps = false;

	public function buildingBudgets()
	{
		return $this->has_many('BuildingBudgets');
	}

	public function buildingBudgetAmount()
	{
		return $this->belongs_to('BuildingBudgetAmount');
	}
}