# 01 - Implementation Specification

## Objective

Build a premium, high-conversion quiz funnel for ChamaLead that functions as a commercial diagnostic tool (not a form). The quiz collects lead data progressively, scores qualification, saves to SQLite at each step, and triggers a webhook upon completion to initiate AI-driven contact.

**Product**: Quiz Funnel → Automation Sale (R$1.997/1st month, R$997/month after)
**Modules Sold**: Prospecção, Atendimento Automático, Follow-up Automático

## Scope

### In Scope
- Quiz entry page (`quiz/index.php`) with module loading pattern
- 10 quiz steps (1 question per screen) with conditional branching
- Progressive save to SQLite (`quiz_leads` table)
- Lead scoring algorithm (authority, revenue, volume, pain, timing)
- Webhook dispatch on completion with idempotency
- Premium dark-mode UI (purple-black, white text, coral/red accents)
- Mobile-first responsive design
- UTM parameter capture and persistence
- Session-based resume capability

### Out of Scope
- Admin panel integration for quiz results (phase 2)
- A/B testing framework
- Email notifications
- Multi-language support
- User authentication for quiz takers
- Analytics dashboard

## Explicit Requirements

| ID | Requirement | Source |
|----|-------------|--------|
| REQ-01 | Quiz must feel like an intelligent tool, not a form | Brief #3 |
| REQ-02 | Save progressively to database at each step | Brief #5 |
| REQ-03 | Trigger webhook on final completion | Brief #5 |
| REQ-04 | 10 steps: Opening, Name, WhatsApp, Role, Revenue, Channel, Volume, Pain, Conditional Pain, Timing, Final CTA | Brief #6 |
| REQ-05 | Revenue bifurcation: <20k = Consultive Track, >20k = Accelerated Track | Brief #7 |
| REQ-06 | Lead scoring: Authority, Revenue, Volume, Pain, Timing with specific point values | Brief #8 |
| REQ-07 | Classification: Cold (0-4), Warm (5-8), Hot (9+) | Brief #8 |
| REQ-08 | Database table `quiz_leads` with specified columns and statuses | Brief #9 |
| REQ-09 | Webhook payload must be strict JSON, idempotent, prevent double-click | Brief #10 |
| REQ-10 | Dark mode UI: purple-black background, white text, coral/red accents, giant typography, mobile-first | Brief #11 |
| REQ-11 | One question per screen | Brief #6, #11 |
| REQ-12 | Capture: name, whatsapp, role, revenue, leads, pain, urgency, score, UTMs | Brief #5 |

## Inferred Requirements

| ID | Requirement | Rationale |
|----|-------------|-----------|
| INF-01 | Session management via localStorage + server-side session_id | Enable resume after refresh |
| INF-02 | UTM capture on entry and persist through quiz | Attribution tracking |
| INF-03 | Input validation at each step before progression | Data quality |
| INF-04 | Smooth transitions between steps (fade/slide) | Premium UX |
| INF-05 | Progress bar visualization | User orientation |
| INF-06 | WhatsApp number formatting/validation | Integration compatibility |
| INF-07 | Webhook retry on failure (3 attempts, exponential backoff) | Reliability |
| INF-08 | Quiz entry via `quiz/` URL path | Logical structure |
| INF-09 | Reuse existing Tailwind config for consistency | Visual coherence |
| INF-10 | Back button on each step (discreet) | User control |

## Assumptions

1. **Webhook URL**: Configurable via `config.php` constant `QUIZ_WEBHOOK_URL`
2. **Session ID**: Generated client-side (UUID v4) on quiz entry, stored in localStorage
3. **Conditional Pain Step**: Step 8 shows different content based on revenue track (consultive vs accelerated)
4. **Webhook Payload**: Standard JSON with all quiz data + score + classification
5. **PHP Version**: 8.2+ available (per AGENTS.md)
6. **SQLite Extensions**: Standard SQLite3 available
7. **No Auth**: Quiz is public, no login required
8. **Single Quiz**: One quiz funnel, no multi-quiz support needed

## Non-Negotiables

