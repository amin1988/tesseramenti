<?php

if (!defined("_BASE_DIR_"))
    exit();

include_class('Database', 'Data', 'Timestamp');

class Modello
{

    protected $_backup = false;
    protected $_errore;

    /**

     * @var string

     */
    private $tabella;

// 	/**
// 	 * @var string[] formato: nome lista => tabella
// 	 */
// 	private $listeTab = array();
// 	/**
// 	 * @var string[] formato: nome lista => colonna
// 	 */
// 	private $listeCol = array();

    /**

     * @var string

     */
    private $chiaveCol;
    private $id = NULL;

    /**

     * Indica se i dati sono stati letti dal database

     * @var boolean

     */
    private $letto = false;

    /**

     * riepito da carica, accessibile con get e set

     * @var array

     */
    private $valori = array();

    /**

     * riempito se $_backup=true e letto da ripristina

     * @var array

     */
    private $valoribk = NULL;

// 	/**
// 	 * @var array[]
// 	 */
// 	private $liste = NULL;

    /**

     * true se campo modificato con set, usato da update per aggiornare i campi

     * @var boolean[]

     */
    private $modificato;

// 	/**
// 	 * true se campo modificato con set, usato da update per aggiornare i campi
// 	 * @var boolean[]
// 	 */
// 	private $modliste;

    /**

     * @var string[]

     */
    private $ignora = NULL;

    public static function _creaConDati($classe, $dati)
    {

        $m = new $classe(NULL);

        $m->carica($dati);

        return $m;
    }

    /**

     * Crea un nuovo modello

     * @param string $tabella la tabella da dove leggere i dati

     * @param string $chiaveCol il nome della colonna chiave primaria

     * @param mixed $id [opzionale] l'id del modello da caricare

     */
    protected function __construct($tabella, $chiaveCol, $id = NULL)
    {

        $this->tabella = $tabella;

        $this->chiaveCol = $chiaveCol;

        if ($id !== NULL)
            $this->id = Database::get()->quote($id);
    }

    /**

     * Indica se questo oggetto è presente sul database

     * @return boolean

     */
    public function esiste()
    {

        if (!$this->haId())
            return false;

        if (!$this->letto)
            $this->carica();

        return $this->letto;
    }

    /**

     * Restituisce l'id di questo oggetto

     * o NULL se è un nuovo oggetto mai salvato sul database

     * @return mixed|NULL

     */
    public function getId()
    {

        return $this->id;
    }

    /**

     * Indica se l'id è stata impostata

     * @return boolean

     */
    public function haId()
    {

        return $this->id !== NULL;
    }

    /**

     * Salva l'oggetto sul database

     */
    public function salva()
    {

        $this->_errore = NULL;

        if ($this->id === NULL)
            return $this->insert();
        else
            return $this->update();
    }

    /**

     * Elimina l'oggetto dal database 

     */
    public function elimina()
    {

        //se chaiveVal non impostato non fa niente

        if ($this->id === NULL)
            return;

        Database::get()->delete($this->tabella, "$this->chiaveCol = '$this->id'");

        //elimina il valore della chiave

        $this->id = NULL;



        return true; //TODO verificare che l'eliminazione è andata a buon fine
    }

    /**

     * Se la funzione di backup è abilitata, ripristina i valori dell'oggetto

     */
    public function ripristina()
    {

        if (!$this->_backup || $this->valoribk === NULL)
            return;

        foreach ($this->valoribk as $c => $v)
        {

            $this->valori[$c] = $v;

            $this->modificato[$c] = false;
        }
    }

    public function getErrore()
    {

        return $this->_errore;
    }

