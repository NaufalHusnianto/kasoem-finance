<?php

namespace App\Filament\Resources\TransactionResource\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ExportTransactionsToPdf extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Export to PDF')
            ->action(function (Model $record) {
                $pdf = PDF::loadView('pdf.transactions', ['transaction' => $record]);

                return response()->streamDownload(
                    fn () => print($pdf->output()),
                    'transaction.pdf'
                );
            });
    }

    public static function make(?string $name = null): static
    {
        return parent::make($name);
    }
}

