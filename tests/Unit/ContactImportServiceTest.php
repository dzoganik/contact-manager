<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Contact;
use App\Services\ContactImportService;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContactImportServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public static function contactsProvider(): array
    {
        return [
            'small batch' => [
                'contacts' => [
                    ['name' => 'A', 'surname' => 'AA', 'email' => 'a@test.com'],
                    ['name' => 'B', 'surname' => 'BB', 'email' => 'b@test.com'],
                    ['name' => 'C', 'surname' => 'CC', 'email' => 'c@test.com'],
                ],
                'batchSize' => 2,
                'expectedInsertCalls' => 2,
            ],
            'single batch' => [
                'contacts' => [
                    ['name' => 'X', 'surname' => 'XX', 'email' => 'x@test.com'],
                    ['name' => 'Y', 'surname' => 'YY', 'email' => 'y@test.com'],
                ],
                'batchSize' => 5,
                'expectedInsertCalls' => 1,
            ],
            'exact batch' => [
                'contacts' => [
                    ['name' => 'L', 'surname' => 'LL', 'email' => 'l@test.com'],
                    ['name' => 'M', 'surname' => 'MM', 'email' => 'm@test.com'],
                    ['name' => 'N', 'surname' => 'NN', 'email' => 'n@test.com'],
                    ['name' => 'O', 'surname' => 'OO', 'email' => 'o@test.com'],
                ],
                'batchSize' => 2,
                'expectedInsertCalls' => 2,
            ],
        ];
    }

    #[DataProvider('contactsProvider')]
    public function test_import_inserts_batches_correctly(
        array $contacts,
        int $batchSize,
        int $expectedInsertCalls
    ) {
        $mock = Mockery::mock('alias:'.Contact::class);
        $mock->shouldReceive('insert')
            ->times($expectedInsertCalls)
            ->withArgs(fn (array $batch) => count($batch) <= $batchSize);

        $service = new ContactImportService(batchSize: $batchSize);

        $service->import($contacts);

        $this->assertTrue(true);
    }
}
