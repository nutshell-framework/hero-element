<?php

declare(strict_types=1);

/*
 * Hero Element for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2022, Erdmann & Freunde
 * @author     Erdmann & Freunde <https://erdmann-freunde.de>
 * @license    MIT
 * @link       http://github.com/nutshell-framework/hero-element
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
        $schemaManager = $this->connection->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_content');

        return
        // column names needs to be written in lowercase!
         !isset($columns['herobackgroundimage'], $columns['herosize']);

        return
            $this->connection->fetchOne(
                "SELECT COUNT(*) FROM tl_content WHERE type = 'hero'"
            ) > 0;
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
                addBackgroundImage = addImage,
                heroBackgroundImage = singleSRC,
                heroSize = size,
                addImage = :addImage
            WHERE
                type = :type
        ');

        $stmt->bindValue('addImage', '0');
        $stmt->bindValue('type', 'hero');
        $stmt->execute();

        return $this->createResult(true, 'Migrate euf_hero to hero-element');
    }
}
