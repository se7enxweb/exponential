# Hinweis zum Upgrade: Das Autorisieren eines OAuth-Clients braucht die Richtlinie oauth/authorize

Ab 6.0.15 darf ein angemeldeter Benutzer einem OAuth-Client nur mit der Richtlinie **oauth / authorize** erlauben, in
seinem Namen zu handeln (die Seite `oauth/authorize`). Ein Benutzer ohne sie wird mit `error=access_denied` an den
Client zurückgeschickt (RFC 6749, Abschnitt 4.1.2.1), und es wird weder ein Code noch ein Token ausgestellt.

## Was falsch war

Die View sollte diese Richtlinie schon immer prüfen, aber die Prüfung lautete

```php
if ( !$access[ 'accessWord' ] == 'yes' )
```

PHP wertet `!` vor `==` aus, daher war die Bedingung `( !$access['accessWord'] ) == 'yes'`, die nie wahr ist.
Außerdem definierte das Modul `oauth` keine Richtlinienfunktion, sodass niemandem die Richtlinie hätte gegeben werden
können. Jeder angemeldete Benutzer konnte jeden registrierten Client autorisieren, egal was seine Rollen sagten.

## Was sich ändert

- Das Modul `oauth` hat die Richtlinienfunktion **authorize**, mit einer optionalen Einschränkung **SiteAccess**
  (derselben wie bei `user/login`: Der Benutzer darf Clients nur in den aufgeführten Siteaccesses autorisieren).
- `oauth/authorize` prüft sie mit `expOAuthAuthorizePolicy` (kernel/private/rest/classes/oauth/authorize_policy.php).
  Ohne die Richtlinie geht der Browser an die `redirect_uri` des Clients mit
  `error=access_denied&error_description=The+user+may+not+authorize+clients`. Die `redirect_uri` wurde vorher gegen
  den Endpunkt des Clients geprüft, daher kann diese Weiterleitung nirgendwo anders hinzeigen.
- `rest.ini [OAuthSettings] AuthorizeRequiresPolicy=enabled` ist der Standard. `disabled` stellt das alte Verhalten
  wieder her (jeder angemeldete Benutzer darf Clients autorisieren).
- Neue Installationen geben der Rolle **Editor** *OAuth / Authorize*; die Rolle **Administrator** hat sie über den
  vollen Zugriff. Andere Rollen eines Site-Pakets (Mitglieder, Partner) erhalten sie nicht: Fügen Sie sie hinzu, wo
  Menschen sie brauchen.
- Tokens und Autorisierungscodes, die vor dem Upgrade ausgestellt wurden, bleiben gültig, bis sie ablaufen; ein
  Client, den ein Benutzer früher autorisiert hat, wird nur dann erneut autorisiert, wenn der Benutzer die Richtlinie
  hat.

## Was beim Upgrade zu tun ist

Benutzer, die sich heute bei REST-Clients anmelden, würden nach dem Upgrade abgewiesen, sofern ihre Rollen die
Richtlinie nicht erhalten. Das Skript `bin/php/addoauthauthorizepolicy.php` gibt sie genau diesen Rollen:

```bash
php bin/php/addoauthauthorizepolicy.php --dry-run        # lists what it would grant, changes nothing
php bin/php/addoauthauthorizepolicy.php                  # grants it
php bin/php/addoauthauthorizepolicy.php --role=Editor --role=12 --dry-run   # only the roles named
```

- Jede Rolle mit `user/login` (oder `user/*`) erhält `oauth/authorize` mit denselben Siteaccesses: Ein
  uneingeschränktes `user/login` ergibt eine uneingeschränkte Richtlinie, ein auf einige Siteaccesses eingeschränktes
  `user/login` ergibt ein auf diese Siteaccesses eingeschränktes `oauth/authorize`.
- Rollen, die sie bereits gewähren (`oauth/authorize`, `oauth/*`, alle Module), werden übersprungen, sodass das Skript
  erneut laufen kann.
- Eine Rolle, die nur dem anonymen Benutzer oder dessen eigener Gruppe (Anonymous Users) zugewiesen ist, wird
  übersprungen: Ein anonymer Benutzer kann nie einen Client autorisieren. Eine Rolle, die auch anderen Benutzern oder
  Gruppen zugewiesen ist, erhält die Richtlinie: Auf Websites, die aus den Standardpaketen gebaut sind, ist die Rolle
  Anonymous auch Mitgliedern und Partnern zugewiesen und gibt ihnen `user/login`, daher erhält sie die Richtlinie
  (harmlos für den anonymen Benutzer, der sich vor dem Autorisieren anmelden muss).
- Mit `--role` (eine ID oder ein Name, wiederholbar) werden nur die genannten Rollen berücksichtigt; eine genannte
  Rolle ohne `user/login` erhält eine uneingeschränkte Richtlinie.
- Nach einer Vergabe wird der Rollen-Cache geleert.

Die Vergabe erhält das alte Verhalten für die Menschen, die es hatten. Sollen nur einige Ihrer Benutzer Clients
autorisieren, führen Sie das Skript stattdessen mit `--role` für diese Rollen aus, oder entfernen Sie die Richtlinie
danach unter **Setup > Rollen und Richtlinien** aus den anderen Rollen.

### Das alte Verhalten für kurze Zeit behalten

Können Sie die Richtlinie nicht sofort vergeben, fügen Sie zu `settings/override/rest.ini.append.php` hinzu

```ini
[OAuthSettings]
AuthorizeRequiresPolicy=disabled
```

und leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`). Entfernen Sie die Zeile wieder, sobald die
Rollen die Richtlinie haben: Mit ihr darf jeder angemeldete Benutzer jeden Client autorisieren, und genau das ist der
Fehler, den diese Änderung behebt.

## So prüfen Sie es

- `php vendor/bin/phpunit tests/tests/kernel/private/oauth/` (ohne Datenbank): die Auswertung des Zugriffsergebnisses,
  der Plan für das Upgrade und die Weiterleitung `access_denied` der View.
- Melden Sie sich als Benutzer ohne die Richtlinie an und öffnen Sie
  `/oauth/authorize?client_id=<id>&redirect_uri=<the client's end point>&response_type=code`: Der Browser wird mit
  `error=access_denied` an den Endpunkt geschickt.
