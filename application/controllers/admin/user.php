<?php

class Admin_User_Controller extends Base_Controller {
	
	public function action_index()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$users = User::where('building_id','!=','0')->get();
			if(sizeof($users) > 0){
				$buildings = Building::all();
				$arrBuildings =  array();
				foreach ($buildings as $value) {
					$arrBuildings[$value->id] = $value->building;
				}
				return View::make('admin.userindex', array('users'=> $users,'buildings'=>$arrBuildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			} else {
				return View::make('admin.userindex2')->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			return View::make('admin.index2');
		}
	}

	public function action_add()
	{
		$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
		$rBadge = $this->_getTotal() - $this->_getProposed();
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			if(Input::has('submit')){
				// User Form Submitted
				$user = new User();
				$user->name = Input::get('name');
				$user->username = Input::get('uid');
				$user->email = Input::get('email');
				$user->building_id = Input::get('building');

				$user->save();
				if(isset($user->id)){
					// Saved correctly
					Session::flash('status_success', 'Successfully added '.$user->name);
					return Redirect::to('/admin/user/add');
				} else {
					// Save Unsuccessful
					Session::flash('status_error', 'unsuccessfully added '.$user->name);
					return Redirect::to('/admin/user/add');
				}
			} else {
				// Add User Form
				$buildings = Building::all();
				return View::make('admin.useradd', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			return View::make('admin.index2');
		}
	}

	public function action_delete()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			if(isset($_GET['id'])){
				$user = User::find($_GET['id']);
				$name = $user->name;
				$user->delete();
				Session::flash('status_success', 'Successfully removed user '.$name);
				return View::make('admin.userindex');
			} else {
				Session::flash('status_error', 'There was a problem removing the user');
				return View::make('admin.index2');
			}
		} else {
		     Session::flash('login_error','You do not have access to this app.');
			return View::make('admin.index2');
		}
	}

}