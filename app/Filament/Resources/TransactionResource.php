<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Actions\ExportTransactionsToPdf;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-plus';

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $isKaryawan = $user && $user->hasRole('Karyawan');

        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->reactive()
                            ->options(function () use ($isKaryawan) {
                                $query = Category::query();
                                if ($isKaryawan) {
                                    $query->where('name', 'Pesanan');
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->afterStateHydrated(function ($state, callable $set) {
                                $category = \App\Models\Category::find($state);
                                $set('is_order', $category ? $category->name === 'Pesanan' : false);
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                $category = \App\Models\Category::find($state);
                                $set('is_order', $category ? $category->name === 'Pesanan' : false);
                            }),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->visible(fn ($get) => !$get('is_order')),
                        Forms\Components\Textarea::make('note')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->imageEditor(),
                    ]),
                ])->columnSpanFull(),

                // Form Group Pesanan jika kategori yang dipilih merupakan pesanan(bernilai 'Pesanan')
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Pesanan')
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->relationship('user', 'name')
                                ->default($user->id)
                                ->options([
                                    $user->id => $user->name,
                                ]),
                            Forms\Components\Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->searchable(),
                            Forms\Components\TextInput::make('number')
                                ->default('OR-' . random_int(100000, 9999999))
                                ->disabled()
                                ->dehydrated(),
                        ]),

                    Forms\Components\Wizard\Step::make('Barang dibeli')
                        ->schema([
                            // form input barang
                            Forms\Components\Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Product')
                                        ->options(Product::query()->pluck('name', 'id'))
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                        $set('unit_price', Product::find($state)?->price ?? 0)),
                                    Forms\Components\TextInput::make('quantity')
                                        ->numeric()
                                        ->live()
                                        ->dehydrated()
                                        ->required(),
                                    Forms\Components\TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->disabled()
                                        ->dehydrated()
                                        ->numeric()
                                        ->required(),
                                    
                                    // total price setiap item
                                    Forms\Components\Placeholder::make('sub_total_price')
                                        ->label('Sub Total Harga')
                                        ->content(function ($get) {
                                            return intval($get('quantity')) * floatval($get('unit_price'));
                                        })
                                    ]),
                            ])
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Hitung total harga dari subtotal-subtotal harga item produk
                                $items = $get('items') ?? [];
                                $total = array_reduce($items, function ($carry, $item) {
                                    return $carry + (intval($item['quantity']) * floatval($item['unit_price']));
                                }, 0);
                                $set('amount', $total);
                            }),
                    
                    // Form Pembayaran
                    Forms\Components\Wizard\Step::make('Pembayaran')
                        ->schema([
                            Forms\Components\TextInput::make('amount')
                                ->label('Total Harga')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->visible(fn ($get) => $get('is_order')),
                            Forms\Components\TextInput::make('pay_amount')
                                ->label('Uang Dibayar')
                                ->numeric()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $amount = floatval($get('amount'));
                                    $payAmount = floatval($state);
                                    $cashChange = $payAmount - $amount;
                                    $set('cash_change', $cashChange);
                                }),
                            Forms\Components\TextInput::make('cash_change')
                                ->label('Kembalian')
                                ->numeric()
                                ->disabled()
                                ->required(),
                        ]),
                ])->columnSpanFull()->hidden(fn ($get) => !$get('is_order')), //form group pesanan hanya muncul jika nilai category berupa Pesanan
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $isKaryawan = Auth::user()->hasRole('Karyawan');

                if ($isKaryawan) {
                    $userId = Auth::user()->id;
                    $query->where('user_id', $userId);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('category.is_expense')
                    ->label('Tipe')
                    ->falseIcon('heroicon-o-arrow-down')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-arrow-up')
                    ->trueColor('danger')
                    ->boolean(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('number')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('pay_amount')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('cash_change')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ExportTransactionsToPdf::make('exportTransactionsToPdf')->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }
}
