import './bootstrap';
import Alpine from 'alpinejs';

// Configuration Alpine.js
window.Alpine = Alpine;

// Utilitaires d'animations modernes
class BracongoAnimations {
    static observeElements() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    
                    // Animation de compteur pour les statistiques
                    if (entry.target.hasAttribute('data-counter')) {
                        this.animateCounter(entry.target);
                    }
                }
            });
        }, observerOptions);

        // Observer tous les éléments avec la classe animate-on-scroll
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }

    static animateCounter(element) {
        const target = parseInt(element.getAttribute('data-counter'));
        const duration = 2000; // 2 secondes
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, duration / steps);
    }

    static init() {
        this.observeElements();
        this.initSmoothScroll();
        this.initParallax();
    }

    static initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const headerOffset = 80; // Hauteur du header fixe
                    const elementPosition = target.offsetTop;
                    const offsetPosition = elementPosition - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    static initParallax() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = element.dataset.parallax || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        });
    }
}

// Composants Alpine.js modernes pour BRACONGO

// Composants Alpine.js pour BRACONGO
Alpine.data('fileUpload', () => ({
    files: [],
    isDragging: false,
    maxFiles: 5,
    maxFileSize: 10 * 1024 * 1024, // 10MB
    allowedTypes: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'],
    
    init() {
        this.$refs.fileInput.addEventListener('change', this.handleFileSelect.bind(this));
    },
    
    handleDragOver(e) {
        e.preventDefault();
        this.isDragging = true;
    },
    
    handleDragLeave(e) {
        e.preventDefault();
        this.isDragging = false;
    },
    
    handleDrop(e) {
        e.preventDefault();
        this.isDragging = false;
        this.handleFiles(e.dataTransfer.files);
    },
    
    handleFileSelect(e) {
        this.handleFiles(e.target.files);
    },
    
    handleFiles(fileList) {
        Array.from(fileList).forEach(file => {
            if (this.validateFile(file)) {
                this.addFile(file);
            }
        });
    },
    
    validateFile(file) {
        if (this.files.length >= this.maxFiles) {
            this.showError(`Maximum ${this.maxFiles} fichiers autorisés`);
            return false;
        }
        
        if (file.size > this.maxFileSize) {
            this.showError(`Le fichier ${file.name} dépasse la taille maximale de 10MB`);
            return false;
        }
        
        if (!this.allowedTypes.includes(file.type)) {
            this.showError(`Type de fichier non autorisé: ${file.name}`);
            return false;
        }
        
        return true;
    },
    
    addFile(file) {
        const fileObj = {
            id: Date.now() + Math.random(),
            file: file,
            name: file.name,
            size: this.formatFileSize(file.size),
            type: this.getFileType(file.type),
            uploaded: false
        };
        
        this.files.push(fileObj);
    },
    
    removeFile(fileId) {
        this.files = this.files.filter(f => f.id !== fileId);
    },
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    getFileType(mimeType) {
        const types = {
            'application/pdf': 'PDF',
            'application/msword': 'DOC',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
            'image/jpeg': 'JPG',
            'image/png': 'PNG'
        };
        return types[mimeType] || 'Fichier';
    },
    
    showError(message) {
        // Afficher une notification d'erreur
        this.$dispatch('notify', {
            type: 'error',
            message: message
        });
    }
}));

Alpine.data('notification', () => ({
    notifications: [],
    
    init() {
        this.$watch('notifications', () => {
            if (this.notifications.length > 0) {
                setTimeout(() => {
                    this.notifications.shift();
                }, 5000);
            }
        });
        
        this.$el.addEventListener('notify', (event) => {
            this.addNotification(event.detail);
        });
    },
    
    addNotification(notification) {
        this.notifications.push({
            id: Date.now(),
            ...notification
        });
    },
    
    removeNotification(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}));

Alpine.data('multiStepForm', () => ({
    currentStep: 1,
    totalSteps: 4,
    formData: {},
    
    nextStep() {
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
        }
    },
    
    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
        }
    },
    
    goToStep(step) {
        if (step >= 1 && step <= this.totalSteps) {
            this.currentStep = step;
        }
    },
    
    isCurrentStep(step) {
        return this.currentStep === step;
    },
    
    isCompletedStep(step) {
        return this.currentStep > step;
    },
    
    getStepTitle(step) {
        const titles = {
            1: 'Informations personnelles',
            2: 'Établissement et formation',
            3: 'Documents',
            4: 'Préférences de stage'
        };
        return titles[step] || '';
    }
}));

// Navigation moderne avec indicateur de scroll
Alpine.data('modernNavigation', () => ({
    scrolled: false,
    
    init() {
        window.addEventListener('scroll', () => {
            this.scrolled = window.pageYOffset > 50;
        });
    }
}));

// Hero section interactive
Alpine.data('heroSection', () => ({
    currentTextIndex: 0,
    texts: [
        'Construisez votre avenir avec BRACONGO',
        'Développez vos compétences professionnelles',
        'Rejoignez l\'industrie brassicole leader'
    ],
    
    init() {
        // Animation de texte rotatif
        setInterval(() => {
            this.currentTextIndex = (this.currentTextIndex + 1) % this.texts.length;
        }, 4000);
    },
    
    get currentText() {
        return this.texts[this.currentTextIndex];
    }
}));

// Cards avec effet hover avancé
Alpine.data('hoverCard', () => ({
    isHovered: false,
    mouseX: 0,
    mouseY: 0,
    
    handleMouseMove(event) {
        const rect = event.currentTarget.getBoundingClientRect();
        this.mouseX = ((event.clientX - rect.left) / rect.width) * 100;
        this.mouseY = ((event.clientY - rect.top) / rect.height) * 100;
    },
    
    get gradientStyle() {
        return `background: radial-gradient(circle at ${this.mouseX}% ${this.mouseY}%, rgba(227, 6, 19, 0.1) 0%, transparent 70%)`;
    }
}));

// Initialiser les animations au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    BracongoAnimations.init();
});

// Démarrer Alpine
Alpine.start(); 