<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SoapAuditService
{
    private string $baseUrl = 'https://iae-sso.virtualfri.id';
    private string $teamId = 'TEAM-04';

    public function sendAudit(array $transactionData, string $token): array
    {
        $logContent = json_encode($transactionData);

        $soapBody = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
  <soap:Body>
    <iae:AuditRequest>
      <iae:TeamID>{$this->teamId}</iae:TeamID>
      <iae:ActivityName>RepaymentProcessed</iae:ActivityName>
      <iae:LogContent><![CDATA[{$logContent}]]></iae:LogContent>
    </iae:AuditRequest>
  </soap:Body>
</soap:Envelope>
XML;

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'text/xml',
        ])->withBody($soapBody, 'text/xml')
          ->post("{$this->baseUrl}/soap/v1/audit");

        $xml = simplexml_load_string($response->body());
        $xml->registerXPathNamespace('iae', 'http://iae.central/audit');

        $status = (string) $xml->xpath('//iae:Status')[0];
        $receiptNumber = (string) $xml->xpath('//iae:ReceiptNumber')[0];

        return [
            'status' => $status,
            'receipt_number' => $receiptNumber,
        ];
    }
}