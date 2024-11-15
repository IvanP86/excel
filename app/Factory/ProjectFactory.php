<?php

namespace App\Factory;

use App\Models\Type;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Str;

class ProjectFactory
{
    private $typeId;
    private $title;
    private $createdAtTime;
    private $contractedAt;
    private $deadline;
    private $isChain;
    private $isOnTime;
    private $hasOutsource;
    private $hasInvestors;
    private $workerCount;
    private $serviceCount;
    private $paymentFirstStep;
    private $paymentSecondStep;
    private $paymentThirdStep;
    private $paymentForthStep;
    private $comment;
    private $effectiveValue;

    public function __construct(
        $typeId,
        $title,
        $createdAtTime,
        $contractedAt,
        $deadline,
        $isChain,
        $isOnTime,
        $hasOutsource,
        $hasInvestors,
        $workerCount,
        $serviceCount,
        $paymentFirstStep,
        $paymentSecondStep,
        $paymentThirdStep,
        $paymentForthStep,
        $comment,
        $effectiveValue
    ) {
        $this->typeId = $typeId;
        $this->title = $title;
        $this->createdAtTime = $createdAtTime;
        $this->contractedAt = $contractedAt;
        $this->deadline = $deadline;
        $this->isChain = $isChain;
        $this->isOnTime = $isOnTime;
        $this->hasOutsource = $hasOutsource;
        $this->hasInvestors = $hasInvestors;
        $this->workerCount = $workerCount;
        $this->serviceCount = $serviceCount;
        $this->paymentFirstStep = $paymentFirstStep;
        $this->paymentSecondStep = $paymentSecondStep;
        $this->paymentThirdStep = $paymentThirdStep;
        $this->paymentForthStep = $paymentForthStep;
        $this->comment = $comment;
        $this->effectiveValue = $effectiveValue;
    }

    public static function make($map, $row): ProjectFactory
    {
        return new self(
            self::getTypeId($map, $row['tip']),
            $row['naimenovanie'],
            Date::excelToDateTimeObject($row["data_sozdaniia"]),
            Date::excelToDateTimeObject($row["podpisanie_dogovora"]),
            isset($row["dedlain"]) ? Date::excelToDateTimeObject($row["dedlain"]) : null,
            isset($row['setevik']) ? self::getBoolean($row["setevik"]) : null,
            isset($row['sdaca_v_srok']) ? self::getBoolean($row["sdaca_v_srok"]) : null,
            isset($row["nalicie_autsorsinga"]) ? self::getBoolean($row["nalicie_autsorsinga"]) : null,
            isset($row["nalicie_investorov"]) ? self::getBoolean($row["nalicie_investorov"]) : null,
            $row["kolicestvo_ucastnikov"] ?? null,
            $row["kolicestvo_uslug"] ?? null,
            $row["vlozenie_v_pervyi_etap"] ?? null,
            $row["vlozenie_vo_vtoroi_etap"] ?? null,
            $row["vlozenie_v_tretii_etap"] ?? null,
            $row["vlozenie_v_cetvertyi_etap"] ?? null,
            $row["kommentarii"] ?? null,
            $row["znacenie_effektivnosti"] ?? null,
        );
    }

    private static function getTypeId($map, $title): int
    {
        return isset($map[$title]) ? $map[$title] : Type::create([
            'title' => $title
        ])->id;
    }

    private static function getBoolean($item): bool
    {
        return $item == 'Да';
    }

    public function getSelfValues(): array
    {
        $props = get_object_vars($this);
        $res = [];
        foreach ($props as $key => $prop) {
            $res[Str::snake($key)] = $prop;
        }
        return $res;
    }
}
