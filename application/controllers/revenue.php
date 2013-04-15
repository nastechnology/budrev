<?php

class Revenue_Controller extends Base_Controller{
	
	public function action_index()
	{
		// List all Revenues
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$buildings = Building::all();
			return View::make('admin.revenueview', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
		} else {
			Session::flash('login_error','You do not have access to this app.');
		    return View::make('admin.index2');
		}
	}

	public function action_edit()
	{
		// List all Revenues
		if(Session::has('sa') && Session::has('user') || Auth::user()){
			$bBadge = $this->_getTotal('budget') - $this->_getProposed('budget');
			$rBadge = $this->_getTotal() - $this->_getProposed();
			$buildings = Building::all();
			if(Input::has('submit')){
				$values = Input::get();
				$submit = array_pop($values);
				foreach ($values as $name=>$value) {
					list($p,$buildingrevenue_id) = explode("-",$name);
					$rbuildingproposed = BuildingRevenueProposed::find($buildingrevenue_id);
					$rbuildingproposed->amount = $value;
					$rbuildingproposed->save();
				}

				Session::flash('status_success','Successfully updated all the building revenues');
			} 

			return View::make('admin.revenueedit', array('buildings'=>$buildings))->nest('nav','partials.nav', array('bBadge'=>$bBadge,'rBadge'=>$rBadge));
		} else {
			Session::flash('login_error','You do not have access to this app.');
		    return View::make('admin.index2');
		}
	}

	public function action_json()
	{
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
	}
}