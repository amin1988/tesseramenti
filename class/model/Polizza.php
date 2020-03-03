<?php

if (!defined("_BASE_DIR_"))
    exit();



include_model('Modello', 'ModelFactory');

class Polizza extends Modello
{

    const TAB = 'polizze_assicurative';
    const IDCOL = 'id';

    function __construct($nome_tabella = NULL, $codice_polizza = NULL)
    {

        $nome_tabella = ($nome_tabella == NULL) ? self::TAB : $nome_tabella;

        parent::__construct($nome_tabella, self::IDCOL, $codice_polizza);

        $this->TAB = $nome_tabella;
    }

    public function setTabella($nome_tab)
    {

        $this->TAB = $nome_tab;
    }

    public function setDati($array_dati)
    {

        if (!empty($array_dati))
        {

            foreach ($array_dati as $colonna => $valore)
            {

                parent::set($colonna, $valore);
            }
        }
    }

    public function unsetDati($array_dati)
    {

        if (!empty($array_dati))
        {

            foreach ($array_dati as $colonna => $valore)
            {

                parent::set($colonna, NULL);
            }
        }
    }

    public function checkScadenza()
    {

        $where = $this->createWhere(1); // controlla tra tutti i convalidati

        $db = Database::get();

        $sql = "";

        if (!empty($where))
            $sql = "SELECT data_a FROM polizze_stipulate WHERE " . $where;

        $result = $db->query($sql);

        $array_polizza = array();

        if ($result)
        {

            while ($row = $result->fetch_array(MYSQLI_ASSOC))
            {

                $array_polizza[] = $row;
            }
        }

        return $array_polizza;
    }

    public function polizzaScaduto()
    {

        $array_polizza = $this->checkScadenza();

        if (!empty($array_polizza))
        {

            $max_data_time_data_a = 0;

            for ($i = 0; $i < count($array_polizza); $i++)
            {

                $polizza_data_a = $array_polizza[$i]['data_a'];

                $data_time_data_a = strtotime($polizza_data_a);

                if ($data_time_data_a > $max_data_time_data_a) // se ci sono piu record mi prendo l'ultimo
                {

                    $max_data_time_data_a = $data_time_data_a;
                }
            }

            $oggi_date_time = strtotime(date('d-m-Y'));

            if ($oggi_date_time >= $max_data_time_data_a)
                return true; //polizza scaduto
        }

        //polizza regolare;

        return false;
    }

    public function createWhere($stato = 0)
    {

        $idsocieta = parent::get('idsocieta');

        $idtesserato = parent::get('idtesserato');

        $id_polizza = parent::get('id_polizza');

        $tipo_polizza = parent::get('tipo_polizza');



        $array_where = "";

        if (!empty($idsocieta))
        {

            $array_where [] = 'idsocieta=' . $idsocieta;
        }

        if (!empty($idtesserato))
        {

            $array_where [] = 'idtesserato=' . $idtesserato;
        }

        if (!empty($id_polizza))
        {

            $array_where [] = 'id_polizza=' . $id_polizza;
        }

        if (!empty($tipo_polizza))
        {

            $array_where [] = 'tipo_polizza=' . $tipo_polizza;
        }

        $array_where[] = " stato =  " . $stato;

        $where = implode(" AND ", $array_where);

        return $where;
    }

    public function getPolizza($stato = 0)
    {

        $db = Database::get();

        $where = $this->createWhere($stato);

        $sql = "";

        if (!empty($where))
            $sql = "SELECT * FROM " . $this->TAB . " WHERE  " . $where;

        $result = $db->query($sql);

        $array_polizza = array();

        if ($result)
        {

            $array_polizza = $result->fetch_array(MYSQLI_ASSOC);
        }

        return $array_polizza;
    }

    public function SearchRow()
    {

        $where = $this->createWhere();

        $db = Database::get();

        $sql = "";

        if (!empty($where))
            $sql = "SELECT count(*) as totale FROM " . $this->TAB . " WHERE  " . $where;

        $result = $db->query($sql);

        if ($result)
        {

            $array_num_rows = $result->fetch_array(MYSQLI_ASSOC);

            $num_rows = (int) $array_num_rows['totale'];
        }

        //$result = $db->select($this->TAB, $where);

        return $num_rows;
    }

