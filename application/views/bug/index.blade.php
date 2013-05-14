@layout('layouts.main')

@section('content')
<div class="content">
@include('plugins.status')
	
	<div class="page-header"><h1>Bug <small>Submit Bug</small></h1></div>
	<form class="form-horizontal" method="post" action="/bug">
		<div class="control-group">
                <label class="control-label" for="title">Title</label>
                <div class="controls">
                    <input type="text" name="title" id="title" value=""  class="input-xxlarge" placeholder="Error Submitting Data">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="body">Description</label>
                <div class="controls">
                    <textarea name="body" id="body" class="input-xxlarge"></textarea>
                </div>
            </div>
            
            <div class="control-group">
                <div class="controls">
                    <input type="submit" name="submit" id="submit" value="Submit" class="btn">
                </div>
            </div>
	</form>

</div>
@endsection