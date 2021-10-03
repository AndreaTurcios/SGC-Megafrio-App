<?php
require('../../app/helpers/report.php');
require('../../app/models/pais.php');
// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReports('Reporte de datos de proveedores por país');
// Se instancia el módelo Pais para obtener los datos.
$pais = new Pais;
// Se verifica si existen registros (paises) para mostrar, de lo contrario se imprime un mensaje.
if ($dataPais = $pais->readAll()) {
    // Se recorren los registros ($dataPais) fila por fila ($rowProveedores).
    foreach ($dataPais as $rowProveedores) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(0, 188, 209);
        // Se imprime una celda con el nombre de los paises.
        $pdf->Cell(240, 10, utf8_decode('País: '.$rowProveedores['nombre_pais']), 1, 1, 'C', 1);
        // Se establece el pais para obtener sus proveedores, de lo contrario se imprime un mensaje de error.
        if ($pais->setId($rowProveedores['id_pais'])) {
            // Se verifica si existen registros (paises) para mostrar, de lo contrario se imprime un mensaje.
            if ($dataPais = $pais->readPais()) {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetFillColor(174, 232, 251);
                // Se imprimen las celdas con los encabezados.
                $pdf->Cell(50, 10, utf8_decode('Compañía'), 1, 0, 'C', 1);
                // Se establece la fuente para los datos de los proveedores.
                $pdf->Cell(40, 10, utf8_decode('Teléfono'), 1, 0, 'C', 1);
                $pdf->Cell(60, 10, utf8_decode('Dirección'), 1, 0, 'C', 1);
                $pdf->Cell(90, 10, utf8_decode('Info. Tributaria'), 1, 0, 'C', 1);
                $pdf->SetFont('Arial', '', 11);
                $pdf->Ln();
                // Se recorren los registros
                foreach ($dataPais as $rowProveedores) {
                    $pdf->Cell(50, 10, utf8_decode($rowProveedores['nombre_compania']), 1, 0);
                    $pdf->Cell(40, 10, utf8_decode($rowProveedores['telefono_pro']), 1, 0);
                    $pdf->Cell(60, 10, utf8_decode($rowProveedores['direccion_pro']), 1, 0);
                    $pdf->Cell(90, 10, utf8_decode($rowProveedores['info_tributaria']), 1, 0);
                    $pdf->Ln();      
                }   
            } else {
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(240, 20, utf8_decode('                                    '.'                                     '.' No hay proveedores registrados para este país'), 1, 1);
            }
            }
}
} else {
$pdf->Cell(0, 10, utf8_decode('No hay proveedores para mostrar'), 1, 1);
}

// Se envía el documento al navegador y se llama al método Footer()
$pdf->Output();
?>