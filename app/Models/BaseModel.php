<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

abstract class BaseModel
{
  protected static function pdo(): PDO
  {
    return DB::pdo();
  }
}
