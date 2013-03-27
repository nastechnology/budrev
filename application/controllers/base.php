<?php

class Base_Controller extends Controller {

	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

	/* Private Methods */

	protected function _getProposed($type=null){
		if($type == "budget"){
			return BudgetProposed::count();
		} else {
			// type equals revenue
			return RevenueProposed::count();
		}
	}

	protected function _getTotal($type=null){
		if($type == "budget"){
			return Budget::count();
		} else {
			// type equals revenue
			return Revenue::count();
		}
	}

	/* End Private Methods */
	

}