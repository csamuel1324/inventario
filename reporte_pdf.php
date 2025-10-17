<?php
require_once('includes/load.php');
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

if (isset($_POST['start-date']) && isset($_POST['end-date'])) {
    $start_date = remove_junk($db->escape($_POST['start-date']));
    $end_date = remove_junk($db->escape($_POST['end-date']));
    $results = find_sales_by_dates($start_date, $end_date);

    $html = '<h2 style="text-align: center;">Reporte de Despachos</h2>';
    $html .= '<p><strong>Desde:</strong> ' . $start_date . ' <strong>Hasta:</strong> ' . $end_date . '</p>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
    $html .= '<thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Cantidad</th>
                </tr>
              </thead>';
    $html .= '<tbody>';

    foreach ($results as $row) {
        $html .= '<tr>';
        $html .= '<td>' . remove_junk($row['date']) . '</td>';
        $html .= '<td>' . remove_junk($row['name']) . '</td>';
        $html .= '<td>' . remove_junk($row['origin']) . '</td>';
        $html .= '<td>' . remove_junk($row['destination']) . '</td>';
        $html .= '<td>' . remove_junk($row['qty']) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    // Crear instancia y cargar HTML
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Descargar PDF
    $dompdf->stream("reporte_despachos_" . $start_date . "_a_" . $end_date . ".pdf", array("Attachment" => true));
} else {
    $session->msg("d", "No se recibieron fechas válidas.");
    redirect('sales_report.php', false);
}
?>
