<?php

class BudgetProposed extends Eloquent {
	public static $timestamps = false;
	public static $table = 'budget_proposed';

	public function budget()
	{
		return $this->belongs_to('Budget');
	}
}