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
                if ($record->category->name === 'Pesanan') {
                    // Jika kategori adalah "Pesanan", lakukan ekspor PDF
                    $pdf = PDF::loadView('pdf.transactions', ['transaction' => $record]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'transaction.pdf'
                    );
                } else {
                    // Jika kategori bukan "Pesanan", beri respons yang sesuai
                    return response()->json([
                        'message' => 'Export to PDF is only available for transactions with category "Pesanan".'
                    ], 403); // Misalnya, 403 untuk akses ditolak
                }
            });
    }

    public static function make(?string $name = null): static
    {
        return parent::make($name);
    }
}

