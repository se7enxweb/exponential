# explayouts_ui_api: der Layout-Editor

Diese Seite richtet sich an Redakteure und Site-Builder, die Seiten mit Exponential Layouts zusammenstellen, und an
Entwickler, die die API des Editors aufrufen. `explayouts_ui_api` ist der visuelle Layout-Editor. Er liefert die
Editor-Anwendung (eine Single-Page-App) und die JSON-API dahinter aus, als gewöhnliche Exponential-Modulansichten auf
Basis der Dienste von `explayouts_core`. Öffnen Sie ihn über den Admin-Reiter **Layouts** (siehe
[explayouts_ui](explayouts_ui.md)) oder direkt unter `/explayouts_ui_api/app`.

Er wurde am 30. Juli 2026 importiert (1.0.0). Mit 1.3.7 (30. September 2026) funktionierte er auf dem Smartphone,
unter einem über einen Pfad erreichten Siteaccess (`/admin`), mit deutschen Texten, mit gemeinsam genutzten Header-
und Footer-Layouts, die als gesperrt angezeigt werden, und mit einem Schreibschutz, der sicherstellt, dass ein
veröffentlichtes Layout nur über seinen Entwurf geändert wird.

**1.3.8 und 1.3.9 (30. September und 1. Oktober 2026):** Der Editor läuft auf jQuery 4 und jQuery UI 1.14 des Admins
aus `ezjscore` statt auf dem mitgelieferten jQuery 2.2.4, sodass der gesamte Admin ein einziges jQuery verwendet. Die
Markdown- und HTML-Blöcke haben wieder ihren Code-Editor, der Kursivschnitt von Roboto wird mitgeliefert, und
Freigabelinks für Layouts funktionieren unter Oracle. Einzelheiten stehen in den
[Versionshinweisen](../../../../changelogs/extensions/explayouts_ui_api.md). Die installierte Kopie in
`extension/explayouts_ui_api` meldet in `ezinfo.php` die Version 1.3.10, ein Release nach dem neuesten Tag des Klons;
prüfen Sie das mit `grep Version extension/explayouts_ui_api/ezinfo.php`.

## Voraussetzungen

- Exponential 6 (PHP 8.1 oder neuer).
- Die Schwester-Extensions `explayouts` (Wertobjekte, Tabellen, Layout- und Blocktypen in `explayouts.ini`),
  `explayouts_core` (Dienste) und `ezformtoken` (Formular-Token).
- Aktivieren Sie sie für den Admin-Siteaccess, nach ihren Abhängigkeiten, und erzeugen Sie dann die Autoloads neu und
  leeren Sie die Caches.
- Prüfen Sie das mit `/explayouts_ui_api/app/api/config`: Es liefert JSON einschließlich des aktuellen
  Formular-Tokens.

## Den Editor öffnen und verwenden

1. Melden Sie sich im Admin an und öffnen Sie **Layouts** (`/explayouts_ui/dashboard`).
2. Wählen Sie **Neues Layout** oder **Layout bearbeiten** auf einer Layout-Karte, einer Zuordnungsregel oder dem
   Dashboard. Der Editor öffnet sich unter `/explayouts_ui_api/app#layout/<id>`.
3. Ziehen Sie einen Block aus dem Menü **+** in eine Zone oder in einen Container-Block.
   Blöcke lassen sich kopieren, verschieben und löschen; ihre Reihenfolge bleibt erhalten.
4. Wählen Sie einen Block aus, um ihn in der Seitenleiste zu bearbeiten. Das Blockformular öffnet sich auf seinem
   Reiter **Design** (Ansichtstyp, Spaltenzahl und die anderen Designparameter; der Name des Blocks ist ein Feld des
   Formulars), die Seitenleiste hat für Blöcke mit einer Sammlung (manuelle Elemente oder eine dynamische Abfrage) die
   Reiter **Block** und **Sammlung** sowie einen Abschnitt **Erweiterte Optionen**. Das Abfrageformular hat einen
   eigenen, einklappbaren Bereich **Versatz und Anzahl der Elemente**.
