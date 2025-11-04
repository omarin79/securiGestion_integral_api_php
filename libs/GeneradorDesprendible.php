<?php
// libs/GeneradorDesprendible.php (Versión con Acentos Corregidos)

require_once __DIR__ . '/fpdf.php';

// Función auxiliar para convertir texto a la codificación correcta para el PDF
function fix_text($text) {
    return iconv('UTF-8', 'windows-1252', $text);
}

class GeneradorDesprendible extends FPDF {
    
    // Cabecera de página
    function Header() {
        $this->Image(dirname(__DIR__) . '/images/logo_segurigestion.png', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, fix_text('Desprendible de Pago'), 0, 0, 'C');
        $this->Ln(20);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, fix_text('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Función principal para crear el desprendible
    function crearDesprendible($datosEmpleado, $datosPago) {
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetFont('Arial', '', 10);
        
        // Cabecera con datos del empleado
        $this->SetFillColor(230, 230, 230);
        $this->Cell(40, 6, fix_text('Nombre:'), 1, 0, 'L', true);
        $this->Cell(0, 6, fix_text($datosEmpleado['nombre']), 1, 1, 'L');
        
        $this->Cell(40, 6, fix_text('Cédula:'), 1, 0, 'L', true);
        $this->Cell(0, 6, fix_text($datosEmpleado['cedula']), 1, 1, 'L');

        $this->Cell(40, 6, fix_text('Cargo:'), 1, 0, 'L', true);
        $this->Cell(0, 6, fix_text($datosEmpleado['cargo']), 1, 1, 'L');

        $this->Cell(40, 6, fix_text('Periodo de Pago:'), 1, 0, 'L', true);
        $this->Cell(0, 6, fix_text($datosEmpleado['periodo_pago']), 1, 1, 'L');
        
        $this->Ln(8);
        
        // Cuerpo del desprendible (Devengados y Deducidos)
        $this->SetFont('Arial', 'B', 11);
        
        $this->Cell(95, 7, fix_text('DEVENGADOS'), 1, 0, 'C', true);
        $this->Cell(95, 7, fix_text('DEDUCIDOS'), 1, 1, 'C', true);
        
        $this->Cell(65, 6, fix_text('Concepto'), 1, 0, 'C');
        $this->Cell(30, 6, fix_text('Valor'), 1, 0, 'C');
        $this->Cell(65, 6, fix_text('Concepto'), 1, 0, 'C');
        $this->Cell(30, 6, fix_text('Valor'), 1, 1, 'C');
        
        $this->SetFont('Arial', '', 10);
        
        $max_filas = max(count($datosPago['devengados']), count($datosPago['deducidos']));
        for ($i = 0; $i < $max_filas; $i++) {
            $concepto_dev = isset($datosPago['devengados'][$i]) ? $datosPago['devengados'][$i]['concepto'] : '';
            $valor_dev = isset($datosPago['devengados'][$i]) ? number_format($datosPago['devengados'][$i]['valor'], 0, ',', '.') : '';
            
            $concepto_ded = isset($datosPago['deducidos'][$i]) ? $datosPago['deducidos'][$i]['concepto'] : '';
            $valor_ded = isset($datosPago['deducidos'][$i]) ? number_format($datosPago['deducidos'][$i]['valor'], 0, ',', '.') : '';

            $this->Cell(65, 6, fix_text($concepto_dev), 'LR');
            $this->Cell(30, 6, $valor_dev, 'R', 0, 'R');
            $this->Cell(65, 6, fix_text($concepto_ded), 'R');
            $this->Cell(30, 6, $valor_ded, 'R', 1, 'R');
        }

        // Totales
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(65, 7, fix_text('TOTAL DEVENGADO'), 1, 0, 'R', true);
        $this->Cell(30, 7, number_format($datosPago['total_devengado'], 0, ',', '.'), 1, 0, 'R', true);
        $this->Cell(65, 7, fix_text('TOTAL DEDUCIDO'), 1, 0, 'R', true);
        $this->Cell(30, 7, number_format($datosPago['total_deducido'], 0, ',', '.'), 1, 1, 'R', true);
        
        $this->Ln(5);
        
        // Neto a Pagar
        $this->Cell(130);
        $this->Cell(30, 8, fix_text('NETO A PAGAR'), 1, 0, 'C');
        $this->Cell(30, 8, number_format($datosPago['neto_a_pagar'], 0, ',', '.'), 1, 1, 'R');
    }
}
?>