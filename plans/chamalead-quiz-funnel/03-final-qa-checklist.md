# 03 - Final QA Checklist

## Functional Testing

### Quiz Flow
- [ ] Quiz accessible at `/quiz/` URL
- [ ] Opening screen displays correctly with compelling headline
- [ ] Start button creates session and advances to step 1
- [ ] All 10 steps load in correct sequence
- [ ] Each step shows only one question
- [ ] Progress bar updates correctly at each step
- [ ] Back button works on steps 2-10
- [ ] Back button preserves previously entered data
- [ ] No back button on opening screen (step 0)

### Data Collection
- [ ] Step 1 (Name): Validates min 2 chars, max 120 chars
- [ ] Step 2 (WhatsApp): Phone mask works, validates 10-11 digits
- [ ] Step 3 (Cargo): Selection required, saves correctly
- [ ] Step 4 (Faturamento): Selection saves, track assigned correctly
- [ ] Step 5 (Canal): Selection saves correctly
- [ ] Step 6 (Volume): Selection saves correctly
- [ ] Step 7 (Dor): Selection saves correctly
- [ ] Step 8 (Conditional): Correct template loads based on revenue track
- [ ] Step 9 (Timing): Selection saves correctly
- [ ] Step 10 (CTA): Score calculated, classification assigned

### Conditional Routing
- [ ] Revenue < 20k → Step 8 loads `dor-consultiva.html`
- [ ] Revenue >= 20k → Step 8 loads `dor-acelerada.html`
- [ ] Track stored correctly in database (`trilha` column)

### Progressive Save
- [ ] Each step saves to database immediately after answer
- [ ] Database row created on step 1 (session start)
- [ ] Subsequent steps update existing row (upsert)
- [ ] `current_step` column updates correctly
- [ ] `status` transitions: started → in_progress → completed
- [ ] All answers persist after page refresh
- [ ] Session resume works after browser close/reopen

### Lead Scoring
- [ ] Authority points: Dono +3, Gestor +2, Outro +1
- [ ] Revenue points: <10k -1, 10-20k +1, 20-50k +3, 50-100k +4, 100k+ +5
- [ ] Volume points: 0-10 → 0, 11-30 → +1, 31-100 → +2, 100+ → +3
- [ ] Pain points: atendimento_lento +2, perdendo_vendas +2, leads_desqualificados +1, dificuldade_escalar +1
- [ ] Timing points: agora +3, este_mes +2, proximo_mes +1, entendendo +0
- [ ] Classification: 0-4 = frio, 5-8 = morno, 9+ = quente
- [ ] Score stored in database correctly

### Webhook
- [ ] Webhook fires on final CTA click
- [ ] Payload matches specified JSON structure
- [ ] Idempotency key present (session_id)
- [ ] Double-click on CTA does NOT trigger duplicate webhook
- [ ] Webhook timeout triggers retry (3 attempts)
- [ ] Exponential backoff: 1s, 2s, 4s
- [ ] Webhook success → status = webhook_sent, webhook_sent_at set
- [ ] Webhook failure → status = webhook_error, response stored
- [ ] Manual retry option available on error

### UTM Parameters
- [ ] UTM parameters captured from URL on entry
- [ ] Supported params: utm_source, utm_medium, utm_campaign, utm_content, utm_term
- [ ] UTMs persist throughout quiz
- [ ] UTMs included in webhook payload
- [ ] Invalid UTM values ignored gracefully

## UI/UX Testing

