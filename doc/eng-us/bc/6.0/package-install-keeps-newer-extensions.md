# A site package keeps the newer installed extension

Since 6.0.15, installing a package never puts an older copy of an extension in place of the one already
installed. Before 6.0.15 an extension package replaced the installed directory whenever it was installed without
questions (`exp:install`, the kickstarter, the setup wizard): `exp:install --package=ezwebin_site` deleted the
Composer copies of ezwebin, ezstarrating, ezwt and ezgmaplocation (6.x releases) and put the copies of 2012 from the
package server in their place. The old ezstarrating then stopped the admin's full view with a fatal error on the
current kernel.

## The rule

`eZExtensionPackageHandler::install()` compares the installed copy with the package's copy before it touches
anything:

| Installed copy | Package's copy | Result |
|---|---|---|
| Composer or git manages it (any version) | anything | the installed copy is kept |
| a plain copy with a release number | a higher release number | the package's copy replaces it |
| a plain copy with a release number | the same or a lower one | the installed copy is kept |
| a plain copy with a release number | none (`dev-master`, `//autogentag//`) | the installed copy is kept |
| a plain copy without a release number | anything | the package's copy replaces it (as before) |

A copy Composer or git manages is kept even against a newer package (since 2026-10-28): Composer or the checkout
updates it, and a package that replaced it would leave `vendor/composer/installed.json` and `composer.lock`
describing files that are no longer there. Uninstalling an extension package does not delete such a directory
either; it only takes the extension out of `ActiveExtensions`, and the setup log says so.

- The version is read from `extension.xml` (`<metadata><version>`) first, as the about page reads it, and from
  `'Version'` in `ezinfo.php` when `extension.xml` has no release number. `ezinfo.php` is read as text, never
  included. A leading `v` is ignored; `6.0.10` is newer than `6.0.9`.
- Composer manages a directory when `vendor/composer/installed.json` has an install path that leads to it; git when
  the directory holds a `.git` entry.
- A kept extension is still activated (site.ini `[ExtensionSettings] ActiveExtensions`), as an installed one would
  be. The rule applies to every way a package is installed, also to an explicit "Replace extension" in the admin's
  package installation: an older copy never replaces a newer one.
- Each kept extension is named in the debug output and in `var/log/setup.log`, with both versions:

  ```
  Extension 'ezstarrating' from package 'ezstarrating_extension' not installed: the installed copy 6.0.16
  (installed by Composer) is kept; the package carries "dev-master", not a release number
  ```

## API

All static and free of side effects except reading the two files (tested without a database in
`tests/tests/kernel/classes/setup/ExtensionKeepNewerTest.php`):

- `normalizeVersion( $version )`: the release number, or null;
- `extensionVersion( $dir )`, `extensionVersionText( $dir )`: the version a directory states;
- `extensionManager( $dir, $rootDir = null )`: `'git'`, `'composer'` or `''`;
- `keepInstalled( $installedVersion, $packageVersion, $manager )`: the rule above;
- `keepInstalledReason( $installedDir, $packageDir, $rootDir = null )`: why the installed copy stays, `''` when it
  does not;
- `activateExtension( $extensionName )`: adds the extension to the override's `ActiveExtensions` once.

## The legacy site packages and the content root

The legacy site packages (ezwebin_site, ezflow_site, ezdemo_site and their `_clean` variants) swap the content root
with their "Home" page and then remove the old root. Their installers named the root by its old name, which the
clean data of 6.0 no longer uses (it names it "Websites"), so the installation stopped at the swap with
"Can't fetch node ''". The rebuilt packages name the root by its remote id (`9459d3c29e15006e45197295722c7ade`, the
same in every release).

Since 2026-10-28 the kernel finds the root for every package, also for the old ones that name it "eZ Publish":

- `eZSiteInstaller::contentObjectByName()` (used by `swapNodes()`, `removeContentObject()`, `moveTreeNode()` and the
  other steps that name an object) looks in the order `objectLookups()` gives: the step's `remote_id`, then its
  `name` (with its `class_id`), then, when the name is one the content root has carried (`CONTENT_ROOT_NAMES`:
  "eZ Publish", "Exponential", "Websites", "eZ Platform"), the object with `CONTENT_ROOT_REMOTE_ID`.
- `removeContentObject()` never purges an object whose main node is at depth 1 (`mayPurgeAtDepth()`): after the
  swap the old root is one level down, where "Home" was; without the swap it would be the content root itself.
- `swapNodes()` names the object it could not find ("Can't find the node of the object named 'X' to swap").

Tested without a database in `tests/tests/kernel/classes/setup/SiteInstallerContentRootLookupTest.php`.
