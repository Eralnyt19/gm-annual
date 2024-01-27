

<html>
<body>

<?php

$customer = $_POST["name"];

// $roots = dirname(__DIR__) . DIRECTORY_SEPARATOR;

//define('PUBLIC_PATH', $roots. 'public' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH',  'public' . DIRECTORY_SEPARATOR);


$mymain = PUBLIC_PATH . 'index.php';

require $mymain;

?>


</body>
</html>

