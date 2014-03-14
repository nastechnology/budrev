<?php

class Home_Controller extends Base_Controller {

	/*
	|--------------------------------------------------------------------------
	| The Default Controller
	|--------------------------------------------------------------------------
	|
	| Instead of using RESTful routes and anonymous functions, you might wish
	| to use controllers to organize your application API. You'll love them.
	|
	| This controller responds to URIs beginning with "home", and it also
	| serves as the default controller for the application, meaning it
	| handles requests to the root of the application.
	|
	| You can respond to GET requests to "/home/profile" like so:
	|
	|		public function action_profile()
	|		{
	|			return "This is your profile!";
	|		}
	|
	| Any extra segments are passed to the method as parameters:
	|
	|		public function action_profile($id)
	|		{
	|			return "This is the profile for user {$id}.";
	|		}
	|
	*/

	public function action_index()
	{

		if(isset($_GET['key'])){
	    	$arrExpended = array();
	    	$arrProposed = array();
			if(isset($_GET['p'])){
				// Revenue
				if(Input::has('submit')){
					// Form Submitted
					$values = Input::get();
					foreach($values as $name=>$value){
						if($name != 'submit' && $name != 'key' && $name != 'p'){
							list($p,$revenue_id) = explode("-",$name);
							$rev = Revenue::find($revenue_id)->proposed()->first();
							$rev->proposed = $value;
							$rev->save();
						}
					}
					Session::flash('status_success', 'Your proposed revenue has been submitted');
					return Redirect::home();
				} else {
					$size = RevenueProposed::where('key','=', $_GET['key'])->count();
					if($size > 0){
						$entries = RevenueProposed::where('key','=', $_GET['key'])->get();
						$arrRevenues = array();
						foreach($entries as $key=>$rp){
							$arrRevenues[$rp->revenue_id] = Revenue::find($rp->revenue_id);
							$string = "";

							$model = $rp->attributes;
							if($model['key'] == $_GET['key']){
								$arrProposed[] = $rp;
								$cy = date('Y');
								$ty = $cy - 3;
								foreach (Revenue::find($rp->revenue_id)->received()->order_by('fyyear','desc')->get() as $value) {
									$string .= $value->fyyear . " : $".$value->amount."\n";
					    		}
							}

							$arrExpended[$rp->revenue_id] = $string;
				    		$arrProposed[$rp->revenue_id] = $rp->proposed;
						}
				    	return View::make('home.index2', array('revenues'=>$arrRevenues,'entries'=>$arrProposed,'expended'=>$arrExpended))->with('key',$_GET['key']);
				    } else {
				    	return View::make('home.index');
				    }
		    	}
			} else {
				// Budget
				if(Input::has('submit')){
					// Form Submitted
					$values = Input::get();
					foreach($values as $name=>$value){
						if($name != 'submit' && $name != 'key'){
							list($p,$budget_id) = explode("-",$name);
							
							$bp = Budget::find($budget_id)->proposed()->first();
							$bp->proposed = $value;
							$bp->save();
						}
					}
					
					Session::flash('status_success', 'Your proposed budget has been submitted');
					return Redirect::home();
				} else {
					$size = BudgetProposed::where('key','=', $_GET['key'])->count();
					if($size > 0){
						$entries = BudgetProposed::where('key','=', $_GET['key'])->get();
						$arrBudgets = array();
						foreach ($entries as $key=>$bp) {
							$arrBudgets[$bp->budget_id] = Budget::find($bp->budget_id);
							$string = "";
							$model = $bp->attributes;
							if($model['key'] == $_GET['key']){
								$arrProposed[] = $bp;
								
								foreach (Budget::find($bp->budget_id)->expended()->order_by('fyyear','desc')->get() as $key=>$value) {
					    			$string .= $value->fyyear . " : $".$value->amount."\n";
					    		}
							}

							$arrExpended[$bp->budget_id] = $string;
				    		$arrProposed[$bp->budget_id] = $bp->proposed;		    		
						}
						return View::make('home.index2', array('budgets'=>$arrBudgets,'entries'=>$arrProposed,'expended'=>$arrExpended))->with('key',$_GET['key']);
					} else {
						return View::make('home.index');
					}

				}
			}
		} else {
			return View::make('home.index');
		}
		
	}


	// public function action_create()
	// {
	// 	$password = md5('amazonnacs');
	// 	echo "Password for Bookstore User: (amazon) " . Hash::make($password);
	// }

}