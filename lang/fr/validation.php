<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash' => 'Le champ :attribute doit contenir uniquement des lettres, des chiffres et des tirets.',
    'alpha_num' => 'Le champ :attribute doit contenir uniquement des chiffres et des lettres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'before' => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between' => [
        'numeric' => 'La valeur de :attribute doit être comprise entre :min et :max.',
        'file' => 'La taille du fichier de :attribute doit être comprise entre :min et :max kilo-octets.',
        'string' => 'Le texte :attribute doit contenir entre :min et :max caractères.',
        'array' => 'Le tableau :attribute doit contenir entre :min et :max éléments.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed' => 'Le champ de confirmation :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
    'declined' => 'Le champ :attribute doit être décliné.',
    'declined_if' => 'Le champ :attribute doit être décliné quand :other a la valeur :value.',
    'different' => 'Les champs :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions' => 'La taille de l\'image :attribute n\'est pas conforme.',
    'distinct' => 'Le champ :attribute a une valeur dupliquée.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'ends_with' => 'Le champ :attribute doit se terminer par une des valeurs suivantes : :values',
    'exists' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'numeric' => 'La valeur de :attribute doit être supérieure à :value.',
        'file' => 'La taille du fichier de :attribute doit être supérieure à :value kilo-octets.',
        'string' => 'Le texte :attribute doit contenir plus de :value caractères.',
        'array' => 'Le tableau :attribute doit contenir plus de :value éléments.',
    ],
    'gte' => [
        'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :value.',
        'file' => 'La taille du fichier de :attribute doit être supérieure ou égale à :value kilo-octets.',
        'string' => 'Le texte :attribute doit contenir au moins :value caractères.',
        'array' => 'Le tableau :attribute doit contenir au moins :value éléments.',
    ],
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'Le champ :attribute est invalide.',
    'in_array' => 'Le champ :attribute n\'existe pas dans :other.',
    'integer' => 'Le champ :attribute doit être un entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit être un document JSON valide.',
    'lt' => [
        'numeric' => 'La valeur de :attribute doit être inférieure à :value.',
        'file' => 'La taille du fichier de :attribute doit être inférieure à :value kilo-octets.',
        'string' => 'Le texte :attribute doit contenir moins de :value caractères.',
        'array' => 'Le tableau :attribute doit contenir moins de :value éléments.',
    ],
    'lte' => [
        'numeric' => 'La valeur de :attribute doit être inférieure ou égale à :value.',
        'file' => 'La taille du fichier de :attribute doit être inférieure ou égale à :value kilo-octets.',
        'string' => 'Le texte :attribute doit contenir au plus :value caractères.',
        'array' => 'Le tableau :attribute ne doit pas contenir plus de :value éléments.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'numeric' => 'La valeur de :attribute ne peut être supérieure à :max.',
        'file' => 'La taille du fichier de :attribute ne peut pas dépasser :max kilo-octets.',
        'string' => 'Le texte de :attribute ne peut contenir plus de :max caractères.',
        'array' => 'Le tableau :attribute ne peut contenir plus de :max éléments.',
    ],
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'min' => [
        'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :min.',
        'file' => 'La taille du fichier de :attribute doit être supérieure à :min kilo-octets.',
        'string' => 'Le texte :attribute doit contenir au moins :min caractères.',
        'array' => 'Le tableau :attribute doit contenir au moins :min éléments.',
    ],
    'multiple_of' => 'La valeur de :attribute doit être un multiple de :value',
    'not_in' => 'Le champ :attribute sélectionné n\'est pas valide.',
    'not_regex' => 'Le format du champ :attribute n\'est pas valide.',
    'numeric' => 'Le champ :attribute doit contenir un nombre.',
    'password' => 'Le mot de passe est incorrect',
    'present' => 'Le champ :attribute doit être présent.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_array_keys' => 'Le champ :attribute doit contenir des entrées pour: :values.',
    'required_if' => 'Le champ :attribute est obligatoire quand la valeur de :other est :value.',
    'required_unless' => 'Le champ :attribute est obligatoire sauf si :other est :values.',
    'required_with' => 'Le champ :attribute est obligatoire quand :values est présent.',
    'required_with_all' => 'Le champ :attribute est obligatoire quand :values sont présents.',
    'required_without' => 'Le champ :attribute est obligatoire quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est requis quand aucun des :values n\'est présent.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit quand :other a la valeur :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit sauf si :other est dans :values.',
    'same' => 'Les champs :attribute et :other doivent être identiques.',
    'size' => [
        'numeric' => 'La valeur de :attribute doit être :size.',
        'file' => 'La taille du fichier de :attribute doit être de :size kilo-octets.',
        'string' => 'Le texte de :attribute doit contenir :size caractères.',
        'array' => 'Le tableau :attribute doit contenir :size éléments.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer avec une des valeurs suivantes : :values',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'La valeur du champ :attribute est déjà utilisée.',
    'uploaded' => 'Le fichier du champ :attribute n\'a pu être téléversé.',
    'url' => 'Le format de l\'URL de :attribute n\'est pas valide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'cv' => [
            'required' => 'Le CV est obligatoire.',
            'file' => 'Le CV doit être un fichier.',
            'mimes' => 'Le CV doit être un fichier PDF, DOC ou DOCX.',
            'max' => 'Le CV ne doit pas dépasser 2 MB.',
        ],
        'lettre_motivation' => [
            'required' => 'La lettre de motivation est obligatoire.',
            'file' => 'La lettre de motivation doit être un fichier.',
            'mimes' => 'La lettre de motivation doit être un fichier PDF, DOC ou DOCX.',
            'max' => 'La lettre de motivation ne doit pas dépasser 2 MB.',
        ],
        'certificat_scolarite' => [
            'required' => 'Le certificat de scolarité est obligatoire.',
            'file' => 'Le certificat de scolarité doit être un fichier.',
            'mimes' => 'Le certificat de scolarité doit être un fichier PDF, JPG ou PNG.',
            'max' => 'Le certificat de scolarité ne doit pas dépasser 2 MB.',
        ],
        'releves_notes' => [
            'file' => 'Les relevés de notes doivent être un fichier.',
            'mimes' => 'Les relevés de notes doivent être un fichier PDF, JPG ou PNG.',
            'max' => 'Les relevés de notes ne doivent pas dépasser 2 MB.',
        ],
        'lettres_recommandation' => [
            'file' => 'Les lettres de recommandation doivent être un fichier.',
            'mimes' => 'Les lettres de recommandation doivent être un fichier PDF, DOC ou DOCX.',
            'max' => 'Les lettres de recommandation ne doivent pas dépasser 2 MB.',
        ],
        'certificats_competences' => [
            'file' => 'Les certificats de compétences doivent être un fichier.',
            'mimes' => 'Les certificats de compétences doivent être un fichier PDF, JPG ou PNG.',
            'max' => 'Les certificats de compétences ne doivent pas dépasser 2 MB.',
        ],
        'nom' => [
            'required' => 'Le nom est obligatoire.',
            'string' => 'Le nom doit être du texte.',
            'max' => 'Le nom ne doit pas dépasser 255 caractères.',
        ],
        'prenom' => [
            'required' => 'Le prénom est obligatoire.',
            'string' => 'Le prénom doit être du texte.',
            'max' => 'Le prénom ne doit pas dépasser 255 caractères.',
        ],
        'email' => [
            'required' => 'L\'adresse email est obligatoire.',
            'email' => 'L\'adresse email doit être valide.',
            'max' => 'L\'adresse email ne doit pas dépasser 255 caractères.',
        ],
        'telephone' => [
            'required' => 'Le numéro de téléphone est obligatoire.',
            'string' => 'Le numéro de téléphone doit être du texte.',
            'max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
        ],
        'etablissement' => [
            'required' => 'L\'établissement est obligatoire.',
        ],
        'etablissement_autre' => [
            'required' => 'Le nom de l\'établissement est obligatoire.',
            'string' => 'Le nom de l\'établissement doit être du texte.',
            'max' => 'Le nom de l\'établissement ne doit pas dépasser 255 caractères.',
        ],
        'niveau_etude' => [
            'required' => 'Le niveau d\'étude est obligatoire.',
        ],
        'objectif_stage' => [
            'required' => 'L\'objectif du stage est obligatoire.',
        ],
        'poste_souhaite' => [
            'required' => 'Le poste souhaité est obligatoire.',
        ],
        'directions_souhaitees' => [
            'required' => 'Au moins une direction doit être sélectionnée.',
            'array' => 'Les directions souhaitées doivent être une liste.',
            'min' => 'Au moins une direction doit être sélectionnée.',
        ],
        'periode_debut_souhaitee' => [
            'required' => 'La date de début souhaitée est obligatoire.',
            'date' => 'La date de début souhaitée doit être une date valide.',
            'after_or_equal' => 'La date de début souhaitée ne peut pas être dans le passé.',
        ],
        'periode_fin_souhaitee' => [
            'required' => 'La date de fin souhaitée est obligatoire.',
            'date' => 'La date de fin souhaitée doit être une date valide.',
            'after' => 'La date de fin souhaitée doit être postérieure à la date de début.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'nom' => 'nom',
        'prenom' => 'prénom',
        'email' => 'adresse email',
        'telephone' => 'numéro de téléphone',
        'etablissement' => 'établissement',
        'etablissement_autre' => 'nom de l\'établissement',
        'niveau_etude' => 'niveau d\'étude',
        'faculte' => 'faculté',
        'objectif_stage' => 'objectif du stage',
        'poste_souhaite' => 'poste souhaité',
        'directions_souhaitees' => 'directions souhaitées',
        'periode_debut_souhaitee' => 'date de début souhaitée',
        'periode_fin_souhaitee' => 'date de fin souhaitée',
        'cv' => 'CV',
        'lettre_motivation' => 'lettre de motivation',
        'certificat_scolarite' => 'certificat de scolarité',
        'releves_notes' => 'relevés de notes',
        'lettres_recommandation' => 'lettres de recommandation',
        'certificats_competences' => 'certificats de compétences',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'current_password' => 'mot de passe actuel',
    ],
];