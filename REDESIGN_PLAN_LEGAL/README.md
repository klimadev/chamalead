# REDESIGN PLAN - Sistema Jurídico Landing Page

## Overview

Transformar a proposta comercial em um produto visual moderno, com estética profissional SaaS (Stripe/Notion-inspired). O design deve parecer "product-ready", não "proposal".

---

## 1. Design System

### 1.1 Color Palette

```css
:root {
  /* Backgrounds */
  --bg-primary: #F9F9F7;        /* Soft beige/off-white */
  --bg-surface: #FFFFFF;        /* White surface */
  --bg-surface-alt: #F5F3EF;    /* Slightly darker beige */
  
  /* Primary - Deep Wine/Burgundy */
  --primary-900: #2D0808;
  --primary-800: #4A0E0E;
  --primary-700: #6B1919;
  --primary-600: #8C2424;
  
  /* Accent - Gold */
  --accent-500: #C6A76A;
  --accent-400: #D4B87A;
  --accent-300: #E2C98A;
  
  /* Neutrals */
  --neutral-900: #1A1718;
  --neutral-700: #4F4346;
  --neutral-500: #7F6F72;
  --neutral-300: #C4BAB6;
  --neutral-100: #F1E8DF;
  
  /* Semantic */
  --success: #6F8A62;
  --warning: #B78943;
  --danger: #8B4A52;
}
```

### 1.2 Typography

```css
/* Primary: Manrope (modern, clean) */
font-family: 'Manrope', sans-serif;

/* Display: Cormorant Garamond (elegant, legal) */
font-family: 'Cormorant Garamond', serif;

/* Sizes */
--text-xs: 0.75rem;    /* 12px */
--text-sm: 0.875rem;   /* 14px */
--text-base: 1rem;     /* 16px */
--text-lg: 1.125rem;   /* 18px */
--text-xl: 1.25rem;    /* 20px */
--text-2xl: 1.5rem;    /* 24px */
--text-3xl: 2rem;      /* 32px */
--text-4xl: 2.5rem;    /* 40px */
--text-5xl: 3.5rem;    /* 56px */
```

### 1.3 Spacing System

```css
--space-1: 0.25rem;   /* 4px */
--space-2: 0.5rem;    /* 8px */
--space-3: 0.75rem;   /* 12px */
--space-4: 1rem;      /* 16px */
--space-5: 1.5rem;    /* 24px */
--space-6: 2rem;      /* 32px */
--space-7: 3rem;      /* 48px */
--space-8: 4rem;      /* 64px */
--space-9: 6rem;      /* 96px */
```

---

## 2. Component Library (Tailwind CSS)

### 2.1 Base Components

```html
<!-- Badge (Stripe-style) -->
<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide uppercase bg-neutral-100 text-primary-800">
  Processos organizados
</span>

<!-- Card -->
<div class="p-5 bg-white border border-neutral-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
  <!-- content -->
</div>

<!-- Section Container -->
<section class="max-w-6xl mx-auto px-6 py-20">
  <!-- content -->
</section>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
  <!-- left: mockup -->
  <!-- right: micro-copy -->
</div>
```

### 2.2 Custom Tailwind Config

```javascript
// tailwind.config.js
module.exports = {
  content: ["./propostas/adv/**/*.php"],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#FBECEC',
          100: '#F5DADA',
          200: '#EBB5B5',
          300: '#E19090',
          400: '#D76B6B',
          500: '#CD4646',
          600: '#A83939',
          700: '#8C2C2C',
          800: '#4A0E0E',
          900: '#2D0808',
        },
        accent: {
          300: '#E2C98A',
          400: '#D4B87A',
          500: '#C6A76A',
          600: '#B89452',
        },
        surface: {
          DEFAULT: '#FFFFFF',
          alt: '#F5F3EF',
          pale: '#F9F9F7',
        }
      },
      fontFamily: {
        sans: ['Manrope', 'sans-serif'],
        display: ['Cormorant Garamond', 'serif'],
      },
      borderRadius: {
        'xl': '1rem',
        '2xl': '1.5rem',
      }
    }
  }
}
```

---

## 3. Grid Layout Specifications

### 3.1 Page Structure

```html
<!-- Main container (centered, max-width) -->
<div class="max-w-5xl mx-auto px-6 lg:px-8"></div>

<!-- Section spacing -->
<section class="py-16 lg:py-24"></section>
```

### 3.2 Alternating Sections

| Section | Left | Right |
|---------|------|-------|
| Hero    | -    | Content + Tags |
| Module 1| Mockup| Text |
| Module 2| Text  | Mockup |
| Module 3| Mockup| Text |

