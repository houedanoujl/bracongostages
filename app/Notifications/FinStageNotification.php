<?php

namespace App\Notifications;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FinStageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Candidature $candidature
    ) {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre stage BRACONGO est terminé - Partagez votre expérience !')
            ->greeting('Bonjour ' . $this->candidature->prenom . ' ' . $this->candidature->nom . ',')
            ->line('Nous espérons que votre stage chez BRACONGO s\'est bien déroulé et qu\'il a répondu à vos attentes.')
            ->line('Votre stage s\'est terminé le **' . $this->candidature->date_fin_stage->format('d/m/Y') . '**. Nous tenons à vous remercier pour votre contribution et votre engagement tout au long de cette période.')
            ->line('**Code de suivi :** ' . $this->candidature->code_suivi)
            ->line('**Durée du stage :** ' . $this->candidature->date_debut_stage->diffInDays($this->candidature->date_fin_stage) . ' jours')
            ->line('**Établissement :** ' . $this->candidature->etablissement)
            ->line('**Votre retour est précieux pour nous !**')
            ->line('En quelques minutes, vous pouvez nous aider à améliorer l\'expérience des futurs stagiaires en évaluant votre stage.')
            ->action('Évaluer mon stage', route('candidature.evaluation', $this->candidature))
            ->line('**Pourquoi évaluer votre stage ?**')
            ->line('• Nous aider à améliorer l\'accueil des futurs stagiaires')
            ->line('• Partager vos suggestions d\'amélioration')
            ->line('• Contribuer à l\'évolution de nos programmes de stage')
            ->line('• Maintenir le contact avec BRACONGO pour de futures opportunités')
            ->line('L\'évaluation ne prend que quelques minutes et vos réponses restent confidentielles.')
            ->line('Merci de prendre le temps de partager votre expérience avec nous !')
            ->salutation('L\'équipe Ressources Humaines BRACONGO');
    }

    public function toArray($notifiable): array
    {
        return [
            'candidature_id' => $this->candidature->id,
            'code_suivi' => $this->candidature->code_suivi,
            'candidat_nom' => $this->candidature->nom_complet,
            'date_fin_stage' => $this->candidature->date_fin_stage->format('Y-m-d'),
            'type' => 'fin_stage',
        ];
    }
} 