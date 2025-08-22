<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\ContactImportService;
use App\Services\ContactXmlParser;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImportContactsCommand extends Command
{
    private const IMPORT_DIRECTORY = 'imports';

    /**
     * The signature of the console command.
     */
    protected $signature = 'import:contacts {filename}';

    /**
     * The console command description.
     */
    protected $description;

    public function __construct()
    {
        parent::__construct();

        $this->description = "Imports contacts from an XML file located in 'storage/app/".self::IMPORT_DIRECTORY."'";
    }

    /**
     * Execute the console command.
     */
    public function handle(
        ContactXmlParser $parser,
        ContactImportService $importer
    ): int {
        $filename = $this->argument('filename');
        $absoluteFilePath = $this->getAndValidateFilePath($filename);

        if (! $absoluteFilePath) {
            return self::FAILURE;
        }

        if (! $this->importToDatabase($parser, $importer, $absoluteFilePath)) {
            return self::FAILURE;
        }

        if (! $this->syncWithSearchEngine()) {
            return self::FAILURE;
        }

        $this->info('Full import process completed successfully.');

        return self::SUCCESS;
    }

    private function importToDatabase(
        ContactXmlParser $parser,
        ContactImportService $importer,
        string $filePath
    ): bool {
        try {
            $this->info("Importing contacts into the database from: {$filePath}");

            DB::transaction(function () use ($parser, $importer, $filePath) {
                $contactsGenerator = $parser->parse($filePath);
                $importer->import($contactsGenerator);
            });

            $this->info('Database import finished successfully.');

            return true;
        } catch (Throwable $e) {
            $this->error('CRITICAL: Database import failed: '.$e->getMessage());
            $this->comment('No data has been saved to the database. The operation was rolled back.');

            return false;
        }
    }

    private function syncWithSearchEngine(): bool
    {
        try {
            $this->info('Synchronizing all contacts with the search engine...');
            $this->comment('This may take a few moments.');

            $exitCode = Artisan::call('scout:import', ['model' => Contact::class], $this->getOutput());

            if ($exitCode !== 0) {
                throw new Exception('The scout:import command failed.');
            }

            $this->info('Synchronization with search engine finished successfully.');

            return true;
        } catch (Throwable $e) {
            $this->error('ERROR: Search engine synchronization failed: '.$e->getMessage());
            $this->warn('The database contains the new contacts, but the search index is out of sync.');
            $this->warn("Please run 'php artisan scout:import \"App\Models\Contact\"' manually to fix it.");

            return false;
        }
    }

    private function getAndValidateFilePath(string $filename): ?string
    {
        $filePathInStorage = self::IMPORT_DIRECTORY."/{$filename}";
        $disk = Storage::disk('local');

        if (! $disk->exists($filePathInStorage)) {
            $this->error("File not found. Please make sure '{$filename}' exists in the /storage/app/".self::IMPORT_DIRECTORY.' directory.');

            return null;
        }

        return $disk->path($filePathInStorage);
    }
}
