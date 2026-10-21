---
title: "Lektion 7.3: Autoloads und Aktivierung"
audience: trainer, developer
category: training
order: 73
summary: Die Teilnehmenden erzeugen nach dem Hinzufügen von Klassen das Autoload-Array der Erweiterungen neu, schalten Erweiterungen für die ganze Installation oder einen Siteaccess ein, verwenden Setup > Extensions mit Änderungen prüfen und beheben eine Klasse, die nicht gefunden wird. 45 Minuten.
keywords: entwickler, autoload, autoloads, ezpgenerateautoloads, -e, autoload/ezp_extension.php, ActiveExtensions, ActiveAccessExtensions, Setup Extensions, änderungen prüfen, autoload-arrays neu erzeugen, klasse nicht gefunden, aktivierung, checkclasses, developers course, activation, Review changes, Regenerate autoload arrays, class not found
type: help
---
# Lektion 7.3: Autoloads und Aktivierung

Zwischen dem Code einer Erweiterung und einer funktionierenden Website stehen zwei Dinge: Der Kernel muss ihre
Klassen finden, und die Erweiterung muss eingeschaltet sein. Diese Lektion behandelt beides: das Autoload-Array und
wann es neu erzeugt werden muss, die beiden Aktivierungseinstellungen, die Seite **Extensions** der Administration
und was zu tun ist, wenn eine Klasse nicht gefunden wird.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 7: Erweiterungen](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Administratoren |
| Voraussetzungen | [Lektion 7.2](extension-xml-und-ezinfo.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Autoloads der Erweiterungen mit `php bin/php/ezpgenerateautoloads.php -e` neu erzeugen und sagen, wann das
  nötig ist;
- eine Klasse im erzeugten Autoload-Array finden;
- eine Erweiterung mit `ActiveExtensions[]` oder für einen Siteaccess mit `ActiveAccessExtensions[]` einschalten;
- unter **Setup** > **Extensions** aktivieren, deaktivieren und Änderungen prüfen;
- die Ursache einer nicht gefundenen Klasse mit den Autoloads, `checkclasses.php` und den Caches finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../../../anleitungen/extensions.md),
[Die Seite Extensions](../../../../anleitungen/extensions-seite.md) und [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md)
(die Zustandsprüfung und `checkclasses.php`).

1. **Autoloads** (4 Minuten). Der Kernel durchsucht zur Laufzeit keine Ordner nach Klassen; er liest erzeugte
   Zuordnungen in `autoload/`. `php bin/php/ezpgenerateautoloads.php -e` durchsucht die Erweiterungen, `-k` den
   Kernel, `-o` die Kernel-Overrides. Führen Sie `-e` aus, nachdem Sie in einer Erweiterung eine Klasse hinzugefügt,
   umbenannt oder verschoben haben.
2. **Aktivierung** (3 Minuten). `site.ini [ExtensionSettings] ActiveExtensions[]` in `settings/override/` schaltet
   eine Erweiterung überall ein; `ActiveAccessExtensions[]` in den Einstellungen eines Siteaccess schaltet sie für
   diesen Siteaccess ein.
3. **Die Seite Extensions** (5 Minuten). **Setup** > **Extensions**: eine Liste in Ladereihenfolge, die
   Schaltflächen **Aktivieren** und **Deaktivieren** auf den Karten, **Änderungen prüfen** zeigt den Plan und die
   Risiken, bevor etwas geschrieben wird, und **Autoload-Arrays neu erzeugen** erledigt den Lauf mit `-e` aus dem
   Browser.
4. **Wenn eine Klasse nicht gefunden wird** (3 Minuten). Der Reihe nach: Liegt die Datei in `classes/` (oder einem
   anderen Ordner, den der Generator durchsucht)? Lief `-e`? Ist die Erweiterung aktiv? Wurden die Caches geleert und
   auf einem Server, der PHP im Speicher hält, die Worker neu geladen (Lektion 13.2)? `php bin/php/checkclasses.php`
   listet jede deklarierte Klasse auf, die PHP nicht laden will.

### Häufige Fragen

**Committe ich die Autoload-Dateien?** Das Autoload-Array der Erweiterungen wird pro Installation erzeugt; das
Repository Ihrer Erweiterung enthält die Klassen, nicht die erzeugte Zuordnung.

**Warum erscheint eine deaktivierte Erweiterung trotzdem in den Autoloads?** Der Generator erfasst die Klassen der
Erweiterungen auf der Festplatte; die Aktivierung entscheidet, ob Einstellungen, Designs und Module verwendet werden.

**Kann eine Aktivierung eine Website lahmlegen?** Ja, eine fehlerhafte Erweiterung kann jede Seite anhalten.
Aktivieren Sie zuerst auf der Entwicklungsinstallation; **Änderungen prüfen** warnt vor bekannten Risiken.

### Stolperfallen

- **`-e` nach einer neuen Klasse vergessen.** Die häufigste Ursache für „Klasse nicht gefunden“.
- **Ein doppelt verwendeter Klassenname.** Zwei Erweiterungen mit demselben Klassennamen: Nur eine davon lässt sich
  laden; geben Sie Ihren Klassen ein eigenes Präfix.
- **Zuerst auf der Live-Website aktivieren.** Immer erst in der Entwicklung, dann deployen.

## Praktische Übung

Auf der Entwicklungsinstallation.

1. Erzeugen Sie die Autoloads der Erweiterungen neu: `php bin/php/ezpgenerateautoloads.php -e`.
2. Suchen Sie die Klasse Ihrer Fetch-Funktion in der erzeugten Zuordnung:
   `grep -rn "mysiteHelloFunctionCollection" autoload/`.
3. Benennen Sie die Klassendatei `classes/mysitehellofunctioncollection.php` in `classes/mysite_fetch.php` um, laden
   Sie den Spielplatz aus Lektion 5.4 neu, ohne neu zu erzeugen, und lesen Sie den Fehler. Erzeugen Sie mit `-e` neu
   und führen Sie ihn erneut aus.
4. Prüfen Sie, dass jede Klasse der Installation geladen wird: `php bin/php/checkclasses.php --quiet-ok`.
5. Öffnen Sie in der Administration **Setup** > **Extensions** und suchen Sie die Karte von `mysite_hello`. Lesen
   Sie, an welcher Stelle sie geladen wird.
6. Klicken Sie bei `mysite_hello` auf **Deaktivieren**, dann auf **Änderungen prüfen**, lesen Sie den Plan und
   verlassen Sie die Seite, ohne ihn anzuwenden (oder verwerfen Sie die Änderungen).
7. Machen Sie `mysite_hello` mit der Konsole nur für Ihren öffentlichen Siteaccess aktiv (und wieder zurück):

   ```bash
   php bin/php/console exp:ini add site.ini/ExtensionSettings/ActiveAccessExtensions mysite_hello siteaccess:<sa> --dry-run
   ```

   Lesen Sie den Probelauf; führen Sie den Befehl nicht ohne `--dry-run` aus, sofern Ihr Trainer nicht darum bittet.
8. Committen Sie die umbenannte Klassendatei im Repository der Erweiterung.

## Erwartetes Ergebnis

- Schritt 2: eine Zeile, die die Klasse ihrer Datei unter `extension/mysite_hello/classes/` zuordnet.
- Schritt 3: Vor dem Neuerzeugen schlägt der Fetch fehl (die Debug-Ausgabe nennt die fehlende Klasse); nach `-e`
  funktioniert er.
- Schritt 4 gibt nichts aus, wenn jede Klasse geladen wird.
- Schritt 6: Die Prüfung listet die Deaktivierung von `mysite_hello` auf und was davon abhängt.
- Der Trainer fragt alle Teilnehmenden nach den vier Prüfungen bei „Klasse nicht gefunden“.

## Quiz

1. Welche Option von `ezpgenerateautoloads.php` durchsucht die Erweiterungen?
   A. `-k` B. `-o` C. `-e` D. `--all`
2. Wo steht die Einstellung, die eine Erweiterung für die ganze Installation aktiviert?
3. Was macht **Änderungen prüfen** auf der Seite **Extensions**?
4. Nennen Sie die vier Prüfungen, wenn eine Klasse Ihrer Erweiterung nicht gefunden wird.
5. Welches Skript listet jede deklarierte Klasse auf, die PHP nicht laden will?

<details>
<summary>Antworten</summary>

1. **C.** `-e`.
2. `site.ini [ExtensionSettings] ActiveExtensions[]`, in `settings/override/site.ini.append.php`.
3. Sie zeigt, was sich ändern würde, mit den Risiken, bevor etwas geschrieben wird.
4. Die Datei liegt in einem durchsuchten Ordner; `-e` lief; die Erweiterung ist aktiv; die Caches wurden geleert und
   die PHP-Worker neu geladen.
5. `php bin/php/checkclasses.php`.

</details>

## Weiterführende Literatur

- [Die Seite Extensions](../../../../anleitungen/extensions-seite.md): Aktivierung, Reihenfolge, Prüfen und Anwenden.
- [Autoloads und Klassenbenennung](../../../../../eng-us/help/creators/app-creators/autoloads-and-class-naming.md).
- [Zusätzliche Erweiterungsverzeichnisse](../../../../../eng-us/features/6.0/additional-extension-directories.md).

## Verwandte Seiten

- [Modul 7: Erweiterungen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
