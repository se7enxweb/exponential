---
title: "Lektion 5.2: Den Legacy-Teil herauslösen und die Konfiguration als INI schreiben"
audience: trainer, admin, developer
category: training
order: 52
summary: Die Teilnehmenden lösen den Legacy-Teil eines 5.x-Projekts in ein neues Exponential-Verzeichnis heraus, kopieren die richtigen Dateien, ersetzen die eingebauten Erweiterungen und schreiben als INI-Einstellungen auf, was die Symfony-Seite früher zur Laufzeit in Legacy eingespeist hat, einschließlich der Siteaccess-Zuordnung. 60 Minuten.
keywords: migration zu exponential, herauslösen, eingebaute erweiterungen, siteaccess-zuordnung, eingespeiste einstellungen, migrating to exponential, 5.x, extraction, ezpublish_legacy, built-in extensions, ezpublish.yml, parameters.yml, site.ini, siteaccess matching, MatchOrder, HTMLForms Secret, exp:ini, injected settings
type: help
---
# Lektion 5.2: Den Legacy-Teil herauslösen und die Konfiguration als INI schreiben

Im Stack mit zwei Kerneln las der Legacy-Kernel mehrere seiner eigenen Einstellungen nicht: Die Bridge überschrieb sie
mit Werten aus der Symfony-Konfiguration. Nach dem Herauslösen überschreibt sie nichts mehr. Diese Lektion vermittelt
das Herauslösen aus Kapitel 15.5 und die Übersetzung aus Kapitel 15.6, den Schritt, der eine migrierte Website am
häufigsten mit der falschen Datenbank oder ohne Geheimnis für den Formular-Token zurücklässt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 5: Vom 5.x-Legacy-Stack](README.md) |
| Dauer | 60 Minuten (25 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 5.1](welche-variante-und-was-uebernommen-wird.md); Shell-Zugang zu einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- das neue Exponential-Verzeichnis neben dem alten Projekt anlegen und sagen, warum nicht darin;
- aufzählen, was aus `ezpublish_legacy/` kopiert wird und was nicht;
- den Ersatz für eine eingebaute Erweiterung von 5.x nennen;
- fünf Einstellungen aufzählen, die die Symfony-Seite eingespeist hat, und wo jede in INI geschrieben wird;
- einen YAML-Siteaccess-Matcher in `[SiteAccessSettings]` übersetzen und einen Wert mit `exp:ini` prüfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Kapitel 15.5](../../../../installation/15-migration-von-5x-legacy.md#155-den-legacy-teil-herauslösen) und
[15.6](../../../../installation/15-migration-von-5x-legacy.md#156-die-symfony-konfiguration-in-ini-übertragen).

1. **Ein neues Verzeichnis** (3 Minuten). `composer create-project se7enxweb/exponential exponential`; niemals
   `composer require` im alten Projekt, dessen `symfony-app-dir` Exponential an die falsche Stelle legen würde.
2. **Was kopiert wird** (6 Minuten). Zeigen Sie die Tabelle aus 15.5: Override-Einstellungen und
   Siteaccess-Einstellungen, eigene Erweiterungen und Designs (nicht `admin`, `admin2`, `base`, `standard`), den
   Speicher mit demselben Verzeichnisnamen `<site>`, Pakete, `config.php`. Kopieren Sie die Dateien, auf die
   symbolische Links zeigen, nicht die Links (`cp -rL`).
3. **Eingebaute Erweiterungen** (4 Minuten). Nicht kopiert: Exponential hat eigene. ezjscore, ezoe und ezformtoken
   liegen im Kernel-Repository; ezflow, ezwebin und andere werden standardmäßig installiert; ezfind wird empfohlen;
   `ez_network` hat keine Entsprechung und wird aus `ActiveExtensions` entfernt.
4. **Eingespeiste Einstellungen** (7 Minuten). Zeigen Sie die Tabelle aus 15.6: Datenbankverbindung, `VarDir`,
   `AnonymousUserID`, Pfadpräfix und Wurzelknoten, die Liste der Bildvarianten (die YAML-`image_variations` ersetzten
   die Legacy-Liste), ImageMagick-Optionen und das Geheimnis für den Formular-Token `[HTMLForms] Secret`. Seit 6.0.15
   wird ein POST ohne gültigen Formular-Token mit HTTP 403 abgelehnt, deshalb ist das Geheimnis wichtig.
5. **Siteaccesses** (5 Minuten). In YAML: Liste, Gruppen, Zuordnung, System. In Exponential: Die Liste und die
   Zuordnung sind global (`[SiteAccessSettings]`), die Konfiguration jedes Siteaccess liegt in
   `settings/siteaccess/<name>/`; Gruppen haben keine direkte Entsprechung.

### Häufige Fragen

**Warum gehen Richtlinien auf `user/login` kaputt, wenn wir einen Siteaccess umbenennen?** Die Einschränkung speichert
einen CRC32 des Namens: Behalten Sie die Namen der Siteaccesses bei (15.1).

**Können wir ein Symfony-Frontend behalten?** Ja, mit der Legacy-Bridge von se7enxweb auf aktuellen Symfony-Versionen;
Kapitel 15 geht davon aus, dass Exponential für sich allein läuft ([Legacy-Bridge](../../../../../eng-us/features/6.0/legacy-bridge.md)).

**Wie sehe ich, welche Datei einen Wert setzt?** `php bin/php/console exp:ini where <file>/<block>/<key>` (15.6).

### Stolperfallen

- **Symbolische Links kopieren.** Der neue Baum zeigt dann in das alte Projekt; wenn das alte Projekt archiviert wird,
  fällt die Website aus.
- **`composer update` über Checkouts ausführen.** Kapitel 15.1: niemals `composer update` über Ihre eigenen Checkouts.
- **Die Datenbankeinstellungen dem Zufall überlassen.** In einem 5.x-Projekt waren sie in den Legacy-Dateien oft leer
  oder veraltet.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation für die nur lesenden Befehle; der Übersetzungsteil erfolgt auf Papier.

1. Sehen Sie im Wurzelverzeichnis der Installation nach, welche Datei den Datenbanknamen und das Speicherverzeichnis
   setzt:

   ```bash
   php bin/php/console exp:ini where site.ini/DatabaseSettings/Database
   php bin/php/console exp:ini where site.ini/FileSettings/VarDir
   ```

2. Lesen Sie die Liste der Siteaccesses und die Reihenfolge der Zuordnung:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/AvailableSiteAccessList
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder
   ```

3. Lesen Sie die Liste der Bildvarianten:

   ```bash
   php bin/php/console exp:ini get image.ini/AliasSettings/AliasList
   ```

4. Übersetzen Sie dieses YAML eines alten 5.x-Projekts anhand der Siteaccess-Tabelle aus Kapitel 15.6 in INI.
   Schreiben Sie für jeden Teil den Dateinamen dazu:

   ```yaml
   ezpublish:
       siteaccess:
           default_siteaccess: eng
           list: [eng, ger, admin]
           match:
               Map\URI: { en: eng, de: ger, admin: admin }
       system:
           ger:
               languages: [ger-DE, eng-GB]
   ```

5. Die `parameters.yml` desselben Projekts hat ein `secret`, eine Doctrine-Verbindung mit `pdo_mysql` und `var_dir:
   var/mysite`. Schreiben Sie für jedes die INI-Einstellungen mit Block und Schlüssel aus Kapitel 15.6 auf (verwenden
   Sie erfundene Werte, niemals ein echtes Passwort).
6. Listen Sie auf, was Sie aus `ezpublish_legacy/` dieses Projekts kopieren und welche eingebauten Erweiterungen Sie
   zurücklassen.

## Erwartetes Ergebnis

- Die Schritte 1 bis 3 zeigen die Dateien, die jeden Wert setzen, und die aktuellen Listen der Schulungsinstallation.
- Schritt 4: `[SiteSettings] DefaultAccess=eng`; `AvailableSiteAccessList[]` mit `eng`, `ger` und `admin`;
  `MatchOrder=uri`, `URIMatchType=map`, `URIMatchMapItems[]=en;eng` und die anderen beiden; in
  `settings/siteaccess/ger/site.ini.append.php` ein `[RegionalSettings] SiteLanguageList[]` mit `ger-DE` und dann
  `eng-GB`.
- Schritt 5: `[HTMLForms] Secret` mit einem langen Zufallswert; `DatabaseImplementation=ezmysqli` und die
  Verbindungsschlüssel; `[FileSettings] VarDir=var/mysite`.
- Schritt 6: Overrides, Siteaccess-Einstellungen, eigene Erweiterungen und Designs, Speicher, Pakete, `config.php`;
  nicht ezjscore, ezoe, ezformtoken oder die anderen eingebauten Erweiterungen.
- Der Trainer prüft Schritt 4 auf einem Blatt.

## Quiz

1. Warum darf Exponential nicht mit `composer require` im alten 5.x-Projekt installiert werden?
2. Welche Einstellung muss einen langen Zufallswert erhalten, weil seit 6.0.15 ein POST ohne gültigen Formular-Token
   abgelehnt wird?
   A. `[UserSettings] HashType` B. `[HTMLForms] Secret` C. `[SiteSettings] DefaultAccess` D. `[FileSettings] VarDir`
3. Was wird aus dem Doctrine-Treiber `pdo_pgsql` in `DatabaseImplementation`?
4. Wo liegt in Exponential die Konfiguration eines einzelnen Siteaccess?
5. Was geschieht mit der eingebauten 5.x-Erweiterung `ez_network`?

<details>
<summary>Antworten</summary>

1. Die alte `composer.json` setzt `symfony-app-dir`, sodass es in `ezpublish_legacy/` einer Symfony-Anwendung landen
   würde, die nicht mehr passt ([Kapitel 15.5](../../../../installation/15-migration-von-5x-legacy.md#155-den-legacy-teil-herauslösen)).
2. **B** ([Kapitel 15.6](../../../../installation/15-migration-von-5x-legacy.md#156-die-symfony-konfiguration-in-ini-übertragen)).
3. `ezpostgresql` (Kapitel 15.6).
4. In `settings/siteaccess/<name>/`; die Liste und die Zuordnung sind global in `[SiteAccessSettings]` (Kapitel 15.6).
5. Sie hat keine Entsprechung; entfernen Sie sie aus `ActiveExtensions` (Kapitel 15.5).

</details>

## Weiterführende Informationen

- [Kapitel 15.6: Die Symfony-Konfiguration in INI übersetzen](../../../../installation/15-migration-von-5x-legacy.md#156-die-symfony-konfiguration-in-ini-übertragen):
  jede eingespeiste Einstellung und jeder Siteaccess-Matcher.
- [Kapitel 10.2: Siteaccesses und Website-Adressen](../../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site):
  die Siteaccess-Zuordnung im Detail.
- [Kapitel 17.4: Einstellungen, INI und YAML](../../../../installation/17-migrationsreferenz.md#174-einstellungen-ini-und-yaml):
  die Nachschlagetabelle.

## Verwandte Seiten

- [Modul 5: Vom 5.x-Legacy-Stack](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
