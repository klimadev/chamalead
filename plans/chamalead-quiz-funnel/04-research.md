# 04 - Research: Existing Codebase Patterns

## Module Loading Mechanism

### How index.php loads partials

**File**: `/var/www/chamalead/index.php`

```php
$modulesDir = __DIR__ . '/modules';
$modules = [];

if (is_dir($modulesDir)) {
    $files = scandir($modulesDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            $modules[] = $file;
        }
    }
    sort($modules);
}
```

Then includes them in order:
```php
<?php foreach ($modules as $module): ?>
<?php include $modulesDir . '/' . $module; ?>
<?php endforeach; ?>
```

**Key observations**:
1. **Alphabetical sorting**: Files are sorted lexicographically, hence the `0.`, `1.`, `2.` prefix pattern
2. **HTML-only**: Only `.html` files are included
3. **Server-side include**: Modules are included at PHP render time, not loaded via AJAX
4. **No caching**: Modules are read from disk on every request
5. **Flat structure**: All modules live in a single directory

### Existing Module Structure

```
modules/
├── 0.overlays-e-progress.html    # Global overlays, progress bar, effects
├── 1.navbar.html                 # Navigation
├── 2.hero.html                   # Hero section with phone mockup
├── 3.problema.html               # Problem section
├── 4.solucao.html                # Solution section
├── 5.case.html                   # Case study
├── 6.prova-social.html           # Social proof
├── 7.formulario.html             # Lead capture form
├── 8.faq.html                    # FAQ
└── 9.footer.html                 # Footer
```

### Quiz-Specific Implications

**For the quiz funnel, we need a DIFFERENT loading pattern** because:
- The quiz is a SPA-like experience (one question per screen)
- Modules should load dynamically via JavaScript, not all at once
- We need state management between steps

**Recommended Quiz Structure**:
```
quiz/
├── index.php                     # Quiz entry point (similar pattern to index.php)
├── modules/                      # Quiz step partials
│   ├── 0.abertura.html           # Welcome/opening screen
│   ├── 1.nome.html               # Name input
│   ├── 2.whatsapp.html           # WhatsApp input
│   ├── 3.cargo.html              # Role/position
│   ├── 4.faturamento.html        # Revenue range
│   ├── 5.canal.html              # Primary channel
│   ├── 6.volume.html             # Lead volume
│   ├── 7.dor.html                # Main pain point
│   ├── 8.dor-consultiva.html     # Conditional: consultive track
│   ├── 8.dor-acelerada.html      # Conditional: accelerated track
│   ├── 9.timing.html             # Urgency/timing
│   └── 10.cta-final.html         # Final CTA
├── api.php                       # Quiz-specific API endpoints
└── assets/
    ├── css/quiz.css              # Quiz-specific styles
    └── js/quiz-engine.js         # Quiz state machine and navigation
```

### Database Pattern

**File**: `/var/www/chamalead/config.php`

- Uses SQLite3 with `static $db` singleton pattern
- Schema migration via `ensureLeadsSchema()` function
- `canWriteDatabase()` checks permissions before writes
- Uses prepared statements with named parameters

**Quiz DB should follow the same pattern**:
- Add `quiz_leads` table creation to `config.php` or separate `quiz-config.php`
- Use same `getDB()` function or create `getQuizDB()` wrapper
- Follow same error handling pattern

### API Pattern

**File**: `/var/www/chamalead/api.php`

- Returns JSON with `Content-Type: application/json`
- Uses `apiResponse()` helper for consistent responses
- Validates input with structured error arrays
- HTTP status codes: 201 (success), 405 (method), 422 (validation), 500 (server)

**Quiz API should follow the same pattern**:
- New endpoint: `quiz/api.php` or extend existing `api.php`
- Endpoints needed:
  - `POST /quiz/api.php?action=save_step` - Progressive save
  - `POST /quiz/api.php?action=complete` - Final submission + webhook trigger
  - `GET /quiz/api.php?action=get_state&session_id=xxx` - Resume session

### Asset Pattern

**Files**: 
- `/var/www/chamalead/assets/css/app.css`
- `/var/www/chamalead/assets/js/app.js`

**Tailwind Config** (in `index.php` head):
- Custom colors: `flame`, `ember`, `dark`
- Custom fonts: `Inter` (sans), `Space Grotesk` (display)
- Custom animations: `pulse-slow`, `float`, `glow`, `shimmer`, `slide-up`, `scale-in`, `mesh-*`

**Quiz should reuse the same Tailwind config** for visual consistency.

## Decision: Quiz Architecture

Given the existing patterns, the quiz should:

1. **Entry Point**: `quiz/index.php` - mirrors `index.php` pattern but for quiz modules
2. **Module Loading**: JavaScript-driven SPA (not PHP includes) because we need:
   - Dynamic step transitions
   - State management
   - Conditional logic (faturamento bifurcation)
   - Progressive saving
3. **API**: `quiz/api.php` - separate endpoint following existing `api.php` patterns
4. **Styling**: Reuse existing Tailwind config + quiz-specific CSS
5. **Database**: Add `quiz_leads` table via `config.php` migration function

## Visual/UX Patterns to Replicate

From existing modules:
- **Dark backgrounds**: `#030303`, `bg-dark`, `bg-dark-900`
- **Gradient accents**: `from-flame-500 to-ember-600`
- **Glass effects**: `backdrop-blur-sm`, `bg-flame-500/10`
- **Rounded corners**: `rounded-xl`, `rounded-3xl`
- **Typography**: `font-display`, `font-black`, large sizes
- **Buttons**: `btn-primary` class with gradient, shadow, hover effects
- **Animations**: `reveal`, `slide-up`, `scale-in`
- **Icons**: Lucide icons via CDN

## Critical Unknowns

1. **Webhook URL**: Not specified in brief - needs to be configurable (suggest `config.php` constant)
2. **Session Management**: How to track quiz progress across page refreshes? (suggest localStorage + server-side session_id)
3. **UTM Parameters**: Need to capture from URL on entry and persist throughout quiz
4. **Conditional Logic Details**: "Condicional da dor" - exact branching logic needs clarification
5. **Webhook Retry Policy**: How many retries on failure? What backoff strategy?