1. **Progressive Save**: Data MUST be saved at each step, not just at completion
2. **Idempotent Webhook**: Same quiz completion must not trigger duplicate webhooks
3. **Mobile-First**: Design must work flawlessly on mobile before desktop
4. **One Question Per Screen**: No multi-field screens
5. **Premium Feel**: Must look like a diagnostic tool, not a lead capture form
6. **No Double Submission**: Final CTA must prevent duplicate submissions

## Requirement Coverage Table

| Req ID | Priority | Planned Solution | Acceptance Criteria | Validation Method |
|--------|----------|------------------|---------------------|-------------------|
| REQ-01 | P0 | SPA-like quiz with conversational microcopy, no form-like layout | Users perceive it as a tool, not a form | Manual UX review |
| REQ-02 | P0 | `POST /quiz/api.php?action=save_step` called after each answer | Each step creates/updates row in `quiz_leads` | DB inspection after each step |
| REQ-03 | P0 | `POST /quiz/api.php?action=complete` triggers webhook dispatch | Webhook receives payload within 5s of completion | Webhook endpoint logs |
| REQ-04 | P0 | 10 HTML partials in `quiz/modules/`, loaded by JS engine | All 10 steps render correctly | Manual walkthrough |
| REQ-05 | P0 | JS routing logic checks revenue value, loads appropriate step 8 | <20k shows consultive, >20k shows accelerated | Test both paths |
| REQ-06 | P0 | PHP scoring function calculates points per spec | Score matches manual calculation for test cases | Automated test script |
| REQ-07 | P0 | Classification logic: 0-4=cold, 5-8=warm, 9+=hot | Classification matches score ranges | Automated test script |
| REQ-08 | P0 | `quiz_leads` table with all columns, status enum | Table exists with correct schema | DB schema inspection |
| REQ-09 | P0 | Idempotency key (session_id), status check before webhook | No duplicate webhooks for same session | Load test with double-click |
| REQ-10 | P0 | Dark theme CSS, mobile-first media queries, Tailwind config | Matches visual spec, passes mobile viewport tests | Visual review + screenshot |
| REQ-11 | P0 | Single question per HTML partial, JS shows one at a time | Only one question visible per screen | Manual walkthrough |
| REQ-12 | P0 | Form fields in steps map to `quiz_leads` columns | All data persisted correctly | DB inspection |
| INF-01 | P1 | localStorage for session_id, server-side lookup | Refresh page, quiz resumes at last step | Manual test |
| INF-02 | P1 | Capture `?utm_*` params on entry, store in session | UTMs appear in final webhook payload | URL parameter test |
| INF-03 | P1 | Client-side validation before step progression | Invalid input blocks progression with message | Manual test |
| INF-04 | P1 | CSS transitions (fade/slide) between steps | Smooth animation, no jank | Visual review |
| INF-05 | P1 | Progress bar at top, updates per step | Bar reflects current step/total | Visual review |
| INF-06 | P1 | JS phone mask + server-side validation | Valid Brazilian phone format | Manual + automated test |
| INF-07 | P1 | PHP retry logic with 3 attempts, exponential backoff | Failed webhook retries automatically | Mock webhook failure test |
| INF-08 | P1 | `quiz/` directory with `index.php` | Accessible at `/quiz/` | URL test |
| INF-09 | P1 | Reuse Tailwind config from `index.php` | Consistent colors, fonts, animations | Visual comparison |
| INF-10 | P1 | Discreet back button on steps 2-10 | Back navigates to previous step, preserves data | Manual test |

## Recommended Approach

### Architecture

```
quiz/
├── index.php              # Entry point, loads Tailwind + quiz engine
├── api.php                # API endpoints (save_step, complete, get_state)
├── modules/               # HTML partials for each step
│   ├── 0.abertura.html
│   ├── 1.nome.html
│   ├── 2.whatsapp.html
│   ├── 3.cargo.html
│   ├── 4.faturamento.html
│   ├── 5.canal.html
│   ├── 6.volume.html
│   ├── 7.dor.html
│   ├── 8.dor-consultiva.html
│   ├── 8.dor-acelerada.html
│   ├── 9.timing.html
│   └── 10.cta-final.html
└── assets/
    ├── css/quiz.css       # Quiz-specific styles
    └── js/
        ├── quiz-engine.js # State machine, navigation, transitions
        └── quiz-api.js    # API communication layer
```