    /**

     * Restituisce il valore di una colonna

     * @param string $colonna la colonna da leggere

     * @return string

     */
    protected function get($colonna)
    {

        $v = $this->valori;

        if (!$this->letto && $this->id !== NULL && !isset($v[$colonna]))
        {

            $this->carica();
        }

        if (isset($this->valori[$colonna]))
            return $this->valori[$colonna];
        else
            return NULL;
    }

// 	/**
// 	 * @param string $nome
// 	 * @return array
// 	 */
// 	protected function getLista($nome){
// 		$v = $this->liste;
// 		if (!isset($v[$nome]idnull($this->id)) {
// 			$this->liste[$nome] = $this->caricaLista($nome);
// 			$this->modliste[$nome] = false;
// 		}
// 		if (isset($this->liste[$nome]))
// 			return $this->liste[$nome];
// 		else return NULL;
// 	}

    /**

     * Restituisce il valore di una colonna di tipo data

     * @param string $colonna la colonna da leggere

     * @return Data

     */
    protected function getData($colonna)
    {

        $v = $this->get($colonna);

        if ($v === NULL)
            return NULL;

        return DataUtil::get()->fromSql($v);
    }

    protected function getTimestamp($colonna)
    {

        $ts = $this->get($colonna);

        if ($ts === NULL)
            return NULL;

        return TimestampUtil::get()->fromSql($ts);
    }

    /**

     * Restituisce il valore di una colonna di tipo booleano

     * @param string $colonna la colonna da leggere

     * @return boolean

     */
    protected function getBool($colonna)
    {

        $v = $this->get($colonna);

        if ($v === NULL)
            return NULL;

        return ($v == 1);
    }

    /**

     * Imposta il valore di una colonna

     * @param string $colonna la colonna in cui scrivere

     * @param $valore il valore da scrivere

     */
    protected function set($colonna, $valore)
    {

        if (!isset($this->valori[$colonna]) || $this->valori[$colonna] != $valore)
            $this->modificato[$colonna] = true;

        $this->valori[$colonna] = $valore;
    }

// 	/**
// 	 * @access protected
// 	 * @param string $nome
// 	 * @param array $valore
// 	 */
// 	protected function setLista($nome, $valore) {
// 		//TODO fare pi� furbo
// 		$this->liste[$nome] = $valore;
// 		$this->modliste[$nome] = true;
// 	}

    /**

     * Imposta il valore di una colonna di tipo data

     * @param string $colonna la colonna in cui scrivere

     * @param Data $valore il valore da scrivere

     */
    protected function setData($colonna, $valore)
    {

        if ($valore === NULL)
            $d = NULL;
        else
            $d = $valore->toSQL();

        $this->set($colonna, $d);
    }

    /**

     * Imposta il valore di una colonna di tipo timestamp

     * @param string $colonna la colonna in cui scrivere

     * @param Timestamp $valore il valore da scrivere

     */
    protected function setTimestamp($colonna, $valore)
    {

        if ($valore === NULL)
            $ts = NULL;
        else
            $ts = $valore->toSQL();

        $this->set($colonna, $ts);
    }

    /**

     * Imposta il valore di una colonna di tipo booleano

     * @param string $colonna la colonna in cui scrivere

     * @param $valore il valore da scrivere

     */
    protected function setBool($colonna, $valore)
    {

        if ($valore === NULL)
            $b = NULL;

        else if ($valore)
            $b = 1;
        else
            $b = 0;

        $this->set($colonna, $b);
    }

    /**

     * Indica se il valore di una colonna è stato modificato

     * dopo la lettura e non è stato ancora salvato

     * @param string $colonna

     * @return boolean

     */
    protected function isMod($colonna)
    {

        if (isset($this->modificato[$colonna]))
            return $this->modificato[$colonna];
        else
            return false;
    }

    /**

     * Indica le colonne da non tenere memorizzate nell'oggetto 

     * @param string[] $ignora l'elenco dei nomi delle colonne da ignorare

     */
    protected function ignoraCol($ignora)
    {

        if (is_string($ignora))
            $ignora = array($ignora);

        $this->ignora = $ignora;

        $this->eliminaVal();
    }

// 	/**
// 	 * Abilita la lettura di una tabella con le colonne $chiaveCol e $colonna
// 	 * @param string $nome nome della lista
// 	 * @param string $tabella nome della tabella contenente la lista
// 	 * @param string $colonna nome della colonna da leggere
// 	 */
// 	protected function aggiungiLista($nome, $tabella, $colonna) {
// 		$this->listeTab[$nome] = $tabella;
// 		$this->listeCol[$nome] = $colonna;
// 	}

