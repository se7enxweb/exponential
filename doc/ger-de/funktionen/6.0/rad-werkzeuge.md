# RAD-Werkzeuge: Extension-Assistenten, Katalog, Übersicht und Zustandsprüfung

Diese Seite ist für Entwickler, die ihre erste (oder fünfzigste) Extension schreiben. Exponential wird auf einige wenige,
sich wiederholende Arten erweitert: eine Klasse in einem Verzeichnis, das der Kernel durchsucht, eine Klasse, die eine
INI-Einstellung nennt, eine Datei an einem Ort, der nach Konvention gefunden wird, oder ein Template in einem Design. Die
RAD-Werkzeuge bündeln dieses Wissen an einer Stelle und schreiben den Boilerplate-Code für Sie. Öffnen Sie sie unter
**Setup > RAD** (`/setup/rad`).

## Warum es hilft

Exponential zu erweitern bedeutete früher, den Kernel zu lesen, um zu erfahren, welche INI-Zeile, welches Verzeichnis
oder welchen Klassennamen ein Hook braucht. Ein Fehler in einem davon scheitert still: Die Extension lädt, nichts
geschieht. Die RAD-Werkzeuge machen aus diesem Wissen eine Liste, die Sie lesen können, Assistenten, die eine
funktionierende Extension schreiben, und Prüfungen, die Ihnen sagen, was konfiguriert ist und nicht funktionieren kann.
Eine erste Extension dauert Minuten, nicht einen Nachmittag Quelltextlektüre.

## In fünf Minuten verwenden

1. Öffnen Sie **Setup > RAD** (`/setup/rad`). Sie brauchen die Richtlinie `setup/setup`.
2. Wählen Sie im Katalog den Punkt, den Sie erweitern wollen. Mit `/setup/rad/(show)/tools` listen Sie nur die Punkte
   auf, die einen Assistenten haben.
3. Drücken Sie sein Werkzeug. Der Assistent fragt nach einem Namen und danach, was erzeugt werden soll, und führt dann
   jede Datei auf, die er unter `extension/<name>` schreiben würde, bevor er eine davon schreibt.
4. Bestätigen Sie. Schalten Sie die Extension ein (`site.ini [ExtensionSettings] ActiveExtensions[]=<name>`), führen Sie
   `php bin/php/ezpgenerateautoloads.php -e` aus und leeren Sie die Caches.
5. Öffnen Sie **Setup > RAD > Übersicht der Erweiterungspunkte**, um Ihre Registrierung gezählt zu sehen, und die
   Zustandsprüfung, um sie laden zu sehen.

## Der Katalog: 68 Erweiterungspunkte, 64 mit Werkzeug

`kernel/setup/expradcatalogue.php` führt jeden Punkt auf, an dem sich das System erweitern lässt, wie er registriert wird
(Verzeichnis, Handler, Autoload, Datei, Design, INI oder Überschreibung), die Kernel-Datei, in der der Mechanismus liegt,
und das Werkzeug, das ihn schreibt. Auf dieser Installation enthält er 68 Punkte in sieben Gruppen (Inhalte, Templates,
Module, Workflow, Speicher, Pakete, Zugriff); 64 haben ein Werkzeug, und 4 sind nur Dokumentation
(`/setup/rad/(show)/docs`). Die Mechanismen sind `directory`, `handler`, `autoload`, `file`, `design`, `ini`,
`override` und `runnable`. Die Änderung vom 14. September brachte ihn von fünfzehn Punkten auf vierundsechzig von
vierundsechzig; danach kamen vier Punkte hinzu. Jeder wurde gegen den Quellcode dieser Installation geprüft. Lesen Sie
den gepflegten Text in [Erweiterungspunkte](../../../eng-us/bc/6.0/rad-extension-points.md).

## Assistenten

