@layout('layouts/main')
@section('content')
<div class="content">
	@if (isset($errors) && count($errors->all()) > 0)
	<div class="alert alert-error">
	    <a class="close" data-dismiss="alert" href="#">×</a>
	    <h4 class="alert-heading">Oh Snap!</h4>
	    <ul>
	    @foreach ($errors->all('<li>:message</li>') as $message)
	    {{ $message }}
	    @endforeach
	    </ul>
	</div>
	@elseif (!is_null(Session::get('status_error')))
	<div class="alert alert-error">
	    <a class="close" data-dismiss="alert" href="#">×</a>
	    <h4 class="alert-heading">Oh Snap!</h4>
	    @if (is_array(Session::get('status_error')))
	        <ul>
	        @foreach (Session::get('status_error') as $error)
	            <li>{{ $error }}</li>
	        @endforeach
	        </ul>
	    @else
	        {{ Session::get('status_error') }}
	    @endif
	</div>
	@elseif (!is_null(Session::get('status_success')))
	<div class="alert alert-success">
	    <a class="close" data-dismiss="alert" href="#">×</a>
	    <h4 class="alert-heading">Success!</h4>
	    @if (is_array(Session::get('status_success')))
	        <ul>
	        @foreach (Session::get('status_success') as $success)
	            <li>{{ $success }}</li>
	        @endforeach
	        </ul>
	    @else
	        {{ Session::get('status_success') }}
	    @endif
	</div>
	@else
	You must have received an email link with an access key to view this page.	
	@endif
</div>
@endsection