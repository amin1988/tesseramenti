<?php

if (!defined('_BASE_DIR_'))
        exit();
include_controller('FileIscrizioneAcsi');
include_class('Sesso');
include_model('Provincia');

class FileIscrizioneAcsiXls_2 {

        const HEADER = 'acsi/header15.csv';
        const FOOTER = 'acsi/footer.csv';

        private $ctrl;

        /**
         * @param bool $nuovi true per includere i non tesserati
         * @param bool $attesa true per includere gli invii non confermati
         */
        public function __construct($rinnovo, $nuovi, $attesa) {
                $this->ctrl = new FileIscrizioneAcsiCtrl($rinnovo, $nuovi, $attesa);
        }

        public function stampa() {
                $files = $this->generaContenuto();
                if (count($files) == 0)
	     exit(); //nessun file

                $filename = $this->creaZip($files);

                $filesize = filesize($filename);
                if ($filesize == 0) {
	     Log::error('File zip acsi vuoto', $filename);
	     echo "Nessun file generato";
	     exit();
                }
                //TODO salvare il file da qualche parte?

                $filename_out = 'acsi_' . date('Y-m-d_H-i-s');
                header("Content-Disposition: attachment; filename=$filename_out.zip");
                header("Content-Type: application/zip");
// 		header("Content-length: $filesize\n\n");
                header("Content-Transfer-Encoding: binary");
                header('Expires: 0');
                header('Cache-Control: must-revalidate');

                readfile($filename);
        }

        /*
          private function getFileName($csvname, $num) {
          if ($num === NULL)
          return "$csvname.csv";
          else
          return "{$csvname}_$num.csv";
          }
         */

        private function generaContenuto() {
                $this->delete(_BASE_DIR_ . 'acsi/temp');
                mkdir(_BASE_DIR_ . 'acsi/temp');

                $files = array();

                require_once _BASE_DIR_ . 'phpexcel/Classes/PHPExcel/IOFactory.php';
                require_once _BASE_DIR_ . 'phpexcel/Classes/PHPExcel.php';

                //Contiene il numero minimo e massimo per ogni società, in modo da poterli assegnare più velocemente
                $num_tess = array();
                $nomi_brevi = array();

                foreach ($this->ctrl->getElencoSocieta() as $idsoc => $soc) {
	     $cod_acsi = $soc->getCodiceAcsi();
	     $nome_breve = $soc->getNomeBreve();
	     $nomi_brevi[$idsoc][0] = $nome_breve;
	     $nomi_brevi[$idsoc][1] = $cod_acsi;

	     if (count($this->ctrl->getTesserati($idsoc)) > 0) {
	             /*
	               $inputFileType = PHPExcel_IOFactory::identify(_BASE_DIR_.'acsi/ACSI.xls');
	               $excel = PHPExcel_IOFactory::load(_BASE_DIR_.'acsi/ACSI.xls');
	               $excel->setActiveSheetIndex(0);

	               $excel->getActiveSheet()->setCellValue('C8', $cod_acsi); //codice ACSI Sodalizio
	              */

	             $curr = 13;
// 				$tot = 0;
	             $num_file = 1;
	             $lista_societa_kudo = societa::listaSocKudo();

	             foreach ($this->ctrl->getTesserati($idsoc) as $idt => $tess) {
		  if ($curr == 13) {
		          $inputFileType = PHPExcel_IOFactory::identify(_BASE_DIR_ . 'acsi/ACSI.xls');
		          $excel = PHPExcel_IOFactory::load(_BASE_DIR_ . 'acsi/ACSI.xls');
		          $excel->setActiveSheetIndex(0);

		          $excel->getActiveSheet()->setCellValue('C8', $cod_acsi); //codice ACSI Sodalizio
		  }

		  $cod_fis = $tess->getCodiceFiscale();
		  if ($cod_fis == '' || $cod_fis === NULL)
		          continue; //codice fiscale OBBLIGATORIO

		  $tessera = $this->ctrl->getNumTessera($idsoc, $idt);
		  if ($tessera === NULL)
		          continue; //finite le tessere


		          
//numero minimo tessera
		  if (isset($num_tess[$idsoc][0])) {
		          if ($tessera < $num_tess[$idsoc][0])
		                  $num_tess[$idsoc][0] = $tessera;
		  } else
		          $num_tess[$idsoc][0] = $tessera;

		  //numero massimo tessera
		  if (isset($num_tess[$idsoc][1])) {
		          if ($tessera > $num_tess[$idsoc][1])
		                  $num_tess[$idsoc][1] = $tessera;
		  } else
		          $num_tess[$idsoc][1] = $tessera;

		  $cogn = $this->esc($tess->getCognome());
		  $nome = $this->esc($tess->getNome());
		  $qual = "Socio/Atleta – 2114";
		  $email = $this->esc($tess->getEmail());
		  
		  $assicurazione = "Base Sport - 102";
		  if(in_array($idsoc, $lista_societa_kudo)) // è una societa kudo
		  {
		    $assicurazione =   "Integrativa Sport – 103";  // quindi l'assicurazione è integrativa
		  }
		  $discConi = "Karate - BP001";
		  $discAcsi = "KARATE - 130";
		  $qualif=$tess->getQualifiche();
		  $qualifica_acsi = "";
		  $qualifica_acsi = $this->getGradoQualificaAcsi($tess->getId());
		  if (!empty($qualifica_acsi))
		  {
		           $qual = $qualifica_acsi;
		  }
		  
		  

		  $excel->getActiveSheet()->setCellValue("A$curr", $tessera)
		          ->setCellValue("B$curr", $cogn)
		          ->setCellValue("C$curr", $nome)
		          ->setCellValue("D$curr", $cod_fis)
		          ->setCellValue("E$curr", $qual)
		          ->setCellValue("F$curr", $email)
		          ->setCellValue("G$curr", $assicurazione)
		          ->setCellValue("H$curr", $discConi)
		          ->setCellValue("K$curr", $discAcsi);

		  $curr++;
// 					$tot++;

		  if ($curr == 112) {
		          $curr = 13;

		          $filename = $cod_acsi . '_' . preg_replace('/\\W/', '', $nome_breve) . $num_file . ".xls";
		          $files[$idsoc / $num_file] = $filename;

		          $num_file++;

		          $objWriter = PHPExcel_IOFactory::createWriter($excel, $inputFileType);
		          $objWriter->save(_BASE_DIR_ . "acsi/temp/$filename");
		  }
	             }

	             /*
	               header("Content-Type: application/vnd.ms-excel");
	               header("Content-Disposition: attachment; filename=\"new_file.xls \"");
	               header("Cache-Control: max-age=0");
	              */

	             if ($curr > 13) {
		  $filename = $cod_acsi . '_' . preg_replace('/\\W/', '', $nome_breve) . $num_file . ".xls";
		  $files[$idsoc / $num_file] = $filename;

		  $objWriter = PHPExcel_IOFactory::createWriter($excel, $inputFileType);
		  $f = _BASE_DIR_ . "acsi/temp/$filename";
		  $objWriter->save(_BASE_DIR_ . "acsi/temp/$filename");
	             }
	     }
                }

                $text_tess = fopen(_BASE_DIR_ . "acsi/temp/tessere.txt", "w+");
                foreach ($num_tess as $idsoc => $minmax) {
	     $str = $nomi_brevi[$idsoc][1] . " - " . $nomi_brevi[$idsoc][0] . ":" . "\r\n";
	     $str .= "MIN = " . $minmax[0] . " - MAX = " . $minmax[1] . "\r\n" . "\r\n";
	     fwrite($text_tess, $str);
                }

                fclose($text_tess);

                return $files;
        }

