<?php

use App\System;

require_once 'app/bootstrap.php';

System::StartFileMaker();
if (System::$dbStatus) {
    migrations('*')->up();
}
refresh();
