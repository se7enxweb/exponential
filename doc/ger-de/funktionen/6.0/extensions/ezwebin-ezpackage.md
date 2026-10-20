# ezwebin-ezpackage: die Installationspakete von ezwebin

Diese Seite ist für Administratoren, die eine Website mit dem Einrichtungsassistenten installieren und wissen möchten,
was er importiert. `ezwebin-ezpackage` ist das Repository der **Pakete des Einrichtungsassistenten** für Website
Interface: die Inhaltsklassen, Beispielinhalte, Einstellungen und der Installer-Code (`ezwebin_site`,
`ezwebin_site_clean` und ihre Installer), die der Einrichtungsassistent importiert, um eine Website anzulegen.

Der Code des Designs selbst ist [ezwebin](ezwebin.md). Dieses Repository ist die Paketquelle, aus der das Design
abgespalten wurde („mass updates from parent repository ezwebin-ezpackage“).

## Wie es verwendet wird

Sie führen diese Pakete nicht von Hand aus. Der Einrichtungsassistent (oder `php bin/php/kickstarter.php`) importiert sie,
und die Installer-Klasse entscheidet, welche Extensions aktiviert, welche Siteaccesses angelegt und welche Rollen und
Richtlinien hinzugefügt werden. Um nach einer Installation die installierte Form zu sehen:

```bash
ls var/storage/packages/
```

## Was im Repository liegt

| Pfad | Was es ist |
|---|---|
| `packages/ezwebin_site` | Das Paket für die vollständige Website: `package.xml`, `settings/ezwebininstaller.php` (die Installer-Klasse `eZWebinInstaller`), die INI-, Rollen- und Einstellungsvorgaben, die der Installer anwendet (`ini-site.php`, `ini-common.php`, `ini-admin.php`, `roles.php`, `preferences.php`, `install-scripts.php`), `documents` und `files` |
| `packages/ezwebin_site_clean` | Derselbe Installer ohne die Demo-Inhalte |
| `packages/ezwebin_classes`, `ezwebin_banners`, `ezwebin_democontent`, `ezwebin_democontent_clean` | Inhaltsklassen, Banner und die Demo-Inhalte |
| `packages/ezwebin_design_blue`, `ezwebin_design_cleangray`, `ezwebin_design_gray` | Die Design-Pakete |
| `packages/ezwebin_extension` | Die Extension selbst, als Paket (`ezextension/ezwebin`) |
| `ant`, `bin` | Build-Helfer des ursprünglichen Projekts |

## Was sich geändert hat (an den Diffs geprüft)

| Datum | Änderung |
|---|---|
| 31. Dezember 2023 | Der Installer wandelt den Port des Siteaccess (`access_type_value`) mit `(int)` um, bevor er eins hinzuzählt, sodass ein als Text gespeicherter Port unter PHP 8 keine Warnung mehr auslöst, wenn der Assistent den Admin-Siteaccess anlegt. Sowohl `ezwebin_site` als auch `ezwebin_site_clean`. |
| 8. Januar 2024 | Unterstützung für **SQLite**, siehe unten. |
| 27. Januar 2024 | Die Autoload-Datei `eZArchive` der verpackten Extension: Die Commit-Nachricht spricht von einem PHP-5-Konstruktor und MySQL 8, aber der Diff ändert nur den Release-Kommentar (`1.4-0` zu `1.4.0`) und Leerraum im Lizenzkopf. Kein Verhalten hat sich geändert. |
| 28. Januar 2024 | Die Commit-Nachricht spricht von einer Korrektur für MySQL 8 in Blog-Templates; der Diff ändert nur die Homepage des Autors in der `composer.json` der verpackten Extension (auf das Repository von se7enxweb). Kein Template hat sich geändert. |
| 5. März 2024 | Aktualisierung beider Installer für den Release-Test, siehe unten. Am selben Tag wurde ein überzähliges Komma in der Klassenliste des sauberen Installers behoben (ein Parse-Problem, wenn der Assistent ihn ausführte). |
| 19. Juli 2026 | HTML5-Markup in den verpackten Templates von ezwebin: selbstschließende Schrägstriche von XHTML und veraltete Attribute `type` aus 56 Dateien entfernt, dieselbe Bereinigung wie in [ezwebin](ezwebin.md) 6.0.3. |

**8. Januar 2024, SQLite.** Wenn das Datenbankschema der Extension installiert wird, verwendet eine `sqlite`-Datenbank
`sql/sqlite/sqlite.sql` der Extension, neben den bisherigen Wahlmöglichkeiten `mysql` und `postgresql`; die
Fehlermeldung sagt „schema“ statt des falsch geschriebenen „shema“. Das ist nur ein Codepfad: Die verpackte Extension
`ezwebin` muss einen Ordner `sql/sqlite` mitbringen, damit er ihn findet. Prüfen Sie mit `ls extension/ezwebin/sql`; die
installierte Kopie von ezwebin hat überhaupt keinen Ordner `sql`, daher wird der Schritt für das Extension-Schema für sie
nicht verwendet.

**5. März 2024, Release-Test.**

- Die Versionskonstante der Installer-Klasse ist `1.6`; Konstruktoren verwenden `__construct` und rufen
  `parent::__construct` auf (PHP 8).
- Die Pakete haben die Version `6.0.0-alpha1` (benannte Version 6.0) und zeigen auf den Paket-Host von se7enx statt auf
  den alten.
- Die Liste der Extensions, die der Installer aktiviert, ist gewachsen: `ezautosave`, `ezodf`, `ezie`,
  `ezprestapiprovider`, die Theme-Extension, `ezpaypal`, `owsimpleoperator`, `swark`, `bcgooglesitemaps`,
  `bcwebsitestatistics`, `bccie`, `xrowextract`, `bcwebshop`, `ezwebin`, `ezmultiupload`.
- Ab den Paketen 6.0.15 vom 15. Oktober 2026 aktiviert der Installer `ezprestapi` statt `ezprestapiprovider` (veraltet;
  `ezprestapi` bedient REST API v1 und v2 allein, siehe [REST API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).
- Die Theme-Extension ist `sevenx-themes-simple` (`solutionExtensionName()`).
- Die sprachbasierten Siteaccesses werden nicht mehr angelegt (die Siteaccess-Liste ist die für Benutzer und Admin).
- Das var-Verzeichnis ist `var/site`.
- Die anonyme Rolle erhielt Richtlinien für `shop/buy`, für das Anlegen von Objekten `comment` in der Sektion Standard
  und für die Funktionen der Sternebewertung von `ezjscore` (siehe [ezstarrating](ezstarrating.md)).
- Das Paket `ezpaypal_extension` wird verlangt.

## Verwandte Seiten

- [ezwebin](ezwebin.md), [ezdemo](ezdemo.md)
- [Einrichtungsassistent und Editor-Siteaccess](../einrichtungsassistent-und-editor-siteaccess.md)
- [Chronik](../../../../eng-us/history/extensions/ezwebin-ezpackage.md) und [Versionshinweise](../../../../changelogs/extensions/ezwebin-ezpackage.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/ezwebin-ezpackage.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2023-12](../../../../eng-us/history/extensions/months/2023-12.md), [2024-01](../../../../eng-us/history/extensions/months/2024-01.md), [2024-03](../../../../eng-us/history/extensions/months/2024-03.md), [2026-07](../../../../eng-us/history/extensions/months/2026-07.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
