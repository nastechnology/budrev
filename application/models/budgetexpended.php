<?php

class BudgetExpended extends Eloquent {
	public static $timestamps = false;
	public static $table = 'budget_expended';

	public function budget()
	{
		return $this->belongs_to('Budget');
	}
}