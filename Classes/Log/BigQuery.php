<?php
namespace Inoovum\Log\Throwable\BigQuery\Log;

use Neos\Flow\Annotations as Flow;
use Google\Cloud\BigQuery\BigQueryClient;
use Inoovum\Log\Throwable\Log\ThrowableInterface;

class BigQuery implements ThrowableInterface
{

    /**
     * @param string $errorInfo
     * @param array $options
     * @return void
     */
    public function throwError(string $errorInfo, array $options): void
    {
        $this->writeLogEntry($errorInfo, $options['projectId'], $options['datasetId'], $options['tableId'], $options['keyFile'], $options['referenceCode']);
    }

    /**
     * @param string $errorInfo
     * @param string $projectId
     * @param string $datasetId
     * @param string $tableId
     * @param string $keyFile
     * @param string $referenceCode
     * @return void
     */
    private function writeLogEntry(string $errorInfo, string $projectId, string $datasetId, string $tableId, string $keyFile, string $referenceCode): void
    {
        $bigQuery = new BigQueryClient([
            'projectId' => $projectId,
            'keyFile' => json_decode(base64_decode($keyFile), true)
        ]);
        $dataset = $bigQuery->dataset($datasetId);

        $table = $dataset->table($tableId);

        $schema = [
            'fields' => [
                ['name' => 'referenceCode', 'type' => 'STRING'],
                ['name' => 'exception', 'type' => 'STRING'],
                ['name' => 'tstamp', 'type' => 'DATETIME'],
            ],
        ];

        if (!$table->exists()) {
            $table = $dataset->createTable($tableId, ['schema' => $schema]);
        } else {
            $table = $dataset->table($tableId);
        }

        $data = [
            'referenceCode' => $referenceCode,
            'exception' => $errorInfo,
            'tstamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        $table->insertRow($data);
    }

}
