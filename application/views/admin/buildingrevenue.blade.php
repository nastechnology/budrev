@layout('layouts.main')
@section('content')
<div class="content">
@include('plugins.status')

<h1 class="page-header">Building Revenues <small>All Revenues</small></h1>

<form action="/admin/building/revenue" method="post" name="revenuereport">
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
		@foreach($revenues as $revenue)
			@if($prevFund != $revenue->fund)
			<tr>
				<td span="12"> Fund: 00{{ $revenue->fund }}</td>
			</tr>
			@else
			<tr>
				<td>{{ $revenue->ti }}</td>
				<td>{{ $revenue->fund }}</td>
				<td>{{ $revenue->receipt }}</td>
				<td>{{ $revenue->scc }}</td>
				<td>{{ $revenue->subject }}</td>
				<td>{{ $revenue->opu }}</td>
				<td>{{ $revenue->description }}</td>
				<td><div class='input-prepend'><span class='add-on'>$</span><input class='input-mini' type='text' name='proposed-{{ $revenue->id }}'></input></div></td>
				<td><a href="#" class="btn btn-info" rel="popover" title="Previous Years" data-content="{{ $expended[$revenue->id] }}">Previous Years</a></td>
			</tr>
			@endif
			<?php $prevFund = $revenue->fund; ?>
		@endforeach
	</tbody>
</table>
<br/><input type='submit' name='submit' class="btn" value='Submit' /></form>

</form>

<script type="text/javascript">
	$(function() {
	    $("form").bind("keypress", function(e) {
	            if (e.keyCode == 13) return false;
	      });
	});
</script>
</div>
@endsection