    function salva()
    {

        $db = Database::get();

        $num_rows = $this->SearchRow();

        if (empty($num_rows)) //se falso allora aggiungi
        {

            $rp = $db->insert($this->TAB, parent::getValori());
        } else
        {

            $rp = false;

            $where = $this->createWhere();

            $array_form = $this->getValori();

            if (empty($array_form['id_polizza'])) // se non c'è id polizzza (ovvero è non è stato checckato)
            {

                $rp = $db->delete($this->TAB, $where); // allora elimina il record
            }

            if (!$rp) // se non è stato eliminato il record, allora è da aggiornare
                $rp = $db->update($this->TAB, parent::getValori(), $where);
        }

        return $rp;
    }

    public function creaFileExcelPoliz($array_assicurazioni)
    {



        $nome_societa = '';



        //tesserati

        $html_campi .= '<tr>';

        $html_campi .= '<td>' . "<strong>Nome</strong>" . '</td>';

        $html_campi .= '<td>' . "<strong>Cognome</strong>" . '</td>';

        $html_campi .= '<td>' . "<strong>Nome polizza </strong>" . '</td>';

        $html_campi .= '<td>' . "<strong>Nome societa</strong>" . '</td>';

        $html_campi .= '<td>' . "<strong>Data richiesta</strong>" . '</td>';

        $html_campi .= '<td>' . "<strong>Prezzo</strong>" . '</td>';

        $html_campi .= '<td>' . "<strong>Tipo polizza</strong>" . '</td>';

        $html_campi .= '</tr>';

        $totale_da_pagare = 0;



        foreach ($array_assicurazioni as $idsoc => $arr_poliz)
        {

            $file = "$idsoc.xls";

            $path = _BASE_DIR_ . 'segr/assicurazioni/' . $file;



            $fp = fopen($path, 'w');

            //itero i tesserati

            $html_tesserati = "";

            $html_consiglieri = "";

            $html_soc = "";

            $tipo_assicurazione = "";

            foreach ($arr_poliz as $poliz => $dati_poliz)
            {



                foreach ($dati_poliz as $keyPol => $row)
                {


                    $nome_tesserato = $row['nome'];

                    $cognome_tesserato = $row['cognome'];

                    $nome_polizza = $row['nome_polizza'];

                    $nome_societa = $row['nome_societa'];

                    $data_richiesta = $row['data_stipula'];

                    $prezzo = $row['prezzo'];

                    $tipo_polizza = $row['tipo_polizza'];



                    if ($tipo_polizza == 1)//societa
                    {

                        $font = ' bgcolor="#6699FF" ';

                        $html_soc .= '<tr>';

                        $html_soc .= "<td $font>-</td>";

                        $html_soc .= "<td $font>-</td>";

                        $html_soc .= "<td $font>$nome_polizza</td>";

                        $html_soc .= "<td $font>$nome_societa</td>";

                        $html_soc .= "<td $font>$data_richiesta</td>";

                        $html_soc .= "<td $font>$prezzo</td>";

                        $html_soc .= "<td $font>Societa</td>";

                        $html_soc .= '</tr>';
                    } else if ($tipo_polizza == 3) //consiglieri
                    {

                        $font = ' bgcolor="#FF9933" ';

                        $html_consiglieri .= '<tr>';

                        $html_consiglieri .= "<td $font>$nome_tesserato</td>";

                        $html_consiglieri .= "<td $font>$cognome_tesserato</td>";

                        $html_consiglieri .= "<td $font>$nome_polizza</td>";

                        $html_consiglieri .= "<td $font>$nome_societa</td>";

                        $html_consiglieri .= "<td $font>$data_richiesta</td>";

                        $html_consiglieri .= "<td $font>$prezzo</td>";

                        $html_consiglieri .= "<td $font>Consigliere</td>";

                        $html_consiglieri .= '</tr>';
                    } else if ($tipo_polizza == 4) //tesserati
                    {



                        $font = ' bgcolor="#FFFF00" ';



                        $html_tesserati .= '<tr>';

                        $html_tesserati .= "<td $font>$nome_tesserato</td>";

                        $html_tesserati .= "<td $font>$cognome_tesserato</td>";

                        $html_tesserati .= "<td $font>$nome_polizza</td>";

                        $html_tesserati .= "<td $font>$nome_societa</td>";

                        $html_tesserati .= "<td $font>$data_richiesta</td>";

                        $html_tesserati .= "<td $font>$prezzo</td>";

                        $html_tesserati .= "<td $font>Tesserato</td>";

                        $html_tesserati .= '</tr>';
                    }
                }
            }

            $body = "";

            $body .= '<table border="1">' . $html_campi;

            if (!empty($html_tesserati))
            {

                $body .= $html_tesserati;
            }



            if (!empty($html_soc))
            {

                $body .= $html_soc;
            }



            if (!empty($html_consiglieri))
            {

                $body .= $html_consiglieri;
            }

            $body .= '</table>';

            $body .= "<br><br>";



            $html_riepilogo = "";

            $html_riepilogo .= '<tr>';

            $html_riepilogo .= '<td>' . "<strong>Nome società</strong>" . '</td>';

            $html_riepilogo .= '<td>' . "<strong>Totale da pagare</strong>" . '</td>';

            $html_riepilogo .= '</tr>';

            $font = ' bgcolor="#FF0000" ';

            $totale_da_pagare = $array_assicurazioni[$idsoc]['totale_societa'];

            $totale_da_pagare = str_replace('.', ',', sprintf('&euro; %.2f', $totale_da_pagare));



            $html_riepilogo .= '<tr>';

            $html_riepilogo .= "<td $font>$nome_societa</td>";

            $html_riepilogo .= "<td $font>$totale_da_pagare</td>";

            $html_riepilogo .= '</tr>';



            $body .= '<table border="1">';

            $body .= $html_riepilogo;

            $body .= '</table>';



            $data = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">

<head>

    <!--[if gte mso 9]>

    <xml>

        <x:ExcelWorkbook>

            <x:ExcelWorksheets>

                <x:ExcelWorksheet>

                    <x:Name>' . $nome_societa . '</x:Name>

                    <x:WorksheetOptions>

                        <x:Print>

                            <x:ValidPrinterInfo/>

                        </x:Print>

                    </x:WorksheetOptions>

                </x:ExcelWorksheet>

            </x:ExcelWorksheets>

        </x:ExcelWorkbook>

    </xml>

    <![endif]-->

    

<meta charset="utf-8" />



</head>

';



            $head = '<meta charset="utf-8" />';

            fwrite($fp, $data . $body . '</html>');

            fclose($fp);
        }
    }

