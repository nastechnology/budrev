@layout('layouts.main')
@section('content')
<div class="content">
@include('plugins.status')
<div class="page-header"><h1>Building Budgets <small>All Budgets</small></h1></div>

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
		<tr>
			<td colspan="12">You have already submitted your budgets</td>
		</tr>
	</tbody>
</table>

</div>


@endsection