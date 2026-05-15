<?php

namespace App\Classes;

use App\Models\TownHall;
use Illuminate\Support\Facades\Storage;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfFile {

    public $templates = [];
    public $documents = [];
    public $documents_data = [];
    public $data = [];
    public $file_name = 'document';
    public $multi_record = false;
    public $zip = false;
    public $header_footer = true;
    public $page_2x1 = false;
    public $content_type = 'application/pdf';

    public function generateFromTemplate($template, $sign=false)
	{
        $pdf_config = config('pdf');

        if ($this->zip) {
            $zip = new \ZipArchive();
            $zip_path = tempnam(sys_get_temp_dir(), 'ZIP') . '.zip';

            if ($zip->open($zip_path, \ZipArchive::CREATE) !== TRUE) {
                exit("cannot open <$zip_path>\n");
            }
            foreach ($this->documents as $record_key => $document) {
                foreach ($this->documents_data as $key => $value) {
                    if (isset($value[$record_key])) {
                       $this->data[$key] = $value[$record_key];
                    }
                }
                $pdf_html = view($template, [
                    'document' => $document,
                    'data' => $this->data,
                ])->render();
                $pdf = LaravelMpdf::loadHtml($pdf_html,[
                    'margin_left'=> ($sign) ? 15:$pdf_config['margin_left'],
                    'margin_top'=> ($this->header_footer) ? $pdf_config['margin_top']:5,
                    'margin_bottom'=> ($this->header_footer) ? $pdf_config['margin_bottom']:5,
                    'author'=> 'Eurofactu',
                ]);
                if ($sign) {
                    $tmp_path = tempnam(sys_get_temp_dir(), 'PDF');
                    $pdf->save($tmp_path);
                    $signed_pdf=$this->signPDF($tmp_path);
                    unlink($tmp_path);
                    $pdf_data = $signed_pdf;

                } else {
                    $pdf_data = $pdf->output($this->file_name.'.pdf');
                }
                $zip->addFromString($this->file_name.'-'.($record_key).'.pdf',$pdf_data);
            }

            $this->file_name = $this->file_name.'.zip';
            $this->content_type = 'application/zip';
            $zip->close();
            $zip_content = file_get_contents($zip_path);
            unlink($zip_path);
            //dd($this->file_name);
            return $zip_content;
        } else {
            $pdf_html = view($template, ['document' => $this->documents, 'data' => $this->data])->render();
            // dd($pdf_html);
            $pdf = LaravelMpdf::loadHtml($pdf_html,[
                'margin_left'=> ($sign) ? 15:$pdf_config['margin_left'],
                'margin_top'=> ($this->header_footer) ? $pdf_config['margin_top']:5,
                'margin_bottom'=> ($this->header_footer) ? $pdf_config['margin_bottom']:5,
                'author'=> 'Eurofactu',
            ]);
            $this->file_name = $this->file_name.'.pdf';
            if ($sign) {
                $tmp_path = tempnam(sys_get_temp_dir(), 'PDF');
                $pdf->save($tmp_path);
                $signed_pdf=$this->signPDF($tmp_path);
                unlink($tmp_path);
                return $signed_pdf;
            } else {
                return $pdf->output($this->file_name);
            }
        }
	}

    public function getFilename()
    {
        return $this->file_name;
    }

    private function signPDF ($vcRutaPDF)
    {
        if (Storage::exists('aytos/'.session('townhall')->id.'/cert/Sello.p12')) {
            $pdf = new FPDI('P', 'mm', 'A4');
            $pages = $pdf->setSourceFile($vcRutaPDF);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $townhall = TownHall::emtGet(session('townhall')->id);
            $certificate =  Storage::get('aytos/'.session('townhall')->id.'/cert/Sello.p12');
            // $certificatePassword = 'LAPUEBLADEMONTALBAN1';
            $certificatePassword = $townhall->cert_secret;
            $certificateInfo = array();
            $result = openssl_pkcs12_read($certificate, $certificateInfo, $certificatePassword);
            $cert=$certificateInfo['cert'];
            $key=$certificateInfo['pkey'];

            // set additional information
            $info = array(
                'Name' => session('townhall')->name,
                'Location' => 'Ayuntamiento',
                'Reason' => 'Sello de órgano',
                'ContactInfo' => session('townhall')->web,
            );

            $x = 3;
            $y = 265;
            for ($i = 1; $i <= $pages; $i++) {
                $pdf->AddPage();
                $page = $pdf->importPage($i);
                $pdf->useTemplate($page, 0, 0, null, null, false);
                if ($i==1) {
                    $pdf->StartTransform();
                    $pdf->Rotate(90, $x, $y);
                    $pdf->Rect($x,$y,50,12);
                    $pdf->image(Storage::path("public/aytos/".session('townhall')->id."/logo_impresos.png"),$x+1,$y+1,0,8);
                    $pdf->SetFontSize(7);
                    $pdf->Multicell(40,0,
                        session('townhall')->name.chr(10).'Sello de Órgano'.chr(10).'Firmado el: '.date("d/m/Y H:i"),
                        0,'L',0,1,$x+10,$y+1);
                    $pdf->StopTransform();
                }
            }
            // set document signature
            $pdf->setSignature($cert, $key, $certificatePassword, '', 2, $info);
            $pdf->setSignatureAppearance($x,$y-50,12,50,1);
            $vcPDF2=$pdf->Output("fichero.pdf","S");
        } else {
            $vcPDF2 = file_get_contents($vcRutaPDF);
        }
        return $vcPDF2;
    }

}