Jeder Assistent baut eine **funktionierende Extension**, kein Schnipsel zum Einfügen, und **zeigt jede Datei, die er
schreiben würde, bevor er eine davon schreibt**. Außerhalb von `extension/<name>` wird nichts geschrieben, eine
bestehende Extension wird nie überschrieben, und erzeugte Templates maskieren, was sie ausgeben; siehe
[Sicherheit der RAD-Werkzeuge](../../../eng-us/bc/6.0/rad-security.md).

| Assistent (URL) | Schreibt |
|---|---|
| Design-Extension (`/setup/designextension`) | Ein Design (Templates, Stylesheets, Menge von Überschreibungen). |
| Template-Extension (`/setup/templateoperator`) | Template-Operatoren, Fetch-Funktionen, Attribut-Operatoren, Template-Funktionen. |
| Handler-Extension (`/setup/handlerextension/<point>`) | Die Erweiterungspunkte für Handler, einschließlich der vier Shop-Handler (MwSt., Versand, Summen des Warenkorbs, Wechselkurse) und Login-Handler. |
| Einstellungs-Extension (`/setup/settingsextension`) | Eine Extension, die nur INI-Einstellungen trägt, einschließlich der Anpassungspunkte für die Extension-Wurzel und den Siteaccess, die mit 6.0 kamen. |
| Inhalts-Extension (`/setup/contentextension`) | Eine Inhaltsklasse, als Skript geschrieben statt eines Nachmittags voller Klicks. |
| Datentyp-Extension (`/setup/datatype`) | Einen Datentyp. |
| Extension für Workflow-Ereignisse (`/setup/workflowevent`) | Workflow-Ereignistypen und Auslöser. |
| Modul-Extension / Modul-Assistent (`/setup/moduleextension`, `/setup/modulewizard`) | Ein Modul mit Ansichten und ein Modul über bestehenden Tabellen. |
| Handler-Assistent, Filterpunkte | Die acht Erweiterungspunkte für Filter; sieben haben einen Generator. |
| Kernel-Überschreibung (`/setup/kerneloverride`) | Eine Kernel-Klasse, ersetzt über den Autoload-Pfad für Überschreibungen, der schwerste Mechanismus und das letzte Mittel. |

Die ersten beiden RAD-Werkzeuge des alten Systems hatten sich seit 2003 nicht geändert und gaben eine PHP-Datei zurück;
sie wurden so neu geschrieben, dass sie eine vollständige Extension schreiben.

## Übersicht über die Erweiterungsfläche

**Setup > RAD > Übersicht der Erweiterungspunkte** (`/setup/radsurvey`) liest von der Platte, was diese Installation
tatsächlich anbietet: Einstellungen, die eine Klasse nennen, was ein Template aufrufen kann, was ein Ereignis ankündigt
und was bereits ersetzt wurde. Sie zählt in der Referenzinstallation am 14. September 1737 Punkte (924, bevor sie auch
Template-Aufrufe, Ereignisse und Ersetzungen erfasste); dieselbe Übersicht im aktuellen Stand zählt auf dieser
Installation 3940 in zehn Gruppen (Einstellungen, die eine Klasse nennen, 624, Orte, an denen der Kernel sucht, 263,
Schnittstellen und abstrakte Klassen 64, Modulansichten 452, Template-Operatoren und -Funktionen 424, Ereignisse 40,
Template-Überschreibungen 411, ersetzte Kernel-Klassen 3, Befehle, Cronjob-Teile und Ansichten als Klassen 485,
Registry-Einträge, die keine Klasse nennen, 1174), weil die Installation und die Erfassung beide gewachsen sind. Auf eine
Seite der Übersicht lässt sich mit `/setup/radsurvey/(show)/<section>/(offset)/<n>` verlinken. Jede Zahl ist ein Link
auf die Einträge dahinter, und jeder Befund sagt, wie er zu beheben ist. Ein Alias wird von einer kaputten Registrierung
unterschieden, sodass ein gesunder Alias nicht als Fehler gemeldet wird. Die erzeugte Referenz ist
[Die Erweiterungsfläche](../../../eng-us/bc/6.0/rad-extension-surface.md).

