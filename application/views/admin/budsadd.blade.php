@layout('layouts.main')

@section('content')
<div class="content">
	@include('plugins.status')
	<h1 class="page-header">Budgets <small>Add Budget</small></h1>
	<form action="/admin/buds/add" class="form-horizontal">
		<div class="control-group">
			<label for="ti" class="control-label">TI</label>
			<div class="controls"><input type="text" id='ti' name='ti'></div>
		</div>
		<div class="control-group">
			<label for="fund" class="control-label">FUND</label>
			<div class="controls"><input type="text" id="fund" name="fund"></div>
		</div>
		<div class="control-group">
			<label for="function" class="control-label">FUNCTION</label>
			<div class="controls"><input type="text" id="function" name="function"></div>
		</div>
		<div class="control-group">
			<label for="object" class="control-label">OBJECT</label>
			<div class="controls"><input type="text" id="object" name="object"></div>
		</div>
		<div class="control-group">
			<label for="scc" class="control-label">SCC</label>
			<div class="controls"><input type="text" id="scc" name="scc"></div>
		</div>
		<div class="control-group">
			<label for="subject" class="control-label">SUBJECT</label>
			<div class="controls"><input type="text" id="subject" name="subject"></div>
		</div>
		<div class="control-group">
			<label for="opu" class="control-label">OPU</label>
			<div class="controls"><input type="text" id="opu" name="opu"></div>
		</div>
		<div class="control-group">
			<label for="il" class="control-label">IL</label>
			<div class="controls"><input type="text" id="il" name="il"></div>
		</div>
		<div class="control-group">
			<label for="job" class="control-label">JOB</label>
			<div class="controls"><input type="text" id="job" name="job"></div>
		</div>
		<div class="control-group">
			<label for="description" class="control-label">DESCRIPTION</label>
			<div class="controls"><input type="text" id="description" name="description"></div>
		</div>
		<div class="control-group">
			<div class="controls"><input type="submit" value="Submit" id="submit" class="btn" name="submit"></div>
		</div>
	</form>
</div>	
@endsection