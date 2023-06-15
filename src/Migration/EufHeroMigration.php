<?php

declare(strict_types=1);

/*
 * Card Element for Contao Open Source CMS.
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

        $columns = $schemaManager->listTableColumns('tl_content');

        return
            isset($columns['singleSRC']) &&
            !isset($columns['heroBackgroundImage']);

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

            ADD heroBackgroundImage binary(16) NULL,
            ADD heroSize varchar(128) NOT NULL default ''
        ");

        $stmt = $this->connection->prepare('
            UPDATE
                tl_content
            SET
                heroBackgroundImage = singleSRC,
                heroSize = size,
            WHERE
                type = :type
        ');

        $stmt->bindValue('type', 'card');
        $stmt->execute();

        return $this->createResult(true, 'Migrate euf_hero to hero-element');
    }
}
