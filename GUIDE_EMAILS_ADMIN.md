# 📧 Guide Administrateur — Gestion des Emails

## Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Modifier les templates d'emails](#modifier-les-templates-demails)
3. [Envoyer un email à un candidat](#envoyer-un-email-à-un-candidat)
4. [Les 4 types d'emails](#les-4-types-demails)
5. [Utiliser les placeholders](#utiliser-les-placeholders)
6. [FAQ / Dépannage](#faq--dépannage)

---

## Vue d'ensemble

La plateforme BRACONGO Stages envoie **4 types d'emails** aux candidats à des moments clés du processus de stage. Chaque email est basé sur un **template modifiable** depuis le panneau d'administration.

**Principe de fonctionnement :**

```
Template (modifiable) → Remplissage automatique des données du candidat → Prévisualisation par l'admin → Envoi
```

L'administrateur peut :
- ✏️ Modifier le contenu par défaut des templates à tout moment
- 👁️ Prévisualiser et ajuster chaque email avant envoi
- 🔄 Les placeholders (`{nom}`, `{date_test}`, etc.) sont remplacés automatiquement par les données réelles du candidat

---

## Modifier les templates d'emails

### Étape 1 — Accéder aux templates

1. Connectez-vous au panneau d'administration : `http://localhost:8000/admin`
2. Dans le menu latéral, cliquez sur **Configuration** → **Templates d'emails**

![Menu Configuration](# "Menu latéral > Configuration > Templates d'emails")

Vous verrez la liste des 4 templates :

| Nom | Utilisé quand |
|-----|--------------|
| **Convocation au test** | Le candidat est convoqué à un test |
| **Résultat : Admis** | Le candidat a réussi le test |
| **Résultat : Non admis** | Le candidat n'a pas réussi le test |
| **Confirmation des dates de stage** | Les dates de stage sont confirmées |

### Étape 2 — Modifier un template

1. Cliquez sur le bouton **✏️ Modifier** à droite du template souhaité
2. Vous accédez au formulaire d'édition avec les champs suivants :

| Champ | Description | Modifiable ? |
|-------|-------------|:------------:|
| **Nom du template** | Nom interne du template | ❌ Non |
| **Identifiant** | Identifiant technique (slug) | ❌ Non |
| **Sujet de l'email** | L'objet de l'email que le candidat verra dans sa boîte de réception | ✅ Oui |
| **Contenu du message** | Le corps du message. Vous pouvez utiliser des placeholders (voir ci-dessous) | ✅ Oui |
| **Actif** | Active ou désactive le template | ✅ Oui |

3. Modifiez le **sujet** et/ou le **contenu** selon vos besoins
4. Cliquez sur **Enregistrer** en haut à droite

> ⚠️ **Important :** Les placeholders entre accolades (ex: `{nom}`) seront automatiquement remplacés par les données réelles du candidat. Ne les supprimez pas si vous souhaitez conserver la personnalisation.

### Exemple de modification

**Avant :**
```
Madame / Monsieur {nom},

Votre stage au sein de Bracongo est donc validé.
```

**Après (personnalisé) :**
```
Cher(e) {prenom} {nom},

Toute l'équipe BRACONGO est ravie de vous annoncer que votre stage est validé !

Bienvenue dans la famille BRACONGO.
```

---

## Envoyer un email à un candidat

Les emails sont envoyés depuis la **fiche d'une candidature**.

### Étape 1 — Accéder à la candidature

1. Allez dans **Gestion des Stages** → **Candidatures**
2. Repérez le candidat dans la liste
3. Cliquez sur le bouton **Actions** (⋮) à droite de la ligne

### Étape 2 — Choisir l'action email

Les actions email disponibles dépendent du **statut** de la candidature :

| Action | Icône | Visible quand |
|--------|:-----:|---------------|
| **Envoyer convocation test** | ✉️ | Statut = « Attente test » ET date de test renseignée |
| **Envoyer résultat : Admis** | ✅ | Statut = « Test passé » ET résultat = admis |
| **Envoyer résultat : Non admis** | ❌ | Statut = « Test passé » ET résultat ≠ admis |
| **Envoyer confirmation dates** | 📅 | Statut = « Affecté » ET dates de début/fin renseignées |

### Étape 3 — Prévisualiser et envoyer

Lorsque vous cliquez sur une action email, une **fenêtre modale** s'ouvre avec :

1. **Champs spécifiques** (si applicable) :
   - Pour la convocation : **Heure du test** (par défaut 09:00)
   - Pour la confirmation : **Heure de présentation** (par défaut 08:00)

2. **Sujet de l'email** — Pré-rempli depuis le template, modifiable avant envoi

3. **Contenu du message** — Pré-rempli avec les données du candidat déjà insérées, modifiable avant envoi

> 💡 **Astuce :** Si vous modifiez l'heure du test ou l'heure de présentation, le contenu se met à jour automatiquement pour refléter la nouvelle heure.

4. Vérifiez que le contenu vous convient
5. Cliquez sur **Envoyer** (ou le bouton de confirmation)

Une notification verte confirmera l'envoi : *« Email de convocation envoyé à candidat@email.com »*

> 📝 **Note :** Les modifications faites dans la modale sont **ponctuelles** — elles ne modifient pas le template par défaut. Pour modifier le template de façon permanente, utilisez **Configuration > Templates d'emails**.

---

## Les 4 types d'emails

### 1. 📝 Convocation au test

**Quand :** Après avoir programmé un test pour le candidat (statut « Attente test »)

**Contenu par défaut :**
```
Madame / Monsieur {nom},

Dans le cadre du processus de sélection des stagiaires au sein de Bracongo,
nous avons le plaisir de vous informer que votre candidature a été retenue
pour la phase de test.

Vous êtes invité(e) à vous présenter selon les modalités suivantes :

Date : {date_test}
Heure : {heure_test}
Lieu : Bracongo - Avenue des Brasseries, numéro 7666, Quartier Kingabwa,
Commune de Limete, Kinshasa, RDC.

Nous vous prions de vous munir d'une pièce d'identité et de vous présenter
15 minutes avant l'heure indiquée.
```

**Placeholders disponibles :** `{nom}`, `{prenom}`, `{email}`, `{date_test}`, `{heure_test}`, `{code_suivi}`

---

### 2. 🎉 Résultat : Admis

**Quand :** Après avoir marqué un candidat comme admis au test

**Contenu par défaut :**
```
Madame / Monsieur {nom},

À l'issue du processus de sélection, nous avons le plaisir de vous informer
que votre candidature a été retenue.

Votre stage au sein de Bracongo est donc validé.

Notre équipe prendra contact avec vous pour finaliser les modalités
administratives.

Félicitations et bienvenue parmi nous.
```

**Placeholders disponibles :** `{nom}`, `{prenom}`, `{email}`, `{code_suivi}`

---

### 3. 📋 Résultat : Non admis

**Quand :** Après le test, si le candidat n'est pas retenu

**Contenu par défaut :**
```
Madame / Monsieur {nom},

Pour donner suite au test de sélection organisé le {date_test}, nous vous
remercions pour votre participation.

Après évaluation, nous regrettons de vous informer que vous n'avez pas
atteint la moyenne requise pour cette session.

Nous vous encourageons à poursuivre vos efforts et à postuler à de
prochaines opportunités.
```

**Placeholders disponibles :** `{nom}`, `{prenom}`, `{email}`, `{date_test}`, `{code_suivi}`

---

### 4. 📅 Confirmation des dates de stage

**Quand :** Après avoir affecté le candidat et renseigné les dates de début/fin

**Contenu par défaut :**
```
Madame / Monsieur {nom},

Nous vous confirmons que votre stage au sein de Bracongo se déroulera
selon les modalités suivantes :

Date de début : {date_debut}
Date de fin : {date_fin}
Direction / Service d'affectation : {direction_service}

Nous vous prions de vous présenter le premier jour à {heure_presentation}
auprès de la Direction des Ressources Humaines pour les formalités d'accueil.
```

**Placeholders disponibles :** `{nom}`, `{prenom}`, `{email}`, `{date_debut}`, `{date_fin}`, `{direction_service}`, `{heure_presentation}`, `{code_suivi}`

---

## Utiliser les placeholders

Les **placeholders** sont des mots-clés entre accolades qui seront automatiquement remplacés par les données réelles du candidat.

### Liste complète des placeholders

| Placeholder | Remplacé par | Exemple |
|-------------|-------------|---------|
| `{nom}` | Nom de famille du candidat | HOUÉDANOU |
| `{prenom}` | Prénom du candidat | Jean Luc |
| `{email}` | Email du candidat | jhouedanou@gmail.com |
| `{date_test}` | Date du test (format jj/mm/aaaa) | 25/02/2026 |
| `{heure_test}` | Heure du test (saisie par l'admin) | 09:00 |
| `{date_debut}` | Date de début du stage (format jj/mm/aaaa) | 01/03/2026 |
| `{date_fin}` | Date de fin du stage (format jj/mm/aaaa) | 01/06/2026 |
| `{direction_service}` | Direction/service d'affectation | Direction Production |
| `{heure_presentation}` | Heure de présentation (saisie par l'admin) | 08:00 |
| `{etablissement}` | Établissement du candidat | ESII |
| `{code_suivi}` | Code de suivi de la candidature | BRC-86CIBYPO |

### Règles d'utilisation

- ✅ Écrivez les placeholders **exactement** comme indiqué (avec les accolades)
- ✅ Vous pouvez utiliser un placeholder **plusieurs fois** dans le même message
- ✅ Vous pouvez **ajouter ou retirer** des placeholders selon vos besoins
- ❌ N'ajoutez pas d'espaces à l'intérieur des accolades (`{ nom }` ne fonctionnera pas)
- ❌ Ne modifiez pas le nom du placeholder (`{NOM}` ou `{Nom}` ne fonctionneront pas)

---

## FAQ / Dépannage

### « Je ne vois pas le bouton d'envoi d'email »

Les boutons d'envoi d'email n'apparaissent que si certaines **conditions** sont remplies :

- **Convocation** : le statut doit être « Attente test » ET une date de test doit être renseignée
- **Résultat admis** : le statut doit être « Test passé » ET le résultat doit être « admis »
- **Résultat non admis** : le statut doit être « Test passé » ET le résultat ne doit PAS être « admis »
- **Confirmation dates** : le statut doit être « Affecté » ET les dates de début/fin de stage doivent être renseignées

➡️ Vérifiez que le candidat est au bon statut et que les champs nécessaires sont remplis.

### « Le candidat n'a pas reçu l'email »

1. Vérifiez que l'adresse email du candidat est correcte dans sa fiche
2. Demandez au candidat de vérifier son dossier **Spam / Courrier indésirable**
3. Les emails sont envoyés via **Mailtrap** — vérifiez le tableau de bord Mailtrap pour le statut de livraison

### « Je veux revenir au template par défaut »

Si vous avez modifié un template et souhaitez revenir au contenu original :

1. Allez dans **Configuration** → **Templates d'emails**
2. Éditez le template concerné
3. Copiez le contenu par défaut depuis la section [Les 4 types d'emails](#les-4-types-demails) de ce guide
4. Collez-le dans le champ **Contenu du message**
5. Enregistrez

### « Je veux désactiver un type d'email »

1. Allez dans **Configuration** → **Templates d'emails**
2. Éditez le template concerné
3. Désactivez le toggle **Actif**
4. Enregistrez

> ⚠️ Si un template est désactivé, l'action d'envoi correspondante provoquera une erreur. Il est préférable de simplement ne pas utiliser le bouton d'envoi plutôt que de désactiver le template.

---

*Guide mis à jour le 20 février 2026 — Plateforme BRACONGO Stages*
