@layout('layouts.main')

@section('content')
<div class="content">
	<h1 class="page-header">Building Revenues <small>View Submitted Revenues by Building</small></h1>
	<label for="building">Building</label>
	<select name="building" id="building" onchange="getData(this.value)">
			<option value="" selected>SELECT BUILDING</option>
		@foreach($buildings as $building)
			<option value="{{ $building->id }}">{{ $building->building }}</option>
		@endforeach
	</select>
	
	<form action="/admin/revenue/edit" class="form-horizontal">
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
				<th>AMOUNT</th>
			</tr>
		</thead>
		<tbody id='tbody'>
			
		</tbody>
	</table>
	<input type="submit" value="Submit" class="btn" name="submit" id="submit">
	</form>
</div>
<script type="text/javascript">
	function getData(str){
		if (str==""){
		  document.getElementById("tbody").innerHTML="";
  		  return;
  		} 
		if (window.XMLHttpRequest){
			// code for IE7+, Firefox, Chrome, Opera, Safari
		    xmlhttp=new XMLHttpRequest();
  		} else {
  			// code for IE6, IE5
		  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.onreadystatechange=function(){
		  	if (xmlhttp.readyState==4 && xmlhttp.status==200){
			    document.getElementById("tbody").innerHTML=xmlhttp.responseText;
		    }
	  	}
		xmlhttp.open("GET","/admin/revenue/json?a=edit&id="+str,true);
		xmlhttp.send();
	}
</script>
@endsection