### PHP Structure

**quiz/index.php**:
- Mirrors `index.php` pattern but for quiz SPA
- Loads Tailwind CDN, fonts, Lucide icons
- Includes quiz-specific CSS/JS
- Captures UTM parameters, passes to JS
- No PHP module includes - JS handles dynamic loading

**quiz/api.php**:
```php
Endpoints:
- POST ?action=save_step    # Progressive save
- POST ?action=complete     # Final submission + webhook
- GET  ?action=get_state    # Resume session

Response format: { success: bool, data?: array, error?: string }
```

**config.php additions**:
```php
// Quiz configuration
define('QUIZ_WEBHOOK_URL', 'https://your-webhook-url.com/quiz-complete');
define('QUIZ_WEBHOOK_TIMEOUT', 10);
define('QUIZ_WEBHOOK_RETRIES', 3);

// Quiz schema migration
function ensureQuizSchema(SQLite3 $db) { ... }
```

### SQLite Schema

```sql
CREATE TABLE IF NOT EXISTS quiz_leads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id TEXT UNIQUE NOT NULL,
    nome TEXT,
    whatsapp TEXT,
    cargo TEXT,
    faturamento TEXT,
    faturamento_valor INTEGER DEFAULT 0,
    canal TEXT,
    volume_leads TEXT,
    dor_principal TEXT,
    dor_detalhe TEXT,
    timing TEXT,
    score INTEGER DEFAULT 0,
    classificacao TEXT DEFAULT 'frio',
    trilha TEXT,  -- 'consultiva' or 'acelerada'
    utm_source TEXT,
    utm_medium TEXT,
    utm_campaign TEXT,
    utm_content TEXT,
    utm_term TEXT,
    status TEXT DEFAULT 'started',  -- started, in_progress, completed, webhook_pending, webhook_sent, webhook_error
    current_step INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    webhook_sent_at DATETIME,
    webhook_response TEXT
);
```

### JavaScript Engine

**quiz-engine.js**:
```javascript
class QuizEngine {
  constructor() {
    this.sessionId = this.getOrCreateSession();
    this.currentStep = 0;
    this.answers = {};
    this.utms = this.captureUTMs();
    this.isTransitioning = false;
  }
  
  // Core methods
  async loadStep(stepNumber)
  async saveStep(stepNumber, answer)
  async complete()
  goBack()
  goForward()
  
  // State management
  getOrCreateSession()
  captureUTMs()
  calculateScore()
  determineTrack()
  
  // UI
  showStep(stepNumber)
  showLoading()
  showError(message)
  updateProgress()
}
```

**quiz-api.js**:
```javascript
class QuizAPI {
  static async saveStep(sessionId, step, answer)
  static async complete(sessionId, answers)
  static async getState(sessionId)
}
```

### HTML Partial Pattern

Each module follows this structure:
```html
<!-- Step X: Question Title -->
<div class="quiz-step" data-step="X">
  <div class="quiz-question">
    <h2 class="quiz-headline">QUESTION TEXT</h2>
    <p class="quiz-subtitle">Supporting text (optional)</p>
  </div>
  
  <div class="quiz-options">
    <!-- Options or input field -->
  </div>
  
  <div class="quiz-actions">
    <button class="quiz-back" data-action="back">Voltar</button>
    <button class="quiz-next" data-action="next">Próximo</button>
  </div>
</div>
```

## UX/UI Specification

### Layout
- **Container**: Centered, max-width 640px on desktop, full-width on mobile
- **Vertical**: Question at top 40%, options in middle 40%, actions at bottom 20%
- **Progress Bar**: Fixed at top, gradient `from-flame-500 to-ember-600`
- **Background**: Deep purple-black `#0a0510` or similar

