<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;

class GerenciaDashboard extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard de Gerencia';
    protected static string $view = 'filament.pages.gerencia-dashboard';

    public $periodo = 2025;
    public $empresa = 'CLINICA LA LUZ SAC';
    public $sucursal = 'Av. Gran Chimú 085, Zárate';
    public $subgrupo = 'CONSULTAS';

    public function mount(): void
    {
        $this->form->fill([
            'periodo' => $this->periodo,
            'empresa' => $this->empresa,
            'sucursal' => $this->sucursal,
            'subgrupo' => $this->subgrupo,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Filtros')
                ->columns(4)
                ->schema([
                    Select::make('periodo')
                        ->label('Periodo')
                        ->options([
                            '2023' => '2023',
                            '2024' => '2024',
                            '2025' => '2025',
                        ])
                        ->required()
                        ->live(),

                    Select::make('empresa')
                        ->label('Empresa')
                        ->options([
                            'CLINICA LA LUZ SAC' => 'CLINICA LA LUZ SAC',
                          
                        ])
                        ->required()
                        ->live(),

                    Select::make('sucursal')
                        ->label('Sucursal')
                        ->options([
                            'Av. Arequipa 1148, Lima' => 'Av. Arequipa 1148, Lima',
                            'Av. Perú 3811, San Martín de Porres' => 'Av. Perú 3811, San Martín de Porres',
                            'Av. Tupac Amaru 809, Comas' => 'Av. Tupac Amaru 809, Comas',
                            'Av. Gran Chimú 085, Zárate' => 'Av. Gran Chimú 085, Zárate',
                        ])
                        ->required()
                        ->live(),

                    Select::make('subgrupo')
                        ->label('Subgrupo')
                        ->options([
                            'CONSULTAS' => 'CONSULTAS',
                            'LABORATORIO' => 'LABORATORIO',
                            'FARMACIA' => 'FARMACIA',
                            'PROCEDIMIENTO' => 'PROCEDIMIENTO',
                        ])
                        ->required()
                        ->live(),

                    // TextInput::make('busqueda')
                    //     ->label('Buscar producto o cliente')
                    //     ->placeholder('Ej: paracetamol, juan perez')
                    //     ->live(debounce: 500),
                ]),
        ];
    }
    public function updated($property): void
    {
        $data = $this->form->getState();

        $this->periodo = $data['periodo'];
        $this->empresa = $data['empresa'];
        $this->sucursal = $data['sucursal'];
        $this->subgrupo = $data['subgrupo'];
    }
}
