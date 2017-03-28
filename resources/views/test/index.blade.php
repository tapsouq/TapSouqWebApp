<!DOCTYPE html>
<html>
<head>
	<title>test</title>
</head>
<body>
	<form method="post" action="{{ url('test') }}">
		<input type="file" name="file">
		{{ csrf_field() }}
		<input type="submit" name="">
	</form>
</body>
</html>