5. **Veröffentlichen** schaltet den Entwurf live; **Verwerfen** wirft ihn weg; **Entwurf erstellen** öffnet ein
   veröffentlichtes Layout zur Änderung.

Der Editor bearbeitet immer den **Entwurf**. Ein veröffentlichtes Layout wird nie direkt geändert:
Jeder Schreibzugriff auf einen Block eines veröffentlichten Layouts wird mit HTTP 403 abgewiesen, und seine
Blöcke ändern sich erst, wenn sein Entwurf veröffentlicht wird.

### Was die Vorschau zeigt

* Blöcke sind mit dem Namen ihrer Definition beschriftet, nicht mit einem internen Bezeichner. Titel- und
  Richtext-Blöcke werden mit den Inline-Bearbeitungs-Hooks dargestellt, die der Editor erwartet; ein Template-Block
  zeigt `Template block: <name>`.
* Ein Listenblock zeigt lesbare Titel der Elemente. Eine Sammlung wird als Spaltenraster dargestellt, das ihre
  Spaltenzahl berücksichtigt; ein Komponentenblock zeigt das Element, auf das er verweist, mit Vorschaubild,
  verlinktem Namen und Werttyp.
* Manuelle Elemente einer Sammlung zeigen Knoten-ID, ID des übergeordneten Knotens und Namen. Elemente lassen sich über
  einen Inhaltsbrowser hinzufügen, einzeln oder alle entfernen und verschieben. Der Sammlungstyp lässt sich zwischen
  manuell und dynamisch umschalten.
* Der ausgewählte Block wird deutlich hervorgehoben.

### Gemeinsam genutzte Layouts und verknüpfte Zonen

Eine Zone kann ihre Blöcke von einer Zone eines gemeinsam genutzten Layouts erben (zum Beispiel Header und Footer der
Website). Der Editor stellt eine solche Zone als **geerbt und gesperrt** dar: Sie sehen die gemeinsamen Blöcke, können
sie aber nicht aus dem Layout heraus bearbeiten, das auf sie verweist. Um sie zu ändern, öffnen Sie das gemeinsam
genutzte Layout selbst und legen einen Entwurf davon an. Zonen lassen sich im Editor verknüpfen und lösen (das
Link-Symbol unten links in einer Zone), und Schreibanfragen in eine verknüpfte Zone werden abgewiesen. Das ersetzte das
Verhalten vor 1.2.1, bei dem der Editor die gemeinsamen Blöcke als eigene Blöcke des Layouts aufführte und eine
Bearbeitung direkt in die veröffentlichten Zeilen des gemeinsam genutzten Layouts schrieb, für die ganze Website.

### Auf dem Smartphone verwenden

Ab 1.2.0 hat der Editor eine mobile Darstellung (das Upstream-Projekt weigert sich, den Editor unter 800 px
darzustellen). Die Arbeitsfläche nimmt die volle Breite neben einer Leiste ein; Blöcke lassen sich mit dem Finger
ziehen; die Blockoptionen öffnen sich über einen Schalter in der Leiste unter dem Button zum Hinzufügen von Blöcken,
und die Auswahl eines Blocks verdeckt das Layout nicht mehr mit dem Eigenschaftenbereich. Der Schalter färbt sich in
der Akzentfarbe, um anzuzeigen, dass ein Block darauf wartet, betrachtet zu werden.

### Sprachen

Der Editor zeigt seine Buttons, Menüs, Dialoge, Hinweise, Leerzustände, Tooltips und die Texte der Datumsauswahl in
der Sprache der Oberfläche. Mitgeliefert werden Englisch, Deutsch (vollständig) und der unübersetzte Katalog,
123 Meldungen. Die Texte werden im Kontext `design/standard/explayouts_ui_api/spa` über das Template
`explayouts_ui_api/spa_strings.tpl` nachgeschlagen; eine Sprache fügen Sie hinzu, indem Sie eine Übersetzungsdatei für
diesen Kontext hinzufügen.

