<?php
// libs/GeneradorInforme.php (Versión Corregida sin utf8_decode)

require_once('fpdf.php');

class GeneradorInforme extends FPDF
{
    private $tituloInforme = 'Informe General';

    // Función auxiliar para manejar la codificación de texto correctamente
    private function fix_text($text) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    }

    // Setter para el título del informe
    function setTitulo($titulo) {
        $this->tituloInforme = $titulo;
    }

    function Header()
    {
        $this->Image(dirname(__DIR__) . '/images/logo_segurigestion.png', 10, 8, 33);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, $this->fix_text($this->tituloInforme), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $this->fix_text('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function crearTabla($cabecera, $datos, $anchos)
    {
        $this->AliasNbPages();
        $this->AddPage('L', 'Letter'); // Página en horizontal para más espacio
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(230, 230, 230);

        // Cabecera de la tabla
        for ($i = 0; $i < count($cabecera); $i++) {
            $this->Cell($anchos[$i], 7, $this->fix_text($cabecera[$i]), 1, 0, 'C', true);
        }
        $this->Ln();

        // Datos de la tabla
        $this->SetFont('Arial', '', 9);
        $fill = false;
        foreach ($datos as $fila) {
            // Se asume que $fila es un array indexado numéricamente
            $keys = array_keys($fila);
            $this->Cell($anchos[0], 6, $this->fix_text($fila[$keys[0]]), 'LR', 0, 'L', $fill);
            $this->Cell($anchos[1], 6, $this->fix_text($fila[$keys[1]]), 'LR', 0, 'L', $fill);
            $this->Cell($anchos[2], 6, $this->fix_text($fila[$keys[2]]), 'LR', 0, 'L', $fill);
            $this->Cell($anchos[3], 6, $this->fix_text($fila[$keys[3]]), 'LR', 0, 'L', $fill);
            $this->Cell($anchos[4], 6, $this->fix_text($fila[$keys[4]]), 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Línea de cierre
        $this->Cell(array_sum($anchos), 0, '', 'T');
    }
}
?>