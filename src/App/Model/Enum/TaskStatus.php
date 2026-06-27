<?php

namespace App\Model\Enum;

enum TaskStatus: string {
	
	case TODO    = 'todo';
	case DOING   = 'doing';
	case DONE    = 'done';
	case BLOCKED = 'blocked';


	/**
	 * value => label, prêt pour l'option `options` d'un SelectField.
	 */
	public static function options(): array {
		$out = [];
		foreach (self::cases() as $case) {
			$out[$case->value] = $case->label();
		}
		return $out;
	}
	
	/**
	 * Libellé lisible pour l'affichage.
	 * Le match est exhaustif : oublier un case = erreur fatale (garde-fou).
	 */
	public function label(): string {
		return match($this) {
			self::TODO    => 'À faire',
			self::DOING   => 'En cours',
			self::DONE    => 'Terminé',
			self::BLOCKED => 'Bloqué',
		};
	}


	public function class() : string {
		return match($this) {
			self::TODO    => 'status-error',
			self::DOING   => 'status-warning',
			self::DONE    => 'status-success',
			self::BLOCKED => 'status-disabled',
		};
	}

	public function pretty() : string {
		return '<div class="'.$this->class().'">'.$this->label().'</div>';
	}

}
