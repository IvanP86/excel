<?php

namespace App\Imports;

use App\Factory\ProjectDynamicFactory;
use App\Factory\ProjectFactory;
use App\Models\FailedRow;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Validators\Failure;

class ProjectDynamicImport implements ToCollection, WithValidation, SkipsOnFailure, WithStartRow, WithEvents
{
    use RegistersEventListeners;
    private $task;
    private static array $headings;

    const STATIC_ROW = 12;
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function collection(Collection $collection)
    {
        $typesMap = $this->getTypesMap(Type::all());
        foreach ($collection as $row) {
            // dd($this->getTypeId($typesMap, $row['tip']));
            if (!isset($row[1])) {
                continue;
            }
            $map = $this->getRowsMap($row);
            $projectFactory = ProjectDynamicFactory::make($typesMap, $map['static']);
            Project::create($projectFactory->getSelfValues());
            $project = Project::updateOrCreate([
                'type_id' => $projectFactory->getSelfValues()['type_id'],
                'title' => $projectFactory->getSelfValues()['title'],
                'created_at_time' => $projectFactory->getSelfValues()['created_at_time'],
                'contracted_at' => $projectFactory->getSelfValues()['contracted_at'],
            ], $projectFactory->getSelfValues());
            if (!isset($map['dymanic'])) {
                continue;
            }
            $dynamicHeadings = $this->getRowsMap(self::$headings)['dymanic'];
            foreach($map['dymanic'] as $key => $item){
                Payment::create([
                    'project_id' =>$project->id,
                    'title' => $dynamicHeadings[$key],
                    'value' => $item
                ]);
            }
            // $project = Project::create([
            //     'type_id' => $this->getTypeId($typesMap, $row['tip']),
            //     'title' => $row['naimenovanie'],
            //     'created_at_time' => Date::excelToDateTimeObject($row["data_sozdaniia"]),
            //     'contracted_at' => Date::excelToDateTimeObject($row["podpisanie_dogovora"]),
            //     'deadline' => isset($row["dedlain"]) ? Date::excelToDateTimeObject($row["dedlain"]) : null,
            //     'is_chain' => isset($row['setevik']) ? $this->getBoolean($row["setevik"]) : null,
            //     'is_on_time' => isset($row['sdaca_v_srok']) ? $this->getBoolean($row["sdaca_v_srok"]) : null,
            //     'has_outsource' => isset($row["nalicie_autsorsinga"]) ? $this->getBoolean($row["nalicie_autsorsinga"]) : null,
            //     'has_investors' => isset($row["nalicie_investorov"]) ? $this->getBoolean($row["nalicie_investorov"]) : null,
            //     'worker_count' => $row["kolicestvo_ucastnikov"] ?? null,
            //     'service_count' => $row["kolicestvo_uslug"] ?? null,
            //     'payment_first_step' => $row["vlozenie_v_pervyi_etap"] ?? null,
            //     'payment_second_step' => $row["vlozenie_vo_vtoroi_etap"] ?? null,
            //     'payment_third_step' => $row["vlozenie_v_tretii_etap"] ?? null,
            //     'payment_forth_step' => $row["vlozenie_v_cetvertyi_etap"] ?? null,
            //     'comment' => $row["kommentarii"] ?? null,
            //     'effective_value' => $row["znacenie_effektivnosti"] ?? null,
            // ]);
            // $projectFactory = ProjectFactory::make($typesMap, $row);
            // $project = Project::updateOrCreate([
            //     'type_id' => $projectFactory->getSelfValues()['type_id'],
            //     'title' => $projectFactory->getSelfValues()['title'],
            //     'created_at_time' => $projectFactory->getSelfValues()['created_at_time'],
            //     'contracted_at' => $projectFactory->getSelfValues()['contracted_at'],
            // ], $projectFactory->getSelfValues());
        }
    }

    private function getRowsMap($row)
    {
        $static = [];
        $dynamic = [];

        foreach ($row as $key => $value) {
            if ($value) {
                $key > 12 ? $dynamic[$key] = $value : $static[$key] = $value;
            }
        }

        return [
            'static' => $static,
            'dymanic' => $dynamic
        ];
    }

    private function getTypesMap($types): array
    {
        $map = [];
        foreach ($types as $type) {
            $map[$type->title] = $type->id;
        }
        return $map;
    }

    private function getTypeId($map, $title): int
    {
        return isset($map[$title]) ? $map[$title] : Type::create([
            'title' => $title
        ])->id;
    }

    private function getBoolean($item): bool
    {
        return $item == 'Да';
    }

    public function rules(): array
    {
        return [
            // "tip" => "required|string",
            // "naimenovanie" => "required|string",
            // "data_sozdaniia" => "required|integer",
            // "podpisanie_dogovora" => "required|integer",
            // "dedlain" => "nullable|integer",
            // "setevik" => "nullable|string",
            // "sdaca_v_srok" => "nullable|string",
            // "nalicie_autsorsinga" => "nullable|string",
            // "nalicie_investorov" => "nullable|string",
            // "kolicestvo_ucastnikov" => "nullable|integer",
            // "kolicestvo_uslug" => "nullable|integer",
            // "vlozenie_v_pervyi_etap" => "nullable|integer",
            // "vlozenie_vo_vtoroi_etap" => "nullable|integer",
            // "vlozenie_v_tretii_etap" => "nullable|integer",
            // "vlozenie_v_cetvertyi_etap" => "nullable|integer",
            // "kommentarii" => "nullable|string",
            // "znacenie_effektivnosti" => "nullable|numeric",
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $map = [];
        foreach ($failures as $failure) {
            foreach ($failure->errors() as $error) {
                $map[] = [
                    'key' => $this->attributMap()[$failure->attribute()],
                    'row' => $failure->row(),
                    'message' => $error,
                    'task_id' => $this->task->id
                ];
            }
        }
        if (count($map) > 0) {
            FailedRow::insertFailedRows($map, $this->task);
        }
    }

    private function attributMap(): array
    {
        return [
            "tip" => "Тип",
            "naimenovanie" => "Наименование",
            "data_sozdaniia" => "Дата создания",
            "podpisanie_dogovora" => "Подписание договора",
            "dedlain" => "Дедлайн",
            "setevik" => "Сетевик",
            "sdaca_v_srok" => "Сдача в срок",
            "nalicie_autsorsinga" => "Наличие аутсорсинга",
            "nalicie_investorov" => "Наличие инвесторов",
            "kolicestvo_ucastnikov" => "Количество участников",
            "kolicestvo_uslug" => "Количество услуг",
            "vlozenie_v_pervyi_etap" => "Вложение в первый этап",
            "vlozenie_vo_vtoroi_etap" => "Вложение во второй этап",
            "vlozenie_v_tretii_etap" => "Вложение в третий этап",
            "vlozenie_v_cetvertyi_etap" => "Вложение в четвертый этап",
            "kommentarii" => "Комментарий",
            "znacenie_effektivnosti" => "Значение эффективности",
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        self::$headings = $event->getSheet()->getDelegate()->toArray()[0];
    }
}
