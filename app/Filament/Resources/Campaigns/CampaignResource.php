<?php

namespace App\Filament\Resources\Campaigns;

use App\Filament\Resources\Campaigns\Pages\ManageCampaigns;
use App\Models\Campaign;
use BackedEnum;
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

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Pemasaran';

    protected static ?string $navigationLabel = 'Kampanye';

    protected static ?string $modelLabel = 'kampanye';

    protected static ?string $pluralModelLabel = 'kampanye pemasaran';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama kampanye')
                    ->required()
                    ->maxLength(150),
                Select::make('barang_id')
                    ->label('Barang')
                    ->relationship(name: 'barang', titleAttribute: 'nama_barang')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('channel')
                    ->label('Kanal pemasaran')
                    ->options(Campaign::CHANNELS)
                    ->required(),
                Textarea::make('objective')
                    ->label('Tujuan kampanye')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('target_audience')
                    ->label('Target audiens')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('budget')
                    ->label('Anggaran (Rp)')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->default(0),
                DatePicker::make('start_date')
                    ->label('Tanggal mulai')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal selesai')
                    ->required()
                    ->afterOrEqual('start_date'),
                Select::make('status')
                    ->label('Status')
                    ->options(Campaign::STATUSES)
                    ->default('draft')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama kampanye')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('barang.nama_barang')
                    ->label('Barang'),
                TextColumn::make('channel')
                    ->label('Kanal')
                    ->formatStateUsing(fn (?string $state): string => Campaign::CHANNELS[$state] ?? '-')
                    ->sortable(),
                TextColumn::make('budget')
                    ->label('Anggaran')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Campaign::STATUSES[$state] ?? '-')
                    ->color(fn (?string $state): string => Campaign::STATUS_COLORS[$state] ?? 'gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Campaign::STATUSES),
                SelectFilter::make('channel')
                    ->label('Kanal')
                    ->options(Campaign::CHANNELS),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCampaigns::route('/'),
        ];
    }
}