        private function getGradoQualificaAcsi($tess) {
                $db = Database::get();
                $sql = "SELECT g.acsi_qualifica FROM pagamenti_correnti as pc INNER JOIN  gradi as g ON pc.idgrado = g.idgrado "
	     . " WHERE pc.idtesserato  = " . $tess;
               
                 $rs = $db->query($sql);
                if ($rs) {
	     $row = $rs->fetch_assoc();
	     return $row['acsi_qualifica'];
                }
                return null;
        }

        private function delete($path) {
                if (is_dir($path) === true) {
	     $files = array_diff(scandir($path), array('.', '..'));

	     foreach ($files as $file) {
	             $this->delete(realpath($path) . '/' . $file);
	     }

	     return rmdir($path);
                } else if (is_file($path) === true) {
	     return unlink($path);
                }

                return false;
        }

        /**
         * Elimina tutti i ;
         * @param string $str
         * @return string
         */
        private function esc($str) {
                return str_replace(';', '', $str);
        }

        private function creaZip($files) {
                $zip = new ZipArchive();

                $filename = tempnam('/tmp', 'acsi');

                if ($zip->open($filename, ZIPARCHIVE::OVERWRITE) !== TRUE) {
	     Log::error("Impossibile aprire il file zip", $filename);
	     exit();
                }

                //carica i file acsi non sono più necessari
                /*
                  $header = file_get_contents(_BASE_DIR_.self::HEADER);
                  if ($header === false) {
                  Log::error('Errore durante la lettura del file',_BASE_DIR_.self::HEADER);
                  exit();
                  }
                  $footer = file_get_contents(_BASE_DIR_.self::FOOTER);
                  if ($footer === false) {
                  Log::error('Errore durante la lettura del file',_BASE_DIR_.self::FOOTER);
                  exit();
                  }
                 */

                //riempie lo zip
                foreach ($files as $idsoc => $nome) {
	     $zip->addFile(_BASE_DIR_ . "acsi/temp/$nome", $nome);
                }

                if (file_exists(_BASE_DIR_ . "acsi/temp/tessere.txt"))
	     $zip->addFile(_BASE_DIR_ . "acsi/temp/tessere.txt", "tessere_assegnate.txt");

                Log::info("Status ZIP $filename", $zip->getStatusString());

                $zip->close();

                return $filename;
        }

}