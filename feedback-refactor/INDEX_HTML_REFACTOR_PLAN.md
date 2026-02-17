# Feedback Refactor Plan for `index.html`

Status: planning only (no UI/code refactor applied yet)

## 1) Scope Mapping (exact targets in current file)

- Main headline: `index.html:715-719` (`<h1>`)
- Section titles (`<h2>`): `index.html:907`, `index.html:1040`, `index.html:1187`, `index.html:1333`, `index.html:1438`, `index.html:1581`
- Solution cards (left column): `index.html:1053-1090`
- Benefits list cards (right column): `index.html:1102-1156`
- Pricing setup line: `index.html:1165`
- Pricing comparison gray text: `index.html:1006`, `index.html:1014`, `index.html:1021`
- iPhone visual classes in CSS: `.iphone-container`, `.iphone-frame`, `.iphone-border`, `.iphone-deep-shadow`, `.glass-reflection`, `.dynamic-island` at `index.html:400-450` and `index.html:608-625`
- iPhone markup wrapper/frame nodes: `index.html:768-775`
- Form submit success timing: `index.html:1926-1998` (timeouts at `1966`, `1971`)
- Case-study owner notification copy: `index.html:1240-1242`, plus chat confirmation text at `index.html:1837`
- List items (`<li>`) to audit punctuation: `index.html:980-995`, `index.html:1103-1156`, `index.html:1680-1696`

## 2) Research Summary -> practical CSS decisions

### A) Glassmorphism + high-contrast text

Based on accessibility guidance for frosted/glass UI on dark backgrounds:

- Keep blur effect, but increase foreground stability (semi-opaque backing behind text areas)
- Promote muted text from `zinc-500` to at least `zinc-400` (prefer `zinc-300` in critical pricing content)
- Use stronger borders on glass containers (`white/10` -> `white/15` or similar) to separate foreground from background noise
- Add subtle text contrast reinforcement only where needed (very light `text-shadow` on tiny text)

Classes to adjust in this file:

- `.glass` (background alpha and border alpha)
- `.glass-orange` (same principle for consistency)
- Direct utility classes in pricing compare area: `text-zinc-500` -> `text-zinc-300` (or `text-zinc-400` fallback)

### B) CSS 3D phone mockup depth

For a less flat and more realistic device look:

- Layer 3 shadows (ambient + key + glow) on outer frame container
- Metallic frame gradient with stronger contrast stops (dark graphite -> steel highlight -> dark graphite)
- Add inner bevel/rim lighting (inset highlights + inset shadows)
- Optional pseudo-element specular highlight for realistic glass reflection
- Keep `perspective` and tilt behavior, but reduce overly flat look by strengthening Z cues

Classes to adjust in this file:

- `.iphone-container` (drop-shadow stack)
- `.iphone-frame` (metallic gradient + outer shadow stack)
- `.iphone-border` (bevel/inner rim)
- `.iphone-deep-shadow` (deeper multi-layer shadow)
- `.glass-reflection` (more natural angled highlight)
- `.dynamic-island` (subtle depth and separation)

## 3) Planned Refactor Steps (implementation order)

### Step 1 - Copywriting/title case normalization

1. Normalize `<h1>/<h2>` text to title case while keeping PT-BR prepositions/conjunctions lowercase (`de`, `da`, `do`, `e`, `que`, `por`, etc.).
2. Ensure main hero line keeps `24 Horas por Dia` exactly in this casing.
3. Explicitly ensure any headline instance of `Que` used as conjunction becomes `que`.

Notes:
- Use a deterministic rule: first and last words capitalized; connector words lowercase unless first word.

### Step 2 - Solution and benefits card copy updates

1. `Análise & Estratégia` card (`index.html:1058-1060`):
   - Add explicit badge text `BASEADO EM NEUROMARKETING` (recommended), or append to title if badge spacing breaks.
2. Tokens item (`index.html:1126-1128`):
   - Replace supporting copy with exactly: `Mais leads, mais lucro... somente isso`
3. Entrega item (`index.html:1144`):
   - Change title to: `Entrega em 48 Horas Garantida`
4. Pricing setup line (`index.html:1165`):
   - Replace `Setup unico`/`Setup unico de ...` wording with `Implementacao` variant:
   - Final text target: `Implementacao de R$ 3.000 (parcelavel)`

### Step 3 - Remove periods from all list items

1. Audit every `<li>` block in file (3 list clusters identified).
2. Remove terminal `.` only when punctuation is at end of list item text.
3. Keep punctuation inside abbreviations/numbers if not terminal sentence period.

Current state note:
- Existing `<li>` items appear without ending period already; keep this step as a guard check to prevent regressions.

### Step 4 - Pricing readability/contrast fix

1. In pricing comparison box (`index.html:1006`, `1014`, `1021`), upgrade text contrast:
   - `text-zinc-500` -> `text-zinc-300` (preferred)
2. If balance becomes too bright, use `text-zinc-400` for secondary line and keep heading at `text-zinc-300`.
3. Optional: if still low contrast over gradient glow, add a shared utility class (example: `.muted-contrast`) with `color: rgb(212 212 216)`.

### Step 5 - iPhone mockup depth pass

1. Strengthen `.iphone-frame` metallic gradient stops (cool gray/graphite bands).
2. Upgrade `.iphone-frame` shadow stack for depth:
   - close hard shadow, mid blur shadow, long ambient shadow, subtle accent glow
3. Upgrade `.iphone-border` inset lighting to simulate chamfered edge.
4. Adjust `.glass-reflection` to asymmetric highlight sweep (top-left to bottom-right) with lower opacity extremes.
5. Keep corner radii but fine-tune frame/screen radius difference for physical thickness perception.
6. Keep `tilt` effect intact; only tune visual depth classes.

### Step 6 - Form success feedback speed

1. In submit listener success block (`index.html:1966-1978`):
   - reduce alert delay from `500ms` to `100-200ms` (or immediate)
   - reduce button reset delay from `3000ms` to `800-1000ms`
2. Keep success text visible briefly, but do not block user from second interaction.
3. Keep error flow unchanged unless UX consistency requires matching timeout.

### Step 7 - Immediate owner notification copy

1. Update case-study owner notification step (`index.html:1241`) to explicitly say immediate channel delivery.
2. Suggested final copy pattern:
   - `O proprietario recebe notificacao imediata por WhatsApp/SMS com evento agendado, valor recebido e dados do cliente`
3. Mirror same idea in final chat confirmation (`index.html:1837`) to keep narrative consistency.

## 4) Proposed class-level delta list

Primary classes expected to be edited:

- `.glass`
- `.glass-orange`
- `.iphone-container`
- `.iphone-frame`
- `.iphone-border`
- `.iphone-deep-shadow`
- `.glass-reflection`
- `.dynamic-island`

Primary utility replacements expected in markup:

- `text-zinc-500` -> `text-zinc-300` or `text-zinc-400` (only in targeted pricing comparison texts)

## 5) Validation checklist (for execution phase)

1. Content QA
   - Requested strings match exactly (especially tokens sentence and `Entrega em 48 Horas Garantida`)
2. Visual QA
   - Pricing gray texts readable at normal distance on dark background
   - iPhone has visible depth on desktop and mobile breakpoints
3. Behavior QA
   - Form success feels snappy (<= 1s to reset interaction state)
4. Regression QA
   - No accidental punctuation added at end of `<li>` items
   - Existing animations and layout remain stable

## 6) Execution boundaries

- This plan is limited to `index.html` refactor points requested by client feedback.
- No backend/API logic changes are required for this pass.
- No implementation has been applied yet in this planning phase.
