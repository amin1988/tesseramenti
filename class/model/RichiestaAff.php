<?php
if (!defined("_BASE_DIR_")) exit();
include_model('Modello');

class RichiestaAff extends Modello {
	
	private $settori = NULL;

	/**
	 * Restituisce le richieste di affiliazione presenti nel database
	 * @return RichiestaAff[]
	 */
	public static function getInDB()
	{
		$rs = Database::get()->select('richieste_aff');
		return ModelFactory::listaSql('RichiestaAff', $rs);
	}
	
	public function __construct($id=NULL) {
		parent::__construct('richieste_aff', 'idrichiesta', $id);
	}

	/**
	 * 
	 * @return int 
	 */
	public function getIDComune() {
		return $this->get('idcomune');
	}

	/**
	 * 
	 * @param int $val
	 */
	public function setIDComune($val) {
		$this->set('idcomune', $val);
	}
	
	/**
	 * 
	 * @return int
	 */
	public function getIDFederazione() {
		return $this->get('idfederazione');
	}
	
	public function setIDFederazione($val) {
		$this->set('idfederazione', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getNome() {
		return $this->get('nome');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setNome($val) {
		$this->set('nome', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getNomebreve() {
		return $this->get('nomebreve');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setNomebreve($val) {
		$this->set('nomebreve', $val);
	}


	/**
	 * 
	 * @return Data 
	 */
	public function getDataCost() {
		return $this->getData('data_cost');
	}

	/**
	 * 
	 * @param Data $val
	 */
	public function setDataCost($val) {
		$this->setData('data_cost', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getPIva() {
		return $this->get('p_iva');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setPIva($val) {
		$this->set('p_iva', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getSedeLegale() {
		return $this->get('sede_legale');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setSedeLegale($val) {
		$this->set('sede_legale', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getCap() {
		return $this->get('cap');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setCap($val) {
		$this->set('cap', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getTel() {
		return $this->get('tel');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setTel($val) {
		$this->set('tel', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getFax() {
		return $this->get('fax');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setFax($val) {
		$this->set('fax', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getEmail() {
		return $this->get('email');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setEmail($val) {
		$this->set('email', $val);
	}


	/**
	 * 
	 * @return string 
	 */
	public function getWeb() {
		return $this->get('web');
	}

	/**
	 * 
	 * @param string $val
	 */
	public function setWeb($val) {
		$this->set('web', $val);
	}


	/**
	 * 
	 * @return Data 
	 */
	public function getDataInserimento() {
		return $this->getTimestamp('data_inserimento');
	}

	/**
	 * 
	 * @param Data $val
	 */
	public function setDataInserimento($val) {
		$this->setTimestamp('data_inserimento', $val);
	}
	
	/**
	 * 
	 * @return integer[]
	 */
	public function getSettori()
	{
		if($this->settori === NULL)
		{
			$str = $this->get('settori');
			$arset = explode(',', $str);
			$this->settori = $arset;
		}
		else $arset = $this->settori;
		return $arset;
	}
	
	/**
	 * 
	 * @param integer[] $val
	 */
	public function setSettori($val)
	{
		$str = implode(',', $val);
		$this->set('settori', $str);
		$this->settori = $val;
	}
	
	/**
	 * 
	 * @param integer $idset
	 */
	public function  haSettore($idset)
	{
		return in_array($idset, $this->getSettori());
	}

}
