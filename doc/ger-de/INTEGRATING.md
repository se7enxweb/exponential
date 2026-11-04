# Mit Exponential 6.0 integrieren

Diese Anleitung verbindet andere Systeme mit einer Installation von Exponential 6.0: Apps, Shops, Portale,
Suchdienste, Identitätsanbieter, Log-Plattformen, KI-Dienste und Deployment-Pipelines. Sie zeigt, welche Wege hinein
und hinaus die Installation bietet, wie sich eine Maschine anmeldet, wie Inhalte und Ereignisse die Installation
verlassen und wie eine Integration sicher und schnell bleibt. Sie setzt eine funktionierende, konfigurierte
Installation aus [Exponential 6.0 installieren](INSTALL.md) und [Exponential 6.0 konfigurieren](CONFIGURING.md)
voraus. Jeder Abschnitt nennt die Einstellungen, zeigt ein funktionierendes Beispiel und verweist auf die Seite mit
allen Einzelheiten. So bleibt diese Seite die Landkarte und die tieferen Seiten bleiben die Referenz.

Jeder Befehl wird im Wurzelverzeichnis der Installation ausgeführt: dem Verzeichnis, das `index.php`, `autoload.php`,
`bin/` und `settings/` enthält. Die Befehlskonsole ist `bin/php/console`; viele Installationen legen die Verknüpfung
`./console` an (`ln -s bin/php/console console`). Wenn Sie ein Skript als `root` ausführen, ergänzen Sie
`--allow-root-user`; das Skript weist Sie darauf hin, wenn es nötig ist. Die Beispiele verwenden
`https://example.com` für die Installation und `site` für ihren öffentlichen Siteaccess.

> **Eine Integration ist ein Benutzer.** Jeder Weg hinein läuft als ein Benutzer der Installation, und dessen Rollen
> und Richtlinien entscheiden, was er lesen und ändern darf, genau wie in der Administration. Geben Sie jeder
> Integration ein eigenes Konto mit der kleinsten Rolle, die funktioniert, nie das des Administrators. Siehe
> [Dienstkonten, Rollen und Richtlinien](hilfe/integratoren/dienstkonten-rollen-und-richtlinien.md).

## Inhalt

