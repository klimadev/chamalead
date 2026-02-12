/**
 * Personalizador de Segmentos - Versão Simplificada
 * Apenas substitui textos específicos, mantendo todo o HTML/estilo intacto
 */

(function() {
  'use strict';

  // Verifica se a configuração existe
  if (!window.SEGMENTOS_CONFIG) {
    console.warn('Configuração de segmentos não encontrada');
    return;
  }

  // Obtém o segmento da URL
  function getSegmento() {
    const params = new URLSearchParams(window.location.search);
    return params.get('segmento')?.toLowerCase().trim();
  }

  // Aplica personalizações ao documento
  function aplicarPersonalizacoes(segmento) {
    const config = window.SEGMENTOS_CONFIG[segmento];
    
    if (!config) {
      console.log('Segmento não configurado, usando conteúdo padrão:', segmento);
      return;
    }

    console.log('Aplicando personalização simplificada para:', segmento);

    // 1. Substitui "EMPRESAS" no headline mantendo todo o HTML
    if (config.termoSegmento) {
      const heroHeadline = document.querySelector('[data-segmento="hero-headline"]');
      if (heroHeadline) {
        // Faz replace apenas do texto, mantendo spans/classes
        heroHeadline.innerHTML = heroHeadline.innerHTML.replace(/EMPRESAS/g, config.termoSegmento);
        heroHeadline.classList.add('segmento-personalizado');
      }
    }

    // 2. Substitui badge
    if (config.badgeTexto) {
      const badge = document.querySelector('[data-segmento="hero-badge"]');
      if (badge) {
        badge.textContent = config.badgeTexto;
      }
    }

    // 3. Substitui textos dos cards mantendo estilo
    if (config.cards) {
      const card1 = document.querySelector('[data-segmento="card-1"]');
      const card2 = document.querySelector('[data-segmento="card-2"]');
      const card3 = document.querySelector('[data-segmento="card-3"]');
      const card4 = document.querySelector('[data-segmento="card-4"]');

      if (card1 && config.cards.card1) card1.textContent = config.cards.card1;
      if (card2 && config.cards.card2) card2.textContent = config.cards.card2;
      if (card3 && config.cards.card3) card3.textContent = config.cards.card3;
      if (card4 && config.cards.card4) card4.textContent = config.cards.card4;
    }

    // 4. Marca body com classe do segmento
    document.body.classList.add('segmento-' + segmento);
    
    // 5. Dispara evento para analytics
    window.dispatchEvent(new CustomEvent('segmento:carregado', { 
      detail: { segmento: segmento, config: config } 
    }));
  }

  // Inicializa quando DOM estiver pronto
  function init() {
    const segmento = getSegmento();
    if (segmento && window.SEGMENTOS_CONFIG[segmento]) {
      aplicarPersonalizacoes(segmento);
    }
  }

  // Executa imediatamente ou aguarda DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Expõe API global
  window.Personalizador = {
    aplicar: aplicarPersonalizacoes,
    getSegmentoAtual: getSegmento,
    segmentosDisponiveis: () => Object.keys(window.SEGMENTOS_CONFIG).filter(k => k !== 'default')
  };

})();
