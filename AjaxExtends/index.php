<!DOCTYPE html>
<html>
<head>
	<title>prueba</title>
	<script src="jquery-3.2.1.min.js"></script>
	<script src="ajax-extend2.js"></script>
</head>
<body>
	<a href="pagina11.php">pagina 1</a> | <a href="pagina2.php">pagina 2</a>
	| <a href="pagina3.php">pagina 3</a> | <a href="pagina4.php">pagina 4</a>

	<div id="content"></div>


	<script>
		$(document).ready(function() {
			$("a").AjaxExtend({
				data : {
					Id : 333,
					Name: 'nombre',
					Apellido: 'apellido',
				},
			});
		});
	</script>
</body>
</html>