```html
<!-- Module Pattern -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
  <!-- Mockup: 7 cols -->
  <div class="lg:col-span-7">...</div>
  
  <!-- Text: 5 cols -->
  <div class="lg:col-span-5">...</div>
</div>
```

---

## 4. Escopo (Landing Page Structure)

### 4.1 Hero Section

```html
<section class="min-h-[70vh] flex items-center justify-center bg-surface-pale pt-20 pb-16">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <!-- Logo placeholder -->
    <div class="w-20 h-20 mx-auto mb-8 bg-white rounded-2xl border border-neutral-200 shadow-lg overflow-hidden">
      <img src="propostas/adv/logo com fundo.png" alt="Logo" class="w-full h-full object-cover" />
    </div>
    
    <!-- Headline -->
    <h1 class="font-display text-4xl lg:text-5xl lg:leading-tight text-primary-900 mb-6">
      Sistema Jurídico Próprio
    </h1>
    
    <!-- Subheadline -->
    <p class="text-xl lg:text-2xl text-neutral-700 mb-8 max-w-2xl mx-auto">
      Controle total da operação jurídica
    </p>
    
    <!-- Tags (Stripe-style badges) -->
    <div class="flex flex-wrap justify-center gap-3 mb-12">
      <span class="px-3 py-1.5 bg-white border border-neutral-200 rounded-full text-sm font-medium text-neutral-700">
        Processos organizados
      </span>
      <span class="px-3 py-1.5 bg-white border border-neutral-200 rounded-full text-sm font-medium text-neutral-700">
        Clientes centralizados
      </span>
      <span class="px-3 py-1.5 bg-white border border-neutral-200 rounded-full text-sm font-medium text-neutral-700">
        Acompanhamento em tempo real
      </span>
    </div>
    
    <!-- Mockup preview (blurred/leightweight) -->
    <div class="relative max-w-3xl mx-auto">
      <div class="aspect-[16/9] bg-white rounded-xl border border-neutral-200 shadow-xl overflow-hidden">
        <!-- Lightweight mockup or blurred preview -->
        <div class="w-full h-full opacity-90" style="background: linear-gradient(135deg, #F5F3EF 0%, #F9F9F7 100%);">
          <!-- Minimal UI representation -->
        </div>
      </div>
    </div>
  </div>
</section>
```

### 4.2 Module 1 - Clientes & Documentos

```html
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-5xl mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      <!-- Mockup (7 cols) -->
      <div class="lg:col-span-7">
        <div class="bg-surface-alt rounded-2xl border border-neutral-200 p-6 shadow-lg">
          <!-- Client list mockup -->
          <div class="flex gap-3 mb-4">
            <div class="w-24 h-4 bg-neutral-300 rounded"></div>
            <div class="w-32 h-4 bg-neutral-200 rounded"></div>
          </div>
          <div class="space-y-3">
            <div class="flex items-center gap-4 p-3 bg-white rounded-lg border border-neutral-100">
              <div class="w-10 h-10 bg-primary-100 rounded-full"></div>
              <div class="flex-1">
                <div class="w-32 h-3 bg-neutral-200 rounded mb-2"></div>
                <div class="w-20 h-2 bg-neutral-100 rounded"></div>
              </div>
            </div>
            <div class="flex items-center gap-4 p-3 bg-white rounded-lg border border-neutral-100">
              <div class="w-10 h-10 bg-primary-100 rounded-full"></div>
              <div class="flex-1">
                <div class="w-32 h-3 bg-neutral-200 rounded mb-2"></div>
                <div class="w-20 h-2 bg-neutral-100 rounded"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Micro-copy (5 cols) -->
      <div class="lg:col-span-5">
        <h2 class="font-display text-3xl lg:text-4xl text-primary-900 mb-4">
          Clientes & Documentos
        </h2>
        <p class="text-lg text-neutral-700 mb-6">
          Quando um cliente entra no escritório, ele já aparece organizado, com tudo junto.
        </p>
        <ul class="space-y-3">
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Dados completos
          </li>
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Histórico completo
          </li>
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Documentos centralizados
          </li>
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Processos vinculados
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>
```

### 4.3 Module 2 - Gestão de Processos

