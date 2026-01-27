<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use DateTimeImmutable;

final class BusinessDays
{
  /**
   * Suma N días hábiles a una fecha (excluye sábados, domingos y feriados nacionales).
   * Devuelve DateTimeImmutable.
   */
  public static function addBusinessDays(DateTimeImmutable $start, int $days): DateTimeImmutable
  {
    $feriados = self::loadFeriadosNacionales($start, $days);

    $date = $start;
    $added = 0;

    while ($added < $days) {
      $date = $date->modify('+1 day');
      $dow = (int)$date->format('N'); // 6=sábado, 7=domingo

      if ($dow >= 6) continue;

      $key = $date->format('Y-m-d');
      if (isset($feriados[$key])) continue;

      $added++;
    }

    return $date;
  }

  private static function loadFeriadosNacionales(DateTimeImmutable $start, int $days): array
  {
    // Cargamos un rango razonable (start..start+90) para no depender del N exacto
    $from = $start->format('Y-m-d');
    $to = $start->modify('+90 days')->format('Y-m-d');

    $sql = "SELECT fecha FROM feriados
            WHERE ambito = 'NACIONAL'
              AND fecha BETWEEN :from AND :to";
    $st = DB::pdo()->prepare($sql);
    $st->execute(['from' => $from, 'to' => $to]);

    $set = [];
    foreach ($st->fetchAll() as $row) {
      $set[$row['fecha']] = true;
    }
    return $set;
  }
}
