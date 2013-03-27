<?php

class Admin_Revs_Controller extends Base_Controller {
	
	public function action_index()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$revs = Revenue::all();
			return View::make('admin.revs', array('revenues'=>$revs))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_add()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			if(Input::has('submit')){
				$values = Input::get();

				$submit = array_pop($values);

				$rev = new Revenue($values);

				$rev->save();
				if($rev->id){
					Session::flash('status_success',"Successfully added ".$rev->description." revenue.");
				} else {
					Session::flash('status_error',"Error adding ".$rev->description." revenue.");
				}
			} 

			return View::make('admin.revsadd')->nest('nav','partials.nav', array('bBadge'=>$bBadge, 'rBadge'=>$rBadge));
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_edit()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();

			if(Input::has('submit')){
				$revs = Revenue::find(Input::get('id'));
				$values = Input::get();
				$submit = array_pop($values);

				foreach ($values as $key => $value) {
					$revs->$key = $value;
				}

				if($revs->save()){
					Session::flash('status_success',"Successfully edited ".$revs->description." revenue.");
				} else {
					Session::flash('status_error',"Error editting ".$revs->description." revenue.");
				}
				return View::make('admin.revs', array('revenues'=>Revenue::all()))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			} else {
				$revs = Revenue::find($_GET['id']);
				return View::make('admin.revsedit', array('revs'=>$revs))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}


	public function action_delete()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$id = $_GET['id'];
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();

			if(Revenue::find($id)->delete()){
				Session::flash('status_success', "Successfully deleted revenue");
				return View::make('admin.revs', array('revenues'=>Revenue::all()))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			} else {
				Session::flash('status_error',"Error deleting revenue.");
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}
}