```html
<section class="py-16 lg:py-24 bg-surface-alt">
  <div class="max-w-5xl mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      <!-- Micro-copy (5 cols) -->
      <div class="lg:col-span-5 order-2 lg:order-1">
        <h2 class="font-display text-3xl lg:text-4xl text-primary-900 mb-4">
          Gestão de Processos
        </h2>
        <p class="text-lg text-neutral-700 mb-6">
          Todos os processos em um fluxo visual claro.
        </p>
        <ul class="space-y-3">
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Etapas claramente definidas
          </li>
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Movimentações atualizadas
          </li>
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Status em tempo real
          </li>
        </ul>
      </div>
      
      <!-- Mockup (7 cols) -->
      <div class="lg:col-span-7 order-1 lg:order-2">
        <div class="bg-white rounded-2xl border border-neutral-200 p-6 shadow-lg">
          <!-- Kanban mockup -->
          <div class="grid grid-cols-3 gap-4">
            <div class="p-3 bg-neutral-50 rounded-lg">
              <div class="text-xs font-semibold text-neutral-500 uppercase mb-2">Pendente</div>
              <div class="space-y-2">
                <div class="p-2 bg-white border border-neutral-200 rounded text-xs">Processo A</div>
                <div class="p-2 bg-white border border-neutral-200 rounded text-xs">Processo B</div>
              </div>
            </div>
            <div class="p-3 bg-amber-50 rounded-lg">
              <div class="text-xs font-semibold text-amber-700 uppercase mb-2">Em Andamento</div>
              <div class="space-y-2">
                <div class="p-2 bg-white border border-amber-200 rounded text-xs">Processo C</div>
              </div>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
              <div class="text-xs font-semibold text-green-700 uppercase mb-2">Concluído</div>
              <div class="space-y-2">
                <div class="p-2 bg-white border border-green-200 rounded text-xs">Processo D</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
```

### 4.4 Module 3 - Andamentos / Audiências

```html
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-5xl mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      <!-- Mockup -->
      <div class="lg:col-span-7">
        <div class="bg-surface-alt rounded-2xl border border-neutral-200 p-6 shadow-lg">
          <!-- Timeline/notifications mockup -->
          <div class="space-y-3">
            <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-neutral-100">
              <div class="w-2 h-2 mt-1.5 bg-green-500 rounded-full"></div>
              <div class="flex-1">
                <div class="text-sm font-medium text-neutral-800">Audiência marcada</div>
                <div class="text-xs text-neutral-500">Tribunal de Justiça - 15/04</div>
              </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-neutral-100">
              <div class="w-2 h-2 mt-1.5 bg-amber-500 rounded-full"></div>
              <div class="flex-1">
                <div class="text-sm font-medium text-neutral-800">Prazo em 3 dias</div>
                <div class="text-xs text-neutral-500">Diligência externa</div>
              </div>
            </div>
            <div class="flex items-start gap-3 p-3 bg-white rounded-lg border border-neutral-100">
              <div class="w-2 h-2 mt-1.5 bg-red-500 rounded-full"></div>
              <div class="flex-1">
                <div class="text-sm font-medium text-neutral-800">Novo andamento</div>
                <div class="text-xs text-neutral-500">Petição protocolada</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Micro-copy -->
      <div class="lg:col-span-5">
        <h2 class="font-display text-3xl lg:text-4xl text-primary-900 mb-4">
          Andamentos & Audiências
        </h2>
        <p class="text-lg text-neutral-700 mb-6">
          Nada se perde. Tudo tem acompanhamento.
        </p>
        <ul class="space-y-3">
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Prazos automatizados
          </li>
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Audiências integradas
          </li>
          <li class="flex items-center gap-3 text-neutral-700">
            <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
            Alertas proativos
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>
```

### 4.5 Final - Próximos Passos

```html
<section class="py-16 lg:py-24 bg-primary-900 text-white">
  <div class="max-w-3xl mx-auto px-6 text-center">
    <h2 class="font-display text-3xl lg:text-4xl mb-8">
      Próximos passos
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="p-6 bg-white/10 rounded-xl border border-white/20">
        <div class="text-2xl font-semibold mb-2">1</div>
        <div class="font-medium">Aprovação</div>
        <div class="text-sm text-white/70 mt-2">Você aprova o escopo</div>
      </div>
      <div class="p-6 bg-white/10 rounded-xl border border-white/20">
        <div class="text-2xl font-semibold mb-2">2</div>
        <div class="font-medium">Configuração</div>
        <div class="text-sm text-white/70 mt-2">Setup inicial do sistema</div>
      </div>
      <div class="p-6 bg-white/10 rounded-xl border border-white/20">
        <div class="text-2xl font-semibold mb-2">3</div>
        <div class="font-medium">Entrega</div>
        <div class="text-sm text-white/70 mt-2">Sistema rodando em dias</div>
      </div>
    </div>
  </div>
</section>
```

