<?php

require_once '../../public/tcpdf/tcpdf.php';
class ExportController {
    public function generatePDF()
    {
        // YOUR DATA
        $data = Flight::get('fiche_data') ?? []; // si tu inclues ce fichier depuis un controller
        // 1. Nouveau PDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // 2. Ton HTML SANS BOOTSTRAP
        $html = '
        <h1 style="text-align:center;">Fiche de paie Novembre 2025</h1>

        <h2>'.$data["type_majoration"].'</h2>

        <p><strong>Nom et prénoms :</strong> '.$data["employe"]["nom"].' '.$data["employe"]["prenom"].'</p>
        <p><strong>Matricule :</strong> '.$data["employe"]["id_employe"].'</p>
        <p><strong>Date embauche :</strong> '.$data["employe"]["date_embauche"].'</p>
        <p><strong>Ancienneté :</strong> '.$data["anciennete"].'</p>

        <p><strong>Salaire de base :</strong> '.$data["employe"]["salaire_base"].'</p>
        <p><strong>Taux journalier :</strong> '.$data["taux_journalier"].'</p>
        <p><strong>Taux horaire :</strong> '.$data["taux_horaire"].'</p>

        <br>

        <table border="1" cellpadding="4">
        <tr>
            <th>Designation</th>
            <th>Nombre</th>
            <th>Taux</th>
            <th>Montant</th>
        </tr>';

        foreach ($data["details_prime"] as $prime) {
        $html .= '
        <tr>
            <td>'.$prime["designation"].'</td>
            <td>1</td>
            <td>'.$prime["montant"].'</td>
            <td>'.$prime["montant"].'</td>
        </tr>';
        }

        $html .= '
        <tr>
            <td colspan="3"><strong>Salaire brut</strong></td>
            <td>'.$data["salaire_brute"].'</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Retenue Cnaps</strong></td>
            <td>'.$data["cnaps"]["taux"].'</td>
            <td>'.$data["cnaps"]["montant"].'</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Retenue OSTIE</strong></td>
            <td>'.$data["ostie"]["taux"].'</td>
            <td>'.$data["ostie"]["montant"].'</td>
        </tr>';

        foreach ($data["details_irsa"] as $i) {
        $html .= '
        <tr>
            <td colspan="2">'.$i["designation"].'</td>
            <td>'.$i["pourcentage"].'</td>
            <td>'.$i["montant"].'</td>
        </tr>';
        }

        $html .= '
        <tr>
            <td colspan="3"><strong>Total IRSA</strong></td>
            <td>'.$data["somme_irsa"].'</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Total retenues</strong></td>
            <td>'.$data["somme_retenus"].'</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Net à payer</strong></td>
            <td>'.$data["net_a_payer"].'</td>
        </tr>
        </table>

        <br>

        <p><strong>Montant imposable :</strong> '.$data["montant_imposable"].'</p>
        ';

        // 3. Écrire le HTML dans le PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        $dm = new DateModel((new \DateTime())->format("H:i:s"));
        $date = $dm->convertirEnSecondes();
        // 4. Output du PDF
        $pdf->Output('fiche_paie.pdf'.$data["employe"]["prenom"].$date, 'I');  // I = afficher, D = télécharger

        }

}