### Visual Design
- [ ] Background is deep purple-black (#0a0510 or similar)
- [ ] Text is white/off-white, legible
- [ ] CTAs use coral/red gradient
- [ ] Typography: Space Grotesk for headlines, Inter for body
- [ ] Headlines are large and heavy (32px+ mobile, 48px+ desktop)
- [ ] No glassmorphism, no corporate blue, no emojis
- [ ] Organic curved lines present at corners
- [ ] Fine dividers between sections
- [ ] Lucide icons used consistently

### Responsiveness
- [ ] Mobile 320px: All content visible, no horizontal scroll
- [ ] Mobile 375px: Comfortable touch targets (min 44px height)
- [ ] Mobile 768px: Layout adapts correctly
- [ ] Tablet 1024px: Centered container, max-width 640px
- [ ] Desktop 1440px: Centered, proper spacing
- [ ] Buttons have comfortable tap targets on mobile
- [ ] Text does not overflow on small screens

### Transitions & Animations
- [ ] Step transitions are smooth (fade/slide)
- [ ] Progress bar animates smoothly
- [ ] Button hover states work (scale, shadow)
- [ ] Selection feedback is immediate
- [ ] Loading states display correctly
- [ ] No jank or stuttering on transitions
- [ ] Animations respect reduced-motion preference (optional)

### States
- [ ] Idle state: Default appearance
- [ ] Focused state: Input glow/border change
- [ ] Filled state: Visual confirmation of answer
- [ ] Loading state: Spinner overlay, disabled inputs
- [ ] Validation error: Red border + error message
- [ ] API error: User-friendly message + retry option
- [ ] Success state: Confirmation message
- [ ] Final screen: webhook_loading, webhook_success, webhook_error states

### Microcopy
- [ ] Opening: "Descubra em 2 minutos..."
- [ ] CTA: "Quero ser chamado agora"
- [ ] Back: "Voltar" (discreet)
- [ ] Progress: "Passo X de Y"
- [ ] Loading: "Analisando suas respostas..."
- [ ] Success: "Diagnóstico completo!..."
- [ ] Tone is confident, objective, consultative
- [ ] No form-like language ("cadastro", "preencha")

## Technical Testing

### Code Quality
- [ ] PHP CS Fixer passes with no errors
- [ ] No PHP warnings or notices
- [ ] No JavaScript console errors
- [ ] Code follows AGENTS.md conventions
- [ ] Proper error handling throughout
- [ ] No hardcoded secrets or URLs

### Performance
- [ ] Initial page load < 2s on 3G
- [ ] Step transitions < 300ms
- [ ] API responses < 500ms
- [ ] No memory leaks (test with 50+ step navigations)
- [ ] Images/assets optimized

### Security
- [ ] Input validation on both client and server
- [ ] SQL injection prevention (prepared statements)
- [ ] XSS prevention (output escaping)
- [ ] No sensitive data in client-side code
- [ ] Webhook URL not exposed in client code

### Database
- [ ] Table schema matches specification
- [ ] Indexes created for performance
- [ ] Upsert logic works correctly
- [ ] Status transitions are valid
- [ ] No orphaned records

## Cross-Browser Testing

- [ ] Chrome (latest) - Mobile + Desktop
- [ ] Safari (latest) - Mobile + Desktop
- [ ] Firefox (latest) - Desktop
- [ ] Samsung Internet - Mobile

## Edge Cases

- [ ] Page refresh mid-quiz resumes correctly
- [ ] Browser back button behavior (should warn or prevent)
- [ ] Multiple tabs with same session (handle gracefully)
- [ ] Network disconnection during save (retry, preserve locally)
- [ ] Database locked (busy timeout handles it)
- [ ] Invalid session ID (creates new session)
- [ ] Empty answers submitted (blocked by validation)
- [ ] Special characters in name (handled correctly)
- [ ] International phone numbers (Brazilian format enforced)
- [ ] Webhook endpoint returns 500 (retry logic works)
- [ ] Webhook endpoint returns 404 (error logged, status updated)
- [ ] Concurrent webhook attempts (idempotency prevents duplicates)

## Definition of Done

- [ ] All checklist items pass
- [ ] No critical or high-severity bugs
- [ ] Manual walkthrough complete (both tracks)
- [ ] Webhook tested with real endpoint (webhook.site or similar)
- [ ] Mobile testing complete on physical device
- [ ] Code reviewed and approved
- [ ] Ready for production deployment
