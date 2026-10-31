# Die GraphQL-API: genau die Felder abfragen, die eine Seite braucht

Diese Seite ist für Entwickler, die ein Frontend, eine App, einen Generator für statische Websites oder eine
Integration auf den Inhalten einer Exponential-Installation bauen, und für Administratoren, die die API einschalten und
festlegen, was sie zeigt. Der Kernel beantwortet GraphQL-Abfragen unter `/api/graphql` in jedem Siteaccess, in dem sie
eingeschaltet ist. Das Schema wird aus den Inhaltsklassen der Installation erzeugt: Jede Klasse ist ein Typ, jedes
Attribut ein Feld. Jede Abfrage läuft als ein Benutzer der Installation, angemeldet mit einem OAuth-Token, einem
persönlichen API-Schlüssel, der Sitzung der Administration oder als anonymer Benutzer, und die Rollen und Richtlinien
dieses Benutzers entscheiden, was er lesen darf, genau wie in der Weboberfläche.

Die API liest Inhalte (Abfragen, gespeicherte Abfragen mit Antwort-Cache, Suche, Tags und Layouts) und ändert sie,
wo [Mutationen](#mutationen) eingeschaltet sind, mit denselben Richtlinien, Workflows und demselben Audit wie die
Administration. Was spätere Phasen hinzufügen, steht unter [Was als Nächstes kommt](#was-als-nächstes-kommt).

| | |
|---|---|
| Endpunkt | `POST /api/graphql` (und `GET`) in jedem Siteaccess; der Sitzungs-Endpunkt `POST /graphql/query` |
| Aus, bis | `settings/graphql.ini` `[GraphQLSettings] Enabled=enabled`, wahlweise nur für die Siteaccesses aus `SiteAccessList[]` |
| Bibliothek | `webonyx/graphql-php` 15, verlangt von der `composer.json` im Wurzelverzeichnis |
| Anmeldung | ein OAuth-Token, ein persönlicher API-Schlüssel mit dem Bereich `graphql:read` (und `graphql:write` für Mutationen), die Sitzung (Playground) oder keine (anonym) |
| Mutationen | zusammen mit dem Endpunkt eingeschaltet (`MutationsEnabled=enabled`, der Standard; `disabled` je Siteaccess für eine API, die nur liest); gegen Richtlinien geprüft, mit Workflows, im Audit, nur `POST`, nur angemeldet |
| Rechte | immer die Rollen und Richtlinien des Benutzers; die Schema-Einstellungen entscheiden, was überhaupt gefragt werden kann |
| Administration | Setup > GraphQL (Richtlinie `graphql/playground`): die Endpunkte, das Schema, die Siteaccesses, die Grenzen, der Antwort-Cache, die gespeicherten Abfragen, die Schlüssel und ein Playground; Schalter mit `graphql/administrate` |
| Konsole | `exp:graphql:enable`, `exp:graphql:disable`, `exp:graphql:status`, `exp:graphql:schema`, `exp:graphql:query`, `exp:graphql:persist`, `exp:graphql:keys`, `exp:graphql:starter`, `exp:graphql:bench` |
| Einstellungen | [graphql.ini [GraphQLSettings]](../../beispiele/ini-einstellungen/graphql/GraphQLSettings.md), [[GraphQLNaming]](../../beispiele/ini-einstellungen/graphql/GraphQLNaming.md) |

## Inhalt

1. [Schnellstart in 60 Sekunden](#schnellstart-in-60-sekunden)
2. [Anfragen und Antworten](#anfragen-und-antworten)
3. [Anmelden](#anmelden)
4. [Das Schema](#das-schema)
5. [Abfragen, mit Beispielen](#abfragen-mit-beispielen)
6. [Mutationen](#mutationen)
7. [Gespeicherte Abfragen](#gespeicherte-abfragen)
8. [Sicherheit](#sicherheit)
9. [Geschwindigkeit](#geschwindigkeit)
10. [Fehler](#fehler)
11. [Der Playground und der Sitzungs-Endpunkt](#der-playground-und-der-sitzungs-endpunkt)
12. [Schema-Erweiterungen](#schema-erweiterungen)
13. [Konsolenbefehle](#konsolenbefehle)
14. [Die Bibliothek](#die-bibliothek)
15. [Einstellungen](#einstellungen)
16. [Was als Nächstes kommt](#was-als-nächstes-kommt)

## Schnellstart in 60 Sekunden

1. **Einschalten**, mit einem Befehl. Ohne `--apply` zeigt er, was er ändern würde, und schreibt nichts:

   ```bash
   ./console exp:graphql:enable --siteaccess=site --origin=https://shop.example.com --key-for=frontend
   ./console exp:graphql:enable --siteaccess=site --origin=https://shop.example.com --key-for=frontend --apply
   ```

   Er prüft, dass sich das Schema bauen lässt, setzt `Enabled=enabled` und `SiteAccessList[]=site` in
   `settings/override/graphql.ini.append.php` (ohne `--siteaccess`: jeder Siteaccess), erlaubt dem Frontend unter
   `https://shop.example.com` Aufrufe aus dem Browser (`rest.ini [CorsSettings]`), erzeugt einen persönlichen
   API-Schlüssel für den Benutzer `frontend` mit dem Bereich `graphql:read` (das Token wird einmal gezeigt), leert den
   INI-Cache und gibt den Endpunkt und eine `curl`-Zeile zum Ausprobieren aus. Die [Mutationen](#mutationen) kommen
   mit der API; `--mutations` gibt dem Schlüssel auch `graphql:write` (und schaltet die Mutationen wieder ein, wo
   `exp:graphql:disable --mutations` sie ausgeschaltet hat). Setup > GraphQL hat dieselben Schalter je
   Siteaccess. Von Hand ist es ein Block in `settings/override/graphql.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?
   [GraphQLSettings]
   Enabled=enabled
   */ ?>
   ```

   mit `SiteAccessList[]` für die Siteaccesses, die antworten (settings/override geht der eigenen Datei eines
   Siteaccess vor), und geleertem INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`).

2. **Prüfen.**

   ```bash
   ./console exp:graphql:status --siteaccess=site
   ```

   ```text
   siteaccess     site
   enabled        yes
   end point      /api/graphql
   library        webonyx/graphql-php 15.39.0
   limits         depth 12, complexity 1000, list 100 (default 25)
   introspection  signed-in
   naming         identifier
   schema         50c68965a5da, 88 classes, 828 fields
   string only    cjwnewsletteredition cjwnewsletterlist cjwnewsletterlistvirtual ezgmaplocation ezmultioption ezoption ezsrrating ezsubtreesubscription eztags ngclasslist ngenhancedlink sckenhancedselection xrowmetadata
   PASS the GraphQL API can run
   ```

3. **Fragen.** Anonym, mit den Rechten des anonymen Benutzers:

   ```bash
   curl -s https://example.com/api/graphql \
        -H 'Content-Type: application/json' \
        -d '{"query": "{ node(urlAlias: \"/\") { name children(limit: 3) { totalCount nodes { name urlAlias } } } }"}'
   ```

   ```json
   {"data":{"node":{"name":"Fit & Healthy","children":{"totalCount":16,"nodes":[{"name":"Fitness","urlAlias":"fitness"},{"name":"Healthy eating","urlAlias":"healthy-eating"},{"name":"Recipes","urlAlias":"recipes"}]}}},"extensions":{"requestId":"40b27114794d909f","schemaHash":"50c68965a5da"}}
   ```

   Mit einem persönlichen API-Schlüssel (Mein Konto > API-Zugang, Bereich **Read content through GraphQL**,
   `graphql:read`) läuft die Abfrage mit den Rechten des Eigentümers des Schlüssels, und der Client darf das Schema lesen:

   ```bash
   curl -s https://example.com/api/graphql \
        -H "Authorization: Bearer $EXP_API_KEY" \
        -H 'Content-Type: application/json' \
        -d '{"query": "{ currentUser { name signedInBy } siteaccess { name languages schemaHash } }"}'
   ```

4. **Abfragen im Browser ausprobieren**: Setup > GraphQL in der Administration, mit Ihren eigenen Rechten.

## Anfragen und Antworten

Der Endpunkt folgt den üblichen Konventionen von GraphQL über HTTP:

| Anfrage | Wie die Abfrage gelesen wird |
|---|---|
| `POST` mit `Content-Type: application/json` | der Body `{ "query": ..., "variables": { ... }, "operationName": ... }` |
| `POST` mit `Content-Type: application/graphql` | der Body ist allein die Abfrage |
| `POST` eines Formulars | die Felder `query`, `variables` (ein JSON-Text) und `operationName` |
| `GET` | dieselben drei im Query-String; nehmen Sie lieber `POST`: Eine Abfrage in einer Adresse landet in Zugriffsprotokollen |

`/api/graphql` und `/api/graphql/` sind derselbe Endpunkt. Es antwortet der Siteaccess, zu dem die Anfrage passt,
nach Host, Pfad oder dem Header `X-Siteaccess`, wie bei jeder REST-Anfrage; er bestimmt die Sprachen, den Wurzelknoten,
die Adressen in der Antwort und ob die API eingeschaltet ist.

| Status | Wann | Body |
|---|---|---|
| 200 | die Abfrage lief, Fehler der Abfrage eingeschlossen | `data`, `errors` (wenn es welche gibt), `extensions` |
| 400 | die Anfrage lässt sich nicht lesen: Der Body ist kein JSON, `variables` ist kein JSON-Objekt | `errors` mit `BAD_USER_INPUT` |
| 404 | die API ist in diesem Siteaccess ausgeschaltet | `errors` mit `DISABLED` |
| 503 | die Bibliothek ist nicht installiert | `errors` mit `INTERNAL` |

Jede Antwort ist JSON in UTF-8 und hat `Cache-Control: private, no-store`: Sie hängt vom Benutzer und von der Abfrage
ab, also darf kein gemeinsamer Cache sie behalten, und der Anwendungs-Cache der REST-Schicht ist für sie ausgeschaltet
(`rest.ini [expGraphQLRestController_CacheSettings] ApplicationCache=disabled`). Eine Antwort der Abfrage hat außerdem
den Header `X-GraphQL-Schema` mit dem Schema-Hash und `extensions`:

```json
"extensions": { "requestId": "40b27114794d909f", "schemaHash": "50c68965a5da" }
```

`requestId` ist für jede Anfrage neu (16 hexadezimale Ziffern) und steht in jedem Fehler noch einmal, sodass sich die
Meldung eines Clients den Protokollen zuordnen lässt. `schemaHash` (12 hexadezimale Ziffern) ändert sich, wenn sich
eine Klasse, ein Attribut oder die Benennung ändert: Ein Client, der das Schema oder daraus erzeugten Code
zwischenspeichert, vergleicht ihn und holt das Schema neu, wenn er abweicht. Derselbe Hash steht in
`siteaccess { schemaHash }`.

Eine Abfrage ist höchstens 100 000 Bytes lang.

## Anmelden

Die REST-Schicht liest die Zugangsdaten, wie bei jeder Route unter `/api/`:

| Die Anfrage trägt | Sie läuft als | `currentUser.signedInBy` |
|---|---|---|
| nichts | der anonyme Benutzer (oder `rest.ini [Authentication] DefaultUserID`) | `anonymous` |
| `Authorization: Bearer <OAuth-Token>` | der Benutzer, für den das Token ausgestellt wurde | `token` |
| `Authorization: Bearer expk_...`, ein persönlicher API-Schlüssel mit dem Bereich `graphql:read` | der Eigentümer des Schlüssels | `apikey` |
| das Sitzungs-Cookie, am Sitzungs-Endpunkt `graphql/query` | der angemeldete Benutzer des Browsers | `session` |
| `./console exp:graphql:query --as=<login>` | dieser Benutzer (ohne `--as` anonym) | `console` |

- **Freiwillig, nicht abwesend.** Die Route steht in `rest.ini [RouteSettings] OptionalAuthentication[]`: Sie wird nur
  authentifiziert, wenn die Anfrage Zugangsdaten vorlegt (einen `Authorization`-Header oder einen Token- oder
  Schlüssel-Parameter). Eine Anfrage mit einem falschen oder abgelaufenen Token wird wie auf jeder anderen Route
  abgelehnt; sie fällt nie auf den anonymen Benutzer zurück.
- **Der Bereich `graphql:read`.** `rest.ini` hat `Scopes[graphql:read]=content/read` und
  `RouteScopes[expGraphQLRestController_query]=graphql:read`: Ein persönlicher API-Schlüssel braucht diesen Bereich,
  und sein Eigentümer muss `content/read` weiterhin haben. Ein Schlüssel nur mit dem Bereich `read` wird an
  `/api/graphql` abgelehnt. Bereichs-IDs dürfen jetzt einen Namensraum tragen (`graphql:read`); siehe
  [API-Schlüssel, Bereiche](../../anleitungen/api-schluessel.md#6-bereiche). Senden Sie den Schlüssel im Header
  `Authorization`: Ein Schlüssel in der Adresse wird abgelehnt.
- **Der Bereich `graphql:write`.** Ein Schlüssel, der [Mutationen](#mutationen) sendet, braucht zusätzlich
  `graphql:write` (`Scopes[graphql:write]=content/edit`); jede Mutation prüft weiterhin die Richtlinien des Eigentümers
  an dem Inhalt, den sie ändert.
- **OAuth-Token** kommen aus dem OAuth-Ablauf der REST-Schnittstelle: [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md).

## Das Schema

Das Schema eines Siteaccess wird aus seinen definierten Inhaltsklassen gebaut, gefiltert durch `graphql.ini`, wenn eine
Anfrage eintrifft. Geben Sie es mit `./console exp:graphql:schema` aus (die Schemasprache) oder lesen Sie es mit einem
beliebigen GraphQL-Client über Introspection.

### Abfragen der Wurzel

```graphql
type Query {
  node(id: Int, remoteId: String, path: String, urlAlias: String, language: String): Content
  content(id: Int, remoteId: String, language: String): Content
  children(parentId: Int!, classes: [String!], sortField: ContentSortField, sortOrder: SortOrder,
           depth: Int = 1, limit: Int, offset: Int, first: Int, after: String, language: String): ContentConnection!
  search(text: String, classes: [String!], subtree: [Int!], sections: [Int!], publishedAfter: String,
         publishedBefore: String, facets: [String!], limit: Int, offset: Int, first: Int, after: String,
         language: String): SearchResult!
  tag(id: Int, remoteId: String, path: String, language: String): Tag          # solange eztags aktiv ist
  tags(parentId: Int = 0, keyword: String, limit: Int, offset: Int, language: String): [Tag!]!
  layout(id: Int, identifier: String, urlAlias: String): Layout                # solange explayouts aktiv ist
  currentUser: User!
  siteaccess: SiteAccess!
  translations: [Language!]!
}
```

| Abfrage | Antwortet mit |
|---|---|
| `node` | dem Inhalt an einem Ort (Knoten), nach genau einem von: der Knoten-ID, der Remote-ID des Orts, dem Pfad (`/1/2/58/`) oder dem URL-Alias (`about/company`; `""` oder `/` ist der Wurzelknoten des Siteaccess) |
| `content` | einem Inhaltsobjekt an seinem Hauptort, nach genau einem von: seiner ID oder seiner Remote-ID |
| `children` | den Inhalten unterhalb eines Orts (siehe [Listen](#listen-limit-und-offset-oder-cursor)) |
| `search` | den Inhalten, die die Suchmaschine findet, nach Text und Filtern (siehe [Suche](#suche)) |
| `tag`, `tags` | Tags von eztags (siehe [Tags](#tags)) |
| `layout` | einem Layout des Seitenbaukastens (siehe [Layouts](#layouts)) |
| `currentUser` | `id`, `login`, `name`, `anonymous`, `signedInBy` des Benutzers, als der die Anfrage läuft |
| `siteaccess` | `name`, `languages`, `rootNodeId`, `schemaHash` |
| `translations` | den Inhaltssprachen der Installation: `code` und `name` |

### Inhalte: ein Typ je Klasse

Jede Inhaltsklasse im Schema ist ein Objekttyp, der das Interface `Content` implementiert. Das Interface hat die
Felder, die jeder Inhalt hat:

| Feld | Typ | |
|---|---|---|
| `id` | `Int!` | die ID des Inhaltsobjekts |
| `remoteId` | `String!` | die Remote-ID des Objekts |
| `name` | `String!` | der Name, in der gelesenen Übersetzung |
| `classIdentifier`, `className` | `String!` | die Klasse |
| `published`, `modified` | `String` | ISO 8601 (`2026-10-29T13:45:00+00:00`) |
| `language` | `String!` | die Übersetzung, in der die Felder gelesen werden |
| `languages` | `[String!]!` | seine Übersetzungen |
| `mainLanguage` | `String!` | die Sprache, in der es zuerst geschrieben wurde |
| `ownerId`, `ownerName`, `sectionId` | | der Eigentümer und die Sektion |
| `url` | `String` | die Adresse des Orts in diesem Siteaccess |
| `urlAlias` | `String` | der URL-Alias des Orts (`about/company`) |
| `location` | `Location` | der Ort, über den er erreicht wurde: der Knoten, den eine Abfrage oder eine Liste nannte, sonst der Hauptort |
| `mainLocation` | `Location` | der Hauptort |
| `locations` | `[Location!]!` | jeder Ort, den der Benutzer lesen darf |
| `parent` | `Content` | der Inhalt des übergeordneten Orts |
| `children` | `ContentConnection!` | die Inhalte unterhalb des Orts, mit den Argumenten der Wurzelabfrage `children` |

Der Typ einer Klasse fügt danach ein Feld je Attribut hinzu. Seine Beschreibung sind Name und Beschreibung der Klasse
und der Klassenbezeichner; die Beschreibung eines Felds sind Name und Beschreibung des Attributs und der Datentyp. Ein
`Location` hat `id`, `remoteId`, `parentId`, `depth`, `pathString`, `priority`, `hidden`, `invisible`, `urlAlias`,
`url`, `content`, `parent` und `children`.

### Benennungsstile

`[GraphQLSettings] NamingStyle` entscheidet, wie Klassen und Attribute heißen:

| Stil | Klasse `ng_article`, Attribut `short_title` |
|---|---|
| `identifier` (der Standard) | Typ `ng_article`, Feld `short_title`: die Bezeichner der Administration, wie sie sind |
| `pascal` | Typ `NgArticle`, Feld `shortTitle` |
| `map` | wie `identifier`, aber `[GraphQLNaming] TypeNames[ng_article]=Article` und `FieldNames[ng_article/short_title]=shortTitle` haben Vorrang |

Regeln, die in jedem Stil gelten, damit jede Klasse einen gültigen, eindeutigen und stabilen Namen bekommt:

- Ein Zeichen, das GraphQL in einem Namen nicht erlaubt (ein Bindestrich, ein Punkt), wird zu `_`; ein Name, der mit
  einer Ziffer beginnt, bekommt ein `_` davor; ein führendes `__` (für die Introspection reserviert) wird zu einem
  einzelnen `_`.
- **Die Endung `_field`.** Ein Attribut, das wie ein Feld des Interfaces `Content` heißt (`name`, `location`, `url`,
  `children` ...), bekommt die Endung `_field`: Das Attribut `name` einer Klasse ist das Feld `name_field`, neben
  `name`, dem Namen des Objekts.
- **Die Endung `Content`.** Ein Typname, der einer der eigenen Typen des Schemas ist (`Image`, `Location`, `User`,
  `Link` ...), bekommt die Endung `Content`: Die Klasse `image` ist im Stil `pascal` der Typ `ImageContent`.
- Zwei Klassen, deren Namen gleich ausfallen, bekommen `_2`, `_3` ... in der Reihenfolge der Klassen; ebenso zwei
  Attribute einer Klasse.

Wählen Sie den Stil, bevor Clients sich darauf verlassen: Die Abfragen eines Clients nennen die Typen und Felder.

### Datentypen

| Datentyp | GraphQL-Typ |
|---|---|
| `ezstring`, `eztext`, `ezemail`, `ezisbn`, `ezidentifier` | `String` |
| `ezinteger` | `Int` |
| `ezfloat` | `Float` |
| `ezboolean` | `Boolean` |
| `ezdate` | `String`, `2026-10-29` |
| `ezdatetime` | `String`, ISO 8601 |
| `eztime` | `String`, `13:45:00` |
| `ezxmltext` | `RichText`: `html`, `text`, `xml`, `excerpt(length: 200)` |
| `ezimage` | `Image`: `url(alias:)`, `width`, `height`, `mimeType` (jeweils mit `alias`), `alt`, `variations(aliases:)` |
| `ezbinaryfile`, `ezmedia` | `BinaryFile`: `fileName`, `mimeType`, `size`, `url` (die Download-Adresse) |
| `ezprice` | `Price`: `price`, `incVat`, `exVat`, `vatPercent` |
| `ezurl` | `Link`: `url`, `text` |
| `ezselection` | `[SelectionOption!]!`: `id`, `name` der gewählten Optionen |
| `ezcountry` | `[Country!]!`: `code`, `name` |
| `ezkeyword` | `[String!]!` |
| `ezauthor` | `[Author!]!`: `name` |
| `ezmatrix` | `Matrix`: `columns`, `rows` |
| `ezobjectrelation` | `Content` |
| `ezobjectrelationlist` | `[Content!]!` |
| `ezgmaplocation` | `GeoLocation`: `latitude`, `longitude`, `address` |
| `ezsrrating` (Sternebewertung) | `Rating`: `average` (null ohne Stimmen), `count` |
| `xrowmetadata` | `Metadata`: `title`, `description`, `keywords`, `priority`, `change`, `sitemapUse`, `canonicalUrl` |
| `ngenhancedlink` | `EnhancedLink`: `type` (`internal`, `external`), `url`, `label`, `target`, `suffix`, `rel`, `content` (der verlinkte Inhalt, wenn lesbar) |
| `sckenhancedselection` | `[String!]!`: die gewählten Bezeichner |
| `ngclasslist` | `[String!]!`: Klassenbezeichner |
| `ezoption` | `OptionList`: `name`, `options` (`id`, `value`, `additionalPrice`) |
| `ezmultioption` | `[OptionGroup!]!`: `name`, `options` |
| `ezrangeoption` | `Range`: `name`, `start`, `stop`, `step` |
| `ezmultiprice` | `[CurrencyPrice!]!`: `currency`, `value`, `type` |
| `ezenum` | `[EnumValue!]!`: `element`, `value` |
| `ezsubtreesubscription` | `Boolean` |
| `ezproductcategory` | `String`: der Name der Kategorie |
| `eztags` | `[Tag!]!`, solange eztags aktiv ist ([Tags](#tags)) |
| ein Datentyp einer [Schema-Erweiterung](#schema-erweiterungen) | der Typ, den die Erweiterung ihm gibt |
| jeder andere Datentyp | `String`: die Textform des Felds (`toString()`) |

Jedes Attribut steht im Schema (wenn es nicht ausgeblendet ist): Ein Datentyp ohne eigene Abbildung, aus dem Kernel
oder einer Schema-Erweiterung, ist ein `String` mit dem Text, den der Datentyp für den Export liefert.
`exp:graphql:status` listet diese Datentypen unter `string only`, und `exp:graphql:schema --format=summary` markiert
jedes solche Feld mit `"mapped": false`. Ein Datentyp einer Erweiterung, die im Siteaccess nicht aktiv ist, hat dort
keinen Inhalt, also ist sein Feld `null` (oder eine leere Liste).

## Abfragen, mit Beispielen

Jedes Beispiel hier lief mit `./console exp:graphql:query` auf einer Entwicklungsinstallation mit Demo-Inhalten; die
Antworten sind nur dort gekürzt, wo es dabeisteht. Adressen in einer Antwort beginnen mit Schema und Host der Anfrage
(`https://example.com/...`); auf der Konsole beginnen sie mit `/`.

### Ein Knoten nach URL-Alias, mit seinen Kindern

```graphql
{
  node(urlAlias: "/") {
    name
    classIdentifier
    children(limit: 3) {
      totalCount
      nodes { name classIdentifier urlAlias }
      pageInfo { hasNextPage endCursor }
    }
  }
}
```

```json
{
    "data": {
        "node": {
            "name": "Fit & Healthy",
            "classIdentifier": "ng_frontpage",
            "children": {
                "totalCount": 16,
                "nodes": [
                    { "name": "Fitness", "classIdentifier": "ng_landing_page", "urlAlias": "fitness" },
                    { "name": "Healthy eating", "classIdentifier": "ng_landing_page", "urlAlias": "healthy-eating" },
                    { "name": "Recipes", "classIdentifier": "ng_category", "urlAlias": "recipes" }
                ],
                "pageInfo": { "hasNextPage": true, "endCursor": "ZXhwOjI=" }
            }
        }
    }
}
```

Nach ID: `node(id: 89)`; nach der Remote-ID des Orts: `node(remoteId: "...")`; nach Pfad: `node(path: "/1/2/89/")`.
Ein Inhaltsobjekt an seinem Hauptort: `content(id: 57)` oder `content(remoteId: "...")`. Inhalt, den es nicht gibt oder
den der Benutzer nicht lesen darf, ist `null`.

### Listen: limit und offset, oder Cursor

Ein Listenfeld (`children` der Wurzel, eines Inhalts und eines Orts) nimmt:

| Argument | |
|---|---|
| `classes` | nur Inhalte dieser Klassenbezeichner (eine Klasse, die nicht im Schema steht, entfällt) |
| `sortField`, `sortOrder` | `PATH`, `PUBLISHED`, `MODIFIED`, `SECTION`, `DEPTH`, `CLASS_IDENTIFIER`, `CLASS_NAME`, `PRIORITY`, `NAME`, `NODE_ID`; `ASC` (der Standard) oder `DESC`. Standard: die Sortierung des übergeordneten Orts |
| `depth` | wie tief unterhalb des Orts: 1 (der Standard) die Kinder, 2 auch deren Kinder ... |
| `limit`, `offset` | eine Seite nach Position |
| `first`, `after` | eine Seite nach Cursor: `first` Einträge nach dem Cursor `after` |

Beide Arten der Seitenaufteilung gehen auf demselben Feld; `first` und `after` haben Vorrang vor `limit` und `offset`,
wenn sie angegeben sind. Ohne beide liefert eine Liste `DefaultListLimit` Einträge (25); mehr als `MaxListLimit` (100)
wird darauf gesenkt. `totalCount` kostet eine Zählabfrage und läuft nur, wenn danach gefragt wird.

Die Cursor-Art, wie Relay-Clients sie erwarten:

```graphql
query Articles($parent: Int!, $after: String) {
  children(parentId: $parent, classes: ["ng_article"], depth: 3,
           sortField: PUBLISHED, sortOrder: DESC, first: 2, after: $after) {
    totalCount
    edges { cursor node { name urlAlias } }
    pageInfo { hasNextPage hasPreviousPage startCursor endCursor }
  }
}
```

```bash
./console exp:graphql:query articles.graphql --variables='{"parent": 89}'
./console exp:graphql:query articles.graphql --variables='{"parent": 89, "after": "ZXhwOjE="}'
```

Der zweite Aufruf liefert die nächsten zwei, mit `"hasPreviousPage": true` und `"startCursor": "ZXhwOjI="`. Ein Cursor
ist die Position eines Eintrags in der ganzen Liste; er bleibt gültig, solange sich die Liste nicht ändert. Ein Cursor,
den diese API nicht ausgegeben hat, wird mit `BAD_USER_INPUT` abgelehnt.

### Felder einer Klasse: Inline-Fragmente

`node`, `content`, `children` und die Relationsfelder antworten mit dem Interface `Content`. Die Felder einer Klasse
lesen Sie mit einem Inline-Fragment auf ihren Typ:

```graphql
{
  children(parentId: 89, classes: ["ng_article"], depth: 3, first: 2) {
    nodes {
      name
      ... on ng_article {
        title
        teaser_intro { excerpt(length: 80) }
        related_content { name url }
      }
    }
  }
}
```

Ein Feld einer Klasse ohne das Fragment ist ein Validierungsfehler, der die Typen nennt, die es haben:
`Cannot query field "title" on type "Content". Did you mean to use an inline fragment on "article", ...?`

### Bilder

```graphql
... on ng_article {
  image {
    url(alias: "small")
    alt
    variations(aliases: ["small", "large"]) { alias url width height }
  }
}
```

```json
"image": {
    "url": "/var/site/storage/images/fit-healthy/fitness/effective-training-techniques-isolation-vs-compound/889-1-eng-US/effective-training-techniques-isolation-vs-compound_small.jpg",
    "alt": "",
    "variations": [
        { "alias": "small", "url": "/var/site/storage/images/.../effective-training-techniques-isolation-vs-compound_small.jpg", "width": 100, "height": 75 },
        { "alias": "large", "url": "/var/site/storage/images/.../effective-training-techniques-isolation-vs-compound_large.jpg", "width": 1066, "height": 800 }
    ]
}
```

(Die URLs unter `variations` sind hier gekürzt.) Jede Größe ist ein Bildalias aus `settings/image.ini`, erzeugt beim
ersten Abruf, wie in einem Template. `url`, `width`, `height` und `mimeType` nehmen `alias` (Standard `original`).
`variations` ohne `aliases` liefert die Aliase aus `[GraphQLSettings] ImageAliasList[]` (`small`, `medium`, `large`).
Ein Alias, den `image.ini` nicht kennt, ist ein Fehler mit `BAD_USER_INPUT`.

### Rich Text

```graphql
body { html }                  # gerendertes HTML, wie die Website es zeigt
body { text }                  # reiner Text, eine Zeile je Block
body { xml }                   # das gespeicherte XML
teaser_intro { excerpt(length: 80) }   # der Anfang des reinen Texts, an einem Wort gekürzt, endet mit …
```

```json
"teaser_intro": { "excerpt": "This Cookie Policy explains what this website stores in your browser, why, and…" }
```

### Suche

`search` fragt die Suchmaschine der Installation (`site.ini [SearchSettings] SearchEngine`): die des Kernels oder Solr
über ezfind. Die Suchmaschine wendet die Rechte des Benutzers an; nur Klassen des Schemas werden durchsucht und
beantwortet.

```graphql
{
  search(text: "pasta", classes: ["ng_article", "ng_recipe"], subtree: [2], publishedAfter: "2024-01-01", first: 10) {
    totalCount
    engine
    nodes { name url ... on ng_article { intro { excerpt(length: 120) } } }
    pageInfo { hasNextPage endCursor }
  }
}
```

| Argument | Filtert nach |
|---|---|
| `text` | den gesuchten Wörtern, in der Syntax der Suchmaschine |
| `classes` | Klassenbezeichnern |
| `subtree` | Knoten-IDs: nur Inhalte darunter |
| `sections` | Sektions-IDs |
| `publishedAfter`, `publishedBefore` | dem Veröffentlichungsdatum, ISO 8601 (`2026-10-30`, `2026-10-30T12:00:00+01:00`) |
| `facets` | den zu zählenden Facetten (`class`, `section`, `author`, `keywords`, `year` ...); nur Solr hat Facetten, die Suchmaschine des Kernels antwortet mit einer leeren Liste |

Eine Suche braucht einen Text oder einen Filter (sonst `BAD_USER_INPUT`); die Suchmaschine des Kernels findet nur nach
Wörtern, braucht also einen Text, Solr findet auch nach Filtern allein. `limit`/`offset` und `first`/`after` wirken wie
bei jeder Liste, `totalCount` ist, was die Suchmaschine zählt, `engine` nennt die Suchmaschine, die geantwortet hat,
und `facets` liefert jede verlangte Facette mit ihren Werten: `{ field values { value name count } }`. Die Suche läuft
einmal für alle ihre Felder, und die URL-Aliasse ihrer Seite entstehen zusammen, wie bei jeder Liste.

### Tags

Solange eztags aktiv ist, fügt seine Schema-Erweiterung (`expGraphQLTagsExtension`, standardmäßig in
`SchemaExtensionList[]`) die Abfragen `tag` und `tags` hinzu, und jedes `eztags`-Feld eines Inhalts ist eine Liste von
`Tag`:

```graphql
{
  tag(path: "Topics/Asian") {            # oder id: 42, oder remoteId: "..."
    id keyword url childrenCount
    parent { keyword }
    path { keyword }                     # von oben bis zum Elterntag
    synonyms { keyword }
    content(classes: ["ng_recipe"], limit: 10) { totalCount nodes { name url } }
  }
  tags(parentId: 0, limit: 20) { id keyword }   # die obersten Tags; keyword: "Pasta" findet nach Schlagwort
  node(urlAlias: "recipes/pad-thai") { ... on ng_recipe { tags { keyword url } } }
}
```

- `Tag`: `id`, `keyword` (in der Übersetzung von `language`, sonst der Hauptübersetzung), `remoteId`, `parentId`,
  `mainTagId`, `isSynonym`, `depth`, `pathString`, `modified`, `languages`, `url` (die Seite des Tags, `tags/view/...`),
  `parent`, `mainTag`, `path`, `children(limit, offset)`, `childrenCount`, `synonyms` und `content`: die Inhalte, die mit
  dem Tag oder einem seiner Synonyme getaggt sind, irgendwo unterhalb der Wurzel des Siteaccess, eine Liste wie jede
  Liste (`classes`, `sortField`, `limit`, `first`/`after` ...), so wie der Benutzer sie lesen darf.
- Die Elterntags, Haupttags und Pfade einer Ebene werden in einem Stapel gelesen.
- `tag` und `tags` antworten nur einem Benutzer mit der Richtlinie `tags/read` (sonst `null` und `[]`); die Tags des
  Felds eines Inhalts gehören zum Inhalt und brauchen keine eigene Richtlinie.

### Layouts

Solange explayouts aktiv ist, fügt seine Schema-Erweiterung (`expGraphQLLayoutsExtension`, standardmäßig in
`SchemaExtensionList[]`) die Abfrage `layout` und das Feld `layout` von `Location` hinzu: das Layout des
Seitenbaukastens, seine Zonen und deren Blöcke, damit ein Frontend eine Seite so aufbauen kann wie die Site.

```graphql
{
  node(urlAlias: "fit-healthy") {
    name
    location {
      layout {                           # das Layout, das die Regeln von explayouts dieser Seite geben
        identifier type
        zones {
          identifier linkedLayoutId      # eine mit einem geteilten Layout verknüpfte Zone zeigt dessen Blöcke
          blocks {
            id definition viewType itemViewType
            title: parameter(name: "title")
            parameters { name value }
            collectionType
            items { name url ... on ng_article { image { url(alias: "small") } } }
            children { definition items { name } }
          }
        }
      }
    }
  }
  home: layout(urlAlias: "") { identifier }   # oder id: 29, oder identifier: "..."
}
```

- `Layout`: `id`, `identifier`, `name`, `type` (der Layouttyp aus `explayouts.ini`), `shared`, `modified`, `zones`,
  `zone(identifier)`.
- `LayoutZone`: `identifier`, `linkedLayoutId`, `linkedZone`, `blocks`.
- `LayoutBlock`: `id`, `definition` (die Blockdefinition: `list`, `title`, `html` ...), `viewType`, `itemViewType`,
  `name`, `position`, `placeholder`, `parameters` (`name`, `value` wie gespeichert), `parameter(name)`,
  `collectionType` (`manual`, `dynamic` oder null), `items` (die von Hand in seine Sammlung gelegten Inhalte, an ihren
  Orten, so wie der Benutzer sie lesen darf), `children` (die Blöcke eines Containers).
- Nur veröffentlichte Layouts der Site werden beantwortet: nie ein Entwurf, nie ein Layout der Administration. Die
  Abfrage einer dynamischen Sammlung läuft, wenn die Seite gerendert wird; ihr Block liefert seinen `collectionType`
  und seine Parameter, aber keine Einträge.
- Die Parameter und die Sammlungen der Blöcke einer Ebene kommen in je einem Stapel, die Einträge einer Sammlung mit
  einer Abfrage.

### Sprachen

Ohne `language` werden die Felder in der Übersetzung gelesen, die der Siteaccess zeigt (die erste seiner Sprachen, die
das Objekt hat). `language` an `node`, `content` und `children` fragt nach einer anderen; hat das Objekt keine solche
Übersetzung, gilt die Wahl des Siteaccess. `language` und `languages` des Inhalts sagen, was gelesen wurde und was es
gibt:

```graphql
{
  node(urlAlias: "fitness", language: "ger-DE") { name language languages mainLanguage }
  translations { code name }
}
```

```json
{
    "data": {
        "node": { "name": "Fitness", "language": "eng-US", "languages": [ "eng-US" ], "mainLanguage": "eng-US" },
        "translations": [
            { "code": "eng-US", "name": "English (American)" },
            { "code": "ger-DE", "name": "German" },
            { "code": "qlo-ZZ", "name": "Lorem Ipsum" }
        ]
    }
}
```

Die Seite hat keine deutsche Übersetzung, also wird sie englisch gelesen, und `language` sagt es.

### Wer fragt, und wo

```graphql
{
  siteaccess { name languages rootNodeId schemaHash }
  currentUser { id anonymous signedInBy }
}
```

```json
{
    "data": {
        "siteaccess": { "name": "site", "languages": [ "eng-US" ], "rootNodeId": 89, "schemaHash": "50c68965a5da" },
        "currentUser": { "id": 10, "anonymous": true, "signedInBy": "console" }
    }
}
```

## Mutationen

Mutationen ändern Inhalte: anlegen, eine neue Version schreiben, übersetzen, einen Entwurf veröffentlichen, entfernen,
verschieben, verbergen und wieder sichtbar machen, einen weiteren Ort geben, Bilder hochladen. Sie sind **zusammen mit
dem Endpunkt eingeschaltet** (`MutationsEnabled=enabled` ist der Standard). Für eine API, die nur liest, schalten Sie
sie je Siteaccess aus, mit `./console exp:graphql:disable --siteaccess=<name> --mutations --apply`, der Schaltfläche
**Mutationen aus** in Setup > GraphQL oder von Hand:

```ini
# settings/siteaccess/<der Siteaccess, der nur liest>/graphql.ini.append.php
[GraphQLSettings]
MutationsEnabled=disabled
```

Solange sie aus sind, hat das Schema keinen Typ `Mutation`, und eine Mutation wird mit `DISABLED` abgelehnt.
Solange sie eingeschaltet sind, gelten für jede Mutation dieselben Regeln wie in der Administration:

- **Die Richtlinien des Benutzers.** Jede Mutation prüft die Rechte des Benutzers, als der sie läuft, an dem Inhalt,
  den sie ändert: `content/create` unter dem Elternknoten (für die Klasse und die Sprache), `content/edit` am Objekt (in
  der Sprache), `content/remove` an jedem Ort, den sie entfernt, `content/edit` und `content/remove` an einem Ort, den
  sie verschiebt, und `content/create` unter dem neuen Elternknoten, `content/hide`. Inhalt, den der Benutzer nicht
  lesen darf, wird mit `NOT_FOUND` beantwortet, so wie eine Abfrage ihn als nicht vorhanden beantwortet; Inhalt, den er
  lesen, aber nicht ändern darf, mit `FORBIDDEN`. Eine Klasse, die das Schema nicht zeigt (die Benutzerkonten,
  `ExcludedClassList[]`), wird nie geschrieben, ebenso wenig ein verborgenes Feld oder ein verborgener Datentyp
  (`HiddenFieldList[]`, `HiddenDatatypeList[]`: das Benutzerkonto, das Passwort). Ein Ort der obersten Ebene wird nie
  entfernt oder verschoben.
- **Die Workflows.** Eine Mutation führt die eigenen Operationen des Kernels aus, `content/publish`, `content/delete`,
  `content/move`, `content/hide` und `content/addlocation`, also laufen die Workflows, die daran hängen, wie in der
  Administration. Eine Veröffentlichung, die ein Freigabe-Workflow anhält, antwortet `PENDING`; eine, die ein Workflow
  abbricht, `CANCELLED`.
- **Wer eine senden darf.** Eine Mutation wird mit `POST` gesendet, nie mit `GET`. Eine anonyme Anfrage wird abgelehnt
  (`FORBIDDEN`), außer mit `AnonymousMutations=enabled`, und dann entscheidet die Rolle Anonymous. Ein persönlicher
  API-Schlüssel braucht neben `graphql:read` den Bereich `graphql:write` (`rest.ini Scopes[graphql:write]=content/edit`).
  Der Sitzungs-Endpunkt (der Playground) braucht das Formular-Token der Sitzung im Header `X-CSRF-Token`, wie jeder
  andere `POST` einer angemeldeten Sitzung. Ein OAuth-Token und die Konsole (`exp:graphql:query --as=<login>`) brauchen
  nichts weiter.
- **Das Audit.** Jede Mutation wird als `content.graphql.mutation` aufgezeichnet: die Mutation, wie die Anfrage
  angemeldet war, ihre Anfrage-ID, der API-Schlüssel, die Bezeichner der geschriebenen Felder (nie ihre Werte), der
  Status und die geänderten IDs. Die eigenen Ereignisse des Kernels zu der Änderung (`content.node.move`,
  `content.node.remove.trash` ...) sind ihre Kinder. Eine abgelehnte oder fehlgeschlagene ist
  `content.graphql.mutation.failed`, mit dem Grund (`anonymous`, `scope`, `form_token`, `get`, `forbidden`,
  `not_found`, `bad_user_input` ...). Beide sind standardmäßig an; siehe [das Audit](../../bc/6.0/audit.md).
- **Der Antwort-Cache.** Eine Mutation, die etwas geändert hat, lässt den [Antwort-Cache](#geschwindigkeit) verfallen,
  und die Antwort einer Mutation wird nie behalten.

### Die Mutationen

| Mutation | Tut |
|---|---|
| `createContent(parentId, classIdentifier, fields, language, remoteId, publish)` | legt Inhalt unter einem Ort an und veröffentlicht ihn (`publish: false`: lässt einen Entwurf) |
| `updateContent(id, fields, language, publish)` | schreibt die angegebenen Felder in eine neue Version (die anderen behalten ihren Wert) und veröffentlicht sie, oder lässt einen Entwurf |
| `translateContent(id, language, fields, publish)` | fügt eine Übersetzung hinzu: eine neue Version in der Sprache, kopiert aus der Hauptsprache |
| `publishDraft(id, version)` | veröffentlicht einen Entwurf (einen eigenen, oder einen, den der Benutzer lesen und bearbeiten darf) |
| `deleteContent(id \| nodeId, trash)` | entfernt einen Ort (`nodeId`) oder jeden Ort eines Objekts (`id`); in den Papierkorb, außer mit `trash: false` |
| `moveContent(nodeId, newParentId)` | verschiebt einen Ort unter einen anderen |
| `hideContent(nodeId)`, `revealContent(nodeId)` | verbirgt einen Ort samt dem Inhalt darunter oder macht ihn wieder sichtbar |
| `addLocation(id, parentId)` | gibt Inhalt einen weiteren Ort |
| `uploadImage(parentId, file, name, alt, classIdentifier, fieldIdentifier, nameIdentifier, language, publish)` | legt ein Bild aus einer mit der Anfrage gesendeten Datei an |
| `uploadFromUrl(parentId, url, ...)` | legt ein Bild aus einer `https`-Adresse auf einem Host aus `UploadFromUrlHostList[]` an (Importe; aus, solange die Liste leer ist) |
| `createPreviewToken(id, version, language, ttl)` | ein kurzlebiges Token, um eine Version (einen Entwurf) mit `preview(token:)` zu lesen |

Jede antwortet mit einem `ContentPayload`: `status` (`PUBLISHED`, `DRAFT`, `PENDING`, `CANCELLED`, `DONE`, `TRASHED`,
`DELETED`), `contentId`, `nodeId`, `version` und `content`, der Inhalt, wie der Benutzer ihn jetzt lesen darf (bei einem
Entwurf: diese Version). Eine Mutation, die fehlschlägt, antwortet `null` und einen Fehler; die Mutationen vor ihr in
derselben Anfrage behalten ihre Antworten, da die Spezifikation die Felder einer Mutation nacheinander ausführt.

### Felder: `FieldInput`

Ein Feld wird über seinen Attribut-Bezeichner geschrieben (den der Administration, welcher
[Benennungsstil](#benennungsstile) auch gilt) und seinen Wert im Textformat seines Datentyps: das Format des
CSV-Imports und von `fromString()` (eine Textzeile wie sie ist, eine Ganzzahl als Ziffern, ein Datum als Unix-Zeit,
eine Auswahl als ihre Optionsnamen, getrennt durch `|`, eine Relationsliste als Objekt-IDs, getrennt durch `-` ...).
Ein Rich-Text-Feld (`ezxmltext`) nimmt auch `format: TEXT` (reiner Text, ein Absatz je Textblock, getrennt durch eine
Leerzeile) oder `format: HTML` (einfaches HTML: `p`, `h1` bis `h6`, `strong`, `em`, `a`, Listen, Tabellen; unbekanntes
Markup fällt weg). Ein neues Objekt braucht jedes Pflichtfeld.

```graphql
mutation {
  createContent(parentId: 2, classIdentifier: "folder", fields: [
    { identifier: "name", value: "Neuigkeiten" },
    { identifier: "short_description", value: "Was passiert.\n\nUnd warum.", format: TEXT }
  ]) {
    status contentId nodeId
    content { name url }
  }
}
```

```json
{ "data": { "createContent": { "status": "PUBLISHED", "contentId": 59338, "nodeId": 57446,
                               "content": { "name": "Neuigkeiten", "url": "/neuigkeiten" } } } }
```

Erst ein Entwurf, ein Blick darauf, dann veröffentlichen:

```graphql
mutation { updateContent(id: 59338, fields: [{ identifier: "name", value: "Neuigkeiten und Geschichten" }], publish: false) { status version } }
mutation { createPreviewToken(id: 59338, version: 2, ttl: 600) { token expires } }
mutation { publishDraft(id: 59338, version: 2) { status content { name } } }
```

### Dateien: Multipart-Anfragen

Ein Feld einer Datei (`ezimage`, `ezbinaryfile`, `ezmedia` und die Datentypen aus `FileDatatypeList[]`) nimmt eine mit
der Anfrage gesendete Datei, nie einen Wert: Ein Pfad in einem Wert ließe einen Client jede Datei des Servers
importieren. Die Datei kommt nach der
[GraphQL multipart request specification](https://github.com/jaydenseric/graphql-multipart-request-spec), die Apollo
Client (`apollo-upload-client`), urql und graphql-request senden: ein `POST` als `multipart/form-data` mit dem Feld
`operations` (die JSON-Anfrage), dem Feld `map` (welche Datei in welche Variable geht) und den Dateien.

```bash
curl https://example.com/api/graphql -H 'Authorization: Bearer <token>' \
  -F operations='{"query":"mutation ($f: Upload!) { uploadImage(parentId: 52, file: $f, alt: \"Der Hafen\") { status contentId content { name } } }","variables":{"f":null}}' \
  -F map='{"0":["variables.f"]}' \
  -F 0=@hafen.jpg
```

Eine Datei in einem `FieldInput` geht genauso nach `variables.<Variable>.<n>.upload`. Der Skalar `Upload` nimmt nur
eine solche Datei; ein Text wird abgelehnt. Bevor eine Datei gespeichert wird, wird sie geprüft: `MaxUploadSize`
(standardmäßig 32 MiB; `upload_max_filesize` und `post_max_size` von PHP halten eine größere Anfrage früher auf), eine
Datei, die PHP nicht vollständig erhalten hat, und jede Prüfung aus `UploadCheckList[]`, Klassen, die
`expGraphQLUploadCheck` implementieren (ein Virenscanner, ein Filter für Dateitypen). Die erste Prüfung, die einen
Grund nennt, lehnt die Datei damit ab; eine fehlende Prüfung lehnt jede Datei ab, also lässt ein entfernter Scanner nie
ungeprüfte Dateien durch. Die Datei wird unter ihrem Namen mit nur Buchstaben, Ziffern, `-`, `_` und `.` gespeichert;
die Bild-Aliase entstehen wie bei einem Bild, das in der Administration hochgeladen wird.

`uploadFromUrl` holt eine Datei, die der Server selbst liest, für Importe: nur `https`, von einem Host aus
`UploadFromUrlHostList[]` (genau; leer: ausgeschaltet), ohne Weiterleitungen, höchstens `MaxUploadSize`.

### Entwürfe und Vorschau-Token

`content(id:, version:)` liest eine Version eines Objekts (einen Entwurf, eine archivierte Version) für einen Benutzer,
der das darf: einen eigenen Entwurf, oder mit `content/versionread`. `createPreviewToken` erzeugt ein Token für eine
Version in einer Sprache, gültig `PreviewTokenTtl` Sekunden (standardmäßig 3600, höchstens `MaxPreviewTokenTtl`);
`preview(token:)` liest diese Version damit, auch ohne Anmeldung, damit ein Frontend einem Redakteur die Seite zeigen
kann, bevor sie veröffentlicht ist. Ein Token ist mit dem geheimen Schlüssel der Installation signiert
(`EXP_SECRET_KEY` oder `var/secrets/exp_secret.key`), braucht keine Tabelle und wird abgelehnt (`FORBIDDEN`), sobald
es abgelaufen ist, verändert wurde oder der geheime Schlüssel rotiert wurde.

```graphql
{ preview(token: "exp1.eyJvIjo1OTMzOC...") { name ... on folder { short_description { html } } } }
```

## Gespeicherte Abfragen

Eine gespeicherte Abfrage (Persisted Query) liegt auf dem Server und wird mit einer Id statt mit ihrem Text
aufgerufen: Die Adresse bleibt kurz, wer sie aufruft, kann die Abfrage nicht ändern, und nur gespeicherte Abfragen
werden im [Antwort-Cache](#geschwindigkeit) gehalten. Die Id ist der SHA-256 der Abfrage (64 Hexadezimalziffern) oder
der Name, unter dem sie gespeichert wurde.

```bash
# jede .graphql-Datei eines Verzeichnisses gegen das Schema prüfen, nichts speichern
./console exp:graphql:persist frontend/queries/
# speichern: jede unter ihrem SHA-256 und ihrem Dateinamen ohne .graphql
./console exp:graphql:persist frontend/queries/ --apply
./console exp:graphql:persist menu.graphql --name=main-menu --apply
./console exp:graphql:persist --list
./console exp:graphql:persist --remove=main-menu --apply
```

Eine Abfrage, die nicht zum Schema passt (ein Syntaxfehler, ein unbekanntes Feld), wird abgelehnt, und dann wird nichts
gespeichert. Der Speicher ist `var/<site>/graphql/persisted/`: `<sha256>.graphql` und `names.json`. Eine Erweiterung
oder ein Deployment liefert Abfragen stattdessen in einem Verzeichnis von `PersistedQueryDirectoryList[]` aus: Jede
`<name>.graphql` dort ist die gespeicherte Abfrage `<name>`, nur lesbar.

Eine aufrufen:

```bash
# nach Namen, GET: variables und operationName wie bei jedem GET
curl 'https://example.com/api/graphql?id=articles50'
curl 'https://example.com/api/graphql?id=menu&variables=%7B%22depth%22%3A2%7D'
# nach Hash, in einem JSON-Body
curl -H 'Content-Type: application/json' -d '{"id":"21f7a4e0...ffad"}' https://example.com/api/graphql
# auf der Konsole
./console exp:graphql:query --id=articles50
```

**Die automatischen Persisted Queries von Apollo** funktionieren so, wie Apollo Client sie sendet: Eine Anfrage mit
`extensions={"persistedQuery":{"version":1,"sha256Hash":"<sha256>"}}` und ohne Abfrage führt die gespeicherte
Abfrage aus oder wird mit `PersistedQueryNotFound` (`PERSISTED_QUERY_NOT_FOUND`) beantwortet; der Client sendet dann
den Text mit dem Hash, und der Server speichert ihn, wenn die Anfrage Abfragen speichern darf
(`AutomaticPersistedQueries`: standardmäßig `signed-in`, höchstens `MaxPersistedQueries` Stück), und führt ihn in jedem
Fall aus. Ein Hash, der nicht der der Abfrage ist, wird abgelehnt (`BAD_USER_INPUT`).

**Nur gespeicherte Abfragen.** `PersistedQueriesOnly=anonymous` lässt anonyme Anfragen nur gespeicherte Abfragen
ausführen; `enabled` tut das für jede Anfrage an `/api/graphql`. Eine Ad-hoc-Abfrage wird dann mit
`PERSISTED_QUERY_REQUIRED` abgelehnt. Der Playground (der Sitzungs-Endpunkt) und die Konsole führen jede Abfrage aus.
`PersistedQueries=disabled` schaltet gespeicherte Abfragen ab (`PERSISTED_QUERY_NOT_SUPPORTED`).

## Sicherheit

- **Die Richtlinien gelten immer.** Jedes Inhaltsobjekt, jeder Ort und jede Relation wird über das Rechtesystem mit
  den Rechten des Benutzers gelesen, als der die Anfrage läuft: Inhalt, den der Benutzer nicht lesen darf, ist in einem
  einzelnen Feld `null` und fehlt in einer Liste und in einer Relationsliste, genau wie ein `fetch` im Template ihn
  auslässt. `totalCount` zählt, was der Benutzer lesen darf. Der anonyme Benutzer liest, was die Rolle Anonymous
  erlaubt.
- **Was das Schema zeigt.** Eine Klasse oder ein Attribut, das nicht im Schema steht, kann niemand abfragen:
  `ExcludedClassList[]` hält standardmäßig die Benutzerkonten, die Benutzergruppen und die Einstellungsklassen heraus
  (`user`, `user_group`, `common_ini_settings`, `template_look`); `ClassList[]` nennt stattdessen die einzigen gezeigten
  Klassen; `HiddenFieldList[]` blendet Attribute aus (`ng_article/internal_note`, oder `*/internal_note` in jeder
  Klasse); `HiddenDatatypeList[]` blendet Datentypen aus (standardmäßig `ezuser` und `ezpassword`: Logins,
  E-Mail-Adressen und Passwort-Hashes stehen nie im Schema). Inhalt einer Klasse, die nicht im Schema steht, ist `null`,
  auch wo der Benutzer ihn lesen darf.
- **Introspection** (das Schema lesen, wie es ein GraphQL-Client oder der Playground tut) ist standardmäßig nur für
  angemeldete Anfragen erlaubt (`Introspection=signed-in`): Anonyme Besucher können Abfragen ausführen, aber das Schema
  nicht auflisten. `enabled` erlaubt sie allen, `disabled` niemandem (die Konsolenbefehle geben es trotzdem aus).
- **Grenzen, geprüft, bevor eine Abfrage läuft.** `MaxDepth` (12): die tiefste Verschachtelung von Feldern.
  `MaxComplexity` (1000): Jedes Feld zählt 1, ein Listenfeld zählt sein `limit` oder `first` (oder `DefaultListLimit`)
  mal die Felder darunter, sodass eine Liste von 100 Einträgen mit einer Liste von 100 darunter sofort abgelehnt wird:
  `Die Abfrage hat eine Komplexität von 20202; höchstens 1000 sind erlaubt.` (`COMPLEXITY_LIMIT`). `MaxListLimit` (100) begrenzt jede Seite,
  und eine Abfrage ist höchstens `MaxQueryLength` Bytes lang (100 000).
- **Nur gespeicherte Abfragen**, für anonyme Anfragen oder für alle (`PersistedQueriesOnly`,
  [oben](#gespeicherte-abfragen)): Ein Frontend in Produktion, das immer nur seine eigenen gespeicherten Abfragen
  sendet, lässt nichts anderes zum Ausprobieren übrig.
- **Ratenbegrenzung.** Ein angemeldeter Benutzer oder ein persönlicher API-Schlüssel darf `RateLimit` Anfragen (600) in
  `RateLimitWindow` Sekunden (60) senden, eine anonyme IP-Adresse `RateLimitAnonymous` (120); `0` schaltet eine Grenze
  ab. Eine Anfrage über ihrer Grenze wird mit `429 Too Many Requests`, `RATE_LIMITED` und `Retry-After` beantwortet,
  bevor ein Schema gebaut wird oder eine Abfrage läuft; jede Antwort trägt `X-RateLimit-Limit`,
  `X-RateLimit-Remaining` und `X-RateLimit-Reset`. Die Zähler sind kleine Dateien in `var/cache/graphql/rate/` mit
  exklusiver Sperre, also zählt jeder PHP-FPM- und jeder [Velocity](velocity-webserver.md)-Worker dieselben Anfragen,
  ohne Datenbankverbindung. Der Playground zählt je Benutzer; die Konsole ist nicht begrenzt. Ein Lasttest von einer
  Adresse braucht `RateLimitAnonymous=0` (oder ein Token) im gemessenen Siteaccess.
- **Änderungen nur durch die Prüfungen.** Eine Abfrage ändert nie Inhalte. [Mutationen](#mutationen) sind zusammen
  mit dem Endpunkt eingeschaltet (`MutationsEnabled=enabled`, der Standard; `disabled` in einem Siteaccess macht seine
  API zu einer, die nur liest); sie gehen nur mit `POST`, werden für anonyme Anfragen
  (`AnonymousMutations`), für einen Schlüssel ohne `graphql:write` und für eine Sitzung ohne ihr Formular-Token
  abgelehnt, gegen die Richtlinien des Benutzers geprüft, über die Operationen des Kernels und ihre Workflows
  ausgeführt und im Audit aufgezeichnet.
- **Kein Caching über Rechte hinweg.** Eine Ad-hoc-Antwort ist `private, no-store` und wird nie behalten. Die Antwort
  einer gespeicherten Abfrage wird je Benutzerrechten gehalten (die Rollen des Benutzers und die Werte seiner
  eingeschränkten Rollenzuweisungen, so wie der View-Cache sie schlüsselt), also bekommt ein Benutzer nie eine Antwort,
  die für andere Rechte entstand; eine Antwort, die den Benutzer nennt (`currentUser`), wird für angemeldete Benutzer
  nicht behalten, und eine Antwort mit Fehlern nie. Der Anwendungs-Cache der REST-Schicht behält nie eine Antwort.
- **Fehler verraten nichts.** Ein Fehler des Codes (eine PHP-Exception) wird als `Internal server error` mit `INTERNAL`
  beantwortet; die Einzelheiten gehen ins Fehlerprotokoll. `Debug=enabled` schreibt die Meldung und den Trace in
  `extensions.debug`: nur auf einem Entwicklungs-Siteaccess.
- **Aus, bis sie eingeschaltet wird**, je Siteaccess: Ein Siteaccess ohne sie antwortet mit `404` und `DISABLED`, und
  sonst ändert sich nichts.

## Geschwindigkeit

Eine Abfrage fragt eine ganze Seite auf einmal ab, also liest die API den Inhalt einer Seite so, wie eine Seite gelesen
wird: in Gruppen, nicht ein Element nach dem anderen.

- **Gebündeltes Laden** (das DataLoader-Muster). Der Executor führt zuerst alle Felder einer Ebene der Abfrage aus und
  sammelt, was sie brauchen: Die Eigentümer von 50 Artikeln werden mit einer Abfrage gelesen, die Eltern einer Liste mit
  einer, die verknüpften Objekte aller Beziehungsfelder einer Seite mit einer (ihre Hauptorte mit den Objekten, dann
  ihre Felder) und die URL-Aliasse einer Seite mit zwei, wie lang die Seite auch ist. Ein einmal gelesener Wert wird für
  den Rest der Anfrage aus dem Speicher beantwortet. Gemessen am Demo-Inhalt: 50 Artikel mit Bild, Eigentümer und
  Adresse kosten 11 SQL-Anweisungen, einzeln gelesen waren es 249. Ein Test hält das fest
  (`expGraphQLQueryBudgetTest`: höchstens 12 Anweisungen, und eine Seite mit 50 höchstens eine mehr als eine Seite mit
  3); er läuft gegen eine Kopie einer Datenbank, die `GRAPHQL_BUDGET_DB` nennt.
- **Das Schema aus einer Cache-Datei.** Die Inhaltsklassen, aus denen das Schema entsteht, werden einmal aus der
  Datenbank gelesen und in `var/<site>/cache/graphql/catalogue-*.php` gehalten, eine Datei je Siteaccess,
  Filtereinstellungen, Sprachen und Klassenänderung: Das Speichern einer Klasse (der Ablauf `class-identifier-cache`)
  lässt die nächste Anfrage eine neue Datei schreiben. OPcache hält die Datei, also liest eine Anfrage keine Klasse aus
  der Datenbank.
- **Listen mit einer Abfrage.** Eine Liste liest ihre Elemente samt Feldern mit einem Teilbaum-Abruf, und `totalCount`
  kostet nur dann eine Zählabfrage, wenn danach gefragt wird.

**Der Antwort-Cache** (`ResponseCache=enabled`, der Standard) hält die Antworten [gespeicherter
Abfragen](#gespeicherte-abfragen) in `var/cache/graphql/responses/`:

- Ein **anonymes `GET`** einer gespeicherten Abfrage (`/api/graphql?id=articles` oder die `extensions` von Apollo) wird
  ganz am Anfang von `index_rest.php` beantwortet, vor Autoload, Einstellungen, Siteaccess, Sitzung und Datenbank: Sein
  Schlüssel entsteht aus der rohen Anfrage (Schema, Host, Adresse, Query-String, `X-Siteaccess`, `Origin`), und nur das
  entscheidet seine Antwort. Es zählt trotzdem gegen die anonyme Ratenbegrenzung, und eine CORS-Anfrage bekommt die
  `Access-Control-*`-Header, die `rest.ini [CorsSettings]` ihrem Origin gab, als die Antwort entstand.
- Ein **`POST`** einer gespeicherten Abfrage wird dort genauso beantwortet, wenn sein JSON-Body sie nennt
  (`{"id": "articles", "variables": {...}}` oder die `extensions` von Apollo): Der Schlüssel ist die rohe Anfrage mit der
  gespeicherten Abfrage, den Variablen und dem Operationsnamen des Bodys (höchstens 64 KB). Ein Formular,
  `application/graphql`, eine Ad-hoc-Abfrage oder ein Abfragetext, dessen SHA-256 nicht der genannte Hash ist, geht
  weiter an den Kernel.
- Ein **angemeldetes `GET` oder `POST`** (ein OAuth-Token oder ein persönlicher API-Schlüssel) wird dort ebenfalls beantwortet,
  sobald der Kernel seine Anmeldedaten geprüft hat: Der Kernel merkt sich die Rechte der Anmeldedaten (einen Hash des
  Tokens, nie das Token) für `CredentialCacheTtl` Sekunden (60) in `var/cache/graphql/credentials/` und vergisst alle
  auf einmal, wenn ein API-Schlüssel widerrufen oder entfernt wird, sich eine Rolle oder Richtlinie ändert oder die
  Benutzer-Caches aufgeräumt werden. Innerhalb dieser Sekunden liest ein auf anderem Weg widerrufenes Token noch die
  Antworten, die für seine Rechte schon gecacht sind; `CredentialCacheTtl=0` schickt jede angemeldete Anfrage durch den
  Kernel. Die Anfrage zählt gegen die Ratenbegrenzung des Benutzers oder Schlüssels.
- Alles andere (die erste Anfrage neuer Anmeldedaten, ein Formular-`POST`) schlägt der Kernel nach, sobald der
  Benutzer bekannt ist, unter demselben Schlüssel, also findet die frühe Antwort, was der Kernel gespeichert hat. Ein
  Token oder API-Schlüssel meldet die Anfrage nur für sich selbst an: keine Sitzung, kein Cookie und keine Zeile in
  „Ihre Geräte“ je Anfrage.
- Nur die Antwort einer **Abfrage** (query) wird behalten: Eine Mutation oder eine Subscription läuft jedes Mal, mit
  welcher Methode auch immer.
- **Abgelaufen** ist alles, sobald Inhalt veröffentlicht oder die View-Caches geleert werden (jeder Weg von
  `eZContentCacheManager` schreibt einen Zeitstempel in `var/cache/graphql/expired`; eine Antwort von davor ist
  veraltet), wenn sich eine Klasse ändert, wenn gespeicherte Abfragen gespeichert oder entfernt werden, nach
  `ResponseCacheTtl` Sekunden (86400) und mit `./console ezcache --clear-id=graphql`, das in `--clear-tag=content` und
  `--clear-tag=ini` enthalten ist: Leeren Sie nach einer Änderung an `graphql.ini` die INI-Caches, wie nach jeder
  Einstellung.
- **Header.** `X-GraphQL-Cache: HIT`, `MISS` oder `BYPASS` (eine gespeicherte Abfrage, deren Antwort nicht behalten
  wird); jede Antwort hat ein `ETag` (schwach bei Ad-hoc-Antworten, deren Anfrage-Id sich unterscheidet), und eine
  Anfrage mit passendem `If-None-Match` bekommt `304 Not Modified` ohne Body. Eine gecachte anonyme Antwort ist
  `public, max-age=0, must-revalidate` (ein Proxy darf sie halten, muss aber neu fragen), eine angemeldete
  `private, no-cache`. Die Anfrage-Id einer gecachten Antwort steht im Header `X-Request-Id`, nicht im Body, also ist
  der Body für jede Anfrage derselbe.

**Messen Sie es** mit [`exp:graphql:bench`](../../beispiele/befehle/expgraphqlbench.md), gegen jeden Endpunkt:

```bash
./console exp:graphql:bench --url=https://example.com/api/graphql --id=articles --variables='{"limit":50}' \
    --query-file=queries/articles.graphql --requests=200 --runs=3
GRAPHQL_BENCH_TOKEN=<Token oder API-Schlüssel> ./console exp:graphql:bench --url=https://example.com/api/graphql --id=articles
```

Er sendet ein zwischengespeichertes `GET`, ein `304`, ein zwischengespeichertes `POST`, eine zwischengespeicherte
Antwort, die den Kernel startet (ein Formular-`POST`, das die frühe Antwort nicht liest: was die erste Anfrage neuer
Anmeldedaten kostet), und die Abfrage als Ad-hoc-`POST` ohne Zwischenspeicher, und gibt Median, p95 und Maximum jeder
Zeile gegen Entscheidung 18 aus: jede zwischengespeicherte Antwort innerhalb von 33 ms (das Maximum), die Ziele 20 ms,
dann 13 ms, für Median und p95.

Damit gemessen auf dem Code von alpha (PHP-FPM 8.5 mit OPcache hinter nginx, 4 Worker, die Erweiterungen von alpha
aktiv, eine Kopie der Datenbank von alpha mit SQLite; eine Liste von bis zu 50 Artikeln mit Bild, Eigentümer und
Adresse; 3 Läufe zu je 200 Anfragen nacheinander, auf einem geteilten Host mit einer Last von 2,5 bis 4,5):

| Anfrage | Median | p95 | Maximum |
|---|---|---|---|
| gecacht, `GET` | 1,0 ms | 2,0 ms | 5,6 ms |
| `304 Not Modified` | 0,9 ms | 1,5 ms | 3,8 ms |
| gecacht, `POST` | 0,8 ms | 1,3 ms | 4,1 ms |
| gecacht, angemeldet mit einem API-Schlüssel (`GET`, `POST`) | 0,8 bis 1,0 ms | 1,5 bis 2,2 ms | 7,4 bis 16,5 ms |
| gecacht, durch den Kernel (anonym) | 15,0 ms | 19,1 ms | 27,0 ms |
| gecacht, durch den Kernel (ein API-Schlüssel, den der Kernel prüft) | 16,0 ms | 20,4 ms | 28,0 ms in einem ruhigen Lauf |
| nicht gecacht (eine Ad-hoc-Abfrage) | 53,8 ms | 67,2 ms | 103,7 ms |

Jede frühe Antwort liegt bei jeder Anfrage unter 33 ms und im Median und p95 unter 13 ms. Eine Antwort, die den Kernel
starten muss, kostet etwa 15 ms: Der Kernel startet den kompilierten
[Kernel-Service-Container](../../beispiele/ini-einstellungen/kernelservices/README.md) wie der Web-Kernel (ohne ihn
kosten die 90 Erweiterungen von alpha weitere 14 ms), und etwa 6 ms davon braucht SQLite, um für die Verbindung sein
Schema zu öffnen. Auf einem ausgelasteten Host kann eine einzelne Anfrage durch den Kernel noch über 33 ms kommen; die
frühe Antwort hält ein Frontend innerhalb der Grenze, für `GET` und `POST` gleichermaßen.

Nichts davon wird zwischen Anfragen behalten: Die Loader und ihr Speicher gehören zu einer Anfrage, also antwortet ein
dauerhafter Worker ([Velocity](velocity-webserver.md)) nie aus dem Speicher einer anderen Anfrage und hält keine eigene
Datenbankverbindung.

## Fehler

Ein Fehler folgt der GraphQL-Spezifikation, mit `extensions.code` und `extensions.requestId`:

```json
{
    "errors": [
        {
            "message": "node needs exactly one of id, remoteId, path and urlAlias.",
            "locations": [ { "line": 1, "column": 3 } ],
            "path": [ "node" ],
            "extensions": { "code": "BAD_USER_INPUT", "requestId": "029c3f6310f931ef" }
        }
    ],
    "data": { "node": null },
    "extensions": { "requestId": "029c3f6310f931ef", "schemaHash": "50c68965a5da" }
}
```

| `extensions.code` | Bedeutung |
|---|---|
| `PARSE` | die Abfrage ist keine gültige GraphQL-Syntax |
| `VALIDATION` | die Abfrage passt nicht zum Schema (ein unbekanntes Feld, ein fehlendes Fragment, ein falscher Argumenttyp) |
| `BAD_USER_INPUT` | ein Argument ist falsch (zwei von `id`/`urlAlias`, ein Cursor, den diese API nicht ausgab, ein unbekannter Bildalias), oder die Anfrage lässt sich nicht lesen |
| `DEPTH_LIMIT` | tiefer als `MaxDepth` |
| `COMPLEXITY_LIMIT` | komplexer als `MaxComplexity` |
| `INTROSPECTION_DISABLED` | die Anfrage darf das Schema nicht lesen (`Introspection`) |
| `DISABLED` | die API ist in diesem Siteaccess ausgeschaltet (HTTP 404), oder eine Mutation, wo Mutationen aus sind |
| `FORBIDDEN` | eine Mutation, die die Anfrage nicht senden darf (anonym, ein Schlüssel ohne `graphql:write`, eine Sitzung ohne ihr Formular-Token), eine Änderung, die die Richtlinien des Benutzers nicht erlauben, oder ein ungültiges Vorschau-Token |
| `NOT_FOUND` | eine Mutation an Inhalt, der nicht da ist oder den der Benutzer nicht lesen darf (eine Abfrage antwortet auf solchen Inhalt mit `null`) |
| `INTERNAL` | ein Fehler des Codes, oder die Bibliothek fehlt (HTTP 503) |
| `PERSISTED_QUERY_NOT_FOUND` | keine gespeicherte Abfrage hat diese Id oder diesen Hash (die Meldung ist `PersistedQueryNotFound`, wie Apollo Client sie erwartet) |
| `PERSISTED_QUERY_NOT_SUPPORTED` | gespeicherte Abfragen sind abgeschaltet (`PersistedQueries=disabled`; die Meldung ist `PersistedQueryNotSupported`) |
| `PERSISTED_QUERY_REQUIRED` | eine Ad-hoc-Abfrage, wo nur gespeicherte Abfragen angenommen werden (`PersistedQueriesOnly`) |
| `RATE_LIMITED` | über der Ratenbegrenzung (HTTP 429, mit `Retry-After`) |

Die Codes sind ein stabiler Vertrag: Ein veröffentlichter Code behält Namen und Bedeutung, neue Codes kommen nur hinzu.

**Die Sprache der Meldungen.** Eine Meldung steht in der Sprache, nach der die Anfrage mit `Accept-Language` fragt
(`de-DE,de;q=0.9`: die erste Sprache, nach ihrem `q`, für die es einen Katalog der Administration gibt, nach ihrem
vollen Code oder ihrer Sprache), sonst in der Sprache des Siteaccess (`site.ini [RegionalSettings] Locale`). Die Texte
stehen im Kontext `kernel/graphql` von `share/translations/<sprache>/translation.ts`, in jedem Katalog der
Administration. `extensions.code` ändert sich nie mit der Sprache: Entscheiden Sie nach dem Code, zeigen Sie die
Meldung. Die Meldungen der Validierung der GraphQL-Spezifikation (ein unbekanntes Feld, ein Syntaxfehler) kommen aus
der Bibliothek und bleiben Englisch, weil sie die Abfrage zitieren; `PersistedQueryNotFound` und
`PersistedQueryNotSupported` bleiben, wie sie sind, weil Apollo Client sie liest.

```bash
curl -H 'Accept-Language: de' 'https://example.com/api/graphql?query=%7Bnode%7Bid%7D%7D'
# {"errors":[{"message":"node braucht genau eines von id, remoteId, path und urlAlias.", ... "code":"BAD_USER_INPUT" ...
```

## Der Playground und der Sitzungs-Endpunkt

**Setup > GraphQL** (`graphql/playground`, in der Gruppe der Datentabellen und des Geheimnisspeichers) zeigt, ob die API
im Siteaccess antwortet, die Adresse des Endpunkts, die Bibliothek und ihre Version, den Schema-Hash und die Zahl der
Klassen, und ob Mutationen an sind; dann:

- **Siteaccesses**: ob die API und ihre Mutationen in jedem Siteaccess antworten, mit **Einschalten**, **Ausschalten**,
  **Mutationen an** und **Mutationen aus** (die Änderungen von `exp:graphql:enable` und `exp:graphql:disable`:
  geschrieben in `settings/override` und die `graphql.ini.append.php` des Siteaccess, vorher gesichert, als
  `system.setting.write` aufgezeichnet, der INI-Cache geleert);
- **Grenzen**: Tiefe, Komplexität, Einträge je Liste, die Ratenbegrenzungen, Introspection, nur gespeicherte Abfragen,
  die größte Abfrage und der größte Upload des Siteaccess;
- **Antwort-Cache**: an oder aus, die gehaltenen Antworten und ihre Bytes, wie lange, wann er zuletzt verfiel, und
  **Antwort-Cache leeren**;
- **Gespeicherte Abfragen**: ihre Namen, ihr SHA-256, ihre Größe und ob sie gespeichert oder mitgeliefert sind, mit
  **Entfernen** für eine gespeicherte;
- **API-Schlüssel**: die persönlichen API-Schlüssel mit `graphql:read` oder `graphql:write`, ihr Eigentümer, Zustand und
  letzte Nutzung (Ihre eigenen, oder die aller Benutzer mit `graphql/administrate`), mit Links zur Seite API-Zugang und
  zu allen Schlüsseln;
- einen Playground: eine Abfrage, ihre Variablen (JSON) und **Run** (oder Strg+Enter, Cmd+Enter). Die Abfrage läuft mit
  Ihren Rechten, angemeldet mit der Sitzung der Administration, und die Introspection ist erlaubt, da die Anfrage
  angemeldet ist. `/api/graphql/playground` leitet dorthin weiter.

Die Schaltflächen brauchen die Richtlinie `graphql/administrate`; ohne sie zeigt die Seite dieselben Angaben und ändert
nichts. Sie folgt dem admin4-Design im hellen und dunklen Modus, nur mit den gemeinsamen Komponenten und Tokens.

Der Playground sendet an den **Sitzungs-Endpunkt** `graphql/query`: `POST /graphql/query` (in der Adresse des
Siteaccess, zum Beispiel `/admin/graphql/query`), mit demselben JSON-Body wie `/api/graphql`, dem Sitzungs-Cookie und
dem Formular-Token der Seite im Header `X-CSRF-Token`, wie jeder `POST` einer angemeldeten Sitzung es braucht. Er
antwortet wie `/api/graphql` (`200`, `400`, `404`, `503`); ein `GET` wird mit `400` abgelehnt, sodass eine Abfrage
einer Sitzung nie in einer Adresse landet. Auch eine Seite der Website kann ihn nutzen, für den angemeldeten Benutzer
des Browsers.

| Richtlinie | Erlaubt |
|---|---|
| `graphql/playground` | Setup > GraphQL öffnen |
| `graphql/query` | Abfragen über den Sitzungs-Endpunkt ausführen (der Playground braucht sie ebenfalls) |
| `graphql/administrate` | die Schaltflächen von Setup > GraphQL: die API und ihre Mutationen ein- und ausschalten, den Antwort-Cache leeren, gespeicherte Abfragen entfernen; die GraphQL-Schlüssel aller Benutzer sehen |

Keine der beiden Richtlinien hat Einschränkungen; was eine Abfrage liest, entscheiden wie immer die Richtlinien für
Inhalte. `/api/graphql` braucht keine eigene Richtlinie: Ein Token oder Schlüssel läuft mit den Rechten seines
Benutzers, und ein Schlüssel braucht den Bereich `graphql:read`.

## Schema-Erweiterungen

Eine Erweiterung ergänzt das Schema mit einer Klasse, die `expGraphQLSchemaExtension` implementiert und in
`[GraphQLSettings] SchemaExtensionList[]` ihrer `settings/graphql.ini.append.php` steht. Sie wird nur dort gefragt, wo
ihr `isAvailable()` sagt, dass sie antworten kann (ihre Erweiterung aktiv, ihre Klassen vorhanden), und sie kann
hinzufügen:

- **Abfragen der Wurzel** (`queryFields()`), neben `node`, `content` und `children` (ein Name, den der Kernel nutzt,
  bleibt);
- **Felder von `Location`** (`locationFields()`; der Wert ist ein `expGraphQLLocation`);
- **den Typ ihrer Datentypen** (`datatypes()` und `fieldMapping()`): Ein Feld eines solchen Attributs bekommt den Typ
  der Erweiterung statt eines `String`.

```php
class myColorGraphQLExtension implements expGraphQLSchemaExtension
{
    public function isAvailable() { return class_exists( 'myColorType' ); }
    public function name() { return 'mycolor/1'; }
    public function datatypes() { return array( 'mycolor' ); }
    public function queryFields( expGraphQLTypes $types ) { return array(); }
    public function locationFields( expGraphQLTypes $types ) { return array(); }
    public function fieldMapping( array $attribute, expGraphQLTypes $types )
    {
        $type = $types->register( 'MyColor', function () {
            return new GraphQL\Type\Definition\ObjectType( array( 'name' => 'MyColor', 'fields' => array(
                'hex' => array( 'type' => GraphQL\Type\Definition\Type::string(), 'resolve' => function ( $c ) { return $c['hex']; } ) ) ) );
        } );
        return array( $type, function ( eZContentObjectAttribute $field, expGraphQLContext $ctx ) {
            return $field->hasContent() ? array( 'hex' => (string)$field->attribute( 'data_text' ) ) : null;
        } );
    }
}
```

Ein Typ entsteht einmal je Schema mit `$types->register( name, factory )`. Ein Resolver liest mit den Rechten des
Benutzers, wie die des Kernels, und fragt einen Batch-Loader, wenn eine Ebene einer Liste viele Werte braucht:
`$ctx->loader( 'mycolor', false, function ( array $keys ) { ... } )->load( $key )`. Der `name()` der Erweiterung gehört
zum Schema-Hash, also sehen Clients, dass sich das Schema ändert. `exp:graphql:status` und
`exp:graphql:schema --format=summary` listen die Erweiterungen, die antworten.

## Konsolenbefehle

| Befehl | Tut |
|---|---|
| [`exp:graphql:status`](../../beispiele/befehle/expgraphqlstatus.md) | ein- oder ausgeschaltet, Endpunkt, Bibliothek und Version, Grenzen, Introspection, Benennung, Schema-Hash, Klassen, die Datentypen ohne Abbildung; `--format=text\|json` |
| [`exp:graphql:schema`](../../beispiele/befehle/expgraphqlschema.md) | das Schema: `--format=sdl` (die Schemasprache, für Code-Generatoren und Editoren), `json` (das Ergebnis der Introspection), `summary` (Klassen, Typen, Felder, Datentypen, abgebildet oder nicht); `--output=<datei>` |
| [`exp:graphql:query`](../../beispiele/befehle/expgraphqlquery.md) | führt eine Abfrage aus, angegeben als Argument, als `.graphql`- oder `.gql`-Datei oder `-` für die Standardeingabe, oder eine gespeicherte Abfrage mit `--id=<Name oder sha256>`; `--variables`, `--operation`, `--as=<login>`, `--format=json\|table` |
| [`exp:graphql:persist`](../../beispiele/befehle/expgraphqlpersist.md) | speichert `.graphql`-Dateien als [gespeicherte Abfragen](#gespeicherte-abfragen), nachdem sie gegen das Schema geprüft wurden (`--apply`; `--name=`), `--list` (`--format=text\|json`), `--remove=<Name oder sha256> --apply` |
| [`exp:graphql:enable`](../../beispiele/befehle/expgraphqlenable.md) | schaltet die API mit einem Befehl ein, ihre Mutationen mit ihr: `--siteaccess=`, `--mutations` (wieder ein nach `disable --mutations`; ein Schlüssel erhält `graphql:write`), `--origin=` (CORS), `--key-for=<login>` mit `--key-name=`, `--scope=`, `--days=`; prüft das Schema, gibt den Endpunkt und eine `curl`-Zeile aus; ein Probelauf (ein Diff) ohne `--apply` |
| [`exp:graphql:disable`](../../beispiele/befehle/expgraphqldisable.md) | schaltet sie aus, überall oder für `--siteaccess=`; `--mutations` schaltet nur die Mutationen aus (eine API, die nur liest); ein Probelauf ohne `--apply` |
| [`exp:graphql:starter`](../../beispiele/befehle/expgraphqlstarter.md) | erzeugt ein Starter-Frontend in ein neues Verzeichnis (`--kit=html\|next\|nuxt\|astro\|expo`, `--to=`, `--endpoint=`, `--list`): die Dateien des Kits und aus dem Schema erzeugte Abfragen (das Menü, eine Seite nach URL-Alias, jedes Feld jeder Klasse), jede vorher gegen das Schema geprüft; siehe [Schritt für Schritt](../../anleitungen/graphql-schritt-fuer-schritt.md); ein Probelauf ohne `--apply` |
| [`exp:graphql:keys`](../../beispiele/befehle/expgraphqlkeys.md) | die persönlichen API-Schlüssel mit einem GraphQL-Bereich: die Liste (`--format=text\|json`), `--create --for=<login>` (`--name`, `--scope`, `--days`; das Token wird einmal gezeigt), `--revoke=<Präfix oder ID>`; ein Probelauf ohne `--apply` |
| [`exp:graphql:bench`](../../beispiele/befehle/expgraphqlbench.md) | misst die Geschwindigkeit eines Endpunkts an Entscheidung 18 ([Geschwindigkeit](#geschwindigkeit)): zwischengespeichertes `GET`, `304`, zwischengespeichertes `POST`, eine zwischengespeicherte Antwort über den Kernel, ohne Zwischenspeicher (`--query-file`); Median, p95 und Maximum je Zeile, PASS oder FAIL gegen die harte Grenze von 33 ms; `--format=text\|json` |

```bash
./console exp:graphql:status --format=json
./console exp:graphql:schema --siteaccess=site --output=schema.graphql
./console exp:graphql:schema --format=summary
./console exp:graphql:query '{ node(id: 2) { name children { totalCount } } }'
./console exp:graphql:query queries/menu.graphql --variables='{"depth": 2}' --as=admin
./console exp:graphql:query '{ siteaccess { name schemaHash } translations { code name } }' --format=table
```

`--format=table` gibt eine Zeile je Wert aus, seinen Pfad und den Wert, getrennt durch einen Tabulator:

```text
data.siteaccess.name	site
data.siteaccess.schemaHash	50c68965a5da
data.translations.0.code	eng-US
data.translations.0.name	English (American)
data.translations.1.code	ger-DE
data.translations.1.name	German
```

`exp:graphql:schema` und `exp:graphql:query` arbeiten, ob die API eingeschaltet ist oder nicht (ein Schema für die
Entwicklung, eine Abfrage in einer Prüfung beim Deploy), und die Konsole darf das Schema immer lesen. Eine Abfrage
läuft als anonymer Benutzer, außer `--as` nennt ein Login.

| Exit-Code | `status` | `schema` | `query` |
|---|---|---|---|
| 0 | die Bibliothek ist verfügbar | ausgegeben oder geschrieben | eine Antwort ohne Fehler |
| 1 | | die Datei ließ sich nicht schreiben | eine Antwort mit Fehlern |
| 2 | falsche Optionen | falsche Optionen | falsche Optionen: keine Abfrage, Variablen kein JSON, ein unbekannter Benutzer |
| 3 | die Bibliothek fehlt | die Bibliothek fehlt | die Bibliothek fehlt |

`exp:graphql:enable` antwortet 0, wenn erledigt oder gezeigt, 1, wenn eine Datei nicht geschrieben oder der Schlüssel
nicht erzeugt werden konnte, 2 bei falschen Optionen (ein unbekannter Siteaccess oder Benutzer, ein Origin, der keiner
ist, ein Bereich, den der Benutzer nicht haben darf), 3, wenn die Bibliothek fehlt; `exp:graphql:disable` und
`exp:graphql:keys` antworten 0, 1 und 2 ebenso. Die Einstellungsbefehle schreiben über den INI-Editor des Kernels, wie
`exp:ini`: Zeile für Zeile, vorher eine Sicherung in `var/backup/ini/`, im Audit aufgezeichnet, abgelehnt, wo die
Konfiguration verwaltet wird.

```bash
./console exp:graphql:enable --siteaccess=site --mutations --key-for=editor --apply
./console exp:graphql:disable --siteaccess=site --mutations --apply
./console exp:graphql:keys --create --for=frontend --name='Shop-Frontend' --days=90 --apply
./console exp:graphql:keys --revoke=expk_qkjdsz3uznp8 --apply
```

Die maschinenlesbare Ausgabe ist versioniert: `exp-graphql-status/1` (`status --format=json`),
`exp-graphql-schema-summary/1` (`schema --format=summary`) und `exp-graphql-keys/1` (`keys --format=json`) nennen sich
in ihrem Feld `schema`. `exp:graphql:status` sagt auch, ob die Mutationen an sind (`mutations`).

**Ein stabiler Vertrag.** Wie die [Befehle `exp:template:*`](../../anleitungen/template-syntaxpruefung.md#kompatibilitätsversprechen)
sind die Befehle `exp:graphql:*` eine öffentliche Schnittstelle, die Jahrzehnte halten soll: Befehlsnamen und Aliase,
Optionsnamen, Exit-Codes, Formate und die Felder der maschinenlesbaren Ausgabe werden nie entfernt, umbenannt oder in
ihrer Bedeutung geändert; neue kommen nur hinzu. Dasselbe gilt für die Fehlercodes der API, die Namen der eigenen Typen
des Schemas (`Content`, `Location`, `ContentConnection`, `Image` ...) und die Felder des Interfaces `Content`. Ein Test
im Kernel (`expGraphQLCommandContractTest`) hält das fest.

## Die Bibliothek

Die API läuft auf [webonyx/graphql-php](https://github.com/webonyx/graphql-php) 15. Die `composer.json` im
Wurzelverzeichnis verlangt `webonyx/graphql-php ^15`, das sich auf jeder PHP-Linie installieren lässt, die Exponential
6.0.15 unterstützt, PHP 7.4 bis 8.5, mit eingeschalteter Sperre bei Sicherheitshinweisen in Composer; das wurde mit
Composer auf jeder PHP-Linie geprüft. Eine Composer-Installation hat sie also.

Eine Installation, die die Bibliotheken des Kernels nicht mit Composer installiert, gibt der Bibliothek ein eigenes
Verzeichnis:

```bash
mkdir -p var/lib/graphql-vendor
cd var/lib/graphql-vendor && composer require webonyx/graphql-php:^15
```

```ini
[GraphQLSettings]
LibraryPath=var/lib/graphql-vendor
```

`LibraryPath` ist ein Verzeichnis mit `vendor/autoload.php` (ein Composer-Verzeichnis, das die Bibliothek verlangt) oder
das Paket selbst (das Verzeichnis mit `src/GraphQL.php`), relativ zum Wurzelverzeichnis der Installation oder absolut.
Die Umgebungsvariable `GRAPHQL_LIBRARY_PATH` wird genauso gelesen, wenn `LibraryPath` leer ist. Ohne die Bibliothek
antworten die Endpunkte mit `503`, Setup > GraphQL sagt es, und `exp:graphql:status` endet mit 3; sonst hängt nichts in
der Installation von ihr ab.

Die API behält zwischen Anfragen keinen Zustand (nur die Tatsache, dass die Bibliothek geladen ist), daher läuft sie
unverändert in den dauerhaften Workern von [Velocity](velocity-webserver.md), die die Einstellungen jeder Anfrage neu
lesen.

## Einstellungen

`settings/graphql.ini`, jede Einstellung mit ihrem Standard: [[GraphQLSettings]](../../beispiele/ini-einstellungen/graphql/GraphQLSettings.md)
(`Enabled`, `SiteAccessList[]`, `LibraryPath`, `NamingStyle`, `ClassList[]`, `ExcludedClassList[]`,
`HiddenFieldList[]`, `HiddenDatatypeList[]`, `Introspection`, `MaxDepth`, `MaxComplexity`, `MaxListLimit`,
`DefaultListLimit`, `PersistedQueries`, `AutomaticPersistedQueries`, `MaxPersistedQueries`, `PersistedQueriesOnly`,
`PersistedQueryDirectoryList[]`, `MaxQueryLength`, `ResponseCache`, `ResponseCacheTtl`, `CredentialCacheTtl`, `RateLimit`,
`RateLimitAnonymous`, `RateLimitWindow`, `SchemaExtensionList[]`, `ImageAliasList[]`, `MutationsEnabled`,
`AnonymousMutations`, `MaxUploadSize`, `UploadCheckList[]`, `FileDatatypeList[]`, `UploadFromUrlHostList[]`,
`PreviewTokenTtl`, `MaxPreviewTokenTtl`, `Debug`) und [[GraphQLNaming]](../../beispiele/ini-einstellungen/graphql/GraphQLNaming.md)
(`TypeNames[]`, `FieldNames[]`). Ändern Sie nie die ausgelieferte Datei: Änderungen gehören in
`settings/override/graphql.ini.append.php` oder in die `graphql.ini.append.php` eines Siteaccess.

In `settings/rest.ini`: `[ApiProvider] ProviderClass[graphql]=expGraphQLRestProvider` (die Route `/api/graphql`; die
Adresse eines Providers funktioniert jetzt auch ohne Schrägstrich danach), `[ApiKeySettings] Scopes[graphql:read]`, `Scopes[graphql:write]` und
`RouteScopes[expGraphQLRestController_query]`, `[RouteSettings] OptionalAuthentication[]` und
`[expGraphQLRestController_CacheSettings]`. Das Modul `graphql` steht in `module.ini [ModuleSettings] ModuleList[]`,
seine Ansichten und Richtlinien stehen unter [Modulansichten](../../beispiele/modulansichten/graphql/README.md) und
[Richtlinien](../../beispiele/richtlinien/graphql.md), und Setup > GraphQL ist `menu.ini [Leftmenu_setup] Links[graphql]`.

## Was als Nächstes kommt

Die API liest Inhalte (Abfragen, gespeicherte Abfragen mit ihrem Antwort-Cache, Suche, Tags, Layouts und die
Datentypen des Kernels und seiner Erweiterungen) und ändert sie mit Mutationen. Spätere Phasen sollen hinzufügen, noch
ohne Termine:

- **Dynamische Sammlungen**: die Einträge der Abfrage eines Blocks, so wie die Seite sie rendert.
- **Webhooks**: ein Aufruf an ein anderes System, wenn sich Inhalte ändern.
- **Subscriptions**: Änderungen, die einem Client gleich beim Geschehen zugestellt werden.

Nichts davon ist schon Teil von 6.0.15. Die veröffentlichten Namen bleiben, wie sie sind; die späteren Phasen fügen nur
hinzu.

## Verwandte Seiten

- [Ein Frontend auf der GraphQL-API, Schritt für Schritt](../../anleitungen/graphql-schritt-fuer-schritt.md): von
  ausgeschaltet bis zum laufenden Starter-Projekt
- [GraphQL-Rezepte](../../anleitungen/graphql-rezepte.md): ein Blog, eine mobile App, ein statischer Build, ein KI-Agent
- [API-Schlüssel](../../anleitungen/api-schluessel.md): persönliche Schlüssel, ihre Bereiche und wie die REST-Schicht sie auflöst
- [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md): die REST-Schnittstelle und ihre OAuth-Token
- [Entfernte Dienste: die ganze Administration als HTTP-API (expservices)](entfernte-dienste-expservices.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Das Änderungsprotokoll von 6.0.15](../../../changelogs/6.0/6.0.15.md)
