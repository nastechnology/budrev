@layout('layouts/main')

@section('content')
<div class="content">
@include('plugins.status')
<div class="page-header"><h1>Budgets <small>All Budgets</small></h1></div>
<form name='budreport' method='post' action='/admin/bud'>
<table class='table-striped table'>
	<thead>
		<tr>
			<th>Group</th>
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
		</tr>
	</thead>
	<tbody>
		@foreach($budgets as $budget)
			<tr>
				<td><input type='checkbox' name='bud[]' id='bud' value='{{ $budget->id}}'></td>
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
			</tr>
		@endforeach
	</tbody>
</table>
<br/><input type='text' name='email' id='email' placeholder='john.smith@example.com'></input>
<br/><input type='submit' name='submit' class="btn" value='Submit' /></form>

</div>
@endsection
