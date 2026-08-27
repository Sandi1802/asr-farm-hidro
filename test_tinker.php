<?php
$req = Request::create('/api/racks/1/update-age', 'POST', ['plant_name' => 'Pakcoy', 'usia_hari' => 12]);
$req->headers->set('Accept', 'application/json');
$res = app()->handle($req);
echo "STATUS: " . $res->getStatusCode() . "\n";
echo "BODY: " . $res->getContent();
