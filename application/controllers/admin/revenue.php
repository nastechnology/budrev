<?php

class Admin_Revenue_Controller extends Base_Controller{
	
	public function action_index()
	{
		// List all Revenues
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$arrBuildings = Building::all();
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();

			if(Input::has('submit')){
				$BuildingBudAmount = new BuildingBudgetAmount();
				$BuildingBudAmount->building_id = Input::get('building');
				$BuildingBudAmount->amount = Input::get('amount');
				$BuildingBudAmount->fyyear = Input::get('fyyear');

				$BuildingBudAmount->save();
				if($BuildingBudAmount->id){
					Session::flash('status_success', 'Successfully saved the building budget total for '.$BuildingBudAmount->fyyear);
				} else {
					Session::flash('status_error', 'There was an error saving the building budget total');
				}
				return View::make('admin.revenue', array('buildings'=>$arrBuildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			} else {
				return View::make('admin.revenue', array('buildings'=>$arrBuildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::flash('login_error','You do not have access to this app.');
		    return View::make('admin.index2');
		}
	}
}