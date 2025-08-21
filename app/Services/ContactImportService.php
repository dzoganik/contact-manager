<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;

class ContactImportService
{
    public function __construct(private int $batchSize = 500) {}

    /**
     * @param  iterable<array{name:string,surname:string,email:string}>  $contacts
     */
    public function import(iterable $contacts): void
    {
        $batch = [];

        foreach ($contacts as $contact) {
            $batch[] = $contact;

            if (count($batch) >= $this->batchSize) {
                Contact::insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            Contact::insert($batch);
        }
    }
}
