# Spezifikation: die Editor-API von explayouts_ui_api

Diese Seite ist die Referenz für die JSON-API hinter dem Layout-Editor der Extension `explayouts_ui_api`
(Releases 1.0.0 bis 1.3.7): ihre Modul-Views, der Dispatcher, die Regel zum Formular-Token, jeder Endpunkt, die
Schreibregeln, Templates und Übersetzungen. Lesen Sie sie, wenn Sie Layouts per Skript bearbeiten, den Editor debuggen
oder einen eigenen Client bauen. Die Beschreibung für Anwender steht auf der
[Funktionsseite](../../../eng-us/features/6.0/extensions/explayouts_ui_api.md).

## Kurz gesagt

- Basispfad: `/explayouts_ui_api/app/api`. Antworten sind `application/json`; Listen haben die Form `{"values": [...], "total": n}`.
- Lesezugriffe (`GET`, `HEAD`, `OPTIONS`) brauchen nur die Sitzung der Administration. Schreibzugriffe brauchen zusätzlich das Formular-Token als `X-CSRF-Token`.
- Nur ein **Entwurfs**-Layout kann geändert werden; veröffentlichte und geteilte Layouts sind über die API schreibgeschützt.

## Beispiel: die Editor-Konfiguration lesen

Melden Sie sich in einem Browser an der Administration an, kopieren Sie das Sitzungs-Cookie und führen Sie Folgendes aus
(ersetzen Sie Host und Cookie):

```bash
curl -s -H 'Cookie: YOUR_SESSION_COOKIE' https://<admin-host>/explayouts_ui_api/app/api/config
```

Erwartete Antwort (der Wert des Tokens weicht ab):

```json
{"csrf_token":"...","automatic_cache_clear":true,"edition":"Open Source"}
```

Verwenden Sie dieses `csrf_token` als Header `X-CSRF-Token` jeder schreibenden Anfrage.

## Modul

Modul `explayouts_ui_api` (`modules/explayouts_ui_api/module.php`, `variable_params`
eingeschaltet). Alle Views verwenden die Richtlinienfunktion `read` des Moduls, Navigationsteil
`ezsetupnavigationpart`.

| View | Parameter | Zweck |
|---|---|---|
| `app` | keine (den Rest des Pfades liest der Dispatcher) | Editor-Hülle unter `/explayouts_ui_api/app`, API-Einstieg `/app/api/...`, Layout-Vorschau `/app/preview/<layout_id>`, Fragmente der Formulare zum Bearbeiten von Blöcken |
| `layouts` | `LayoutID` | Schreibgeschützte JSON-Ansicht eines Layouts |
| `rules` | `RuleID` | Schreibgeschützte JSON-Ansicht einer Zuordnungsregel |
| `blocks` | `ZoneID` | Schreibgeschützte JSON-Ansicht der Blöcke einer Zone |

Mitgelieferte Einstellungen: `module.ini` (`[ModuleSettings] ExtensionRepositories[]` und
`ModuleList[]` für `explayouts_ui_api`), `design.ini` (`DesignExtensions[]`), `site.ini`
(`[RegionalSettings] TranslationExtensions[]`).

## Dispatcher

`expLayoutsUIApplicationApi::handle( $parts )` in
`classes/explayoutsuiapplicationapi.php`. Jede Antwort ist `application/json`; Listenantworten
haben die Form `{"values": [...], "total": n}`. Der Dispatcher prüft zuerst das Formular-Token
und leitet dann nach Ressource weiter. Ein nicht abgefangenes `Throwable` wird mit einem Backtrace
in `error.log` protokolliert und mit HTTP 500 `{"error":"Internal error","details":...}` beantwortet.

## Authentifizierung und das Formular-Token

Dieselbe Sitzung der Administration wie im Rest der Administration. `GET`, `HEAD` und `OPTIONS` brauchen kein Token.
Jede andere Methode braucht das Sitzungstoken (`ezxFormToken::getToken()`) als
`X-CSRF-Token` oder `ezxform_token`. Ablehnungen: HTTP 403, `Cache-Control: no-store`, eine
Warnzeile im Log, Body `{"error":{"code":403,"reason":"form_token_missing"}}` oder
`"reason":"form_token_wrong"`. Ist `ezformtoken` nicht aktiv, wird kein Token ausgegeben und
nichts geprüft. Anfrage-Bodys dürfen `application/x-www-form-urlencoded` oder
`application/json` sein (`requestData()` greift auf `php://input` zurück, wenn `$_POST` leer ist).

