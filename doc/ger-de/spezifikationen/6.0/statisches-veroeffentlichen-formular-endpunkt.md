# Statisches Veröffentlichen: der Formular-Endpunkt

Der Vertrag zwischen einem Formular auf einer statischen Kopie und der Installation, die es sammelt. Umgesetzt von
`expStaticFormEndpoint`, `expStaticFormToken` und der Ansicht `staticpublish/form`; beim Build von `expStaticDegrader`
in die Seiten geschrieben. Seit Exponential 6.0.15.

Verwandt: [Dynamische Funktionen](../../anleitungen/statisches-veroeffentlichen-dynamische-funktionen.md),
[Das Build-Format](statisches-veroeffentlichen-build-format.md)

## Das Formular in der statischen Seite

Ein Build mit `Forms=endpoint` ändert jedes Formular eines Informationssammlers so:

```html
<form method="post" action="https://www.example.com/staticpublish/form" data-exp-static="form">
<input type="hidden" name="ExpStaticTarget" value="github-public" />
<input type="hidden" name="ExpStaticToken" value="v1.1793586627.57.jwRn67yebKzrLeQWZ3O-nN3oyhya5v2FJSXIByKh0tE" />
... die eigenen Felder des Formulars: ContentObjectID, ContentNodeID, die Attribute, ActionCollectInformation ...
</form>
```

Das Ziel ist die Adresse der Live-Site des Ziels (`LiveURL`, oder die `SiteURL` des Siteaccess über https), gefolgt vom
Siteaccess-Pfad des Builds und `/staticpublish/form`.

## Das Token

```
v1.<ausgestellt>.<Objekt-ID>.<mac>
mac = base64url( HMAC-SHA256( key, "staticpublish-form/1|<target>|<object id>|<issued>" ) )
key = expMailSecret::derive( "staticpublish-form" )
```

`ausgestellt` ist die Unix-Zeit des Builds. Ein Token ist gültig, wenn die Version `v1` ist, die Objekt-ID der
`ContentObjectID` des Posts gleicht, der MAC für das gesendete Ziel stimmt, `ausgestellt` höchstens 5 Minuten in der
Zukunft und höchstens `[StaticPublishSettings] FormTokenDays` Tage (Voreinstellung 400) in der Vergangenheit liegt.
Das Token enthält kein Geheimnis und keine Sitzung. Ein neues Geheimnis der Installation beendet jedes Token; eine neue
Veröffentlichung schreibt neue.

## Die Anfrage

`POST /staticpublish/form`, `application/x-www-form-urlencoded` oder `multipart/form-data`, von einem Browser auf der
statischen Kopie. `OPTIONS` wird für Preflights beantwortet. Jede andere Methode ist `405` mit `Allow: POST, OPTIONS`.

## Die Prüfungen, der Reihe nach

| # | Prüfung | Antwort, wenn sie fehlschlägt |
| --- | --- | --- |
| 1 | Statisches Veröffentlichen ist eingeschaltet und `FormEndpoint=enabled` | 404 |
| 2 | `ExpStaticTarget` nennt ein Ziel mit `Forms=endpoint` | 404 |
| 3 | der Header `Origin` (oder, ohne ihn, der Ursprung des `Referer`) ist der Ursprung der `PublicURL` des Ziels oder `https://`/`http://` seiner `CustomDomain` | 403 |
| 4 | `ExpStaticToken` ist für das Ziel und die `ContentObjectID` gültig | 403 |
| 5 | die Adresse des Clients hat weniger als `FormRateLimit` Mal (Voreinstellung 5) in `FormRateWindow` Sekunden (Voreinstellung 600) an dieses Ziel gesendet | 429 mit `Retry-After` |

Gezählt wird je Hash aus Ziel und Adresse in `<Cache-Verzeichnis>/staticpublish/rate`; die Adresse selbst wird nicht
gespeichert.

## Die Übergabe

Der Post ohne `ExpStaticTarget` und `ExpStaticToken`, dazu `ActionCollectInformation`, läuft durch `content/action`
als der aktuelle Benutzer (der anonyme Benutzer für einen Besucher). Es gelten die eigenen Prüfungen des Sammlers: Das
Objekt muss existieren, lesbar sein und Sammler-Attribute haben; Prüfung, die Regel „eine Antwort je Benutzer“,
E-Mail und Weiterleitung sind die der Live-Site. Der Post gilt als angenommen, wenn dieser Lauf mit einer
Weiterleitung endet.

## Die Antwort

- **JSON**, wenn die Anfrage `Accept: application/json` oder `X-Requested-With` hat: Status 200 (angenommen), 422
  (vom Sammler nicht angenommen) oder der Status der fehlgeschlagenen Prüfung, Inhalt
  `{ "ok": true|false, "message": "..." }`.
- **Sonst** `303 See Other` zum `Referer` (wenn er auf dem Ursprung des Ziels liegt; sonst zur öffentlichen Adresse),
  mit angehängtem `#exp-static-form-sent` oder `#exp-static-form-failed`. Eine Prüfung, die fehlschlägt, bevor der
  Ursprung bekannt ist (1 bis 3), wird als JSON beantwortet.

CORS: `Access-Control-Allow-Origin` ist der geprüfte Ursprung der Anfrage, nie `*`; `Access-Control-Allow-Credentials`
wird nie gesendet; `Vary: Origin` immer. Ein Preflight von einem Ursprung eines Ziels bekommt `204` mit
`Access-Control-Allow-Methods: POST, OPTIONS`, `Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With`
und `Access-Control-Max-Age: 600`. Jede Antwort hat `Cache-Control: no-store`, `X-Content-Type-Options: nosniff` und
`X-Robots-Tag: noindex, nofollow`.

## Zugriff und Audit

`site.ini [RoleSettings] PolicyOmitList[]=staticpublish/form`: Jeder darf senden; die Prüfungen oben sind der Schutz.
Jede Ablehnung (403, 429) und jeder übergebene Post ist das Audit-Ereignis `system.staticpublish.form` mit Ziel,
Objekt, Ergebnis und Grund.

## Ausschalten

`[StaticPublishSettings] FormEndpoint=disabled` beantwortet jede Anfrage mit 404. Die nächsten Builds ersetzen die
Formulare durch den Ersatztext und sagen das in ihren Warnungen.
