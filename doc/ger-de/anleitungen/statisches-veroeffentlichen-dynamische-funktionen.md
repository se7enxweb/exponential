# Statisches Veröffentlichen: dynamische Funktionen

Diese Anleitung ist für Administratoren und Entwickler einer Site mit Exponential 6.0.15+, die als statische Kopie
veröffentlicht wird. Sie erklärt, wie ein Build die Teile einer Seite findet, die einen Server brauchen, was er mit
jedem tut, wie die Suche einer Kopie funktioniert, wie Formulare die Live-Site erreichen und wie eine Kopie den
Inhalten automatisch folgt.

[Anleitungen](README.md) · Verwandt: [Suche, Formulare und Kommentare auf einer statischen Site](../hilfe/statisches-veroeffentlichen/suche-formulare-und-kommentare-auf-einer-statischen-site.md),
[Der Formular-Endpunkt](../spezifikationen/6.0/statisches-veroeffentlichen-formular-endpunkt.md), [Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md)

## Was Sie am Ende haben

- Ein Ziel, dessen Suche, Formulare und Anmeldungen so ersetzt werden, wie Sie es gewählt haben, mit dem Bericht zur
  Kontrolle.
- Suchindex und Suchseite, Formular-Endpunkt und Weiterleitungsseiten, gut genug verstanden, um ein Design daran
  anzupassen.
- Veröffentlichen beim Veröffentlichen von Inhalten und nach Zeitplan, mit Benachrichtigungen.

## Wie ein Build findet, was einen Server braucht

Nachdem eine Seite geholt, bereinigt und ihre Links umgeschrieben sind, sieht sich `expStaticDegrader` ihre Formulare
und Skripte an:

| Funktion | Erkannt an | Ersatz |
| --- | --- | --- |
| `search` | einem Formular mit Ziel `content/search` oder einem GET-Formular mit Feld `SearchText` | `search.html` der Kopie (Search=enabled), sonst die Live-Suche |
| `collect` | einem Formular mit Knopf `ActionCollectInformation` | der Formular-Endpunkt (Forms=endpoint), sonst der Ersatztext |
| `comments` | einem Ziel `comment/` oder `ezcomments/` oder einem Kommentarknopf | der Ersatztext |
| `shop` | `ActionAddToBasket`, `ActionAddToWishList` oder einem Ziel `shop/` | der Ersatztext |
| `login` | einem Ziel `user/login` oder einem Feld `Login` mit Passwort | entfernt |
| `ajax` | `ezjscore/call`, `ezjscore/run`, `ezjscServer` oder `$.ez(` in der Seite | nur gemeldet |
| `form` | jedem anderen Formular mit `method="post"` | bleibt; sendet an die Live-Site |

Der Bericht des Builds (`exp:static:build --report`, die Vorschau, die Seite des Jobs) hat `degraded`: je Funktion die
Anzahl, die Seiten, was getan wurde (`search-page`, `endpoint`, `fallback`, `removed`, `live`) und bis zu zehn
Beispielseiten. Das Audit-Ereignis `system.staticpublish.build` nennt die gefundenen Funktionen.

Der Ersatztext ist ein `<p class="exp-static-fallback">` mit `[StaticPublishSettings] FormFallbackText` und einem Link
(`FormFallbackLink`) zur selben Seite auf der Live-Site, wenn das Ziel eine kennt.

## Suche

Mit `Search=enabled` nimmt der Build jede Seite, die nicht `noindex` ist, in `expStaticSearchIndex` auf: das `lang`
des `<html>`, den Titel, die Meta-Beschreibung (oder den Anfang des Textes) und bis zu 40 Wörter des Hauptinhalts nach
Häufigkeit (Navigation, Formulare und Skripte ausgenommen). Er schreibt:

```
search.html                       die Seite; liest ?SearchText= oder ?q=
static-search/search.js           das Skript, ohne Bibliothek
static-search/search.css          ein kleines Stylesheet, hell und dunkel
static-search/index-<lang>.json   Schema exp-static-search/1: { lang, pages: [ { t, u, d, k } ] }
```

