# Konfiguration, die das Deployment verwaltet (ManagedByDeployment)

Seit 6.0.15. Ein Schalter pro Siteaccess, standardmäßig aus:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php, or an extension's
# versioned settings; never written from the admin
[ConfigurationSettings]
ManagedByDeployment=enabled
```

Ist er eingeschaltet, kommt die Konfiguration dieses Siteaccess nur aus dem Deployment (der Versionsverwaltung), und
die Administrationsoberfläche ändert sie auf dem Server nicht. Ohne ihn schreiben der INI-Editor, die Editoren für
Werkzeugleisten und Menüs und der Template-Editor `*.ini.append.php`- und `.tpl`-Dateien in das Release-Verzeichnis, wo
die Versionsverwaltung sie nicht sieht und das nächste Deployment sie verwirft.

## Was er sperrt

In einer Webanfrage, bei eingeschaltetem Schalter:

- **Ansichten.** Die Ansichten in `expManagedConfiguration::LOCKED_VIEWS` antworten wie eine in `[SiteAccessRules]`
  abgeschaltete Ansicht (standardmäßig mit 404, siehe `DisabledModuleResult`), gleich, was die Rollen sagen:
  `settings/edit`, `setup/settingstoolbar`, die Editoren für Werkzeugleisten und Menüs (Werkzeugleiste von `visual/`
  und `setup/`, toolbarlist, menuconfig), der Template-Editor (templateedit, templatecreate), der
  Einrichtungsassistent (`setup/init`), die Aktivierung von Extensions (`setup/extensions`), die Codegeneratoren
  (`setup/rad`, `setup/radsurvey`, kerneloverride, modulewizard, datatype, templateoperator, workflowevent und die
  *Extension-Assistenten) und `package/upload|install|uninstall|create|export`. Die Liste steht in der Klasse, nicht in
  einer INI-Datei, sodass eine Einstellungsdatei sie nicht abschwächen kann.
- **Menüs und Links.** Menüs, Reiter und das Dashboard blenden diese Ansichten über die Prüfung
  `fetch( 'user', 'can_open' )` aus, und die Templates von admin und admin4 blenden die Links zum Bearbeiten,
  Anlegen, Importieren und Exportieren aus, die zu ihnen führen.
- **Schreibende Stellen.** `eZINI::save()` gibt false zurück, und `expIniEditor::save()` wirft eine Ablehnung
  (`expIniException` REFUSED). Das hält auch schreibende Stellen auf, die die Liste nicht kennt: Extension-Module,
  ezjscore-Aufrufe, die Einstellungen der Debug-Leiste, die Einstellungsseiten für Audit und Mail-Absender, später
  hinzugefügte Ansichten.
- **Einrichtungsassistent.** Er startet nicht, gleich, was `[SiteAccessSettings] CheckValidity` sagt.
- **Pakete.** `package/compare` vergleicht weiter, bietet aber keinen Import an; Paketliste und Paketansicht bieten
  kein Entfernen an.

## Was offen bleibt

- Ansichten, die nur lesen: `settings/view`, die Listen der Templates und Pakete, `setup/cache`, `setup/info`, die
  Protokolle.
- Kommandozeilenskripte und das Deployment selbst (SAPIs `cli` und `phpdbg`), einschließlich `exp:ini` und
  `bin/php/*.php`.
- Schlüsselmaterial, das der Code bei der ersten Verwendung selbst erzeugt (die Audit-Schlüssel, das Geheimnis der
  Mail-Einstellungen): Es ist Laufzeitzustand, keine Konfiguration, und wird weiterhin geschrieben (Option
  `generatedSecret` von `expIniEditor::save()`).

## Webserver

Eine Webanfrage ist jede SAPI außer `cli` und `phpdbg`, und außerdem ein CLI-Prozess, der das Web bedient: die Worker
von Velocity (`QBIX_SERVER_VERSION`, `QBIX_WORKER`) und jeder CLI-Prozess mit einer Anfragemethode und einem Header
`Host` (`expManagedConfiguration::isWebServerInCli()`). PHP-FPM, Apache, FrankenPHP, der eingebaute Server und Velocity
lehnen also alle auf dieselbe Weise ab.

## Grenzen

- Das Installieren eines Pakets auf der Kommandozeile schreibt weiterhin in die Datenbank, und Pakete können Inhalte
  enthalten.
- Dateiberechtigungen auf dem Release-Verzeichnis bleiben die stärkere Maßnahme. Dieser Schalter ist ein Schutz auf
  Anwendungsebene, kein Ersatz für sie.

## Kompatibilität

`settings/site.ini` liefert `ManagedByDeployment=disabled` aus, und eine fehlende Einstellung gilt als ausgeschaltet,
sodass sich bestehende Installationen wie bisher verhalten. Templates, die die geänderten Admin-Templates überschreiben,
funktionieren weiter; sie zeigen nur weiterhin Links, die der Schalter dann mit der Seite für abgeschaltete Ansichten
beantwortet.

## Tests

`tests/tests/kernel/classes/expManagedConfigurationTest.php` (ohne Datenbank): Jede gesperrte Ansicht existiert, jede
ist mit dem Schalter gesperrt und ohne ihn offen, die nur lesenden Ansichten bleiben offen, `eZINI::save()` und
`expIniEditor::save()` lehnen in einer Webanfrage ab (auch in einem CLI-Webserver) und schreiben auf der
Kommandozeile, erzeugte Schlüssel werden weiterhin geschrieben, und `settings/site.ini` liefert den Schalter
ausgeschaltet aus.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
