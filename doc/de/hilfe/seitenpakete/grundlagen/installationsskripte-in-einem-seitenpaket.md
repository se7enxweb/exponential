---
title: "Installationsskripte in einem Seitenpaket"
audience: developer
category: install-upgrade
order: 17
summary: Wie ein Seitenpaket PHP-Installationsskripte mitbringt (settings/settings-file in package.xml, Dateien in settings/), die acht Funktionen, die der Schritt zum Anlegen der Websites aufruft, und wann, was jede zurückgeben muss, die Parameter, die sie erhalten, und wie die Seitenpakete von 7x und CJW sie nutzen.
keywords: Installationsskripte, settings-file, eZSitePreInstall, eZSiteINISettings, eZSiteAdminINISettings, eZSiteCommonINISettings, eZSiteRoles, eZSitePreferences, eZSitePostInstall, eZSiteFinalText, eZSiteInstaller, sevenxMultiSiteInstaller, EZSW-080, ezstep_create_sites, install scripts
type: help
---
# Installationsskripte in einem Seitenpaket

Diese Seite ist für Entwickler, die den PHP-Teil eines Seitenpakets schreiben oder ändern. Sie beantwortet: wie die Skripte angegeben
werden, welche Funktionen der Installer aufruft, in welcher Reihenfolge, womit und was sie zurückgeben müssen.

## Inhalt

