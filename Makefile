# Makefile pour BRACONGO Stages
.PHONY: help up down build fresh logs shell install test

help: ## Afficher l'aide
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Démarrer les containers
	docker-compose up -d

down: ## Arrêter les containers
	docker-compose down

build: ## Reconstruire les containers
	docker-compose build --no-cache

fresh: ## Reset complet de la DB + migrate + seed
	docker-compose down -v
	docker-compose up -d
	sleep 10
	$(MAKE) install
	docker-compose exec app php artisan migrate:fresh --seed
	docker-compose exec app php artisan storage:link

logs: ## Voir les logs
	docker-compose logs -f

shell: ## Shell dans le container app
	docker-compose exec app bash

install: ## Installer les dépendances
	docker-compose exec app composer install
	docker-compose exec node npm install
	docker-compose exec node npm run build

test: ## Lancer les tests
	docker-compose exec app php artisan test

queue-work: ## Démarrer le worker de queues
	docker-compose exec app php artisan queue:work redis

cache-clear: ## Vider le cache
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

migrate: ## Lancer les migrations
	docker-compose exec app php artisan migrate

seed: ## Lancer les seeders
	docker-compose exec app php artisan db:seed

permissions: ## Fixer les permissions
	docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
	docker-compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

fresh-data: ## Réinitialiser avec nouvelles données congolaises
	docker-compose exec app php artisan migrate:fresh --seed
	docker-compose exec app php artisan storage:link
	@echo "🍺 Nouvelles données BRACONGO chargées avec succès !"

backup: ## Créer une sauvegarde de la base de données
	docker-compose exec mysql mysqldump -u bracongo_user -pbracongo_pass_2024 bracongo_stages > backup_bracongo_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "✅ Sauvegarde créée: backup_bracongo_$(shell date +%Y%m%d_%H%M%S).sql"

restore: ## Restaurer depuis une sauvegarde (usage: make restore FILE=backup.sql)
	@if [ -z "$(FILE)" ]; then echo "❌ Utilisez: make restore FILE=votre_backup.sql"; exit 1; fi
	docker-compose exec -T mysql mysql -u bracongo_user -pbracongo_pass_2024 bracongo_stages < $(FILE)
	@echo "✅ Base de données restaurée depuis $(FILE)"

mysql-cli: ## Accéder à MySQL CLI
	docker-compose exec mysql mysql -u bracongo_user -pbracongo_pass_2024 bracongo_stages

redis-cli: ## Accéder à Redis CLI  
	docker-compose exec redis redis-cli

clean-all: ## Nettoyer complètement Docker
	docker-compose down --volumes --remove-orphans
	docker system prune -af
	@echo "🧹 Nettoyage Docker terminé" 