    public function getAllSocietaConPolizza()
    {

        $db = Database::get();

        $sql = "SELECT distinct idsocieta FROM polizze_stipulate WHERE stato=0 ";

        $result = $db->query($sql);

        $array_idsoc = array();

        if ($result)
        {
            while ($row = $result->fetch_row())
            {
                $array_idsoc[] = $row[0];
            }
        }

        return $array_idsoc;
    }

    public function getNomeSoc($id_soc)
    {

        $db = Database::get();

        $query = "SELECT nome FROM societa WHERE idsocieta =  " . $id_soc;

        $result_soc = $db->query($query);

        $nome_societa = "";

        if ($result_soc)
        {

            $nome_societa = $result_soc->fetch_row();

            $nome_societa = $nome_societa[0];
        }

        return $nome_societa;
    }

    public function getAllPolizRichieste()
    {

        $db = Database::get();

        $sql = "SELECT distinct idsocieta FROM polizze_stipulate WHERE stato=0 ";

        $result = $db->query($sql);

        $array_assicurazioni = array();

        if ($result)
        {

            $array_idsoc = $result->num_rows;

            if ($result->num_rows > 0)
            {

                while ($riga = $result->fetch_row())
                {

                    $id_soc = $riga[0];

                    //$id_soc = $id_soc[0];

                    $row = array();



                    $nome_societa = $this->getNomeSoc($id_soc); // nome della societa

                    $query = "SELECT pa.nome_polizza,pa.prezzo,ps.id_polizza,ps.idtesserato,ps.data_stipula,ps.tipo_polizza "
                            . "FROM `polizze_stipulate` as ps "
                            . "inner join polizze_assicurative as pa "
                            . "ON ps.id_polizza = pa.id "
                            . " WHERE ps.stato=0 AND ps.idsocieta = " . $id_soc;

                    $result_pol = $db->query($query);

                    if ($result_pol)
                    {

                        while ($array_poliz = $result_pol->fetch_array(MYSQLI_ASSOC))
                        {

                            $id_poliz = $array_poliz['id_polizza'];

                            $id_tess = $array_poliz['idtesserato'];

                            $row[$id_tess][$id_poliz]['nome_polizza'] = $array_poliz['nome_polizza'];

                            $row[$id_tess][$id_poliz]['prezzo'] = $array_poliz['prezzo'];

                            $row[$id_tess][$id_poliz]['data_stipula'] = $array_poliz['data_stipula'];

                            $row[$id_tess][$id_poliz]['nome_societa'] = $nome_societa;

                            $row[$id_tess][$id_poliz]['tipo_polizza'] = $array_poliz['tipo_polizza'];
                        }
                    }

                    $query = "SELECT tess.nome,tess.cognome,ps.id_polizza,ps.idtesserato,ps.data_stipula,ps.tipo_polizza "
                            . "FROM `polizze_stipulate` as ps "
                            . "inner join tesserati as tess "
                            . "ON ps.idtesserato = tess.idtesserato "
                            . " WHERE ps.stato=0 AND ps.idsocieta = " . $id_soc;

                    $result_tess = $db->query($query);

                    if ($result_tess)
                    {

                        while ($array_tess = $result_tess->fetch_array(MYSQLI_ASSOC))
                        {

                            $id_poliz = $array_tess['id_polizza'];

                            $id_tess = $array_tess['idtesserato'];

                            $row[$id_tess][$id_poliz]['nome'] = $array_tess['nome'];

                            $row[$id_tess][$id_poliz]['cognome'] = $array_tess['cognome'];

                            $row[$id_tess][$id_poliz]['data_stipula'] = $array_tess['data_stipula'];

                            $row[$id_tess][$id_poliz]['nome_societa'] = $nome_societa;

                            $row[$id_tess][$id_poliz]['tipo_polizza'] = $array_tess['tipo_polizza'];
                        }
                    }



                    //calcolo del totale

                    $query = "SELECT sum(pa.prezzo) as tot_soc FROM polizze_assicurative AS pa INNER JOIN polizze_stipulate as ps ON pa.id=ps.id_polizza "
                            . " WHERE ps.stato=0 AND ps.idsocieta = " . $id_soc;

                    $result_sum = $db->query($query);

                    if ($result_sum)
                    {

                        $array_sum = $result_sum->fetch_array(MYSQLI_ASSOC);

                        $somma_soc = $array_sum['tot_soc'];

                        $row['totale_societa'] = $somma_soc;
                    }





                    $array_assicurazioni [$id_soc] = $row;
                }
            }
        }

        return $array_assicurazioni;
    }

