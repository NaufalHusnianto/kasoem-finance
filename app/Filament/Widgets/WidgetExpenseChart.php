<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WidgetExpenseChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Pengeluaran per bulan';

    protected static string $color = 'danger';

    protected function getData(): array
    {
        $data = Trend::query(Transaction::expenses())
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->sum('amount');
 
        return [
            'datasets' => [
                [
                    'label' => 'Pengaluran per bulan',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
