<?php

declare(strict_types=1);

/*
 * This file is part of nutshell-framework/hero-element.
 *
 * (c) Erdmann & Freunde <https://erdmann-freunde.de>
 *
 * @license MIT
 */

namespace Nutshell\HeroElement\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class EufHeroMigration extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_content');

        return
        // column names needs to be written in lowercase!
         !isset($columns['herobackgroundimage'], $columns['herosize']);
    }

    public function run(): MigrationResult
    {
        $this->connection->executeQuery("
            ALTER TABLE
                tl_content

            ADD addBackgroundImage char(1) NOT NULL default '',
            ADD heroBackgroundImage binary(16) NULL,
            ADD heroSize varchar(255) NOT NULL default ''
        ");

        $stmt = $this->connection->prepare('
            UPDATE
                tl_content
            SET
            addImage = :addImage,
            addBackgroundImage = :addBackgroundImage,
            heroBackgroundImage = singleSRC,
                heroSize = size
            WHERE
                type = :type
        ');

        $stmt->bindValue('addImage', '0');
        $stmt->bindValue('addBackgroundImage', '1');
        $stmt->bindValue('type', 'hero');
        $stmt->executeStatement();

        return $this->createResult(true, 'Migrate euf_hero to hero-element');
    }
}
