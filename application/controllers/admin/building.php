<?php

class Admin_Building_Controller extends Base_Controller {
	
	public function action_index()
	{
		if(Session::has('user')){
			$user = Session::get('user');
			return View::make('admin.dashboard')->nest('nav','partials.nav2');
		} else {
			return View::make('admin.index2');
		}
	}

	public function action_budget()
	{
		if(Session::has('user')){
			if(Input::has('submit')){
				// Submitted Budget
			} else {
				$user = Session::get('user');
				if(isset($_GET['building'])){
					$entries = BuildingBudget::where('building_id','=',$_GET['building'])->where('is_proposed','=',0)->get();
				} else {
					$entries = BuildingBudget::where('building_id','=', $user->building_id)->where('is_proposed','=',0)->get();
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
		} else {
			return View::make('admin.index2');
		}
	}

	public function aciton_revenue()
	{
		if(Session::has('user')){
			$user = Session::get('user');
			var_dump($user);
		} else {
			return View::make('admin.index2');
		}
	}

	public function action_export()
	{
		if(Session::has('user')){
			$user = Session::get('user');
			$entries = BuildingBudget::where('building_id','=',$user->building_id);
			header('Content-type: text/csv');
    		header('Content-Disposition: attachment;filename=BUILDBUDPROPOSED.csv');
    		$fp = fopen('php://output','w');
    		fputcsv($fp, array('TI','FUND','FUNCTION','OBJECT','SCC','SUBJECT','OPU','IL','JOB','Description','Proposed'));
    		foreach($entries as $bb){

    		}

    		fclose($fp);
		} else {
			return View::make('admin.index2');
		}

	}

}