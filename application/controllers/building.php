<?php

class Building_Controller extends Base_Controller {

	public function action_index()
	{
		if(Session::has('user')){
			$user = Session::get('user');
			return View::make('admin.dashboard')->nest('nav','partials.nav2');
		} else {
			return View::make('admin.index2');
		}
	}

	public function action_budget($param = "")
	{
		if(Session::has('user')){
			$user = Session::get('user');
			if(isset($_GET['building'])){
				$entries = BuildingBudget::where('building_id','=',$_GET['building'])->where('is_proposed','=',0)->order_by('fund')->get();
			} else {
				$entries = BuildingBudget::where('building_id','=', $user->building_id)->where('is_proposed','=',0)->order_by('fund')->get();
			}
			switch ($param) {
				case 'edit':
				  if(Input::has('submit')){
							$values = Input::get();
							$submit = array_pop($values);

							// var_dump($values);

							foreach ($values as $name=>$value) {
								list($p,$buildingbudget_id) = explode("-",$name);

								$bbuildingproposed = BuildingBudgetProposed::where('buildingbudget_id','=',$buildingbudget_id)->first();


								$bb = BuildingBudget::find($buildingbudget_id);
								if($submit == "Submit"){
									$bb->is_proposed = '1';
								} else {
									$bb->is_proposed = '0';
								}


								$bbuildingproposed->amount = $value;

								$bbuildingproposed->save();
								$bb->save();
							}

							Session::flash('status_success','Successfully updated all the building budgets');
							return View::make('admin.budgetedit', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
						} else {

							if(sizeof($entries)>0){
								$arrBudgets = array();
									$arrExpended = array();
									$arrProposed = array();
									$arrFundTotals = array();
									$fy = date('y') + 1;

									$budgettotal = BuildingBudgetAmount::where('building_id','=', $user->building_id)->where('fyyear','=','FY'.$fy)->first();

									$prevFund = "";
									$fundTotal = "0.00";
									foreach($entries as $key=>$obj){
										if($obj->fund == '1' || $obj->fund == '3' || $obj->fund == '9'){
											$arrBudgets[] = $obj;
											$string = "";

                      $arrProposed[$obj->id] = BuildingBudgetProposed::where('buildingbudget_id','=',$obj->id)->first();
											if($prevFund != $obj->fund){
												$prevFund = $obj->fund;
												$arrFundTotals[$obj->fund] = $fundTotal;
												$fundTotal = "";
											}

											$budgettotal->amount = $budgettotal->amount - $arrProposed[$obj->id]->amount;
											$fundTotal = $fundTotal + $arrProposed[$obj->id]->amount;

											foreach (BuildingBudgetExpended::where('buildingbudget_id','=',$obj->id)->order_by('fyyear','DESC')->get() as $value) {
												$string .= $value->fyyear . " : $".$value->amount."\n";
											}

											$arrExpended[$obj->id] = $string;
										}
									}
									$arrFundTotals[$prevFund] = $fundTotal;
								}
							return View::make('admin.buildingbudget', array('budgets'=>$arrBudgets,'expended'=>$arrExpended, 'fundTotal'=>$arrFundTotals,'edit'=>true, 'proposed'=>$arrProposed, 'budgettotal'=>$budgettotal->amount))->nest('nav','partials.nav2');
						}
				  break;

				default:
				if(Input::has('submit') || Input::has('save')){
					// Submitted Budget

					$values = Input::get();

					$submit = array_pop($values);

					$fy = "FY".(date('y')+1);
					foreach ($values as $name=>$value) {
						list($p,$buildingbudget_id) = explode("-",$name);
						$bbp = new BuildingBudgetProposed;
						$bb = BuildingBudget::find($buildingbudget_id);
						if($submit == "Submit"){
							$bb->is_proposed = '1';
						} else {
							$bb->is_proposed = '0';
						}


						$bbp->buildingbudget_id = $buildingbudget_id;
						$bbp->fyyear = $fy;
						if($value == null || $value == ""){
							$bbp->amount = '0.00';
						} else {
							$bbp->amount = $value;
						}

						$bb->save();
						$bbp->save();
					}
					Session::flash('status_success', 'Your proposed building budget has been submitted');
					return Redirect::to('/building');
				} else {
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
								if($obj->fund == '1' || $obj->fund == '3' || $obj->fund == '9'){
					    		$arrBudgets[] = $obj;
					    		$string = "";



					    		foreach (BuildingBudgetExpended::where('buildingbudget_id','=',$obj->id)->order_by('fyyear','DESC')->get() as $value) {
					    			$string .= $value->fyyear . " : $".$value->amount."\n";
					    		}

					    		$arrExpended[$obj->id] = $string;
								}
				    	}

				    	return View::make('admin.buildingbudget', array('budgets'=>$arrBudgets,'expended'=>$arrExpended, 'budgettotal'=>$budgettotal->amount))->nest('nav','partials.nav2');
			    	} else {
			    		return View::make('admin.buildingbudget2')->nest('nav','partials.nav2');
			    	}
		    	}
				break;
			}
		} else {
			// Not authenticated
			return View::make('admin.index2');
		}
	}

	public function action_revenue()
	{
		if(Session::has('user')){
			if(Input::has('submit')){
				// Submitted Revenues
				$values = Input::get();
				var_dump($values);
				exit();
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
				return Redirect::to('/building');
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
		} else {
			return View::make('admin.index2');
		}
	}

	public function action_export()
	{
		if(Session::has('user')){
			$user = Session::get('user');
			$entries = BuildingBudget::where('building_id','=',$user->building_id)->get();
			$budgetFile = "";
    		$budgetFile = '"TI","FUND","FUNCTION","OBJECT","SCC","SUBJECT","OPU","IL","JOB","Description","Proposed"'."\r\n";
    		foreach($entries as $bb){

    			$bbp = BuildingBudgetProposed::where('buildingbudget_id','=',$bb->id)->first();

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

    		$user = Session::get('user');
    		$entries = BuildingRevenue::where('building_id','=',$user->building_id)->get();

    		$revFile = "";
    		$revFile .= '"TI","FUND","RECEIPT","SCC","SUBJECT","OPU","Description","Proposed"'."\r\n";
    		foreach($entries as $br){
    			$brp = BuildingRevenueProposed::where('buildingrevenue_id','=',$br->id)->first();
    			$revFile .= '"'.$br->ti.'","';
    			$revFile .= $br->fund.'","';
    			$revFile .= $br->receipt.'","';
    			$revFile .= $br->scc.'","';
    			$revFile .= $br->subject.'","';
    			$revFile .= $br->opu.'","';
    			$revFile .= $br->description.'","';
    			$revFile .= $brp->amount.'"'."\r\n";
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

		} else {
			return View::make('admin.index2');
		}

	}

}
