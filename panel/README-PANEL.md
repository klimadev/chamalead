# Chamalead Panel - Technical Documentation

## Architecture Overview

The Chamalead Panel is a PHP 8.2+ web application built with modern security practices and clean architecture principles.

### Technology Stack

- **Backend**: PHP 8.2+ with Apache
- **Frontend**: Vanilla JavaScript (ES6+) with Tailwind CSS
- **Database**: SQLite for data persistence
- **Caching**: File-based with HMAC signature validation
- **Icons**: Lucide (modern, consistent icon set)
- **External API**: Evolution API integration

### Architecture Patterns

- **Single Responsibility**: Each class handles one concern
- **Service Layer**: `EvolutionApiService` centralizes all external API calls
- **Configuration Management**: `Config` class provides centralized env access
- **Structured Logging**: `Logger` class with rotation and multiple levels
- **Security-First**: CSRF tokens, rate limiting, prepared statements, XSS protection

---

## Directory Structure

```
panel/
├── auth.php                    # Authentication & security middleware
├── db.php                      # Database connection management
├── Config.php                  # Configuration management (NEW)
├── EvolutionApiService.php     # Evolution API client with caching
├── Logger.php                  # Structured logging with rotation
├── index.php                   # Main dashboard
├── login.php                   # Authentication page
├── logout.php                  # Session termination
├── instance-actions.php        # AJAX endpoints for CRUD operations
├── health.php                  # Health check endpoint
├── panel.js                    # Frontend logic & UX
├── styles.css                  # Global styles
├── modal-styles.css            # Modal component styles
├── .env.example                # Environment configuration template
├── .htaccess                   # Apache security configuration
├── Dockerfile                  # Container configuration
├── data/                       # Protected data directory
│   ├── panel.db               # SQLite database
│   ├── cache/                 # File-based cache
│   └── logs/                  # Application logs
├── tests/                      # Test suite
│   └── EvolutionApiServiceTest.php  # API service tests
└── .githooks/                  # Git hooks (optional)
    └── pre-commit             # Pre-commit validation
```

---

## Security Features

### Authentication & Authorization

- **Session-based authentication** with configurable timeout
- **Rate limiting**: 5 attempts per 15 minutes per IP
- **CSRF tokens** on all state-changing forms
- **Secure session handling** with HTTP-only cookies

### Data Protection

- **SQL Injection Prevention**: All database queries use prepared statements
- **XSS Protection**: Output escaped with `htmlspecialchars()`
- **Cache Poisoning Prevention**: HMAC signatures on all cached data
- **Content Security Policy (CSP) headers** configured in `.htaccess`

### Configuration Security

- `.env` file excluded from version control
- HMAC key auto-generated and stored securely
- No sensitive data in logs or error messages
- Automatic permission setting on sensitive files

---

## Configuration

### Environment Variables

Copy `.env.example` to `.env` and configure:

```env
# Evolution API Configuration
EVOLUTION_API_URL=http://evolution-api:8080
EVOLUTION_API_KEY=your-api-key-here

# Database Configuration
DB_PATH=/var/www/html/data/panel.db

# Session Configuration
SESSION_TIMEOUT=1800

# Rate Limiting
RATE_LIMIT_ATTEMPTS=5
RATE_LIMIT_WINDOW=900

# Cache Configuration
CACHE_ENABLED=true
CACHE_TTL=300
CACHE_PATH=/var/www/html/data/cache

# Logging
LOG_LEVEL=info
LOG_PATH=/var/www/html/data/logs

# Additional Security Settings
API_TIMEOUT=10
HMAC_SECRET=auto-generated-if-not-set
BACKUP_INTERVAL=86400
BACKUP_RETENTION=10

# Development
DEBUG=false
```

### Configuration via Config Class

```php
// Load configuration
Config::load();

// Get values with type safety
$apiUrl = Config::getString('EVOLUTION_API_URL', 'http://localhost:8080');
$timeout = Config::getInt('API_TIMEOUT', 10);
$cacheEnabled = Config::getBool('CACHE_ENABLED', true);
$apiKey = Config::get('EVOLUTION_API_KEY');

// Check environment
if (Config::isProduction()) {
    // Production-specific logic
}
```

---

## API Endpoints

### Internal AJAX Endpoints

All endpoints require valid CSRF token and session.

#### POST `/instance-actions.php`

**Action: create**
```json
{
  "action": "create",
  "csrf_token": "...",
  "instanceName": "my-instance",
  "token": "instance-token",
  "number": "5511999999999"
}
```

**Action: edit**
```json
{
  "action": "edit",
  "csrf_token": "...",
  "instanceName": "my-instance",
  "rejectCall": true,
  "msgCall": "Busy now",
  "groupsIgnore": false
}
```

**Action: delete**
```json
{
  "action": "delete",
  "csrf_token": "...",
  "instanceName": "my-instance"
}
```

**Action: getSettings**
```json
{
  "action": "getSettings",
  "csrf_token": "...",
  "instanceName": "my-instance"
}
```

**Action: getInstanceDetails**
```json
{
  "action": "getInstanceDetails",
  "csrf_token": "...",
  "instanceName": "my-instance"
}
```

### Health Check

#### GET `/health.php`

Returns health status of the application and API connection.

```json
{
  "status": "healthy",
  "timestamp": "2024-01-15T10:30:00Z",
  "api": {
    "connected": true,
    "response_time_ms": 150
  },
  "database": {
    "connected": true
  },
  "cache": {
    "enabled": true,
    "size_mb": 0.5
  }
}
```