### Colors
```
Background: #0a0510 (deep purple-black)
Surface: #130a1f (slightly lighter)
Text Primary: #ffffff
Text Secondary: #a0a0b0
Accent Primary: #ff6b4a (coral)
Accent Secondary: #dc2626 (red)
Gradient CTA: linear-gradient(135deg, #ff6b4a, #dc2626)
Border: rgba(255, 255, 255, 0.1)
Success: #22c55e
Error: #ef4444
```

### Typography
```
Headlines: Space Grotesk, 700-900 weight, 32px-48px mobile, 48px-64px desktop
Body: Inter, 400-500 weight, 16px-18px
Buttons: Inter, 600-700 weight, 16px-18px, uppercase
Labels: Inter, 500 weight, 14px
```

### Transitions
- **Step Change**: Fade out (150ms) → Slide in from right (250ms)
- **Progress Bar**: Smooth width transition (300ms)
- **Button Hover**: Scale 1.02, shadow increase (150ms)
- **Selection**: Immediate border color change + subtle scale (100ms)

### States per Screen
| State | Description |
|-------|-------------|
| idle | Default, waiting for input |
| focused | Input field focused, subtle glow |
| filled | Answer selected, visual confirmation |
| loading | Step saving, spinner overlay |
| validation_error | Invalid input, red border + message |
| api_error | Server error, retry option |
| success | Step saved, auto-advance |

### Final Screen States
| State | Description |
|-------|-------------|
| idle | CTA button ready |
| loading | "Preparando sua análise..." spinner |
| webhook_loading | "Conectando com especialista..." |
| webhook_success | "Pronto! Vamos te chamar no WhatsApp" |
| webhook_error | "Erro ao conectar. Tentar novamente?" + retry button |

### Microcopy Guidelines
- **Tone**: Confident, objective, consultative
- **Opening**: "Descubra em 2 minutos o potencial de automação do seu negócio"
- **CTA**: "Quero ser chamado agora"
- **Back**: "Voltar" (discreet, small)
- **Progress**: "Passo X de Y"
- **Loading**: "Analisando suas respostas..."
- **Success**: "Diagnóstico completo! Nossa IA vai te chamar em instantes."

### Visual Elements
- Fine outline mascot/icon (flame motif from existing branding)
- Organic curved lines at corners (subtle SVG)
- Thin dividers between sections
- Minimalist linear icons (Lucide)
- NO: generic SaaS cards, light themes, glassmorphism, corporate blue, emojis, childish illustrations

## Business Rules

### Lead Scoring Algorithm

```php
function calculateQuizScore($answers): array {
    $score = 0;
    
    // Authority (cargo)
    $authorityScores = [
        'dono' => 3,
        'gestor' => 2,
        'outro' => 1,
    ];
    $score += $authorityScores[$answers['cargo']] ?? 0;
    
    // Revenue (faturamento)
    $revenueScores = [
        'ate_10k' => -1,
        '10k_20k' => 1,
        '20k_50k' => 3,
        '50k_100k' => 4,
        'acima_100k' => 5,
    ];
    $score += $revenueScores[$answers['faturamento']] ?? 0;
    
    // Volume (volume_leads)
    $volumeScores = [
        '0_10' => 0,
        '11_30' => 1,
        '31_100' => 2,
        '100_mais' => 3,
    ];
    $score += $volumeScores[$answers['volume_leads']] ?? 0;
    
    // Pain (dor_principal)
    $painScores = [
        'atendimento_lento' => 2,
        'perdendo_vendas' => 2,
        'leads_desqualificados' => 1,
        'dificuldade_escalar' => 1,
    ];
    $score += $painScores[$answers['dor_principal']] ?? 0;
    
    // Timing
    $timingScores = [
        'agora' => 3,
        'este_mes' => 2,
        'proximo_mes' => 1,
        'entendendo' => 0,
    ];
    $score += $timingScores[$answers['timing']] ?? 0;
    
    // Classification
    $classification = match (true) {
        $score >= 9 => 'quente',
        $score >= 5 => 'morno',
        default => 'frio',
    };
    
    return [
        'score' => $score,
        'classificacao' => $classification,
    ];
}
```

