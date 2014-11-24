<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjTicketPdf
{
	private $hash = '';
	
	private $barcode_value = '';
	
	public function __construct()
	{
		mt_srand();
		$this->hash = mt_rand(1000, 9999);
	}
	
	function generateBarcode()
	{
		require_once(PJ_LIBS_PATH . 'barcode/class/BCGFont.php');
		require_once(PJ_LIBS_PATH . 'barcode/class/BCGColor.php');
		require_once(PJ_LIBS_PATH . 'barcode/class/BCGDrawing.php');
		
		require_once(PJ_LIBS_PATH . 'barcode/class/BCGcode39.barcode.php');
		
		$font = new BCGFont(PJ_LIBS_PATH . 'barcode/class/font/Arial.ttf', 12);

		// The arguments are R, G, and B for color.
		$color_black = new BCGColor(0, 0, 0);
		$color_white = new BCGColor(255, 255, 255);

		# $code = new BCGcode11();
		$code = new BCGcode39();
		$code->setScale(1); // Resolution
		$code->setThickness(18); // Thickness
		$code->setForegroundColor($color_black); // Color of bars
		$code->setBackgroundColor($color_white); // Color of spaces
		$code->setFont($font); // Font (or 0)
		
		$code->parse($this->barcode_value); // Text
		$code->calculateChecksum();
		$code->setLabel($this->barcode_value);
		
		$filename = PJ_UPLOAD_PATH.'tickets/barcodes/b_'. $this->barcode_value .'.png';
		$drawing = new BCGDrawing($filename, $color_white);
		$drawing->setBarcode($code);
		$drawing->draw();
		$drawing->finish($drawing->IMG_FORMAT_PNG);
		return $filename;
	}
	
	function generateTicket($ticket_img, $ticket_id)
	{
		$ticket = $ticket_img;
				
		if (is_file($ticket))
		{
			$ticketSize = getimagesize($ticket);
			switch ($ticketSize[2])
			{
				case IMAGETYPE_GIF:
					$dest = imagecreatefromgif($ticket);
					break;
				case IMAGETYPE_PNG:
					$dest = imagecreatefrompng($ticket);
					break;
				case IMAGETYPE_JPEG:
					$dest = imagecreatefromjpeg($ticket);
					break;
			}
		} else {
			$dest = imagecreate(510, 280);
			$background = imagecolorallocate($dest, 255, 255, 255);
		}
		
		$this->barcode_value = $ticket_id;
		
		$barcode = $this->generateBarcode();
		$barcodeSize = getimagesize($barcode);
		switch ($barcodeSize[2])
		{
			case IMAGETYPE_GIF:
				$src = imagecreatefromgif($barcode);
				break;
			case IMAGETYPE_PNG:
				$src = imagecreatefrompng($barcode);
				break;
			case IMAGETYPE_JPEG:
				$src = imagecreatefromjpeg($barcode);
				break;
		}
		$filename = PJ_UPLOAD_PATH . 'tickets/t_' . $this->barcode_value . '.png';
		imagecopymerge($dest, $src, 284, 219, 0, 0, $barcodeSize[0], $barcodeSize[1], 100);
		imagepng($dest, $filename, 9);
		imagedestroy($src);
		imagedestroy($dest);
		return $filename;
	}
	
	function generatePdf($params)
	{
		require_once(PJ_LIBS_PATH . 'tcpdf/config/lang/eng.php');
		require_once(PJ_LIBS_PATH . 'tcpdf/tcpdf.php');
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(10, 10, 10);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		$pdf->SetFont('dejavusans', '', 8);
		
		$unique_id = '';
		
		foreach($params as $v)
		{
			$ticket = $this->generateTicket($v['ticket_img'], $v['ticket_id']);
			
			$pdf->AddPage();
			$pdf->Image($ticket, 10, 10, '', '', 'PNG', '', 'T', false, 300, '', false, false, 0, true, false, true);
			$pdf->Ln(100);
			
			$html = '<p style="color: #000; border:none;">' . preg_replace('/\r\n|\n/', '<br />', $v['ticket_info']) . '</p>';
			$pdf->writeHTMLCell(87, 19, 13, 68, $html, 0);
			
			$unique_id = $v['unique_id'];
		}
		
		$pdf->Output(PJ_UPLOAD_PATH . 'tickets/pdfs/p_'. $unique_id .'.pdf', 'F');
		$filename = PJ_UPLOAD_PATH . 'tickets/pdfs/p_'. $unique_id . '.pdf';
		return $filename;
	}
}

?>