<?php
$pdf = new \FPDF\SyPdf();
$pdf->AddGBFont('simhei', '微软雅黑');
$pdf->AddPage();
$pdf->SetFont('simhei', '', 20);
$pdf->Write(5, iconv("UTF-8", "gbk", 'zxc123你好'));
$pdf->Output();