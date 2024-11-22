<?php

namespace App\Factory;

use App\Models\Type;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Str;

class ProjectDynamicFactory
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
        $this->comment = $comment;
        $this->effectiveValue = $effectiveValue;
    }

    public static function make($map, $row): ProjectDynamicFactory
    {
        return new self(
            self::getTypeId($map, $row[0]),
            $row[1],
            Date::excelToDateTimeObject($row[2]),
            Date::excelToDateTimeObject($row[9]),
            isset($row[7]) ? Date::excelToDateTimeObject($row[7]) : null,
            isset($row[3]) ? self::getBoolean($row[3]) : null,
            isset($row[8]) ? self::getBoolean($row[8]) : null,
            isset($row[5]) ? self::getBoolean($row[5]) : null,
            isset($row[6]) ? self::getBoolean($row[6]) : null,
            $row[4] ?? null,
            $row[10] ?? null,
            $row[11] ?? null,
            $row[12] ?? null,
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
