<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\LinePlot;
use Amenadiel\JpGraph\Graph\PieGraph;
use Amenadiel\JpGraph\Plot\PiePlot;
use PDF;



class PdfController extends Controller
{
    public function generatePdf(Request $request)
    {
        $file = $request->file('json_data');

        if (!$file) {
            return redirect()->back()->withErrors(['json_data' => 'No se ha seleccionado ningún archivo JSON.']);
        }

        $jsonContent = file_get_contents($file->path());

        if ($jsonContent === false) {
            return redirect()->back()->withErrors(['json_data' => 'Error al leer el archivo JSON.']);
        }

        $jsonData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['json_data' => 'El archivo JSON no es válido.']);
        }

        $data = $this->extractData($jsonData);

        try {
            $data['graph_path'] = $this->generateGraphs($jsonData);
            $data['pie_graph_path'] = $this->generatePieGraph($jsonData);
            $pdf = PDF::loadView('pdf.template', $data);
            return $pdf->download('Pylon.pdf');
        } catch (\Exception $e) {
            Log::error('Error al generar el PDF: ' . $e->getMessage());
            return redirect()->back()->withErrors(['pdf' => 'Error al generar el PDF.']);
        }
    }


    private function extractData($jsonData)
    {
        return [
            'version' => $jsonData['version'] ?? 'N/A',
            'installed_power' => $jsonData['installed_power'] ?? 'N/A',
            'inverters_total_power' => $jsonData['inverters_total_power'] ?? 'N/A',
            'tariff' => $jsonData['tariff'] ?? 'N/A',
            'green_kpis' => [
                'co2_kpi' => [
                    'ratio' => $jsonData['green_kpis']['co2_kpi']['ratio'] ?? 'N/A',
                    'annual_reduction' => $jsonData['green_kpis']['co2_kpi']['annual_reduction'] ?? 'N/A',
                    'total_reduction' => $jsonData['green_kpis']['co2_kpi']['total_reduction'] ?? 'N/A',
                ],
                'sox_kpi' => [
                    'ratio' => $jsonData['green_kpis']['sox_kpi']['ratio'] ?? 'N/A',
                    'annual_reduction' => $jsonData['green_kpis']['sox_kpi']['annual_reduction'] ?? 'N/A',
                    'total_reduction' => $jsonData['green_kpis']['sox_kpi']['total_reduction'] ?? 'N/A',
                ],
                'nox_kpi' => [
                    'ratio' => $jsonData['green_kpis']['nox_kpi']['ratio'] ?? 'N/A',
                    'annual_reduction' => $jsonData['green_kpis']['nox_kpi']['annual_reduction'] ?? 'N/A',
                    'total_reduction' => $jsonData['green_kpis']['nox_kpi']['total_reduction'] ?? 'N/A',
                ],
                'trees' => $jsonData['green_kpis']['trees'] ?? 'N/A',
                'houses' => $jsonData['green_kpis']['houses'] ?? 'N/A',
                'car_annual_km' => $jsonData['green_kpis']['car_annual_km'] ?? 'N/A',
            ],
            'generic_kpis' => [
                'self_cons_savings' => $jsonData['generic_kpis']['self_cons_savings'] ?? 'N/A',
                'self_cons_savings_percent' => $jsonData['generic_kpis']['self_cons_savings_percent'] ?? 'N/A',
                'self_cons_savings_with_taxes' => $jsonData['generic_kpis']['self_cons_savings_with_taxes'] ?? 'N/A',
                'battery_savings' => $jsonData['generic_kpis']['battery_savings'] ?? 'N/A',
                'battery_savings_percent' => $jsonData['generic_kpis']['battery_savings_percent'] ?? 'N/A',
                'battery_savings_with_taxes' => $jsonData['generic_kpis']['battery_savings_with_taxes'] ?? 'N/A',
                'excess_savings' => $jsonData['generic_kpis']['excess_savings'] ?? 'N/A',
                'excess_savings_percent' => $jsonData['generic_kpis']['excess_savings_percent'] ?? 'N/A',
                'excess_savings_with_taxes' => $jsonData['generic_kpis']['excess_savings_with_taxes'] ?? 'N/A',
                'savings_without_excess' => $jsonData['generic_kpis']['savings_without_excess'] ?? 'N/A',
                'savings_without_excess_percent' => $jsonData['generic_kpis']['savings_without_excess_percent'] ?? 'N/A',
                'savings_without_excess_with_taxes' => $jsonData['generic_kpis']['savings_without_excess_with_taxes'] ?? 'N/A',
                'savings_with_excess' => $jsonData['generic_kpis']['savings_with_excess'] ?? 'N/A',
                'savings_with_excess_percent' => $jsonData['generic_kpis']['savings_with_excess_percent'] ?? 'N/A',
                'savings_with_excess_with_taxes' => $jsonData['generic_kpis']['savings_with_excess_with_taxes'] ?? 'N/A',
                'net_savings' => $jsonData['generic_kpis']['net_savings'] ?? 'N/A',
                'net_savings_with_taxes' => $jsonData['generic_kpis']['net_savings_with_taxes'] ?? 'N/A',
                'net_savings_with_battery' => $jsonData['generic_kpis']['net_savings_with_battery'] ?? 'N/A',
                'net_savings_with_battery_with_taxes' => $jsonData['generic_kpis']['net_savings_with_battery_with_taxes'] ?? 'N/A',
                'net_savings_with_excess' => $jsonData['generic_kpis']['net_savings_with_excess'] ?? 'N/A',
                'net_savings_with_excess_with_taxes' => $jsonData['generic_kpis']['net_savings_with_excess_with_taxes'] ?? 'N/A',
                'total_net_savings' => $jsonData['generic_kpis']['total_net_savings'] ?? 'N/A',
                'total_net_savings_with_taxes' => $jsonData['generic_kpis']['total_net_savings_with_taxes'] ?? 'N/A',
                'average_actual_cons_price' => $jsonData['generic_kpis']['average_actual_cons_price'] ?? 'N/A',
                'average_actual_cons_price_with_iee' => $jsonData['generic_kpis']['average_actual_cons_price_with_iee'] ?? 'N/A',
                'energy_saving_percent' => $jsonData['generic_kpis']['energy_saving_percent'] ?? 'N/A',
                'economic_saving_percent' => $jsonData['generic_kpis']['economic_saving_percent'] ?? 'N/A',
                'self_cons_savings_over_self_cons' => $jsonData['generic_kpis']['self_cons_savings_over_self_cons'] ?? 'N/A',
                'lcoe' => $jsonData['generic_kpis']['lcoe'] ?? 'N/A',
                'lcoe_over_self_cons_savings_over_self_cons_percent' => $jsonData['generic_kpis']['lcoe_over_self_cons_savings_over_self_cons_percent'] ?? 'N/A',
            ],
            'bill_summary' => [
                'battery_savings' => $jsonData['bill_summary']['battery_savings'] ?? 'N/A',
                'battery_savings_with_iva' => $jsonData['bill_summary']['battery_savings_with_iva'] ?? 'N/A',
                'contracted_power_cost' => $jsonData['bill_summary']['contracted_power_cost'] ?? 'N/A',
                'contracted_power_cost_with_iva' => $jsonData['bill_summary']['contracted_power_cost_with_iva'] ?? 'N/A',
                'contracted_power_savings' => $jsonData['bill_summary']['contracted_power_savings'] ?? 'N/A',
                'contracted_power_savings_with_iva' => $jsonData['bill_summary']['contracted_power_savings_with_iva'] ?? 'N/A',
                'energy_consumption_cost' => $jsonData['bill_summary']['energy_consumption_cost'] ?? 'N/A',
                'energy_consumption_cost_with_fv_needed_with_iva' => $jsonData['bill_summary']['energy_consumption_cost_with_fv_needed_with_iva'] ?? 'N/A',
                'energy_consumption_cost_with_iva' => $jsonData['bill_summary']['energy_consumption_cost_with_iva'] ?? 'N/A',
                'excess_power_cost' => $jsonData['bill_summary']['excess_power_cost'] ?? 'N/A',
                'excess_power_cost_with_iva' => $jsonData['bill_summary']['excess_power_cost_with_iva'] ?? 'N/A',
                'excess_savings' => $jsonData['bill_summary']['excess_savings'] ?? 'N/A',
                'excess_savings_with_iva' => $jsonData['bill_summary']['excess_savings_with_iva'] ?? 'N/A',
                'iee' => $jsonData['bill_summary']['iee'] ?? 'N/A',
                'iee_cost' => $jsonData['bill_summary']['iee_cost'] ?? 'N/A',
                'iva' => $jsonData['bill_summary']['iva'] ?? 'N/A',
                'iva_cost' => $jsonData['bill_summary']['iva_cost'] ?? 'N/A',
                'reactive_energy_cost' => $jsonData['bill_summary']['reactive_energy_cost'] ?? 'N/A',
                'self_consumption_savings' => $jsonData['bill_summary']['self_consumption_savings'] ?? 'N/A',
                'self_consumption_savings_with_iva' => $jsonData['bill_summary']['self_consumption_savings_with_iva'] ?? 'N/A',
                'total_annual_cost' => $jsonData['bill_summary']['total_annual_cost'] ?? 'N/A',
                'total_annual_cost_with_fv_with_iva' => $jsonData['bill_summary']['total_annual_cost_with_fv_with_iva'] ?? 'N/A',
                'total_annual_cost_with_iee' => $jsonData['bill_summary']['total_annual_cost_with_iee'] ?? 'N/A',
                'total_annual_cost_with_iva' => $jsonData['bill_summary']['total_annual_cost_with_iva'] ?? 'N/A',
                'total_annual_savings' => $jsonData['bill_summary']['total_annual_savings'] ?? 'N/A',
            ],
            'installation_details' => [
                'generated_energy' => $jsonData['installation_details']['generated_energy'] ?? 'N/A',
                'inclination' => $jsonData['installation_details']['inclination'] ?? 'N/A',
                'm2' => $jsonData['installation_details']['m2'] ?? 'N/A',
                'orientation' => $jsonData['installation_details']['orientation'] ?? 'N/A',
                'yield' => $jsonData['installation_details']['yield'] ?? 'N/A',
            ],
            'contracted_powers' => [
                'P1' => $jsonData['contracted_powers']['P1'] ?? 'N/A',
                'P2' => $jsonData['contracted_powers']['P2'] ?? 'N/A',
                'P3' => $jsonData['contracted_powers']['P3'] ?? 'N/A',
                'P4' => $jsonData['contracted_powers']['P4'] ?? 'N/A',
                'P5' => $jsonData['contracted_powers']['P5'] ?? 'N/A',
                'P6' => $jsonData['contracted_powers']['P6'] ?? 'N/A',
            ],
            'tariff' => $jsonData['tariff'] ?? 'N/A',
            'total_annual_consumption' => $jsonData['total_annual_consumption'] ?? 'N/A',
            'total_annual_savings' => $jsonData['total_annual_savings'] ?? 'N/A',
            'periods' => [
                'P1' => [
                    'cons' => $jsonData['periods']['P1']['cons'] ?? 'N/A',
                    'percent' => $jsonData['periods']['P1']['percent'] ?? 'N/A',
                ],
                'P2' => [
                    'cons' => $jsonData['periods']['P2']['cons'] ?? 'N/A',
                    'percent' => $jsonData['periods']['P2']['percent'] ?? 'N/A',
                ],
                'P3' => [
                    'cons' => $jsonData['periods']['P3']['cons'] ?? 'N/A',
                    'percent' => $jsonData['periods']['P3']['percent'] ?? 'N/A',
                ],
            ],
            'averages' => [
                'apr_may_oct' => [
                    'demand' => $jsonData['averages']['apr_may_oct']['demand'] ?? 'N/A',
                    'period' => $jsonData['averages']['apr_may_oct']['period'] ?? 'N/A',
                
                ],
                'avg_hourly_demand' => [
                    'demand' => $jsonData['averages']['avg_hourly_demand']['demand'] ?? 'N/A',
                    'period' => $jsonData['averages']['avg_hourly_demand']['period'] ?? 'N/A',
                ],

                'jan_feb_jul_dec' => [
                    'demand' => $jsonData['averages']['jan_feb_jul_dec']['demand'] ?? 'N/A',
                    'period' => $jsonData['averages']['jan_feb_jul_dec']['period'] ?? 'N/A',
                ],
                'jun_aug_sep' => [
                    'demand' => $jsonData['averages']['jun_aug_sep']['demand'] ?? 'N/A',
                    'period' => $jsonData['averages']['jun_aug_sep']['period'] ?? 'N/A',
                ],
                'mar_nov' => [
                    'demand' => $jsonData['averages']['mar_nov']['demand'] ?? 'N/A',
                    'period' => $jsonData['averages']['mar_nov']['period'] ?? 'N/A',
                ]
            ],
            'financing' => [
                'standard' => [
                    'projections' => [
                    'pv_savings' => $jsonData['financing']['standard']['projections']['pv_savings'] ?? 'N/A',
                    'excess_savings' => $jsonData['financing']['standard']['projections']['excess_savings'] ?? 'N/A',
                    'power_savings' => $jsonData['financing']['standard']['projections']['power_savings'] ?? 'N/A',
                    'battery_savings' => $jsonData['financing']['standard']['projections']['battery_savings'] ?? 'N/A',
                    'savings' => $jsonData['financing']['standard']['projections']['savings'] ?? 'N/A',
                    'differential' => $jsonData['financing']['standard']['projections']['differential'] ?? 'N/A',
                    'fiscal_deduction' => $jsonData['financing']['standard']['projections']['fiscal_deduction'] ?? 'N/A',
                    'om_cost' => $jsonData['financing']['standard']['projections']['om_cost'] ?? 'N/A',
                    'cost' => $jsonData['financing']['standard']['projections']['cost'] ?? 'N/A',
                    'cash_flow' => $jsonData['financing']['standard']['projections']['cash_flow'] ?? 'N/A',
                    'accumulated_cash_flow' => $jsonData['financing']['standard']['projections']['accumulated_cash_flow'] ?? 'N/A'                    
                    ]
                ],
               
            ],
        ];
    }

    private function generateGraphs($jsonData)
    {
        $datasets = [
            'apr_may_oct' => ['color' => 'blue'],
            'avg_hourly_demand' => ['color' => 'red'],
            'jan_feb_jul_dec' => ['color' => 'green'],
            'jun_aug_sep' => ['color' => 'orange'],
            'mar_nov' => ['color' => 'purple'],
        ];
    
        $graph = new Graph(800, 600);
        $graph->SetScale('textlin');
        $graph->title->Set('Gráfico de Consumo y Demanda');
        $graph->xaxis->title->Set('Período');
        $graph->yaxis->title->Set('Demanda');
    
        foreach ($datasets as $key => $value) {
            if (!isset($jsonData['averages'][$key])) {
                Log::warning("Datos para el gráfico ($key) no están disponibles.");
                continue;
            }
    
            $data = $jsonData['averages'][$key];
            
            if (isset($data['value'])) {
                $data = [$data['value']];
            } elseif (is_array($data)) {
                $data = array_map(function($entry) {
                    return $entry['demand'] ?? 0;
                }, $data);
            } else {
                Log::warning("Los datos para el gráfico ($key) no tienen un formato esperado.");
                continue;
            }
    
            if (empty($data)) {
                Log::warning("Los datos para el gráfico ($key) están vacíos o no son válidos.");
                continue;
            }
    
            $lineplot = new LinePlot($data);
            $lineplot->SetColor($value['color']);
            $lineplot->SetWeight(2);
    
            $graph->Add($lineplot);
        }
    
        $graphDir = storage_path('app/public');
        $graphPath = $graphDir . '/graphPath.png';
    
        if (!file_exists($graphDir)) {
            mkdir($graphDir, 0775, true);
        }
    
        if (file_put_contents($graphPath, 'Test write') === false) {
            Log::error("No se puede escribir en el directorio: $graphDir");
            return null;
        } else {
            unlink($graphPath);
        }
    
        $graph->Stroke($graphPath);
        if (!file_exists($graphPath) || filesize($graphPath) === 0) {
            Log::error("El archivo del gráfico no se creó correctamente o está vacío.");
            return null;
        }
        
        return $graphPath;
    }

    private function generatePieGraph($jsonData)
    {
        if (!isset($jsonData['financing']['standard'][0]['projections'])) {
            Log::warning("Datos para el gráfico de rosco no están disponibles.");
            return null;
        }
    
        $projections = $jsonData['financing']['standard'][0]['projections'] ?? [];
        
        if (empty($projections)) {
            Log::warning("Lista de proyecciones está vacía.");
            return null;
        }
    
        $data = array_filter($projections, function($item) {
            return array_sum($item) > 0;
        });
    
        if (empty($data)) {
            Log::error("Error al generar el gráfico de rosco: Todos los datos son cero.");
            return "No hay datos suficientes para generar el gráfico de rosco.";
        }
    
        $data = reset($data);
    
        $labels = [
            'Ahorros PV',
            'Ahorros Excedentes',
            'Ahorros de Batería',
            'Ahorros Totales',
            'Diferencial',
            'Cuota',
            'Costos O&M',
            'Costo',
            'Flujo de Caja'
        ];
    
        $values = [
            $data['pv_savings'] ?? 0,
            $data['excess_savings'] ?? 0,
            $data['battery_savings'] ?? 0,
            $data['savings'] ?? 0,
            $data['differential'] ?? 0,
            $data['quota'] ?? 0,
            $data['om_cost'] ?? 0,
            $data['cost'] ?? 0,
            $data['cash_flow'] ?? 0
        ];
    
        if (array_sum($values) <= 0) {
            Log::error("Error al generar el gráfico de rosco: La suma de todos los datos es cero o negativa.");
            return "No hay datos suficientes para generar el gráfico de rosco.";
        }
    
        $graph = new PieGraph(800, 600);
        $graph->SetShadow();
        $graph->title->Set("Distribución del Financiamiento");
    
        $piePlot = new PiePlot($values);
        $piePlot->SetLegends($labels);
        $piePlot->SetLabelType(PIE_VALUE_ABS);
        $piePlot->SetSliceColors(array('#FF9999', '#66B2FF', '#99FF99', '#FFCC99', '#FFCCFF', '#FF6666', '#66FF66', '#99CCFF', '#FF9966'));
        $piePlot->SetSize(0.3);
    
        $graph->Add($piePlot);
    
        $graphDir = storage_path('app/public');
        $graphPath = $graphDir . '/pie_graph.png';
    
        if (!file_exists($graphDir)) {
            if (mkdir($graphDir, 0775, true)) {
                Log::info("Directorio creado exitosamente: " . $graphDir);
            } else {
                Log::error("No se pudo crear el directorio: " . $graphDir);
                return "Error al crear el directorio para el gráfico.";
            }
        }
    
        if (file_exists($graphPath)) {
            if (!unlink($graphPath)) {
                Log::error("No se pudo eliminar el archivo existente: " . $graphPath);
            }
        }
    
        try {
            $graph->Stroke($graphPath);
            if (!file_exists($graphPath) || filesize($graphPath) === 0) {
                Log::error("El archivo del gráfico de rosco no se creó correctamente o está vacío.");
                return "El archivo del gráfico no se creó correctamente o está vacío.";
            }
        } catch (\Exception $e) {
            Log::error("Error al generar el gráfico de rosco: " . $e->getMessage());
            return "Error al generar el gráfico de rosco: " . $e->getMessage();
        }
    
        return $graphPath;
    }
    
    
  
}