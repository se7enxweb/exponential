<?php
/**
 * The REST routes of the migrations (doc/eng-us/guides/rest-api-v1-and-v2.md, "Migrations"), read only:
 *
 *   GET /api/migration/v1/status        the status: counts, environment, engine, lock, every migration and its state
 *   GET /api/migration/v1/runs          the latest runs, newest first (?limit=20&offset=0, at most 100)
 *   GET /api/migration/v1/runs/<id>     one run and the migrations it wrote
 *   GET /api/migration/v1/snapshot      fingerprints of the classes, roles, sections, states, languages, settings
 *                                       and a content subtree (?compare=...&subtree=<node remote id>&limit=500), never
 *                                       a value: what exp:migration:generate --from-diff=<address> compares with
 *
 * The REST layer authenticates the request (an OAuth token, or a personal API key with the scope migration:read,
 * rest.ini [ApiKeySettings] Scopes[migration:read]=migration/read); the user needs the policy migration/read. The
 * answers are those of expMigrationService, as the GraphQL queries migrationStatus, migrationRuns and migrationRun
 * give them. Nothing runs or is undone through the API.
 *
 * Registered in rest.ini [ApiProvider] ProviderClass[migration]=expMigrationRestProvider.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMigrationRestProvider implements ezpRestProviderInterface
{
    public function getRoutes()
    {
        return array(
            'expMigrationStatus' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute( '/status', 'expMigrationRestController', array( 'http-get' => 'status' ) ), 1 ),
            'expMigrationRuns' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute( '/runs', 'expMigrationRestController', array( 'http-get' => 'runs' ) ), 1 ),
            'expMigrationRun' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute( '/runs/:runId', 'expMigrationRestController', array( 'http-get' => 'run' ) ), 1 ),
            'expMigrationSnapshot' => new ezpRestVersionedRoute(
                new ezpMvcRailsRoute( '/snapshot', 'expMigrationRestController', array( 'http-get' => 'snapshot' ) ), 1 ),
        );
    }

    public function getViewController()
    {
        return new expMigrationRestViewController();
    }
}

?>