---

## 5. Financeiro (Pricing Page Structure)

### 5.1 Hero (Calm, Neutral)

```html
<section class="min-h-[50vh] flex items-center bg-surface-pale py-16 lg:py-24">
  <div class="max-w-3xl mx-auto px-6 text-center">
    <h1 class="font-display text-4xl lg:text-5xl text-primary-900 mb-6">
      Investimento para estrutura própria
    </h1>
    <p class="text-xl text-neutral-700">
      Base sólida, sem dependência externa
    </p>
  </div>
</section>
```

### 5.2 Breakdown Table (Notion-style)

```html
<section class="py-16 lg:py-24 bg-white">
  <div class="max-w-2xl mx-auto px-6">
    <h2 class="font-display text-2xl text-primary-900 mb-8">
      Composição do investimento
    </h2>
    
    <div class="border border-neutral-200 rounded-xl overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="bg-surface-alt">
            <th class="px-6 py-4 text-left text-sm font-semibold text-neutral-700">Item</th>
            <th class="px-6 py-4 text-right text-sm font-semibold text-neutral-700">Valor</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
          <tr class="bg-white">
            <td class="px-6 py-4 text-neutral-800">Módulo Clientes</td>
            <td class="px-6 py-4 text-right font-medium text-neutral-800">R$ 1.200</td>
          </tr>
          <tr class="bg-surface-alt/50">
            <td class="px-6 py-4 text-neutral-800">Módulo Processos</td>
            <td class="px-6 py-4 text-right font-medium text-neutral-800">R$ 1.500</td>
          </tr>
          <tr class="bg-white">
            <td class="px-6 py-4 text-neutral-800">Módulo Acompanhamento</td>
            <td class="px-6 py-4 text-right font-medium text-neutral-800">R$ 1.000</td>
          </tr>
          <tr class="bg-surface-alt/50">
            <td class="px-6 py-4 text-neutral-800">Estrutura Base</td>
            <td class="px-6 py-4 text-right font-medium text-neutral-800">R$ 800</td>
          </tr>
          <tr class="bg-white">
            <td class="px-6 py-4 text-neutral-800">Setup & Configuração</td>
            <td class="px-6 py-4 text-right font-medium text-neutral-800">R$ 500</td>
          </tr>
          <tr class="bg-primary-50">
            <td class="px-6 py-4 font-semibold text-primary-900">Total</td>
            <td class="px-6 py-4 text-right font-bold text-xl text-primary-900">R$ 5.000</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
```

### 5.3 Variable Costs

```html
<section class="py-12 bg-surface-alt">
  <div class="max-w-2xl mx-auto px-6">
    <h3 class="text-lg font-medium text-neutral-700 mb-6">
      Custos operacionais (após 1 ano)
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
      <div class="p-4 bg-white rounded-lg border border-neutral-200">
        <div class="text-sm text-neutral-500 mb-1">Servidor</div>
        <div class="text-xl font-semibold text-neutral-800">R$ 50<span class="text-sm font-normal text-neutral-500">/mês</span></div>
      </div>
      <div class="p-4 bg-white rounded-lg border border-neutral-200">
        <div class="text-sm text-neutral-500 mb-1">Base de dados</div>
        <div class="text-xl font-semibold text-neutral-800">R$ 30<span class="text-sm font-normal text-neutral-500">/mês</span></div>
      </div>
    </div>
    
    <p class="text-xs text-neutral-500 mt-4">* Valores estimados, sujeito a ajuste conforme uso.</p>
  </div>
</section>
```

### 5.4 1-Year Free Highlight (Featured)

```html
<section class="py-16 lg:py-20 bg-gradient-to-br from-primary-800 to-primary-900 text-white">
  <div class="max-w-3xl mx-auto px-6 text-center">
    <div class="inline-block px-3 py-1 mb-4 text-xs font-semibold tracking-wider uppercase bg-accent-500 text-primary-900 rounded-full">
      Incluso no projeto
    </div>
    
    <h2 class="font-display text-3xl lg:text-4xl mb-6">
      1 ano de infraestrutura sem custo
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
      <div class="p-5 bg-white/10 rounded-xl border border-white/20">
        <div class="flex justify-center mb-3">
          <svg class="w-6 h-6 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <div class="font-medium">Servidor incluso</div>
        <div class="text-sm text-white/70 mt-1">Primeiro ano Grátis</div>
      </div>
      <div class="p-5 bg-white/10 rounded-xl border border-white/20">
        <div class="flex justify-center mb-3">
          <svg class="w-6 h-6 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <div class="font-medium">Banco de dados incluso</div>
        <div class="text-sm text-white/70 mt-1">Primeiro ano Grátis</div>
      </div>
      <div class="p-5 bg-white/10 rounded-xl border border-white/20">
        <div class="flex justify-center mb-3">
          <svg class="w-6 h-6 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <div class="font-medium">Sistema completo</div>
        <div class="text-sm text-white/70 mt-1">Sem custo adicional</div>
      </div>
    </div>
    
    <p class="text-lg text-white/80 mt-8">
      Sem custo adicional no primeiro ano
    </p>
  </div>
</section>
```

