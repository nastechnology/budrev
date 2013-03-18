<?php

class Budget extends Eloquent {
	public static $timestamps = false;
	public static $table = 'budget';

	public function expended()
	{
		return $this->has_many('BudgetExpended');
	}

	public function proposed()
	{
		return $this->has_one('BudgetProposed');
	}
}