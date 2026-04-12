# 02 - Tasks

## Phase 1: Foundation

### Task 1: Database Schema + Config Migration
**Outcome**: `quiz_leads` table exists and is accessible via existing `getDB()` function.
**Status**: ✅ COMPLETE

**Acceptance Criteria**:
- [REQ-08] Table `quiz_leads` created with all specified columns ✅ (32 columns, pre-existing English-named schema)
- [REQ-02] Table supports progressive updates (session_id UNIQUE, upsert pattern) ✅
- [INF-07] Status column supports all 6 states ✅
- Schema migration runs automatically on first access ✅ (`ensureQuizSchema` in `config.php`)

**Validation**: Table verified with 32 columns, 4 existing rows. `canWriteDatabase()` returns true.

**Note**: Existing table used English column names (`name`, `role`, `revenue_band`, etc.). API adapted to match actual schema.

---

### Task 2: Quiz Entry Point
**Outcome**: `quiz/index.php` loads with Tailwind, fonts, and quiz engine ready.
**Status**: ✅ COMPLETE

**Acceptance Criteria**:
- [INF-08] Accessible at `/quiz/` URL ✅ (verified via curl HTTPS)
- [INF-09] Reuses existing Tailwind config ✅ (same colors, fonts, animations)
- [INF-02] Captures UTM parameters from URL, passes to JS ✅
- Loads `quiz/assets/css/quiz.css` and `quiz/assets/js/quiz-engine.js` ✅
- Progress bar at top ✅

**Validation**: Page renders at `https://chamalead.com/quiz/` with all 11 step partials included.

---

### Task 3: Quiz API Endpoints
**Outcome**: `quiz/api.php` handles save_step, complete, and get_state actions.
**Status**: ✅ COMPLETE

**Acceptance Criteria**:
- [REQ-02] `POST ?action=save_step` accepts session_id, step, field, value; upserts to DB ✅
- [REQ-03] `POST ?action=complete` finalizes quiz, triggers webhook ✅
- [INF-01] `GET ?action=get_state` returns current quiz state by session_id ✅
- [REQ-09] Returns structured JSON responses with appropriate HTTP status codes ✅
- [INF-03] Validates input before saving ✅
- Follows existing `api.php` patterns ✅

**Validation**: Full 8-step flow tested via curl. All saves return `{"success":true}`. Get state returns complete answers. Complete returns score=14, classificacao=quente, trilha=acelerada.

---

### Task 4: Quiz Engine (JS State Machine)
**Outcome**: `quiz-engine.js` manages quiz state, navigation, and transitions.
**Status**: ✅ COMPLETE

**Acceptance Criteria**:
- [REQ-11] Loads and displays one step at a time ✅
- [REQ-02] Calls save_step API after each answer ✅
- [INF-01] Creates/resumes session via localStorage ✅
- [INF-04] Smooth transitions between steps (fade/slide) ✅
- [INF-05] Updates progress bar ✅
- [INF-10] Back button works ✅
- [REQ-05] Conditional routing for step 8 based on revenue ✅
- Handles loading, error, and success states ✅

---

## Phase 2: Quiz Steps

### Task 5: Step 0 - Opening
**Status**: ✅ COMPLETE
- Premium dark UI with flame icon, compelling headline, "Começar diagnóstico" CTA
- No back button ✅

### Task 6: Step 1 - Name
**Status**: ✅ COMPLETE
- Text input with validation (min 2, max 120 chars)
- Saves to `name` column ✅

### Task 7: Step 2 - WhatsApp
**Status**: ✅ COMPLETE
- Phone mask: (00) 00000-0000 ✅
- Validates 10-11 digits ✅

### Task 8: Step 3 - Role/Cargo
**Status**: ✅ COMPLETE
- Options: Dono/Sócio, Gestor/Gerente, Outro with Lucide icons ✅

### Task 9: Step 4 - Revenue/Faturamento
**Status**: ✅ COMPLETE
- 5 revenue options ✅
- Track assignment: <=20k = consultiva, >20k = acelerada ✅

### Task 10: Step 5 - Channel/Canal
**Status**: ✅ COMPLETE
- Options: Instagram, Facebook, Google, Indicação, Outro ✅

### Task 11: Step 6 - Volume
**Status**: ✅ COMPLETE
- Options: 0-10, 11-30, 31-100, 100+ ✅

### Task 12: Step 7 - Main Pain/Dor
**Status**: ✅ COMPLETE
- 4 pain options with descriptive labels ✅

### Task 13: Step 8 - Conditional Pain Detail
**Status**: ✅ COMPLETE
- `8.dor-consultiva.html`: "Como você lida com leads que não compram na hora?" ✅
- `8.dor-acelerada.html`: "Quanto tempo seu time leva para responder um lead?" ✅
- Both have 4 option buttons with Lucide icons ✅

### Task 14: Step 9 - Timing
**Status**: ✅ COMPLETE
- Options: Agora, Este mês, Próximo mês, Apenas entendendo ✅

### Task 15: Step 10 - Final CTA
**Status**: ✅ COMPLETE
- "Seu diagnóstico está pronto" headline ✅
- "Quero ser chamado agora" CTA ✅
- Loading → success/error states ✅
- Score calculation and classification display ✅
- Idempotent webhook (double-click blocked) ✅

---

## Phase 3: Polish & Integration

### Task 16: Styling + Animations
**Status**: ✅ COMPLETE
- Deep purple-black background (#0a0510) ✅
- Space Grotesk headlines, Inter body ✅
- Mobile-first responsive (breakpoints 480px, 768px) ✅
- Fade/slide transitions (300ms) ✅
- Hover/focus states on all interactive elements ✅
- No glassmorphism, no emojis ✅

### Task 17: Progress Bar
**Status**: ✅ COMPLETE
- Fixed top, gradient flame-to-ember ✅
- Smooth width animation ✅
- Step counter "Passo X de 10" ✅

### Task 18: Webhook Integration
**Status**: ✅ COMPLETE
- Webhook fires on completion ✅
- Idempotency via session_id ✅ (verified: second complete returns "already_sent")
- Retry with exponential backoff (3 attempts) ✅
- Status updates: webhook_pending → webhook_sent/webhook_error ✅
- Response stored in meta_json ✅

### Task 19: Error Handling + Retry Logic
**Status**: ✅ COMPLETE
- Toast error notifications ✅
- Retry button on errors ✅
- Validation errors with red borders ✅
- Webhook retry logic ✅

### Task 20: Testing + QA
**Status**: ✅ COMPLETE

**Automated Tests**:
| Test | Result |
|------|--------|
| Scoring Algorithm | 5/5 PASS |
| Track Determination | 5/5 PASS |
| Input Validation | 22/22 PASS |

**API Integration Tests**:
| Test | Result |
|------|--------|
| Progressive save (8 steps) | PASS |
| Track assignment | PASS |
| Session state retrieval | PASS |
| Quiz completion + scoring | PASS |
| Idempotency (double complete) | PASS |
| Page renders at /quiz/ | PASS |

---

## Implementation Notes

1. **Schema adaptation**: Existing `quiz_leads` table used English column names. API adapted accordingly.
2. **Revenue boundary**: `<= 20000` maps to consultiva (10k_20k = consultiva).
3. **Webhook URL**: Placeholder in `config.php` - needs real endpoint configured.
4. **Report**: See `reports/final-execution-report.md` for full details.
