<html>
<head>
    <title>Aria Automation - New Project</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<p><a href="list-all-projects.php">back</a></p>
	<?php include 'menu.php'; ?>
<body>
    <table class="minimal-table-small">
	<div class="minimal-table-auth">
    <form action="create-project.php" name="salva" method="POST">
        <div class="table-row">
            <div class="table-cell-title">NAME</div>
            <div class="table-cell"><input type="text" name="name" required></div>
        </div>
        <div class="table-row">
            <div class="table-cell-title">DESCRIPTION</div>
            <div class="table-cell"><input type="text" name="description" required></div>
        </div>
        <div class="table-row">
            <div class="table-cell" style="text-align: center;">
                <input type="submit" name="salva" value="SAVE" />
            </div>
        </div>
    </form>
</div>
	<?php include 'footer.php';?>
</body>
</html>