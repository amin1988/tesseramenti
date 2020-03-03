<?php

if (!defined("_BASE_DIR_"))
    exit();

include_controller('ConfermaPolizze');

include_model('Societa', 'Polizza');

include_formview('FormView');

class ConfermaPolizze extends ViewWithForm
{

    private $ctrl;
    private $elenco;

    public function __construct()
    {

        $this->ctrl = new ConfermaPolizzeCtrl();
        $this->form = new FormView($this->ctrl->getForm());
    }

    public function convalidaFile($id_soc_post)
    {
        $polizza = new Polizza('polizze_stipulate');
        $convalidato = $polizza->convalidaPolizzeSocieta($id_soc_post);
        if ($convalidato)
        {
           $polizza->unlinkFile($id_soc_post);
        }
    }

    public function stampa()
    {

        $fv = $this->form;

        $fv->stampaInizioForm();
        $convalida = !empty($_POST['submit']) ? $_POST['submit'] : NULL;
        if (!empty($convalida))
        {
            $id_soc_post = !empty($_POST['id_soc']) ? $_POST['id_soc'] : 0;
            if (!empty($id_soc_post))
            {
                $this->convalidaFile($id_soc_post);
            }
        }

        $url_form = $_SERVER["PHP_SELF"];
        print '<form action="' . $url_form . '" method="post" >';
        print '<table class="table table-hover table-condensed table-bordered">';
        print '<tr>';
        print '<td><strong>Nome societa</strong></td>';
        print '<td><strong>File</strong></td>';
        print '<td><strong>Conferma</strong></td>';
        print '</tr>';

        $polizza = new Polizza('polizze_stipulate');
        $array_idsoc = $polizza->getAllPolizRichieste();
        $polizza->creaFileExcelPoliz($array_idsoc);
        foreach ($array_idsoc as $id_soc=>$idtess)
        {
           
            //$id_soc = $id_soc[0];
            $nome_soc = $polizza->getNomeSoc($id_soc);
            $file = "$id_soc.xls";
            $path = "/tesseramento/segr/assicurazioni/" . $file;

            print '<tr>';
            print '<td>' . $nome_soc . '</td>';

            print'<td> <a href="' . $path . '" target="_blank">';
            print'<strong>' . "Visualizza" . '</strong>';
            print'</a></td>';
            print'<td>';
            print '<div class="modal-footer">';
            print '<center>';
            print '<input name="submit" class="btn btn-primary" type="submit" value="Convalida">';
            // print '<button type="button" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Convalida</button>';
            print '</center>';
            print '</div>';
            print'</td>';

            print '<input type="hidden" name="id_soc" value="' . $id_soc . '">';

            print '</tr>';
        }

        print '</table>';
        print '</form>';
    }

}