---

## 6. Responsive Breakpoints

```css
/* Mobile First */

/* sm: 640px */
@media (min-width: 640px) {
  /* adjustments */
}

/* md: 768px - Tablet */
@media (min-width: 768px) {
  /* adjustments */
}

/* lg: 1024px - Desktop */
@media (min-width: 1024px) {
  /* Default styling for desktop */
}

/* xl: 1280px */
@media (min-width: 1280px) {
  /* Max content width considerations */
}
```

---

## 7. Asset Management

### 7.1 Mockup Strategy

```html
<!-- Option 1: Simplified CSS-only mockups -->
<!-- Lightweight, no image assets needed -->

<!-- Option 2: Lightweight blurred previews -->
<div class="opacity-90 blur-sm">
  <!-- Blurred UI representation -->
</div>

<!-- Option 3: SVG-based mockups -->
<!-- Scalable, maintainable -->
```

### 7.2 Directory Structure

```
propostas/adv/
├── escopo/
│   ├── index.php
│   └── modules/
│       └── ...
├── financeiro/
│   ├── index.php
│   └── modules/
│       └── ...
└── assets/
    ├── css/
    │   └── redesign.css
    ├── js/
    │   └── redesign.js
    └── images/
        └── (place mockup images here)
```

---

## 8. Implementation Checklists

### 8.1 For AI Developer

1. **Setup Phase**
   - [x] Create Tailwind config with custom colors
   - [x] Import Manrope + Cormorant Garamond fonts
   - [x] Set up base CSS variables

2. **Escopo Page**
   - [x] Hero with tags + mockup preview
   - [x] Module 1 (Clients) - alternating layout
   - [x] Module 2 (Processes) - alternating layout  
   - [x] Module 3 (Audiences) - alternating layout
   - [x] Next Steps section

3. **Financeiro Page**
   - [x] Calm hero section
   - [x] Breakdown table (Notion-style)
   - [x] Variable costs section
   - [x] 1-Year Free highlight (featured)

4. **Polish**
   - [x] Responsive verification
   - [x] Performance optimization
   - [x] Accessibility check

### 8.2 Key Principles to Remember

| Principle | Implementation |
|-----------|----------------|
| Mockup as protagonist | Image/mockup takes 60%+ of section width |
| Text as support | Max 2 lines + bullet points |
| 1 focus per screen | Single module/theme per section |
| Visual hierarchy | Tags → Headline → Micro-copy |
| "Obvious" not "explained" | No explanatory paragraphs |
| Clean spacing | py-16 lg:py-24 sections |

---

## 9. File Structure for Implementation

```
propostas/adv/
├── escopo/
│   ├── index.php          (update to use new templates)
│   └── modules/
│       ├── hero.php       (NEW - simplified hero)
│       ├── module-1.php   (NEW - clients)
│       ├── module-2.php   (NEW - processes)
│       ├── module-3.php   (NEW - audiences)
│       └── next-steps.php (NEW - closing)
├── financeiro/
│   ├── index.php          (update)
│   └── modules/
│       ├── hero.php       (NEW - calm pricing hero)
│       ├── breakdown.php  (NEW - Notion table)
│       ├── costs.php      (NEW - variable costs)
│       └── highlight.php  (NEW - 1 year free)
└── styles/
    └── redesign.css       (NEW - custom styles)
```

---

## 10. Quick Start Commands

```bash
# Install Tailwind (if using build step)
npm install -D tailwindcss

# Or use CDN in existing PHP files
<script src="https://cdn.tailwindcss.com"></script>

# Verify Tailwind config
npx tailwindcss init

# Build CSS
npx tailwindcss -i ./styles/input.css -o ./styles/output.css
```

---

*Document generated based on briefing requirements and UI research.*
*Design inspiration: Stripe, Notion, modern legal SaaS patterns*