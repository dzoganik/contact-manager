<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\FileOpenException;
use App\Exceptions\IncompleteContactDataException;
use App\Exceptions\MalformedXmlException;
use App\Services\ContactXmlParser;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ContactXmlParserTest extends TestCase
{
    private string $tempFile;

    private ContactXmlParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'xml');
        $this->parser = new ContactXmlParser();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        parent::tearDown();
    }

    public static function validXmlProvider(): array
    {
        return [
            'single contact' => [
                'xmlContent' => '<?xml version="1.0" encoding="UTF-8"?>
                <data>
                    <item>
                        <email>gtillman@schaefer.info</email>
                        <first_name>Henri</first_name>
                        <last_name>Schinner</last_name>
                    </item>
                </data>',
                'expectedContacts' => [
                    ['name' => 'Henri', 'surname' => 'Schinner', 'email' => 'gtillman@schaefer.info'],
                ],
            ],
            'multiple contacts' => [
                'xmlContent' => '<?xml version="1.0" encoding="UTF-8"?>
                <data>
                    <item>
                        <first_name>Alice</first_name>
                        <last_name>Smith</last_name>
                        <email>alice@test.com</email>
                    </item>
                    <item>
                        <first_name>Bob</first_name>
                        <last_name>Brown</last_name>
                        <email>bob@test.com</email>
                    </item>
                </data>',
                'expectedContacts' => [
                    ['name' => 'Alice', 'surname' => 'Smith', 'email' => 'alice@test.com'],
                    ['name' => 'Bob', 'surname' => 'Brown', 'email' => 'bob@test.com'],
                ],
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validXmlProvider')]
    public function test_parse_yields_correct_data_with_timestamps(
        string $xmlContent,
        array $expectedContacts
    ): void {
        file_put_contents($this->tempFile, $xmlContent);
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 12, 0, 0));

        $result = iterator_to_array($this->parser->parse($this->tempFile));

        foreach ($result as $index => $contactData) {
            $this->assertSame($expectedContacts[$index]['name'], $contactData['name']);
            $this->assertSame($expectedContacts[$index]['surname'], $contactData['surname']);
            $this->assertSame($expectedContacts[$index]['email'], $contactData['email']);
            
            $this->assertArrayHasKey('created_at', $contactData);
            $this->assertArrayHasKey('updated_at', $contactData);
            $this->assertEquals(Carbon::getTestNow(), $contactData['created_at']);
            $this->assertEquals(Carbon::getTestNow(), $contactData['updated_at']);
        }

        Carbon::setTestNow();
    }

    public function test_parse_throws_on_malformed_xml(): void
    {
        $xml = '<?xml version="1.0"?>
            <data>
                <item>
                    <first_name>John</first_name>
                    <last_name>Doe</last_name>
                    <email>john@example.com</email>
                </item>';
        file_put_contents($this->tempFile, $xml);

        $this->expectException(MalformedXmlException::class);
        iterator_to_array($this->parser->parse($this->tempFile));
    }

    public function test_parse_throws_on_incomplete_data(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <data>
                <item>
                    <email>gtillman@schaefer.info</email>
                    <first_name>Henri</first_name>
                </item>
            </data>';
        file_put_contents($this->tempFile, $xml);

        $this->expectException(IncompleteContactDataException::class);
        iterator_to_array($this->parser->parse($this->tempFile));
    }

    public function test_parse_throws_if_file_cannot_be_opened(): void
    {
        $invalidPath = '/non/existent/file.xml';
        
        $this->expectException(FileOpenException::class);
        iterator_to_array($this->parser->parse($invalidPath));
    }
}
