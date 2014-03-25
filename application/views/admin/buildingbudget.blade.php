@layout('layouts.main')
@section('content')
<div class="content">
@include('plugins.status')
<div class="page-header"><h1>Building Budgets <small>All Budgets</small></h1></div>
<h3 id="money">$<span id="budgettotal">{{ $budgettotal }}</span></h3>
<form name='budreport' method='post' action='/admin/building/budget'>
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
		<?php $prevFund == ""; ?>
		@foreach($budgets as $budget)
		  @if($prevFund != $budget->fund)
			<tr>
				<td span="12"> Fund: 00{{ $budget->fund }}</td>
			</tr>
			@else
			<tr>
				<td>{{ $budget->ti }}</td>
				<td>00{{ $budget->fund }}</td>
				<td>{{ $budget->function }}</td>
				<td>{{ $budget->object }}</td>
				<td>{{ $budget->scc }}</td>
				<td>{{ $budget->subject }}</td>
				<td>{{ $budget->opu }}</td>
				<td>{{ $budget->il }}</td>
				<td>{{ $budget->job }}</td>
				<td>{{ $budget->description }}</td>
				<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-{{ $budget->id }}' onchange="subtractFromBudget(this,document.getElementById('budgettotal'))"></input></div></td>
				<td>
				<a href="#" class="btn btn-info" rel="popover" title="Previous Years" data-content="{{ $expended[$budget->id] }}">Previous Years</a>
				</td>
			</tr>
			@endif
			<?php $prevFund = $budget->fund; ?>
		@endforeach
	</tbody>
</table>
<br/><input type='submit' name='submit' class="btn" value='Submit' /></form>

</div>

<script type="text/javascript">
	function subtractFromBudget(obj, current)
	{
		var value = obj.value;
		var budgettotal = current.innerHTML;

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
	$(function() {
	    $("form").bind("keypress", function(e) {
	            if (e.keyCode == 13) return false;
	      });
	});
</script>
@endsection
