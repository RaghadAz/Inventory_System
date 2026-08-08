<?php

namespace App\Filament\Admin\Pages;

use App\Models\Product;
use App\Models\Sale;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class MonthlyReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.admin.pages.monthly-report';
    protected static ?string $navigationLabel = 'Monthly Financial Report';
    protected ?string $heading = 'Detailed Profit & Loss Statement';

    public $month;
    public $year;
    public $monthlyDetails = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function mount(): void
    {
        $this->month = date('m');
        $this->year = date('Y');

        $this->form->fill([
            'month' => $this->month,
            'year' => $this->year,
        ]);

        $this->getReportData();
        $this->checkSystemAlerts();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('month')
                    ->label('Select Report Month')
                    ->options([
                        '01' => 'January (1)',
                        '02' => 'February (2)',
                        '03' => 'March (3)',
                        '04' => 'April (4)',
                        '05' => 'May (5)',
                        '06' => 'June (6)',
                        '07' => 'July (7)',
                        '08' => 'August (8)',
                        '09' => 'September (9)',
                        '10' => 'October (10)',
                        '11' => 'November (11)',
                        '12' => 'December (12)',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn() => $this->getReportData()),

                Select::make('year')
                    ->label('Select Year')
                    ->options(array_combine(range(date('Y') - 5, date('Y') + 5), range(date('Y') - 5, date('Y') + 5)))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn() => $this->getReportData()),
            ])->columns(2);
    }

    protected function checkSystemAlerts(): void
    {
        $lowStockCount = Product::where('quantity', '<', 5)->count();

        if ($lowStockCount > 0) {
            Notification::make()
                ->title('⚠️ Critical Stock Alert')
                ->body("You have {$lowStockCount} items nearing stock depletion. Please review and restock immediately.")
                ->warning()
                ->duration(10000)
                ->send();
        }
    }

    public function getReportData()
    {
        if (!$this->month || !$this->year) {
            return;
        }

        $salesData = DB::table('sales')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(id) as count'),
                DB::raw('SUM(total_price) as sales'),
                DB::raw('SUM(total_profit) as profit_from_sales')
            )
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('date');

        $expensesData = DB::table('expenses')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as expenses')
            )
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('date');

        $allDates = $salesData->keys()->merge($expensesData->keys())->unique()->sortDesc();

        $this->monthlyDetails = [];

        foreach ($allDates as $date) {
            $salesRow = $salesData->get($date);
            $expenseRow = $expensesData->get($date);

            $sales = $salesRow ? (float)$salesRow->sales : 0;
            $expenses = $expenseRow ? (float)$expenseRow->expenses : 0;

            $profitFromSales = $salesRow ? (float)$salesRow->profit_from_sales : 0;

            $netProfit = $profitFromSales - $expenses;


            $this->monthlyDetails[] = (object)[
                'date' => $date,
                'count' => $salesRow ? $salesRow->count : 0,
                'sales' => $sales,
                'expenses' => $expenses,
                'profit' => $netProfit,
            ];
        }
    }

    public function exportToExcel()
    {
        $data = $this->monthlyDetails;

        $books = [
            ['Date', 'Transactions Count', 'Total Sales ', 'Total Expenses', 'Net Profit/Loss']
        ];

        foreach ($data as $row) {
            $profitText = $row->profit >= 0
                ? '+' . number_format($row->profit, 2) . '  SYP (Profit)'
                : number_format(abs($row->profit), 2) . ' SYP (Loss)';

            $books[] = [
                (string)$row->date,
                (int)$row->count . ' Transactions',
                (float)$row->sales,
                (float)$row->expenses,
                $profitText
            ];
        }

        $fileName = "Financial_Report_{$this->month}_{$this->year}.xlsx";
        $filePath = storage_path('app/public/' . $fileName);

        \Shuchkin\SimpleXLSXGen::fromArray($books)->saveAs($filePath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
