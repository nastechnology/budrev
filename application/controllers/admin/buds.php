<?php

class Admin_Buds_Controller extends Base_Controller {
	/**
	Function: Index
	Description: Default Action on load
	*/
	public function action_index()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$budgets = Budget::all();
			return View::make('admin.buds', array('budgets'=>$budgets))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}	
	}

	/**
	Function: Add
	Description: Add Budgets
	*/
	public function action_add()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			if(Input::has('submit')){
				$values = Input::get();
				
				$submit = array_pop($values);
				$budget = new Budget($values);

				$budget->save();

				if($budget->id){
					Session::flash('status_success',"Successfully added ".$budget->description." budget.");
				} else {
					Session::flash('status_error',"Error adding ".$budget->description." budget.");
				}
			} 
			
			return View::make('admin.budsadd')->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));			
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	/**
	Function: Edit
	Description: Edit Budgets
	*/
	public function action_edit()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();

			
			if(Input::has('submit')){
				
				$budget = Budget::find(Input::get('id'));
				$values = Input::get();
				$submit = array_pop($values);

				foreach ($values as $key => $value) {
					$budget->$key = $value;
				}

				if($budget->save()){
					Session::flash('status_success',"Successfully edited ".$budget->description." budget.");
				} else {
					Session::flash('status_error',"Error editting ".$budget->description." budget.");
				}
				return View::make('admin.buds', array('budgets'=>Budget::all()))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			} else {
				$budget = Budget::find($_GET['id']);
				return View::make('admin.budsedit', array('budget'=>$budget))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	/**
	Function: Delete
	Description: Delete Budgets
	*/

	public function action_delete()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$id = $_GET['id'];
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			if(Budget::find($id)->delete()){
				Session::flash('status_success', "Successfully deleted budget");
				return View::make('admin.buds', array('budgets'=>Budget::all()))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			} else {
				Session::flash('status_error',"Error deleting budget.");
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}
}