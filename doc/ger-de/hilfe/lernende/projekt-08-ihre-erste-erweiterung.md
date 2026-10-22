---
title: "Projekt 8: Ihre erste Erweiterung"
audience: student
category: learning
order: 32
summary: Lernprojekt 8 - eine kleine Erweiterung von Hand nach der Anleitung zu Erweiterungen bauen (ezinfo.php, extension.xml, Modul- und Designeinstellungen, ein Modul mit einer View und einem Template), sie einschalten, die View mit einer Richtlinie schützen, sie mit dem vergleichen, was der RAD-Assistent erzeugt, sie in einem eigenen Git-Repository aufbewahren und abgeben, bewertet mit einem Bewertungsraster.
keywords: lernprojekt, erweiterung, modul, view, template, ezinfo.php, extension.xml, module.ini, design.ini, ActiveExtensions, richtlinie, RAD, autoloads, php -l, lernende, study project, extension, module, policy, learner
type: help
---
# Projekt 8: Ihre erste Erweiterung

Mit Erweiterungen wird Exponential erweitert, ohne den Kernel zu ändern. In diesem Projekt bauen Sie die kleinste
funktionierende Erweiterung, eine Seite, die Besucher mit Namen begrüßt, machen sie dann sicher und bewahren sie in
Git auf. Die Schritte folgen [Erweiterungen, Abschnitt 6](../../anleitungen/extensions.md#6-eine-eigene-extension-bauen).

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Ziel](#ziel)
3. [Bevor Sie beginnen](#bevor-sie-beginnen)
4. [Aufgaben](#aufgaben)
5. [Abgabe](#abgabe)
6. [Bewertungsraster](#bewertungsraster)
7. [Zusatzziele](#zusatzziele)

## Auf einen Blick

| | |
|---|---|
| Stufe | Fortgeschritten |
| Zeit | Etwa 4 Stunden |
| Pfade | Entwickler |
| Voraussetzungen | Grundkenntnisse in PHP; ein Terminal; [Projekt 3](projekt-03-ein-template-override.md); [Git als Lernende nutzen](git-als-lernende-nutzen.md) |

## Ziel

Eine Erweiterung mit Ihrem eigenen Namen (zum Beispiel `studyhello`), die `/hello/world/<name>` ausliefert, den Namen
maskiert innerhalb des Layouts Ihrer Website zeigt, unter **Setup > Extensions** mit ihrer Version aufgeführt ist, durch
eine Richtlinie geschützt ist und in einem eigenen Git-Repository liegt.

## Bevor Sie beginnen

- Lesen Sie [Erweiterungen](../../anleitungen/extensions.md), Abschnitte 1, 4 und 6.1, und
  [Was eine Erweiterung ist](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md).
- Wählen Sie einen Namen, der Ihnen gehört: kleingeschrieben, Buchstaben, Ziffern und Unterstriche. Die Anleitung
  verwendet `myhello`.

## Aufgaben

1. **Legen Sie die Ordner an**: `extension/<name>/settings`, `modules/hello` und `design/standard/templates/hello`.
2. **Schreiben Sie `ezinfo.php` und `extension.xml`** mit derselben Version (`0.1.0`), Ihrem Namen in der
   Copyright-Zeile und „GNU General Public License v2.0 (or any later version)“ als Lizenz.
3. **Schreiben Sie die Einstellungen**: `settings/module.ini.append.php` mit `ExtensionRepositories[]=<name>` unter
   `[ModuleSettings]` und `settings/design.ini.append.php` mit `DesignExtensions[]=<name>` unter
   `[ExtensionSettings]`.
4. **Schreiben Sie das Modul und die View**: `modules/hello/module.php` mit einer View `world`, die den Parameter
   `Name` entgegennimmt, und `modules/hello/world.php`, das den Namen an das Template übergibt und `$Result`
   zurückgibt.
5. **Schreiben Sie das Template** `design/standard/templates/hello/world.tpl`, das den Namen mit `{$name|wash}`
   ausgibt.
6. **Prüfen Sie das PHP**: `php -l` für jede PHP-Datei. Erwartet: `No syntax errors detected in <file>`.
7. **Schalten Sie sie ein** und aktualisieren Sie:

   ```bash
   php bin/php/console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' <name> global --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

8. **Öffnen Sie** `/hello/world/Ada` auf Ihrer Website. Erwartet: **Hello, Ada** innerhalb des Layouts Ihrer Website,
   und **Setup > Extensions** führt Ihre Erweiterung mit der Version 0.1.0 auf.
9. **Versuchen Sie, sie zu brechen.** Öffnen Sie `/hello/world/<b>Ada</b>` (bei Bedarf URL-kodiert). Der Name muss als
   Text angezeigt werden, nicht als fettes Markup. Erklären Sie, warum.
10. **Schützen Sie die View.** Eine View ohne Eintrag `'functions'` hat keine Zugriffskontrolle: Jeder, der die
    Website erreichen kann, kann sie öffnen. Deklarieren Sie eine Funktion (`$FunctionList['read'] = array();` in
    `module.php`, `'functions' => array( 'read' )` an der View) und vergeben Sie sie unter **Benutzer > Rollen und
    Richtlinien** an eine Rolle. Prüfen Sie als anonymer Besucher und als Benutzer mit der Rolle
    ([Erweiterungen](../../anleitungen/extensions.md#61-die-kleinste-funktionierende-extension-von-hand)).
11. **Committen Sie** die Erweiterung in ihr eigenes Git-Repository, mit einer Commit-Betreffzeile wie
    `Added: The hello world view`.

## Abgabe

- Das Repository (oder ein Archiv davon) mit jeder Datei der Erweiterung.
- Screenshots: die Begrüßungsseite; **Setup > Extensions** mit Ihrer Erweiterung; die verweigerte Seite für einen
  Besucher ohne die Richtlinie.
- Die Ausgabe von `php -l` für jede PHP-Datei.
- Ein kurzer Bericht: was jede Datei tut und warum die Richtlinie wichtig ist.

## Bewertungsraster

Bewerten Sie die vier gemeinsamen Kriterien aus [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md) und diese
Prüfungen (8):

1. Der Name der Erweiterung ist der eigene der lernenden Person, kleingeschrieben.
2. `ezinfo.php` und `extension.xml` tragen dieselbe Version und die Lizenz.
3. Jede PHP-Datei besteht `php -l`.
4. Die Erweiterung wurde mit einem Override der Einstellungen eingeschaltet, nicht durch Bearbeiten von
   `settings/site.ini`.
5. Die Begrüßung funktioniert, und der Name ist maskiert.
6. Die View ist durch eine Funktion und eine Richtlinie geschützt, gezeigt für beide Fälle.
7. Die Erweiterung liegt in einem eigenen Git-Repository mit lesbaren Commit-Betreffzeilen.
8. Keine Kernel-Datei wurde geändert.

## Zusatzziele

- Erzeugen Sie mit dem RAD-Assistenten (`/setup/moduleextension`) ein Modul und vergleichen Sie seine Dateien mit
  Ihren ([Erweiterungen](../../anleitungen/extensions.md#62-die-rad-werkzeuge-schreiben-lassen)). Hinweis: Die Assistenten listen
  jede Datei vor dem Schreiben auf und überschreiben niemals eine vorhandene Erweiterung.
- Fügen Sie eine eigene Einstellung in `settings/<name>.ini` hinzu und lesen Sie sie mit `eZINI::instance( '<name>.ini' )`
  ([Erweiterungen](../../anleitungen/extensions.md#64-eigene-einstellungen-inhalte-und-cronjobs)).
- Fügen Sie eine Übersetzung der Begrüßung hinzu ([Erweiterungen, Abschnitt 6.4](../../anleitungen/extensions.md#64-eigene-einstellungen-inhalte-und-cronjobs)).
- Veröffentlichen Sie sie auf GitHub als Ihr eigenes Projekt ([Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)).
  Ihre eigene Erweiterung fällt nicht unter die CLA ([Die CLA für Lernende](die-cla-fuer-lernende.md)).

## Verwandte Seiten

- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Erweiterungen](../../anleitungen/extensions.md)
- [Lernpfad: Entwickler](lernpfad-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
