<?php
if (!defined('_BASE_DIR_')) exit();
include_controller('FileIscrizioneAcsi');
include_class('Sesso');
include_model('Provincia');

class FileIscrizioneAcsi {
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
		$files = $this->rigeneraContenuto();//DEBUG
		if (count($files) == 0) exit(); //nessun file
		
		$filename = $this->creaZip($files);
		
	 	$filesize = filesize($filename);
	 	if ($filesize == 0) {
	 		Log::error('File zip acsi vuoto',$filename);
	 		exit();
	 	}
	 	//TODO salvare il file da qualche parte?
	 	
	 	$filename_out = 'acsi_'.date('Y-m-d_H-i-s');
		header("Content-Disposition: attachment; filename=$filename_out.zip");
		header("Content-Type: application/zip");
		header("Content-length: $filesize\n\n");
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		
		readfile($filename);
	}
	
	private function getFileName($csvname, $num) {
		if ($num === NULL)
			return "$csvname.csv";
		else 
			return "{$csvname}_$num.csv";
	}
	
	private function rigeneraContenuto()
	{
		$files = array();
		
		foreach($this->ctrl->getElencoSocieta() as $idsoc=>$soc) {
			$cod_acsi = $soc->getCodiceAcsi();
			$nome_breve = $soc->getNomeBreve();
			$csvname = $cod_acsi.'_'.preg_replace('/\\W/', '', $nome_breve);
			
			//completo header con dati società e intestazione tabella
			$cont = "$nome_breve;;$cod_acsi;;\r\n";
			for($i=0; $i<3; $i++)
				$cont .= ";\r\n";
			$cont .= "N.Tessera;Cognome;Nome;Codice Fiscale;Qualifica;Email;Inserire Disciplina 1;Inserire Disciplina 2;Inserire Disciplina 3;Inserire Disciplina 4;;\r\n";
			
			$num = 0;
			foreach($this->ctrl->getTesserati($idsoc) as $idt=>$tess) {
				//linea tesserato
				$cod_fis = $tess->getCodiceFiscale();
				if($cod_fis == '' || $cod_fis === NULL)
					continue; //codice fiscale obbligatorio
				
				$cogn = $this->esc($tess->getCognome());
				$nome = $this->esc($tess->getNome());
				$qual = "Atleta/Socio";
				$email = $this->esc($tess->getEmail());
				
				$tessera = $this->ctrl->getNumTessera($idsoc, $idt);
				if ($tessera === NULL) continue; //finite le tessere
				
				$cont.="$tessera;$cogn;$nome;$cod_fis;$qual;$email;ARTI MARZIALI;;\r\n";
				$num++;
			}
			
			if($num > 0)
				$files[$this->getFileName($csvname, NULL)] = $cont;
		}
		
		return $files;
	}
	
	private function generaContenuto() {
		$files = array();
		
		foreach ($this->ctrl->getElencoSocieta() as $idsoc=>$soc) {
			$csvname = $soc->getCodiceAcsi().'_'.preg_replace('/\\W/', '', $soc->getNomeBreve());
			$num = 1;
			$cont = '';
			$riga = 1;
			foreach ($this->ctrl->getTesserati($idsoc) as $idt => $tess) {
				// linea tesserato
				$objp = Provincia::fromId($tess->getIDProvinciaRes());
				if ($objp === NULL) continue; //non ha inserito la provinicia
				else $prov = $objp->getSigla();
				
				$cogn = $this->esc($tess->getCognome());
				$nome = $this->esc($tess->getNome());
				$luogo_nasc = $this->esc($tess->getLuogoNascita());
				if ($luogo_nasc == '') continue;
				$data_nasc = $this->esc($tess->getDataNascita()->format('d/m/Y'));
				$ind = $this->esc($tess->getIndirizzo());
				if ($ind == '') continue;
				$cap = $this->esc($tess->getCap());
				if ($cap == '') continue;
				$citta = $this->esc($tess->getCittaRes());
				if ($citta == '') continue;
				if ($tess->getSesso() == Sesso::M)
					$sesso = 'M';
				else
					$sesso = 'F';
				$email = $this->esc($tess->getEmail());

				$tessera = $this->ctrl->getNumTessera($idsoc, $idt);
				if ($tessera === NULL) continue; //finite le tessere
				
				$cont.="$tessera;$cogn;$nome;$luogo_nasc;$data_nasc;Atleta/Socio;$ind;$cap;$citta;$prov;$sesso;$email;ARTI MARZIALI;;\r\n";
				if ($riga == 20) {
					$files[$this->getFileName($csvname, $num)] = $cont;
					$num++;
					$cont = '';
					$riga = 1;
				} else {
					$riga++;
				}
			}
			if ($riga > 1) {
				for($i=$riga; $i<=20; $i++)
					$cont .= ";;;;;;;;;;;;;;\r\n";
				if ($num == 1) $num = NULL;
				$files[$this->getFileName($csvname, $num)] = $cont;
			}
		}
		
		return $files;
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
		
		if ($zip->open($filename, ZIPARCHIVE::OVERWRITE)!==TRUE) {
			Log::error("Impossibile aprire il file zip", $filename);
			exit();
		}
		
		//carica i file acsi
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
		
		//riempie lo zip
		foreach ($files as $nome => $cont) {
			$zip->addFromString($nome, $header . $cont);// . $footer);
		}
		$zip->close();
		
		return $filename;
	}
}