<?php

namespace App\Lib;

use League\Csv\Writer;
use SplTempFileObject;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Csv\AbstractCsv as LeagueCsvWriter;

class ExportCSV
{
    /**
     * The applied callback.
     *
     * @var callable|null
     */
    protected $beforeEachCallback;

    /**
     * The CSV writer.
     *
     * @var \League\Csv\Writer
     */
    protected $csv;

    /**
     * Export constructor.
     *
     * @param \League\Csv\AbstractCsv|null $writer
     * @return void
     */
    public function __construct(LeagueCsvWriter $writer = null)
    {
        $this->csv = $writer ?: Writer::createFromFileObject(new SplTempFileObject);
    }

    /**
     * Build the writer.
     *
     * @param \Illuminate\Support\Collection $collection
     * @param array $fields
     * @return $this
     */
    public function build(Collection $collection, array $fields, $outputHeaders = true)
    {
        $csv = $this->csv;
        $headers = [];

        foreach ($fields as $key => $field) {
            $headers[] = $field;

            if (! is_numeric($key)) {
                $fields[$key] = $key;
            }
        }

        // Add first line, the header
        if ($outputHeaders == true)
            $csv->insertOne($headers);

        $this->addCsvRows($collection, $fields, $csv);

        return $this;
    }

    /**
     * Download the CSV file.
     *
     * @param string|null $filename
     * @return void
     */
    public function download($filename = null)
    {
        $filename = $filename ?: date('Y-m-d_His') . '.csv';
        $this->csv->output($filename);
    }

    /**
     * Set the callback.
     *
     * @param callable $callback
     * @return $this
     */
    public function beforeEach(callable $callback)
    {
        $this->beforeEachCallback = $callback;
        return $this;
    }

    /**
     * Get the CSV writer.
     *
     * @return \League\Csv\Writer
     */
    public function getCsv()
    {
        return $this->csv;
    }

    /**
     * Add rows to the CSV.
     *
     * @param \Illuminate\Support\Collection $collection
     * @param array $fields
     * @param \League\Csv\Writer $csv
     * @return void
     */
    private function addCsvRows(Collection $collection, array $fields, Writer $csv)
    {
        $isEloquentCollection = false;
        if(is_a($collection,\Illuminate\Database\Eloquent\Collection::class)) {
            $collection->makeVisible($fields);
            $isEloquentCollection = true;
        }

        foreach ($collection as $model) {
            $beforeEachCallback = $this->beforeEachCallback;

            // Call hook
            if ($beforeEachCallback) {
                $return = $beforeEachCallback($model);
                if ($return === false) {
                    continue;
                }
            }

            if($isEloquentCollection) {
                $model->toArray();
            } else {
                $model = collect($model);
            }

            $csvRow = [];
            foreach ($fields as $field) {
                $csvRow[] = Arr::get($model, $field);
            }

            $csv->insertOne($csvRow);
        }
    }
}