# Spec: pagespeed-mobile

Scope: feature

# Feature Spec: Pagespeed Mobile

## Goal
Improve `quiz2` performance on mobile without regressing the already strong desktop experience, keeping the premium dark UI intact.

## Problem
Lighthouse shows mobile performance at 87 with high TBT and render delay dominated by style/layout and initial paint cost. Desktop is already near optimal.

## Requirements
- Reduce mobile render delay and main-thread work on initial load.
- Keep accessibility, SEO, and best-practices at 100.
- Preserve the current visual identity and quiz behavior.
- Avoid external CDNs, web fonts, or heavy dependencies.
- Keep markup, CSS, and JS lean.

## Success Criteria
- Mobile Lighthouse performance increases meaningfully from the current baseline.
- Desktop remains at or near current 99 performance.
- No functional regressions in the quiz flow, start screen, or result screen.
- Critical CSS remains inline; noncritical work is deferred.

## Constraints
- Follow repository AGENTS guidance: dark premium UI, performance-first, accessible, no external resources.
- Prefer minimal, targeted changes over broad refactors.
- Validate with Lighthouse after implementation.