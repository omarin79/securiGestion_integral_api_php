<?php
// libs/GeneradorCertificado.php (Versión Restaurada y Mejorada)

require_once('fpdf.php');

class GeneradorCertificado extends FPDF
{
    function Header()
    {
        // RUTA DEL LOGO CORREGIDA para que siempre funcione
        $this->Image(dirname(__DIR__) . '/images/logo_segurigestion.png', 10, 8, 33);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, 'CERTIFICADO DE INGRESOS Y RETENCIONES', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 7, 'ANO GRAVABLE ' . ($_POST['anio'] ?? date('Y')-1), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Documento generado por SecuriGestion Integral', 0, 0, 'C');
    }

    function TituloSeccion($label)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(0, 7, $label, 1, 1, 'L', true);
        $this->SetFont('Arial', '');
    }

    function FilaDato($label, $valor)
    {
        $this->Cell(95, 7, $label, 1);
        $this->Cell(95, 7, $valor, 1, 1);
    }

    // Tu método original para construir el certificado
    function crearCertificado($datosRetenedor, $datosEmpleado, $datosPagos)
    {
        $this->AddPage();
        $this->SetFont('Arial', '', 10);

        $this->TituloSeccion('1. Datos del Agente Retenedor');
        $this->FilaDato('NIT:', $datosRetenedor['nit']);
        $this->FilaDato('Razon Social:', $datosRetenedor['razon_social']);
        $this->Ln(7);

        $this->TituloSeccion('2. Datos del Empleado Asalariado');
        $this->FilaDato('Tipo y No. Documento:', 'C.C. ' . number_format($datosEmpleado['documento'], 0, ',', '.'));
        $this->FilaDato('Apellidos y Nombres:', $datosEmpleado['nombre_completo']);
        $this->Ln(7);

        $this->TituloSeccion('3. Concepto de los Ingresos');
        $this->FilaDato('Pagos por salarios, emolumentos eclesiasticos:', '$ ' . number_format($datosPagos['salarios'], 2, ',', '.'));
        $this->FilaDato('Pagos por honorarios:', '$ ' . number_format($datosPagos['honorarios'], 2, ',', '.'));
        $this->FilaDato('Pagos por servicios:', '$ ' . number_format($datosPagos['servicios'], 2, ',', '.'));
        $this->FilaDato('Pagos por comisiones:', '$ ' . number_format($datosPagos['comisiones'], 2, ',', '.'));
        $this->FilaDato('Pagos por prestaciones sociales:', '$ ' . number_format($datosPagos['prestaciones'], 2, ',', '.'));
        $this->FilaDato('Cesantias e intereses de cesantias:', '$ ' . number_format($datosPagos['cesantias'], 2, ',', '.'));
        $this->Ln(7);

        $this->TituloSeccion('4. Concepto de los Aportes');
        $this->FilaDato('Aportes obligatorios por salud:', '$ ' . number_format($datosPagos['aportes_salud'], 2, ',', '.'));
        $this->FilaDato('Aportes obligatorios a fondos de pensiones:', '$ ' . number_format($datosPagos['aportes_pension'], 2, ',', '.'));
        $this->Ln(7);

        $this->TituloSeccion('5. Retencion en la Fuente');
        $this->FilaDato('Valor total de la retencion en la fuente:', '$ ' . number_format($datosPagos['retencion'], 2, ',', '.'));
        $this->Ln(15);

        $this->Cell(0, 10, 'Atentamente,', 0, 1);
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 7, '_________________________', 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 7, 'Agente Retenedor', 0, 1);
        $this->Cell(0, 7, $datosRetenedor['razon_social'], 0, 1);
    }
}
?>