## Zustandsprüfung: was konfiguriert ist und nicht funktionieren kann

Die Zustandsprüfung (`kernel/setup/expradhealth.php`, auf der RAD-Seite gezeigt) meldet Einstellungen, die eine Klasse
nennen, die sich nicht laden lässt, und, weiter gefasst, diese Arten von Befunden: `setting-names-no-class`,
`directory-searched-and-not-there`, `design-extension-with-no-design`, `view-with-no-script`,
`datatype-offered-and-not-found` und `nothing-implements-it`. Im aktuellen Stand meldet sie auf dieser Installation 32
Befunde (`expRADHealth::findings()`), jeden mit seiner Behebung. Ihr erster Lauf fand einen Fehler im Kernel, der das
Laden jedes Zahlungs-Gateways verhinderte (`ezpaymentgatewaytype.php`), in derselben Änderung behoben.

Auf der Kommandozeile lädt `bin/php/checkclasses.php` jede Klasse, die diese Installation deklariert, und führt die auf,
die PHP ablehnt. Es lädt sie in einem Kindprozess, der jeden Namen ausgibt, bevor er ihn versucht, sodass eine Klasse,
die PHP beendet, als zuletzt ausgegebener Name erkannt wird und der Lauf weitergeht:

```bash
php bin/php/checkclasses.php --allow-root-user
php bin/php/checkclasses.php --kernel --quiet-ok --allow-root-user
```

`--kernel` prüft auch die Kernel-Klassen (langsamer), `--tests` schließt Klassen unter Testverzeichnissen ein, und
`--quiet-ok` gibt nichts aus, wenn alles lädt. (Optionen aus `php bin/php/checkclasses.php --help --allow-root-user`.)

Eine Klasse, die sich nicht laden lässt, ist kein stilles Problem: Der fatale Fehler reißt die ganze Anfrage mit sich, und
er bleibt unsichtbar, bis etwas diese Klasse berührt.

## Einstellungen

Die RAD-Seiten lesen keine eigenen Einstellungen; sie werden aus dem Katalog im Code und aus den INI-Dateien der
Installation gezeichnet. Der eine Einstellungseintrag ist der Menülink in `settings/menu.ini` (`Links[rad]=setup/rad`,
`PolicyList_rad[]=setup/setup`). Prüfen mit `grep -n -B1 -A3 "Links\[rad\]" settings/menu.ini`.

## Grenzen

- Ein Assistent überschreibt nie eine bestehende Extension und schreibt nichts außerhalb von `extension/<name>`.
- Übersicht und Zustandsprüfung lesen die Installation, wie sie auf der Platte liegt; führen Sie nach einer Änderung
  zuerst die Schritte für Autoload und Cache aus.
- Vier Punkte des Katalogs haben keinen Assistenten: Folgen Sie dem gepflegten Text in
  [Erweiterungspunkte](../../../eng-us/bc/6.0/rad-extension-points.md).

## Verwandte Seiten

- [Erweiterungspunkte](../../../eng-us/bc/6.0/rad-extension-points.md), [die Erweiterungsfläche](../../../eng-us/bc/6.0/rad-extension-surface.md), [Sicherheit der RAD-Werkzeuge](../../../eng-us/bc/6.0/rad-security.md)
- [Ausführbare Befehle, Cronjobs und Ansichten (Spezifikation)](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md), [Extension-Metadaten (Spezifikation)](../../spezifikationen/6.0/extension-metadaten.md)
- [Modulüberschreibung durch Extensions](modulueberschreibung-durch-extensions.md), [zusätzliche Extension-Verzeichnisse](zusaetzliche-extension-verzeichnisse.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik: September 2026, erste Hälfte, 14. September](../../../eng-us/history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