### Unter einem über einen Pfad erreichten Siteaccess

Ab 1.3.1 funktioniert der Editor, wenn der Admin unter einem Pfad wie `/admin/` liegt statt auf einem eigenen Host.
Alle URLs des Editors laufen über `eZURI::transformURI()`, der Router akzeptiert ein Siteaccess-Präfix, und „Im CMS
anzeigen“ sowie die Links, die aus dem Editor herausführen, tragen das Präfix. Das Verlassen des Editors (Verwerfen,
Abbrechen, Schließen) kehrt zurück zu: dem Parameter `return_to`, sonst der Seite, die den Editor geöffnet hat, sonst
einem gespeicherten Wert innerhalb dieses Siteaccess, sonst der Wurzel des Siteaccess.

## Fehler, Reihenfolge und Freigabe (geprüft in `classes/explayoutsuiapplicationapi.php`)

* **Fehler sind JSON.** Eine unbekannte Ressource antwortet mit `404 {"error":"Unknown resource."}`; alles, was die API nicht behandeln kann, wird abgefangen (28. August 2026), mit seinem Stacktrace in das
  `error.log` des Protokollordners geschrieben und mit `500 {"error":"Internal error","details":"<message> in <file>:<line>"}` beantwortet statt mit einer leeren Seite. Der Text in `details` nennt eine Datei und
  eine Zeile der Installation; halten Sie die API deshalb hinter dem Admin-Login (das ist sie, aber kopieren Sie solche Antworten nicht in öffentliche Tickets).
* **Die Blockreihenfolge bleibt erhalten** (7. September 2026): Erstellen, Kopieren, Verschieben und Löschen schreiben echte Werte für `position`; vorher wurde jeder Block an Position 0 gespeichert, und die
  Reihenfolge fiel auf die Einfügereihenfolge zurück, sodass ein verschobener Block zurückspringen konnte.
* **Block-Endpunkte wirken auf den Entwurf** (28. August 2026): Eine Anfrage für einen Block eines veröffentlichten Layouts wird auf den aktiven Entwurf dieses Layouts aufgelöst.
* **Freigabelinks** (`<api base>/share/<layout id>`): `POST` erzeugt ein Token (64 Hexadezimalzeichen aus der sicheren Zufallsquelle des Systems) und antwortet mit `201` sowie `layout_id`, `share_token`
  und `created`; `GET` listet die Tokens des Layouts auf, die neuesten zuerst; `DELETE share/<layout id>/<token>` entfernt eines. Seit dem 19. September 2026 ist ein Token, das nicht gespeichert werden konnte,
  ein Fehler (`500 {"error":"Share token could not be stored."}`) und kein `201` mit einem Token, das nie gültig wird; die Tabelle `explayouts_share` wird bei der ersten Verwendung angelegt, für MySQL, SQLite
  und (seit 1.3.8) Oracle.
* Die Editor-Hülle wird mit einem Titelzusatz, einer Cache-Busting-Version an ihrem Skript und den benötigten Material-Icons-Schriften ausgeliefert, und ihre Aufrufe `create_new_draft` und `publish`
  werden als `POST` gesendet (ein Wrapper um `Backbone.sync` bildet die Methode ab; vorher ging der Aufruf als `PATCH` hinaus und wurde als Laden eines Layouts behandelt, sodass kein Entwurf erschien).

## Sicherheit

