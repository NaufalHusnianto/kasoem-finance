<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WidgetIncomeChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Pemasukan per bulan';

    protected static string $color = '#00BFFF';

    protected function getData(): array
    {
        $data = Trend::query(Transaction::incomes())
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->sum('amount');
 
        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan per bulan',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => static::$color,
                    'borderColor' => static::$color,
                    'fill' => false,
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
