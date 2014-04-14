@layout('layouts.main')

@section('content')
<div class="content">
	@include('plugins.status')
	<div class="page-header">
		<h1>Budgets <small>View Current Activity Budgets</small></h1>
	</div>
	<a class="btn btn-success" href="/admin/buds/add"><i class="icon-plus icon-white"></i> Add Budget</a>

	<table class="table table-striped">
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
				<th><i class="icon-remove"></i> Delete | <i class="icon-pencil"></i> Edit</th>
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
					<td><a href="/admin/buds/delete?id={{ $budget->id }}"><i class="icon-remove"></i> Delete</a> | <a href="/admin/buds/edit?id={{ $budget->id }}"><i class="icon-pencil"></i> Edit</a></td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection