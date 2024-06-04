<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Forms\Components\Select;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class WidgetIncomeChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Pemasukan';

    protected static string $color = 'rgba(0, 191, 255, 1)'; // sky color

    public ?string $filter = 'monthly'; // default filter

    protected function getFilters(): array
    {
        return [
            'daily' => 'Tiap Hari',
            'monthly' => 'Tiap Bulan',
            'yearly' => 'Tiap Tahun',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('filter')
                ->options($this->getFilters())
                ->default('monthly')
                ->reactive()
                ->afterStateUpdated(fn () => $this->call('updateChart')),
        ];
    }

    protected function getData(): array
    {
        $start = now()->startOfYear();
        $end = now()->endOfYear();

        switch ($this->filter) {
            case 'daily':
                $data = Trend::query(Transaction::incomes())
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perDay()
                    ->sum('amount');
                break;

            case 'monthly':
                $data = Trend::query(Transaction::incomes())
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perMonth()
                    ->sum('amount');
                break;

            case 'yearly':
                $data = Trend::query(Transaction::incomes())
                    ->between(
                        start: $start->startOfDecade(),
                        end: $end->endOfDecade(),
                    )
                    ->perYear()
                    ->sum('amount');
                break;

            default:
                $data = collect([]);
                break;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(0, 191, 255, 0.2)', // light sky color
                    'borderColor' => 'rgba(0, 191, 255, 1)', // sky color
                    'fill' => false,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => match ($this->filter) {
                'daily' => Carbon::parse($value->date)->format('d M'),
                'monthly' => Carbon::parse($value->date)->format('M Y'),
                'yearly' => $value->date,
            }),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
