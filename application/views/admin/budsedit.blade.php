@layout('layouts.main')

@section('content')
<div class="content">
	@include('plugins.status')
	<h1 class="page-header">Budgets <small>Add Budget</small></h1>
	<form action="/admin/buds/edit" class="form-horizontal">
		<div class="control-group">
			<label for="ti" class="control-label">TI</label>
			<div class="controls"><input type="text" id='ti' name='ti' value="{{ $budget->ti }}"></div>
		</div>
		<div class="control-group">
			<label for="fund" class="control-label">FUND</label>
			<div class="controls"><input type="text" id="fund" name="fund" value="{{ $budget->fund }}"></div>
		</div>
		<div class="control-group">
			<label for="function" class="control-label">FUNCTION</label>
			<div class="controls"><input type="text" id="function" name="function" value="{{ $budget->function }}"></div>
		</div>
		<div class="control-group">
			<label for="object" class="control-label">OBJECT</label>
			<div class="controls"><input type="text" id="object" name="object" value="{{ $budget->object }}"></div>
		</div>
		<div class="control-group">
			<label for="scc" class="control-label">SCC</label>
			<div class="controls"><input type="text" id="scc" name="scc" value="{{ $budget->scc }}"></div>
		</div>
		<div class="control-group">
			<label for="subject" class="control-label">SUBJECT</label>
			<div class="controls"><input type="text" id="subject" name="subject" value="{{ $budget->subject }}"></div>
		</div>
		<div class="control-group">
			<label for="opu" class="control-label">OPU</label>
			<div class="controls"><input type="text" id="opu" name="opu" value="{{ $budget->opu }}"></div>
		</div>
		<div class="control-group">
			<label for="il" class="control-label">IL</label>
			<div class="controls"><input type="text" id="il" name="il" value="{{ $budget->il }}"></div>
		</div>
		<div class="control-group">
			<label for="job" class="control-label">JOB</label>
			<div class="controls"><input type="text" id="job" name="job" value="{{ $budget->job }}"></div>
		</div>
		<div class="control-group">
			<label for="description" class="control-label">DESCRIPTION</label>
			<div class="controls"><input type="text" id="description" name="description" value="{{ $budget->description }}"></div>
		</div>
		<input type="hidden" name="id" value="{{ $budget->id }}">
		<div class="control-group">
			<div class="controls"><input type="submit" value="Submit" id="submit" class="btn" name="submit"></div>
		</div>
	</form>
</div>	
@endsection