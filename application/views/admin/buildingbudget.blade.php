@layout('layouts.main')
@section('content')
<div class="content">
@include('plugins.status')
<div class="page-header"><h1>Building Budgets <small>All Budgets</small></h1></div>
<h3 id="money">$<span id="budgettotal">{{ $budgettotal }}</span></h3>
@if(isset($edit))
<form name='budreport' method='post' action='/building/budget/edit'>
@else
<form name='budreport' method='post' action='/building/budget'>
@endif
<table class='table-striped table'>
	<thead>
		<tr>
			<th>TI</th>
			<th>FUND</th>
			<th>FUNCTION</th>
			<th>OBJECT</th>
			<th>SCC</th>
			<th>SUBJECT</th>
			<th>OPU</th>
			<th>IL</th>
			<th>JOB</th>
			<th>DESCRIPTION</th>
			<th>PROPOSED</th>
			<th>PREV YEARS</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$prevFund == "";
		//$fundTotal = 0.00;
		?>
		@foreach($budgets as $budget)
		  @if($prevFund != $budget->fund)
			<tr>
				<td colspan="12"> Fund: {{ $budget->fund }} - Fund Total: <h3 id="money{{$budget->fund}}">$<span id="00{{$budget->fund}}total">
				@if(isset($edit))
					{{ $fundTotal[$budget->fund] }}
				@else
				  {{ $fundTotal }}
				@endif
				</span></h3></td>
			</tr>
			@endif
			<tr>
				<td>{{ $budget->ti }}</td>
				<td>{{ $budget->fund }}</td>
				<td>{{ $budget->function }}</td>
				<td>{{ $budget->object }}</td>
				<td>{{ $budget->scc }}</td>
				<td>{{ $budget->subject }}</td>
				<td>{{ $budget->opu }}</td>
				<td>{{ $budget->il }}</td>
				<td>{{ $budget->job }}</td>
				<td>{{ $budget->description }}</td>
				<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-{{ $budget->id }}' onchange="subtractFromBudget(this,document.getElementById('budgettotal'),{{$budget->fund}})"
				@if(!is_null($proposed[$budget->id]))
				value="{{$proposed[$budget->id]->amount}}"
				@endif
					></div></td>
				<td>
				<a href="#" class="btn btn-info" rel="popover" title="Previous Years" data-content="{{ $expended[$budget->id] }}">Previous Years</a>
				</td>
			</tr>
			<?php $prevFund = $budget->fund; ?>
		@endforeach
	</tbody>
</table>
<br/><input type='submit' name='save' class="btn" value='Save'><input type='submit' name='submit' class="btn" value='Submit' ></form>

</div>

<script type="text/javascript">
	function subtractFromBudget(obj, current, fund)
	{
		var value = obj.value;
		var budgettotal = current.innerHTML;

		addToFundBudget(value, fund);

		var testvalue = (budgettotal - value).toFixed(2);
		if(testvalue < 0){
			var error =	document.getElementById('money');
			error.style.color="#ff0000";
			current.innerHTML = testvalue;
			error.innerHTML = error.innerHTML + "<br/> You have gone over budget";
		} else {
			current.innerHTML = testvalue;
		}
	}

	function addToFundBudget(value, fund)
	{
		var nMoney = parseInt(value);
		var fundInit = document.getElementById('00'+fund+'total');
		var fundTotal = parseInt(fundInit.innerHTML);
		var newTotal = fundTotal + nMoney;
		fundInit.innerHTML = newTotal.toFixed(2);
	}

	$(function() {
	    $("html").bind("keypress", function(e) {
	            if (e.keyCode == 13) return false;
	      });
	});
</script>
@endsection