    /**

     * mwtodo che convalida le polizze assicurative richieste dalle societa (lato segreteria)

     * @param type $id_soc

     * @return boolean

     */
    public function convalidaPolizzeSocieta($id_soc)
    {

        $db = Database::get();



        $data_da = date("d-m-Y");

        $cur_date_add_month = date('d-m-Y', strtotime("+12 months"));

        $data_a = $cur_date_add_month;

        $tutto_prossimo_anno_solare = date('31-12-Y', strtotime("+12 months"));



        $query = "UPDATE polizze_stipulate ";

        $query .= " SET data_da= '$data_da'  , data_a = '$tutto_prossimo_anno_solare' , stato=1, data_pagamento=CURRENT_TIMESTAMP()";

        $query .= " WHERE stato=0 AND idsocieta=" . $id_soc;

        $result = $db->query($query);

        if ($result)
        {

            return true;
        }

        return false;
    }

    public function registraPagamentoTesserato($idtesserato, $id_soc)
    {

        if (empty($idtesserato))
        {

            return false;
        }

        if (empty($id_soc))
        {

            return false;
        }

        $db = Database::get();

        $data_da = date("d-m-Y");

        $cur_date_add_month = date('d-m-Y', strtotime("+12 months"));

        $data_a = $cur_date_add_month;

        $tutto_prossimo_anno_solare = date('31-12-Y', strtotime("+12 months"));



        $query = "UPDATE polizze_stipulate ";

        $query .= " SET data_da= '$data_da'  , data_a = '$tutto_prossimo_anno_solare' , stato=1, data_pagamento=CURRENT_TIMESTAMP()";

        $query .= " WHERE stato=0 AND idtesserato=" . $idtesserato . " AND idsocieta=" . $id_soc;

        $result = $db->query($query);

        if ($result)
        {

            return true;
        }

        return false;
    }

