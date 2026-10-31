<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\ManageCustomers;
use App\Models\Customer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Pelanggan';

    protected static ?string $navigationLabel = 'Prospek & pelanggan';

    protected static ?string $modelLabel = 'kontak';

    protected static ?string $pluralModelLabel = 'prospek dan pelanggan';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama kontak')
                    ->required()
                    ->maxLength(255),
                TextInput::make('company_name')
                    ->label('Nama perusahaan')
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->label('Nomor telepon')
                    ->maxLength(50),
                Select::make('source')
                    ->label('Sumber prospek')
                    ->options(Customer::SOURCES)
                    ->default('other')
                    ->required(),
                Select::make('campaign_id')
                    ->label('Kampanye')
                    ->relationship('campaign', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('status')
                    ->label('Status')
                    ->options(Customer::STATUSES)
                    ->default(Customer::STATUS_PROSPECT)
                    ->required(),
                DatePicker::make('follow_up_date')
                    ->label('Tanggal tindak lanjut'),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(2),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama kontak')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                TextColumn::make('source')
                    ->label('Sumber')
                    ->formatStateUsing(fn (?string $state): string => Customer::SOURCES[$state] ?? '-')
                    ->sortable(),
                TextColumn::make('campaign.name')
                    ->label('Kampanye'),
                TextColumn::make('follow_up_date')
                    ->label('Tindak lanjut')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Customer::STATUSES[$state] ?? '-')
                    ->color(fn (?string $state): string => Customer::STATUS_COLORS[$state] ?? 'gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Customer::STATUSES),
                SelectFilter::make('source')
                    ->label('Sumber')
                    ->options(Customer::SOURCES),
            ])
            ->recordActions([
                Action::make('convertToCustomer')
                    ->label('Jadikan pelanggan')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Customer $record): bool => $record->status === Customer::STATUS_PROSPECT)
                    ->successNotificationTitle('Prospek berhasil dikonversi menjadi pelanggan')
                    ->action(fn (Customer $record) => $record->update([
                        'status' => Customer::STATUS_CUSTOMER,
                    ])),
                EditAction::make()
                    ->label('Ubah'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCustomers::route('/'),
        ];
    }
}
