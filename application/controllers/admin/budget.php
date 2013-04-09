<?php

class Admin_Budget_Controller extends Base_Controller{
	
	public function action_index()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$arrBuildings = Building::all();
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();

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
		} else {
			Session::flash('login_error','You do not have access to this app.');
		    return View::make('admin.index2');
		}
	}

	public function action_view()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$buildings = Building::all();
			return View::make('admin.budgetview', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
		} else {
			Session::flash('login_error','You do not have access to this app.');
		    return View::make('admin.index2');
		}
	}

	public function action_edit()
	{
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$buildings = Building::all();
			if(Input::has('submit')){
				$values = Input::get();
				$submit = array_pop($values);
				foreach ($values as $name=>$value) {
					list($p,$buildingbudget_id) = explode("-",$name);
					$bbuildingproposed = BuildingBudgetProposed::find($buildingbudget_id);
					$bbuildingproposed->amount = $value;
					$bbuildingproposed->save();
				}

				Session::flash('status_success','Successfully updated all the building budgets');
				return View::make('admin.budgetedit', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			} else {
				return View::make('admin.budgetedit', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
			}
		} else {
			Session::flash('login_error','You do not have access to this app.');
		    return View::make('admin.index2');
		}
	}

	public function action_json()
	{
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
	}

}