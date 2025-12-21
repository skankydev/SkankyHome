<?php 

namespace App\Utilities;

class Upload {
	
	private array $file;
	private string $uploadDir;
	private ?string $filename = null;
	private array $errors = [];
	
	public function __construct(array $file, string $uploadDir = UPLOAD_FOLDER.DS) {
		$this->file = $file;
		$this->uploadDir = rtrim($uploadDir, DS).DS ;
		debug($this->uploadDir);
		debug($this->file);
	}
	
	/**
	 * Définir un nom de fichier custom
	 */
	public function setFilename(string $filename): self {
		$this->filename = $filename;
		return $this;
	}
	
	/**
	 * Uploader le fichier
	 */
	public function upload(): bool {
		// Vérifier que le fichier existe
		if (!isset($this->file['tmp_name']) || empty($this->file['tmp_name'])) {
			$this->errors[] = 'Aucun fichier uploadé';
			return false;
		}
		
		// Créer le dossier si nécessaire
		if (!is_dir($this->uploadDir)) {
			mkdir($this->uploadDir, 0775, true);
		}
		
		// Générer le nom si pas défini
		if (!$this->filename) {
			$extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
			$this->filename = uniqid() . '_' . date('YmdHis') . '.' . $extension;
		}
		
		$destination = $this->uploadDir . $this->filename;
		
		// Déplacer le fichier
		if (!move_uploaded_file($this->file['tmp_name'], $destination)) {
			$this->errors[] = 'Erreur lors du déplacement du fichier';
			return false;
		}
		
		return true;
	}
	
	/**
	 * Obtenir le nom du fichier
	 */
	public function getFilename(): ?string {
		return $this->filename;
	}
	
	/**
	 * Obtenir le chemin relatif (pour URL)
	 */
	public function getRelativePath(): ?string {
		if (!$this->filename) return null;
		
		$relative = str_replace(PUBLIC_FOLDER, '', $this->uploadDir);
		return $relative . $this->filename;
	}
	
	/**
	 * Obtenir la taille
	 */
	public function getSize(): int {
		return $this->file['size'] ?? 0;
	}
	
	/**
	 * Obtenir les erreurs
	 */
	public function getErrors(): array {
		return $this->errors;
	}

	/**
	 * Obtenir l'URL complète du fichier
	 */
	public function getUrl(bool $absolute = false): ?string {
		if (!$this->filename) return null;
		
		$relativePath = $this->getRelativePath();
		$relativePath = str_replace(DS, '/', $relativePath);

		if ($absolute) {
			$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
			$host = $_SERVER['HTTP_HOST'];
			return $protocol . '://' . $host . $relativePath;
		}
		
		return $relativePath;
	}

	public function getFileInfo():array {
		return [
			'name' => $this->getFilename(),
			'path' => $this->getRelativePath(),
			'size' => $this->getSize(),
			'url'  => $this->getUrl(),
		];

	}
}