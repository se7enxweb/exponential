# Die Testsprache Lorem Ipsum: aktivieren und wieder entfernen

Exponential liefert eine Sprache mit, die nur zum Testen existiert: **Lorem Ipsum**, Code `qlo-ZZ`. Damit können Sie
Übersetzungen von Inhalten (eine zweite oder dritte Sprache, die Sprachauswahl im Editor, Fallbacks, Sprachfilter,
URL-Aliase pro Sprache, ein Entfernen) auf einer echten Installation ausprobieren, ohne eine Sprache anzutasten, die
Ihre Inhalte verwenden.

Sie wird nur als Locale-Datei mitgeliefert, `share/locale/qlo-ZZ.ini`. **Keine Installation fügt sie hinzu**: weder
die leeren Daten noch die Startdaten des Installers, weder ein Paket noch der Kickstart. Sie belegt keinen Platz für
eine Inhaltssprache, bis Sie sie hinzufügen (siehe
[die Grenze von 62 Inhaltssprachen](../../eng-us/languages.md#11-the-limit-of-62-content-languages)), und Sie sollten sie
wieder entfernen, wenn Ihr Test abgeschlossen ist.

## Inhalt

1. [Was sie ist](#1-was-sie-ist)
2. [Sie aktivieren](#2-sie-aktivieren)
3. [Inhalte mit Lorem ipsum füllen](#3-inhalte-mit-lorem-ipsum-füllen)
4. [Sie wieder entfernen](#4-sie-wieder-entfernen)
5. [Wo sie nicht angeboten wird](#5-wo-sie-nicht-angeboten-wird)

## 1. Was sie ist

| | |
|---|---|
| Code | `qlo-ZZ`. `qlo` liegt im ISO-639-2-Bereich `qaa`-`qtz`, der für die lokale Verwendung reserviert ist, sodass keine echte Sprache je diesen Code erhalten wird; `ZZ` ist der Code einer unbekannten Region. |
| Name | *Lorem Ipsum*, wie die Sprachliste und der Editor sie anzeigen. |
| Formate | Englisch: `1,234.50`, 24-Stunden-Zeit, die Woche beginnt am Montag, kurze ISO-Datumsangaben (`2023-11-14`), Währung `¤` (ISO 4217 `XXX`, keine Währung). |
| Monats- und Tagesnamen | Im Lorem-Stil, sodass ein Datum in dieser Sprache auf einen Blick erkennbar ist: *Ipsumdies 14 Temporember 2023*. |
| Kennzeichen | `[LanguageSettings] TestLanguage=true` in der Locale-Datei. Die Auswahl der Oberflächensprache und der Befehl `exp:loremipsum` lesen es. |

Das Kennzeichen macht eine Sprache zu einer Testsprache; Sie können eine eigene Locale-Datei auf dieselbe Weise
kennzeichnen. Legen Sie solche Dateien im reservierten Bereich `qaa`-`qtz` an.

## 2. Sie aktivieren

1. **Setup > Sprachen > Sprache hinzufügen.** Geben Sie `lorem` oder `qlo` in das Suchfeld ein, wählen Sie *Lorem
   Ipsum* und klicken Sie auf *Sprache hinzufügen*. Sie wird unter ihrem Namen zwischen den anderen Sprachen
   aufgeführt.
2. **Auf einem Siteaccess anzeigen (optional).** Nur wenn Sie die Übersetzungen auf einer Site sehen wollen: Fügen
   Sie `SiteLanguageList[]=qlo-ZZ` in die `site.ini.append.php` dieses Siteaccess ein, hinter den Sprachen, die er
   jetzt aufführt, und leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`). Verwenden Sie einen
   Test-Siteaccess, keinen produktiven.

Der Editor bietet nun *Lorem Ipsum* an, wenn Sie ein Objekt bearbeiten; übersetzen Sie von Hand oder füllen Sie die
Übersetzung wie unten beschrieben.

## 3. Inhalte mit Lorem ipsum füllen

`exp:loremipsum` (`bin/php/exploremipsumfill.php`) fügt die Lorem-Ipsum-Übersetzung von Objekten hinzu oder füllt
sie: Textzeilen, Textblöcke, Rich Text und Schlagwörter erhalten Lorem-ipsum-Text; jedes andere Attribut (Zahlen,
Bilder, Relationen, Datumsangaben) behält den Wert der Sprache, aus der es kopiert wird, und nicht übersetzbare
Attribute bleiben unberührt. Jedes Objekt erhält eine neue veröffentlichte Version. Dasselbe Objekt und Attribut
erhalten immer denselben Text.

```bash
php bin/php/exploremipsumfill.php 123 --dry-run              # what would be written, nothing changes
php bin/php/exploremipsumfill.php 123,124                    # two objects
php bin/php/exploremipsumfill.php --subtree=2 --limit=20     # a node and the objects below it, at most 20
./console exp:loremipsum 123 --from=ger-DE                   # copy the other attributes from German
```

Der Befehl läuft nur, wenn Sie ihn starten, und er lehnt ab:

- eine Sprache, die keine Testsprache ist (`--language=ger-DE` wird abgelehnt: Er schreibt nie Lorem ipsum in eine
  echte Sprache);
- eine Testsprache, die noch keine Inhaltssprache ist: Er fügt nie eine hinzu; fügen Sie sie zuerst wie in Abschnitt 2
  hinzu.

Ergänzen Sie `--allow-root-user`, wenn Sie ihn als root ausführen. Exit-Code 0: erledigt (oder ein Probelauf), 1:
abgelehnt oder fehlgeschlagen, 2: Aufruffehler.

## 4. Sie wieder entfernen

1. **Entfernen Sie die Übersetzungen.** Eine Sprache mit Inhalten kann nicht entfernt werden. Für jedes Objekt mit
   einer Lorem-Ipsum-Übersetzung: Öffnen Sie es, *Übersetzungen*, wählen Sie *Lorem Ipsum* und entfernen Sie sie;
   oder entfernen Sie die Testobjekte selbst. Setup > Sprachen, die Karte von Lorem Ipsum, zeigt, wie viele Objekte
   und Klassen sie noch verwenden.
2. **Entfernen Sie `qlo-ZZ` aus jeder `SiteLanguageList[]`**, in die Sie es eingetragen haben, und leeren Sie den
   INI-Cache.
3. **Setup > Sprachen**: Wählen Sie *Lorem Ipsum* und klicken Sie auf *Ausgewählte entfernen*. Ihr Platz ist wieder
   frei.
4. Leeren Sie den Inhalts-Cache (`php bin/php/ezcache.php --clear-tag=content`) und, unter Velocity,
   `./console exp:velocity cache clear`.

Vergleichen Sie die Liste der Inhaltssprachen vorher und nachher: Sie sollte dieselbe sein wie vor Beginn Ihres Tests.
Siehe [Übersetzungen und Sprachen sicher entfernen](inhaltssprachen.md#9-übersetzungen-und-sprachen-sicher-entfernen).

## 5. Wo sie nicht angeboten wird

- **Die Auswahl der Oberflächensprache** (der UI-Umschalter, `uiswitch.ini [UISwitch_language]`) lässt eine
  Testsprache aus ihren gewichteten Gruppen und aus der A-Z-Liste von `AllLanguages=enabled` heraus, selbst wenn eine
  Gruppe sie nennt. Eine Installation, die sie dort haben will, nennt sie in `AllowedValues[]`: Sie wird dann A-Z
  aufgeführt, nie gewichtet. Ihre Oberfläche bleibt englisch (es gibt keine Übersetzung), mit eigenen Datumsformaten.
- **Installation**: Keine der mitgelieferten Daten, Startdaten, Pakete oder Kickstart-Einstellungen fügt sie hinzu.

Quelle: [Sprachen in Exponential](../../eng-us/languages.md#12-test-languages-lorem-ipsum),
[Inhaltssprachen](inhaltssprachen.md).
