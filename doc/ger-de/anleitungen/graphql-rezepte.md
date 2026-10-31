# GraphQL-Rezepte: ein Blog, eine mobile App, ein statischer Build, ein KI-Agent

Vier übliche Frontends auf der [GraphQL-API](../funktionen/6.0/graphql.md), jedes mit den Abfragen, die die Arbeit
tun, und den Einstellungen, die dazugehören. Die Abfragen liefen gegen die Demo-Inhalte der Media-Site (Fit & Healthy)
von 6.0.15; die Antworten sind mit `...` gekürzt. Schalten Sie die API zuerst ein:
[Schritt für Schritt](graphql-schritt-fuer-schritt.md) oder `./console exp:graphql:enable --apply`.

Die Klassen- und Feldnamen unten sind die der Demo (`ng_article`, `teaser_intro`, `body`): Mit der Standardbenennung
verwendet das Schema die Bezeichner Ihrer Administration, also lesen Sie Ihre aus
`./console exp:graphql:schema --format=summary`.

## Inhalt

1. [Ein Blog- oder News-Frontend](#1-ein-blog--oder-news-frontend)
2. [Eine mobile App, die Menü und Artikel liest](#2-eine-mobile-app-die-menü-und-artikel-liest)
3. [Ein statischer Build](#3-ein-statischer-build)
4. [Ein KI-Agent, der Inhalte über GraphQL liest](#4-ein-ki-agent-der-inhalte-über-graphql-liest)

## 1. Ein Blog- oder News-Frontend

Eine Listenseite mit den neuesten Artikeln einer Rubrik, Seite für Seite, und eine Artikelseite.

```graphql
query Articles($path: String!, $after: String) {
  node(urlAlias: $path) {
    name
    children(classes: ["ng_article"], sortField: PUBLISHED, sortOrder: DESC, first: 3, after: $after) {
      totalCount
      pageInfo { hasNextPage endCursor }
      edges {
        node {
          name
          urlAlias
          published
          ... on ng_article {
            teaser_intro { excerpt(length: 80) }
            image { url(alias: "medium") alt }
          }
        }
      }
    }
  }
}
```

Mit `{"path": "fit-healthy/fitness"}`:

```json
{ "data": { "node": { "name": "Fitness", "children": {
    "totalCount": 14,
    "pageInfo": { "hasNextPage": true, "endCursor": "ZXhwOjI=" },
    "edges": [
      { "node": { "name": "Effective Training Techniques: Isolation vs Compound ...",
                  "urlAlias": "fit-healthy/fitness/effective-training-techniques-isolation-vs-compound",
                  "published": "2018-08-23T22:44:50+00:00", ... } },
      { "node": { "name": "How to Track Your Mobility: Training for Performance and Injury Prevention", ... } },
      { "node": { "name": "Effective Training Techniques: High Intensity Interval Training (HIIT)", ... } } ] } } } }
```

Die nächste Seite sendet `"after": "ZXhwOjI="`, den `endCursor` dieser Seite, bis `hasNextPage` `false` ist. `limit` und
`offset` funktionieren ebenso, am selben Feld.

Die Artikelseite, nach ihrer Adresse:

```graphql
query Article($path: String!) {
  node(urlAlias: $path) {
    name
    ownerName
    published
    parent { name urlAlias }
    ... on ng_article {
      full_intro { text }
      body { html }
      image { url(alias: "large") width(alias: "large") height(alias: "large") alt }
    }
  }
}
```

```json
{ "data": { "node": { "name": "Why Track Your Workouts?", "ownerName": "Graham Brookins",
    "parent": { "name": "Fitness", "urlAlias": "fit-healthy/fitness" },
    "full_intro": { "text": "Of all the things I track, one of the most valuable are my workouts. ..." },
    "body": { "html": "<p>Obviously getting to the gym or just doing your runs is ..." }, ... } } }
```

**Einstellungen.** Speichern Sie beide Abfragen (`exp:graphql:persist queries/ --apply`) und rufen Sie sie nach Namen mit
`GET` auf: Der [Antwort-Cache](../funktionen/6.0/graphql.md#geschwindigkeit) beantwortet sie, und das Veröffentlichen
eines Artikels lässt ihn verfallen. Das [Starter-Kit für Next.js oder Nuxt](graphql-schritt-fuer-schritt.md#6-das-starter-projekt-erzeugen)
hat diese Seiten fertig.

## 2. Eine mobile App, die Menü und Artikel liest

Eine App öffnet mit dem Menü, zeigt eine Liste einer Rubrik und einen Artikel. Sie ist ein öffentlicher Client: Geben
Sie ihr keinen Schlüssel (sie liest, was der anonyme Benutzer darf) oder einen nur mit `graphql:read`, nie einen, der
schreibt.

```graphql
query Menu {
  node(urlAlias: "fit-healthy") {
    name
    children(limit: 10) { nodes { name urlAlias classIdentifier } }
  }
}
```

```json
{ "data": { "node": { "name": "Fit & Healthy", "children": { "nodes": [
    { "name": "Fitness", "urlAlias": "fit-healthy/fitness", "classIdentifier": "ng_landing_page" },
    { "name": "Healthy eating", "urlAlias": "fit-healthy/healthy-eating", "classIdentifier": "ng_landing_page" },
    { "name": "Recipes", "urlAlias": "fit-healthy/recipes", "classIdentifier": "ng_category" },
    { "name": "Running", "urlAlias": "fit-healthy/running", "classIdentifier": "ng_topic" }, ... ] } } } }
```

Die Liste einer Rubrik ist die Abfrage `Articles` aus Rezept 1; der Artikelbildschirm liest den Text, den die App
selbst setzt:

```graphql
query ArticleForApp($path: String!) {
  node(urlAlias: $path) {
    name
    ... on ng_article {
      full_intro { text }
      body { html }
      image { variations(aliases: ["small", "large"]) { alias url width height } }
    }
  }
}
```

`variations` liefert jede Größe in einem Feld, sodass die App die für ihren Bildschirm wählt. Rich Text kommt als HTML
(das Expo-Kit rendert es mit `react-native-render-html`) oder als `text`.

**Einstellungen.** `./console exp:graphql:starter --kit=expo --to=../app --apply` erzeugt die App. Speichern Sie ihre
Abfragen und setzen Sie `PersistedQueriesOnly=anonymous`: Die App kann dann nur die Abfragen ausführen, die Sie
gespeichert haben, wer immer sie auseinandernimmt. Die anonyme Ratenbegrenzung (`RateLimitAnonymous`, 120 je Minute und
Adresse) zählt alle Telefone hinter einer gemeinsamen Netzadresse zusammen; erhöhen Sie sie für den Siteaccess der App,
wenn viele Benutzer sich eine teilen.

## 3. Ein statischer Build

Eine Site, beim Build in HTML-Dateien erzeugt, von jedem Host oder CDN ausgeliefert, neu gebaut, wenn sich Inhalte
ändern.

```bash
./console exp:graphql:starter --kit=astro --to=../site-static --apply
cd ../site-static && npm install && npm run build       # dist/
```

Der Build geht den Inhaltsbaum ab der Wurzel ab (`EXP_BUILD_DEPTH` Ebenen, standardmäßig 3), mit der erzeugten Abfrage
`Node`, und schreibt eine Seite je URL-Alias, jede mit der Abfrage ihrer Klasse:

```graphql
query Node($path: String!) {
  node(urlAlias: $path) {
    id
    name
    classIdentifier
    urlAlias
    mainLocation { id }
    parent { name urlAlias }
    children(limit: 20) {
      totalCount
      nodes { id name urlAlias classIdentifier }
    }
  }
}
```

**Einstellungen.** Der Build liest als anonymer Benutzer (für öffentliche Inhalte ist kein Schlüssel nötig) und läuft
auf einem Server, also braucht es keine CORS-Einstellung. Ein Build liest viele Seiten in kurzer Zeit: Geben Sie ihm
einen Schlüssel (`exp:graphql:keys --create --for=<ein Build-Benutzer> --apply`), damit er gegen `RateLimit` (600 je
Minute) zählt statt gegen die anonyme Grenze seiner Adresse. Bauen Sie aus einem Cronjob oder einem Deploy-Hook neu,
nachdem sich Inhalte geändert haben.

## 4. Ein KI-Agent, der Inhalte über GraphQL liest

Ein Agent (ein Chat-Assistent, der aus den Artikeln der Site antwortet, ein Zusammenfasser, ein Werkzeug eines
MCP-Servers) braucht die Inhalte als reinen Text, nach Wörtern gefunden, mit den Rechten eines Benutzers.

```graphql
query Find($text: String!) {
  search(text: $text, classes: ["ng_article"], limit: 5) {
    totalCount
    engine
    nodes {
      name
      url
      published
      ... on ng_article { full_intro { text } body { excerpt(length: 400) } }
    }
  }
}
```

Mit `{"text": "workouts"}`:

```json
{ "data": { "search": { "totalCount": 7, "engine": "eZSearchEngine", "nodes": [
    { "name": "The BiPolar Learning Curve", "url": "/fit-healthy/fitness/the-bipolar-learning-curve",
      "published": "2018-07-23T11:02:45+00:00",
      "full_intro": { "text": "People always forget about the nature of a learning curve." },
      "body": { "excerpt": "If you’re relatively new to training or ..." } }, ... ] } } }
```

`search` verwendet die Suchmaschine der Installation (die des Kernels oder Solr über ezfind, mit `facets`); `engine`
sagt, welche geantwortet hat. Dann der ganze Artikel als Text:

```graphql
query Read($path: String!) {
  node(urlAlias: $path) {
    name
    ownerName
    modified
    ... on ng_article { full_intro { text } body { text } }
  }
}
```

```json
{ "data": { "node": { "name": "Why Track Your Workouts?", "ownerName": "Graham Brookins",
    "full_intro": { "text": "Of all the things I track, one of the most valuable are my workouts. Along with tracking your heart and your sleep, I believe tracking your workouts provides a lot of benefits." },
    "body": { "text": "Obviously getting to the gym or just doing your runs is ..." } } } }
```

**Einstellungen.** Geben Sie dem Agenten einen eigenen Benutzer und einen Schlüssel nur mit `graphql:read`: Der Agent
liest dann genau, was die Rollen dieses Benutzers erlauben, und jede Anfrage wird je Schlüssel gezählt.
`excerpt(length:)` hält den Text kurz, den ein Agent in seinen Prompt setzt. Ein Agent, der nur Fragen beantwortet,
braucht nie Mutationen; muss einer schreiben (Entwürfe, die ein Redakteur prüft), geben Sie ihm `graphql:write` und
lassen Sie ihn `publish: false` senden, damit ein Mensch veröffentlicht.

## Verwandte Seiten

- [Ein Frontend auf der GraphQL-API, Schritt für Schritt](graphql-schritt-fuer-schritt.md)
- [Die GraphQL-API](../funktionen/6.0/graphql.md): Abfragen, Mutationen, Einstellungen, Fehler
