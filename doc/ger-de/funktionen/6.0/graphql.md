# Die GraphQL-API: genau die Felder abfragen, die eine Seite braucht

Diese Seite ist für Entwickler, die ein Frontend, eine App, einen Generator für statische Websites oder eine
Integration auf den Inhalten einer Exponential-Installation bauen, und für Administratoren, die die API einschalten und
festlegen, was sie zeigt. Der Kernel beantwortet GraphQL-Abfragen unter `/api/graphql` in jedem Siteaccess, in dem sie
eingeschaltet ist. Das Schema wird aus den Inhaltsklassen der Installation erzeugt: Jede Klasse ist ein Typ, jedes
Attribut ein Feld. Jede Abfrage läuft als ein Benutzer der Installation, angemeldet mit einem OAuth-Token, einem
persönlichen API-Schlüssel, der Sitzung der Administration oder als anonymer Benutzer, und die Rollen und Richtlinien
dieses Benutzers entscheiden, was er lesen darf, genau wie in der Weboberfläche.

Das ist Phase 1: Inhalte lesen. Die API beantwortet Abfragen; sie ändert nichts. Was spätere Phasen hinzufügen, steht
unter [Was als Nächstes kommt](#was-als-nächstes-kommt).

| | |
|---|---|
| Endpunkt | `POST /api/graphql` (und `GET`) in jedem Siteaccess; der Sitzungs-Endpunkt `POST /graphql/query` |
| Aus, bis | `settings/graphql.ini` `[GraphQLSettings] Enabled=enabled`, wahlweise nur für die Siteaccesses aus `SiteAccessList[]` |
| Bibliothek | `webonyx/graphql-php` 15, verlangt von der `composer.json` im Wurzelverzeichnis |
| Anmeldung | ein OAuth-Token, ein persönlicher API-Schlüssel mit dem Bereich `graphql:read`, die Sitzung (Playground) oder keine (anonym) |
| Rechte | immer die Rollen und Richtlinien des Benutzers; die Schema-Einstellungen entscheiden, was überhaupt gefragt werden kann |
| Administration | Setup > GraphQL (Richtlinie `graphql/playground`): die Endpunkte, das Schema und ein Playground |
| Konsole | `exp:graphql:status`, `exp:graphql:schema`, `exp:graphql:query` |
| Einstellungen | [graphql.ini [GraphQLSettings]](../../beispiele/ini-einstellungen/graphql/GraphQLSettings.md), [[GraphQLNaming]](../../beispiele/ini-einstellungen/graphql/GraphQLNaming.md) |

## Inhalt

1. [Schnellstart in 60 Sekunden](#schnellstart-in-60-sekunden)
2. [Anfragen und Antworten](#anfragen-und-antworten)
3. [Anmelden](#anmelden)
4. [Das Schema](#das-schema)
5. [Abfragen, mit Beispielen](#abfragen-mit-beispielen)
6. [Gespeicherte Abfragen](#gespeicherte-abfragen)
7. [Sicherheit](#sicherheit)
8. [Geschwindigkeit](#geschwindigkeit)
9. [Fehler](#fehler)
10. [Der Playground und der Sitzungs-Endpunkt](#der-playground-und-der-sitzungs-endpunkt)
11. [Konsolenbefehle](#konsolenbefehle)
12. [Die Bibliothek](#die-bibliothek)
13. [Einstellungen](#einstellungen)
14. [Was als Nächstes kommt](#was-als-nächstes-kommt)

## Schnellstart in 60 Sekunden

1. **Einschalten.** Für jeden Siteaccess, in `settings/override/graphql.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?
   [GraphQLSettings]
   Enabled=enabled
   */ ?>
   ```

   Nur für einen Siteaccess legen Sie denselben Block in `settings/siteaccess/<siteaccess>/graphql.ini.append.php` an,
   oder Sie nennen die Siteaccesses, die antworten, in `SiteAccessList[]`. Leeren Sie den INI-Cache
   (`php bin/php/ezcache.php --clear-tag=ini`).

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
| jeder andere Datentyp | `String`: die Textform des Felds (`toString()`) |

Jedes Attribut steht im Schema (wenn es nicht ausgeblendet ist): Ein Datentyp ohne eigene Abbildung, aus dem Kernel
oder einer Erweiterung (`eztags`, `ezgmaplocation`, `xrowmetadata` ...), ist ein `String` mit dem Text, den der Datentyp
für den Export liefert. `exp:graphql:status` listet diese Datentypen unter `string only`, und
`exp:graphql:schema --format=summary` markiert jedes solche Feld mit `"mapped": false`. `eztags` bekommt in einer
späteren Phase einen eigenen Typ ([Was als Nächstes kommt](#was-als-nächstes-kommt)).

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
  `Max query complexity should be 1000 but got 20202.` (`COMPLEXITY_LIMIT`). `MaxListLimit` (100) begrenzt jede Seite,
  und eine Abfrage ist höchstens `MaxQueryLength` Bytes lang (100 000).
- **Nur gespeicherte Abfragen**, für anonyme Anfragen oder für alle (`PersistedQueriesOnly`,
  [oben](#gespeicherte-abfragen)): Ein Frontend in Produktion, das immer nur seine eigenen gespeicherten Abfragen
  sendet, lässt nichts anderes zum Ausprobieren übrig.
- **Nur lesend.** Phase 1 hat keine Mutationen: Nichts, was eine Abfrage tut, ändert Inhalte.
- **Kein Caching über Benutzer hinweg.** Die Antworten sind `private, no-store` und werden nie vom Anwendungs-Cache der
  REST-Schicht behalten.
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
  Adresse kosten 11 SQL-Anweisungen, einzeln gelesen waren es 249.
- **Das Schema aus einer Cache-Datei.** Die Inhaltsklassen, aus denen das Schema entsteht, werden einmal aus der
  Datenbank gelesen und in `var/<site>/cache/graphql/catalogue-*.php` gehalten, eine Datei je Siteaccess,
  Filtereinstellungen, Sprachen und Klassenänderung: Das Speichern einer Klasse (der Ablauf `class-identifier-cache`)
  lässt die nächste Anfrage eine neue Datei schreiben. OPcache hält die Datei, also liest eine Anfrage keine Klasse aus
  der Datenbank.
- **Listen mit einer Abfrage.** Eine Liste liest ihre Elemente samt Feldern mit einem Teilbaum-Abruf, und `totalCount`
  kostet nur dann eine Zählabfrage, wenn danach gefragt wird.

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
| `DISABLED` | die API ist in diesem Siteaccess ausgeschaltet (HTTP 404) |
| `INTERNAL` | ein Fehler des Codes, oder die Bibliothek fehlt (HTTP 503) |
| `PERSISTED_QUERY_NOT_FOUND` | keine gespeicherte Abfrage hat diese Id oder diesen Hash (die Meldung ist `PersistedQueryNotFound`, wie Apollo Client sie erwartet) |
| `PERSISTED_QUERY_NOT_SUPPORTED` | gespeicherte Abfragen sind abgeschaltet (`PersistedQueries=disabled`; die Meldung ist `PersistedQueryNotSupported`) |
| `PERSISTED_QUERY_REQUIRED` | eine Ad-hoc-Abfrage, wo nur gespeicherte Abfragen angenommen werden (`PersistedQueriesOnly`) |
| `FORBIDDEN`, `NOT_FOUND`, `RATE_LIMITED` | für spätere Phasen reserviert; Phase 1 sendet sie nicht (Inhalt, der sich nicht lesen lässt, ist `null`) |

Die Codes sind ein stabiler Vertrag: Ein veröffentlichter Code behält Namen und Bedeutung, neue Codes kommen nur hinzu.

## Der Playground und der Sitzungs-Endpunkt

**Setup > GraphQL** (`graphql/playground`, in der Gruppe der Datentabellen und des Geheimnisspeichers) zeigt, ob die API
im Siteaccess antwortet, die Adresse des Endpunkts, die Bibliothek und ihre Version, den Schema-Hash und die Zahl der
Klassen, und einen Playground: eine Abfrage, ihre Variablen (JSON) und **Run** (oder Strg+Enter, Cmd+Enter). Die
Abfrage läuft mit Ihren Rechten, angemeldet mit der Sitzung der Administration, und die Introspection ist erlaubt, da
die Anfrage angemeldet ist. `/api/graphql/playground` leitet dorthin weiter.

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

Keine der beiden Richtlinien hat Einschränkungen; was eine Abfrage liest, entscheiden wie immer die Richtlinien für
Inhalte. `/api/graphql` braucht keine eigene Richtlinie: Ein Token oder Schlüssel läuft mit den Rechten seines
Benutzers, und ein Schlüssel braucht den Bereich `graphql:read`.

## Konsolenbefehle

| Befehl | Tut |
|---|---|
| [`exp:graphql:status`](../../beispiele/befehle/expgraphqlstatus.md) | ein- oder ausgeschaltet, Endpunkt, Bibliothek und Version, Grenzen, Introspection, Benennung, Schema-Hash, Klassen, die Datentypen ohne Abbildung; `--format=text\|json` |
| [`exp:graphql:schema`](../../beispiele/befehle/expgraphqlschema.md) | das Schema: `--format=sdl` (die Schemasprache, für Code-Generatoren und Editoren), `json` (das Ergebnis der Introspection), `summary` (Klassen, Typen, Felder, Datentypen, abgebildet oder nicht); `--output=<datei>` |
| [`exp:graphql:query`](../../beispiele/befehle/expgraphqlquery.md) | führt eine Abfrage aus, angegeben als Argument, als `.graphql`- oder `.gql`-Datei oder `-` für die Standardeingabe, oder eine gespeicherte Abfrage mit `--id=<Name oder sha256>`; `--variables`, `--operation`, `--as=<login>`, `--format=json\|table` |
| [`exp:graphql:persist`](../../beispiele/befehle/expgraphqlpersist.md) | speichert `.graphql`-Dateien als [gespeicherte Abfragen](#gespeicherte-abfragen), nachdem sie gegen das Schema geprüft wurden (`--apply`; `--name=`), `--list` (`--format=text\|json`), `--remove=<Name oder sha256> --apply` |

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

Die maschinenlesbare Ausgabe ist versioniert: `exp-graphql-status/1` (`status --format=json`) und
`exp-graphql-schema-summary/1` (`schema --format=summary`) nennen sich in ihrem Feld `schema`.

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
`PersistedQueryDirectoryList[]`, `MaxQueryLength`, `ImageAliasList[]`, `Debug`) und [[GraphQLNaming]](../../beispiele/ini-einstellungen/graphql/GraphQLNaming.md)
(`TypeNames[]`, `FieldNames[]`). Ändern Sie nie die ausgelieferte Datei: Änderungen gehören in
`settings/override/graphql.ini.append.php` oder in die `graphql.ini.append.php` eines Siteaccess.

In `settings/rest.ini`: `[ApiProvider] ProviderClass[graphql]=expGraphQLRestProvider` (die Route `/api/graphql`; die
Adresse eines Providers funktioniert jetzt auch ohne Schrägstrich danach), `[ApiKeySettings] Scopes[graphql:read]` und
`RouteScopes[expGraphQLRestController_query]`, `[RouteSettings] OptionalAuthentication[]` und
`[expGraphQLRestController_CacheSettings]`. Das Modul `graphql` steht in `module.ini [ModuleSettings] ModuleList[]`,
seine Ansichten und Richtlinien stehen unter [Modulansichten](../../beispiele/modulansichten/graphql/README.md) und
[Richtlinien](../../beispiele/richtlinien/graphql.md), und Setup > GraphQL ist `menu.ini [Leftmenu_setup] Links[graphql]`.

## Was als Nächstes kommt

Phase 1 liest Inhalte. Spätere Phasen sollen hinzufügen, noch ohne Termine:

- **Caching und gespeicherte Abfragen**: Antworten je Benutzergruppe behalten und beim Veröffentlichen ungültig gemacht,
  und Abfragen, die auf dem Server liegen und über eine ID aufgerufen werden.
- **Mutationen**: Inhalte anlegen, bearbeiten, veröffentlichen und entfernen, mit denselben Richtlinien wie in der
  Administration.
- **Suche**: Volltext- und gefilterte Suche über die Suchmaschine der Installation.
- **Tags**: `eztags` als eigener Typ, und Listen nach Tag.
- **Layouts**: die Layouts, Zonen und Blöcke des Seitenbaukastens.
- **Webhooks**: ein Aufruf an ein anderes System, wenn sich Inhalte ändern.
- **Subscriptions**: Änderungen, die einem Client gleich beim Geschehen zugestellt werden.
- **Starter-Kits**: Frontend-Projekte, die die API von der ersten Minute an lesen.

Nichts davon ist schon Teil von 6.0.15. Die in Phase 1 veröffentlichten Namen bleiben, wie sie sind; die späteren
Phasen fügen nur hinzu.

## Verwandte Seiten

- [API-Schlüssel](../../anleitungen/api-schluessel.md): persönliche Schlüssel, ihre Bereiche und wie die REST-Schicht sie auflöst
- [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md): die REST-Schnittstelle und ihre OAuth-Token
- [Entfernte Dienste: die ganze Administration als HTTP-API (expservices)](entfernte-dienste-expservices.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
- [Das Änderungsprotokoll von 6.0.15](../../../changelogs/6.0/6.0.15.md)
