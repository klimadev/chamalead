# Hero Section & Transition Overhaul Plan

This plan outlines the visual and technical redesign of the ChamaLead landing page hero, focusing on a premium 'magnetic' experience inspired by Linear and Vercel.

## 1. Visual Hierarchy & Typography
### Typography Scale
- **Headline**: `text-6xl md:text-8xl font-black tracking-tight`. Use `leading-[1.1]` to keep it compact.
- **Subheadline**: `text-xl md:text-2xl text-zinc-400 font-medium`.
- **Primary CTA**: `text-lg font-bold px-10 py-5`.

### Spacing & Rhythm
- Increase the Hero top padding to `pt-32 pb-48`.
- Use `gap-16` in the main grid to create more separation between content and visual demo.

## 2. Background: 'Spotlight' & Mesh Gradients
### The Spotlight
- **Base Layer**: Deep dark background `#030303`.
- **Radial Beam**: A top-centered radial gradient:
  ```css
  background: radial-gradient(circle at 50% -20%, rgba(249, 115, 22, 0.15), transparent 80%);
  ```
- **Dot Grid Mask**: A secondary layer with a dot pattern mask to catch the 'light' of the spotlight.

### Mesh Gradients
- Use 3 background blobs with blur:
  - `bg-flame-500/10` at top-left.
  - `bg-ember-600/10` at center-right.
  - `bg-flame-900/5` at bottom-center.

## 3. iPhone Mockup: Floating Overlap
### Depth Integration
- Position the iPhone using `absolute lg:relative -bottom-20 lg:-bottom-32 z-20`.
- The mockup should 'bleed' into the next section (`#problema`).
- **Glassmorphism**: Apply a subtle reflection overlay to the phone screen and a deep, soft shadow (`shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)]`).

## 4. Seamless Transitions
### Fade-out Mask
- Wrap the transition area in a container with a CSS mask:
  ```css
  mask-image: linear-gradient(to bottom, black 80%, transparent 100%);
  ```
- This avoids the hard border currently present between sections.

### Perspective Divider
- Add an SVG divider with a slight curve or slant that follows the spotlight's trajectory.

## 5. Magnetic CTA Button
### HTML Structure
```html
<div class="magnetic-zone p-10 flex items-center justify-center">
  <button class="magnetic-button btn-primary px-8 py-4 rounded-xl">
    Quero Minha Automação
  </button>
</div>
```

### JavaScript Implementation
```javascript
const zone = document.querySelector('.magnetic-zone');
const btn = document.querySelector('.magnetic-button');

zone.addEventListener('mousemove', (e) => {
  const rect = zone.getBoundingClientRect();
  const x = e.clientX - rect.left - rect.width / 2;
  const y = e.clientY - rect.top - rect.height / 2;
  
  btn.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
});

zone.addEventListener('mouseleave', () => {
  btn.style.transform = `translate(0px, 0px)`;
});
```

## 6. Color Palette Refinement
### Standardized Tokens
- **Flame Core**: `hsl(24, 95%, 53%)`
- **Flame Glow**: `hsla(24, 95%, 53%, 0.15)`
- **Ember Core**: `hsl(0, 72%, 51%)`
- **Dark Neutral**: `hsl(240, 10%, 4%)`

---

## Step-by-Step Implementation Guide

1.  **Step 1: Update Tailwind Config** - Add HSL colors and deep shadow presets.
2.  **Step 2: Hero Background Refactor** - Replace existing blur divs with the Spotlight + Dot Grid system.
3.  **Step 3: Content Scaling** - Update typography with new scale and spacing.
4.  **Step 4: Mockup Positioning** - Move the iPhone container and apply negative bottom positioning.
5.  **Step 5: Apply Transitions** - Use the Fade-out mask on the bottom of the Hero container.
6.  **Step 6: Magnetic Interaction** - Inject the JS snippet into a script tag at the bottom of the page.
