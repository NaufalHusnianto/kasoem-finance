<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        $income = Transaction::incomes()->get()->sum('amount');  //mengambil data pemasukan dari transaction
        $expense = Transaction::expenses()->get()->sum('amount');    //mengambil data pengeluaran dari transaction
        $difference = $income - $expense; //selisih

        // deskripsi, warna, dan ikon untuk nilai selisih
        if ($difference < 0) {
            $description = 'are at a loss';
            $color = 'danger';
            $descriptionIcon = 'heroicon-o-arrow-down';
        } else {
            $description = 'are profitable';
            $color = 'success';
            $descriptionIcon = 'heroicon-m-arrow-trending-up';
        }

        return [
            stat::make('Total Pengeluaran', $expense)
                ->description('pengeluaran')
                ->descriptionIcon('heroicon-o-arrow-up')
                ->color('danger'),
            stat::make('Total Pemasukan', $income)
                ->description('pemasukan')
                ->descriptionIcon('heroicon-o-arrow-down')
                ->color('success'),
            stat::make('Selisih', $difference)
                ->description($description)
                ->descriptionIcon($descriptionIcon)
                ->color($color),
            Stat::make('Total pelanggan', Customer::count())
        ];
    }
}
