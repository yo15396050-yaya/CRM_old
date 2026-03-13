<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TaskFile;
use Illuminate\Support\Facades\File;

$files = TaskFile::orderBy('id', 'desc')->take(10)->get();

foreach ($files as $f) {
    $path = public_path('user-uploads/' . TaskFile::FILE_PATH . '/' . $f->task_id . '/' . $f->hashname);
    $exists = File::exists($path) ? 'OUI' : 'NON';
    echo "ID: {$f->id}, TaskID: {$f->task_id}, File: {$f->filename}, Exists: {$exists}, Created: {$f->created_at}\n";
    if ($exists === 'NON') {
        echo "   Path cherché: {$path}\n";
    }
}
