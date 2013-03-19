@layout('layouts/main')
@section('content')
<div class="content">
	@if( isset($_GET['p']))
		<div class="page-header"><h1>Revenues <small>Edit Proposed Revenues</small></h1></div>
		<form name='budreport' method='post' action='/?key={{ $key }}&p=rev'>
		<table class='table-striped table'>
			<thead>
				<tr>
					<th>TI</th>
					<th>FUND</th>
					<th>RECEIPT</th>
					<th>SCC</th>
					<th>SUBJECT</th>
					<th>OPU</th>
					<th>DESCRIPTION</th>
					<th>PROPOSED</th>
					<th>PREV YEARS</th>
				</tr>
			</thead>
			<tbody>
				@foreach($revenues as $revenue)
					<tr>
						<td>{{ $revenue->ti }}</td>
						<td>{{ $revenue->fund }}</td>
						<td>{{ $revenue->receipt }}</td>
						<td>{{ $revenue->scc }}</td>
						<td>{{ $revenue->subject }}</td>
						<td>{{ $revenue->opu }}</td>
						<td>{{ $revenue->description }}</td>
						<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-{{ $revenue->id }}' value='{{ $entries[$revenue->id] }}'></input></div></td>
						<td>
						<a href="#" class="btn btn-info" rel="popover" title="Previous Years" data-content="{{ $expended[$revenue->id] }}">Previous Years</a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<br/><input type='submit' name='submit' class="btn" value='Submit' /></form>
	@else
		<div class="page-header"><h1>Budgets <small>Proposed Budgets</small></h1></div>
		<form name='budreport' method='post' action='/?key={{ $key }}'>
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
	@endif	
</div>
@endsection