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

/*
	public function action_password()
	{
		$new = md5('nhsbookstore'.'nacs');
		echo Hash::make($new);
	}
*/
	public function action_login()
	{
		$username = Input::get('uid');
		$password = Input::get('pwd');

		if(strcspn($username, '0123456789') != 0){
			// DB Auth
			Config::set('auth.driver', 'eloquent');
			$password = md5($password . 'nacs');

			$credentials = array(
	            'username' => $username,
	            'password' => $password
	        );

	        if( Auth::attempt($credentials)) {
	        	if(User::where('username','=',$username)->first()){
					//$user = User::where('username','=',Auth::user()->name)->first();
					$user = Auth::user();

					Session::put('user',$user);
					if($user->is_sa){
						Session::put('sa',1);
						return Redirect::to('admin/index');
					} else {
						return Redirect::to('building/index');
					}

		        } else {
		        	Session::flash('login_error','You do not have access to this app.');
		        	return View::make('admin.index2');
		        }
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
						return Redirect::to('building/index');
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

	/* Activity Accounts Budget Section */

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
    			$body.= "http://budrev/?key=".$key."\n\n";
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
				$submit = array_pop($values);
				foreach($values as $name=>$value){
					list($p,$budget_id) = explode("-",$name);

					$bud = BudgetProposed::where('budget_id','=',$budget_id)->first();

					$bud->proposed = $value;
					$bud->save();
				}

				Session::flash('status_success', 'Successfully updated Unproposed Budgets');
				return Redirect::to('admin/budedit');
			} else {
				$entries = BudgetProposed::all();

		    	$arrBudgets = array();
		    	$arrExpended = array();
		    	$arrProposed = array();

		    	foreach($entries as $key=>$obj){
		    		$arrBudgets[$obj->budget_id] = Budget::find($obj->budget_id);
		    		$string = "";

		    		foreach (Budget::find($obj->budget_id)->expended()->order_by('fyyear', 'desc')->get() as $value) {
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
				foreach($bp as $budprop){
					if($budprop->fyyear == $fyyear){
						if($budprop->proposed == null){
							$budprop->proposed = '0';
						}
						$arrBudExpInsert[] = array('budget_id'=>$budprop->budget_id,'fyyear'=>$budprop->fyyear,'amount'=>$budprop->proposed);
						$budprop->delete();
					}
				}

				echo "<br>".sizeof($arrBudExpInsert)."<br>";


				if(sizeof($arrBudExpInsert) > 0){
					foreach($arrBudExpInsert as $buds){
						// echo "Budget_id::" . $buds['budget_id'] . "<br>";
						//var_dump($buds);

						$bud = Budget::find($buds['budget_id']);
						$bud->is_proposed = '0';
						$bud->save();
						$be = new BudgetExpended($buds);
						$be->save();

					}
					Session::flash('status_success', 'Successfully removed '.$fyyear);
				} else {
					Session::flash('status_error', 'There were no budgets for '.$fyyear);
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

	public function action_budexport()
	{
		$entries = BuildingBudget::all();
		$budgetFile = "";
		$budgetFile = '"TI","FUND","FUNCTION","OBJECT","SCC","SUBJECT","OPU","IL","JOB","Description","Proposed"'."\r\n";


		foreach($entries as $bb){

			$bbp = BuildingBudgetProposed::where('buildingbudget_id','=',$bb->id)->first();

			// if()
			$budgetFile .= '"'.$bb->ti . '","';
			$budgetFile .= $bb->fund.'","';
			$budgetFile .= $bb->function .'","';
			$budgetFile .= $bb->object.'","';
			$budgetFile .= $bb->scc.'","';
			$budgetFile .= $bb->subject.'","';
			$budgetFile .= $bb->opu.'","';
			$budgetFile .= $bb->il.'","';
			$budgetFile .= $bb->job.'","';
			$budgetFile .= $bb->description.'","';
			$budgetFile .= $bbp->amount.'"'."\r\n";
		}

		$entries2 = Budget::all();
		$actBudgetFile = '"TI","FUND","FUNCTION","OBJECT","SCC","SUBJECT","OPU","IL","JOB","Description","Proposed"'."\r\n";

		foreach($entries2 as $b){

			$bp = BudgetProposed::where('budget_id','=',$b->id)->first();

			// if()
			$actBudgetFile .= '"'.$b->ti . '","';
			$actBudgetFile .= $b->fund.'","';
			$actBudgetFile .= $b->function .'","';
			$actBudgetFile .= $b->object.'","';
			$actBudgetFile .= $b->scc.'","';
			$actBudgetFile .= $b->subject.'","';
			$actBudgetFile .= $b->opu.'","';
			$actBudgetFile .= $b->il.'","';
			$actBudgetFile .= $b->job.'","';
			$actBudgetFile .= $b->description.'","';
			$actBudgetFile .= $bp->proposed.'"'."\r\n";

		}

		$file = tempnam("tmp", "zip");
		$zip = new ZipArchive();
		$zip->open($file, ZipArchive::OVERWRITE);

		$zip->addFromString("BUILDINGBUDGET.CSV",$budgetFile);
		$zip->addFromString("ActivityBUDGET.CSV",$actBudgetFile);

		$zip->close();

		header('Content-Type: application/zip');
        header('Content-Length: '.filesize($file));
        header('Content-Disposition: attachement; filename="budget.zip"');
        readfile($file);
        unlink($file);


	}

	public function action_buds($param = "")
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			switch ($param) {
				case 'add':

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

					break;
				case 'edit':
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
					break;

				case 'delete':
					$id = $_GET['id'];

					if(Budget::find($id)->delete()){
						Session::flash('status_success', "Successfully deleted budget");
						return View::make('admin.buds', array('budgets'=>Budget::all()))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					} else {
						Session::flash('status_error',"Error deleting budget.");
					}
					break;

				default:
					$budgets = Budget::all();
					return View::make('admin.buds', array('budgets'=>$budgets))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					break;
			}

		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}
	/* End Budget Section */

	/* Activity Accounts Revenue Section */

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
    			$body.= "http://budrev/?key=".$key."&p=rev\n\n";
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
				$submit = array_pop($values);

				foreach($values as $name=>$value){
					list($p,$revenue_id) = explode("-",$name);
					$rev = RevenueProposed::where('revenue_id','=',$revenue_id)->first();
					$rev->proposed = $value;
					$rev->save();
				}
				Session::flash('status_success', 'Successfully updated Proposed Revenues');
				return Redirect::to('admin/revedit');
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

		    		foreach (Revenue::find($obj->revenue_id)->received()->order_by('fyyear', 'desc')->get() as $value) {
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

	public function action_revexport()
	{
		$entries = BuildingRevenue::all();
		$budgetFile = "";
		$budgetFile = '"TI","FUND","RECEIPT","SCC","SUBJECT","OPU","Description","Proposed"'."\r\n";

		foreach($entries as $bb){

			$bbp = BuildingRevenueProposed::where('buildingrevenue_id','=',$bb->id)->first();

			// if()
			$budgetFile .= '"'.$bb->ti . '","';
			$budgetFile .= $bb->fund.'","';
			$budgetFile .= $bb->receipt.'","';
			$budgetFile .= $bb->scc.'","';
			$budgetFile .= $bb->subject.'","';
			$budgetFile .= $bb->opu.'","';
			$budgetFile .= $bb->description.'","';
			$budgetFile .= $bbp->amount.'"'."\r\n";
		}

		$entries2 = Revenue::all();
		$actBudgetFile = '"TI","FUND","RECEIPT","SCC","SUBJECT","OPU","Description","Proposed"'."\r\n";

		foreach($entries2 as $b){

			$bp = RevenueProposed::where('revenue_id','=',$b->id)->first();

			// if()
			$actBudgetFile .= '"'.$b->ti . '","';
			$actBudgetFile .= $b->fund.'","';
			$actBudgetFile .= $b->receipt.'","';
			$actBudgetFile .= $b->scc.'","';
			$actBudgetFile .= $b->subject.'","';
			$actBudgetFile .= $b->opu.'","';
			$actBudgetFile .= $b->description.'","';
			$actBudgetFile .= $bp->proposed.'"'."\r\n";

		}

		$file = tempnam("tmp", "zip");
		$zip = new ZipArchive();
		$zip->open($file, ZipArchive::OVERWRITE);

		$zip->addFromString("BUILDINGRevenue.CSV",$budgetFile);
		$zip->addFromString("ActivityRevenue.CSV",$actBudgetFile);

		$zip->close();

		header('Content-Type: application/zip');
        header('Content-Length: '.filesize($file));
        header('Content-Disposition: attachement; filename="revenue.zip"');
        readfile($file);
        unlink($file);


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

	public function action_revs($param="")
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			switch($param){
				case 'add':
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
					break;

				case 'edit':
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
					break;

				case 'delete':
					$id = $_GET['id'];

					if(Revenue::find($id)->delete()){
						Session::flash('status_success', "Successfully deleted revenue");
						return View::make('admin.revs', array('revenues'=>Revenue::all()))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					} else {
						Session::flash('status_error',"Error deleting revenue.");
					}

					break;

				default:
					if(Session::has('sa') && Session::has('user') || Auth::user()){
						$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
						$rBadge = $this->_getTotal() - $this->_getProposed();
						$revs = Revenue::all();
						return View::make('admin.revs', array('revenues'=>$revs))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					} else {
						Session::forget('login_error');
						return View::make('admin.index2');
					}
					break;
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}
	}

	/* End Revenue Section */

	/* User Section */

	public function action_user($param = "")
	{
		$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
		$rBadge = $this->_getTotal() - $this->_getProposed();

		if(Session::has('sa') && Session::has('user') || Auth::user()){
			 switch($param){
				case 'add':
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

					break;

				case 'delete':
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
					break;

				default:
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
					break;
			}
		} else {
			Session::forget('login_error');
			return View::make('admin.index2');
		}

	}

	/* End User Section */

	/* Building Section */

  /**
	 * Handles Building Budget for logged in user
	 *
	 */
	public function action_building($param = "")
	{
		if( Session::has('user') || Auth::user() || Session::has('sa')){
			$user = Session::get('user');
			switch ($param) {
				case 'budget':
					if(Input::has('submit') || Input::has('save')){
						// Submitted Budget

						$values = Input::get();
						//var_dump($vales);
						//exit();
						//$submit = array_pop($values);


						$fy = "FY".(date('y')+1);
						foreach ($values as $name=>$value) {
							echo $name . ":::" . $value . "<br>";
							// list($p,$buildingbudget_id) = explode("-",$name);
							// $bbp = new BuildingBudgetProposed;
							// $bb = BuildingBudget::find($buildingbudget_id);
							// //$bb->is_proposed = '1';
							//
							// $bbp->buildingbudget_id = $buildingbudget_id;
							// $bbp->fyyear = $fy;
							// if($value == null || $value == ""){
							// 	$bbp->amount = '0.00';
							// } else {
							// 	$bbp->amount = $value;
							// }
							//
							// $bb->save();
							// $bbp->save();
						}
						exit();
						Session::flash('status_success', 'Your proposed building budget has been submitted');
						return Redirect::to('/admin/building');
					} else {
						// View Users budget
						$user = Session::get('user');
						if(isset($_GET['building'])){
							$entries = BuildingBudget::where('building_id','=',$_GET['building'])->where('is_proposed','=',0)->order_by('fund')->get();
						} else {
							$entries = BuildingBudget::where('building_id','=', $user->building_id)->where('is_proposed','=',0)->order_by('fund')->get();
						}
						if(sizeof($entries)>0){
							$arrBudgets = array();
					    	$arrExpended = array();
					    	$arrProposed = array();

					    	$fy = date('y') + 1;

					    	$budgettotal = BuildingBudgetAmount::where('building_id','=', $user->building_id)->where('fyyear','=','FY'.$fy)->first();

					    	foreach($entries as $key=>$obj){
					    		$arrBudgets[] = $obj;
					    		$string = "";

					    		foreach (BuildingBudgetExpended::where('buildingbudget_id','=',$obj->id)->get() as $value) {
					    			$string .= $value->fyyear . " : $".$value->amount."\n";
					    		}

					    		$arrExpended[$obj->id] = $string;
					    	}

					    	return View::make('admin.buildingbudget', array('budgets'=>$arrBudgets,'expended'=>$arrExpended,'budgettotal'=>$budgettotal->amount))->nest('nav','partials.nav2');
				    	} else {
				    		return View::make('admin.buildingbudget2')->nest('nav','partials.nav2');
				    	}
				    }
					break;

				case 'revenue':
					if(Input::has('submit')){
						// Submitted Revenues
						$values = Input::get();
						$submit = array_pop($values);

						$fy = "FY".(date('y')+1);
						foreach ($values as $name=>$value) {
							list($p,$buildingrevenue_id) = explode("-",$name);
							$brp = new BuildingRevenueProposed;
							$br = BuildingRevenue::find($buildingrevenue_id);
							$br->is_proposed = '1';

							$brp->buildingrevenue_id = $buildingrevenue_id;
							$brp->fyyear = $fy;
							if($value == null || $value == ""){
								$brp->amount = '0.00';
							} else {
								$brp->amount = $value;
							}

							$br->save();
							$brp->save();
						}
						Session::flash('status_success', 'Your proposed building Revenues has been submitted');
						return Redirect::to('/admin/building');
					} else {
						$user = Session::get('user');
						if(isset($_GET['building'])){
							$entries = BuildingRevenue::where('building_id','=',$_GET['building'])->where('is_proposed','=',0)->get();
						} else {
							$entries = BuildingRevenue::where('building_id','=', $user->building_id)->where('is_proposed','=',0)->get();
						}

						if(sizeof($entries)>0){
							$arrRevs = array();
					    	$arrExpended = array();
					    	$arrProposed = array();
							foreach($entries as $key=>$obj){
					    		$arrRevs[] = $obj;
					    		$string = "";

					    		//var_dump(BuildingRevenueExpended::where('buildingrevenue_id','=',$obj->id)->get() );

					    		foreach (BuildingRevenueExpended::where('buildingrevenue_id','=',$obj->id)->get() as $value) {
					    			$string .= $value->fyyear . " : $".$value->amount."\n";
					    			Log::write("info", $obj->id.":::".$string);
					    		}


					    		$arrExpended[$obj->id] = $string;
					    	}
							return View::make('admin.buildingrevenue', array('revenues'=>$arrRevs,'expended'=>$arrExpended))->nest('nav','partials.nav2');
						} else {
							return View::make('admin.buildingrevenue2')->nest('nav','partials.nav2');
						}
					}
					break;

				case 'export':

					$entries = BuildingBudget::where('building_id','=',$user->building_id)->get();
					$budgetFile = "";
		    		$budgetFile = "TI,FUND,FUNCTION,OBJECT,SCC,SUBJECT,OPU,IL,JOB,Description,Proposed\r\n";
		    		foreach($entries as $bb){

		    			$bbp = BuildingBudgetProposed::where('buildingbudget_id','=',$bb->id)->first();

		    			$budgetFile .= $bb->ti . ",";
		    			$budgetFile .= $bb->fund.",";
		    			$budgetFile .= $bb->function .",";
		    			$budgetFile .= $bb->object.",";
		    			$budgetFile .= $bb->scc.",";
		    			$budgetFile .= $bb->subject.",";
		    			$budgetFile .= $bb->opu.",";
		    			$budgetFile .= $bb->il.",";
		    			$budgetFile .= $bb->job.",";
		    			$budgetFile .= $bb->description.",";
		    			$budgetFile .= $bbp->amount."\r\n";
		    		}

		    		$user = Session::get('user');
		    		$entries = BuildingRevenue::where('building_id','=',$user->building_id)->get();

		    		$revFile = "";
		    		$revFile .= "TI,FUND,RECEIPT,SCC,SUBJECT,OPU,Description,Proposed\r\n";
		    		foreach($entries as $br){
		    			$brp = BuildingRevenueProposed::where('buildingrevenue_id','=',$br->id)->first();
		    			$revFile .= $br->ti.",";
		    			$revFile .= $br->fund.",";
		    			$revFile .= $br->receipt.",";
		    			$revFile .= $br->scc.",";
		    			$revFile .= $br->subject.",";
		    			$revFile .= $br->opu.",";
		    			$revFile .= $br->description.",";
		    			$revFile .= $brp->amount."\r\n";
		    		}
		    		$file = tempnam("tmp", "zip");
		    		$zip = new ZipArchive();
		    		$zip->open($file, ZipArchive::OVERWRITE);

		    		$zip->addFromString("BUILDINGBUDGET.CSV",$budgetFile);
		    		$zip->addFromString("BUILDINGREVENUE.CSV",$revFile);

		    		$zip->close();

					header('Content-Type: application/zip');
			        header('Content-Length: '.filesize($file));
			        header('Content-Disposition: attachement; filename="budrev.zip"');
			        readfile($file);
			        unlink($file);
					break;

				default:
					return View::make('admin.dashboard')->nest('nav','partials.nav2');
					break;
			}
		} else {
			Session::flash('status_success', 'Output does not work..');
			return View::make('admin.index2');
		}
	}

	/* End BUilding Section */

	/* Building Budget Section */


	public function action_budget($param = "")
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			switch ($param) {
				case 'view':
					$buildings = Building::all();
					return View::make('admin.budgetview', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					break;

				case 'edit':
					$buildings = Building::all();
					if(Input::has('submit')){
						$values = Input::get();
						$submit = array_pop($values);

						// var_dump($values);

						foreach ($values as $name=>$value) {
							list($p,$buildingbudget_id) = explode("-",$name);
							// echo "BuildingBudgetID :: " . $buildingbudget_id . "<br/>Before Setting Amount";
							$bbuildingproposed = BuildingBudgetProposed::where('buildingbudget_id','=',$buildingbudget_id)->first();
							// var_dump($bbuildingproposed);

							// echo "After Setting Amount";
							$bbuildingproposed->amount = $value;
							// var_dump($bbuildingproposed);
							$bbuildingproposed->save();
						}

						Session::flash('status_success','Successfully updated all the building budgets');
						return View::make('admin.budgetedit', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					} else {
						return View::make('admin.budgetedit', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					}
					break;

				case 'json':
					$buildingBudget = BuildingBudget::where('building_id','=',$_GET['id'])->get();
					// echo json_encode($buildingBudget);
					$arrJson = array();


					foreach ($buildingBudget as $key => $value) {

						// $arrJson[$key] = ($value->attributes);
						$fy = 'FY'.(date('y')+1);
						$bbuildingproposed = BuildingBudgetProposed::where('buildingbudget_id','=',$value->id)->where('fyyear','=',$fy)->first();

						echo "<tr>";
						echo "<td>".$value->ti."</td>";
						echo "<td>".$value->fund."</td>";
						echo "<td>".$value->function."</td>";
						echo "<td>".$value->object."</td>";
						echo "<td>".$value->scc."</td>";
						echo "<td>".$value->subject."</td>";
						echo "<td>".$value->opu."</td>";
						echo "<td>".$value->il."</td>";
						echo "<td>".$value->job."</td>";
						echo "<td>".$value->description."</td>";
						if($bbuildingproposed == null){
							if(isset($_GET['a'])){
								echo "<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-".$value->id."' value='0.00'></input></div></td>";
							} else {
								echo "<td>$0.00</td>";
							}
						} else {
							if(isset($_GET['a'])){
								echo "<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-".$value->id."' value='".$bbuildingproposed->amount."'></input></div></td>";
							} else {
								echo "<td>$".$bbuildingproposed->amount."</td>";
							}
						}
						echo "</tr>";
					}

					break;

				default:
					$arrBuildings = Building::all();
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
						return View::make('admin.budget', array('buildings'=>$arrBuildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					} else {
						return View::make('admin.budget', array('buildings'=>$arrBuildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					}
					break;
			}
		} else {
			Session::flash('login_error','You do not have access to this app.');
		    return View::make('admin.index2');
		}
	}
	/* End Building Budget Section */

	/* Building Revenue Section */

	public function action_revenue($param = "")
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			switch ($param) {
				case 'edit':
					$buildings = Building::all();
					if(Input::has('submit')){
						$values = Input::get();
						$submit = array_pop($values);
						// var_dump($values);
						// exit();
						foreach ($values as $name=>$value) {
							list($p,$buildingrevenue_id) = explode("-",$name);
							$rbuildingproposed = BuildingRevenueProposed::where('buildingrevenue_id','=',$buildingrevenue_id)->first();
							$rbuildingproposed->amount = $value;
							$rbuildingproposed->save();
						}

						Session::flash('status_success','Successfully updated all the building revenues');
					}

					return View::make('admin.revenueedit', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					break;

				case 'json':
					$buildingBudget = BuildingRevenue::where('building_id','=',$_GET['id'])->get();

					//echo json_encode($buildingBudget);
					$arrJson = array();
					foreach ($buildingBudget as $key => $value) {

						// $arrJson[$key] = ($value->attributes);
						$fy = 'FY'.(date('y')+1);
						$rbuildingproposed = BuildingRevenueProposed::where('buildingrevenue_id','=',$value->id)->where('fyyear','=',$fy)->first();

						echo "<tr>";
						echo "<td>".$value->ti."</td>";
						echo "<td>".$value->fund."</td>";
						echo "<td>".$value->receipt."</td>";
						echo "<td>".$value->scc."</td>";
						echo "<td>".$value->subject."</td>";
						echo "<td>".$value->opu."</td>";
						echo "<td>".$value->description."</td>";
						if($rbuildingproposed == null){
							if(isset($_GET['a'])){
								echo "<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-".$value->id."' value='0.00'></input></div></td>";
							} else {
								echo "<td>$0.00</td>";
							}
						} else {
							if(isset($_GET['a'])){
								echo "<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-".$value->id."' value='".$rbuildingproposed->amount."'></input></div></td>";
							} else {
								echo "<td>$".$rbuildingproposed->amount."</td>";
							}
						}
						echo "</tr>";
					}
					break;

				default:
					$buildings = Building::all();
			return View::make('admin.revenueview', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
					break;
			}
		} else {
			Session::flash('login_error','You do not have access to this app.');
		    return View::make('admin.index2');
		}
	}

	/* End Building Revenue Section */

}
