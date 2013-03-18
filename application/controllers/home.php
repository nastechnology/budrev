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
			$arrProposed = array();
			if($_GET['p']){
				// Revenue
				$entries = RevenueProposed::all();
				foreach($entries as $rp){
					if($rp->key == $_GET['key']){
						$arrProposed[] = $rp;
					}
				}
			} else {
				// Budget
				$entries = BudgetProposed::all();
				foreach ($entries as $bp) {
					if($bp->key == $_GET['key']){
						$arrProposed[] = $bp;
					}
				}
			}

			return View::make('home.index2', array('entries'=>$arrProposed));
		} else {
			return View::make('home.index');
		}
		
	}

}