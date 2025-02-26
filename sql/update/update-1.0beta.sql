CREATE TABLE tb_bulk_emails (
	title varchar(100) NULL,
	body varchar(100) NULL,
	`date` DATETIME DEFAULT now() NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;
ALTER TABLE tb_mensagens ADD unregister_message LONGTEXT NULL;
ALTER TABLE tb_checkout
ADD COLUMN tiktok VARCHAR(255) DEFAULT NULL AFTER website,
ADD COLUMN linktree VARCHAR(255) DEFAULT NULL AFTER tiktok;