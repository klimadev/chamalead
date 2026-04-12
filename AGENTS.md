# AGENTS.md - ChamaLead Development Guidelines

## Project Overview

ChamaLead is a PHP-based lead management system with SQLite database. The codebase includes an admin panel, public API, and Evolution API integration for WhatsApp automation.

## Build, Lint, and Test Commands

### PHP Code Style (PHP CS Fixer)

```bash
# Install PHP CS Fixer (if not installed)
composer global require friendsofphp/php-cs-fixer

# Run fixer in check mode (dry-run)
php-cs-fixer fix --dry-run --diff

# Apply fixes
php-cs-fixer fix

# Check specific file
php-cs-fixer fix --dry-run --diff panel/EvolutionApiService.php
```

### PHPUnit Tests

```bash
# Install PHPUnit (if not available)
composer require --dev phpunit/phpunit

# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit panel/tests/EvolutionApiServiceTest.php

# Run specific test method
./vendor/bin/phpunit --filter testFetchInstancesReturnsArray

# Run with coverage (if configured)
./vendor/bin/phpunit --coverage-html coverage/
```

### PHP Syntax Check

```bash
# Check syntax of all PHP files
find . -name "*.php" -exec php -l {} \;

# Check specific file
php -l panel/EvolutionApiService.php
```

---

## Code Style Guidelines

### General Principles

- **PSR-12** is the base coding standard
- Use **PHP 8.2+** features where appropriate
- Always enable strict types: `declare(strict_types=1);`
- Maximum line length: 120 characters (soft limit: 80)

### Formatting

- Use **4 spaces** for indentation (no tabs)
- Use **short array syntax**: `['key' => 'value']`
- Add trailing commas in multiline arrays/arguments
- One blank line between import groups
- Blank line after namespace declaration
- No trailing whitespace

### Imports

```php
// Order: internal → external → parent → current
use App\Services\InternalService;
use External\Library\Class;
use Panel\Config;
use function helper_function;
use const CONSTANT_NAME;
```

### Naming Conventions

- **Classes**: `PascalCase` (e.g., `EvolutionApiService`)
- **Methods/Properties**: `camelCase` (e.g., `fetchInstances`)
- **Constants**: `UPPER_SNAKE_CASE` (e.g., `MAX_RETRIES`)
- **Files**: Match class name (e.g., `EvolutionApiService.php`)
- **Private properties**: Prefix with underscore optional: `private string $_cachePath`

### Types

```php
// Use strict type declarations
private string $apiUrl;
private int $timeout;
private ?string $optionalParam = null;

// Return types required
public function fetchInstances(): array
private function getCache(string $key): ?array
```

### Error Handling

- Use `try-catch` for operations that may fail
- Return structured error responses: `['success' => false, 'error' => 'message']`
- Use exceptions for unexpected errors: `throw new RuntimeException('message')`
- Never expose raw error messages to users
- Log errors with contextual data: `error_log("[Service] Error: {$message}")`

### Security

- **NEVER use `serialize()`** for untrusted data - use JSON
- Use `hash_equals()` for timing-safe comparisons
- Always escape output: `htmlspecialchars($string, ENT_QUOTES, 'UTF-8')`
- Use parameterized queries for database operations
- Validate and sanitize all input data
- Cache data must use HMAC signatures to prevent tampering

### PHP DocBlocks

```php
/**
 * Fetch all instances from the API.
 *
 * @param bool $forceRefresh Force cache bypass
 * @return array{success: bool, data: array, cached: bool}
 */
public function fetchInstances(bool $forceRefresh = true): array
```

- Align `@param`, `@return`, `@throws` tags
- Use `phpdoc_single_line_var_spacing`
- Include return type in docblock for complex types
- Remove `@inheritDoc` when not needed

### Class Structure (ordered)

1. `use` statements for traits
2. `const` (public → protected → private)
3. `property` (public → protected → private)
4. Constructor
5. Destructor
6. Magic methods
7. PHPUnit methods (setUp, tearDown)
8. Public methods
9. Protected methods
10. Private methods

### Control Structures

```php
// Prefer early returns
if (!$condition) {
    return null;
}

// Use null coalescing
$value = $data['key'] ?? 'default';

// Use match for multiple conditions
$status = match ($code) {
    200 => 'success',
    404 => 'not_found',
    default => 'unknown',
};
```

### Database (SQLite)

- Use prepared statements with named parameters
- Always bind types: `SQLITE3_TEXT`, `SQLITE3_INTEGER`
- Use transactions for multi-step operations
- Handle busy timeout for concurrent access

### JavaScript (Frontend)

- Use ES6+ syntax
- Use `const`/`let` instead of `var`
- Prefer arrow functions for callbacks
- Use template literals for string interpolation
- Follow similar naming to PHP (camelCase)

---

## Testing Guidelines

### PHPUnit Conventions

- Test files: `*Test.php` in `tests/` directory
- Test class: `<ClassName>Test extends TestCase`
- Method naming: `test<Description>()` with prefix style
- Use `setUp()` and `tearDown()` for fixtures
- Mock external dependencies (cURL, database)
- Test success and failure cases
- Include edge cases and error handling

```php
protected function setUp(): void
{
    // Setup test environment
}

protected function tearDown(): void
{
    // Cleanup
}

public function testFetchInstancesReturnsArray(): void
{
    $result = $this->api->fetchInstances();
    $this->assertIsArray($result);
}
```

---

## File Organization

```
/var/www/chamalead/
├── admin.php              # Admin panel entry
├── admin-login.php        # Admin login
├── api.php                # Public API endpoint
├── config.php             # Main configuration
├── index.php              # Landing page
├── privacidade.php        # Privacy policy
├── leads.db               # SQLite database
├── AGENTS.md              # This file
└── panel/
    ├── Config.php
    ├── EvolutionApiService.php
    ├── DeepLinkService.php
    ├── Logger.php
    ├── db.php
    ├── auth.php
    ├── health.php
    ├── .php-cs-fixer.php  # PHP CS Fixer config
    └── tests/
        └── EvolutionApiServiceTest.php
```

---

## Environment

- PHP 8.2+
- SQLite3
- timezone: America/Sao_Paulo
- Language: Portuguese (pt-BR)