`u` ist die Adresse der Seite relativ zur Wurzel der Kopie. Ein Teil-Build behält die Einträge der Seiten, die er
nicht gebaut hat. Über `SearchMaxBytes` (2 MB) behält ein Index 10 Wörter je Seite, dann keine, dann keine
Anrisstexte, dann lässt er die letzten Seiten weg. Eine Site, die ihre eigene Suche gestalten will, verlinkt
`search.html` im Design oder nutzt die Indexdateien mit einem eigenen Skript.

## Formulare

Mit `Forms=endpoint` bekommt das Formular eines Informationssammlers das Ziel `<Live-Site>/staticpublish/form` und zwei
versteckte Felder, `ExpStaticTarget` und `ExpStaticToken`. Das Token ist `v1.<ausgestellt>.<Objekt-ID>.<HMAC>`,
signiert mit einem aus dem Geheimnis der Installation abgeleiteten Schlüssel, gültig für `FormTokenDays` Tage. Der
Endpunkt nimmt einen Post nur von der öffentlichen Adresse oder eigenen Domain des Ziels, mit gültigem Token und unter
der Ratenbegrenzung an und übergibt ihn dann an `content/action` mit `ActionCollectInformation`: Prüfung, E-Mail und
Einstellungen des Sammlers gelten wie auf der Live-Site. Jeder Post wird als `system.staticpublish.form` auditiert.
Der vollständige Vertrag steht in der [Spezifikation des Formular-Endpunkts](../spezifikationen/6.0/statisches-veroeffentlichen-formular-endpunkt.md).

Ein Design kann nach dem Senden eine Meldung zeigen, indem es auf `#exp-static-form-sent` oder
`#exp-static-form-failed` in der Adresse achtet, mit CSS (`:target`) oder einer Zeile Skript. Ein Skript kann auch mit
`fetch` und `Accept: application/json` senden und `{ "ok": true, "message": "..." }` lesen.

Der Build warnt und nimmt den Ersatztext, wenn der Endpunkt nicht nutzbar ist: `FormEndpoint=disabled`, keine Adresse
der Live-Site oder keine öffentliche Adresse.

## Weiterleitungsseiten

Mit `Extras[]=redirects` liest ein vollständiger Build `ezurlalias_ml` und `ezurlwildcard` und schreibt eine Seite für
jeden Verlaufseintrag und jeden eigenen Alias eines Knotens, den er enthält, und für jeden Platzhalter ohne `*`, der
zu einer Seite der Kopie führt. Jede ist ein Meta-Refresh mit Canonical-Link auf die öffentliche Adresse und
`noindex`. Ein Teil-Build behält die des letzten vollständigen Builds.

## Automatisch veröffentlichen

`eZContentCacheManager` ruft `expStaticPublishQueue::nodes()` mit den Knoten auf, deren Ansichts-Cache er leert. Für
jedes Ziel mit `Triggers[]=publish` bekommen die Zeilen dieser Knoten in `exp_static_page` in `stale` die Uhrzeit; ein
Knoten ohne Seite bekommt eine Zeile `.queue/<knoten>`. Der Cronjob-Teil `staticpublish` baut die veralteten Knoten
eines Ziels, sobald seit der letzten Änderung `PublishDebounce` Sekunden vergangen sind (über 200 Knoten ein
vollständiger Build), und veröffentlicht. Eine Änderung während eines Builds bleibt für den nächsten Lauf veraltet.

`Triggers[]=schedule` mit `Schedule=<Cron-Ausdruck>` lässt denselben Cronjob-Teil vollständig bauen und
veröffentlichen, wenn der Ausdruck seit dem letzten geplanten Lauf des Ziels zutraf (gemerkt in
`<WorkDir>/.schedule.json`).

## Benachrichtigungen

Nach jeder Veröffentlichung und Rücknahme sendet `expStaticNotifier` die E-Mail von `Notify[]` und postet an
`Webhook`; siehe [Benachrichtigungen und Webhooks](../hilfe/statisches-veroeffentlichen/benachrichtigungen-und-webhooks.md).

## Nächster Schritt

[Die Spezifikation des Formular-Endpunkts](../spezifikationen/6.0/statisches-veroeffentlichen-formular-endpunkt.md).
