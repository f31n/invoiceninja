<?php

namespace Tests\Feature\PdfMaker;

use App\Models\Invoice;
use App\Services\PdfMaker\Designs\Playful;
use App\Services\PdfMaker\PdfMaker;
use App\Utils\HtmlEngine;
use App\Utils\Traits\MakesInvoiceValues;
use Tests\MockAccountData;
use Tests\TestCase;

class ExampleIntegrationTest extends TestCase
{
    use MakesInvoiceValues, MockAccountData;

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTestData();
    }

    public function testExample()
    {
        $invoice = $this->invoice;
        $invitation = $invoice->invitations()->first();

        $engine = new HtmlEngine(null, $invitation, 'invoice');
        $design = new Playful();

        $product_table_columns = json_decode(
            json_encode($invoice->company->settings->pdf_variables),
            1
        )['product_columns'];

        $state = [
            'template' => $design->elements([
                'client' => $invoice->client,
                'entity' => $invoice,
                'product-table-columns' => $product_table_columns,
            ]),
            'variables' => $engine->generateLabelsAndValues(),
        ];

        $maker = new PdfMaker($state);

        $maker
            ->design(Playful::class)
            ->build();

  //      exec('echo "" > storage/logs/laravel.log');

//        info($maker->getCompiledHTML(true));

        $this->assertTrue(true);
    }
}