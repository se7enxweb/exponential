# URL-Zuordnungen: Import, Export und Rückgängig in der Administration

Diese Seite erklärt die Seiten für URL-Zuordnungen in der Administrationsoberfläche: den Import einer Datei mit
alten Adressen in den URL-Übersetzer, die Platzhalter und die Weiterleitungen (`/content/urlimport`), die
Importhistorie mit dem Rückgängigmachen (`/content/urlimporthistory`), den Export (`/content/urlexport`) und die
Liste der Weiterleitungen (`/content/urlredirects`). Die Schaltflächen **Importieren**, **Exportieren**,
**Importhistorie** und **Weiterleitungen** über dem URL-Übersetzer und den Platzhaltern führen dorthin.

Sie richtet sich an Administratoren, die eine Website auf diese Installation umziehen oder die Adressen einer alten
Website erhalten wollen. Die Dateiformate beschreibt
[Die Dateiformate der URL-Zuordnungen](../spezifikationen/6.0/url-zuordnungsdateiformate.md); was der Import einer
Installation hinzufügt und die Konsolenbefehle beschreibt
[URL-Zuordnungen importieren und exportieren](../bc/6.0/url-zuordnungen-importieren.md).

[Anleitungen](README.md) · Siehe auch: [Links, URL-Aliase, Platzhalter und Suchstatistik](urls-und-aliase.md),
[Identität bestätigen](identitaet-bestaetigen.md), [URL-Zuordnungen importieren und exportieren](../funktionen/6.0/url-zuordnungen-importieren-und-exportieren.md),
[URLs migrieren](../hilfe/urls-migrieren.md) (ein Tutorial), [URL-Zuordnungen als Beispiele](../beispiele/url-zuordnungen/README.md)

## 1. Eine URL-Zuordnung importieren

**Importieren** (`/content/urlimport`) nimmt eine Datei oder in das Textfeld eingefügte Zeilen: CSV, JSON, Excel
(`.xlsx`), OpenDocument (`.ods`), Apache- oder nginx-Umschreibregeln, höchstens 10 MB. Das Format ergibt sich aus
dem Namen oder dem Inhalt der Datei; wählen Sie es nur, wenn beides nichts sagt.

| Feld | Was es entscheidet |
|---|---|
| Siteaccess für reine Pfade | Der Siteaccess der Zeilen mit einem Pfad (`/ueber-uns`). Eine Zeile mit vollständiger Adresse (`https://www.alte-seite.de/ueber-uns`) nimmt den Siteaccess ihres Hosts. |
| Sprache | Die Sprache der Zeilen ohne eigene; standardmäßig die Sprache des Siteaccess. |
| Wenn eine alte Adresse schon einen Eintrag hat | Den Eintrag behalten und die Zeile überspringen, oder ihn ersetzen. |
| Alte Hosts | `www.alte-seite.de = site`, einer pro Zeile: der Siteaccess, für den ein alter Host steht. Zeilen eines Hosts, für den diese Installation nicht antwortet, werden sonst nicht importiert. |

**Vorschau** behält die Datei auf dem Server und zeigt, was jede Zeile bewirken würde. Noch wird nichts geschrieben.

## 2. Die Vorschau

Jede Zeile der Datei steht mit ihrem Zustand in der Tabelle; die Zustände filtern die Tabelle:

| Zustand | Bedeutung | Importiert |
|---|---|---|
| Neu | An der alten Adresse ist noch nichts | ja (angehakt) |
| Geändert | An der alten Adresse ist ein anderer Eintrag | nur, wenn er ersetzt werden darf |
| Unverändert | Derselbe Eintrag ist schon da | nein |
| Doppelt | Eine frühere Zeile der Datei hat dieselbe alte Adresse; die erste zählt | nein |
| Konflikt | Die alte Adresse ist eine Seite dieser Website, eine Moduladresse oder die Startseite | nie |
| Ziel fehlt | Die neue Adresse gibt es auf dieser Website nicht | nein |
| Unbekannter Host | Der alte Host gehört nicht zu dieser Installation: unter **Neu planen** zuordnen | nein |
| Nicht importierbar | Die Zeile selbst ist fehlerhaft (siehe ihre Hinweise) | nein |

