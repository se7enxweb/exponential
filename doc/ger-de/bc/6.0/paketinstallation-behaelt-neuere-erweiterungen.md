# Ein Website-Paket behält die neuere installierte Erweiterung

Seit 6.0.15 setzt die Installation eines Pakets nie eine ältere Kopie einer Erweiterung an die Stelle der schon
installierten. Vor 6.0.15 ersetzte ein Erweiterungspaket das installierte Verzeichnis jedes Mal, wenn es ohne
Rückfragen installiert wurde (`exp:install`, der Kickstarter, der Setup-Assistent): `exp:install --package=ezwebin_site`
löschte die Composer-Kopien von ezwebin, ezstarrating, ezwt und ezgmaplocation (Releases 6.x) und setzte die Kopien von
2012 vom Paketserver an ihre Stelle. Das alte ezstarrating brach dann die volle Ansicht der Administration mit einem
fatalen Fehler auf dem aktuellen Kernel ab.

## Die Regel

`eZExtensionPackageHandler::install()` vergleicht die installierte Kopie mit der Kopie des Pakets, bevor es irgendetwas
anfasst:

| Installierte Kopie | Kopie des Pakets | Ergebnis |
|---|---|---|
| Composer oder git verwaltet sie (jede Version) | beliebig | die installierte Kopie bleibt |
| eine einfache Kopie mit Release-Nummer | eine höhere Release-Nummer | die Kopie des Pakets ersetzt sie |
| eine einfache Kopie mit Release-Nummer | dieselbe oder eine niedrigere | die installierte Kopie bleibt |
| eine einfache Kopie mit Release-Nummer | keine (`dev-master`, `//autogentag//`) | die installierte Kopie bleibt |
| eine einfache Kopie ohne Release-Nummer | beliebig | die Kopie des Pakets ersetzt sie (wie bisher) |

Eine Kopie, die Composer oder git verwaltet, bleibt auch gegenüber einem neueren Paket (seit dem 28. Oktober 2026):
Composer oder der Checkout aktualisiert sie, und ein Paket, das sie ersetzte, ließe `vendor/composer/installed.json` und
`composer.lock` Dateien beschreiben, die es nicht mehr gibt. Auch das Deinstallieren eines Erweiterungspakets löscht ein
solches Verzeichnis nicht; es nimmt die Erweiterung nur aus `ActiveExtensions`, und das Setup-Log sagt das.

- Die Version wird zuerst aus `extension.xml` (`<metadata><version>`) gelesen, wie die Info-Seite sie liest, und aus
  `'Version'` in `ezinfo.php`, wenn `extension.xml` keine Release-Nummer hat. `ezinfo.php` wird als Text gelesen, nie
  eingebunden. Ein führendes `v` wird ignoriert; `6.0.10` ist neuer als `6.0.9`.
- Composer verwaltet ein Verzeichnis, wenn `vendor/composer/installed.json` einen Installationspfad hat, der zu ihm
  führt; git, wenn das Verzeichnis einen Eintrag `.git` enthält.
- Eine behaltene Erweiterung wird trotzdem aktiviert (site.ini `[ExtensionSettings] ActiveExtensions`), wie es eine
  installierte würde. Die Regel gilt für jeden Weg, auf dem ein Paket installiert wird, auch für ein ausdrückliches
  "Erweiterung ersetzen" bei der Paketinstallation in der Administration: Eine ältere Kopie ersetzt nie eine neuere.
- Jede behaltene Erweiterung wird in der Debug-Ausgabe und in `var/log/setup.log` mit beiden Versionen genannt:

  ```
  Extension 'ezstarrating' from package 'ezstarrating_extension' not installed: the installed copy 6.0.16
  (installed by Composer) is kept; the package carries "dev-master", not a release number
  ```

## API

Alle statisch und ohne Nebenwirkungen außer dem Lesen der beiden Dateien (ohne Datenbank getestet in
`tests/tests/kernel/classes/setup/ExtensionKeepNewerTest.php`):

- `normalizeVersion( $version )`: die Release-Nummer oder null;
- `extensionVersion( $dir )`, `extensionVersionText( $dir )`: die Version, die ein Verzeichnis angibt;
- `extensionManager( $dir, $rootDir = null )`: `'git'`, `'composer'` oder `''`;
- `keepInstalled( $installedVersion, $packageVersion, $manager )`: die Regel oben;
- `keepInstalledReason( $installedDir, $packageDir, $rootDir = null )`: warum die installierte Kopie bleibt, `''`, wenn
  sie nicht bleibt;
- `activateExtension( $extensionName )`: fügt die Erweiterung einmal zu `ActiveExtensions` des Overrides hinzu.

## Die alten Website-Pakete und die Inhaltswurzel

Die alten Website-Pakete (ezwebin_site, ezflow_site, ezdemo_site und ihre `_clean`-Varianten) tauschen die
Inhaltswurzel mit ihrer Seite "Home" und entfernen dann die alte Wurzel. Ihre Installer nannten die Wurzel bei ihrem
alten Namen, den die sauberen Daten von 6.0 nicht mehr verwenden (sie nennen sie "Websites"), daher brach die
Installation beim Tausch mit "Can't fetch node ''" ab. Die neu gebauten Pakete nennen die Wurzel bei ihrer Remote-ID
(`9459d3c29e15006e45197295722c7ade`, in jedem Release dieselbe).

Seit dem 28. Oktober 2026 findet der Kernel die Wurzel für jedes Paket, auch für die alten, die sie "eZ Publish" nennen:

- `eZSiteInstaller::contentObjectByName()` (verwendet von `swapNodes()`, `removeContentObject()`, `moveTreeNode()` und
  den anderen Schritten, die ein Objekt nennen) sucht in der Reihenfolge, die `objectLookups()` vorgibt: die
  `remote_id` des Schritts, dann sein `name` (mit seiner `class_id`), dann, wenn der Name einer ist, den die
  Inhaltswurzel getragen hat (`CONTENT_ROOT_NAMES`: "eZ Publish", "Exponential", "Websites", "eZ Platform"), das Objekt
  mit `CONTENT_ROOT_REMOTE_ID`.
- `removeContentObject()` löscht nie endgültig ein Objekt, dessen Hauptknoten auf Tiefe 1 liegt (`mayPurgeAtDepth()`):
  Nach dem Tausch liegt die alte Wurzel eine Ebene tiefer, wo "Home" war; ohne den Tausch wäre es die Inhaltswurzel
  selbst.
- `swapNodes()` nennt das Objekt, das es nicht finden konnte ("Can't find the node of the object named 'X' to swap").

Ohne Datenbank getestet in `tests/tests/kernel/classes/setup/SiteInstallerContentRootLookupTest.php`.
