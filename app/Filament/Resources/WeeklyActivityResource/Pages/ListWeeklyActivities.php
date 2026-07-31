<?php
namespace App\Filament\Resources\WeeklyActivityResource\Pages;
use App\Filament\Resources\WeeklyActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListWeeklyActivities extends ListRecords { protected static string $resource = WeeklyActivityResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