    public function registraPagamentoPolizza($tipo_polizza, $id_soc)
    {

        $db = Database::get();



        $data_da = date("d-m-Y");

        $cur_date_add_month = date('d-m-Y', strtotime("+12 months"));

        $data_a = $cur_date_add_month;

        $tutto_prossimo_anno_solare = date('31-12-Y', strtotime("+12 months"));



        $query = "UPDATE polizze_stipulate ";

        $query .= " SET data_da= '$data_da'  , data_a = '$tutto_prossimo_anno_solare' , stato=1, data_pagamento=CURRENT_TIMESTAMP()";

        $query .= " WHERE stato=0 AND tipo_polizza=" . $tipo_polizza . " AND idsocieta=" . $id_soc;

        $result = $db->query($query);

        if ($result)
        {

            return true;
        }

        return false;
    }

    public function unlinkFile($id_soc)
    {

        if (empty($id_soc))
        {

            return false;
        }

        $file = "$id_soc.xls";

        $path = _BASE_DIR_ . 'segr/assicurazioni/' . $file;

        unlink($path);

        return true;
    }

    public function getPolizzaAssicurazione($id_soc, $stato = 0)
    {



        $db = Database::get();

        $sql = "SELECT * FROM polizze_stipulate WHERE idsocieta=$id_soc AND stato= " . $stato;

        $result = $db->query($sql);

        $array_polizza_rich = array();

        if ($result)
        {

            while ($polizza = $result->fetch_array(MYSQLI_ASSOC))
            {

                $array_polizza_rich[] = $polizza;
            }
        }

        return $array_polizza_rich;
    }

    public function getTotaleTesserato($idtess)
    {

        $db = Database::get();

        $sql = "SELECT *  FROM polizze_stipulate where idtesserato=$idtess";

        $result = $db->query($sql);

        $len_array = array();

        $totale = 0;

        if ($result)
        {

            while ($len_array = $result->fetch_array(MYSQLI_ASSOC))
            {

                $id_polizza = $len_array['id_polizza'];

                $totale += $this->calcolaTotale(1, $id_polizza);
            }
        }

        return $totale;
    }

    public function getRiepilogoPagamento($id_soc)
    {

        //$array_polizza = $this->getPolizzaAssicurazione($id_soc);

        $tot_societa = $this->countTipo($id_soc, 1);

        $tot_settori = $this->countTipo($id_soc, 2);

        $tot_consiglieri = $this->countTipo($id_soc, 3);

        $tot_tesserati = $this->countTipo($id_soc, 4);



        $totale_complessivo_da_saldare = $tot_societa['totale_tipo_polizza_da_saldare'] + $tot_settori['totale_tipo_polizza_da_saldare'] + $tot_consiglieri['totale_tipo_polizza_da_saldare'] + $tot_tesserati['totale_tipo_polizza_da_saldare'];



        $totale_complessivo_saldato = $tot_societa['totale_tipo_polizza_saldato'] + $tot_settori['totale_tipo_polizza_saldato'] + $tot_consiglieri['totale_tipo_polizza_saldato'] + $tot_tesserati['totale_tipo_polizza_saldato'];



        $array_riepilogo = array('tot_societa' => $tot_societa, 'tot_settori' => $tot_settori, 'tot_consiglieri' => $tot_consiglieri, 'tot_tesserati' => $tot_tesserati, 'totale_complessivo_da_saldare' => $totale_complessivo_da_saldare, 'totale_complessivo_saldato' => $totale_complessivo_saldato);

        return $array_riepilogo;
    }

