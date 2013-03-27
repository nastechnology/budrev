<?php

class Admin_Controller  extends Base_Controller {
	
	public function action_index()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			return View::make('admin.index')->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
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
					$user = User::where('username','=',Auth::user()->name)->first();
					Session::put('user',$user);
					if($user->is_sa){
						Session::put('sa',1);
						return Redirect::to('admin/index');
					} else {
						return Redirect::to('admin/building/index');
					}
		        	
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
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			if(Input::has('submit')){
				$buds = Input::get('bud');
				$temp = md5(microtime()+rand());
    			$key = strtoupper(substr($temp, 0, 6));
    			$fyyear = "FY".(date('y')+1);
    			$email = Input::get('email');
    			$arrInsert = array();
    			foreach($buds as $keyid=>$value) {
    				$bud = Budget::find($value);
    				$bud->is_proposed = '1';
    				$bud->save();
    				$arrInsert[] = array('budget_id'=>$value,'key'=>$key,'fyyear'=>$fyyear);
    			}

    			BudgetProposed::insert($arrInsert);

    			$body = "\n\n***This site can only be used while on the school district's network.***\n\n";
    			$body.= "Please go to the following URL to setup your proposed revenues for next school year. Please open the link using Firefox or Chrome, copy and paste the link into the url bar.\n";
    			$body.= "http://budrev2.dev/?key=".$key."\n\n";
    			$body.= "Thank You,\nSara Buchhop";

    			if(mail($email,'[Budget]',$body,'From: sara.buchhop@napoleonareaschools.org')){
    				Session::flash('status_success', 'Successfully Emailed '.$email.' Regarding Budgets');
    				return Redirect::to('/admin/bud');
    			} else {
    				Session::flash('status_error', 'There was an error sending the email');
    				return Redirect::to('/admin/bud');
    			}
			} else {
				$budgets = Budget::where('is_proposed','=',0)->get();
				$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
				$rBadge = $this->_getTotal() - $this->_getProposed();
				return View::make('admin.bud', array('budgets'=>$budgets))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_budedit()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			if(Input::has('submit')){
				// Save Budget Proposed
				$values = Input::get();
				foreach($values as $name=>$value){
					if($name != 'submit'){
						list($p,$budget_id) = explode("-",$name);
						$bud = Budget::find($budget_id)->proposed()->first();
						$bud->proposed = $value;
						$bud->save();
					}
				}
				Session::flash('status_success', 'Successfully updated Unproposed Budgets');
				return Redirect::to('admin/bud');
			} else {
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

				return View::make('admin.budedit', array('budgets'=>$arrBudgets,'entries'=>$arrProposed,'expended'=>$arrExpended))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_buddelete()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			if(Input::has('submit')){
				$fyyear = Input::get('fyyear');
				$bp = BudgetProposed::all();
				$arrBudExpInsert = array();
				foreach($bp as $budprop){
					if($budprop->fyyear == $fyyear){
						if($budprop->proposed == null){
							$budprop->proposed = '0';
						}
						$arrBudExpInsert[] = array('revenue_id'=>$budprop->revenue_id,'fyyear'=>$budprop->fyyear,'amount'=>$budprop->proposed);
						$revprop->delete();
					}
				}
				
				if(sizeof($arrBudExpInsert) > 0){
					foreach($arrBudExpInsert as $buds){
						$bud = Budget::find($buds['revenue_id']);
						$budbud->is_proposed = '0';
						$bud->save();
						$be = new BudgetExpeneded($buds);
						$be->save();
					}
					Session::flash('status_success', 'Successfully removed '.$fyyear);
				} else {
					Session::flash('status_error', 'There was an error removing '.$fyyear);
				}

				return View::make('admin.buddelete')->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
				
			} else {
				return View::make('admin.buddelete')->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}


	}


	public function action_budunproposed()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$buds = Budget::all();
			foreach($buds as $bud){
				if($bud->is_proposed == 1){
					$bud->is_proposed = '0';
					$bud->save();
				}
			}
			Session::flash('status_success', 'Successfully set all budgets as unproposed');
			return View::make('admin.index')->with('bBadge',$bBadge)->with('rBadge',$rBadge);
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}
	/* End Budget Section */

	/*  Revenue Section */

	public function action_rev()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){

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

    			if(mail($email,'[Revenue]',$body,'From: sara.buchhop@napoleonareaschools.org')){
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
				return View::make('admin.rev', array('revenues'=>$revenue))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_revedit()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			if(Input::has('submit')){
				// Save Revenue Proposed
				$values = Input::get();
				foreach($values as $name=>$value){
					if($name != 'submit'){
						list($p,$revenue_id) = explode("-",$name);
						$rev = Revenue::find($revenue_id)->proposed()->first();
						$rev->proposed = $value;
						$rev->save();
					}
				}
				Session::flash('status_success', 'Successfully updated Unproposed Revenues');
				return Redirect::to('admin/rev');
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

				return View::make('admin.revedit', array('revenues'=>$arrRevenues,'entries'=>$arrProposed,'expended'=>$arrExpended))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	public function action_revdelete()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			if(Input::has('submit')){
				$fyyear = Input::get('fyyear');
				$rp = RevenueProposed::all();
				$arrRevRecInsert = array();
				foreach($rp as $revprop){
					if($revprop->fyyear == $fyyear){
						if($revprop->proposed == null){
							$revprop->proposed = '0';
						}
						$arrRevRecInsert[] = array('revenue_id'=>$revprop->revenue_id,'fyyear'=>$revprop->fyyear,'amount'=>$revprop->proposed);
						$revprop->delete();
					}
				}
				
				if(sizeof($arrRevRecInsert) > 0){
					foreach($arrRevRecInsert as $rps){
						$rev = Revenue::find($rps['revenue_id']);
						$rev->is_proposed = '0';
						$rev->save();
						$rr = new RevenueReceived($rps);
						$rr->save();
					}
					Session::flash('status_success', 'Successfully removed '.$fyyear);
				} else {
					Session::flash('status_error', 'There was an error removing '.$fyyear);
				}

				return View::make('admin.revdelete')->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
				
			} else {
				return View::make('admin.revdelete')->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}

	}

	public function action_revunproposed()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$revs = Revenue::all();
			foreach($revs as $rev){
				if($rev->is_proposed == 1){
					$rev->is_proposed = '0';
					$rev->save();
				}
			}
			Session::flash('status_success', 'Successfully set all revenues as unproposed');
			return View::make('admin.index')->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	/* End Revenue Section */

	
}