<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Report</title> 
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }
        .container-pdf {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        h1, h2, h3 {
            color: #0056b3;
        }
        .box-information-pdf, .green-kpis, .generic-kpis, .bill-summary, .installation-details, .contracted-powers, .periods, .averages {
            margin-bottom: 20px;
        }
        .box-information-pdf p, .green-kpis p, .generic-kpis p, .bill-summary p, .installation-details p, .contracted-powers p, .periods p, .averages p {
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }
        .green-kpis, .generic-kpis, .bill-summary, .installation-details, .contracted-powers, .periods, .averages {
            background-color: #e6f7ff;
            padding: 10px;
            border-radius: 5px;
        }
        .green-kpis h3, .periods h3, .averages h3 {
            margin-top: 10px;
        }
        img {
            display: block;
            margin: 20px auto;
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="container-pdf">
        <h1>Reporte de Consumo de Energía</h1>
        <div class="box-information-pdf">
            <p><strong>Versión:</strong> {{ $version }}</p>
            <p><strong>Energía Instalada:</strong> {{ $installed_power }}</p>
            <p><strong>Inversores de Potencia Total:</strong> {{ $inverters_total_power }}</p>
            <p><strong>Tarifa:</strong> {{ $tariff }}</p>
        </div>

        <h2>KPI Verdes</h2>
        <div class="green-kpis">
            <h3>CO2</h3>
            <p><strong>Ratio:</strong> {{ number_format($green_kpis['co2_kpi']['ratio'], 0) }}</p>
            <p><strong>Reducción Anual:</strong> {{ number_format($green_kpis['co2_kpi']['annual_reduction'], 0) }}</p>
            <p><strong>Reducción Total:</strong> {{ number_format($green_kpis['co2_kpi']['total_reduction'], 0) }}</p>

            <h3>SOx</h3>
            <p><strong>Ratio:</strong> {{ number_format($green_kpis['sox_kpi']['ratio'], 0) }}</p>
            <p><strong>Reducción Anual:</strong> {{ number_format($green_kpis['sox_kpi']['annual_reduction'], 0) }}</p>
            <p><strong>Reducción Total:</strong> {{ number_format($green_kpis['sox_kpi']['total_reduction'], 0) }}</p>

            <h3>NOx</h3>
            <p><strong>Ratio:</strong> {{ number_format($green_kpis['nox_kpi']['ratio'], 0) }}</p>
            <p><strong>Reducción Anual:</strong> {{ number_format($green_kpis['nox_kpi']['annual_reduction'], 0) }}</p>
            <p><strong>Reducción Total:</strong> {{ number_format($green_kpis['nox_kpi']['total_reduction'], 0) }}</p>

            <p><strong>Árboles:</strong> {{ $green_kpis['trees'] }}</p>
            <p><strong>Casas:</strong> {{ $green_kpis['houses'] }}</p>
            <p><strong>KM Anuales de Coche:</strong> {{ number_format($green_kpis['car_annual_km'], 0) }}</p>
        </div>

        <h2>KPI Genéricos</h2>
        <div class="generic-kpis">
            @foreach($generic_kpis as $key => $value)
                <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ number_format($value, 0) }}</p>
            @endforeach
        </div>

        <h2>Resumen de Factura</h2>
        <div class="bill-summary">
            @foreach($bill_summary as $key => $value)
                <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ number_format($value, 0) }}</p>
            @endforeach
        </div>

        <h2>Detalles de Instalación</h2>
        <div class="installation-details">
            @foreach($installation_details as $key => $value)
                <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ number_format($value, 0) }}</p>
            @endforeach
        </div>

        <h2>Potencias Contratadas</h2>
        <div class="contracted-powers">
            @foreach($contracted_powers as $key => $value)
                <p><strong>{{ ucfirst($key) }}:</strong> {{ number_format($value, 0) }}</p>
            @endforeach
        </div>

        <h2>Consumo Anual Total</h2>
        <p><strong>{{ number_format($total_annual_consumption, 0) }}</strong></p>

        <h2>Ahorros Anuales Totales</h2>
        <p><strong>{{ number_format($total_annual_savings, 0) }}</strong></p>

        <h2>Periodos</h2>
        <div class="periods">
            @foreach($periods as $period => $details)
                <h3>{{ $period }}</h3>
                <p><strong>Consumo:</strong> {{ number_format($details['cons'], 0) }}</p>
                <p><strong>Porcentaje:</strong> {{ number_format($details['percent'], 0) }}</p>
            @endforeach
        </div>

        <h2>Promedios</h2>
        <img src="{{ $graph_path }}" alt="Gráfico de Consumo">

        <h2>Financiación</h2>
        <img src="{{ $pie_graph_path }}" alt="Gráfico de Rosco">
    </div>
</body>
</html>