## Endpunkte

Alle Pfade unten sind relativ zu `/explayouts_ui_api/app/api`. `<locale>` ist ein optionales
Präfix für die Inhaltssprache.

| Methode und Pfad | Zweck |
|---|---|
| `GET /config` | `{"csrf_token", "automatic_cache_clear": true, "edition": "Open Source"}` |
| `GET /config/layout_types` | Layouttypen und ihre Zonen aus `explayouts.ini` (`LayoutType_*`) |
| `GET /config/block_types` | Blocktypen in `block_types` und `block_type_groups` (gruppiert nach `Category`) |
| `GET /layouts` | Layouts auflisten |
| `GET /layouts/shared` | Nur veröffentlichte geteilte Layouts |
| `POST /layouts` | Ein Entwurfs-Layout anlegen. Body: `layout_type` (erforderlich, sonst 422 `Layout type is required.`), `name`, `identifier` (ein Slug des Namens, wenn er fehlt). Antwortet mit 201 |
| `GET /layouts/<id>` | Ein Layout. `?published=false` lädt den Entwurf, `?published=true` die veröffentlichte Version; ohne Query wird auf `load()` zurückgegriffen |
| `POST /layouts/<id>/publish` | Den Entwurf veröffentlichen (404, wenn es keinen gibt) |
| `POST /layouts/<id>/draft` | Einen Entwurf eines veröffentlichten Layouts anlegen (201) |
| `DELETE /layouts/<id>/draft` | Den Entwurf verwerfen |
| `GET [/<locale>]/layouts/<id>/blocks` | Blöcke des Layouts |
| `GET [/<locale>]/layouts/<id>/zones/<identifier>/blocks` | Blöcke einer Zone, als **nacktes JSON-Array** (der Editor liest die IDs direkt daraus) |
| `POST /layouts/<id>/zones/<identifier>/link`, `DELETE ...` | Eine Zone mit einer Zone eines geteilten Layouts verknüpfen und die Verknüpfung lösen, auf dem Entwurf |
| `GET /<locale>/blocks/<id>`, `POST/PUT/PATCH` | Einen Block laden und aktualisieren: `name`, `view_type`, `position`, `parameters` (Objekt mit dem Parameternamen als Schlüssel) |
| `POST /<locale>/blocks` | Einen Block anlegen. `parent_position` bestimmt seine Lage; die Geschwister werden neu nummeriert |
| `DELETE /<locale>/blocks/<id>` | Löschen; die Geschwister werden zusammengeschoben |
| `POST /<locale>/blocks/<id>/copy`, `/move` | Kopieren und Verschieben, auch in Container |
| `GET\|POST\|PATCH /collections/...` | Sammlungen: `result`, `change_type`, `query`, `items` (`GET`, `POST`, `PUT`, `DELETE`, `remove_all`) |
| `GET /content_browser/...` | Daten des Inhaltsbrowsers zum Hinzufügen von Sammlungseinträgen |
| `GET /rules`, `GET /rules/<id>`, `GET /mappings` | Zuordnungsregeln mit Zielen und Bedingungen; Anzahl der Regeln je `layout_id` |
| `/transfer/...` | Import und Export über `expLayoutsImporter` und `expLayoutsExporter` |
| `/forms/...`, `/parameters/...` | Formular- und Parameter-Metadaten für die Seitenleiste zum Bearbeiten von Blöcken |
| `GET /versions/<layout_id>` | Entwurfs- und veröffentlichte Versionen |
| `GET\|POST\|DELETE /share/<layout_id>` | Freigabe-Tokens (Tabelle `explayouts_share`) |

HTML-Fragmente, relativ zu `/explayouts_ui_api/app`: `GET /<locale>/blocks/<id>/edit`
(Hülle, `form_block_edit.tpl`) und `GET /<locale>/blocks/<id>/form` (das `<form>`,
`form_block_fields.tpl`). Das Formular sendet an `POST /app/api/<locale>/blocks/<id>`.

### Schreibregeln

* Ein Block wird nur geändert, kopiert oder verschoben, wenn er zu einem **Entwurf** gehört; andernfalls
  HTTP 403. Ein Entwurfsblock kann nicht unter einem veröffentlichten übergeordneten Block angelegt, kopiert oder
  verschoben werden. Neue und verschobene Blöcke übernehmen die Zone des Entwurfs.
