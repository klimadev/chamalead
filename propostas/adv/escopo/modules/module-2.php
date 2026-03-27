<section class="relative">
  <div class="max-w-7xl mx-auto px-6 py-16 lg:py-24">
    <div data-reveal class="grid items-start gap-12 xl:grid-cols-12 xl:gap-14">
      <div class="order-2 xl:order-1 xl:col-span-5">
        <span class="inline-flex items-center rounded-full border border-primary-900/10 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-primary-800">Módulo 02</span>
        <h2 class="mt-5 text-3xl font-semibold tracking-[-0.06em] text-primary-900 sm:text-4xl">Gestão de processos</h2>
        <p class="mt-4 max-w-md text-base leading-7 text-neutral-700">Atribuição, histórico devisualização e notificações.</p>
        <div class="mt-6 flex flex-wrap gap-2.5">
          <span class="inline-flex items-center rounded-full bg-surface-alt px-3 py-2 text-sm font-medium text-neutral-700">Advogado relacionado</span>
          <span class="inline-flex items-center rounded-full bg-surface-alt px-3 py-2 text-sm font-medium text-neutral-700">Visto ou não visto</span>
          <span class="inline-flex items-center rounded-full bg-surface-alt px-3 py-2 text-sm font-medium text-neutral-700">Notificações</span>
        </div>
        <div class="mt-8 space-y-3">
          <article class="rounded-[24px] border border-primary-900/10 bg-white p-4 shadow-panel">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-neutral-500">atribuição</p>
            <p class="mt-2 text-lg font-semibold tracking-[-0.04em] text-primary-900">Advogado vinculado ao caso</p>
          </article>
          <article class="rounded-[24px] border border-primary-900/10 bg-white p-4 shadow-panel">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-neutral-500">rastreamento</p>
            <p class="mt-2 text-lg font-semibold tracking-[-0.04em] text-primary-900">Sabe se foi visto ou não</p>
          </article>
        </div>
      </div>

      <div class="order-1 xl:order-2 xl:col-span-7">
        <div data-slot="process-mockup" class="mockup-shell rounded-[34px] border border-primary-900/10 bg-white p-6 shadow-panel xl:p-8">
          <div class="glass-line flex items-center justify-between gap-3 rounded-[20px] border border-primary-900/10 px-4 py-3">
            <div>
              <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-neutral-500">gestão de processos</p>
              <h3 class="mt-1 text-xl font-semibold tracking-[-0.05em] text-primary-900">Processos com attribuição</h3>
            </div>
            <span class="rounded-full bg-accent-400/20 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-primary-800">MOCKUP</span>
          </div>
          <div class="mt-6 grid gap-6 xl:grid-cols-[1.12fr,0.88fr]">
            <article class="rounded-[28px] border border-primary-900/10 bg-surface-alt p-6">
              <div class="space-y-4">
                <div class="rounded-[22px] bg-white p-4 border border-primary-900/10">
                  <div class="flex items-center justify-between gap-3">
                    <div>
                      <p class="text-sm font-semibold text-primary-900">0009128-24.2025</p>
                      <p class="mt-1 text-sm text-neutral-500">Ação cível · Dra. Lídia</p>
                    </div>
                    <span class="rounded-full bg-[#eef5ec] px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#5d7b54]">visto</span>
                  </div>
                </div>
                <div class="rounded-[22px] bg-white p-4 border border-primary-900/10">
                  <div class="flex items-center justify-between gap-3">
                    <div>
                      <p class="text-sm font-semibold text-primary-900">0008124-24.2025</p>
                      <p class="mt-1 text-sm text-neutral-500">Trabalhista · Dr. Carlos</p>
                    </div>
                    <span class="rounded-full bg-accent-400/20 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-primary-800">não visto</span>
                  </div>
                </div>
                <div class="rounded-[22px] bg-white p-4 border border-primary-900/10">
                  <div class="flex items-center justify-between gap-3">
                    <div>
                      <p class="text-sm font-semibold text-primary-900">0011448-24.2025</p>
                      <p class="mt-1 text-sm text-neutral-500">Criminal · Dra. Lídia</p>
                    </div>
                    <span class="rounded-full bg-[#eef5ec] px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#5d7b54]">visto</span>
                  </div>
                </div>
              </div>
            </article>

            <div class="grid gap-5">
              <article class="rounded-[28px] border border-primary-900/10 bg-white p-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-neutral-500">atribuição</p>
                <h3 class="mt-2 text-xl font-semibold tracking-[-0.05em] text-primary-900">Dra. Lídia</h3>
                <div class="mt-4 rounded-[18px] bg-surface-alt px-4 py-3 text-sm text-neutral-600">OAB/SP 123.456</div>
              </article>

              <article class="rounded-[28px] border border-primary-900/10 bg-primary-900 p-5 text-white">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/55">notificações</p>
                <div class="mt-5 space-y-3 text-sm text-white/75">
                  <div class="rounded-[18px] bg-white/10 px-4 py-3">Novo andamento · 0009128</div>
                  <div class="rounded-[18px] bg-white/10 px-4 py-3">Prazo 48h · 0008124</div>
                  <div class="rounded-[18px] bg-white/10 px-4 py-3">Audiência confirmada · 0011448</div>
                </div>
              </article>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
