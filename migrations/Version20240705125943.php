<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240705125943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, film_id INT NOT NULL, commentaire LONGTEXT NOT NULL, date_de_creation DATETIME NOT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_8F91ABF0FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bannissement (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, raison LONGTEXT NOT NULL, date_de_bannissement DATETIME NOT NULL, statut TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_906F3098FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, objet VARCHAR(255) DEFAULT NULL, corps LONGTEXT NOT NULL, date_denvoie DATETIME NOT NULL, statut TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favoris (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, film_id INT NOT NULL, date_de_creation DATETIME NOT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_8933C432FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_utilisateur (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, nom VARCHAR(150) NOT NULL, prenom VARCHAR(150) NOT NULL, date_de_naissance DATE DEFAULT NULL, code_postale INT DEFAULT NULL, adresse_postale VARCHAR(255) DEFAULT NULL, numero_de_telephone INT DEFAULT NULL, date_de_creation DATETIME NOT NULL, UNIQUE INDEX UNIQ_C224494CFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE signalement (id INT AUTO_INCREMENT NOT NULL, avis_id INT NOT NULL, utilisateur_qui_signale_id INT NOT NULL, raison LONGTEXT NOT NULL, date_de_creation DATETIME NOT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_F4B55114197E709F (avis_id), INDEX IDX_F4B55114BAA46E9B (utilisateur_qui_signale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE bannissement ADD CONSTRAINT FK_906F3098FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE info_utilisateur ADD CONSTRAINT FK_C224494CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B55114197E709F FOREIGN KEY (avis_id) REFERENCES avis (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B55114BAA46E9B FOREIGN KEY (utilisateur_qui_signale_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0FB88E14F');
        $this->addSql('ALTER TABLE bannissement DROP FOREIGN KEY FK_906F3098FB88E14F');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432FB88E14F');
        $this->addSql('ALTER TABLE info_utilisateur DROP FOREIGN KEY FK_C224494CFB88E14F');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B55114197E709F');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B55114BAA46E9B');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE bannissement');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE favoris');
        $this->addSql('DROP TABLE info_utilisateur');
        $this->addSql('DROP TABLE signalement');
        $this->addSql('DROP TABLE utilisateur');
    }
}
