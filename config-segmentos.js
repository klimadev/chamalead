// Configuração de Personalização por Segmento - Versão Simplificada
// Apenas substituições de texto específicas, mantendo todo o estilo original

const SEGMENTOS_CONFIG = {
  consorcio: {
    // Substitui "EMPRESAS" no headline
    termoSegmento: "ADMINISTRADORAS",
    
    // Badge no hero
    badgeTexto: "Para Administradoras de Consórcio",
    
    // Cards de stats (apenas os textos, mantém números e ícones)
    cards: {
      card1: "Consórcios Digitalizados",
      card2: "Prazo de Entrega",
      card3: "Admins. Satisfeitas",
      card4: "Investimento Único"
    }
  },
  
  clinica: {
    // Substitui "EMPRESAS" no headline
    termoSegmento: "CLÍNICAS",
    
    // Badge no hero
    badgeTexto: "Para Clínicas que Querem Crescer",
    
    // Cards de stats
    cards: {
      card1: "Clínicas no Ar",
      card2: "Prazo de Entrega",
      card3: "Médicos Satisfeitos",
      card4: "Investimento Único"
    }
  },
  
  // Fallback - usa o conteúdo genérico já existente no HTML
  default: null
};

// Exporta para uso global
window.SEGMENTOS_CONFIG = SEGMENTOS_CONFIG;
