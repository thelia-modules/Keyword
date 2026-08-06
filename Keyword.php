<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Keyword;

use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Thelia\Core\Install\Database;
use Thelia\Module\BaseModule;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;


class Keyword extends BaseModule
{
    public function postActivation(?ConnectionInterface $con = null): void
    {
        // Garde explicite plutot qu'un try/catch sur six requetes Propel : le catch
        // large attrapait aussi les pannes de connexion et rejouait alors le SQL
        // d'installation (qui contient des DROP TABLE) sur une base peuplee.
        $this->installSchemaOnce($con, 'keyword_group', [__DIR__.'/Config/thelia.sql']);
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([
                __DIR__.'/I18n',
                __DIR__.'/Config',
                __DIR__.'/Tests',
                __FILE__,
            ])
            ->autowire(true)
            ->autoconfigure(true);
    }

    /**
     * Installation du schema idempotente ET sans risque sur une base deja peuplee :
     * Config/thelia.sql commence par des DROP TABLE, le rejouer detruit les donnees.
     * Garde en deux temps : drapeau is_initialized, sinon presence reelle de la table
     * temoin (rattrapage automatique du drapeau sur un parc existant).
     *
     * @param list<string> $sqlFiles
     */
    private function installSchemaOnce(?ConnectionInterface $con, string $witnessTable, array $sqlFiles): void
    {
        if (static::getConfigValue('is_initialized', false)) {
            return;
        }

        if ($this->tableExists($witnessTable, $con)) {
            static::setConfigValue('is_initialized', true);

            return;
        }

        (new Database($con))->insertSql(null, $sqlFiles);

        static::setConfigValue('is_initialized', true);
    }

    private function tableExists(string $table, ?ConnectionInterface $con): bool
    {
        $con ??= Propel::getReadConnection('TheliaMain');

        try {
            $statement = $con->prepare(
                'SELECT COUNT(*) FROM information_schema.tables
                 WHERE table_schema = DATABASE() AND table_name = :table'
            );
            $statement->execute(['table' => $table]);

            return 0 < (int) $statement->fetchColumn();
        } catch (\Throwable) {
            // Ne rien faire est toujours moins grave que rejouer des DROP TABLE.
            return true;
        }
    }
}
