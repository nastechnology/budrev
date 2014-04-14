@layout('layouts/main')
@section('content')
<div class="content">
@include('plugins.status')
<div class="page-header"><h1>Budgets <small>Edit Proposed Budgets</small></h1></div>
<form name='budreport' method='post' action='/admin/budedit'>
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
		@foreach($budgets as $budget)
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
				<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-{{ $budget->id }}' value='{{ $entries[$budget->id] }}'></input></div></td>
				<td>
				<a href="#" class="btn btn-info" rel="popover" title="Previous Years" data-content="{{ $expended[$budget->id] }}">Previous Years</a>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
<br/><input type='submit' name='submit' class="btn" value='Submit' /></form>

</div>
@endsection
