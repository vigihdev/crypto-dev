### Untuk Method \_\_construct:

```php
class ConnectionRepository {

    public function __construct(
        private string $host,
        private int $port,
        private string $username,
        private int $timeout = 10,
        private string|null $password = null
    ) {}
}
```
