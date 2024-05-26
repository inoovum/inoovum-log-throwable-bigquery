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
        $this->writeLogEntry($errorInfo, $options['projectId'], $options['datasetId'], $options['tableId'], $options['keyFile']);
    }

    /**
     * @param string $errorInfo
     * @param string $projectId
     * @param string $datasetId
     * @param string $tableId
     * @param string $keyFile
     * @return void
     */
    private function writeLogEntry(string $errorInfo, string $projectId, string $datasetId, string $tableId, string $keyFile): void
    {
        $bigQuery = new BigQueryClient([
            'projectId' => $projectId,
            'keyFile' => json_decode(base64_decode($keyFile), true)
        ]);
        $dataset = $bigQuery->dataset($datasetId);

        $table = $dataset->table($tableId);
        if (!$table->exists()) {
            $schema = [
                'fields' => [
                    ['name' => 'exception', 'type' => 'STRING'],
                    ['name' => 'tstamp', 'type' => 'DATETIME'],
                ],
            ];
            $table = $dataset->createTable($tableId, ['schema' => $schema]);
        } else {
            $table = $dataset->table($tableId);
        }

        $data = [
            'exception' => $errorInfo,
            'tstamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        $table->insertRow($data);
    }

}