---

## Testing

### PHP CS Fixer

Format PHP code according to PSR-12 standards:

```bash
# Check code style issues
php-cs-fixer fix --config=.php-cs-fixer.php --dry-run --diff

# Fix code style issues
php-cs-fixer fix --config=.php-cs-fixer.php
```

### ESLint

Lint JavaScript code:

```bash
# Check JavaScript issues
eslint panel.js

# Fix auto-fixable issues
eslint panel.js --fix
```

### PHPUnit

Run the test suite:

```bash
# Run all tests
phpunit tests/

# Run with coverage
phpunit tests/ --coverage-html coverage/

# Run specific test
phpunit tests/EvolutionApiServiceTest.php --filter testFetchInstancesReturnsArray
```

### Pre-commit Hooks

Automated validation before commits:

```bash
# Set up git hooks
chmod +x .githooks/pre-commit
git config core.hooksPath .githooks

# Now hooks run automatically on every commit
```

---

## Development Workflow

### 1. Environment Setup

```bash
# Clone repository
git clone <repo-url>
cd panel/

# Copy environment file
cp .env.example .env
# Edit .env with your configuration

# Install dependencies (if using composer)
# composer install
```

### 2. Code Style

Before committing, ensure code follows standards:

```bash
# Format PHP
php-cs-fixer fix

# Lint JavaScript
eslint panel.js --fix

# Run tests
phpunit tests/
```

### 3. Testing API Interactions

```bash
# Check Evolution API connectivity
curl -H "apikey: YOUR_API_KEY" \
  http://evolution-api:8080/instance/fetchInstances

# Test local health endpoint
curl http://localhost/health.php
```

### 4. Security Checklist

Before deploying to production:

- [ ] `.env` file created and configured
- [ ] `DEBUG=false` in production
- [ ] HMAC key auto-generated (check `data/.hmac_key`)
- [ ] File permissions set correctly (logs: 0750, key: 0600)
- [ ] `.env` not committed to version control
- [ ] Rate limiting enabled
- [ ] CSP headers configured

---

## Caching System

### File-Based Cache with HMAC Validation

All cached data includes HMAC signatures to prevent tampering:

```php
// Cache structure
$data = [
    'expires' => time() + $ttl,
    'value' => $cachedValue,
    'hmac' => hash_hmac('sha256', serialize($cachedValue), $hmacKey)
];
```

### Cache Invalidation

- **Automatic**: Expired entries are removed on read
- **Manual**: Call `clearCache()` on specific key or all
- **Event-based**: Creating/deleting instances clears relevant cache

### Cache Keys

- `instances_all` - List of all instances
- `settings_{instanceName}` - Per-instance settings (1 min TTL)

---

## Logging

### Log Levels

- **debug**: Detailed debugging information
- **info**: General operational information
- **warning**: Warning conditions
- **error**: Error conditions
- **critical**: Critical system failures

### Usage

```php
// Log messages
Logger::info('Instance created', ['instance' => $name, 'user' => $username]);
Logger::error('API request failed', ['error' => $error, 'endpoint' => $url]);

// Get recent logs
$recent = Logger::getRecent(100, 'error');
```

### Log Rotation

Automatic rotation when log file exceeds 10MB:
- Current: `panel_YYYY-MM-DD.log`
- Rotated: `panel_YYYY-MM-DD.log.old`

---

## Troubleshooting

### Common Issues

#### Permission Denied on data/

```bash
chmod -R 750 data/
chown -R www-data:www-data data/
```

#### Cache Not Working

- Check `CACHE_ENABLED=true` in `.env`
- Verify `data/cache/` directory exists and is writable
- Check HMAC key exists in `data/.hmac_key`

#### API Connection Failed

1. Verify `EVOLUTION_API_URL` is correct
2. Check `EVOLUTION_API_KEY` is valid
3. Test connectivity: `curl -I http://evolution-api:8080`
4. Check firewall rules

#### Session Timeout Too Fast

Adjust `SESSION_TIMEOUT` in `.env` (seconds):
- 1800 = 30 minutes
- 3600 = 1 hour

### Debug Mode

Enable detailed error messages (development only):

```env
DEBUG=true
```

**Warning**: Never enable in production as it may expose sensitive information.

---

## Performance Optimization

### Caching Strategy

- Instance list cached for 5 minutes
- Settings cached for 1 minute
- Force refresh available for real-time updates

### Frontend Optimizations

- Passive event listeners for scroll/touch
- Intersection Observer for scroll animations
- Performance detection for low-end devices
- Debounced search/filter operations

### Database

- SQLite with WAL mode for better concurrency
- Prepared statements for all queries
- Indexed columns on frequently queried fields

---

## Contributing

### Code Standards

1. Follow PSR-12 for PHP code
2. Use ESLint rules for JavaScript
3. Write tests for new features
4. Update documentation for API changes
5. Never commit `.env` or sensitive data

### Commit Messages

```
feat: add new feature
fix: resolve bug in instance creation
docs: update API documentation
test: add tests for EvolutionApiService
refactor: improve cache performance
style: fix code formatting
```

---

## License

Proprietary - All rights reserved.

---

## Support

For technical support or questions:
- Check logs in `data/logs/`
- Review this documentation
- Contact the development team

---

*Last updated: January 2024*
