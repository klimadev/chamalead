const assetsVersion = "20260422l";
const appSrc = `quiz.js?v=${assetsVersion}`;
const stylesHref = `styles.css?v=${assetsVersion}`;
const startButton = document.getElementById("welcomeCta");

let appLoading = false;
let appLoaded = false;

const markStylesReady = () => {
  document.body.classList.add("style-ready");
};

requestAnimationFrame(markStylesReady);
window.addEventListener("load", markStylesReady, { once: true });

const ensureDeferredStyles = () => {
  const existing = document.querySelector('link[data-deferred-styles="true"]');
  if (existing) {
    if (existing.dataset.loaded === "true") return Promise.resolve();
    return new Promise((resolve) => {
      existing.addEventListener(
        "load",
        () => {
          existing.dataset.loaded = "true";
          resolve();
        },
        { once: true }
      );
    });
  }

  const link = document.createElement("link");
  link.rel = "stylesheet";
  link.href = stylesHref;
  link.dataset.deferredStyles = "true";

  return new Promise((resolve) => {
    link.addEventListener(
      "load",
      () => {
        link.dataset.loaded = "true";
        resolve();
      },
      { once: true }
    );
    document.head.appendChild(link);
  });
};

const loadScript = (src) =>
  new Promise((resolve, reject) => {
    const script = document.createElement("script");
    script.src = src;
    script.defer = true;
    script.onload = resolve;
    script.onerror = reject;
    document.head.appendChild(script);
  });

const runPendingStart = () => {
  if (!window.__quizStartPending || typeof window.__startQuiz !== "function") return;
  window.__quizStartPending = false;
  window.__startQuiz();
};

const loadQuizApp = async (startNow = false) => {
  if (startNow) window.__quizStartPending = true;

  if (appLoaded) {
    runPendingStart();
    return;
  }

  if (appLoading) return;

  appLoading = true;
  document.body.classList.add("motion-ready");

  try {
    await ensureDeferredStyles();
    await loadScript(appSrc);
    appLoading = false;
    appLoaded = true;
    runPendingStart();
  } catch {
    appLoading = false;
    if (startButton) {
      startButton.disabled = false;
      startButton.textContent = "Comecar agora";
    }
  }
};

if (startButton) {
  const warmApp = () => loadQuizApp(false);

  startButton.addEventListener("touchstart", warmApp, { once: true, passive: true });
  startButton.addEventListener("click", (event) => {
    if (typeof window.__startQuiz === "function") {
      window.__startQuiz();
      return;
    }

    event.preventDefault();
    startButton.disabled = true;
    startButton.textContent = "Abrindo quiz...";
    loadQuizApp(true);
  });
}