1. [Der Schnellstart: Ihr erster Aufruf](#1-der-schnellstart-ihr-erster-aufruf)
2. [Einen Weg hinein wählen](#2-einen-weg-hinein-wählen)
3. [Die REST-Inhalts-API und OAuth 2.0](#3-die-rest-inhalts-api-und-oauth-20)
4. [Persönliche API-Schlüssel und Tokens](#4-persönliche-api-schlüssel-und-tokens)
5. [GraphQL](#5-graphql)
6. [Remote-Dienste und ezjscore-Aufrufe](#6-remote-dienste-und-ezjscore-aufrufe)
7. [SOAP, WebDAV und Syndikation](#7-soap-webdav-und-syndikation)
8. [Inhalte hinein und hinaus: Pakete, Migrationen, Importe, Exporte und Feeds](#8-inhalte-hinein-und-hinaus-pakete-migrationen-importe-exporte-und-feeds)
9. [Ereignisse hinaus: Webhooks, der Audit-Trail, E-Mail und die Job-Warteschlange](#9-ereignisse-hinaus-webhooks-der-audit-trail-e-mail-und-die-job-warteschlange)
10. [Benutzer und Anmeldung: LDAP, Single Sign-on und die Benutzerkonsole](#10-benutzer-und-anmeldung-ldap-single-sign-on-und-die-benutzerkonsole)
11. [Frontends: Headless, CORS, Caching und Velocity](#11-frontends-headless-cors-caching-und-velocity)
12. [Suche](#12-suche)
13. [KI-Dienste](#13-ki-dienste)
14. [Automatisierung und CI: die Konsole](#14-automatisierung-und-ci-die-konsole)
15. [Sicherheit für Integrationen](#15-sicherheit-für-integrationen)
16. [Fehlerbehebung](#16-fehlerbehebung)

## 1. Der Schnellstart: Ihr erster Aufruf

Drei Schritte führen ein Skript von nichts zu einem angemeldeten Lesezugriff auf den Inhaltsbaum.

**1. Prüfen Sie, welche Wege hinein eingeschaltet sind:**

```bash
./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions   # ezprestapi, expservices ...
./console exp:ini get rest.ini/Authentication/RequireAuthentication
./console exp:ini get rest.ini/ApiKeySettings/ApiKeys
./console exp:graphql:status --siteaccess=site
```

**2. Erstellen Sie einen persönlichen API-Schlüssel** für das eigene Konto der Integration: Melden Sie sich mit diesem
Konto an, öffnen Sie **Mein Konto > API-Zugang**, wählen Sie die Bereiche (Scopes) und ein Enddatum und kopieren Sie
den Schlüssel. Er wird nur einmal angezeigt. Die Rolle des Kontos braucht die Richtlinie `apikey/create`, die keine
Rolle hat, bis Sie sie vergeben ([Persönliche API-Schlüssel](anleitungen/api-schluessel.md)).

**3. Rufen Sie damit die REST-Inhalts-API auf:**

```bash
curl -s -H 'Authorization: Bearer expk_...' https://example.com/api/ezp/v2/content/node/2
```

Ohne den Schlüssel antwortet dieselbe Anfrage mit `401`, weil `rest.ini [Authentication] RequireAuthentication=enabled`
die Vorgabe ist. Mit ihm ist die Antwort die Wurzel des Inhaltsbaums als JSON, beschränkt auf das, was der Besitzer
des Schlüssels lesen darf.

Wählen Sie von hier aus die Schnittstelle, die zur Aufgabe passt ([Abschnitt 2](#2-einen-weg-hinein-wählen)), und
lesen Sie [Integratoren: Hier beginnen](hilfe/integratoren/integratoren-hier-beginnen.md) für die Projektseite einer
Integration.

## 2. Einen Weg hinein wählen

| Sie möchten | Verwenden Sie | Abschnitt |
|---|---|---|
| Inhalte aus einer App, einem Shop oder einem Portal lesen und schreiben | Die REST-Inhalts-API oder GraphQL | [3](#3-die-rest-inhalts-api-und-oauth-20), [5](#5-graphql) |
| Benutzer einer anderen Anwendung als sie selbst auf der Website handeln lassen | OAuth 2.0 | [3](#3-die-rest-inhalts-api-und-oauth-20) |
| Ein eigenes Skript als ein Konto ausführen | Einen persönlichen API-Schlüssel (REST, GraphQL) oder ein `expservices`-Token | [4](#4-persönliche-api-schlüssel-und-tokens) |
| Verwaltungsfunktionen (Caches, Benutzer, Shop, Feeds) aus der Ferne steuern | Remote-Dienste (`expservices`) | [6](#6-remote-dienste-und-ezjscore-aufrufe) |
| Den Inhaltsbaum als Laufwerk einbinden oder eine alte Middleware anbinden | WebDAV, SOAP | [7](#7-soap-webdav-und-syndikation) |
| Inhalte, Klassen und Einstellungen zwischen Installationen bewegen | Pakete, Migrationen, Syndikation | [7](#7-soap-webdav-und-syndikation), [8](#8-inhalte-hinein-und-hinaus-pakete-migrationen-importe-exporte-und-feeds) |
| Aus Dateien, Datenbanken und Feeds importieren oder dorthin exportieren | Die Import- und Exportwerkzeuge, RSS, Sitemaps | [8](#8-inhalte-hinein-und-hinaus-pakete-migrationen-importe-exporte-und-feeds) |
| Einem anderen System mitteilen, wenn sich etwas ändert | GraphQL-Webhooks, Senken des Audit-Trails | [9](#9-ereignisse-hinaus-webhooks-der-audit-trail-e-mail-und-die-job-warteschlange) |
| Benutzer aus einem Verzeichnis oder bei einem anderen Identitätsanbieter anmelden | Anmelde- und Single-Sign-on-Handler | [10](#10-benutzer-und-anmeldung-ldap-single-sign-on-und-die-benutzerkonsole) |
| Ein Frontend auf einem anderen Host ausliefern | CORS, der HTTP-Cache, statisches Veröffentlichen | [8](#8-inhalte-hinein-und-hinaus-pakete-migrationen-importe-exporte-und-feeds), [11](#11-frontends-headless-cors-caching-und-velocity) |
| Eine Installation aus einer Pipeline automatisieren | Die Konsole und ihre JSON-Ausgabe | [14](#14-automatisierung-und-ci-die-konsole) |

Das vollständige Verzeichnis, mit Richtung, Protokoll, Anmeldung und Vorgabe jeder Schnittstelle, ist
[Integrationsschnittstellen auf einen Blick](hilfe/integratoren/integrationsschnittstellen-auf-einen-blick.md); die Entscheidung
ausführlicher beschreibt [Eine Integrationsschnittstelle wählen](hilfe/integratoren/eine-integrationsschnittstelle-waehlen.md).
Code, der innerhalb der Installation läuft (Workflow-Ereignisse, Event-Listener, Cronjob-Teile, ein eigenes Modul),
ist Erweitern statt Integrieren: siehe [Exponential 6.0 erweitern](../eng-us/EXTENDING.md).

## 3. Die REST-Inhalts-API und OAuth 2.0

*Vollständige Anleitung: [REST-API v1 und v2](anleitungen/rest-api-v1-und-v2.md)*

Die Extension `ezprestapi` liefert die Inhalts-API über `index_rest.php` aus, unterhalb von `/api/`:

- **Lesen** antwortet in Version 1 und Version 2: `/api/ezp/v1/...` und `/api/ezp/v2/...`.
- **Schreiben** (Anlegen, Ändern, Löschen) antwortet nur in Version 2.
- **Jedes Lesen und Schreiben prüft die Richtlinien des Benutzers** (`content/read`, `content/create`,
  `content/edit`, `content/remove`), womit auch immer die Anfrage angemeldet wurde.

Aktivieren Sie sie mit `ActiveExtensions[]=ezprestapi`
([Exponential 6.0 konfigurieren, Abschnitt 8](CONFIGURING.md#8-extensions)). Das ältere `ezprestapiprovider` ist
veraltet: Seine letzte Version ist eine Kompatibilitätsschicht über `ezprestapi`. Entfernen Sie es deshalb aus
`ActiveExtensions[]`, sobald `ezprestapi` aktiv ist.

### Anmelden

`rest.ini [Authentication] RequireAuthentication=enabled` (die Vorgabe) verlangt für jede Inhaltsroute einen
Benutzer. Zwei Anmeldedaten werden angenommen:

- **Ein persönlicher API-Schlüssel**, für ein eigenes Skript ([Abschnitt 4](#4-persönliche-api-schlüssel-und-tokens)).
- **Ein OAuth-2.0-Token**, für eine Anwendung, bei der sich *andere* Benutzer anmelden und für die sie handelt:
  1. Registrieren Sie die Anwendung unter **Setup > oAuth admin** (`/oauthadmin/list`): Sie erhält eine Client-ID,
     ein Geheimnis und ihre Weiterleitungsadresse.
  2. Die Anwendung schickt den Benutzer zu `https://example.com/oauth/authorize?client_id=<client id>&redirect_uri=<redirect uri>&response_type=code&state=<random>`,
     mit PKCE zusätzlich `&code_challenge=<challenge>&code_challenge_method=S256`. Der Benutzer braucht die
     Richtlinie `oauth/authorize`.
  3. Sie tauscht den Code bei `POST /api/auth/v1/oauth/token` ein (`grant_type=authorization_code`) und sendet das
     Token als `Authorization: Bearer <token>`.
  4. Sie erneuert das Token mit `grant_type=refresh_token`, bevor `rest.ini [OAuthSettings] TokenTTL` (eine Stunde)
     abläuft.

  Der Token-Endpunkt unterstützt die Grants `authorization_code` und `refresh_token`. Ein öffentlicher Client, etwa
  eine mobile App ohne Geheimnis, muss PKCE verwenden. Ein Token funktioniert nicht mehr, sobald sein Benutzer
  deaktiviert oder gesperrt ist. Einzelheiten:
  [OAuth-Anwendungen für Integrationen](hilfe/integratoren/oauth-anwendungen-fuer-integrationen.md),
  [PKCE und state](bc/6.0/oauth-pkce-und-state.md) und [Härtung der OAuth-Tokens](bc/6.0/oauth-token-haertung.md).

```bash
# die Kinder der Inhaltswurzel, als Besitzer des Schlüssels
curl -s -H 'Authorization: Bearer expk_...' https://example.com/api/ezp/v2/content/node/2/list/offset/0/limit/20
```

Weitere Aufrufe, die Routentabelle und die Einstellungen: [REST-API v1 und v2](anleitungen/rest-api-v1-und-v2.md) und
[Die REST-API für Integratoren](hilfe/integratoren/rest-api-fuer-integratoren.md).

## 4. Persönliche API-Schlüssel und Tokens

*Vollständige Anleitung: [Persönliche API-Schlüssel](anleitungen/api-schluessel.md)*

Mit einem persönlichen API-Schlüssel ruft ein Skript die REST-API und GraphQL als ein Benutzer auf, ohne dessen
Passwort, beschränkt auf die **Bereiche**, für die der Schlüssel erstellt wurde.

- **Erstellt** vom Benutzer unter **Mein Konto > API-Zugang**, mit der Richtlinie `apikey/create`. Der Schlüssel
  beginnt mit `expk_` und wird nur einmal angezeigt; gespeichert wird nur sein Hash.
- **Gesendet als** `Authorization: Bearer expk_...`.
- **Begrenzt durch** `rest.ini [ApiKeySettings]`: `ApiKeys=enabled`, höchstens `MaxKeysPerUser=10` Schlüssel je
  Benutzer, `DefaultExpiryDays=90`, `MaxExpiryDays=365` und `RateLimitPerMinute=120` Anfragen pro Minute und
  Schlüssel.
- **Beobachtet** vom Audit-Trail: `access.apikey.create`, `.revoke`, `.use` und `.use.failed` (mit dem Grund, etwa
  `expired`, `scope` oder `rate_limited`). Die Administration listet jeden Schlüssel und kann jeden widerrufen.

`expservices` hat eigene Tokens (`expt_` und 48 Hexadezimalzeichen), gemacht für seine Remote-Dienste
([Abschnitt 6](#6-remote-dienste-und-ezjscore-aufrufe)). Welche Anmeldedaten zu welcher Integration passen:
[Anmeldedaten für eine Integration wählen](hilfe/integratoren/zugangsdaten-fuer-eine-integration-waehlen.md) und
[Persönliche API-Schlüssel für Integrationen](hilfe/integratoren/persoenliche-api-schluessel-fuer-integrationen.md).

## 5. GraphQL

*Vollständige Referenz: [Die GraphQL-API](funktionen/6.0/graphql.md)*

Der Kernel beantwortet GraphQL unter `/api/graphql` in jedem Siteaccess, in dem es eingeschaltet ist. Das Schema wird
aus den Inhaltsklassen der Installation erzeugt: Jede Klasse ist ein Typ, jedes Attribut ein Feld. Eine Abfrage läuft
als ein Benutzer (angemeldet mit einem OAuth-Token, einem persönlichen API-Schlüssel, der Sitzung der Administration
oder anonym), und dessen Richtlinien entscheiden, was sie liest. Mutationen ändern Inhalte mit denselben Richtlinien,
Workflows und demselben Audit wie die Administration.

Schalten Sie es für einen Siteaccess und ein Frontend mit einem Befehl ein. Ohne `--apply` zeigt er, was er ändern
würde:

```bash
./console exp:graphql:enable --siteaccess=site --origin=https://shop.example.com --key-for=frontend
./console exp:graphql:enable --siteaccess=site --origin=https://shop.example.com --key-for=frontend --apply
./console exp:graphql:status --siteaccess=site
```

Er setzt `graphql.ini [GraphQLSettings] Enabled=enabled` in `settings/override/`, erlaubt den Ursprung des Frontends
(`rest.ini [CorsSettings]`), erstellt einen Schlüssel mit dem Bereich `graphql:read` für den Benutzer `frontend` und
gibt eine `curl`-Zeile zum Ausprobieren aus. **Setup > GraphQL** hat dieselben Schalter. Abfragen sind begrenzt (Tiefe,
Komplexität, Listengröße; siehe `exp:graphql:status`), und gespeicherte Abfragen (Persisted Queries) werden aus einem
Antwort-Cache beantwortet.

Schritt für Schritt: [GraphQL-Rundgang](anleitungen/graphql-schritt-fuer-schritt.md) und [GraphQL-Rezepte](anleitungen/graphql-rezepte.md).

## 6. Remote-Dienste und ezjscore-Aufrufe

*Vollständige Anleitung: [Remote-Dienste und Apps](anleitungen/entfernte-dienste-und-apps.md)*

Die Extension `expservices` macht aus den Funktionen der Administration mehr als tausend benannte Remote-Dienste
(Inhalte, Benutzer, Rollen, Caches, Cronjobs, Shop, Feeds, Medien, Tags, Layouts, das Audit), als JSON ausgeliefert
über die Aufrufschnittstelle von `ezjscore`:

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

Ein Aufruf läuft als die angemeldete Sitzung (mit ihrem Formular-Token) oder als Besitzer eines persönlichen
`expservices`-Tokens (`expt_...`). Die Einstellungen stehen in `extension/expservices/settings/expservices.ini`
(`[Services] Enabled=enabled`); **Setup > RAD** (`/setup/rad`) listet den Katalog der Installation. Eigene
`ezjscore`-Serverfunktionen antworten ebenso unter `/ezjscore/call/<server>::<function>` und brauchen die Richtlinie
`ezjscore/call`.

Mehr: [Remote-Dienste: die ganze Administration als HTTP-API](funktionen/6.0/entfernte-dienste-expservices.md) und
[Remote-Dienste mit expservices](hilfe/integratoren/entfernte-dienste-mit-expservices.md).

## 7. SOAP, WebDAV und Syndikation

Alle drei sind **in der Vorgabe ausgeschaltet** und fragen nur nach dem Passwort, nie nach dem zweiten
Anmeldeschritt ([Abschnitt 15](#15-sicherheit-für-integrationen)).

| Schnittstelle | Schalter | Einstiegsskript | Seite |
|---|---|---|---|
| SOAP-Server (SOAP 1.1, ohne WSDL) | `soap.ini [GeneralSettings] EnableSOAP=true` | `index_soap.php` (`soap.php` vor 6.0.15) | [Das SOAP-Einstiegsskript](bc/6.0/soap-einstiegsskript-index-soap.md) |
| WebDAV (Klasse 1), der Inhaltsbaum als Laufwerk | `webdav.ini [GeneralSettings] EnableWebDAV=true` | `index_webdav.php` (`webdav.php` vor 6.0.15) | [Das WebDAV-Einstiegsskript](bc/6.0/webdav-einstiegsskript-index-webdav.md) |
| `syndication`: Inhalte, die Installationen als Feeds teilen, per Cron abgeglichen | die Extension, dazu SOAP | `/syndication/menu` | [syndication](funktionen/6.0/erweiterungen/syndication.md) |

Ruft eine Website *andere* SOAP-Server auf, prüft der Client deren Zertifikate: [SOAP-Client und TLS](funktionen/6.0/soap-client-tls.md).
Beide Protokolle für Integratoren: [WebDAV und SOAP](hilfe/integratoren/webdav-und-soap.md) und
[Syndikation zwischen Installationen](hilfe/integratoren/syndizierung-zwischen-installationen.md).

## 8. Inhalte hinein und hinaus: Pakete, Migrationen, Importe, Exporte und Feeds

| Aufgabe | Werkzeug | Seite |
|---|---|---|
| Inhalte, Klassen, Templates und Dateien zwischen Installationen bewegen | Pakete (`.ezpkg`): Setup > Pakete, oder `ezpm` auf der Kommandozeile | [Pakete](anleitungen/pakete.md), [ezpm](funktionen/6.0/ezpm-paketmanager-cli.md) |
| Dieselben Änderungen auf jede Installation einer Website anwenden, in Reihenfolge, mit Protokoll und Rücknahme | Migrationen: `exp:migration:migrate`, `exp:migration:status`, `--format=json` | [Migrationen](anleitungen/migrationen.md), [Migrationen im Kernel](funktionen/6.0/migrationen.md) |
| Aus Dateien, Datenbanken, Feeds und anderen Plattformen importieren, nach Zeitplan | Die Importwerkzeuge, mit einem gemeinsamen Laufverlauf (**Importe**) | [Welches Import- oder Exportwerkzeug brauche ich?](hilfe/import-oder-export-werkzeug.md), [Übertragungsläufe](funktionen/6.0/transfer-laeufe.md) |
| Inhalte als CSV, JSON, XML oder Pakete exportieren, nach SFTP, S3, WebDAV oder HTTP | `xrowextract`, der Reiter **Export** | [xrowextract](funktionen/6.0/erweiterungen/xrowextract.md) |
| Benutzer hinein und hinaus, als CSV oder JSON, ohne Passwörter | `exp:user:export`, `exp:user:import` | [Benutzerverwaltung über die Konsole](funktionen/6.0/benutzerverwaltung-von-der-konsole.md) |
| RSS-1.0- oder 2.0-, Atom-, OPML- oder Podcast-Feeds veröffentlichen; RSS importieren | Setup > RSS; `/rss/feed/<address>`; der Cronjob `rssimport.php` | [RSS-Feeds](anleitungen/rss-feeds.md) |
| Suchmaschinen jede öffentliche Seite nennen | `bcgooglesitemaps`, je Siteaccess eine `sitemap_<siteaccess>.xml`, per Cronjob | [bcgooglesitemaps](funktionen/6.0/erweiterungen/bcgooglesitemaps.md) |
| Eine statische Kopie einer Website auf GitHub Pages, einem anderen Git-Host oder einem einfachen Webserver | Statisches Veröffentlichen: Setup > Statisches Veröffentlichen, `exp:static:build`, `exp:static:publish` | [Statisches Veröffentlichen](funktionen/6.0/statisches-veroeffentlichen.md), [Statische Websites auf GitHub Pages hosten](anleitungen/statische-websites-auf-github-pages-hosten.md) |

Ein Import sollte über die Remote-ID jedes Objekts geschlüsselt sein, sodass ein zweiter Lauf nichts mehr ändert:
[Idempotente Importe über die Remote-ID](hilfe/integratoren/idempotente-importe-ueber-die-remote-id.md). Leeren Sie nach
einem großen Import die betroffenen Caches ([Caching nach Importen](hilfe/integratoren/caching-nach-importen.md)).

## 9. Ereignisse hinaus: Webhooks, der Audit-Trail, E-Mail und die Job-Warteschlange

### GraphQL-Webhooks und Abonnements

Wo immer sich Inhalte ändern (in der Administration, durch eine Mutation, einen Befehl, einen Cronjob, einen Import),
notiert der Kernel das Ereignis (`publish`, `update`, `trash`, `delete`, `move`, `hide`, `reveal`, `mutation`) in
einem Ereignisjournal. Daraus:

- **Webhooks** senden per POST einen signierten JSON-Körper an einen anderen Server, mindestens einmal, mit
  Wiederholungen über Stunden, protokolliert: `./console exp:graphql:webhooks --add=... --apply` oder
  **Setup > GraphQL**.
- **Abonnements** streamen die Ereignisse an einen Client, der den Inhalt lesen darf: Server-Sent Events unter
  `/api/graphql/subscribe` oder ein WebSocket unter Velocity.

Siehe [Echtzeit: Webhooks und Abonnements](funktionen/6.0/graphql.md#echtzeit-webhooks-und-subscriptions).

### Der Audit-Trail und seine Senken

*Vollständige Referenz: [Audit](bc/6.0/audit.md) und [Der Audit-Trail](funktionen/6.0/pruefprotokoll.md)*

Der Audit-Trail zeichnet auf, was in der Installation geschieht, und liefert seine Einträge an **Senken**: Syslog
(RFC 5424 oder journald), E-Mail und einen **signierten Webhook**. Der Webhook ist aus, bis er eine URL hat:

```ini
# settings/override/audit.ini.append.php
[AuditSink_webhook]
URL=https://siem.example.com/exponential
SigningSecret=<ein langer Zufallswert>
Events[]
Events[]=content.object.publish
```

Jeder Stapel trägt `X-Exponential-Signature` (HMAC-SHA-256 des Körpers), `X-Exponential-Timestamp` und
`X-Exponential-Batch`; der Empfänger prüft die Signatur und verwirft eine Stapel-ID, die er schon gesehen hat. Den
Filter der Konsole können Sie außerdem als CSV, JSON Lines oder JSON exportieren (Richtlinie `audit/export`). Wie Sie
ihn empfangen und prüfen:
[Ausgehende Webhooks aus dem Audit-Trail](hilfe/integratoren/ausgehende-webhooks-aus-dem-audit-trail.md).

### Benachrichtigungen und E-Mail

Benachrichtigungen teilen Benutzern per E-Mail mit, wenn sich Inhalte ändern, denen sie folgen. E-Mails gehen über
`site.ini [MailSettings] Transport` hinaus (`sendmail`, `smtp` oder `file`); siehe
[Exponential 6.0 konfigurieren, Abschnitt 5](CONFIGURING.md#5-datenbank-e-mail-ablage-und-sitzungen),
[Benachrichtigungen für Administratoren](anleitungen/benachrichtigungen-administrator.md) und
[E-Mail und Benachrichtigungen integrieren](hilfe/integratoren/integration-von-e-mail-und-benachrichtigungen.md).

### Die Job-Warteschlange

`expqueue` ist eine datenbankgestützte Job-Warteschlange für Hintergrundarbeit: Erzeuger stellen einen Job mit einem
Aufruf ein, und Worker führen ihn auf der Kommandozeile, per Cron oder als systemd-Dienst aus, nie innerhalb einer
Webanfrage. Sie wiederholt mit wachsendem Abstand, hat eine Dead-Letter-Ablage, Idempotenzschlüssel, Prioritäten und
Nebenläufigkeitsgrenzen, die Hinweisen auf Ratenlimits folgen. Sie ist in jeder Installation vorhanden und bleibt
inaktiv, bis sie aktiviert wird; **Setup > Job queue** zeigt die Jobs. Siehe
[expqueue](funktionen/6.0/erweiterungen/expqueue.md) und
[KI-Anbieter, die Job-Warteschlange und MCP](hilfe/integratoren/ki-jobwarteschlange-und-mcp.md).

## 10. Benutzer und Anmeldung: LDAP, Single Sign-on und die Benutzerkonsole

| Bedarf | Einstellung | Seite |
|---|---|---|
| Anmeldung gegen ein Verzeichnis (LDAP, Active Directory) | `site.ini [UserSettings] LoginHandler[]=LDAP`, mit `settings/ldap.ini` | [Anmelde-Handler: LDAP und textfile](hilfe/integratoren/login-handler-ldap-und-textdatei.md) |
| Single Sign-on aus einem anderen System | `site.ini [UserSettings] SingleSignOnHandlerArray[]`, eine eigene Handler-Klasse | [Single Sign-on und Social Login](hilfe/integratoren/single-sign-on-und-social-login.md) |
| Social Login, Anmeldung in zwei Schritten, Passkeys | `sevenx_authentication_2fa` | [Anmeldung in zwei Schritten](anleitungen/zwei-faktor-anmeldung.md), [Passkeys](anleitungen/passkeys.md) |
| Konten aus Skripten anlegen, ändern, deaktivieren, freigeben und prüfen | `exp:user:*` (`create`, `edit`, `disable`, `enable`, `reset-password`, `approve`, `import`, `export`, `bulk`, `audit` und mehr) | [Benutzerverwaltung über die Konsole](funktionen/6.0/benutzerverwaltung-von-der-konsole.md) |

Die Anmelde-Handler werden in der Reihenfolge von `LoginHandler[]` versucht; behalten Sie `standard` in der Liste,
damit sich lokale Konten wie das des Administrators weiter anmelden können, wenn das Verzeichnis nicht erreichbar ist.
SAML und OpenID Connect werden nicht mitgeliefert: Schreiben Sie einen Anmelde- oder Single-Sign-on-Handler, oder
stellen Sie einen identitätsbewussten Proxy davor.

## 11. Frontends: Headless, CORS, Caching und Velocity

- **Headless.** Ein Frontend auf einem anderen Host liest Inhalte über GraphQL oder die REST-API und stellt sie selbst
  dar. Die Inhalte behalten ihre Berechtigungen, Workflows und ihr Audit in Exponential.
- **CORS.** Eine Browserseite auf einem anderen Host darf die APIs nur aufrufen, wenn ihr Ursprung erlaubt ist:
  `rest.ini [CorsSettings] Cors=enabled` mit `AllowedOrigins[]`. In der Vorgabe ist es aus. Native Apps, Server und
  `curl` brauchen es nicht. Siehe [CORS für die REST-API](bc/6.0/cors-fuer-die-rest-api.md).
- **Der HTTP-Cache.** `settings/httpcache.ini` (in der Vorgabe `Enabled=disabled`) hält ganze Seiten je
  Berechtigungskontext vor und liefert sie aus, bevor der Kernel startet, an anonyme wie angemeldete Besucher; der
  Header `X-Exp-Cache` unterscheidet Treffer und Fehlschlag. Siehe [Der rollenbewusste HTTP-Cache](bc/6.0/httpcache.md).
- **Velocity.** Exponential Velocity, der empfohlene Webserver, bringt einen Antwort-Cache, WebSockets, Server-Sent
  Events und Räume für Echtzeit-Frontends mit. Siehe [Velocity-Antwort-Cache](funktionen/6.0/velocity-antwort-cache.md)
  und [Velocity: WebSockets, Server-Sent Events und Räume](funktionen/6.0/velocity-websockets-und-ereignisse.md).
- **Ein statisches Frontend.** Braucht die öffentliche Website gar kein PHP, schreibt das statische Veröffentlichen
  sie als Dateien ([Abschnitt 8](#8-inhalte-hinein-und-hinaus-pakete-migrationen-importe-exporte-und-feeds)).

Ratschläge zur Leistung von Integrationen: [Velocity und Leistung für Integrationen](hilfe/integratoren/velocity-und-leistung-fuer-integrationen.md).

## 12. Suche

Exponential sucht mit seinem eingebauten Index (`site.ini [SearchSettings] SearchEngine=eZSearchEngine`), der beim
Veröffentlichen gefüllt und mit `php bin/php/updatesearchindex.php` neu aufgebaut wird. Für größere Websites,
Facetten und die Wortstammbildung weiterer Sprachen übernimmt ein Solr-Server über die Extension `ezfind`;
`exp:solr` startet, stoppt und prüft ihn ([Befehle zur Serversteuerung](funktionen/6.0/befehle-fuer-webserver-und-solr.md)).
Einen externen Suchdienst füttern oder Suchergebnisse in ein anderes System lesen:
[Suchmaschinen integrieren](hilfe/integratoren/anbindung-einer-suchmaschine.md).

## 13. KI-Dienste

`expai` (Exponential Intelligence) verbindet die Administration mit KI-Anbietern: Redakteure fragen in einfacher
Sprache, und der Assistent schreibt Entwürfe als der angemeldete Redakteur, mit dessen Berechtigungen; er
veröffentlicht nie von selbst. Anbieter, Schlüssel, Budgets und das lokale Modell werden unter Setup > EI: Models und
Setup > EI: Budgets eingestellt; lange Arbeiten laufen über `expqueue`. `expai` kann in seinem Bereich außerdem
lesende Werkzeuge von MCP-Servern verwenden (Richtlinie `expai/mcp`). Der Kernel selbst liefert keinen MCP-Server
mit. Siehe [expai](funktionen/6.0/erweiterungen/expai.md) und
[KI-Anbieter, die Job-Warteschlange und MCP](hilfe/integratoren/ki-jobwarteschlange-und-mcp.md).

## 14. Automatisierung und CI: die Konsole

*Vollständige Referenz: [Exponential Console](bc/6.0/konsole.md)*

Jedes Skript von Exponential läuft über einen Einstiegspunkt, `bin/php/console`, der den Exit-Code des Skripts
unverändert zurückgibt; er ist damit sicher in Shell-Pipelines und CI-Jobs.

- **Maschinenlesbare Ausgabe.** Neuere Befehle geben mit `--format=json` ein versioniertes JSON-Schema aus:
  `exp-user/1` für `exp:user:*`, `exp-migration/1` für `exp:migration:*`. Die Exit-Codes sind je Befehl
  dokumentiert: 0 erledigt, 1 fehlgeschlagen oder abgelehnt und so weiter.
- **Einstellungen aus einer Pipeline.** `exp:ini get|set|where` liest und schreibt dieselben Dateien wie die
  Einstellungsseite ([Einstellungen auf der Kommandozeile ändern](funktionen/6.0/exp-ini-befehl.md)).
- **Deployments.** `exp:velocity deploy` führt die Schritte eines PHP-Deployments in Reihenfolge aus und hält beim
  ersten Fehler an; Migrationen bringen die Inhalts- und Einstellungsänderungen
  ([Abschnitt 8](#8-inhalte-hinein-und-hinaus-pakete-migrationen-importe-exporte-und-feeds)).
- **Eigenes PHP mit geladenem Kernel.** `php bin/php/ezexec.php <script.php>`; siehe
  [CLI-Skripte und ezexec](hilfe/integratoren/cli-skripte-und-ezexec.md).
- **Geplante Arbeit.** Cronjob-Teile führen Importe und Exporte nach Zeitplan aus
  ([Cronjobs für Integrationen](hilfe/integratoren/cronjobs-fuer-integrationen.md)).

## 15. Sicherheit für Integrationen

*Checkliste: [Sicherheitscheckliste für Integrationen](hilfe/integratoren/sicherheits-checkliste-fuer-integrationen.md)*

- **Ein Konto je Integration**, mit der kleinsten Rolle, die funktioniert, und Schlüssel mit den engsten Bereichen und
  einem Enddatum. Widerrufen Sie einen Schlüssel am Tag, an dem die Integration endet.
- **Die Anmeldung in zwei Schritten ist für Menschen am Anmeldeformular.** Die Basic-Anmeldung der REST-API, WebDAV
  und SOAP fragen nur nach dem Passwort; lassen Sie sie aus oder beschränken Sie sie auf Konten, die den zweiten
  Schritt nicht brauchen. Maschinen verwenden API-Schlüssel oder OAuth-Tokens, die abgelehnt werden, sobald ihr
  Benutzer deaktiviert oder gesperrt ist ([Anmeldung in zwei Schritten](anleitungen/zwei-faktor-anmeldung.md)).
- **Geheimnisse gehören in den Geheimnisspeicher**, nie in eine INI-Datei oder ein Repository: **Setup > Secrets**,
  oder ein Name `env:<VARIABLE>` für Container ([Der Geheimnisspeicher](funktionen/6.0/geheimnisspeicher.md)).
- **Ratenlimits.** API-Schlüssel sind pro Minute begrenzt (`RateLimitPerMinute`); GraphQL begrenzt Tiefe und
  Komplexität; Anfrageregeln können jede Adresse erlauben, ablehnen oder umleiten, bevor ihre Ansicht läuft
  ([Anfrageregeln](funktionen/6.0/anfrageregeln.md)).
- **Hinter einem Proxy** tragen Sie ihn in `site.ini [HTTPHeaderSettings] TrustedProxies[]` ein, sonst sieht die
  Installation die Adresse des Proxys statt die des Clients ([Vertrauenswürdige Proxys](bc/6.0/vertrauenswuerdige-proxys.md)).
- **Die Content Security Policy** entscheidet, was eine Seite laden darf; ein eingebettetes Widget oder ein Skript
  von einem anderen Host muss in `site.ini [ContentSecurityPolicy] Directives[]` erlaubt sein
  ([Content Security Policy](funktionen/6.0/content-security-policy.md)).
- **Beobachten.** Der Audit-Trail zeichnet die Verwendung und Ablehnung von Schlüsseln auf; `var/log/security.log`
  sammelt, was wie ein Angriff aussieht ([Anfrage-Logs](funktionen/6.0/anfrageprotokolle.md)), und
  [Integrationen überwachen](hilfe/integratoren/integrationen-ueberwachen.md) nennt, worauf Sie achten sollten.

## 16. Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| `401` von `/api/ezp/...` | Keine Anmeldedaten, oder abgelaufene oder widerrufene. Senden Sie `Authorization: Bearer <Schlüssel oder Token>`; prüfen Sie das Enddatum des Schlüssels unter Mein Konto > API-Zugang. |
| `403` bei einem REST- oder GraphQL-Aufruf, der in der Administration funktioniert | Dem Konto der Integration fehlt die Richtlinie, oder dem Schlüssel der Bereich. Vergleichen Sie seine Rolle mit dem Aufruf; `access.apikey.use.failed` im Audit nennt den Grund. |
| `404` für `/api/...` | `ezprestapi` ist nicht aktiv, oder der Webserver leitet `/api/` nicht an `index_rest.php`. Prüfen Sie `ActiveExtensions[]` und die Umschreiberegeln aus [Exponential 6.0 installieren, Abschnitt 8](INSTALL.md#8-die-website-ausliefern-webserver-https-und-berechtigungen). |
| Ein Browser-Frontend erhält einen CORS-Fehler | `rest.ini [CorsSettings] Cors=enabled` und sein Ursprung in `AllowedOrigins[]` ([CORS für die REST-API](bc/6.0/cors-fuer-die-rest-api.md)). Native Apps und Server brauchen es nie. |
| `/api/graphql` antwortet, GraphQL sei aus | Es ist für diesen Siteaccess nicht eingeschaltet: `./console exp:graphql:status --siteaccess=<sa>`, dann `exp:graphql:enable ... --apply`. |
| Ein Schlüssel wird mit `rate_limited` abgelehnt | Mehr als `RateLimitPerMinute` Anfragen pro Minute. Verlangsamen Sie den Client, oder reihen Sie die Arbeit über `expqueue` ein. |
| Der Webhook-Empfänger lehnt die Stapel des Audits ab | Die Signatur wird über den unveränderten Körper mit demselben `SigningSecret` geprüft; prüfen Sie auch die Uhr, denn der Zeitstempel ist mitsigniert. |
| WebDAV oder SOAP antwortet nicht | Beide sind in der Vorgabe aus (`EnableWebDAV`, `EnableSOAP`), und die Einstiegsskripte sind seit 6.0.15 `index_webdav.php` und `index_soap.php`. |
| Ein LDAP-Benutzer kann sich nicht anmelden, lokale Konten schon | Prüfen Sie `settings/ldap.ini` (Server, Basis-DN, das Attribut mit dem Login) und dass `LDAP` in `LoginHandler[]` steht ([Anmelde-Handler: LDAP und textfile](hilfe/integratoren/login-handler-ldap-und-textdatei.md)). |
| Änderungen eines Imports erscheinen nicht | Die Caches halten noch die alten Seiten; leeren Sie sie nach dem Import ([Caching nach Importen](hilfe/integratoren/caching-nach-importen.md)). |
| Eine geänderte Einstellung wirkt nicht | Der INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`, und unter Velocity ein Neustart ([Exponential 6.0 konfigurieren, Abschnitt 10](CONFIGURING.md#10-velocity-apache-und-php-fpm-wann-eine-änderung-wirkt)). |

Mehr: [FAQ für Integratoren](hilfe/integratoren/faq-fuer-integratoren.md), [Fehlerbehandlung und Protokollierung](hilfe/integratoren/fehlerbehandlung-und-protokollierung.md)
und [Integrationen testen](hilfe/integratoren/integrationen-testen.md).

## Verwandte Seiten

- [Exponential 6.0 installieren](INSTALL.md), [Exponential 6.0 konfigurieren](CONFIGURING.md),
  [Exponential 6.0 aktualisieren](UPDATE.md) und [Exponential 6.0 erweitern](../eng-us/EXTENDING.md).
- [Integratoren: Hier beginnen](hilfe/integratoren/integratoren-hier-beginnen.md) und
  [Der Lesepfad für Integratoren](hilfe/integratoren/lesepfad-fuer-integratoren.md).
- [API- und Protokollstandards](standards/apis-und-protokolle.md).
- [Updates, ohne Integrationen zu brechen](hilfe/integratoren/upgrades-ohne-defekte-integrationen.md).
