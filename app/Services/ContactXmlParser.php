<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\FileOpenException;
use App\Exceptions\IncompleteContactDataException;
use App\Exceptions\MalformedXmlException;
use Exception;
use Generator;
use SimpleXMLElement;
use XMLReader;

class ContactXmlParser
{
    /**
     * @return Generator<mixed, array{email: string, name: string, surname: string>}
     *
     * @throws \App\Exceptions\FileOpenException
     * @throws \App\Exceptions\MalformedXmlException
     * @throws \App\Exceptions\IncompleteContactDataException
     */
    public function parse(string $filePath): Generator
    {
        if (! file_exists($filePath)) {
            throw new FileOpenException("XML file does not exist at path: {$filePath}");
        }

        $reader = new XMLReader;

        if (! $reader->open($filePath)) {
            throw new FileOpenException("Failed to open XML file at path: {$filePath}");
        }

        libxml_use_internal_errors(true);

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'item') {
                try {
                    $node = new SimpleXMLElement($reader->readOuterXML());
                } catch (Exception $e) {
                    throw new MalformedXmlException('Malformed XML node encountered during parsing: '.$e->getMessage());
                }

                $firstName = (string) $node->first_name;
                $lastName = (string) $node->last_name;
                $email = (string) $node->email;

                if (empty($firstName) || empty($lastName) || empty($email)) {
                    throw new IncompleteContactDataException('Incomplete contact data found in the XML file.');
                }

                yield [
                    'name' => $firstName,
                    'surname' => $lastName,
                    'email' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $reader->close();
    }
}
