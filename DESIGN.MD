# Design System Document: High-End Digital Editorial

## 1. Overview & Creative North Star: "The Obsidian Architect"
This design system is built to transcend the "standard SaaS" aesthetic. Our Creative North Star is **The Obsidian Architect** - a philosophy that treats digital space as a physical environment crafted from dark, high-end materials like polished stone and tinted glass.

We break the "template" look by rejecting traditional grids and harsh borders in favor of **Tonal Depth**. By using intentional asymmetry, overlapping card elements, and a high-contrast typographic scale (pairing the technical precision of Inter with the geometric character of Space Grotesk), we create a premium, authoritative experience that feels custom-tailored rather than assembled.

---

## 2. Colors: Tonal Depth vs. Structural Lines
The palette is rooted in a deep, monochromatic base (`background: #141313`) energized by "Ignition Colors" - vibrant oranges and orange-reds that signify momentum and conversion.

### Core Color Tokens

```css
:root {
  --background: #141313;
  --surface: #141313;
  --surface-container-low: #1c1b1b;
  --surface-container: #201f1f;
  --surface-container-highest: #353434;
  --surface-bright: #3f3d3d;

  --primary: #ffb690;
  --primary-container: #f97316;
  --secondary-container: #4a2f24;
  --tertiary: #4ae176;

  --on-surface: #e5e2e1;
  --on-surface-muted: #b9b4b2;
  --outline-variant: #9a8f89;
}
```

### The "No-Line" Rule
**Explicit Instruction:** Prohibit 1px solid borders for sectioning or container definition. Boundaries must be defined through background color shifts. To separate a section, transition from `surface` to `surface-container-low`.

### Surface Hierarchy & Nesting
Treat UI as stacked layers. Use `surface-container` tiers to define importance through perceived elevation:
- **Base Layer:** `surface` (`#141313`)
- **Secondary Sectioning:** `surface-container-low` (`#1c1b1b`)
- **Primary Cards:** `surface-container` (`#201f1f`)
- **Floating/Active Elements:** `surface-container-highest` (`#353434`)

### The "Glass & Gradient" Rule
To inject soul into the interface:
- **Glassmorphism:** Use semi-transparent `surface-bright` with `backdrop-blur` (12px-20px) for navigation bars and floating modals.
- **Signature Gradients:** Main CTAs and high-impact hero backgrounds must use linear gradient from `primary` (`#ffb690`) to `primary-container` (`#f97316`) to create glow over flat fill.

---

## 3. Typography: Editorial Authority
We use a dual-typeface system to balance technical reliability with modern tech-forward character.

- **Space Grotesk (Display & Headline):** For expressive titles and editorial hero messaging.
- **Inter (Title, Body, Label):** For maximum legibility in UI, forms, and functional text.

### Type Scale

```css
:root {
  --font-display: "Space Grotesk", sans-serif;
  --font-body: "Inter", sans-serif;

  --display-lg-size: 3.5rem;
  --display-lg-line: 1.05;
  --display-lg-tracking: -0.02em;

  --headline-lg-size: 2.25rem;
  --headline-lg-line: 1.15;

  --body-lg-size: 1rem;
  --body-lg-line: 1.6;

  --label-md-size: 0.75rem;
  --label-md-line: 1.4;
  --label-md-tracking: 0.08em;
}
```

### Hierarchy Strategy
- **Primary Messaging:** `headline-lg` in Space Grotesk.
- **Narrative Flow:** `body-lg` with at least 1.6 line-height.
- **Technical Detail:** `label-md` in Inter, uppercase, increased tracking.

---

## 4. Elevation & Depth: Tonal Layering
Traditional shadows are a fallback, not the default. This system prioritizes **Tonal Layering**.

- **Layering Principle:** Place a `surface-container-lowest` card on a `surface-container-low` section to create natural lift.
- **Ambient Shadows:** When floating is required, use diffused shadow only: `0 20px 40px rgba(0, 0, 0, 0.4)`.
- **Ghost Border Fallback:** If accessibility requires edge definition, use `outline-variant` at 15%-20% opacity.
- **Backdrop Blur:** Floating nav uses `surface` at 70% opacity plus blur.

