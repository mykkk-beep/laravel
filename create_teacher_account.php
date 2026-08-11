<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = new App\Models\User;
$user->name = 'Teacher Demo';
$user->email = 'teacher@example.com';
$user->password = Hash::make('password');
$user->role = 'teacher';
$user->active = true;
$user->save();
echo $user->id, PHP_EOL;
