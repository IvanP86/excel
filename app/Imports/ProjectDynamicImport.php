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
            if (!isset($map['dynamic'])) {
                continue;
            }
            $dynamicHeadings = $this->getRowsMap(self::$headings)['dynamic'];
            foreach($map['dynamic'] as $key => $item){
                Payment::create([
                    'project_id' =>$project->id,
                    'title' => $dynamicHeadings[$key],
                    'value' => $item
                ]);
            }
        }
    }

    private function getRowsMap($row)
    {
        $static = [];
        $dynamic = [];

        foreach ($row as $key => $value) {
            if ($value != null) {
                $key > 12 ? $dynamic[$key] = $value : $static[$key] = $value;
            }
        }

        return [
            'static' => $static,
            'dynamic' => $dynamic
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
        return array_replace([
            "0" => "required|string",
            "1" => "required|string",
            "2" => "required|integer",
            "9" => "required|integer",
            "7" => "nullable|integer",
            "3" => "nullable|string",
            "5" => "nullable|string",
            "6" => "nullable|string",
            "8" => "nullable|string",
            "4" => "nullable|integer",
            "10" => "nullable|integer",
            "11" => "nullable|string",
            "12" => "nullable|numeric",
        ], $this->getDynamicValidation());
    }

    public function onFailure(Failure ...$failures)
    {
        $map = [];
        foreach ($failures as $failure) {
            foreach ($failure->errors() as $error) {
                $map[] = [
                    'key' => $this->getAttributMap()[$failure->attribute()],
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

    private function getAttributMap(): array
    {
        return array_replace([
            "0" => "Тип",
            "1" => "Наименование",
            "2" => "Дата создания",
            "9" => "Подписание договора",
            "7" => "Дедлайн",
            "3" => "Сетевик",
            "5" => "Наличие аутсорсинга",
            "6" => "Наличие инвесторов",
            "8" => "Сдача в срок",
            "4" => "Количество участников",
            "10" => "Количество услуг",
            "11" => "Комментарий",
            "12" => "Значение эффективности",
        ], $this->getRowsMap(self::$headings)['dynamic']);
    }

    public function startRow(): int
    {
        return 2;
    }

    public static function beforeSheet(BeforeSheet $event): void
    {
        self::$headings = $event->getSheet()->getDelegate()->toArray()[0];
    }

    private function getDynamicValidation(): array
    {
        $headers = $this->getRowsMap(self::$headings)['dynamic'];
        foreach($headers as $key => $value){
            $headers[$key] = 'required|integer';
        }

        return $headers;
    }
}
