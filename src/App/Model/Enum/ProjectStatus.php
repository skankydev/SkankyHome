<?php

namespace App\Model\Enum;

enum ProjectStatus: string {
	case ACTIVE   = 'active';
	case PAUSED   = 'paused';
	case ARCHIVED = 'archived';

	/**
	 * Libellé lisible pour l'affichage.
	 * Le match est exhaustif : oublier un case = erreur fatale (garde-fou).
	 */
	public function label(): string {
		return match($this) {
			self::ACTIVE   => 'Actif',
			self::PAUSED   => 'En pause',
			self::ARCHIVED => 'Archivé',
		};
	}

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
}