1. [Die Skripte angeben](#die-skripte-angeben)
2. [Die Hook-Funktionen](#die-hook-funktionen)
3. [Die Reihenfolge beim Anlegen der Websites](#die-reihenfolge-beim-anlegen-der-websites)
4. [Was die Funktionen erhalten](#was-die-funktionen-erhalten)
5. [Was die Funktionen zurückgeben](#was-die-funktionen-zurückgeben)
6. [Das Muster der Installer-Klasse](#das-muster-der-installer-klasse)
7. [Regeln und Fallstricke](#regeln-und-fallstricke)
8. [Verwandte Seiten](#verwandte-seiten)

## Die Skripte angeben

Die Skripte sind PHP-Dateien im Verzeichnis `settings/` des Pakets, jede in `package.xml` aufgeführt:

```xml
<settings>
  <settings-file filename='ini-site.php'/>
  <settings-file filename='ini-admin.php'/>
  <settings-file filename='ini-common.php'/>
  <settings-file filename='sevenx-multi-site-installer.php'/>
  <settings-file filename='install-scripts.php'/>
  <settings-file filename='roles.php'/>
  <settings-file filename='preferences.php'/>
</settings>
```

Der Schritt zum Anlegen der Websites bindet jede aufgeführte Datei, die existiert, mit `include_once` aus
`<package path>/settings/<filename>` ein (`kernel/setup/steps/ezstep_create_sites.php`). Eine Datei, die in `settings/` liegt, aber nicht
aufgeführt ist, bindet der Schritt nicht ein; eine aufgeführte Datei kann sie dennoch laden (der Installer von 7x lädt
`post-install-fix.php` mit `require_once`).

| Seitenpaket | Aufgeführte Skripte |
|---|---|
| `sevenx_multisite`, `sevenx_multisite_clean` | `ini-site.php`, `ini-admin.php`, `ini-common.php`, `sevenx-multi-site-installer.php`, `install-scripts.php`, `roles.php`, `preferences.php` |
| `cjw_multisite` | dieselben, mit `cjw-multi-site-installer.php` anstelle des Installers von sevenx |
| `plain_site` | `ini-site.php`, `ini-admin.php`, `ini-common.php`, `roles.php`, `preferences.php` |

## Die Hook-Funktionen

Die Skripte definieren einfache PHP-Funktionen mit festen Namen. Der Schritt ruft jede nur auf, wenn sie existiert (`function_exists()`):

| Funktion | Aufgerufen | Gibt zurück |
|---|---|---|
| `eZSitePreInstall( $siteType )` | nachdem die Skripte eingebunden sind, bevor irgendein verlangtes Paket installiert wird | nichts |
| `eZSiteINISettings( $parameters )` | während die Einstellungen der Siteaccesses geschrieben werden | eine Liste von INI-Änderungen für den öffentlichen Siteaccess |
| `eZSiteAdminINISettings( $parameters )` | ebenso | eine Liste von INI-Änderungen für den Siteaccess der Administration |
| `eZSiteCommonINISettings( $parameters )` | ebenso | eine Liste von INI-Änderungen für `settings/override` |
| `eZSiteRoles( $parameters )` | nach den Einstellungen | eine Liste von Rollen mit Richtlinien und Zuweisungen |
| `eZSitePreferences( $parameters )` | nach den Rollen | eine Liste von Benutzervoreinstellungen |
| `eZSitePostInstall( $parameters )` | am Ende des Schritts | nichts; darf abbrechen (unten) |
| `eZSiteFinalText( $parameters )` | zuletzt | Text für die Seite **Beendet** des Assistenten |

Die Namen sind Bezeichner des Kernels, die aus dem ursprünglichen Produkt stammen und nicht umbenannt werden.

## Die Reihenfolge beim Anlegen der Websites

1. Das Seitenpaket holen (`EZSW-041`, wenn es sich nicht holen lässt).
2. Die aufgeführten Skripte einbinden, `eZSitePreInstall()` aufrufen.
3. Die Sprachen anlegen, dann die verlangten Pakete der Reihe nach installieren (siehe
   [Abhängigkeiten und die Installationsreihenfolge](abhaengigkeiten-und-die-installationsreihenfolge.md)).
4. `$parameters` bauen und `eZSiteINISettings()`, `eZSiteAdminINISettings()`, `eZSiteCommonINISettings()` aufrufen; die Ergebnisse
   schreiben.
5. `eZSiteRoles()` aufrufen und die Rollen anlegen; `eZSitePreferences()` aufrufen und die Voreinstellungen setzen.
6. `eZSitePostInstall()` mit der Sprache der Inhalte als Standardsprache aufrufen.
7. Den Siteaccess der Redaktion anlegen und den Siteaccess der Admin UI, wenn `exp_adminui` vorhanden ist.
8. `eZSiteFinalText()` aufrufen.

## Was die Funktionen erhalten

`$parameters` wird kurz vor den INI-Hooks gebaut. Seine Schlüssel umfassen:

| Schlüssel | Inhalt |
|---|---|
| `user_siteaccess`, `admin_siteaccess` | Die im Assistenten gewählten Namen der Siteaccesses |
| `siteaccess_urls`, `access_map` | Die Adressen und wie Siteaccesses zugeordnet werden |
| `package_object` | Das Seitenpaket (`eZPackage`) |
| `node_remote_map`, `object_remote_map`, `class_remote_map` | Remote-IDs der installierten Inhalte, auf IDs abgebildet |
| `preview_design`, `design_list` | Das Design der Website und die Designliste (`<design>`, `admin2`, `admin`) |
| `site_type` | Die Daten des Seitentyps aus dem Assistenten |
| `all_language_codes`, `primary_language`, `extra_language_codes`, `fallback_language_codes`, `language_map` | Die Sprachen |
| `database_settings` | Die Datenbankeinstellungen, die die Siteaccesses bekommen haben |

## Was die Funktionen zurückgeben

Die INI-Hooks geben eine Liste von Einträgen zurück, jeder für eine INI-Datei:

```php
return array(
    array( 'name' => 'site.ini',
           'reset_arrays' => true,
           'settings' => array( 'SiteSettings' => array( 'SiteName' => 'My site' ) ) ),
);
```

Die Einträge für den öffentlichen Siteaccess und die Administration werden als `.append.php`-Dateien in `settings/siteaccess/<name>/`
gespeichert, die gemeinsamen in `settings/override/`. Ein Eintrag, der `false` ist, wird übersprungen.

`eZSiteRoles()` gibt Einträge mit `name`, wahlweise `policies` (jede `module`, `function`, wahlweise `limitation`) und wahlweise
`assignments` (jede `user_id`, wahlweise `limitation` mit `identifier` und `value`) zurück. `eZSitePreferences()` gibt Einträge mit
`user_id` und `preferences` (jede `name` und `value`) zurück; ein gescheiterter wird als `EZSW-070` gemeldet.

## Das Muster der Installer-Klasse

Die Pakete von 7x und CJW halten die Logik in einer Klasse und lassen jede Hook-Datei sie nur erzeugen:

```php
// install-scripts.php of sevenx_multisite
function eZSitePreInstall( )
{
    $installer = new sevenxMultiSiteInstaller( array( 'var_dir' => 'var/site' ) );
    $installer->preInstall();
}

function eZSitePostInstall( &$parameters )
{
    $installer = new sevenxMultiSiteInstaller( $parameters );
    $installer->postInstall();
}
```

`sevenxMultiSiteInstaller` erweitert `eZSiteInstaller` des Kernels (`kernel/classes/ezsiteinstaller.php`).
`eZSiteInstaller::postInstall()` führt die Liste der Schritte aus `postInstallSteps()` nacheinander aus (`executeSteps()`). Wenn ein Schritt
abbricht, behält `eZSiteInstaller::abortedStep()` ihn, und der Schritt zum Anlegen der Websites hält mit `EZSW-080` an: Die
Nachinstallation des Seitenpakets hielt bei Schritt N von M an, und die Schritte danach liefen nicht. Was der Installer von 7x Schritt für
Schritt tut, ist in [Das Skript des Multisite-Installers](../sevenx-multisite/das-skript-des-multisite-installers.md) beschrieben; der von CJW in
[Die Installerskripte](../cjw-multisite/die-installerskripte-von-cjw-multisite.md).

## Regeln und Fallstricke

- **Ein Seitenpaket pro Lauf.** Die Hooks sind globale Funktionen, einmal eingebunden; nur die Skripte des gewählten Seitenpakets werden
  eingebunden.
- **Einstellungen werden nicht zurückgespeichert.** `eZPackage` liest `<settings>`, schreibt es aber nicht; halten Sie die Liste im
  Quell-Repository (siehe [Referenz zu package.xml](referenz-zu-package-xml.md)).
- **Die Sprache während der Nachinstallation** ist die Sprache der mitgelieferten Inhalte, nicht die gewählte Hauptsprache; der Schritt
  schaltet danach zurück.
- **Keine Geheimnisse in Skripten.** Die Skripte werden im Archiv auf dem Paketserver veröffentlicht.
- **Mit jedem Frontend testen.** Der Assistent, der Kickstarter und `exp:install` führen alle denselben Schritt zum Anlegen der Websites aus,
  sodass ein Testlauf den Codepfad abdeckt; testen Sie zuerst mit einem Probelauf (siehe
  [Wie der Kickstarter und die Konsole Pakete nutzen](wie-der-kickstarter-und-die-konsole-pakete-nutzen.md)).

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Das Installerskript ändern](../eigene/das-installerskript-aendern.md)
- [Eine Installationsmethode wählen, 4.3 Seitenpakete](../../../../en/us/install/04-choosing-an-install-method.md#43-site-packages-what-is-installed)
- [Der Setup-Assistent, 5.3.15 Websites anlegen](../../../../en/us/install/05-setup-wizard.md#5315-creating-sites)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md): 7x und die Exponential Foundation ermutigen Sie ausdrücklich, Ihr
  Seitenpaket per Pull Request einzureichen.
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
