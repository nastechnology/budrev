<?php

class Admin_Controller  extends Base_Controller {
	
	public function action_index()
	{
		if(Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			return View::make('admin.index')->with('bBadge', $bBadge)->with('rBadge',$rBadge);
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_login()
	{
		$username = Input::get('uid');
		$password = Input::get('pwd');

		if($username == 'admin'){
			// DB Auth
			Config::set('auth.driver', 'eloquent');
			$password = md5($password . 'nacs');

			$credentials = array(
	            'username' => $username,
	            'password' => $password
	        );

	        if( Auth::attempt($credentials)) {
	        	return Redirect::to('admin/index');
	        } else {
	        	echo "Failed to login";
	        }
		} else {
			// LDAPAuth
			Config::set('auth.driver', 'ldapauth');
			$credentials = array(
	            'username' => $username,
	            'password' => $password
	        );
			if(Auth::attempt($credentials)){
				// Authenticated against LDAP now check to see if they 
				// are allowed to access resource.
				if(User::where('username','=',$username)->first()){
					Session::put('user', Auth::user());
		        	return Redirect::to('admin/index');
		        } else {
		        	Session::flash('login_error','You do not have access to this app.');
		        	return View::make('admin.index2');
		        } 
			} else {
				echo "Failed to login";
			}
		}
	}

	public function action_logout()
	{
		Auth::logout();
		Session::flush();
		return Redirect::to('/');
	}

	/* Budget Section */

	public function action_bud()
	{
		if(Session::has('user') || Auth::user()){
			if(Input::has('submit')){
				var_dump(Input::get('bud'));
			} else {
				$budgets = Budget::where('is_proposed','=',0)->get();
				$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
				$rBadge = $this->_getTotal() - $this->_getProposed();
				return View::make('admin.bud', array('budgets'=>$budgets))->with('bBadge', $bBadge)->with('rBadge',$rBadge);
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_budedit()
	{
		if(Session::has('user') || Auth::user()){
			if(Input::has('submit')){
				$buds = Input::get('bud');
			} else {
				$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
				$rBadge = $this->_getTotal() - $this->_getProposed();

				$entries = BudgetProposed::all();

		    	$arrBudgets = array();
		    	$arrExpended = array();
		    	$arrProposed = array();
		    	
		    	foreach($entries as $key=>$obj){
		    		$arrBudgets[$obj->budget_id] = Budget::find($obj->budget_id);
		    		$string = "";

		    		foreach (Budget::find($obj->budget_id)->expended()->get() as $value) {
		    			$string .= $value->fyyear . " : $".$value->amount."\n";
		    		}
		    		$arrExpended[$obj->budget_id] = $string;
		    		$arrProposed[$obj->budget_id] = $obj->proposed;
		    	}

				return View::make('admin.budedit', array('budgets'=>$arrBudgets,'entries'=>$arrProposed,'expended'=>$arrExpended))->with('bBadge', $bBadge)->with('rBadge',$rBadge);
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_buddelete()
	{
		if(Session::has('user') || Auth::user()){
			
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			if(Input::has('submit')){
				$fyyear = Input::get('fyyear');
				
			} else {
				return View::make('admin.buddelete')->with('bBadge',$bBadge)->with('rBadge',$rBadge);
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}

	}


	public function action_budunproposed()
	{
		if(Session::has('user') || Auth::user()){
			$bud = new Budget(array('is_proposed'=>'0'));
			$bud->save();
			Session::flash('status_success', 'Successfully set all budgets as unproposed');
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}
	/* End Budget Section */

	/*  Revenue Section */

	public function action_rev()
	{
		if(Session::has('user') || Auth::user()){

			if(Input::has('submit')){
				$revs = Input::get('rev');
				$temp = md5(microtime()+rand());
    			$key = strtoupper(substr($temp, 0, 6));
    			$fyyear = "FY".(date('y')+1);
    			$email = Input::get('email');
    			$arrInsert = array();
    			foreach ($revs as $keyid=> $value) {
    				$rev = Revenue::find($value);
    				$rev->is_proposed = '1';
    				$rev->save();
    				$arrInsert[] = array('revenue_id'=>$value,'key'=>$key,'fyyear'=>$fyyear);
    			}

    			RevenueProposed::insert($arrInsert);

    			$body = "\n\n***This site can only be used while on the school district's network.***\n\n";
    			$body.= "Please go to the following URL to setup your proposed revenues for next school year. Please open the link using Firefox or Chrome, copy and paste the link into the url bar.\n";
    			$body.= "http://budrev2.dev/?key=".$key."&p=rev\n\n";
    			$body.= "Thank You,\nSara Buchhop";

    			if(mail($email,'[Revenu]',$body,'From: sara.buchhop@napoleonareaschools.org')){
    				Session::flash('status_success', 'Successfully Emailed '.$email.' Regarding Revenues');
    				return Redirect::to('/admin/rev');
    			} else {
    				Session::flash('status_error', 'There was an error sending the email');
    				return Redirect::to('/admin/rev');
    			}

			} else {
				$revenue = Revenue::where('is_proposed','=',0)->get();
				$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
				$rBadge = $this->_getTotal() - $this->_getProposed();
				return View::make('admin.rev', array('revenues'=>$revenue))->with('bBadge', $bBadge)->with('rBadge',$rBadge);
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_revedit()
	{
		if(Session::has('user') || Auth::user()){
			if(Input::has('submit')){
				var_dump(Input::get('rev'));
			} else {
				$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
				$rBadge = $this->_getTotal() - $this->_getProposed();
				
				$entries = RevenueProposed::all();

		    	$arrRevenues = array();
		    	$arrExpended = array();
		    	$arrProposed = array();
		    	
		    	foreach($entries as $key=>$obj){
		    		$arrRevenues[$obj->revenue_id] = Revenue::find($obj->revenue_id);
		    		$string = "";

		    		foreach (Revenue::find($obj->revenue_id)->received()->get() as $value) {
		    			$string .= $value->fyyear . " : $".$value->amount."\n";
		    		}
		    		$arrExpended[$obj->revenue_id] = $string;
		    		$arrProposed[$obj->revenue_id] = $obj->proposed;
		    	}

				return View::make('admin.revedit', array('revenues'=>$arrRevenues,'entries'=>$arrProposed,'expended'=>$arrExpended))->with('bBadge', $bBadge)->with('rBadge',$rBadge);
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_revdelete()
	{
		if(Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			if(Input::has('submit')){
				$fyyear = Input::get('fyyear');
				$rp = RevenueProposed::all();
				$arrRevRecInsert = array();
				foreach($rp as $revprop){
					if($revprop->fyyear == $fyyear){
						$arrRevRecInsert[] = array('revenue_id'=>$revprop->revenue_id,'fyyear'=>$revprop->fyyear,'amount'=>$revprop->proposed);
						$revprop->delete();
					}
				}
				if(sizeof($arrRevRecInsert) > 0){
					$rev = new Revenue(array('is_proposed'=>'0'));
					$rev->save();
					Session::flash('status_success', 'Successfully removed '.$fyyear);
				} else {
					Session::flash('status_error', 'There was an error removing '.$fyyear);
				}

				return View::make('admin.revdelete')->with('bBadge',$bBadge)->with('rBadge',$rBadge);
				
			} else {
				return View::make('admin.revdelete')->with('bBadge',$bBadge)->with('rBadge',$rBadge);
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}

	}

	public function action_revunproposed()
	{
		if(Session::has('user') || Auth::user()){
			$rev = new Revenue(array('is_proposed'=>'0'));
			$rev->save();
			Session::flash('status_success', 'Successfully set all revenues as unproposed');
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	/* End Revenue Section */

	/* Private Methods */

	private function _getProposed($type=null){
		if($type == "budget"){
			return BudgetProposed::count();
		} else {
			// type equals revenue
			return RevenueProposed::count();
		}
	}

	private function _getTotal($type=null){
		if($type == "budget"){
			return Budget::count();
		} else {
			// type equals revenue
			return Revenue::count();
		}
	}

	/* End Private Methods */
}