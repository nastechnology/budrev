@layout('layouts.main')
@section('content')
<div class="content">
@include('plugins.status')

<h1 class="page-header">Building Revenues <small>All Revenues</small></h1>

<table class="table table-striped">
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
		<tr>
			<td colspan="9">You have already submitted your revenues</td>
		</tr>
	</tbody>
</table>

</form>


</div>
@endsection