    /**

     * Elimina da $valori le colonne in $ignora

     */
    private function eliminaVal()
    {

        if ($this->ignora === NULL || $this->valori === NULL)
            return;

        foreach ($this->ignora as $c)
        {

            unset($this->valori[$c]);
        }
    }

    /**

     * Legge i valori dell'oggetto dal database

     * @return array un array associativo con le coppie (colonna => valore)

     * oppure NULL se l'id non è stato impostato o se non esiste nessuna

     * riga col l'id impostato

     */
    protected function leggiDaDb()
    {

        //se non ha id non restituisce niente

        if ($this->id === NULL)
            return NULL;



        $rs = Database::get()->select($this->tabella, "$this->chiaveCol = '$this->id'");

        return $rs->fetch_assoc();
    }

    /**

     * Carica i valori del modello dal database o dall'array associativo $row

     * @param array $row array associativo contenente colonna -> valore

     */
    protected function carica($row = NULL)
    {

        if ($row === NULL)
        {

            $row = $this->leggiDaDb();

            if ($row !== NULL)
                $this->carica($row);
        } else
        {

            foreach ($row as $c => $v)
            {

                //se questa colonna è la chiave

                if ($c == $this->chiaveCol)
                {

                    //se la chiave non è impostata la salva

                    if ($this->id === NULL)
                        $this->id = Database::get()->quote($v);
                } else if (!isset($this->modificato[$c]) || !$this->modificato[$c])
                {

                    //se questo valore non è stato modificato
                    //salva il valore e lo imposta come non modificato 

                    $this->valori[$c] = $v;

                    $this->modificato[$c] = false;

                    if ($this->_backup)
                        $this->valoribk[$c] = $v;
                }
            }

            $this->eliminaVal();

            $this->letto = true;
        }
    }

    /**

     * inserisce nel database e imposta il valore id

     * @access private

     */
    protected function insert()
    {

        $conn = Database::get();

        $ret = $conn->insert($this->tabella, $this->valori);

        if (!$ret)
            $this->_errore = $conn->error();

        /* 		if ($ret && $conn->affectedRows() != 1) {

          $this->_errore = 'Affected rows: '.$conn->affectedRows();

          $ret = false;

          }
         */
        if ($ret)
        {

            $this->id = $conn->quote($conn->lastId());

            foreach ($this->modificato as $km => $mod)
                $this->modificato[$km] = false;
        }

        return $ret;
    }

    /**

     * modifica solo valori con modificato=true

     * @access private

     */
    protected function update()
    {

        $mod = array();

        foreach ($this->valori as $c => $v)
        {

            if ($this->modificato[$c])
            {

                $mod[$c] = $v;
            }
        }

        if (count($mod) == 0)
            return true;

        $db = Database::get();

        $res = $db->update($this->tabella, $mod, "$this->chiaveCol = '$this->id'");

        if (!$res)
            $this->_errore = $db->error();

// 		else if($db->affectedRows() != 1) $this->_errore = "update affected rows =".$db->affectedRows();

        if ($res)// && ($db->affectedRows() == 1))
        {

            foreach ($this->modificato as $km => $mod)
                $this->modificato[$km] = false;

            return true;
        } else
        {

            return false;
        }
    }

    public function logValori($livello, $note, $dett = NULL)
    {

        if ($dett === NULL)
            $dett = $this->valori;
        else
            $dett['val'] = $this->valori;

        Log::add($livello, $note, $this->valori);
    }

    protected function getTabella()
    {

        return $this->tabella;
    }

    protected function isLetto()
    {

        return $this->letto;
    }

    protected function getValori()
    {

        return $this->valori;
    }

}
