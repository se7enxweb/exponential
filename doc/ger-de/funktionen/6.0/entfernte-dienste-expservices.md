# Entfernte Dienste: die ganze Administrationsoberfläche als HTTP-API (expservices)

Diese Seite ist für Entwickler, die entfernte Admin-Anwendungen oder JavaScript-Frontends auf Exponential aufbauen.
`extension/expservices` macht aus Serverfunktionen von ezjscore eine Bibliothek entfernter Dienste, 1.122 davon, wie
Setup > RAD sie zum Zeitpunkt des Schreibens zählte (zählen Sie dort neu oder mit dem Katalogaufruf
`expservices::catalog`, unten gezeigt), sodass entfernte Admin-Anwendungen (Desktop, iOS, Android, Shell-Skripte) und
JavaScript-Frontends tun können, was die Administrationsoberfläche tut: Einstellungen lesen, Caches leeren, Inhalte,
Benutzer, Shop, Feeds, Medien, Tags, Layouts und das Audit verwalten. Hinzugefügt am 02.10.2026 (Version 0.1.0 der
Extension, erster Entwurf).

Vollständiger Katalog und Tabellen pro Domäne: [Anleitung zu den Backend-Diensten](../../bc/6.0/backend-dienste-ueber-ezjscore.md).

## Einen in 30 Sekunden aufrufen

```bash
# sign in once, keep the cookie
curl -c jar -d 'username=<user>' --data-urlencode 'password=<password>' \
  'https://<site>/ezjscore/call/expsession::login?ContentType=json'
curl -b jar 'https://<site>/ezjscore/call/expsession::whoami?ContentType=json'
# discover everything
curl -b jar 'https://<site>/ezjscore/call/expservices::catalog::system?ContentType=json'
```

Ein Konto, für das die zweistufige Anmeldung verlangt wird (`sevenx_authentication_2fa`, `Enforce2FA`), sendet
zusätzlich den aktuellen Code seiner Authenticator-App: `-d 'otp=<code>'` (oder den Header `X-Exp-OTP`). Ohne ihn
antwortet die Anmeldung mit `401` und `error.reason` `second_factor_required`
([Zweiter Schritt auf den anderen Anmeldewegen](zweiter-schritt-auf-anderen-anmeldewegen.md)).

Die Form der URL ist `<root>/ezjscore/call/exp<domain>::<method>[::<arg>...]`. Antworten verwenden eine Hülle:
`{"ok":true,"data":...,"meta":{...}}` oder `{"ok":false,"error":{"code":403,"message":"..."}}`; Listen werden mit
`total`, `offset`, `limit`, `count`, `has_more` seitenweise geliefert.

## Persönliche API-Tokens (für Skripte und Anwendungen)

Ein angemeldeter Benutzer erzeugt ein Token mit `expsession::tokenCreate` (POST `name`, `expires_in_days` 1 bis 365,
Standard 90). Das Token (`expt_` und 48 Hexadezimalzeichen) wird einmal gezeigt; nur sein SHA-256-Hash wird gespeichert.
Senden Sie es als `Authorization: Bearer <token>` oder, wo ein Proxy diesen Header nicht an PHP weitergibt, als
`X-Exp-Token: <token>`. Anfragen melden sich nur für diese Anfrage als der Benutzer des Tokens an: kein Cookie, dieselben
Richtlinien wie der Benutzer, Schreibvorgänge brauchen weiterhin POST, aber kein Formular-Token. Abgelaufene,
widerrufene Tokens oder Tokens abgeschalteter Benutzer antworten mit 401; ein Token kann keine Tokens erzeugen.
`expsession::tokenList` und `expsession::tokenRevoke` verwalten sie. Die Ereignisse `access.expservices.token.create`,
`.revoke` und `.failed` gehen in das [Prüfprotokoll](pruefprotokoll.md). Die Tabelle `expservices_token` wird aus
`extension/expservices/sql/<engine>/schema.sql` angelegt (MySQL, PostgreSQL, SQLite).

## Domänen

| Gruppe | Domänen und Dienste |
|---|---|
| Kern | Katalog 5, Sitzung 11, System 16, Einstellungen lesen 10, Caches 14, Cronjobs 7, Extensions 9, Pakete 7, Workflows 10, Velocity 5, Übersicht der Erweiterungspunkte 7, Debug-Zusammenfassung 6 |
| Inhalte (350) | Knoten, Objekte, Klassen, Klassengruppen, Attribute, Versionen, Übersetzungen, Beziehungen, Platzierungen, Papierkorb, Sektionen, Zustände, URL-Aliase und Platzhalter, Suche, Inhaltsaufträge |
| Benutzer und Zugriff (195) | Benutzer, Gruppen, Rollen, Richtlinien, Sitzungen, Einstellungen, Benachrichtigungen, Zusammenarbeit, Kontoabläufe |
| Handel, Community, Feeds (255) | Produkte, Warenkorb und Bestellvorgang, Bestellungen, MwSt., Währungen, Rabatte, Wunschlisten, Versand, Zahlungen, gesammelte Formulare, Umfragen, Foren, Kommentare, Bewertungen, RSS-Exporte und -Importe, Ausgabe von RSS/Atom/JSON Feed für jeden Teilbaum |
| Medien und weitere (218) | Bilder und Varianten, Dateien und signierte Download-Links, Audio und Video, Tags, Layouts, Audit (nur lesend), Spalten der Unterelemente, Newsletter, Sitemaps, Statistik, Designs, Sprachen, externe Links, PDF-Links |
| Editor | `expeditor::engines`, `get`, `set`, `uploadExtensions`, `config` (siehe [den Editor TinyMCE 8](online-editor-tinymce8.md)) |

