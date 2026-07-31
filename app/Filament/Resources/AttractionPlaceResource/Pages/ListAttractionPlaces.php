<?php
namespace App\Filament\Resources\AttractionPlaceResource\Pages; use App\Filament\Resources\AttractionPlaceResource; use Filament\Actions\CreateAction; use Filament\Resources\Pages\ListRecords; class ListAttractionPlaces extends ListRecords { protected static string $resource=AttractionPlaceResource::class; protected function getHeaderActions(): array{return [CreateAction::make()];} }
