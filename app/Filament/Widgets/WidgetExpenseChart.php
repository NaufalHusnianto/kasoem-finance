<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class WidgetExpenseChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Pengeluaran';

    protected static string $color = 'danger'; // default color for the chart

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
                $data = Trend::query(Transaction::expenses())
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perDay()
                    ->sum('amount');
                break;

            case 'monthly':
                $data = Trend::query(Transaction::expenses())
                    ->between(
                        start: $start,
                        end: $end,
                    )
                    ->perMonth()
                    ->sum('amount');
                break;

            case 'yearly':
                $data = Trend::query(Transaction::expenses())
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
                    'label' => 'Pengeluaran',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)', // light danger color
                    'borderColor' => 'rgba(220, 53, 69, 1)', // danger color
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
