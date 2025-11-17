<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facture->Type ?: 'FACTURE' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #fff; font-size: 12px; }
        .a4 { width: 210mm; min-height: 297mm; margin: auto; background: #fff; padding: 0 18mm 0 10mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; min-height: 297mm; }
        .a5 { width: 148mm; min-height: 210mm; margin: auto; background: #fff; padding: 0 10mm 0 5mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; min-height: 210mm; }
        .facture-title { text-align: center; font-size: 22px; font-weight: bold; margin-top: 10px; margin-bottom: 28px; letter-spacing: 2px; }
        .a5 .facture-title { font-size: 18px; margin-bottom: 20px; }
        .bloc-patient { margin: 0 0 10px 0; }
        .bloc-patient-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .bloc-patient-table td { padding: 2px 8px; font-size: 12px; }
        .a5 .bloc-patient-table td { font-size: 10px; padding: 1px 4px; }
        .bloc-patient-table .label { font-weight: bold; color: #222; width: 80px; }
        .bloc-patient-table .value { color: #222; }
        .bloc-patient-table .ref-cell { text-align: right; padding: 2px 4px; }
        .bloc-patient-table .ref-label { font-weight: bold; padding-right: 3px; display: inline; }
        .bloc-patient-table .ref-value { display: inline; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .details-table th, .details-table td { border: 1px solid #222; font-size: 12px; padding: 6px 8px; }
        .a5 .details-table th, .a5 .details-table td { font-size: 10px; padding: 4px 6px; }
        .details-table th { background: #f4f6fa; text-align: center; }
        .details-table td { text-align: center; }
        .details-table th:first-child, .details-table td:first-child { text-align: left; }
        .details-table th:last-child, .details-table td:last-child { width: 40%; text-align: right; }
        .details-table tfoot tr { background-color: #f4f6fa; font-weight: bold; }
        .details-table tfoot td { border: 1px solid #222; }
        .totaux-table { width: 40%; border-collapse: collapse; margin-top: 0; margin-bottom: 0; margin-left: auto; }
        .totaux-table td { border: 1px solid #222; font-size: 12px; padding: 6px 8px; text-align: right; }
        .a5 .totaux-table td { font-size: 10px; padding: 4px 6px; }
        .montant-lettres { margin-top: 18px; font-size: 12px; clear: both; text-align: left; }
        .a5 .montant-lettres { font-size: 10px; margin-top: 12px; }
        .recu-header, .recu-footer { width: 100%; text-align: center; }
        .recu-header img, .recu-footer img { max-width: 100%; height: auto; }
        .recu-footer { position: absolute; bottom: 0; left: 0; width: 100%; }
        
        /* En-tête et pied de page pour pagination - masqués à l'écran */
        .print-header-fixed, .print-footer-fixed {
            display: none;
        }
        
        /* Styles pour l'impression avec pagination */
        @media print {
            .a4, .a5 { box-shadow: none; }
            .print-controls { display: none !important; }
            
            /* Définir les marges pour la première page (sans en-tête/pied fixe) */
            @page:first {
                margin: 0;
            }
            
            /* Définir les marges pour les pages suivantes (avec en-tête/pied fixe) */
            @page {
                margin-top: 60mm; /* Espace pour l'en-tête fixe */
                margin-bottom: 25mm; /* Espace pour le pied de page fixe */
            }
            
            /* En-tête fixe - masqué sur la première page, visible sur les pages suivantes */
            .print-header-fixed {
                display: none; /* Masqué par défaut (première page) */
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                width: 100%;
                background: #fff;
                z-index: 1000;
                padding: 5mm 18mm 5mm 10mm;
                border-bottom: 1px solid #ddd;
            }
            .a5 .print-header-fixed {
                padding: 3mm 10mm 3mm 5mm;
            }
            
            /* Afficher l'en-tête fixe seulement sur les pages suivantes */
            /* La classe sera ajoutée par JavaScript si le contenu dépasse une page */
            body.has-multiple-pages .print-header-fixed {
                display: block;
            }
            
            /* Pied de page fixe - masqué sur la première page, visible sur les pages suivantes */
            .print-footer-fixed {
                display: none; /* Masqué par défaut (première page) */
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100%;
                background: #fff;
                z-index: 1000;
                padding: 5mm 18mm;
                border-top: 1px solid #ddd;
                text-align: center;
            }
            .a5 .print-footer-fixed {
                padding: 3mm 10mm;
            }
            
            /* Afficher le pied de page fixe seulement sur les pages suivantes */
            body.has-multiple-pages .print-footer-fixed {
                display: block;
            }
            
            /* Ajouter le numéro de page au pied de page */
            .print-footer-fixed::after {
                content: "Page " counter(page);
                display: block;
                margin-top: 5px;
                font-size: 10px;
                color: #666;
            }
            
            /* Sur la première page, garder les en-têtes et pieds de page originaux visibles */
            /* Sur les pages suivantes, masquer les en-têtes et pieds de page originaux */
            /* On utilise une approche avec page-break pour détecter les pages suivantes */
            .recu-header, .recu-footer {
                display: block;
            }
            
            
            /* Ajuster les marges du contenu */
            .a4 {
                padding-top: 0;
                padding-bottom: 0;
            }
            .a5 {
                padding-top: 0;
                padding-bottom: 0;
            }
            
            /* Répéter les en-têtes de tableaux sur chaque page */
            .details-table thead {
                display: table-header-group;
            }
            .details-table tfoot {
                display: table-footer-group;
            }
            
            /* Éviter les coupures dans les éléments importants */
            .totaux-table, .montant-lettres, .signature-block {
                page-break-inside: avoid;
            }
            
            /* Permettre les sauts de page automatiques dans le contenu */
            .facture-content {
                page-break-inside: auto;
            }
            
            /* S'assurer que le contenu peut se répartir sur plusieurs pages */
            .a4, .a5 {
                page-break-after: auto;
            }
            
            /* Éviter les sauts de page dans les lignes de tableau individuelles */
            .details-table tr {
                page-break-inside: avoid;
            }
        }
        .print-controls { display: flex; gap: 10px; justify-content: flex-end; margin: 18px 0; }
        .print-controls select, .print-controls button { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .print-controls button { background: #2c5282; color: #fff; border: none; cursor: pointer; }
        .bloc-patient-table .praticien-value { padding-left: 2px !important; }
        .signature-block {
            margin-top: 40px;
            margin-bottom: 40px;
            text-align: right;
            padding-right: 20px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 25px;
        }
        .signature-name {
            font-style: italic;
        }
    </style>
</head>
<body>
<!-- En-tête fixe pour pagination (uniquement avec partials) -->
<div class="print-header-fixed">
    @include('partials.recu-header')
    <div class="facture-title" style="font-size: 18px; margin: 10px 0; text-align: center;">{{ $facture->Type ?: 'FACTURE' }}</div>
    <div class="bloc-patient" style="margin: 5px 0;">
        <table class="bloc-patient-table" style="font-size: 10px; width: 100%;">
            <tr>
                <td class="label">N° Fiche :</td>
                <td class="value">{{ $facture->patient->IdentifiantPatient ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2" style="text-align: right;">
                    <span class="ref-label">Réf :</span>
                    <span class="ref-value">{{ $facture->Nfacture ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Nom Patient :</td>
                <td class="value">{{ $facture->patient->NomContact ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2" style="text-align: right;">
                    <span class="ref-label">Date :</span>
                    <span class="ref-value">{{ $facture->DtFacture ? $facture->DtFacture->format('d/m/Y H:i') : 'N/A' }}</span>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Pied de page fixe pour pagination (uniquement avec partial) -->
<div class="print-footer-fixed">
    @include('partials.recu-footer')
</div>

<div class="a4" id="documentContainer">
    <div class="print-controls">
        <select id="documentType" onchange="updateDocumentType()">
            <option value="Facture" {{ $facture->Type === 'Facture' ? 'selected' : '' }}>Facture</option>
            <option value="Devis" {{ $facture->Type === 'Devis' ? 'selected' : '' }}>Devis</option>
        </select>
        <select id="pageFormat" onchange="updatePageFormat()">
            <option value="A4">Format A4</option>
            <option value="A5">Format A5</option>
        </select>
        <button onclick="window.print()" class="print-btn">
            Imprimer
        </button>
    </div>
    <div class="recu-header">@include('partials.recu-header')</div>
    <div class="facture-title" id="documentTitle">{{ $facture->Type ?: 'FACTURE' }}</div>
    <div class="bloc-patient">
        <table class="bloc-patient-table">
            <tr>
                <td class="label">N° Fiche :</td>
                <td class="value">{{ $facture->patient->IdentifiantPatient ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Réf :</span>
                    <span class="ref-value">{{ $facture->Nfacture ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Nom Patient :</td>
                <td class="value">{{ $facture->patient->NomContact ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Date :</span>
                    <span class="ref-value">{{ $facture->DtFacture ? $facture->DtFacture->format('d/m/Y H:i') : 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Téléphone :</td>
                <td class="value">{{ $facture->patient->Telephone1 ?? 'N/A' }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label">Praticien :</td>
                <td class="value">{{ $facture->medecin->Nom ?? '' }}</td>
                <td colspan="2"></td>
            </tr>
            @if($facture->patient && $facture->patient->assureur)
            <tr>
                <td class="label">Assureur :</td>
                <td class="value">
                    {{ $facture->patient->assureur->LibAssurance ?? 'N/A' }}
                    @if($facture->patient->IdentifiantAssurance)
                        ({{ $facture->patient->IdentifiantAssurance }})
                    @endif
                </td>
                <td colspan="2"></td>
            </tr>
            @endif
        </table>
    </div>
    @php
        $detailsGroupes = $facture->getDetailsGroupesParType();
        
        // Recalculer le total réel à partir des détails
        // Inclut TOUS les types : IsAct=1 (Actes), IsAct=2 (Médicaments), IsAct=3 (Analyses), IsAct=4 (Radios)
        $totalReel = $facture->details->sum(function($detail) {
            $prix = floatval($detail->PrixFacture ?? 0);
            $quantite = floatval($detail->Quantite ?? 0);
            return $prix * $quantite;
        });
        
        // Recalculer TotalPEC et TotalfactPatient si ISTP == 1
        $txpec = $facture->TXPEC ?? 0;
        $totalPECReel = $facture->ISTP == 1 ? ($totalReel * $txpec) : 0;
        $totalfactPatientReel = $facture->ISTP == 1 ? ($totalReel - $totalPECReel) : $totalReel;
        
        // Utiliser les totaux réels calculés ou ceux de la base de données
        $totalFacture = $totalReel > 0 ? $totalReel : ($facture->TotFacture ?? 0);
        $totalPEC = $totalPECReel > 0 ? $totalPECReel : ($facture->TotalPEC ?? 0);
        $totalPatient = $totalfactPatientReel > 0 ? $totalfactPatientReel : ($facture->TotalfactPatient ?? 0);
        
        // Recalculer le reste à payer
        $restePatient = $facture->ISTP == 1 
            ? ($totalPatient - ($facture->TotReglPatient ?? 0))
            : ($totalFacture - ($facture->TotReglPatient ?? 0));
    @endphp
    
    @if(count($detailsGroupes) > 1)
        {{-- Affichage par sections si plusieurs types --}}
        @foreach($detailsGroupes as $section => $details)
            <div class="section-header" style="margin-top: 15px; margin-bottom: 10px; font-weight: bold; font-size: 14px; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px;">
                {{ $section }}
            </div>
            <table class="details-table" style="margin-bottom: 20px;">
                <thead>
                <tr>
                    <th>Traitement</th>
                    <th>Qté</th>
                    <th>P.U</th>
                    <th>Sous Total (MRU)</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $sousTotalSection = 0;
                @endphp
                @foreach($details as $detail)
                    @php
                        $sousTotalLigne = $detail->PrixFacture * $detail->Quantite;
                        $sousTotalSection += $sousTotalLigne;
                    @endphp
                    <tr>
                        <td>{{ $detail->Actes }}</td>
                        <td>{{ $detail->Quantite }}</td>
                        <td>{{ number_format($detail->PrixFacture, 2) }}</td>
                        <td>{{ number_format($sousTotalLigne, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr style="background-color: #f4f6fa; font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding-right: 15px;">Sous-total {{ $section }} :</td>
                    <td style="text-align: right;">{{ number_format($sousTotalSection, 2) }} MRU</td>
                </tr>
                </tfoot>
            </table>
        @endforeach
    @else
        {{-- Affichage simple si un seul type ou pas de type --}}
        <table class="details-table">
            <thead>
            <tr>
                <th>Traitement</th>
                <th>Qté</th>
                <th>P.U</th>
                <th>Sous Total (MRU)</th>
            </tr>
            </thead>
            <tbody>
            @foreach($facture->details as $detail)
                <tr>
                    <td>{{ $detail->Actes }}</td>
                    <td>{{ $detail->Quantite }}</td>
                    <td>{{ number_format($detail->PrixFacture, 2) }}</td>
                    <td>{{ number_format($detail->PrixFacture * $detail->Quantite, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
    <table class="totaux-table" id="totauxTable">
        <tr>
            <td>Total {{ strtolower($facture->Type ?: 'facture') }}</td>
            <td>{{ number_format($totalFacture, 2) }} MRU</td>
        </tr>
        <tbody id="detailsFacture" style="display: {{ $facture->Type === 'Facture' ? 'table-row-group' : 'none' }}">
            @if($facture->ISTP == 1)
                <tr>
                    <td>Part assurance</td>
                    <td>{{ number_format($totalPEC, 2) }} MRU</td>
                </tr>
                <tr>
                    <td>Part patient</td>
                    <td>{{ number_format($totalPatient, 2) }} MRU</td>
                </tr>
            @endif
            <tr>
                <td>Total règlements</td>
                <td>{{ number_format($facture->TotReglPatient ?? 0, 2) }} MRU</td>
            </tr>
            <tr>
                <td>Reste à payer</td>
                <td>{{ number_format($restePatient, 2) }} MRU</td>
            </tr>
        </tbody>
    </table>
    <div class="montant-lettres">
        Arrêté le présent {{ strtolower($facture->Type ?: 'facture') }} à la somme de : <strong>{{ $facture->en_lettres ?? '' }}</strong>
    </div>

    <div class="signature-block">
        <div class="signature-title">Signature</div>
        <div class="signature-name">{{ $facture->medecin->Nom ?? 'Non spécifié' }}</div>
    </div>

    <div class="recu-footer">@include('partials.recu-footer')</div>
</div>

<script>
// Cache des éléments DOM fréquemment utilisés
const elements = {
    documentType: document.getElementById('documentType'),
    documentTitle: document.getElementById('documentTitle'),
    montantLettres: document.querySelector('.montant-lettres'),
    totalLabel: document.querySelector('.totaux-table tr:first-child td:first-child'),
    detailsFacture: document.getElementById('detailsFacture'),
    pageFormat: document.getElementById('pageFormat'),
    container: document.getElementById('documentContainer')
};

function updateDocumentType() {
    const isDevis = elements.documentType.value === 'Devis';
    
    elements.documentTitle.textContent = isDevis ? 'DEVIS' : 'FACTURE';
    elements.montantLettres.innerHTML = elements.montantLettres.innerHTML.replace(
        isDevis ? 'facture' : 'devis',
        isDevis ? 'devis' : 'facture'
    );
    elements.totalLabel.textContent = `Total ${isDevis ? 'devis' : 'facture'}`;
    elements.detailsFacture.style.display = isDevis ? 'none' : 'table-row-group';
}

function updatePageFormat() {
    const isA5 = elements.pageFormat.value === 'A5';
    elements.container.classList.toggle('a4', !isA5);
    elements.container.classList.toggle('a5', isA5);
}

// Gestion de la pagination avec compteur CSS
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            @page {
                counter-increment: page;
            }
            @page:first {
                counter-reset: page 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Détecter si le contenu dépasse une page et afficher les éléments fixes
    function checkMultiplePages() {
        const container = document.querySelector('.a4, .a5');
        if (container) {
            // Calculer la hauteur du contenu
            const contentHeight = container.scrollHeight;
            // Hauteur d'une page A4 en pixels (297mm à 96 DPI ≈ 1123px)
            // Hauteur d'une page A5 en pixels (210mm à 96 DPI ≈ 794px)
            const isA5 = container.classList.contains('a5');
            const pageHeight = isA5 ? 794 : 1123;
            
            // Si le contenu dépasse une page, ajouter la classe
            if (contentHeight > pageHeight) {
                document.body.classList.add('has-multiple-pages');
            } else {
                document.body.classList.remove('has-multiple-pages');
            }
        }
    }
    
    // Vérifier au chargement
    checkMultiplePages();
    
    // Vérifier avant l'impression
    window.addEventListener('beforeprint', checkMultiplePages);
    
    // Vérifier après le chargement complet
    window.addEventListener('load', checkMultiplePages);
    
    // Vérifier lors des changements de format
    const pageFormatSelect = document.getElementById('pageFormat');
    if (pageFormatSelect) {
        pageFormatSelect.addEventListener('change', function() {
            setTimeout(checkMultiplePages, 100);
        });
    }
});
</script>
</body>
</html>

