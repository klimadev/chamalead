# Quiz Partials Map

This folder is organized by domain to keep the quiz code easy to navigate without changing behavior.

## Entry wrappers

- `head.php`: loads document metadata, theme, and style blocks.
- `layout.php`: loads static HTML structure and step markup.
- `app-script.php`: loads runtime, flow, and result JavaScript.

## Head modules

- `head/foundation/document-and-theme-bootstrap.php`: document opening, pixel bootstrap, Tailwind config, CSS token foundation.
- `head/foundation/progress-and-shell-styles.php`: header shell, progress bar, and container UI styles.
- `head/form/form-and-option-styles.php`: fields, buttons, and option interactions.
- `head/result/result-core-styles.php`: result card core visuals and animations.
- `head/result/result-responsive-and-footer-styles.php`: result responsiveness and footer/CTA behaviors.

## Layout modules

- `layout/steps/core-shell-and-context-steps.php`: body shell and early/context steps.
- `layout/steps/pain-detail-urgency-result-footer.php`: pain detail steps, urgency, result, and footer.

## Script modules

- `script/runtime/state-persistence-and-bootstrap.php`: constants, session state, persistence, and runtime bootstrap.
- `script/runtime/progress-guidance-and-layout-fit.php`: progress UI, guidance copy, and no-scroll layout fit engine.
- `script/flow/interaction-validation-and-submit.php`: event binding, field validation, step transitions, and API submit.
- `script/result/result-render-and-score-engine.php`: preview/result rendering and score animation engine.

## Constraint

- Keep each quiz file at or below 400 lines.