Anfragen laufen in der authentifizierten Admin-Sitzung. Seit 1.3.3 muss jede API-Anfrage außer `GET`, `HEAD` und
`OPTIONS` das Formular-Token der Sitzung mitführen, als Header `X-CSRF-Token` oder als Formularfeld `ezxform_token`.
Vorher prüfte die Formular-Token-Prüfung der Extension `ezformtoken` nur `POST`, sodass `PUT`, `PATCH` und `DELETE`
(einen Entwurf verwerfen, einen Block ändern oder löschen, eine Zone verknüpfen oder lösen) nicht geprüft wurden. Ohne
Token oder mit einem falschen Token antwortet die API, bevor sie etwas liest oder ändert: HTTP 403,
`Cache-Control: no-store` und der Body

```json
{"error":{"code":403,"reason":"form_token_missing"}}
```

(`form_token_wrong` bei einem falschen Token). Der Editor fügt das Token selbst hinzu, auch bei Aufrufen von `fetch()`
und `XMLHttpRequest` auf derselben Website, die etwas ändern, sodass nichts, was er sendet, abgewiesen wird. Ist
`ezformtoken` nicht aktiv, wird nichts geprüft. Schreibzugriffe auf Blöcke werden zusätzlich abgewiesen, wenn der
Block nicht zu einem Entwurf gehört (1.3.4).

## Die API selbst aufrufen

Jeder HTTP-Client funktioniert mit einem Sitzungs-Cookie einer authentifizierten Admin-Sitzung und dem
Formular-Token. Holen Sie das Token vom Config-Endpunkt und legen Sie dann ein Layout und einen Block an:

```bash
BASE=https://admin.example.com/explayouts_ui_api/app/api
# cookie.txt holds the admin session cookie
TOKEN=$(curl -s -b cookie.txt "$BASE/config" | sed -n 's/.*"csrf_token":"\([^"]*\)".*/\1/p')
curl -s -b cookie.txt -H "X-CSRF-Token: $TOKEN" -H 'Content-Type: application/json' \
  -d '{"name":"My layout","layout_type":"2_column"}' "$BASE/layouts"
```

Die vollständige Liste der Endpunkte, die Formen von Anfragen und Antworten sowie der Fehlervertrag stehen in der
[Spezifikation von explayouts_ui_api](../../../spezifikationen/6.0/explayouts-ui-api.md).

## Verhaltensänderungen, die Sie beim Aktualisieren kennen sollten

* **1.3.4**: Schreibzugriffe auf Blöcke eines veröffentlichten Layouts werden mit 403 abgewiesen. Hat ein Skript
  veröffentlichte Blöcke direkt geändert, lassen Sie es einen Entwurf anlegen und veröffentlichen.
* **1.3.3**: Anfragen außer GET brauchen das Formular-Token (siehe oben).
* **1.2.1**: Gemeinsam genutzte und verknüpfte Blöcke sind in dem Layout gesperrt, das auf sie verweist.
* **1.2.4**: Die Indizes der Freigabetabelle heißen `idx_share_layout` und `idx_share_token`. Eine Installation, die
  schon vorher ein Layout freigegeben hat, behält die alten Indexnamen; die System-Upgrade-Seite nennt die vier
  Anweisungen, die das korrigieren, oder Sie löschen `explayouts_share` (sie enthält nur Freigabe-Tokens), und sie wird
  bei der nächsten Freigabe neu angelegt. Erfordert `explayouts` 1.3.7 oder neuer.
* **1.3.0**: Modulansichten können von einem persistenten PHP-Worker (Velocity) ausgeliefert werden; siehe
  [Velocity-Engines](../../../bc/6.0/velocity-engines.md).

## Verwandte Seiten

- [explayouts_ui](explayouts_ui.md): die Admin-Seiten von Layouts, die in den Editor verlinken
- [Spezifikation](../../../spezifikationen/6.0/explayouts-ui-api.md)
- [Exponential Layouts (BC-Hinweis)](../../../bc/6.0/layouts.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/explayouts_ui_api.md) und [Versionshinweise](../../../../changelogs/extensions/explayouts_ui_api.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/explayouts_ui_api.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-07](../../../geschichte/erweiterungen/monate/2026-07.md), [2026-08](../../../geschichte/erweiterungen/monate/2026-08.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
