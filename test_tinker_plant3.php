<?php
$user = \App\Models\User::whereIn('role_agri', ['produksi', 'produksi_gh'])->first();
if (!$user) {
    echo "No user with role";
    exit;
}
Auth::login($user);
$req = Request::create('/api/racks/1/plant', 'POST', ['plant_name' => 'Pakcoy', 'jumlah' => 2, 'planted_at' => '2026-08-10T12:00:00.000Z']);
$req->headers->set('Accept', 'application/json');
$res = app()->handle($req);
echo "STATUS: " . $res->getStatusCode() . "\n";
echo "BODY: " . $res->getContent();
