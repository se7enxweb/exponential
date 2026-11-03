# Suchlinks der Administration verwenden View-Parameter

Lesen Sie diese Seite, wenn Sie Links, Lesezeichen oder Skripte haben, die eine Suche in der Administration mit einem
Query-String öffnen, etwa `content/search?SearchText=news` oder eine Adresse `content/advancedsearch?...`. Seit dem
2026-11-02 liest die Suchseite der Administration ihre Suche nur noch aus View-Parametern. Ein Frontend-Siteaccess,
dessen Design ein eigenes `content/search.tpl` mitbringt, ist nicht betroffen.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Die Suchseite der Administration nimmt ihre Suche als View-Parameter (`content/search/(text)/news`), nie aus dem Query-String. |
| Wer betroffen ist | Links, Lesezeichen, Skripte und Templates, die die Suche der Administration mit `?SearchText=...` oder dem Query-String einer erweiterten Suche öffnen. |
| Wie Sie es prüfen | Öffnen Sie `content/search?SearchText=news` in der Administration: Die Seite zeigt eine leere Suche. |
| Wie Sie es beheben | Schreiben Sie die Suche als View-Parameter: `content/search/(text)/news`. |

## Was sich geändert hat

- **`content/search?SearchText=...` öffnet eine leere Suche.** Der Query-String wird nicht gelesen; die Seite öffnet
  sich mit leerem Suchfeld und ohne Ergebnisse statt mit den Ergebnissen für die Wörter.
- **Eine alte Adresse `content/advancedsearch?...` führt zur Suchseite, ebenfalls leer.** Auch ihr Query-String
  (`SearchText`, `PhraseSearchText`, `SearchContentClassID`, `SearchSectionID`, `SearchDate` ...) wird nicht gelesen.
  Ein abgeschicktes Formular der erweiterten Suche funktioniert weiter: Die Suchseite übernimmt es und antwortet mit
  der Adresse derselben Suche.
- **Die Suche steht in der Adresse.** Die Filterleiste und das Suchfeld im Kopf werden abgeschickt (POST), und die
  Ansicht antwortet mit einer Weiterleitung (303) auf eine Adresse wie
  `content/search/(text)/easter/(class)/2,16/(section)/1/(sort)/name_asc`. Die View-Parameter sind `text`, `mode`,
  `class`, `attribute`, `section`, `modified`, `modified_from`, `modified_to`, `published`, `published_from`,
  `published_to`, `owner`, `language`, `subtree`, `sort` und `limit`; eine Liste steht einmal, ihre Werte durch Kommas
  getrennt.
- **Die Suche des Auswahlfensters (`Mode=browse`) bleibt unverändert.**

## So prüfen Sie es

1. Öffnen Sie in der Administration `content/search?SearchText=news`. Die Seite zeigt ein leeres Suchfeld.
2. Öffnen Sie `content/search/(text)/news`. Die Seite zeigt die Ergebnisse für „news“.

## So beheben Sie es

Schreiben Sie jeden Link in die neue Form um:

| Alt | Neu |
|---|---|
| `content/search?SearchText=news` | `content/search/(text)/news` |
| `content/search?SearchText=news&SectionID=1` | `content/search/(text)/news/(section)/1` |
| `content/advancedsearch?SearchText=news&SearchContentClassID=2` | `content/search/(text)/news/(class)/2` |

Lesezeichen einer Suche von vor der Änderung behalten ihre alte Adresse. Öffnen Sie die Suche erneut und legen Sie das
Lesezeichen noch einmal an; das neue Lesezeichen speichert die Adresse mit View-Parametern. PHP-Code bildet die Adresse
mit `expContentSearchFilters::viewSuffix()`, das auch einen Schrägstrich oder ein anderes Sonderzeichen in den Wörtern
maskiert.

## Siehe auch

- [Changelog 6.0.15, die Suchseite mit Filterleiste](../../../changelogs/6.0/6.0.15.md#2026-11-02-the-search-page-with-a-filter-bar-the-advanced-search-in-it)
- [Lesezeichen für Systemseiten](lesezeichen-fuer-systemseiten.md)