## Einen eigenen Dienst schreiben

Eine Domäne ist eine Klasse, die `expServiceBase` erweitert (`extension/expservices/classes/expservicebase.php`),
registriert als `[ezjscServer_exp<domain>]` in einer `ezjscore.ini.append.php` (die mitgelieferten stehen in
`extension/expservices/settings/ezjscore.ini.append.php`). Jeder Dienst ist eine `public static`-Methode, deklariert im
Array `$services` der Klasse mit `summary`, `access` (`public`, `user` oder `[module, function]`), `write` (true = POST,
Formular-Token, protokolliert), `args` (`int`, `string`, `bool`, `json`, `list`) und `returns`. Ein Dienst antwortet über
`ok()` oder `page()`; das Werfen von `expServiceException` mit 400, 401, 403, 404, 409 oder 422 wird zur Fehlerhülle. Der
neue Dienst erscheint ohne weiteren Schritt im Katalog und in Setup > RAD (`/setup/rad`).

## Regeln, denen jeder Dienst folgt

- `access` ist `public`, `user` oder eine Richtlinie (Modul, Funktion); 401 ohne Anmeldung, 403 ohne die Richtlinie.
- Schreibvorgänge brauchen POST, das Formular-Token (Feld `ezxform_token` oder Header `X-CSRF-Token`, aus
  `expsession::token`) und die Richtlinie; jeder erfolgreiche Schreibvorgang erfasst `service.<domain>.<method>` im
  Audit.
- Große Operationen werden zu [Inhaltsaufträgen](inhaltsauftraege.md) geleitet.
- Stimmen in Umfragen, Absenden von Formularen und die Wahl des MwSt.-Landes brauchen `content/read` (anonyme Besucher
  haben es auf einer öffentlichen Website); kein Schreibvorgang ist ohne Richtlinie offen.
- `expfeed::runImport` führt einen RSS-Import mit dem Code des Cronjobs aus: POST, Richtlinie `rss/edit`, `dry_run`, um
  nur zu berichten, `max_items`, um zu begrenzen.

## Einstellungen (extension/expservices/settings/expservices.ini)

| Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|
| `Services` | `Enabled` | `enabled` (bei `disabled` antwortet jeder Dienst mit 403) | Installation |
| `Paging` | `DefaultLimit` / `MaxLimit` | 25 / 200 | Installation |
| `Writes` | `RequireToken` | `enabled` (nur für Tests abschalten) | Installation |
| `Writes` | `Audit` | `enabled` | Installation |
| `Community` | `ForumClass`, `ForumContainerClass`, `TopicClass`, `ReplyClass`, `CommentClass`, `PollClass`, `ReviewClass`, `TopicStickyAttribute` | `forum`, `forums`, `forum_topic`, `forum_reply`, `comment`, `poll`, `review`, `sticky` | Installation |

## Darauf gebaute Portale

Beispiel-Frontends (vier Designs unter `extension/expservices/design/`: `expportal_jquery`, `expportal_react`,
`expportal_reactive` und `expportal_wireframe`; ihre gemeinsamen Einstellungen stehen in
`extension/expservices/settings/expportal.ini`) geben News, Shop, Foren, Medien, Feeds, Suche und Anmeldung im Browser
ohne Build-Schritt aus: `expportal_jquery` (Referenz mit jQuery 4) und `expportal_reactive` (React mit React Bootstrap,
Komponenten, einem Store und einseitigem Datenfluss). Sie rufen die Dienste über den Katalognamen auf. Das Kapitel
„Clients“ der Anleitung hat Beispiele für Shell und Python.

## Verwandte Seiten

- [Anleitung zu den Backend-Diensten](../../bc/6.0/backend-dienste-ueber-ezjscore.md), [Spezifikation von expservices](../../spezifikationen/6.0/expservices.md)
- [Das Prüfprotokoll](pruefprotokoll.md), [Audit-Ereignismodell](../../spezifikationen/6.0/audit-ereignismodell.md)
- [Spalten der Unterelementliste](optionen-der-unterelementtabelle.md) (`expsubitems_svc`), [Online-Editor auf TinyMCE 8](online-editor-tinymce8.md) (`expeditor`), [ezjscore](erweiterungen/ezjscore.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../geschichte/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
