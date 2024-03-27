# inoovum® throwable log for google bigquery

This package extends the throwable log.

### Installation

Just run:
`composer require inoovum/log-throwable-bigquery`

### Configuration

```yaml
Inoovum:
  Log:
    Throwable:
      classes:
        -
          class: 'Inoovum\Log\Throwable\BigQuery\Log\BigQuery'
          options:
            projectId: '<your-google-cloud-console-project>'
            datasetId: '<your-bigquery-dataset-id>'
            tableId: '<your-bigquery-table-id>'
            keyFile: '<your-key-file-base64-encoded>'
```

## Author

* E-Mail: patric.eckhart@steinbauer-it.com
* URL: http://www.steinbauer-it.com