```css
.elevation-floating {
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.ghost-border {
  border: 1px solid color-mix(in srgb, var(--outline-variant) 20%, transparent);
}

.glass {
  background: color-mix(in srgb, var(--surface-bright) 30%, transparent);
  backdrop-filter: blur(16px);
}
```

---

## 5. Components: Precision & Conversion

### Buttons
- **Primary:** Gradient fill (`primary` -> `primary-container`), radius `md`, text in Inter Bold.
- **Secondary:** `surface-container-highest` + Ghost Border.
- **Tertiary:** No fill, text in `primary`, subtle glow on hover.

### Cards (Core Pattern)
- **Constraint:** Never use divider lines.
- **Style:** `surface-container` background, internal separation by spacing (`16px`, `24px`, `32px`).
- **Accent:** Optional 2px left accent bar in `tertiary` (`#4ae176`) for success/live contexts.

### Input Fields
- **Background:** `surface-container-low`.
- **Border:** Ghost Border.
- **Focus:** Border to `primary` plus subtle outer glow.

### Chips & Tags
- Selection chips use `secondary-container` background with readable contrasting text.
- Roundedness should be `full` (`9999px`) for friendly modern tone.

### Lists
- Use zebra striping between `surface` and `surface-container-low`.
- Do not separate rows with horizontal lines.

---

## 6. Layout Principles (Applicable to Any Product)

### Editorial Flow
- Prefer asymmetrical compositions where text blocks are offset from media blocks.
- Break repetitive card grids with occasional span or overlap.
- Use negative space as a first-class design element.

### Rhythm & Spacing

```css
:root {
  --space-2: 0.125rem;
  --space-4: 0.25rem;
  --space-8: 0.5rem;
  --space-12: 0.75rem;
  --space-16: 1rem;
  --space-24: 1.5rem;
  --space-32: 2rem;
  --space-48: 3rem;
  --space-64: 4rem;
}
```

If a section feels crowded, increase vertical padding by 2x before introducing visual separators.

### Roundedness

```css
:root {
  --radius-md: 0.375rem;
  --radius-lg: 0.5rem;
  --radius-xl: 0.75rem;
  --radius-full: 9999px;
}
```

No sharp corners for containers and interactive elements.

---

## 7. Motion & Interaction

### Motion Philosophy
Motion should clarify hierarchy and state change, not decorate randomly.

- Entrance animations: 240ms-420ms, ease-out.
- Hover transitions: 140ms-220ms.
- Large shifts use transform + opacity only.
- Respect `prefers-reduced-motion`.

```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 1ms !important;
    transition-duration: 1ms !important;
    scroll-behavior: auto !important;
  }
}
```

---

## 8. Accessibility Guardrails

- Never use pure white (`#ffffff`) for body text on dark surfaces.
- Default body text should use `on-surface` (`#e5e2e1`).
- Maintain minimum contrast ratio for all text and control states.
- Focus states must be visible beyond color-only change.
- Keyboard navigation must be complete for menus, modals, forms, and accordions.

---

## 9. Do's and Don'ts

### Do
- Use tonal surfaces to define structure.
- Use `tertiary` (`#4ae176`) sparingly for trust/status.
- Keep generous negative space.
- Build visual hierarchy via typography first, color second.

### Don't
- Do not use section divider lines.
- Do not use high-opacity generic drop shadows.
- Do not use sharp corners in UI containers.
- Do not default to symmetrical, repetitive card grids.

---

## 10. Adoption Checklist (Any System)

1. Define tokens (`color`, `type`, `spacing`, `radius`, `motion`) in a single source.
2. Replace line-based separators with tonal sectioning.
3. Upgrade primary CTA to gradient + glow behavior.
4. Standardize card, input, button, chip variants using this spec.
5. Validate accessibility (contrast, focus, keyboard, reduced motion).
6. Run visual QA on desktop and mobile before release.

This design system is product-agnostic by intent. It can be applied to dashboards, SaaS apps, lead funnels, admin panels, marketplaces, and internal tools while preserving a premium, editorial, and high-conviction visual identity.
