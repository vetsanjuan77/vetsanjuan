<?php

namespace App\Services\Dian\Ubl;

use App\Models\Factura;

class UblInvoiceBuilder
{
    public function build(Factura $factura): string
    {
        $factura->loadMissing(['cliente', 'detalles.producto', 'detalles.presentacion']);

        $supplierNit = (string) (config('dian.direct.nit') ?? '000000000');
        $issueDate = optional($factura->fecha_emision)->format('Y-m-d') ?? date('Y-m-d');
        $currency = 'COP';
        $docId = $factura->numero_factura ?: ('FAC-' . $factura->id);

        $linesXml = '';
        foreach ($factura->detalles as $idx => $det) {
            $lineId = $idx + 1;
            $quantity = (float) $det->cantidad;
            $unitPrice = (float) $det->precio_unitario;
            $lineTotal = max(0, $quantity * $unitPrice - (float) ($det->descuento ?? 0));
            $description = trim((optional($det->presentacion)->nombre ?: optional($det->producto)->nombre) ?? 'Item');

            $linesXml .= "\n        <cac:InvoiceLine>\n" .
                "            <cbc:ID>{$lineId}</cbc:ID>\n" .
                "            <cbc:InvoicedQuantity unitCode=\"NIU\">{$quantity}</cbc:InvoicedQuantity>\n" .
                "            <cbc:LineExtensionAmount currencyID=\"{$currency}\">" . number_format($lineTotal, 2, '.', '') . "</cbc:LineExtensionAmount>\n" .
                "            <cac:Item><cbc:Description>" . htmlspecialchars($description) . "</cbc:Description></cac:Item>\n" .
                "            <cac:Price><cbc:PriceAmount currencyID=\"{$currency}\">" . number_format($unitPrice, 2, '.', '') . "</cbc:PriceAmount></cac:Price>\n" .
            "        </cac:InvoiceLine>\n";
        }

        $total = (float) ($factura->total ?? 0);
        $subtotal = (float) ($factura->subtotal ?? 0);
        $taxes = (float) ($factura->impuestos ?? 0);
        $discount = (float) ($factura->descuento ?? 0);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>\n'
            . '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" '
            . 'xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" '
            . 'xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">\n'
            . "    <cbc:CustomizationID>UBL 2.1 DIAN</cbc:CustomizationID>\n"
            . "    <cbc:ProfileID>DIAN 2.1</cbc:ProfileID>\n"
            . "    <cbc:ID>" . htmlspecialchars($docId) . "</cbc:ID>\n"
            . "    <cbc:IssueDate>{$issueDate}</cbc:IssueDate>\n"
            . "    <cbc:InvoiceTypeCode>01</cbc:InvoiceTypeCode>\n"
            . "    <cbc:DocumentCurrencyCode>{$currency}</cbc:DocumentCurrencyCode>\n"
            . "    <cac:AccountingSupplierParty>\n"
            . "        <cac:Party>\n"
            . "            <cac:PartyTaxScheme><cbc:CompanyID>" . htmlspecialchars($supplierNit) . "</cbc:CompanyID></cac:PartyTaxScheme>\n"
            . "        </cac:Party>\n"
            . "    </cac:AccountingSupplierParty>\n"
            . "    <cac:AccountingCustomerParty>\n"
            . "        <cac:Party>\n"
            . "            <cac:PartyTaxScheme><cbc:CompanyID>" . htmlspecialchars((string) optional($factura->cliente)->getAttribute('documento') ?: (string) optional($factura->cliente)->id) . "</cbc:CompanyID></cac:PartyTaxScheme>\n"
            . "        </cac:Party>\n"
            . "    </cac:AccountingCustomerParty>\n"
            . "    <cac:LegalMonetaryTotal>\n"
            . "        <cbc:LineExtensionAmount currencyID=\"{$currency}\">" . number_format($subtotal, 2, '.', '') . "</cbc:LineExtensionAmount>\n"
            . "        <cbc:TaxExclusiveAmount currencyID=\"{$currency}\">" . number_format($subtotal - $taxes, 2, '.', '') . "</cbc:TaxExclusiveAmount>\n"
            . "        <cbc:TaxInclusiveAmount currencyID=\"{$currency}\">" . number_format($subtotal, 2, '.', '') . "</cbc:TaxInclusiveAmount>\n"
            . "        <cbc:PayableAmount currencyID=\"{$currency}\">" . number_format($total, 2, '.', '') . "</cbc:PayableAmount>\n"
            . "    </cac:LegalMonetaryTotal>\n"
            . $linesXml .
            "</Invoice>\n";

        return $xml;
    }
}