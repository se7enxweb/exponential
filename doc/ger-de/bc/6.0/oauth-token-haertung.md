# OAuth-Tokens: deaktivierte Benutzer, Client-Bindung, Lebensdauer der Refresh-Tokens und Token-Quellen (6.0.15)

Ab 6.0.15 prüft die OAuth-Schicht der REST-API (`/api/...` und der Token-Endpunkt `POST /api/auth/v1/oauth/token`)
den Benutzer eines Tokens, beendet Refresh-Tokens nach einer Lebensdauer und nimmt ein Zugriffstoken nicht mehr aus
der Adresse.

## Was sich ändert

- **Deaktivierte und gesperrte Benutzer.** Ein Zugriffstoken, ein Autorisierungscode und ein Refresh-Token
  funktionieren nur, solange ihr Benutzer aktiviert und nicht durch fehlgeschlagene Anmeldungen gesperrt ist
  (`eZUser::isEnabledAfterFailedLogin()`), die Regel, die persönliche API-Schlüssel und die Anmeldung mit Passwort
  schon anwenden. Bisher behielt ein in der Verwaltung deaktivierter Benutzer mit einem früher ausgestellten Token den
  vollen REST-Zugriff und erhielt mit dem Refresh-Token weiter neue Zugriffstokens. Eine Ressourcenanfrage mit einem
  solchen Token erhält `401`; der Token-Endpunkt antwortet `invalid_grant` mit der Beschreibung, die er für einen Code
  oder ein Refresh-Token eines anderen Clients gibt, sodass die Antwort dem Inhaber des Tokens nicht verrät, dass das
  Konto deaktiviert oder gesperrt ist.
- **Client-Bindung.** Ein Code oder Refresh-Token wird nur von dem Client eingelöst, für den er ausgestellt wurde. Die
  Client-Kennungen werden als Zeichenketten in konstanter Zeit verglichen, und eine leere Kennung passt nie.
- **Lebensdauer der Refresh-Tokens.** Ein Refresh-Token galt bisher für immer. Es gilt jetzt `RefreshTokenTTL`
  (standardmäßig 30 Tage) ab der Erneuerung, die es ausgestellt hat; ein abgelaufenes wird gelöscht und die Anfrage
  mit `invalid_grant` beantwortet, sodass die Anwendung den Benutzer erneut über `oauth/authorize` schickt.
  - `ezprest_token` speichert ein Ablaufdatum je Zeile, das des Zugriffstokens, und keine Ausstellungszeit. Die
    Ausstellungszeit wird als `expirytime - TokenTTL` abgeleitet, es gibt also keine Schemaänderung. Eine Änderung von
    `TokenTTL` verschiebt deshalb die Frist früher ausgestellter Refresh-Tokens um die Differenz.
  - Jede Erneuerung stellt ein neues Refresh-Token aus, die Lebensdauer gleitet also ab der letzten Erneuerung; eine
    feste Grenze ab der ersten Autorisierung bräuchte eine neue Spalte.
  - Eine Zeile ohne Ablaufdatum (`expirytime=0`) wird abgelehnt, solange `RefreshTokenTTL` gesetzt ist.
- **Token-Quellen.** Der Header `Authorization` (`Bearer <token>` oder `OAuth <token>`) wird immer gelesen. Der
  Parameter `oauth_token` eines POST-Formulars (RFC 6750, Abschnitt 2.2) wird gelesen, außer bei
  `AllowTokenInBody=disabled`. Der Query-Parameter `oauth_token` (`?oauth_token=...`, RFC 6750, Abschnitt 2.3) wird
  **nicht mehr gelesen**, außer bei `AllowTokenInQuery=enabled`: Ein Token in der Adresse landet in den Zugriffslogs
  von Webservern und Proxys, im Browserverlauf und in `Referer`-Headern.
- **Fehlende und ungültige Parameter.** Der Token-Endpunkt nennt jeden fehlenden Parameter ("Missing grant_type,
  client_id parameters."). Bisher wurde nur ein fehlender letzter Parameter abgelehnt, und eine Anfrage ohne
  `client_id` suchte mit Warnungen "Undefined array key" nach einem Client `null`. Ein als Liste gesendeter Parameter
  (`client_id[]=...`) wird als `invalid_request` abgelehnt ("Invalid client_id parameter.") statt als Array bis zur
  Datenbankschicht zu gelangen.
- **Diagnose.** Eine Anfrage, deren Token nur in der Adresse steht, während `AllowTokenInQuery` aus ist, schreibt einen
  Hinweis in die Debug-Ausgabe ("An OAuth token in the query string (oauth_token) was ignored ...").
- **Unverändert.** Die REST-API, SOAP, WebDAV und `expservices` sind standardmäßig nicht durch die
  Zwei-Faktor-Anmeldung geschützt; diese Änderung fügt dem Token-Endpunkt und den Ressourcenanfragen keinen zweiten
  Faktor hinzu. Der zweite Faktor wird dort abgefragt, wo sich der Benutzer im Webformular anmeldet, das
  `oauth/authorize` verwendet.

## Einstellungen

`rest.ini [OAuthSettings]`:

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `RefreshTokenTTL` | `2592000` | Lebensdauer eines Refresh-Tokens in Sekunden ab der Erneuerung, die es ausgestellt hat; `0` = kein Ablauf, wie bisher |
| `AllowTokenInQuery` | `disabled` | `enabled` nimmt auch `?oauth_token=` aus der Adresse, wie bisher |
| `AllowTokenInBody` | `enabled` | `oauth_token` aus einem POST-Formular nehmen, wenn es keinen Header `Authorization` gibt |

## Was beim Upgrade zu tun ist

- Ein Client, der das Token in der Adresse sendet (`?oauth_token=...`), erhält jetzt `401`. Stellen Sie ihn auf
  `Authorization: Bearer <token>` um. Wo das nicht sofort geht, setzen Sie für die nötige Zeit
  `AllowTokenInQuery=enabled` in `settings/override/rest.ini.append.php`. Der Beispiel-Client `php_ezrestcall` für die
  Kommandozeile aus dem Paket `ezprestapi` sendet das Token bei einigen Aufrufen in der Adresse und braucht den Header
  ebenfalls.
- Eine Anwendung, die ein Refresh-Token länger als 30 Tage ungenutzt aufbewahrt hat, muss ihren Benutzer erneut über
  `oauth/authorize` schicken. Setzen Sie `RefreshTokenTTL=0`, um das bisherige Verhalten zu behalten.
- Für aktive Benutzer von Anwendungen, die innerhalb der Lebensdauer erneuern, ändert sich nichts.

## Code und Tests

- `ezpOauthUtility` (`kernel/private/rest/classes/oauth/utility.php`): `userMayUseToken()`, `clientMatches()`,
  `refreshTokenExpired()`, `tokenSources()`; `ezpOauthFilter::run()` (`oauth_filter.php`);
  `ezpRestOauthTokenController::missingParams()` und `invalidParams()` (`controllers/oauth_token.php`).
- `tests/tests/kernel/private/oauth/ezpOauthGrantChecksTest.php` und
  `tests/tests/kernel/classes/rest/ezpOauthUtilityTokenTest.php` (ohne Datenbank).