    public function countTipo($id_soc, $tipo_polizza)
    {

        $db = Database::get();

        $sql = "SELECT distinct id_polizza FROM polizze_stipulate ";

        $result = $db->query($sql);

        $array_idpoliz = array();

        $totale_complessivo_saldato = 0;

        $totale_complessivo_da_saldare = 0;

        if ($result)
        {

            while ($row = $result->fetch_array(MYSQLI_NUM))
            {

                $array_idpoliz [] = $row;
            }
        }



        $array_group_tipo = array();

        if (!empty($array_idpoliz))
        {

            for ($i = 0; $i < count($array_idpoliz); $i++)
            {

                $id_polizza = $array_idpoliz[$i][0];



                //saldato

                $sql = "SELECT count(*) FROM polizze_stipulate WHERE idsocieta=$id_soc AND id_polizza=" . $id_polizza . " AND tipo_polizza=" . $tipo_polizza . " and stato=0";

                $result = $db->query($sql);

                if ($result)
                {



                    $tot = $result->fetch_row();

                    $valore = $tot[0];

                    if ($valore > 0)
                    {

                        $array_group_tipo [$id_polizza]['qt_da_saldare'] = $valore;

                        $array_group_tipo [$id_polizza]['totale_da_saldare'] = $this->calcolaTotale($valore, $id_polizza);

                        $totale_complessivo_da_saldare += $this->calcolaTotale($valore, $id_polizza);
                    }
                }

                //saldato

                $sql = "SELECT count(*) FROM polizze_stipulate WHERE idsocieta=$id_soc AND id_polizza=" . $id_polizza . " AND tipo_polizza=" . $tipo_polizza . " and stato=1";

                $result = $db->query($sql);

                if ($result)
                {



                    $tot = $result->fetch_row();

                    $valore = $tot[0];

                    if ($valore > 0)
                    {

                        $array_group_tipo [$id_polizza]['qt_saldato'] = $valore;

                        $array_group_tipo [$id_polizza]['totale_saldato'] = $this->calcolaTotale($valore, $id_polizza);

                        $totale_complessivo_saldato += $this->calcolaTotale($valore, $id_polizza);
                    }
                }
            }
        }

        $array_group_tipo['totale_tipo_polizza_da_saldare'] = $totale_complessivo_da_saldare;

        $array_group_tipo['totale_tipo_polizza_saldato'] = $totale_complessivo_saldato;

        return $array_group_tipo;
    }

    public function calcolaTotale($qt, $tipo_polizza)
    {

        $db = Database::get();

        $sql = "SELECT prezzo  FROM polizze_assicurative where id=" . $tipo_polizza;

        $result = $db->query($sql);

        $polizza_assicurativa = array();

        $totale = 0;

        if ($result)
        {

            $polizza_assicurativa = $result->fetch_array(MYSQLI_ASSOC);



            $totale = ($polizza_assicurativa['prezzo'] * $qt);
        }

        return $totale;
    }

    public function getPolizzaNome($idtess)
    {

        $db = Database::get();

        $sql = "SELECT distinct id_polizza  FROM polizze_stipulate where stato=0 and idtesserato=$idtess";

        $result = $db->query($sql);

        $array_id_pol = array();

        if ($result)
        {

            while ($row = $result->fetch_array(MYSQLI_NUM))
            {

                $elem = $row[0];

                $array_id_pol [] = $elem;
            }
        }

        return $array_id_pol;
    }

    public function salvaPolizzaTesserati($array_dati)
    {

        $db = Database::get();



        $this->setDati($array_dati);

        $num_rows = $this->SearchRow();

        if (empty($num_rows)) //se falso allora aggiungi
        {

            $rp = $db->insert($this->TAB, $array_dati);
        } else
        {

            $where = $this->createWhere();

            $rp = $db->update($this->TAB, $array_dati, $where);
        }

        $this->unsetDati($array_dati);

        return $rp;
    }

    public static function fromID($idPolizza)
    {



        $db = Database::get();

        $rs = $db->select(self::TAB, "id = '$idPolizza'");



        return ModelFactory::get(__CLASS__)->singleFromSql($rs, self::IDCOL);
    }

    public static function elenco($where = "1")
    {



        $rs = Database::get()->select('polizze_assicurative', $where);

        return ModelFactory::get(__CLASS__)->listaCompleta(self::TAB, self::IDCOL);
    }

    function getIdSocieta()
    {

        return $this->get['idsocieta'];
    }

    /*     * ** */

    function getNomePolizza()
    {



        return $this->get('nome_polizza');
    }

    function getPrezzo()
    {

        return $this->get('prezzo');
    }

    function getObbligatorio()
    {

        return $this->get('obbligatorio');
    }

    function getIdTipo()
    {

        return $this->get('idTipo');
    }

}