* Ein Block eines veröffentlichten **geteilten** Layouts und jeder Schreibzugriff in eine **verknüpfte** Zone werden
  für jede Block-Route außer `GET` abgelehnt.
* Block-Endpunkte lösen auf den aktiven Entwurf auf, wenn keine ausdrückliche Query `published`
  angegeben ist.
* Eine verknüpfte Zone meldet `block_ids: []`; ihre Blöcke kommen aus der Route für die Blöcke einer Zone.
  Die Layout-Nutzlast meldet das tatsächliche Flag `shared` und je Zone die Verknüpfung, normalisiert
  auf die ID des veröffentlichten Layouts.
* `publish()` veröffentlicht nur einen Entwurf. Es lädt den Entwurf (`loadDraft()`) und gibt daher
  false zurück, statt das veröffentlichte Layout auf sich selbst zu kopieren.

### Freigabe-Endpunkt

`explayouts_share` wird bei Bedarf von `ensureShareTable()` angelegt und muss der
Deklaration in `share/db_schema.dba` von `explayouts` entsprechen (Indizes `idx_share_layout` und
`idx_share_token`). MySQL und MongoDB verwenden die MySQL-Anweisung (der MongoDB-Treiber liest
die Schlüsselklauseln, um seine Indizes aufzubauen); SQLite hat eine eigene Schreibweise. Jedes Ergebnis wird
geprüft: Ein Fehler wird mit 500 beantwortet, `POST` antwortet nur mit einem gespeicherten Token mit 201, `GET`
gibt immer die dokumentierte Liste zurück und `DELETE` meldet, was geschehen ist.

## Templates und Assets

| Datei | Rolle |
|---|---|
| `design/standard/templates/explayouts_ui_api/app.tpl` | Startseite. Meta-Tags, die das JavaScript liest: `nglayouts-route-prefix` (`/explayouts_ui_api`), `nglayouts-base-path`, `ngcb-base-path`, `ezxform-token` |
| `.../spa_strings.tpl` | Definiert `window.nglayoutsSpaI18n` aus Aufrufen von `\|i18n( 'design/standard/explayouts_ui_api/spa' )`, mit dem englischen Text als Schlüssel |
| `.../form_block_edit.tpl`, `form_block_fields.tpl`, `form_query_edit.tpl` | Formulare zum Bearbeiten von Blöcken und Abfragen |
| `design/standard/javascript/netgen-layouts.js` | Das Editor-Bundle. Sein `l.sync` ordnet `options.method` (`POST`, `PATCH`, `PUT`, `GET`, `DELETE`) dem CRUD-Verb zu, damit `create_new_draft` ein `POST` sendet |
| `design/standard/javascript/explayouts-mobile.js`, `stylesheets/explayouts-mobile.css` | Mobiles Layout |

Überschreiben Sie Templates über die Design-Kaskade. Layouttypen und Blocktypen kommen aus
`explayouts.ini` und wirken nach dem Leeren des Caches; eine Codeänderung ist nicht nötig.

## Übersetzungen

Kontext `design/standard/explayouts_ui_api/spa` in `untranslated`, `eng-US` und `ger-DE`
(123 Meldungen, Deutsch vollständig). Sätze, die aus Teilen zusammengesetzt werden, sind einzelne Meldungen mit
Platzhaltern (`%kind`, `%language`, `%name`, `%count`) und haben Singular- und Pluralformen.

## Verwandte Seiten

- Funktionen: [explayouts_ui_api](../../../eng-us/features/6.0/extensions/explayouts_ui_api.md), [explayouts_ui](../../../eng-us/features/6.0/extensions/explayouts_ui.md), [Exponential Layouts](../../../eng-us/bc/6.0/LAYOUTS.md)
- [Verhaltensänderungen der Extensions](../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- [Chronik](../../../eng-us/history/extensions/explayouts_ui_api.md), [Release-Notes](../../../changelogs/extensions/explayouts_ui_api.md); Monate [2026-07](../../../eng-us/history/extensions/months/2026-07.md), [2026-08](../../../eng-us/history/extensions/months/2026-08.md), [2026-09](../../../eng-us/history/extensions/months/2026-09.md)