Entfernen Sie den Haken, um eine Zeile auszulassen; **Haken behalten** speichert sie für die Zeilen der Seite.
**Neu planen** plant die Datei erneut, mit alten Hosts, die einem Siteaccess zugeordnet sind, oder mit einer anderen
Wahl für Adressen, die schon einen Eintrag haben. **Probelauf** prüft jede Zeile erneut gegen die Website und
zählt, was der Import tun würde, ohne zu schreiben. **Plan als CSV** und **Plan als JSON** laden die Vorschau
herunter. **Verwerfen** entfernt die Datei; sonst wird sie nach einem Tag entfernt.

**Importieren** fragt erst nach Ihrer Identität („Identität bestätigen“), schreibt dann die angehakten Zeilen als
einen Import und zeigt seine Nummer. Eine Zeile, deren alte Adresse sich seit der Vorschau geändert hat, wird
übersprungen.

## 3. Importhistorie und Rückgängig

**Importhistorie** (`/content/urlimporthistory`) listet jeden Import, den neuesten zuerst: wann, von wem, aus welcher
Datei, wie viele Einträge angelegt, ersetzt, übersprungen oder fehlgeschlagen sind, und den Zustand (angewendet,
rückgängig gemacht, teilweise rückgängig gemacht). Die Seite eines Imports listet jeden Eintrag, den er geschrieben
hat.

**Import rückgängig machen** fragt erneut nach Ihrer Identität, entfernt dann jeden Eintrag, den der Import angelegt
hat, und stellt jeden ersetzten Eintrag wieder her, den letzten zuerst. Ein nach dem Import geänderter Eintrag steht
über der Schaltfläche und bleibt erhalten, außer **Auch die nach dem Import geänderten Einträge zurücknehmen** ist
angehakt (`--force` der Konsole); der Import ist dann teilweise rückgängig gemacht und kann später erneut
zurückgenommen werden.

## 4. Export

**Exportieren** (`/content/urlexport`) lädt die globalen URL-Aliase des URL-Übersetzers (auf Wunsch auch die eigenen
URL-Aliase von Inhalten, geschrieben als `node:42`), die Platzhalter und die Weiterleitungen herunter, als CSV, JSON,
`.xlsx`, `.ods`, Apache- oder nginx-Regeln. Die Filter Siteaccess und Sprache gelten für die Weiterleitungen (die
Sprache auch für die Aliase); URL-Aliase und Platzhalter gehören zu jedem Siteaccess. Wer einen Export in dieselbe
Installation importiert, findet jede Zeile unverändert.

## 5. Weiterleitungen

**Weiterleitungen** (`/content/urlredirects`) listet die Weiterleitungen aus Importen, die ein globaler URL-Alias
nicht aufnehmen kann: alte Adressen mit Abfrage (`/index.php?id=12`), vorübergehende Weiterleitungen (302, 303,
307), 308 und Weiterleitungen auf andere Websites. Sie werden an der vollständigen Adresse geprüft, vor den
URL-Aliasen und Platzhaltern. Eine Weiterleitung lässt sich hier entfernen (eine oder die angehakten); sie wirkt
dann sofort nicht mehr.

Platzhalter, die vorübergehend weiterleiten, zeigen auf der Platzhalterseite **Vorübergehende Weiterleitung (302)**.

## 6. Wer was darf

| Seite | Richtlinie |
|---|---|
| Importieren, Importhistorie, Rückgängig | `content/urltranslator_import` |
| Exportieren, Weiterleitungen | `content/urltranslator` |

Importieren und Rückgängigmachen fragen nach der Identität (`site.ini [ReauthGroup_system]`). Eine Rolle mit
`content/*` (oder `*/*` wie Administrator) umfasst beide Funktionen; geben Sie `content/urltranslator_import` einzeln
einer Rolle, die URL-Zuordnungen importieren darf, aber nur `content/urltranslator` hat.
