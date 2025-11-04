<?php
// C:\xampp\htdocs\securigestion\libs\GeneradorCarta.php

require_once('fpdf.php');

class GeneradorCarta extends FPDF
{
    private $marcaDeAguaMostrada = false;

    // --- ENCABEZADO PERSONALIZADO ---
    function Header()
    {
        // Logo de la empresa
        $this->Image('../images/logo_segurigestion.png', 10, 8, 33);

        // Título del documento
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 51, 102); // Azul oscuro corporativo
        $this->Cell(0, 10, utf8_decode('CERTIFICACIÓN LABORAL'), 0, 1, 'C');

        // Línea divisoria
        $this->Line(10, 30, 200, 30);

        $this->Ln(15);
    }

    // --- PIE DE PÁGINA PERSONALIZADO ---
    function Footer()
    {
        $this->SetY(-15);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(128);
        $textoFooter = utf8_decode("SecuriGestión Integral S.A.S. | NIT: 900.XXX.XXX-X\n");
        $textoFooter .= utf8_decode("Dirección: Calle Falsa 123, Bogotá D.C. | Teléfono: (601) 555-1234\n");
        $textoFooter .= utf8_decode("www.securigestion.com");
        $this->MultiCell(0, 4, $textoFooter, 0, 'C');
    }

    // --- MÉTODO PRINCIPAL PARA CREAR LA CARTA ---
    function crearCarta($nombreEmpleado, $documento, $cargo, $fechaContratacion, $salario, $tipoCarta)
    {
        $this->AliasNbPages();
        $this->AddPage();

        // Restauramos la fuente y el color para el contenido
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0, 0, 0);

        // Fecha de expedición
        $this->Cell(0, 10, utf8_decode('Bogotá D.C., ' . date('d \d\e F \d\e Y')), 0, 1, 'R');
        $this->Ln(15);

        // Cuerpo de la carta
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, utf8_decode("A QUIEN PUEDA INTERESAR:"), 0, 1);
        $this->Ln(10);

        $this->SetFont('Arial', '', 12);
        $cuerpo = "Por medio de la presente, la empresa SECURIGESTIÓN INTEGRAL S.A.S. identificada con NIT 900.XXX.XXX-X, certifica que el/la señor(a) " . strtoupper($nombreEmpleado) . ", identificado(a) con Cédula de Ciudadanía No. " . number_format($documento, 0, ',', '.') . ", labora en nuestra compañía desde el " . date("d de F de Y", strtotime($fechaContratacion)) . ", desempeñando el cargo de " . strtoupper($cargo) . " con un contrato a término indefinido.";

        if ($tipoCarta === 'con_salario' && $salario > 0) {
            $cuerpo .= " Actualmente, su salario básico mensual asignado corresponde a $" . number_format($salario, 0, ',', '.') . " COP.";
        }

        $this->MultiCell(0, 8, utf8_decode($cuerpo));
        $this->Ln(15);

        $this->MultiCell(0, 8, utf8_decode("Esta constancia se expide a solicitud del interesado para los fines que estime pertinentes."));
        $this->Ln(25);

        $this->Cell(0, 10, 'Atentamente,', 0, 1);
        $this->Ln(20);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, '_________________________', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 7, utf8_decode('Departamento de Gestión Humana'), 0, 1);
        $this->Cell(0, 7, utf8_decode('SecuriGestión Integral S.A.S.'), 0, 1);
    }
}
?>