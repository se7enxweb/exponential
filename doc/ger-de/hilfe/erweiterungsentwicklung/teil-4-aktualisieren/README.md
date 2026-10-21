---
title: "Teil 4: Eine alte Erweiterung auf Exponential 6 aktualisieren"
audience: developer
category: extension-development
order: 40
summary: Teil 4 des Buches - bringen Sie eine alte Erweiterung auf den Stand von Exponential 6, von der Bestandsaufnahme und den PHP-8-Korrekturen bis zu CSP, jQuery 4, admin4-Styling, Metadaten und Tests, mit echten Vorher-nachher-Beispielen.
keywords: Buch, Teil 4, Upgrade, aktualisieren, alte Erweiterung, veraltete Funktionen, Inline-Skripte, Metadaten, Fallstudie, book, part 4, upgrade, old extension, PHP 8, deprecations, CSP, inline scripts, jQuery 4, YUI, admin4, ezinfo, extension.xml, composer.json, case study
type: help
---
# Teil 4: Eine alte Erweiterung auf Exponential 6 aktualisieren

Dies ist der vierte Teil von [Erweiterungsentwicklung: das Buch](../README.md).
Er richtet sich an alle, die eine Erweiterung haben, die für ein älteres Release und ein älteres PHP geschrieben
wurde: eine, die Sie auf GitHub gefunden haben, eine, die Ihr Team vor zehn Jahren geschrieben hat, oder eine, die
Sie übernommen haben. Exponential 6 behält das Erweiterungssystem bei, für das die alten Erweiterungen geschrieben
wurden, daher brauchen die meisten nur kleine Änderungen. Dieser Teil zeigt, welche, und zwar in der Reihenfolge, in
der Sie ihnen begegnen. Jedes Beispiel einer Änderung ist ein echter Commit aus einer Erweiterung, die dieses Projekt
auf den aktuellen Stand gebracht hat (das Repository und der kurze Commit-Hash stehen daneben, sodass Sie die ganze
Änderung auf GitHub nachlesen können).

## Wie dieser Teil aufgebaut ist

| Gruppe | Kapitel | Was Sie bekommen |
|---|---|---|
| Vorbereitung | [Warum die meisten Änderungen klein sind](warum-die-meisten-aenderungen-klein-sind.md), [Die Upgrade-Checkliste](die-upgrade-checkliste.md), [Bestandsaufnahme einer alten Erweiterung](bestandsaufnahme-einer-alten-erweiterung.md) | was Sie erwartet, die ganze Arbeit auf einer Seite und wie Sie sehen, was Sie haben |
| PHP | [PHP-8-Fehler, die alten Code stoppen](php-8-fehler-die-alten-code-stoppen.md), [Veraltetes in PHP 8.1 bis 8.5](php-8-1-bis-8-5-veraltete-funktionen.md), [Entfernte PHP-Funktionen](entfernte-php-funktionen.md), [Konstruktoren alten Stils und statische Aufrufe](alte-konstruktoren-und-statische-aufrufe.md), [Nullable-Parameter](nullable-parameter-und-typen.md), [Dynamische Eigenschaften](dynamische-eigenschaften.md), [Datenbankcode](datenbankcode-fuer-php-8.md) | jede Art von PHP-Änderung, wie Sie sie finden und wie Sie sie beheben |
| Metadaten | [ezinfo.php und extension.xml](ezinfo-und-extension-xml-modernisieren.md), [composer.json](composer-json-modernisieren.md) | die Dateien, die dem System und Composer sagen, wer Sie sind |
| Der Browser | [Inline-Skripte](csp-inline-skripte-auslagern.md), [Event-Handler-Attribute](csp-event-handler-zu-data-attributen.md), [javascript:-Links und eval](csp-javascript-links-und-eval.md), [Inline-CSS](inline-css-in-stylesheets.md), [Von YUI zu expui](yui-zu-expui.md), [jQuery 4](altes-jquery-zu-jquery-4.md) | Content Security Policy und die JavaScript-Bibliotheken von Exponential 6 |
| Die Administration | [admin4-Styling](admin4-styling-fuer-erweiterungsseiten.md), [Kontextmenüs](kontextmenues-und-admin-integration.md) | damit Ihre Seiten aussehen und funktionieren wie der Rest der Administration |
| Abschluss | [Namen und Marken](namen-und-marken-bei-einem-upgrade.md), [Tests und CI](tests-und-ci-fuer-eine-aktualisierte-erweiterung.md) | Namen, Nennung der Urheber und dafür sorgen, dass alles funktionsfähig bleibt |
| Fallstudien | [expchangeclass](fallstudie-expchangeclass.md), [der Layout-Editor auf jQuery 4](fallstudie-der-layout-editor-auf-jquery-4.md), [Zwei-Faktor-Authentifizierung](fallstudie-zwei-faktor-authentifizierung.md), [kleine Korrekturen in vielen Erweiterungen](fallstudie-kleine-korrekturen-in-vielen-erweiterungen.md) | ganze Upgrades, von Anfang bis Ende erzählt |

