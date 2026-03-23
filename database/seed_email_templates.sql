INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('analyse_dossier', 'Analyse du dossier', 'Votre dossier est en cours d''analyse - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nNous accusons réception de votre dossier de candidature pour le programme de stages BRACONGO.\n\nVotre dossier est actuellement en cours d''analyse par notre Direction des Ressources Humaines.\n\nVotre code de suivi : {code_suivi}\n\nNous vous tiendrons informé(e) des prochaines étapes dans les meilleurs délais.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi", "email"]', 1, NOW(), NOW());

INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('dossier_incomplet', 'Dossier incomplet', 'Dossier incomplet - Action requise - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nAprès examen de votre dossier de candidature, nous constatons que celui-ci est incomplet.\n\nNous vous prions de bien vouloir compléter les pièces manquantes dans les plus brefs délais afin de poursuivre le traitement de votre candidature.\n\nVotre code de suivi : {code_suivi}\n\nVeuillez vous connecter à votre espace candidat pour mettre à jour votre dossier.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi", "email"]', 1, NOW(), NOW());

INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('resultat_test', 'Résultat du test', 'Résultat de votre test - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nNous vous informons que vous avez passé le test dans le cadre du processus de sélection des stagiaires BRACONGO.\n\nVotre dossier va maintenant être soumis à la phase de décision.\n\nVous serez informé(e) de la suite dans les meilleurs délais.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi", "email"]', 1, NOW(), NOW());

INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('reponse_lettre_recommandation', 'Réponse lettre de recommandation', 'Lettre de recommandation - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nDans le cadre de la préparation de votre stage au sein de BRACONGO, nous vous informons que la lettre de recommandation a été traitée.\n\nVotre affectation :\nDirection / Service : {direction_service}\nDate de début prévue : {date_debut}\nDate de fin prévue : {date_fin}\n\nLes prochaines étapes vous seront communiquées prochainement.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi", "direction_service", "date_debut", "date_fin"]', 1, NOW(), NOW());

INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('induction_rh', 'Induction RH', 'Induction RH - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nNous vous informons que votre session d''induction RH est programmée.\n\nCette session vous permettra de découvrir l''organisation de BRACONGO, ses valeurs, ses politiques internes et les règles de sécurité.\n\nDirection / Service d''affectation : {direction_service}\n\nVeuillez vous munir d''une pièce d''identité.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi", "direction_service"]', 1, NOW(), NOW());

INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('debut_stage', 'Début du stage', 'Votre stage débute - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nNous avons le plaisir de vous informer que votre stage au sein de BRACONGO débute officiellement.\n\nVoici les informations relatives à votre stage :\nDirection / Service : {direction_service}\nDate de début : {date_debut}\nDate de fin prévue : {date_fin}\n\nNous vous souhaitons pleine réussite dans cette expérience professionnelle.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi", "direction_service", "date_debut", "date_fin"]', 1, NOW(), NOW());

INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('envoi_evaluation', 'Évaluation de stage', 'Évaluation de votre stage - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nVotre évaluation de stage a été finalisée.\n\nNote d''évaluation : {note_evaluation}/20\nAppréciation du tuteur : {appreciation_tuteur}\n\nNous vous remercions pour votre engagement tout au long de votre stage.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi", "note_evaluation", "appreciation_tuteur"]', 1, NOW(), NOW());

INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('envoi_attestation', 'Envoi attestation', 'Votre attestation de stage - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nNous avons le plaisir de vous informer que votre attestation de stage a été générée.\n\nVous pouvez la récupérer auprès de la Direction des Ressources Humaines de BRACONGO ou la télécharger depuis votre espace candidat.\n\nNous vous remercions pour votre contribution et vous souhaitons plein succès dans la suite de votre parcours.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi"]', 1, NOW(), NOW());

INSERT INTO email_templates (slug, nom, sujet, contenu, placeholders_disponibles, actif, created_at, updated_at) VALUES
('stage_termine', 'Stage terminé', 'Fin de votre stage - BRACONGO Stages',
'Madame / Monsieur {nom},\n\nVotre stage au sein de BRACONGO est officiellement terminé.\n\nNous tenons à vous remercier pour votre implication et votre sérieux tout au long de cette expérience.\n\nN''hésitez pas à partager votre retour d''expérience depuis votre espace candidat.\n\nNous vous souhaitons une excellente continuation dans votre parcours académique et professionnel.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO',
'["nom", "prenom", "code_suivi", "note_evaluation", "appreciation_tuteur"]', 1, NOW(), NOW());