### Conditional Routing

```javascript
function determineTrack(faturamento) {
  const revenueMap = {
    'ate_10k': 10000,
    '10k_20k': 20000,
    '20k_50k': 50000,
    '50k_100k': 100000,
    'acima_100k': 150000,
  };
  
  const value = revenueMap[faturamento] ?? 0;
  return value < 20000 ? 'consultiva' : 'acelerada';
}
```

### Webhook Payload

```json
{
  "event": "quiz_completed",
  "timestamp": "2024-01-15T10:30:00-03:00",
  "idempotency_key": "session-uuid-here",
  "lead": {
    "session_id": "uuid",
    "nome": "John Doe",
    "whatsapp": "5511999999999",
    "cargo": "dono",
    "faturamento": "20k_50k",
    "faturamento_valor": 50000,
    "canal": "instagram",
    "volume_leads": "31_100",
    "dor_principal": "atendimento_lento",
    "dor_detalhe": "detail text",
    "timing": "agora",
    "score": 12,
    "classificacao": "quente",
    "trilha": "acelerada",
    "utm_source": "google",
    "utm_medium": "cpc",
    "utm_campaign": "brand",
    "utm_content": "ad1",
    "utm_term": "automation"
  }
}
```

### Status Flow

```
started → in_progress → completed → webhook_pending → webhook_sent
                                            ↓
                                      webhook_error (retry)
```

## Edge Cases and Failure Handling

| Scenario | Handling |
|----------|----------|
| Double-click on final CTA | Disable button immediately, show loading state |
| Network failure during save | Retry 3x, show error message, preserve data locally |
| Webhook timeout | Retry with exponential backoff (1s, 2s, 4s) |
| Webhook returns error | Update status to `webhook_error`, store response |
| Page refresh mid-quiz | Resume from localStorage + server state |
| Invalid session ID | Create new session, start from beginning |
| Empty answer submission | Block progression, show validation message |
| Concurrent submissions (same session) | Idempotency key prevents duplicate webhook |
| Database locked | Retry with busy timeout (3s) |
| Malformed UTM parameters | Ignore invalid, capture valid ones |

## Implementation Order

### Phase 1: Foundation (Tasks 1-4)
1. Database schema + config
2. Quiz entry point (`quiz/index.php`)
3. API endpoints (`quiz/api.php`)
4. Quiz engine (JS state machine)

### Phase 2: Steps (Tasks 5-15)
5. Step 0: Opening
6. Step 1: Name
7. Step 2: WhatsApp
8. Step 3: Role
9. Step 4: Revenue
10. Step 5: Channel
11. Step 6: Volume
12. Step 7: Pain
13. Step 8: Conditional (consultive + accelerated)
14. Step 9: Timing
15. Step 10: Final CTA

### Phase 3: Polish (Tasks 16-20)
16. Styling + animations
17. Progress bar
18. Webhook integration
19. Error handling + retry logic
20. Testing + QA

## Definition of Done

### Task Level
- Code written following AGENTS.md conventions
- PHP CS Fixer passes
- Unit tests pass (scoring, validation)
- Manual walkthrough successful

### Project Level
- All 10 steps functional with transitions
- Progressive save working (verified in DB)
- Lead scoring accurate (verified with test cases)
- Webhook fires exactly once per completion
- Mobile responsive (tested on 320px-768px)
- Desktop centered layout (tested on 1024px+)
- No console errors
- Page load < 2s on 3G
- UTM parameters captured and persisted
- Session resume working after refresh

## Key Risks

| Risk | Impact | Mitigation |
|------|--------|------------|
| Webhook endpoint unavailable | High | Retry logic, error logging, manual retry option |
| Quiz feels like a form | High | Careful microcopy, conversational tone, no form-like layout |
| Mobile UX issues | High | Mobile-first design, thorough testing |
| Data loss on refresh | Medium | localStorage + server-side sync |
| Performance on slow networks | Medium | Minimal JS, lazy load modules, optimize assets |
| Scoring logic incorrect | Medium | Automated tests with known inputs/outputs |
