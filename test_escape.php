<?php
$str = htmlspecialchars(json_encode(["name" => "Sandi"]), ENT_QUOTES, "UTF-8");
echo htmlspecialchars($str, ENT_QUOTES, "UTF-8");