## Die Kurzfassung

Wenn Sie nur eine Seite dieses Teils lesen, lesen Sie [Die Upgrade-Checkliste](die-upgrade-checkliste.md). Wenn Sie
sofort anfangen wollen:

1. Kopieren Sie die Erweiterung nach `extension/` einer Entwicklungsinstallation (nie zuerst auf eine Live-Website).
2. Führen Sie `php -l` für jede PHP-Datei mit dem ältesten PHP aus, das Sie unterstützen wollen (8.0 ist das älteste,
   das Exponential 6 unterstützt).
3. Aktivieren Sie sie, öffnen Sie ihre Seiten mit eingeschalteter Debug-Ausgabe und notieren Sie jeden Fehler, jede
   Warnung und jede „Deprecated“-Zeile, die eine ihrer Dateien nennt.
4. Beheben Sie diese, eine Art nach der anderen, mit den Kapiteln dieses Teils.
5. Öffnen Sie auf ihren Seiten die Browser-Konsole und beheben Sie, was die Content Security Policy blockiert.
6. Geben Sie `ezinfo.php`, `extension.xml` und `composer.json` die aktuelle Version, Lizenz und Website.
7. Fügen Sie einen Test hinzu, der dafür sorgt, dass es so bleibt.

> **Hinweis**
> Sie dürfen eine Erweiterung nur so aktualisieren, veröffentlichen und weitergeben, wie es ihre Lizenz erlaubt. Lesen
> Sie [Lizenzen lesen, bevor Sie etwas wiederverwenden](../teil-1-vom-benutzer-zum-entwickler/lizenzen-vor-der-wiederverwendung-lesen.md),
> bevor Sie eine Erweiterung ändern, die jemand anderes geschrieben hat, und behalten Sie dessen Copyright-Hinweise bei.

## Was Sie vor dem Start brauchen

| Sie brauchen | Warum | Wo es erklärt ist |
|---|---|---|
| Eine Entwicklungsinstallation von Exponential 6 | Sie werden absichtlich Dinge kaputt machen | [Werkzeuge, die Sie brauchen](../teil-1-vom-benutzer-zum-entwickler/werkzeuge-die-sie-brauchen.md) |
| Git | um jede Ihrer Änderungen zu sehen und rückgängig zu machen | [Mit Git entwickeln](../../mit-git-entwickeln.md) |
| Die Debug-Ausgabe | um Warnungen und veraltete Aufrufe zu sehen | [Debug-Ausgabe und Logs für Entwickler](../teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md) |
| Einen Browser mit Entwicklerwerkzeugen | um zu sehen, was die Content Security Policy blockiert | [Content Security Policy für Erweiterungen](../teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md) |
| Teil 3 dieses Buches, mindestens den Aufbau einer Erweiterung | Sie müssen wissen, wo was liegt | [Teil 3](../teil-3-erweiterungsentwickler/README.md) |

## Verwandte Seiten

- Zurück: [Teil 3: Der Erweiterungsentwickler](../teil-3-erweiterungsentwickler/README.md)
- Weiter: [Warum die meisten Änderungen klein sind](warum-die-meisten-aenderungen-klein-sind.md)
- [PHP-8-Unterstützung](../../../bc/6.0/php8.md) und [PHP-8.0-Unterstützung](../../../bc/6.0/php-8.0-unterstuetzung.md)
- [Verhaltensänderungen für Erweiterungen](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Erweiterungsentwicklung: das Buch](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
