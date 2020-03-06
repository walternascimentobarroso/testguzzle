<?php

namespace App;

require '../vendor/autoload.php';

main();

function main()
{
    $html = getPage("http://www.guiatrabalhista.com.br/guia/salario_minimo.htm");
    $data = getData($html);
    if(isset($_GET["format"]) && htmlspecialchars($_GET["format"]) == 'json') {
        header('Content-Type: application/json');
        echo json_encode($data);
    }   else {
        echo('<pre>');
        print_r($data);
    } 
}

function getData($html)
{
    $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
    $data = $crawler->filter('div#content table tbody tr')->each(function ($contentContainer, $index) {
        $VIGENCIA = $contentContainer->filter('td:nth-child(1)')->text();
        $VALOR_MENSAL = $contentContainer->filter('td:nth-child(2)')->text();
        $VALOR_DIARIO = $contentContainer->filter('td:nth-child(3)')->text();
        $VALOR_HORA = $contentContainer->filter('td:nth-child(4)')->text();
        $NORMA_LEGAL = $contentContainer->filter('td:nth-child(5)')->text();
        $DOU = $contentContainer->filter('td:nth-child(6)')->text();

        return [
            "vigencia" => $VIGENCIA,
            "valor_mensal" => $VALOR_MENSAL,
            "valor_diario" => $VALOR_DIARIO,
            "valor_hora" => $VALOR_HORA,
            "norma_legal" => $NORMA_LEGAL,
            "dou" => $DOU,
        ];
    });
    array_splice($data, 0, 1);
    return $data;
}

function getPage($URL)
{
    $client = new \GuzzleHttp\Client(
        [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0',
            ],
        ]
    );
    $response = $client->get($URL);
    return $response->getBody()->getContents();
}
