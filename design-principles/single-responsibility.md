# Single Responsibility Principle

## Sample
```php
class User
{
    public function create(array $data)
    {
        try {
            // save user to database
        } catch (DatabaseException $e) {
            $this->logError($e->getMessage());
        }

    }

    public function logError($message)
    {
        // write error to file
    }
}
```


```php
class Logger
{
    public function writeToFile($message)
    {
        // write to the file
    }
}

class User
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function create(array $data)
    {
        try {
            // save user to database
        } catch (DatabaseException $e) {
            $this->logger->writeToFile($e->getMessage());
        }

    }
}
```