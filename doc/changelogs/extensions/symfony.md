# symfony: release notes

Read this page before you install or update `symfony`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/symfony.md).

## v3.4.50 (2026-01-13)

**Updated**

- Update composer.json changed package vendor name (`acf172d10`)
- Update composer.json added tag to test deployment workflow. No change. (`f68a4f210`)
- Update ErrorHandler.php adding tested php 8.2 support with hopes it will test working for php 8.4+. New PHP 8 Support. (`9dcfb7a69`)
- Update ArrayNode.php tested as working bugfix for our env. Note: This is likely not required anylonger but we must test with it. Incomplete Patch. (`cd6d0491d`)
- Update ExceptionCaster.php to include bugfix for php8.4 support as tested with ezplatform 2.5 gpl and confirmed working. New PHP 8.4 Support. (`df6faf1aa`)
- Update LazyLoadingValueHolderGenerator.php to include php 8.2+ tested as working changes to reflect Zend to Laminas Library Transition. New PHP 8 Supp (`118d7f4b0`)
- Update composer.json initial attemp at working around breakdowns in forking package due to dependencies conflicts from other packages. Bugfix attempt. (`1b35573f6`)
- Update composer.json (`cc1e3c14f`)
- Bugfix for composer.json validation test to pass. Bugfix. (`5fc51171c`)

## After the last tag

**Added**

- Added bugfixes required by 7x Nexus to support default installations with more graceful features implmentations. Added support for change of behavior  (`14065e4da`)

**Updated**

- PHP 8.x: Add return types to VarDumper Data class (`f2852d452`)
- PHP 8.x closure to method reference in WebProfilerExtension (`e39f6b0c1`)
- Add explicit nullable types and fix ReflectionProperty deprecation in ErrorHandler.php for PHP 8.1+ (`fd017e42a`)
- Replace twig/twig with se7enxweb/twig in composer.json (`66f8c749c`)
- PHP85 Compatiblity bugfixes for deprecation warnings. Tested. Bugfixes. (`4e267ed5b`)
- Guard ini_set() in NativeFileSessionHandler against active/sent sessions (`feb97c92f`)

## Related pages

- [Framework forks](../../features/6.0/platform-php85-framework-forks.md)
- [package map](../../specifications/6.0/platform-package-map.md)
- [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
