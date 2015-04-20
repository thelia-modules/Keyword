<?php

namespace Keyword\Commands;

use Propel\Runtime\Propel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Thelia\Command\ContainerAwareCommand;

class Update extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName("keyword:update")
            ->setDescription("Update Keyword module");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = Propel::getConnection(\Keyword\Model\Map\KeywordTableMap::DATABASE_NAME);
        $connection = $connection->getWrappedConnection();

        $stmt = $connection->query("SELECT keyword_group_id, GROUP_CONCAT(keyword_id) keyword_ids
                                    FROM `keyword_group_associated_keyword`
                                    GROUP BY keyword_group_id");
        $stmt->execute();

        $output->writeln(array(
            '',
            '<question>Starting update...</question>'
        ));

        foreach ($stmt->fetchAll() as $data) {
            $msg = 'Insert of value ' . $data["keyword_group_id"] . ' into keyword_group_id field of keywords : ' . $data["keyword_ids"];

            $stmt = $connection->query('UPDATE `keyword` SET keyword_group_id = ' . $data["keyword_group_id"] . ' WHERE id IN (' . $data["keyword_ids"] . ')');
            $stmt->execute();

            $output->writeln(array(
                '',
                '<info>' . $msg . '</info>'
            ));
        }

        $output->writeln(array(
            '',
            '<info>Keyword table are now updated.</info>'
        ));

        $output->writeln(array(
            '',
            '<question>Removing obsolete tables...</question>'
        ));

        $stmt = $connection->query("DROP TABLE IF EXISTS `keyword_group_associated_keyword`");
        $stmt->execute();

        $output->writeln(array(
            '',
            '<info>Tables of the Keyword module are now updated.</info>'
        ));

        $output->writeln(array(
            '',
            '<info>Update completed successfully !</info>'
        ));


    }
}