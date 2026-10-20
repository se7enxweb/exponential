---
title: Bei Übersetzungen helfen
audience: advocate
category: advocacy
order: 100
summary: Wie Fürsprecher helfen, die Administration und die Hilfe von Exponential in mehr Sprachen zu bringen - den Stand einer Sprache prüfen, fehlende Texte mit Qt Linguist oder einem XML-Editor übersetzen, Platzhalter erhalten, messen, in der laufenden Administration gegenlesen, einen Pull Request senden, Hilfeseiten übersetzen - und wie Sie Übersetzer gewinnen.
keywords: Übersetzungen, Übersetzer, Sprachen, translation.ts, Qt Linguist, ezlupdate, ezchecktranslation, Platzhalter, Gegenlesen, Pull Request, Hilfeseiten, Gewinnung, Lokalisierung, translations, translators, languages, placeholders, review, pull request, help pages, recruiting, localization
type: help
---
# Bei Übersetzungen helfen

Diese Seite ist für Fürsprecher, die neben Englisch eine weitere Sprache sprechen, und für diejenigen, die Übersetzer
gewinnen. Übersetzen ist einer der wertvollsten Beiträge: „Übersetzungen der seit 6.0 hinzugekommenen Texte (etwa
10.000) sind die nützlichste Arbeit für jede Sprache“, die noch nicht vollständig ist
([Sprachen in Exponential, Abschnitt 8](../../../en/us/languages.md#8-adding-or-completing-an-administration-translation)).

## Inhalt

1. [Den Stand Ihrer Sprache prüfen](#den-stand-ihrer-sprache-prüfen)
2. [Was Sie übersetzen können](#was-sie-übersetzen-können)
3. [Die Administration übersetzen, Schritt für Schritt](#die-administration-übersetzen-schritt-für-schritt)
4. [Regeln, die Übersetzungen funktionsfähig halten](#regeln-die-übersetzungen-funktionsfähig-halten)
5. [Ihre Arbeit einreichen](#ihre-arbeit-einreichen)
6. [Übersetzer gewinnen](#übersetzer-gewinnen)

## Den Stand Ihrer Sprache prüfen

| Stand | Bedeutung |
|---|---|
| Vollständig | Die Administration ist komplett (zehn Sprachen; mehrere maschinell unterstützt) |
| Teilweise | Eine ältere Übersetzung deckt einen Teil der heutigen Texte ab |
| Nur Inhalte | Noch keine Übersetzung der Administration |

Die Zeile Ihrer Sprache steht in [Abschnitt 4, Jede Übersetzung der Administration](../../../en/us/languages.md#4-every-administration-translation);
die Stufen in [Abschnitt 2](../../../en/us/languages.md#2-support-levels).

- **Vollständig, aber maschinell unterstützt** (Spanisch, Französisch, Ukrainisch, Hebräisch, Latein, Klingonisch): Ein
  Gegenlesen ist „der schnellste Gewinn“ ([Eine maschinell unterstützte Übersetzung gegenlesen](maschinell-unterstuetzte-uebersetzung-gegenlesen.md)).
- **Teilweise**: Übersetzen Sie die fehlenden Texte.
- **Nur Inhalte**: Beginnen Sie eine neue Übersetzung.

## Was Sie übersetzen können

| Was | Wo |
|---|---|
| Die Oberfläche der Administration | `share/translations/<code>/translation.ts` |
| Die Oberflächen von Erweiterungen | Die eigenen Übersetzungsdateien jeder Erweiterung |
| Hilfeseiten | `doc/en/us/help/...` als neue Dateien oder übersetzte Kopien, unter der GNU FDL ([Freie Dokumentation](../../../en/us/help/free-documentation.md#translate-it)) |

## Die Administration übersetzen, Schritt für Schritt

Aus [Abschnitt 8](../../../en/us/languages.md#8-adding-or-completing-an-administration-translation):

1. **Neue Texte**: Die Betreuer aktualisieren die unübersetzte Datei mit `ezlupdate`; für Ihre Sprache fügt
   `bin/linux/ezlupdate <code>` neue Texte hinzu und markiert verschwundene als veraltet.
2. **Neue Sprache**: Legen Sie `share/translations/<code>/` an, kopieren Sie die unübersetzte `translation.ts` hinein und
   setzen Sie das Attribut `language` von `<TS>` auf den Code.
3. **Übersetzen** Sie mit Qt Linguist oder einem beliebigen XML-Editor. Eine Meldung zählt, wenn ihre `<translation>`
   Text hat und kein `type="unfinished"`.
4. **Messen**: `php bin/php/ezchecktranslation.php <code> --allow-root-user` gibt den fertigen Anteil aus.
5. **Gegenlesen** Sie in der laufenden Administration mit Ihrer Locale, nachdem Sie den Übersetzungscache geleert haben.

## Regeln, die Übersetzungen funktionsfähig halten

- Behalten Sie Platzhalter (`%1`, `%name`), HTML-Entitäten und Tastaturhinweise genau wie in der Quelle.
- Behalten Sie Leerzeichen am Anfang und Ende sowie Zeilenumbrüche.
- Lassen Sie den englischen Text nicht als „Übersetzung“ stehen, es sei denn, er ist richtig (ein Produktname); der
  Qualitätstest prüft das.
- Verwenden Sie die Produktnamen korrekt ([Kurzkarte zu den Namensregeln](kurzkarte-namensregeln.md)).
- Testen Sie bei Sprachen mit Schreibrichtung von rechts nach links die Bildschirme mit `dir="rtl"` ([Von rechts nach links](../../../en/us/features/6.0/right-to-left.md)).

Die Qualitätsregeln stammen aus dem Qualitätstest für Übersetzungen, der in Abschnitt 8 beschrieben ist.

## Ihre Arbeit einreichen

Reichen Sie sie als Pull Request ein ([Mit Git entwickeln](../../../en/us/help/develop-with-git.md#contribute-a-change-with-a-pull-request)),
mit einer Commit-Nachricht wie `Updated: 300 more German strings of the administration`. Kleine, regelmäßige Pull
Requests sind einfacher als ein riesiger.

## Übersetzer gewinnen

- Zeigen Sie den Stand der Sprache und sagen Sie genau, was gebraucht wird.
- Schlagen Sie einen Sprint vor: einige Stunden gemeinsam ([Einen Beitrags-Sprint vorschlagen](einen-beitrags-sprint-vorschlagen.md)).
- Bringen Sie eine übersetzende mit einer gegenlesenden Person zusammen.
- Nutzen Sie die Vorlage unter [Vorlagen für Beiträge in sozialen Medien](vorlagen-fuer-soziale-medien.md#aufrufe-an-die-community).
- Danken Sie Übersetzern öffentlich, mit Zustimmung ([Ideen für Anerkennung](ideen-fuer-anerkennung.md)).

## Verwandte Seiten

- [Eine maschinell unterstützte Übersetzung gegenlesen](maschinell-unterstuetzte-uebersetzung-gegenlesen.md)
- [Gesprächspunkte: Sprachen und Schreibrichtung von rechts nach links](gespraechspunkte-sprachen-und-rtl.md)
- [Sprachen in Exponential](../../../en/us/languages.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
