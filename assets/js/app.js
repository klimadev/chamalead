        // Initialize Lucide Icons
        lucide.createIcons();
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const icon = document.getElementById('menuIcon');
            menu.classList.toggle('hidden');
            
            if (menu.classList.contains('hidden')) {
                icon.setAttribute('data-lucide', 'menu');
            } else {
                icon.setAttribute('data-lucide', 'x');
            }
            lucide.createIcons();
        }

        // Scroll Progress Bar
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            document.getElementById('progressBar').style.width = scrolled + '%';
        });

        // Navbar Background on Scroll
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-dark/95', 'backdrop-blur-xl', 'shadow-lg');
            } else {
                navbar.classList.remove('bg-dark/95', 'backdrop-blur-xl', 'shadow-lg');
            }
        });

        // Reveal on Scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        if (prefersReducedMotion) {
            document.querySelectorAll('.reveal').forEach((el) => el.classList.add('active'));
        } else {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
            
            setTimeout(() => {
                document.querySelectorAll('.reveal:not(.active)').forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < window.innerHeight * 0.9 && rect.bottom > 0) {
                        el.classList.add('active');
                    }
                });
            }, 100);
        }

        // Mouse Follow Spotlight Effect
        const spotlight = document.getElementById('mouseSpotlight');
        let spotlightTimeout;
        let isMoving = false;

        if (!prefersReducedMotion && spotlight) {
            document.addEventListener('mousemove', (e) => {
                isMoving = true;
                spotlight.style.left = e.clientX + 'px';
                spotlight.style.top = e.clientY + 'px';
                spotlight.style.opacity = '1';

                clearTimeout(spotlightTimeout);
                spotlightTimeout = setTimeout(() => {
                    isMoving = false;
                    setTimeout(() => {
                        if (!isMoving) {
                            spotlight.style.opacity = '0';
                        }
                    }, 100);
                }, 50);
            });
        }

        // 3D Tilt Effect for iPhone
        const tiltContainer = document.querySelector('.tilt-container');
        const tiltElement = document.querySelector('.tilt-element');
        
        if (!prefersReducedMotion && tiltContainer && tiltElement) {
            tiltElement.style.setProperty('--tilt-x', '0deg');
            tiltElement.style.setProperty('--tilt-y', '0deg');
            
            tiltContainer.addEventListener('mousemove', (e) => {
                const rect = tiltContainer.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;
                
                tiltElement.style.setProperty('--tilt-x', `${rotateX}deg`);
                tiltElement.style.setProperty('--tilt-y', `${rotateY}deg`);
            });
            
            tiltContainer.addEventListener('mouseleave', () => {
                tiltElement.style.setProperty('--tilt-x', '0deg');
                tiltElement.style.setProperty('--tilt-y', '0deg');
            });
        }

        // Magnetic Button Effect
        const magneticZones = document.querySelectorAll('.magnetic-zone');
        
        magneticZones.forEach(zone => {
            const button = zone.querySelector('.magnetic-button');
            if (prefersReducedMotion || !button) {
                return;
            }
            
            zone.addEventListener('mousemove', (e) => {
                const rect = zone.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                button.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
            });
            
            zone.addEventListener('mouseleave', () => {
                button.style.transform = 'translate(0px, 0px)';
            });
        });

        // Case Study Chat Animation
        const caseMessages = [
            { type: 'user', text: 'Oi! Quero alugar o espaço para uma festa de aniversário.' },
            { type: 'ai', text: 'Olá! 🎉 Que ótimo! Sou o assistente virtual da Fazenda Vale Verde. Fico feliz em ajudar com sua festa!' },
            { type: 'ai', text: 'Para verificar disponibilidade, preciso de algumas informações:\n\n📅 Qual a data pretendida?\n👥 Quantos convidados?\n🎂 É aniversário de adulto ou criança?' },
            { type: 'user', text: 'Quero dia 15/12, uns 50 convidados, aniversário de 30 anos.' },
            { type: 'ai', text: 'Perfeito! Deixe-me verificar a disponibilidade...' },
            { type: 'ai', text: '🎉 Ótimas notícias! O dia 15/12 está disponível!\n\nPara 50 convidados, temos dois pacotes:\n\n🌟 Pacote Standard: R$ 3.500\n✨ Pacote Premium: R$ 4.800 (inclui decoração e buffet)' },
            { type: 'ai', text: 'Qual você prefere? Para reservar, precisamos de um sinal de 50%.' },
            { type: 'user', text: 'Vou querer o Premium!' },
            { type: 'ai', text: 'Excelente escolha! 🌟\n\nPara confirmar sua reserva:\n\n💰 Sinal: R$ 2.400,00 (50%)\n\nChave Pix: fazenda.valeverde@email.com\n\nAssim que fizer o pagamento, me envie o comprovante que já bloqueio a data para você! 😉' },
            { type: 'user', text: '[Comprovante enviado]' },
            { type: 'ai', text: '✅ Pagamento confirmado!\n\n🎉 Sua data está RESERVADA!\n\n📅 15 de Dezembro de 2024\n👥 50 convidados\n💎 Pacote Premium\n\nO proprietário foi notificado imediatamente por WhatsApp/SMS com evento agendado, valor recebido e dados do cliente. Entraremos em contato 3 dias antes para alinhar os detalhes finais.\n\nObrigado pela preferência! 🙏' }
        ];

        let caseAnimationStarted = false;
        const caseChat = document.getElementById('caseChat');
        const typingIndicator = document.getElementById('typingIndicator');
        const caseInputPreview = document.getElementById('caseInputPreview');

        function wait(ms) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        }

        function showTyping() {
            if (typingIndicator) {
                typingIndicator.classList.remove('hidden');
            }
            if (caseChat) {
                caseChat.scrollTop = caseChat.scrollHeight;
            }
        }

        function hideTyping() {
            if (typingIndicator) {
                typingIndicator.classList.add('hidden');
            }
        }

        function appendCaseBubble(msg) {
            const div = document.createElement('div');
            div.className = msg.type === 'user' 
                ? 'flex justify-end message-enter' 
                : 'flex justify-start message-enter';
            
            div.style.opacity = '0';
            div.style.transform = msg.type === 'user' 
                ? 'translateX(20px) scale(0.95)' 
                : 'translateX(-20px) scale(0.95)';
            div.style.transition = 'all 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
            
            if (msg.type === 'user') {
                div.innerHTML = `
                    <div class="bg-flame-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-[80%] shadow-lg shadow-flame-500/20 hover:shadow-flame-500/40 transition-shadow duration-300">
                        <p class="text-sm">${msg.text}</p>
                    </div>
                `;
            } else {
                div.innerHTML = `
                    <div class="bg-dark-800 border border-white/10 rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-[80%] shadow-lg hover:border-flame-500/30 transition-all duration-300">
                        <p class="text-sm text-zinc-200">${msg.text.replace(/\n/g, '<br>')}</p>
                    </div>
                `;
            }

            caseChat.appendChild(div);
            
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    div.style.opacity = '1';
                    div.style.transform = 'translateX(0) scale(1)';
                });
            });
            
            smoothScrollToBottom(caseChat);
        }
        
        function smoothScrollToBottom(element) {
            const targetScroll = element.scrollHeight - element.clientHeight;
            const startScroll = element.scrollTop;
            const distance = targetScroll - startScroll;
            const duration = 300;
            let startTime = null;
            
            function animation(currentTime) {
                if (!startTime) startTime = currentTime;
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                const easeOut = 1 - Math.pow(1 - progress, 3);
                element.scrollTop = startScroll + (distance * easeOut);
                
                if (progress < 1) {
                    requestAnimationFrame(animation);
                }
            }
            
            requestAnimationFrame(animation);
        }

        function scrollInputToRight(inputContainer) {
            if (inputContainer) {
                inputContainer.scrollLeft = inputContainer.scrollWidth;
            }
        }

        async function animateCaseInputText(text) {
            if (!caseInputPreview) {
                return;
            }
            
            const container = caseInputPreview.parentElement;

            caseInputPreview.textContent = '';
            caseInputPreview.classList.remove('text-zinc-600');
            caseInputPreview.classList.add('text-zinc-300');
            caseInputPreview.classList.add('input-typing');

            const minSpeed = 52;
            const maxSpeed = 90;
            for (let i = 0; i < text.length; i++) {
                caseInputPreview.textContent += text[i];
                scrollInputToRight(container);
                const speed = minSpeed + Math.floor(Math.random() * (maxSpeed - minSpeed));
                await wait(speed);
            }

            await wait(620);
            caseInputPreview.textContent = 'Digite uma mensagem...';
            caseInputPreview.classList.remove('input-typing');
            caseInputPreview.classList.remove('text-zinc-300');
            caseInputPreview.classList.add('text-zinc-600');
            if(container) container.scrollLeft = 0;
        }

        // Timeline State Management with smooth animations
        function updateTimelineStep(stepNumber, status) {
            const step = document.getElementById(`timeline-step-${stepNumber}`);
            if (!step) return;
            
            const currentStatus = step.getAttribute('data-status');
            if (currentStatus === status) return;
            
            step.setAttribute('data-status', status);
            const indicator = step.querySelector('.step-indicator');
            const numberEl = step.querySelector('.step-number');
            const spinnerEl = step.querySelector('.step-spinner');
            const checkEl = step.querySelector('.step-check');
            const titleEl = step.querySelector('h3');
            
            requestAnimationFrame(() => {
                if (status === 'pending') {
                    indicator.style.transform = 'scale(1)';
                    indicator.style.background = '#262626';
                    indicator.style.borderColor = '#f97316';
                    indicator.style.boxShadow = 'none';
                    numberEl.classList.remove('hidden');
                    spinnerEl.classList.add('hidden');
                    checkEl.classList.add('hidden');
                } else if (status === 'active') {
                    indicator.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        indicator.style.transform = 'scale(1)';
                    }, 200);
                    indicator.style.background = 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)';
                    indicator.style.borderColor = '#f97316';
                    indicator.style.boxShadow = '0 0 30px rgba(249, 115, 22, 0.6)';
                    numberEl.classList.add('hidden');
                    spinnerEl.classList.add('hidden');
                    spinnerEl.classList.remove('hidden');
                    checkEl.classList.add('hidden');
                    
                    spinnerEl.style.animation = 'none';
                    spinnerEl.offsetHeight;
                    spinnerEl.style.animation = 'spinSmooth 0.8s linear infinite';
                    
                    titleEl.style.transition = 'color 0.4s ease';
                } else if (status === 'completed') {
                    indicator.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        indicator.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            indicator.style.transform = 'scale(1)';
                        }, 150);
                    }, 100);
                    
                    indicator.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
                    indicator.style.borderColor = '#22c55e';
                    indicator.style.boxShadow = '0 0 30px rgba(34, 197, 94, 0.5)';
                    numberEl.classList.add('hidden');
                    spinnerEl.classList.add('hidden');
                    checkEl.classList.remove('hidden');
                    
                    checkEl.style.transform = 'scale(0)';
                    checkEl.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
                    setTimeout(() => {
                        checkEl.style.transform = 'scale(1)';
                    }, 50);
                }
            });
            
            updateTimelineProgress();
        }
        
        function updateTimelineProgress() {
            const steps = document.querySelectorAll('.timeline-step');
            let completedCount = 0;
            let activeCount = 0;
            
            steps.forEach((step, index) => {
                const status = step.getAttribute('data-status');
                if (status === 'completed') completedCount++;
                if (status === 'active') activeCount++;
            });
            
            const totalSteps = steps.length;
            const progress = ((completedCount + (activeCount * 0.5)) / totalSteps) * 100;
            
            const container = document.getElementById('timelineContainer');
            if (container) {
                container.style.setProperty('--progress', `${progress}%`);
            }
        }

        async function playCaseConversation() {
            if (!caseChat) {
                return;
            }

            const paymentConfirmation = document.getElementById('paymentConfirmation');
            const chatContainer = caseChat.closest('.bg-dark-950, .glass') || caseChat.parentElement;
            
            for (let s = 1; s <= 5; s++) {
                updateTimelineStep(s, 'pending');
            }

            if (chatContainer) {
                chatContainer.classList.add('case-chat-active');
            }

            for (let i = 0; i < caseMessages.length; i++) {
                const msg = caseMessages[i];
                
                if (i === 0) {
                    updateTimelineStep(1, 'active');
                }
                if (i === 1) {
                    updateTimelineStep(1, 'completed');
                    updateTimelineStep(2, 'active');
                }
                if (i === 3) {
                    updateTimelineStep(2, 'completed');
                    updateTimelineStep(3, 'active');
                }
                if (i === 5) {
                    updateTimelineStep(3, 'completed');
                    updateTimelineStep(4, 'active');
                }
                if (i === 8) {
                    updateTimelineStep(4, 'completed');
                    updateTimelineStep(5, 'active');
                }
                if (i === 10) {
                    updateTimelineStep(5, 'completed');
                }
                
                if (msg.type === 'user') {
                    await animateCaseInputText(msg.text);
                    appendCaseBubble(msg);
                    await wait(200);
                    continue;
                }

                showTyping();
                const typingTime = Math.min(2500, Math.max(600, msg.text.length * 15)); 
                await wait(typingTime);
                hideTyping();
                appendCaseBubble(msg);
                
                if (msg.text.includes('Pagamento confirmado') && paymentConfirmation) {
                    paymentConfirmation.classList.remove('hidden');
                    void paymentConfirmation.offsetWidth;
                    paymentConfirmation.style.opacity = '1';
                }
                
                const nextMsg = caseMessages[i + 1];
                if (nextMsg && nextMsg.type === 'ai') {
                     await wait(100);
                } else {
                     await wait(1000);
                }
            }
            
            if (chatContainer) {
                chatContainer.classList.remove('case-chat-active');
            }
        }

        const caseSection = document.getElementById('case');
        function startCaseChatOnce() {
            if (!caseAnimationStarted) {
                caseAnimationStarted = true;
                playCaseConversation();
            }
        }

        const timelineSteps = document.querySelectorAll('.timeline-step');
        let timelineAnimated = false;
        
        function animateTimelineSteps() {
            if (timelineAnimated) return;
            timelineSteps.forEach((step, index) => {
                setTimeout(() => {
                    step.classList.add('animate-in');
                }, index * 100);
            });
            timelineAnimated = true;
        }

        if (caseSection && caseChat) {
            const caseObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        startCaseChatOnce();
                        animateTimelineSteps();
                        caseObserver.disconnect();
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });

            caseObserver.observe(caseChat);

            const checkCaseVisibility = () => {
                if (caseAnimationStarted) {
                    return;
                }

                const rect = caseChat.getBoundingClientRect();
                const visible = rect.top < window.innerHeight * 0.85 && rect.bottom > window.innerHeight * 0.15;
                if (visible) {
                    startCaseChatOnce();
                    animateTimelineSteps();
                    window.removeEventListener('scroll', checkCaseVisibility);
                }
            };

            window.addEventListener('scroll', checkCaseVisibility, { passive: true });
            window.addEventListener('load', checkCaseVisibility);
            setTimeout(checkCaseVisibility, 200);
        }

        // FAQ Toggle
        function toggleFaq(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('[data-lucide="chevron-down"]');
            
            content.classList.toggle('hidden');
            
            if (content.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
        }

        // Lead forms: inline feedback + shared submission
        const leadForms = document.querySelectorAll('.lead-capture-form');
        const whatsappInputs = document.querySelectorAll('input[name="whatsapp"]');

        function maskWhatsapp(value) {
            const digits = value.replace(/\D/g, '').slice(0, 11);
            if (digits.length <= 10) {
                return digits
                    .replace(/(\d{2})(\d)/, '($1) $2')
                    .replace(/(\d{4})(\d)/, '$1-$2');
            }
            return digits
                .replace(/(\d{2})(\d)/, '($1) $2')
                .replace(/(\d{5})(\d)/, '$1-$2');
        }

        whatsappInputs.forEach((input) => {
            input.addEventListener('input', () => {
                input.value = maskWhatsapp(input.value);
            });
        });

        function showFeedback(box, type, message) {
            if (!box) {
                return;
            }
            box.classList.remove('hidden', 'border-red-500/40', 'bg-red-500/10', 'text-red-200', 'border-green-500/40', 'bg-green-500/10', 'text-green-200');
            if (type === 'success') {
                box.classList.add('border-green-500/40', 'bg-green-500/10', 'text-green-200');
            } else {
                box.classList.add('border-red-500/40', 'bg-red-500/10', 'text-red-200');
            }
            box.textContent = message;
        }

        leadForms.forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('.submit-btn');
                const text = form.querySelector('.submit-text');
                const icon = form.querySelector('.submit-icon');
                const feedback = form.querySelector('[id$="Feedback"]');
                const defaultText = button.dataset.defaultText || 'Enviar';
                const loadingText = button.dataset.loadingText || 'Enviando...';

                button.disabled = true;
                text.textContent = loadingText;
                icon.setAttribute('data-lucide', 'loader-2');
                icon.classList.add('animate-spin');
                lucide.createIcons();

                const formData = new FormData(form);

                try {
                    const response = await fetch('api.php', { method: 'POST', body: formData });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        const serverMessage = Array.isArray(result.errors) ? result.errors.join(' | ') : (result.message || 'Erro ao enviar formulario');
                        throw new Error(serverMessage);
                    }

                    text.textContent = 'Enviado com sucesso';
                    icon.setAttribute('data-lucide', 'check');
                    icon.classList.remove('animate-spin');
                    lucide.createIcons();
                    showFeedback(feedback, 'success', 'Recebemos seus dados. Vamos entrar em contato em ate 48h.');
                    form.reset();
                } catch (error) {
                    text.textContent = 'Erro ao enviar';
                    icon.setAttribute('data-lucide', 'alert-circle');
                    icon.classList.remove('animate-spin');
                    lucide.createIcons();
                    showFeedback(feedback, 'error', error.message || 'Nao foi possivel enviar agora. Tente novamente.');
                } finally {
                    setTimeout(() => {
                        text.textContent = defaultText;
                        icon.setAttribute('data-lucide', 'arrow-right');
                        icon.classList.remove('animate-spin');
                        button.disabled = false;
                        lucide.createIcons();
                    }, 1400);
                }
            });
        });

        // Floating Embers Animation - Optimized
        let emberInterval;
        function createEmber() {
            const container = document.getElementById('embersContainer');
            if (!container) return;
            
            if (container.children.length > 15) return;
            
            const ember = document.createElement('div');
            ember.className = 'floating-ember';
            ember.style.left = Math.random() * 100 + 'vw';
            ember.style.animationDuration = (Math.random() * 3 + 3) + 's';
            ember.style.opacity = Math.random() * 0.5 + 0.2;
            
            const colors = ['#f97316', '#fb923c', '#fbbf24', '#ef4444'];
            ember.style.background = colors[Math.floor(Math.random() * colors.length)];
            
            container.appendChild(ember);
            
            ember.addEventListener('animationend', () => {
                ember.remove();
            });
            
            setTimeout(() => {
                if (ember.parentNode) ember.remove();
            }, 6000);
        }

        if (!prefersReducedMotion) {
            window.addEventListener('load', () => {
                emberInterval = setInterval(createEmber, 400);
            });
            
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    clearInterval(emberInterval);
                } else if (!prefersReducedMotion) {
                    emberInterval = setInterval(createEmber, 400);
                }
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: prefersReducedMotion ? 'auto' : 'smooth',
                        block: 'start'
                    });
                    
                    document.getElementById('mobileMenu').classList.add('hidden');
                }
            });
        });

        // ======================
        // PHONE MOCKUP - DYNAMIC TIME & CHAT
        // ======================
        
        // Real-time clock
        function updatePhoneTime() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const timeString = `${hours}:${minutes}`;
            
            const timeDisplay = document.getElementById('phoneTime');
            if (timeDisplay) {
                timeDisplay.textContent = timeString;
            }
        }
        
        updatePhoneTime();
        setInterval(updatePhoneTime, 1000);
        
        // WhatsApp Conversation Simulation - About ChamaLead
        const chatMessages = [
            {
                type: 'lead',
                text: 'Oi! Vi que vocês criam IA para WhatsApp. Quero saber mais! 🤖',
                time: '09:41'
            },
            {
                type: 'typing',
                duration: 1500
            },
            {
                type: 'ai',
                text: 'Olá! 😊 Seja bem-vindo à ChamaLead! Sou a própria IA que vai atender seus clientes.',
                time: '09:41'
            },
            {
                type: 'ai',
                text: 'Posso agendar consultas, responder dúvidas, qualificar leads e fechar vendas 24/7 sem você precisar fazer nada. Como posso te ajudar hoje? 💼',
                time: '09:41'
            },
            {
                type: 'lead',
                text: 'Show! Qual o valor e quanto tempo demora pra ficar pronto? ⏱️',
                time: '09:42'
            },
            {
                type: 'typing',
                duration: 2000
            },
            {
                type: 'ai',
                text: 'Ótima pergunta! 💡\n\n📦 Pacote Completo ChamaLead:\n• Setup único: R$ 3.000 (parcelável)\n• Mensalidade: R$ 497/mês\n• Entrega: 48 horas\n\nInclui tudo: API do WhatsApp, VPS dedicado, tokens de IA ilimitados e suporte! 🚀',
                time: '09:42'
            },
            {
                type: 'ai',
                text: 'E olha só... 🤯\n\nSe você contratasse 2 atendentes (salário + encargos + treinamento), custaria mais de R$ 6.000/mês.\n\nCom a ChamaLead você economiza R$ 5.000+ todo mês! 💰',
                time: '09:42'
            },
            {
                type: 'lead',
                text: 'Caraca, faz sentido mesmo! E como funciona na prática? Meu negócio é de odontologia 🦷',
                time: '09:43'
            },
            {
                type: 'typing',
                duration: 1800
            },
            {
                type: 'ai',
                text: 'Perfeito para odonto! 🦷✨\n\nExemplo real:\n• Paciente manda "Oi" no WhatsApp\n• Eu já apresento os tratamentos\n• Qualifico o interesse (limpeza, clareamento, etc)\n• Verifico disponibilidade na agenda\n• Agendo automaticamente\n• Envio lembretes 24h antes\n• Tudo integrado com seu Google Calendar! 📅',
                time: '09:43'
            },
            {
                type: 'ai',
                text: 'Resultado: você acorda com a agenda cheia de pacientes qualificados, enquanto a IA trabalhou a noite toda pra você! 😴➡️😃',
                time: '09:43'
            },
            {
                type: 'lead',
                text: 'Nossa, preciso disso URGENTE! Como faço pra contratar? 🚀',
                time: '09:44'
            },
            {
                type: 'typing',
                duration: 1200
            },
            {
                type: 'ai',
                text: 'Ótima decisão! 🎉\n\nVou te passar o link do formulário de contratação:\n\n👉 chama-lead.com.br/formulario\n\nBasta preencher com seus dados que entramos em contato em até 2 horas para começar a configuração da sua IA personalizada! ⚡',
                time: '09:44'
            },
            {
                type: 'lead',
                text: 'Já preenchi aqui! Ansioso pra começar 🔥',
                time: '09:45'
            },
            {
                type: 'typing',
                duration: 2500
            },
            {
                type: 'ai',
                text: '✅ Cadastro recebido!\n\n🎊 Bem-vindo à ChamaLead!\n\nNossa equipe vai entrar em contato em breve. Em 48h sua IA estará online e vendendo sozinha!\n\nQualquer dúvida, estou aqui 24/7. Vamos transformar seu WhatsApp em uma máquina de vendas! 🤖💪✨',
                time: '09:45'
            }
        ];
        
        let messageIndex = 0;
        let isTyping = false;
        let heroChatStarted = false;
        const heroInputPreview = document.getElementById('heroInputPreview');
        
        function createMessageElement(message, index) {
            const chatContainer = document.getElementById('chatMessages');
            
            if (message.type === 'typing') {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'typing-indicator';
                typingDiv.id = 'typing-indicator';
                typingDiv.innerHTML = `
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                `;
                chatContainer.appendChild(typingDiv);
                chatContainer.scrollTop = chatContainer.scrollHeight;
                return;
            }
            
            const messageDiv = document.createElement('div');
            const isAI = message.type === 'ai';
            
            messageDiv.className = isAI ? 'message-bubble-received p-3 px-4' : 'message-bubble-sent p-3 px-4';
            
            const formattedText = message.text.replace(/\n/g, '<br>');
            
            messageDiv.innerHTML = `
                <p class="text-[14px] leading-relaxed">${formattedText}</p>
                <div class="flex items-center justify-end gap-1.5 mt-1.5">
                    <span class="text-[10px] ${isAI ? 'text-[#8696a0]' : 'text-[#99beb3]'} font-medium">${message.time}</span>
                    ${!isAI ? `
                        <svg class="w-3.5 h-3.5 text-[#53bdeb]" viewBox="0 0 16 11" fill="currentColor">
                            <path d="M11.28 2.28a.75.75 0 0 1 1.06 1.06l-7.5 7.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 0 1 1.06-1.06L4 9.19l7.28-7.21z"/>
                            <path d="M15.28 2.28a.75.75 0 0 1 1.06 1.06l-7.5 7.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 1 1 1.06-1.06L8 9.19l7.28-7.21z"/>
                        </svg>
                    ` : ''}
                </div>
            `;

            
            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
            
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            const chatArea = document.getElementById('chatArea');
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        async function animateHeroInputText(text) {
            if (!heroInputPreview) {
                return;
            }
            
            const container = heroInputPreview.parentElement;

            heroInputPreview.textContent = '';
            heroInputPreview.classList.add('input-typing');

            const minSpeed = 44;
            const maxSpeed = 78;
            for (let i = 0; i < text.length; i++) {
                heroInputPreview.textContent += text[i];
                scrollInputToRight(container);
                const speed = minSpeed + Math.floor(Math.random() * (maxSpeed - minSpeed));
                await wait(speed);
            }

            await wait(460);
            heroInputPreview.classList.remove('input-typing');
            heroInputPreview.textContent = 'Mensagem';
            if(container) container.scrollLeft = 0;
        }

        function showNextMessage() {
            if (messageIndex >= chatMessages.length) {
                return;
            }
            
            const message = chatMessages[messageIndex];
            
            if (message.type === 'typing') {
                createMessageElement(message);
                isTyping = true;
                setTimeout(() => {
                    isTyping = false;
                    messageIndex++;
                    showNextMessage();
                }, message.duration * 0.5);
            } else {
                const proceed = () => {
                    createMessageElement(message);
                    messageIndex++;
                    
                    let nextDelay = Math.max(600, message.text.length * 15);
                    const nextMsg = chatMessages[messageIndex];
                    if (nextMsg && nextMsg.type === 'ai' && message.type === 'ai') {
                        nextDelay = 100;
                    }

                    setTimeout(showNextMessage, nextDelay);
                };

                if (message.type === 'lead') {
                    animateHeroInputText(message.text).then(proceed);
                } else {
                    proceed();
                }
            }
        }

        
        // Start mockup chat only when visible
        const heroChatArea = document.getElementById('chatArea');
        if (heroChatArea) {
            const heroChatObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !heroChatStarted) {
                        heroChatStarted = true;
                        setTimeout(showNextMessage, 800);
                        heroChatObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2, rootMargin: '0px 0px -10% 0px' });

            heroChatObserver.observe(heroChatArea);
            
            const checkHeroChatVisibility = () => {
                if (heroChatStarted) return;
                const rect = heroChatArea.getBoundingClientRect();
                const isVisible = rect.top < window.innerHeight * 0.8 && rect.bottom > 0;
                if (isVisible) {
                    heroChatStarted = true;
                    setTimeout(showNextMessage, 800);
                    window.removeEventListener('scroll', checkHeroChatVisibility);
                }
            };
            
            window.addEventListener('scroll', checkHeroChatVisibility, { passive: true });
            window.addEventListener('load', checkHeroChatVisibility);
            setTimeout(checkHeroChatVisibility, 100);
        }
        
        // Auto-scroll to bottom on load
        window.addEventListener('load', () => {
            const chatArea = document.getElementById('chatArea');
            if (chatArea) {
                chatArea.scrollTop = chatArea.scrollHeight;
            }
        });
