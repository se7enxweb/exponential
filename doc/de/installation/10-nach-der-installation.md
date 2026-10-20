# 10. Nach der Installation: das Betriebshandbuch

Der Installer hinterlässt Ihnen eine Site, die ihre erste Anfrage beantwortet. Dieses Kapitel macht daraus eine Site,
die Sie betreiben können: das Administratorkonto absichern, die Adressen der Site richtig einstellen, die Cronjobs
einplanen, die veröffentlichen, benachrichtigen und aufräumen, echte Mails versenden, wissen, welcher Cache wann zu
leeren ist, die Autoloads der Klassen und den Suchindex aktuell halten, die Logs lesen und rotieren, Sicherungen
anlegen, die Sie tatsächlich wiederhergestellt haben, die Site überwachen, sie auf Geschwindigkeit trimmen, Extensions
und ihre Reihenfolge verwalten und Sprachen hinzufügen. Jeder Teil erklärt, was der Mechanismus tut, nennt die Befehle
mit den Einstellungen, die sie lesen, und endet mit einer Möglichkeit, das Ergebnis zu prüfen. Arbeiten Sie das
Kapitel nach der Installation einmal der Reihe nach durch; danach dient es als Nachschlagewerk.

[Inhalt](README.md) · Zurück: [9. Datenbanken](09-datenbanken.md) · Weiter: [11. Upgrade](11-aktualisieren.md)

## Inhalt

- [Konventionen in diesem Kapitel](#konventionen-in-diesem-kapitel)
- [10.1 Erste Anmeldung und das Administratorpasswort](#101-erste-anmeldung-und-das-administratorpasswort)
- [10.2 Siteaccesses und Adressen der Site](#102-siteaccesses-und-adressen-der-site)
- [10.3 Cronjobs](#103-cronjobs)
- [10.4 Mail: der Datei-Transport und echter Versand](#104-mail-der-datei-transport-und-echter-versand)
- [10.5 Benachrichtigungen und Newsletter](#105-benachrichtigungen-und-newsletter)
- [10.6 Caches](#106-caches)
- [10.7 Autoloads der Klassen](#107-autoloads-der-klassen)
- [10.8 Suchindex und Bildverarbeitung](#108-suchindex-und-bildverarbeitung)
- [10.9 Sicherung und Wiederherstellung](#109-sicherung-und-wiederherstellung)
- [10.10 Logs und Log-Rotation](#1010-logs-und-log-rotation)
- [10.11 Überwachung und Zustandsprüfungen](#1011-überwachung-und-zustandsprüfungen)
- [10.12 Performance-Tuning](#1012-performance-tuning)
- [10.13 Verwaltung von Extensions](#1013-verwaltung-von-extensions)
- [10.14 Mehrsprachige Sites](#1014-mehrsprachige-sites)
- [10.15 Eine Checkliste für die erste Woche](#1015-eine-checkliste-für-die-erste-woche)
- [10.16 Wenn etwas nicht funktioniert](#1016-wenn-etwas-nicht-funktioniert)
- [Referenzen](#referenzen)

## Konventionen in diesem Kapitel

- Jeder Befehl wird im Installationsverzeichnis ausgeführt, dem Verzeichnis, das `index.php` enthält.
- `php bin/php/console` ist die Befehlskonsole; viele Installationen verlinken sie als `./console`.
  `php bin/php/console list` zeigt jeden Befehl, `php bin/php/console list cron` einen Namensraum.
- Skripte verweigern die Ausführung als `root`, sofern sie nicht `--allow-root-user` erhalten. Führen Sie Routinearbeiten
  und vor allem Cron als eigenen Benutzer der Site aus; dann ist die Option nicht nötig, und Dateien werden mit dem
  richtigen Eigentümer angelegt. Die Beispiele enthalten die Option, damit sie sich unverändert kopieren lassen.
  `bin/php/ezpgenerateautoloads.php` hat keine eigene root-Prüfung; es akzeptiert die Option, damit dieselbe
  Gewohnheit auch dort funktioniert ([10.7](#107-autoloads-der-klassen)).
- Dateien, die `root` anlegt, gehören `root`. Eine Cache- oder Log-Datei, die `root` angelegt hat und die der Benutzer
  des Webservers nicht überschreiben kann, ist die häufigste Ursache für Fehler nach Wartungsarbeiten
  ([Kapitel 12](12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft)). Wenn Sie als `root` arbeiten müssen, führen Sie
  den Befehl stattdessen als Benutzer der Site aus: `sudo -u <site user> php ...`.
- Einstellungen werden nie in `settings/*.ini` bearbeitet. Overrides gehören nach
  `settings/override/<file>.ini.append.php` (die ganze Installation) oder
  `settings/siteaccess/<name>/<file>.ini.append.php` (ein Siteaccess). `php bin/php/console exp:ini` liest und
  schreibt sie in jedem Geltungsbereich ([exp:ini](../../en/us/features/6.0/exp-ini-command.md)); führen Sie nach einer
  Bearbeitung von Hand `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` aus.
- `site` und `admin` stehen für die Namen Ihres öffentlichen Siteaccess und Ihres Administrations-Siteaccess (die
  Voreinstellungen von `exp:install`).

## 10.1 Erste Anmeldung und das Administratorpasswort

1. **Adresse und Passwort finden.** Die Zusammenfassung des Installers hat die Adresse der Admin-Anmeldung (beim
   voreingestellten URL-Zugriff `/admin/user/login` unterhalb der Adresse der Site) und den Benutzer `admin`
   ausgegeben. Ein generiertes Passwort wurde einmal angezeigt und in `var/log/initial-admin-password` geschrieben,
   nur für dessen Eigentümer lesbar.
2. **Melden Sie sich an und ändern Sie das Passwort** sofort: in der Administration über **Passwort ändern** im
   Benutzermenü oder direkt unter `/admin/user/password`. Die Seite listet die Anforderungen neben dem Feld auf:
   mindestens `[UserSettings] MinPasswordLength` Zeichen (ausgeliefert `10`, `settings/site.ini`), kein bekanntes oder
   gängiges Passwort (`WeakPasswordCheck`, seit 6.0.15) und die Regeln aus `[PasswordSettings]`, die Sie eingeschaltet
   haben. Wählen Sie ein langes, zufälliges Passwort.
   Der Benutzer erhält eine E-Mail darüber, dass das Passwort geändert wurde (`ChangeNotificationMail`); das ist
   zugleich der erste Test Ihrer Mail-Einstellungen ([10.4](#104-mail-der-datei-transport-und-echter-versand)).
3. **Entfernen Sie die Passwortdatei**, sobald Sie sich mit dem neuen Passwort angemeldet haben:

   ```bash
   rm var/log/initial-admin-password
   ```

4. **Geben Sie dem Administrator eine echte E-Mail-Adresse** (bearbeiten Sie das Benutzerobjekt des Administrators
   in der Administration), damit das Zurücksetzen des Passworts per Mail jemanden erreicht.

Ein verlorenes Passwort wird auf der Kommandozeile zurückgesetzt. Als `root`-Benutzer des Betriebssystems ist keine
weitere Anmeldung nötig:

```bash
php bin/php/resetuserpassword.php -u admin -g --allow-root-user
```

`-u` nennt den Benutzer (Voreinstellung `admin`); `-g` generiert ein Passwort (`-l <length>`, Voreinstellung 16) und
gibt es aus; `-p <password>` setzt eines; `-a <login>` mit `-ap <password>` autorisiert das Zurücksetzen stattdessen
mit der Anmeldung eines anderen Administrators, für den Fall, dass Sie nicht `root` des Betriebssystems sind. Ein mit
`-p` übergebenes Passwort ist in der Prozessliste und in der Shell-Historie sichtbar; verwenden Sie lieber `-g` und
ändern Sie das Passwort nach der Anmeldung. Siehe
[Ein Benutzerpasswort zurücksetzen](../../en/us/features/6.0/reset-user-password.md) und
[Ihr Passwort ändern](../../en/us/features/6.0/modern-password-change.md). Alle Wege, es zu ändern, die Voreinstellung je
Installationsmethode und wie Sie die Anmeldung `admin` stilllegen:
[Das Administratorpasswort ändern](../../en/us/help/change-the-admin-password.md),
[Das voreingestellte Administratorpasswort](../../en/us/help/default-administrator-password.md).

## 10.2 Siteaccesses und Adressen der Site

Ein **Siteaccess** ist ein Zugang zur Installation: ein Name, ein Satz Einstellungen in
`settings/siteaccess/<name>/` und eine Regel, welche Anfragen er beantwortet. Der Installer legt mindestens den
öffentlichen Siteaccess und die Administration an. Die Regeln stehen in `site.ini [SiteAccessSettings]`:

| Schlüssel | Ausgeliefert | Bedeutung |
|---|---|---|
| `AvailableSiteAccessList[]` | vom Installer gesetzt | Jeder Siteaccess, den die Installation kennt. |
| `MatchOrder` | `uri;host;port` | Die Arten, wie eine Anfrage zugeordnet wird, der Reihe nach versucht: `uri`, `host`, `host_uri`, `port`, `servervar`. |
| `URIMatchType`, `URIMatchElement` | `element`, `1` | Mit `uri`: Das erste Pfadelement nennt den Siteaccess (`/admin/...`). |
| `HostMatchType`, `HostMatchMapItems[]` | `map` | Mit `host`: Ein Hostname wird einem Siteaccess zugeordnet, `HostMatchMapItems[]=www.example.com;site`. |
| `RelatedSiteAccessList[]` | | Siteaccesses, die sich eine Datenbank teilen. |
| `ForceVirtualHost` | `false` | `true`, wenn der Webserver jede Adresse auf `index.php` umschreibt, wie es die ausgelieferten Regeln tun. |
| `RemoveSiteAccessIfDefaultAccess` | `disabled` | `enabled` lässt den Namen des voreingestellten Siteaccess in generierten Links weg. |
| `RequireUserLogin` | `true` in `site.ini` | Wird je Siteaccess überschrieben; eine öffentliche Site setzt es auf `false`. |

In `site.ini [SiteSettings]` jedes Siteaccess:

| Schlüssel | Bedeutung |
|---|---|
| `SiteURL` | Die Adresse der Site ohne Schema (`www.example.com`, oder `www.example.com/admin` bei URL-Zugriff). Wird in Mails, Feeds, Sitemaps und von `exp:benchmark` verwendet. |
| `DefaultAccess` | Der Siteaccess, den eine Anfrage erhält, wenn keine Regel gegriffen hat. |
| `SiteList[]` | Die öffentlichen Siteaccesses, von der Administration für Vorschauen und Links verwendet. |

Der Installer hat eines von drei Schemata gewählt (`exp:install --access=url|host|port`):

- **URL** (`/admin`): ein Hostname für alles; am einfachsten hinter jedem Webserver.
- **Host** (`admin.example.com`): ein Hostname je Siteaccess; am besten für den Produktivbetrieb und für
  HTTPS-Zertifikate je Host.
- **Port** (`:8081`): ein Port je Siteaccess; nützlich für die lokale Arbeit.

Um nach der Installation von URL- auf Host-Zugriff umzustellen, zum Beispiel:

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=admin.example.com;admin
```

und in `settings/siteaccess/site/site.ini.append.php` und `settings/siteaccess/admin/site.ini.append.php`
`[SiteSettings] SiteURL=www.example.com` bzw. `admin.example.com` setzen. Leeren Sie den INI-Cache, tragen Sie die
Hostnamen beim Webserver ([Kapitel 8](08-die-website-ausliefern.md)) und im DNS ein und prüfen Sie, welchen Siteaccess eine
Adresse erreicht. Die Administration verlangt eine Anmeldung, die öffentliche Site nicht, was den Unterschied leicht
erkennbar macht:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' https://admin.example.com/
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
```

Erwarten Sie vom Admin-Host eine Weiterleitung auf `.../user/login` (oder die Anmeldeseite selbst) und vom
öffentlichen Host `200`. Antworten beide gleich, greift die Host-Zuordnung nicht: Der Hostname, den der Browser
sendet, muss genau dem in `HostMatchMapItems[]` entsprechen, ohne Port und in Kleinbuchstaben.

**Hinter einem Proxy oder Load Balancer.** Das Schema (`http` oder `https`) und der Hostname, den der Besucher
angefragt hat, werden nur dann aus weitergeleiteten Headern (`X-Forwarded-Proto`, `X-Forwarded-Host`, ...)
übernommen, wenn die Anfrage von einer Adresse in `site.ini [HTTPHeaderSettings] TrustedProxies[]` kommt
(ausgeliefert: `127.0.0.1` und `::1`, ein Proxy auf demselben Rechner); die Adresse des Besuchers wird nur dann aus
`X-Forwarded-For` gelesen, wenn zusätzlich `ClientIpByCustomHTTPHeader=X-Forwarded-For` gesetzt ist. Jeder kann diese
Header senden, deshalb wird ihnen nur von den Proxys geglaubt, die Sie auflisten. Ein Proxy auf einem anderen Rechner
muss hinzugefügt werden, sonst behandelt die Site HTTPS-Anfragen als einfaches HTTP und verzeichnet die Adresse des
Proxys als die jedes Besuchers:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
TrustedProxies[]=10.0.0.5
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

Die leere Zeile `TrustedProxies[]` ersetzt die ausgelieferte Liste; wiederholen Sie daher die Loopback-Einträge, wenn
Sie sie weiterhin brauchen. Siehe [Kapitel 8](08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy) und
[Vertrauenswürdige Proxys](../../en/us/bc/6.0/trusted-proxies.md).

Einstellungen je Site innerhalb einer Extension, um mehrere Sites aus einer Installation zu betreiben, werden unter
[Einstellungen je Site innerhalb von Extensions](../../en/us/features/6.0/multi-site-ini-overrides.md) erklärt.

## 10.3 Cronjobs

Cronjobs erledigen die Arbeit, auf die kein Besucher wartet: Sie veröffentlichen und verbergen Inhalte nach Zeitplan,
versenden Benachrichtigungen, setzen Inhaltsjobs fort, führen die Wartung des Audit-Protokolls aus, importieren Feeds,
indizieren Inhalte und räumen auf. Ohne sie funktioniert eine Site zwar, aber geplante Inhalte erscheinen nie,
Benachrichtigungen gehen nie hinaus, und Caches und Entwürfe stapeln sich auf der Festplatte.

### Wie runcronjobs.php funktioniert

`runcronjobs.php` führt **Teile** aus: benannte Listen von Skripten in `settings/cronjob.ini`. Extensions fügen über
`[CronjobSettings] ExtensionDirectories[]` eigene Teile und Verzeichnisse hinzu. Ohne Namen eines Teils aufgerufen,
führt es die Skripte aus `[CronjobSettings] Scripts[]` aus, den Standardteil.

```bash
php runcronjobs.php --list --allow-root-user                       # every part and its scripts
php runcronjobs.php -s site frequent --allow-root-user              # one part
php runcronjobs.php -s site --script=notification.php --allow-root-user   # one script
php runcronjobs.php -s site --allow-root-user                       # the default part
```

`-s` (`--siteaccess`) wählt den Siteaccess, dessen Einstellungen die Skripte lesen; verwenden Sie den öffentlichen
Siteaccess, sofern ein Teil nichts anderes verlangt (die Oracle-Teile wollen die Administration). `-q` unterdrückt die
Ausgabe außer Fehlern. In `--list` erscheint jedes Skript einmal je durchsuchtem Verzeichnis; diese Wiederholung ist
normal. Jeder Teil hat außerdem einen Konsolen-Alias, `php bin/php/console cron:<part>` (zum Beispiel
`cron:frequent`), und die Administration führt jeden Teil unter **Setup > Cronjobs** aus; dort lässt sich die Ausgabe
während des Laufs verfolgen, und die Seite schlägt Crontab-Zeilen vor. Teile, die in `[AdminSettings] ForbiddenParts[]`
aufgeführt sind (ausgeliefert: `cluster_maintenance`, `unlock`), lassen sich dort nicht starten.
`MaxScriptExecutionTime` (ausgeliefert `43200` Sekunden) begrenzt die Laufzeit eines Skripts.

### Die Teile und ihre Skripte

Die Teile des Kernels (`settings/cronjob.ini`):

| Teil | Skript | Was es tut |
|---|---|---|
| default | `unpublish.php` | Führt geplante Aktionen zum Zurückziehen der Veröffentlichung aus. |
| default | `rssimport.php` | Holt die konfigurierten RSS-Importe in den Inhaltsbaum. |
| default | `indexcontent.php` | Indiziert Inhalte, deren Indizierung verzögert wurde (`[SearchSettings] DelayedIndexing`). |
| default | `hide.php` | Führt geplante Aktionen zum Verbergen und Wiederanzeigen aus. |
| default | `internal_drafts_cleanup.php` | Entfernt interne Entwürfe, die älter als das konfigurierte Alter sind. |
| `frequent` | `notification.php` | Verarbeitet ausstehende Benachrichtigungsereignisse und versendet die Nachrichten und Sammelmails. |
| `frequent` | `workflow.php` | Führt ausstehende Workflow-Ereignisse aus und bringt hängende Workflows voran. |
| `frequent` | `contentjobs.php` | Setzt Inhaltsjobs fort, deren Worker abgestürzt ist, und startet wartende (umfangreiches Entfernen und Kopieren). |
| `frequent` | `audit.php` | Das Audit-Protokoll: Sink-Spools, Alarmregeln, Rotation nach Tag, Archive, Aufbewahrung, Prüfpunkte. |
| `frequent` | `mailbounces.php` | Liest das Bounce-Postfach: Harte Bounces und Beschwerden kommen auf die Sperrliste. |
| `infrequent` | `basket_cleanup.php` | Entfernt verlassene Warenkörbe des Shops. |
| `infrequent` | `linkcheck.php` | Prüft die internen und externen Links in veröffentlichten Inhalten. |
| `infrequent` | `mailpreferences.php` | Aufbewahrung der E-Mail-Präferenzen: Einwilligungsprotokoll, abgelaufene Bestätigungen, alte Einträge der Sperrliste. |
| `cache_cleanup` | `cachecleanup.php`, `httpcache_cleanup.php` | Entfernt View-Cache- und Cache-Block-Dateien, die nie wieder ausgeliefert werden können, sowie abgelaufene Einträge des HTTP-Caches (`[CacheCleanupSettings] MaxAge`, ausgeliefert zwei Tage). |
| `cleanuprss` | `cleanuprss.php` | Kürzt per RSS importierte Inhalte auf die neuesten Einträge jedes Feeds; tut nichts, solange `content.ini [RSSImportCleanupSettings]` keine Klassen nennt. |
| `cluster_maintenance` | `clusterpurge.php` | Löscht abgelaufene Dateien eines Datenbank-Cluster-Backends endgültig. |
| `unlock` | `unlock.php` | Gibt Inhaltsobjekte frei, die in einem gesperrten Bearbeitungszustand hängen. |

Einzelne Teile gibt es für `contentjobs`, `audit`, `notification`, `mailpreferences` und `mailbounces`, sodass jeder
nach eigenem Zeitplan laufen kann. `cronjobs/` enthält weitere Skripte, die kein ausgelieferter Teil aufführt; sie
werden mit `--script=` ausgeführt oder einem eigenen Teil hinzugefügt: `session_gc.php` (abgelaufene Sitzungen, siehe
unten), `old_drafts_cleanup.php`, `trashpurge.php` (leert den Papierkorb, oder mit
`content.ini [TrashSettings] KeepItemsForDays` nur das, was älter ist), `staticcache_cleanup.php`,
`subtreeexpirycleanup.php` und `updateviewcount.php`.

Aktive Extensions fügen weitere Teile hinzu; `php bin/php/console list cron` zeigt sie mit einer Beschreibung, zum
Beispiel die Newsletter-Teile ([10.5](#105-benachrichtigungen-und-newsletter)), die Sitemap-Teile von `bcgooglesitemaps`
und `xrowmetadata` sowie die Oracle-Teile ([Kapitel 9](09-datenbanken.md#wartung)).

### Eine Crontab

Installieren Sie sie in der Crontab des Benutzers der Site (`crontab -e -u <site user>` oder
`php bin/php/console crontab:edit`; `crontab:list` zeigt die aktuelle):

```
# m   h  dom mon dow  command
*/5   *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
*/15  *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site >> var/log/cron-default.log 2>&1
17    *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site infrequent >> var/log/cron-infrequent.log 2>&1
40    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cache_cleanup >> var/log/cron-cleanup.log 2>&1
50    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site --script=session_gc.php >> var/log/cron-cleanup.log 2>&1
```

- Verwenden Sie dasselbe PHP-Binary, das der Webserver verwendet (`/opt/.../php` auf Hosts mit mehreren
  PHP-Versionen); `php -v` in der Cron-Umgebung kann von dem Ihrer Shell abweichen.
- `frequent` alle paar Minuten ist das, was Benachrichtigungen und Inhaltsjobs erwarten; `infrequent` stündlich oder
  täglich.
- `session_gc.php` entfernt Sitzungen, die älter als `site.ini [Session] SessionTimeout` sind (ausgeliefert
  `259200` s, drei Tage), wo die Sitzungsbereinigung von PHP durch das Betriebssystem nicht läuft. Dasselbe von Hand:
  `php bin/php/ezsessiongc.php --allow-root-user`.
- Rotieren Sie die Dateien `cron-*.log` mit logrotate des Systems ([10.10](#1010-logs-und-log-rotation)).

Prüfen Sie, dass die Jobs laufen: Die Dateien wachsen, **Setup > Cronjobs** zeigt die letzte Ausgabe, und der
Statusbefehl der Benachrichtigungen meldet den letzten Lauf ([10.5](#105-benachrichtigungen-und-newsletter)).

Velocity hat einen eigenen [Scheduler](../../en/us/features/6.0/velocity-scheduler.md), der Handler-Funktionen zu bestimmten
Zeiten oder in Intervallen ausführt, solange der Server läuft. Er ersetzt den System-Cron für diese Teile nicht von
Haus aus: Er braucht einen Handler, den Sie schreiben, und er holt keine Zeitpunkte nach, die verpasst wurden, während
der Server nicht lief.

## 10.4 Mail: der Datei-Transport und echter Versand

Exponential versendet Mails bei Passwortänderungen, Registrierungen, Formularen, Benachrichtigungen und Newslettern.
Der Transport des Kernels wird in `site.ini [MailSettings]` festgelegt:

| Schlüssel | Ausgeliefert | Bedeutung |
|---|---|---|
| `Transport` | `sendmail` | `sendmail`, `SMTP` oder `file` (`TransportAlias[]` ordnet sie `eZSendmailTransport`, `eZSMTPTransport`, `eZFileTransport` zu). |
| `TransportServer`, `TransportPort` | leer, `25` | Der SMTP-Server. Gesichertes SMTP läuft meist über 465 (`ssl`) oder 587 (`tls`). |
| `TransportConnectionType` | leer | leer (keine Verschlüsselung), `ssl` oder `tls`. |
| `TransportUser`, `TransportPassword` | leer | SMTP-Anmeldung. |
| `SenderHost` | `localhost` | Der Name, der im SMTP-`EHLO` angegeben wird. |
| `AdminEmail` | ein Platzhalter | Die Adresse des Administrators; der Absender, wenn nichts anderes gesetzt ist. **Ändern Sie sie.** |
| `EmailSender`, `EmailReplyTo` | leer | `From` und `Reply-To`, sofern ein Template sie nicht setzt. |
| `UserAgent` | leer | Der Header `User-Agent` jeder Mail (sendmail, SMTP und file), z. B. der Name des Portals; nur ASCII. Leer: `Exponential, Version <version>` (sendmail, file) und `Apache Zeta Components` (SMTP). `eZMail::setUserAgent()` setzt ihn weiterhin für eine einzelne Mail, auch über SMTP. |
| `ContentType`, `OutputCharset` | `text/plain`, `utf-8` | |
| `SendmailOptions[]` | leer | Zusätzliche Optionen für sendmail, eine pro Zeile, z. B. `-f` mit dem Envelope-Absender. |
| `SendmailEnvelopeSender` | `enabled` | `disabled` lässt das `-f <sender>` weg, das der sendmail-Transport hinzufügt, für msmtp mit `--read-envelope-from`; eine Mail ohne eigenen Absender erhält dann einen From-Header mit `EmailSender`, andernfalls `AdminEmail`. |
| `DebugSending`, `DebugReceiverEmail` | `disabled` | `enabled` sendet jede Mail an eine Testadresse statt an ihre Empfänger. |
| `ExcludeHeaders[]` | leer | Header, die vor dem Versand über SMTP entfernt werden (fügen Sie `bcc` hinzu, wenn Ihr Server ihn nicht ausblendet). |

**Der Datei-Transport** schreibt jede Mail als Datei, statt sie zu versenden: `Transport=file` legt sie in
`var/log/mail/` ab (oder im Verzeichnis aus `[MailSettings] FileTransportDirectory`), eine Datei
`<time>-<random>.mail` je Nachricht. Verwenden Sie ihn auf einer Test- oder Staging-Site, um genau zu sehen, was
hinausgehen würde, ohne dass jemand es erhält:

```bash
ls -lt var/log/mail/ | head
```

**Echter Versand.** Verwenden Sie im Produktivbetrieb SMTP zum Mailserver Ihrer Organisation oder zu einem
Relay-Dienst:

```ini
# settings/override/site.ini.append.php
[MailSettings]
Transport=SMTP
TransportServer=smtp.example.com
TransportPort=587
TransportConnectionType=tls
TransportUser=<smtp user>
TransportPassword=<smtp password>
SenderHost=www.example.com
AdminEmail=webmaster@example.com
EmailSender=noreply@example.com
```

Mails, die echte Postfächer erreichen sollen, brauchen eine Absenderadresse einer Domain, die Sie kontrollieren, mit
SPF-Einträgen (und vorzugsweise DKIM und DMARC), die den sendenden Server zulassen, und zwar für jede Adressfamilie,
über die der Server sendet: Sendet der Server über IPv6 und listet der SPF-Eintrag nur IPv4-Adressen, weisen große
Anbieter die Mail zurück. Senden Sie eine Testmail, indem Sie das Passwort eines Testbenutzers ändern oder ein
Formular mit einem E-Mail-Empfänger absenden, und lesen Sie das Log des Mailservers.

Einige Extensions haben eigene Transport-Einstellungen. Die der Newsletter-Extension sind in
[10.5](#105-benachrichtigungen-und-newsletter) beschrieben; das System der E-Mail-Präferenzen (Kategorien, Einwilligung,
Sperrliste, das Bounce-Postfach) unter
[E-Mail-Präferenzen: Einrichtung und Betrieb](../anleitungen/mail-einstellungen-administrator.md).
`php bin/php/console exp:mailstatus` zeigt den Zustand des Präferenzsystems.

## 10.5 Benachrichtigungen und Newsletter

### Benachrichtigungen

Benutzer abonnieren Teilbäume; das Veröffentlichen erzeugt **Ereignisse**; das Skript `notification.php` (in
`frequent` und als Teil `notification`) macht aus Ereignissen Mails und Sammelmails. Die Einstellungen stehen in
`settings/notification.ini`.

```bash
php runcronjobs.php -s site notification --allow-root-user          # run it once by hand
php bin/php/console exp:notificationstatus                          # pending events, last runs, problems
php bin/php/console exp:notificationrun --dry-run                   # who would get what, nothing sent
```

Die Statusseite in der Administration und der Statusbefehl melden die üblichen Fehler: Ereignisse, die warten, ohne
dass ein Lauf verzeichnet ist (Cron ist nicht eingerichtet), ein fehlgeschlagener Lauf, eine überfällige Sammelmail,
der noch aktive Datei-Transport, ein fehlender Absender. Das vollständige Vorgehen mit jedem Fehler und seiner Behebung
steht unter [Benachrichtigungen: Betrieb und Fehlerbehebung](../anleitungen/benachrichtigungen-administrator.md).

### Newsletter (cjw_newsletter)

Wenn die Newsletter-Extension `cjw_newsletter` aktiv ist, ist der Versand ein zweistufiger Job, den Cron ausführt:

| Teil | Skripte | Was es tut |
|---|---|---|
| `cjw_newsletter` | `cjw_newsletter_mailqueue_create.php`, `cjw_newsletter_mailqueue_process.php` | Prüft ausstehende Abonnenten, baut die Mail-Warteschlange der fälligen Ausgaben auf und versendet sie. |
| `cjw_newsletter_mailqueue_create` | `cjw_newsletter_mailqueue_create.php` | Baut nur die Warteschlange auf. |
| `cjw_newsletter_mailqueue_process` | `cjw_newsletter_mailqueue_process.php` | Versendet nur, was in der Warteschlange wartet. |
| `cjw_newsletter_mailbox` | `cjw_newsletter_mailbox.php` | Holt die Mails der Bounce-Konten ab und wertet sie aus (nicht Teil von `cjw_newsletter`). |

```
*/10  *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cjw_newsletter >> var/log/cron-newsletter.log 2>&1
25    *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cjw_newsletter_mailbox >> var/log/cron-newsletter.log 2>&1
```

Die Extension hat **eigene Transporte**, in `cjw_newsletter.ini [NewsletterMailSettings]` (Override in
`settings/override/cjw_newsletter.ini.append.php`):

| Schlüssel | Ausgeliefert | Verwendet für |
|---|---|---|
| `TransportMethodCronjob` | `file` | die Newsletter-Ausgaben, die der Cronjob versendet |
| `TransportMethodPreview` | `sendmail` | Test-Newsletter aus dem Editor |
| `TransportMethodDirectly` | `file` | Bestätigungen von Abonnements und Info-Mails |
| `FileTransportMailDir` | `var/log/mail` | wohin der Datei-Transport schreibt, eine `.eml`-Datei je Mail |
| `SmtpTransportServer`, `SmtpTransportPort`, `SmtpTransportUser`, `SmtpTransportPassword`, `SmtpTransportConnectionType` | | die SMTP-Verbindung, wenn eine Methode `smtp` ist |

Eine frische Installation **simuliert** also den Newsletter-Versand: Die Ausgaben und Bestätigungen landen als Dateien
in `var/log/mail/`, sodass Sie Abonnement, Double-Opt-in, Versand und Abmeldung ohne Mailserver und ohne echte
Empfänger testen können. Um wirklich zu versenden, setzen Sie die Methoden auf `smtp` (mit den Schlüsseln
`SmtpTransport*`) oder `sendmail`. Das Verzeichnis muss für den Benutzer des Webservers und für den Cron-Benutzer
beschreibbar sein. Zustellbarkeit, Drosselung und Bounce-Behandlung sind bei der Extension und in
[der Spezifikation von cjw_newsletter](../../en/us/specifications/6.0/cjw_newsletter.md) dokumentiert.

## 10.6 Caches

Exponential cacht auf vielen Ebenen, und die meisten Fragen der Art „meine Änderung erscheint nicht“ sind
Cache-Fragen. Die Regel lautet: **Leeren Sie den kleinsten Cache, der das Problem erklärt**, und leeren Sie Caches vor
PHP erst, nachdem PHP den neuen Code ausführt.

### Die Caches nach Id und Tag

`bin/php/ezcache.php` leert nach **Id** (ein Cache) oder **Tag** (jeder Cache, der ihn trägt). Die folgende Liste ist
das, was `--list-ids --verbose` auf einer Standardinstallation ausgibt, mit den Tags aus `kernel/classes/ezcache.php`
und den `[Cache_*]`-Blöcken der Extensions:

| Id | Was er enthält | Tags | Leeren, wenn |
|---|---|---|---|
| `content` | der View-Cache der Inhalte (der Hauptbereich gerenderter Seiten) | content | Inhalte veraltet aussehen; geschieht beim Veröffentlichen automatisch |
| `exphttpcache` | der rollenbewusste HTTP-Cache (wenn `httpcache.ini` ihn einschaltet) | content, template | nach einer Template-Änderung, sofern er eingeschaltet ist |
| `querycache` | Ergebnisse von SQL-Abfragen (wenn `querycache.ini` ihn einschaltet) | content, ini | Daten außerhalb von Exponential geändert wurden |
| `global_ini` | der globale INI-Cache | ini | eine Einstellung sich geändert hat |
| `ini` | der INI-Cache | ini | eine Einstellung sich geändert hat |
| `codepage` | Zuordnungen von Zeichensätzen | codepage | selten |
| `classid` | Nachschlagen von Klassenbezeichnern | content | ein Klassenbezeichner sich geändert hat |
| `sortkey` | Nachschlagen von Sortierschlüsseln | content | |
| `urlalias` | Wildcard-Cache der URL-Aliase | content | URL-Aliase sich falsch verhalten |
| `chartrans` | Tabellen für Zeichentransformationen (URL-Aliase) | i18n | Transformationsregeln sich geändert haben |
| `imagealias` | erzeugte Bildvarianten | image | eine Alias-Definition in `image.ini` sich geändert hat |
| `template` | kompilierte Templates | template | eine `.tpl` sich geändert hat, während das Cachen der Kompilate eingeschaltet ist |
| `template-block` | Ausgabe von `{cache-block}` | template, content | Menüs oder Blöcke veraltet aussehen; ein Stylesheet sich geändert hat (der gepackte Link ist in einem solchen Block gecacht) |
| `template-override` | die Override-Regeln aus `override.ini` | template | eine neue Template-Datei oder Override-Regel (sonst: eine Seite mit 200 und leerem Inhaltsbereich) |
| `texttoimage` | Ausgabe von Text-zu-Bild | template | |
| `rss_cache` | erzeugte Feeds | content | |
| `user_info_cache` | Benutzer- und Rolleninformationen | user | Rollen oder Richtlinien sich geändert haben und ein Benutzer es nicht sieht |
| `content_tree_menu` | das Baummenü der Administration im Browser | content | |
| `state_limitations` | Einschränkungen nach Objektzustand | content | |
| `content_language` | die Liste der Sprachen | content | eine Sprache hinzugefügt wurde |
| `design_base` | die Liste der Design-Verzeichnisse | template | ein Design oder eine Extension hinzugefügt wurde |
| `active_extensions` | die Liste der aktiven Extensions | ini | `ActiveExtensions` sich geändert hat |
| `translation` | kompilierte `.ts`-Übersetzungen | i18n | eine Übersetzungsdatei sich geändert hat |
| `sslzones` | Einstellungen der SSL-Zonen | ini | |
| `rest`, `rest-routes` | Caches der REST-Anwendung und der Routen | content, rest / rest | die REST-Konfiguration sich geändert hat |
| `ezjscore-packer` | gepackte JavaScript- und CSS-Dateien | content, template | ein Skript oder Stylesheet des Packers sich geändert hat |

Die Tags sind also `content`, `template`, `ini`, `codepage`, `i18n`, `image`, `user` und `rest`.

```bash
php bin/php/ezcache.php --list-ids --verbose --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override,design_base --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
# remove the files for real (saves disk space), only those older than two days:
php bin/php/ezcache.php --clear-id=content --purge --expiry="-2 days" --allow-root-user
```

Ohne `--purge` verschiebt ein Leeren nur einen Ablaufzeitstempel (oder schiebt ein Verzeichnis beiseite) und ist
daher sofort erledigt; der Cron-Teil `cache_cleanup` entfernt, was nie wieder ausgeliefert werden kann.
`-s <siteaccess>` leert den Cache im var-Verzeichnis eines anderen Siteaccess.

### Welcher Cache zu leeren ist

| Sie haben geändert | Leeren |
|---|---|
| eine `.ini`-Einstellung | `--clear-tag=ini` (nicht `--clear-id=ini`, das den globalen INI-Cache auslässt) |
| eine `.tpl`-Datei | `--clear-tag=template`: Kompilierte Templates werden nicht mit ihrer Quelle verglichen, außer bei `[TemplateSettings] DevelopmentMode=enabled`, das Live-Sites ausgeschaltet lassen sollten |
| eine neue `.tpl`-Datei, eine Override-Regel, eine Extension oder ein Design | `--clear-id=template-override,design_base` |
| ein Stylesheet oder Skript | `--clear-id=ezjscore-packer,template-block`, dann den Velocity-Cache |
| Inhalte sehen veraltet aus | `--clear-tag=content` |
| Rollen und Richtlinien | `--clear-tag=user` |
| eine Bild-Alias-Definition | `--clear-id=imagealias` |
| eine Übersetzung | `--clear-tag=i18n` |
| unsicher | `--clear-all` (kostet danach einen langsameren ersten Aufruf jeder Seite) |

### Die Caches vor PHP

`php bin/php/cache.php` (Konsole: `exp:cache`) führt jede Aktion von **Setup > Cache** auf der Kommandozeile aus,
endet mit PASS oder FAIL, akzeptiert `--dry-run` und `--json` und erreicht auch die Caches, die `ezcache.php` nicht
erreicht:

```bash
php bin/php/cache.php list --sizes --allow-root-user     # every cache, its tag, size and how it is cleared
php bin/php/cache.php status --allow-root-user           # static, HTTP, query, Velocity, precompress, PHP caches
php bin/php/cache.php velocity clear --allow-root-user   # Velocity's response cache
php bin/php/cache.php httpcache status --allow-root-user
php bin/php/cache.php opcache status --allow-root-user
```

**Der Antwort-Cache von Velocity** beantwortet wiederholte anonyme Anfragen im Elternprozess des Servers, ohne PHP.
Leeren Sie ihn nach einer Änderung an Templates oder Stylesheets (kein Neustart nötig):

```bash
php bin/php/console exp:velocity cache clear --allow-root-user
php bin/php/console exp:velocity cache stats --allow-root-user
```

### Die Reihenfolge nach einer Code-Änderung

Eine Seite, die zwischen dem Leeren eines Caches und dem Neuladen von PHP noch vom alten Code gerendert wird, wird
erneut gecacht und sieht so aus, als hätte die Änderung nicht gewirkt. Die Reihenfolge lautet: INI- und
Template-Caches, dann PHP-FPM neu laden und Velocity neu starten, und **erst dann** die Inhalts-, HTTP-,
Template-Block- und Antwort-Caches. Unter Velocity erledigt das ein einziger Befehl, mit PASS oder FAIL je Schritt und
einem Abbruch beim ersten Fehler:

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do
php bin/php/console exp:velocity deploy --allow-root-user             # do it
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

`--packer` leert zusätzlich die gepackten Skripte und Styles; `--no-fpm`, `--no-velocity` und `--no-autoload`
überspringen Schritte. Der PHP-FPM-Dienst, den der Befehl neu lädt, ist `velocity.ini [DeploySettings] PhpFpmService`
(`auto` findet ihn). Siehe
[Velocity-Engines: eine PHP-Änderung bereitstellen](../../en/us/bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).

## 10.7 Autoloads der Klassen

Exponential findet Klassen über generierte Autoload-Arrays in `autoload/` (der Kernel) und `var/autoload/` (die
Extensions). Eine Klasse, die in keinem Array steht, wird nicht gefunden, selbst wenn ihre Datei vorhanden ist:
„Class ... not found“ direkt nach dem Hinzufügen einer Extension oder einer Klasse ist das klassische Symptom.

```bash
php bin/php/ezpgenerateautoloads.php -e        # extensions: after adding, renaming or removing a class
php bin/php/ezpgenerateautoloads.php -k        # the kernel: only when kernel/ or lib/ changed
php bin/php/ezpgenerateautoloads.php -o        # kernel overrides
php bin/php/ezpgenerateautoloads.php -e -n     # dry run: report, write nothing
php bin/php/ezpgenerateautoloads.php -e -p     # with progress output
```

Das Skript hat keine root-Prüfung; es akzeptiert `--allow-root-user` (ohne Wirkung), damit die Option wie bei jedem
anderen Skript angegeben werden kann. Eine Option, die es nicht kennt, beendet es mit Exit-Status 1, ohne etwas zu
schreiben:

```text
$ php bin/php/ezpgenerateautoloads.php -e --bogus
The referenced parameter '--bogus' is not registered.
```

Ältere Releases gaben bei einer unbekannten Option ihre Hilfe aus und endeten mit 0, `--allow-root-user`
eingeschlossen, sodass ein Deployment-Skript nie bemerkte, dass nichts generiert worden war. Wenn eines Ihrer
Deployment-Skripte den Generator aufgerufen und nichts geprüft hat, prüfen Sie es jetzt einmal von Hand. Führen Sie ihn
als Benutzer der Site aus: Die Arrays, die er in `var/autoload/` schreibt, müssen für den nächsten Lauf beschreibbar
bleiben.

Welche Verzeichnisse er durchläuft, begrenzt **`.autoloadignore`** im Installationsverzeichnis, ein Verzeichnis pro
Zeile, verankert am Installationsverzeichnis, `#` für Kommentare. Die ausgelieferte Datei schließt `vendor` aus
(Composer löst diese Klassen selbst auf), außerdem `ai`, `var` und `.claude` (Arbeitskopien, die sonst jede
Kernel-Klasse auf eine Kopie abbilden würden). Tragen Sie dort ein Verzeichnis ein, wenn es PHP-Dateien enthält, die
keine Anwendungsklassen sind; `--exclude` tut dasselbe für einen einzelnen Lauf.

Leeren Sie nach dem Neugenerieren den INI-Cache und starten Sie unter Velocity die Worker neu: Sie behalten die Arrays,
die sie beim Start geladen haben. `exp:velocity deploy` (mit `--kernel` für das Kernel-Array) erledigt alle drei
Schritte. `php bin/php/console exp:checkclasses` lädt jede deklarierte Klasse und meldet die, deren Laden PHP
verweigert.

## 10.8 Suchindex und Bildverarbeitung

### Suche

Die eingebaute Engine ist `eZSearchEngine` (`site.ini [SearchSettings] SearchEngine`); sie hält ihren Index in der
Datenbank. Inhalte werden beim Veröffentlichen indiziert, es sei denn, `DelayedIndexing` ist `enabled` (oder
`classbased` mit `DelayedIndexingClassList[]`): Dann stellt das Veröffentlichen sie nur in eine Warteschlange, und
`indexcontent.php` aus dem Standardteil von Cron indiziert sie später, was das Veröffentlichen großer Objekte
beschleunigt.

Die Warteschlange ist die Tabelle `ezpending_actions`: `index_object` indiziert das Objekt neu, `index_moved_node` das
Objekt und jedes Objekt unterhalb seines Hauptknotens (für eine Suchmaschine, die Pfade speichert, wie den Solr von
ezfind). Jede Zeile trägt den Zeitpunkt, zu dem sie eingereiht wurde (`created`); der Cronjob nimmt die älteste zuerst
und entfernt eine Zeile, sobald sie indiziert ist. Was ein Vorgang bei verzögerter Indizierung einreiht:

| Vorgang | `eZSearchEngine` (eingebaut) | Eine Engine mit `addNodeAssignment()` (ezfind) |
|---|---|---|
| Ein neues Objekt oder eine neue Version veröffentlichen (mit oder ohne Benachrichtigung, beliebig viele Orte) | ein `index_object` | ein `index_object` |
| Eine Version veröffentlichen, die einen Ort verschiebt (ein Elternknoten im Bearbeitungsformular geändert) | ein `index_object` | ein `index_moved_node` |
| Verschieben (content/move) | nichts: Der Index enthält keine Pfade | `index_moved_node` und `index_object` |
| Einen Ort hinzufügen, tauschen | nichts | `index_object` |
| Verbergen, wieder anzeigen | nichts | ezfind indiziert sofort und reiht eine eigene Aktion für den Teilbaum ein |
| In den Papierkorb verschieben, löschen | entfernt die eingereihten Zeilen `index_object` und `index_moved_node` des Objekts | dasselbe |

Bauen Sie den ganzen Index nach einem Import, einem Datenbankumzug oder bei falschen Suchergebnissen neu auf:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user      # --clean removes the old index first
```

Suchstatistiken (`LogSearchStats`) sind standardmäßig ausgeschaltet, weil sie jeden Suchbegriff speichern, den
Besucher eingeben, was personenbezogene Daten sein können. Einen Solr-Server steuert
`php bin/php/console exp:solr`, sobald er installiert und konfiguriert ist.

### Bilder

Hochgeladene Bilder liegen in `var/<site var dir>/storage/images`; ihre Varianten (**Aliase**) werden beim ersten
Gebrauch erzeugt. Die Regeln stehen in `settings/image.ini`:

- `[AliasSettings] AliasList[]` nennt die Aliase (`reference`, `small`, `tiny`, `medium`, `large`, `rss` und die
  Ihres Designs), jeden mit einem Block von Filtern.
- `[ImageConverterSettings] ImageConverters[]` listet die Konverter der Reihe nach: **GD** zuerst (die eigene
  Erweiterung von PHP, kein externes Programm je Bild), **ImageMagick** als Rückfall für Formate, die GD nicht lesen
  kann (PSD, TIFF, PDF, WebP), und für Filter, die nur es hat. `[ImageMagick] ExecutablePath` und `Executable`
  (`convert`) finden das Programm; prüfen Sie mit `convert -version`.
- `[OutputSettings] AllowedOutputFormat[]` (JPEG, PNG, WebP, GIF) listet die Formate, die Seiten anzeigen dürfen.
- `[ImageConverterSettings] LockTimeout` (ausgeliefert `60`): Sekunden, die ein Prozess wartet, während ein anderer
  denselben Alias erzeugt; danach beendet der wartende Prozess den anderen und erzeugt den Alias selbst.

Entfernen Sie nach der Änderung einer Alias-Definition die erzeugten Varianten, damit sie neu erstellt werden:

```bash
php bin/php/cache.php imagealias clear --allow-root-user
```

## 10.9 Sicherung und Wiederherstellung

Eine Sicherung besteht aus der **Datenbank** und den **Dateien**, im selben Moment erstellt. Dateien, auf die es
ankommt:

| Pfad | Warum |
|---|---|
| `var/storage` und `var/<site>/storage` | hochgeladene Bilder und Dateien |
| `settings/override`, `settings/siteaccess` | die Einstellungen der Installation (sie enthalten Passwörter: Halten Sie die Sicherung vertraulich) |
| Ihre eigenen Extensions und Designs | falls sie nicht in einem Repository liegen |
| `var/storage/sqlite3/` | die Datenbank, bei SQLite (separat gesichert, siehe unten) |
| `var/log` | optional, für spätere Untersuchungen |

Caches (`var/cache`, `var/<site>/cache`) brauchen keine Sicherung: Sie werden neu erzeugt.

### Vorgehen

1. Versetzen Sie die Site in den Wartungsmodus, damit zwischen der Sicherung der Datenbank und der der Dateien nichts
   geschrieben wird. Besucher erhalten eine 503-Seite; `--allow-admin` hält die Administration erreichbar,
   `--allow-ip` lässt angegebene Adressen durch:

   ```bash
   php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
   php bin/php/maintenance.php status --allow-root-user
   ```

2. Sichern Sie die Datenbank mit dem Werkzeug ihrer Engine:

   | Engine | Sicherung | Wiederherstellung |
   |---|---|---|
   | SQLite | `sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/db.sqlite'"` | `sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/db.sqlite'"` |
   | MySQL, MariaDB | `mysqldump --single-transaction -u USER -p DATABASE > /backup/db.sql` | `mysql -u USER -p DATABASE < /backup/db.sql` |
   | PostgreSQL | `pg_dump -U USER -Fc DATABASE > /backup/db.dump` | `pg_restore -U USER -d DATABASE --clean /backup/db.dump` |
   | MongoDB | `mongodump --uri="mongodb://USER@HOST/DATABASE" --out=/backup/mongo` | `mongorestore --uri="..." --drop /backup/mongo` |
   | Oracle | `php bin/php/console ext:ezoracle:datapump --export=db.dmp` (oder `expdp`) | `ext:ezoracle:datapump --import=db.dmp` |

   `-p` lässt den Client nach dem Passwort fragen; schreiben Sie es nie in die Kommandozeile. Lesen Sie für SQLite die
   Einzelheiten in [Kapitel 9](09-datenbanken.md#sqlite-sicherung-und--wiederherstellung): Die Online-Sicherung ist sicher, während
   die Site läuft, eine einfache Kopie einer viel benutzten Datei nicht.

3. Sichern Sie die Dateien:

   ```bash
   tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
   ```

   (fügen Sie `var/<site>/storage` hinzu, wenn das var-Verzeichnis der Site separat ist, sowie Ihre eigenen
   Extensions).

4. Schalten Sie die Wartung aus: `php bin/php/maintenance.php off --allow-root-user`.

Bewahren Sie Sicherungen außerhalb des Servers auf und behalten Sie mehrere Generationen. Bei SQLite und bei MySQL mit
InnoDB ist die Datenbanksicherung auch ohne Wartungsmodus konsistent; der Wartungsmodus sorgt dafür, dass Datenbank
und Dateien zueinander passen.

### Wiederherstellung

1. Wartung ein. Unter Velocity halten Sie außerdem den Server an
   (`php bin/php/console exp:velocity stop --allow-root-user`), damit kein Worker die alte Datenbank festhält.
2. Stellen Sie die Datenbank wieder her (Tabelle oben) und entpacken Sie die Dateien über die Installation; korrigieren
   Sie Eigentümer und Modus ([Kapitel 8](08-die-website-ausliefern.md)).
3. Leeren Sie jeden Cache und generieren Sie die Autoloads neu:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

4. Starten Sie Velocity, leeren Sie seinen Antwort-Cache, schalten Sie die Wartung aus und öffnen Sie die Startseite
   und die Administration.

**Testen Sie eine Wiederherstellung** mindestens einmal auf einem anderen Rechner und nach jedem größeren Upgrade:
Eine Sicherung, die nie wiederhergestellt wurde, ist eine Hoffnung, keine Sicherung. Siehe
[Wartungsmodus](../../en/us/features/6.0/maintenance-mode.md) und
[Eine Site betreiben, Teil 6](../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen).

### Wartungsfenster

Nehmen Sie für ein Upgrade, einen Import oder eine Wiederherstellung die öffentliche Site unter **Setup > Wartung**
vom Netz (Meldung, voraussichtliche Dauer, wer weiterhin Zugang hat, eine Vorschau und eine Bestätigung) oder mit
`php bin/php/maintenance.php on|off|status`. Besucher erhalten eine 503-Wartungsseite, die Administration unter
`/admin` bleibt erreichbar, und das Löschen von `var/maintenance.json` beendet den Wartungsmodus, was auch immer sonst
nicht stimmt. Die vollständige Anleitung: [Wartungsmodus](../anleitungen/wartungsmodus.md).

## 10.10 Logs und Log-Rotation

| Datei (unter `var/log/`, sofern nicht anders angegeben) | Geschrieben von | Lesen Sie sie für |
|---|---|---|
| `error.log` | dem Kernel, immer (`site.ini [DebugSettings] AlwaysLog[]=error`) | Fehler: die erste Anlaufstelle |
| `warning.log`, `notice.log`, `debug.log`, `strict.log` | dem Kernel, wenn eingeschaltet | Untersuchungen |
| `setup.log`, `kickstart.log`, `exp-install-<date>.ini` | den Installern: ein Eintrag je Installationslauf (`setup.log`), die vollständige Ausgabe jedes `exp:kickstarter`-Laufs (`kickstart.log`, frühere Läufe in `.1` bis `.9`), die Konfiguration, die `exp:install` verwendet hat; Passwörter sind in allen dreien maskiert | Installationsprobleme |
| `mail/` | den Datei-Transporten | die Mails, die hinausgegangen wären |
| `oracle-slow.log` | `ezoracle`, mit `SlowQueryThreshold` | langsame Oracle-Anweisungen |
| `cron-*.log` | Ihren Umleitungen in der Crontab | Cron-Ausgaben |
| `<site var dir>/log/cronjobs/output.log`, `error.log` | Jobs, die unter **Setup > Cronjobs** gestartet wurden (`cronjob.ini [AdminSettings] LogFile`, `ErrorFile`) | im Browser gestartete Jobs |
| Die Logs von Velocity | dem Server (`velocity.ini [LogSettings]`) | den Anwendungsserver |
| das Audit-Protokoll | `audit.php`; lesen mit `php bin/php/audit.php` | wer was getan hat |

```bash
tail -n 50 var/log/error.log
php bin/php/audit.php status --allow-root-user
php bin/php/audit.php tail --channel=access --lines=20 --allow-root-user
```

**Eingebaute Rotation.** Der Kernel rotiert seine eigenen Logs: Wenn eine Datei 200 KB überschreitet, wird sie zu
`.1`, die älteren rücken auf, und drei werden behalten (`error.log.1` bis `error.log.3`). Ändern Sie die Grenzen in
`config.php`, der PHP-Konfigurationsdatei der Installation (`config.php-RECOMMENDED` listet sie auf):

```php
define( 'EZPUBLISH_LOG_MAX_FILE_SIZE', 10485760 );   // 10 MB per file
define( 'EZPUBLISH_LOG_ROTATE_FILES', 10 );          // keep ten; 0 switches built-in rotation off
define( 'CUSTOM_LOG_MAX_FILE_SIZE', 10485760 );       // logs written through eZLog
define( 'CUSTOM_LOG_ROTATE_FILES', 10 );
```

**System-Rotation.** Dateien, die der Kernel nicht selbst schreibt (Ihre Dateien `cron-*.log`), brauchen logrotate
des Systems. Wenn Sie die eingebaute Rotation ausschalten, übernimmt logrotate auch die Logs des Kernels:

```
# /etc/logrotate.d/exponential
/path/to/installation/var/log/cron-*.log {
    weekly
    rotate 8
    compress
    missingok
    notifempty
    copytruncate
    su <site user> <site group>
}
```

`copytruncate`, weil die schreibenden Prozesse die Datei offen halten. Lassen Sie `[DebugSettings] DebugOutput=enabled`
im Produktivbetrieb nie eingeschaltet: Es zeigt Besuchern Interna und schreibt sehr viel.

## 10.11 Überwachung und Zustandsprüfungen

Es gibt keine einzelne Health-URL; kombinieren Sie eine Prüfung von außen mit den Statusbefehlen im Inneren. Jeder der
folgenden Befehle endet mit einem erkennbaren Status, und die meisten bieten `--json` für ein Überwachungssystem.

| Prüfung | Befehl | Gesund |
|---|---|---|
| Die Site antwortet | `curl -s -o /dev/null -w '%{http_code} %{time_total}\n' https://www.example.com/` | `200` und eine Zeit, die Sie kennen |
| Die Administration antwortet | `curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/admin/user/login` | `200` |
| Die Caches | `php bin/php/cache.php status --json --allow-root-user` | endet mit `PASS` |
| Velocity | `php bin/php/console exp:velocity status --json --allow-root-user` | läuft, Worker wie konfiguriert |
| Benachrichtigungen | `php bin/php/console exp:notificationstatus` | keine Ereignisse, die ohne einen kürzlichen Lauf warten |
| Das Audit-Protokoll | `php bin/php/audit.php verify --allow-root-user` | Exit-Status 0 (die Kette jedes Kanals intakt; 1 bedeutet gebrochen) |
| Neue Fehler | `tail -n 100 var/log/error.log` | nichts, was Sie nicht erklären können |
| Cron | `php bin/php/console crontab:list` und die Daten der `cron-*.log` | Einträge vorhanden, Logs aktuell |
| Festplatte | `df -h /path/to/installation` | Platz für Caches, Logs und eine Sicherung |
| Die Datenbank | je nach Engine; SQLite: [Kapitel 9](09-datenbanken.md#überwachung-einer-sqlite-site); Oracle: `ext:ezoracle:health` | |
| Performance | `php bin/php/console exp:benchmark --baseline=var/benchmark/base.json` | Exit-Status 0 (keine Regression) |

Ein kleines Skript in der Crontab des Benutzers der Site, das diese Prüfungen ausführt und bei einem Exit-Status
ungleich null eine Mail schickt, genügt für einen einzelnen Server. In der Administration zeigt
**Setup > Systeminformationen** Versionen, PHP und die Server-Engine.

## 10.12 Performance-Tuning

Messen Sie vor und nach jeder Änderung. `exp:benchmark` fragt die Startseite, die ersten Menüseiten, die Suchseite
(wenn Besucher suchen dürfen) und die Admin-Anmeldeseite ab, immer in dieser Reihenfolge, jeweils 100-mal nach 5
Aufwärmanfragen, 4 gleichzeitig, und gibt Median, p90, p99, Minimum, Maximum und Standardabweichung aus; der Modus
`kernel` misst die Bestandteile einer Seite im Prozess (Bootvorgang, Rendern kalt und warm, Laden der INI, Abrufen von
Inhalten, Datenbank-Roundtrip, Schreiben und Lesen des Caches), und der Modus `micro` misst reine PHP-Hot-Paths ohne
HTTP oder Datenbank:

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
# ... make a change ...
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15 --allow-root-user
php bin/php/console exp:benchmark --runs=200 --warmup=10 --format=json --allow-root-user > var/benchmark/run.json
php bin/php/console exp:benchmark kernel --repeat=30 --allow-root-user
php bin/php/console exp:benchmark micro --allow-root-user                 # no web server, no database
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8443 --allow-root-user
php bin/php/console exp:benchmark --cold --requests=50 --allow-root-user   # past the response caches
```

Jeder Lauf zeichnet seine Umgebung auf (PHP, OPcache und JIT, Xdebug, CPUs, Last, den Commit, das Datum), und ein
Vergleich mit einer Baseline, die unter einem anderen Aufbau entstanden ist, weist darauf hin. Für vergleichbare Zahlen
gilt: derselbe Rechner, nichts anderes läuft, OPcache wie im Produktivbetrieb, Xdebug aus, und beurteilen Sie zwei
Server nur anhand von Läufen, die direkt nacheinander gemacht wurden. Die CI des Repositorys führt den Modus `micro`
gegen `tests/benchmark/baseline.json` als Prüfung aus, die nur warnt.

Standardmäßig ist es rücksichtsvoll: Gegen einen anderen Host als diesen Rechner brauchen mehr als 1000 Anfragen je
URL oder mehr als 16 gleichzeitig `--force`. Die Anleitung [Benchmarking von Exponential](../anleitungen/benchmarking.md)
lehrt jeden Einsatz mit echten Ausgaben: Median, p90 und p99 lesen, Läufe fair vergleichen, die Checkliste gegen
Messrauschen, Arbeitsabläufe für eine Code-Änderung, Apache gegen Velocity und ein Deployment sowie die CI-Prüfung;
[Benchmark: exp:benchmark](../../en/us/features/6.0/benchmark.md) ist die Referenz.

### Die Hebel, nach Wirkung geordnet

1. **Mit Velocity ausliefern**, samt seinem Antwort-Cache ([Kapitel 8](08-die-website-ausliefern.md)). Eine gecachte
   anonyme Seite wird ohne PHP beantwortet; persistente Worker (`ForkPerRequest=disabled`) halten die Anwendung, die
   Datenbankverbindung und die kompilierten Dateien warm.
2. **View-Cache der Inhalte und Template-Block-Cache eingeschaltet lassen** (`[ContentSettings] ViewCaching=enabled`,
   `[TemplateSettings] TemplateCache=enabled`, beide ausgeliefert). Wärmen Sie die Site nach einem Leeren auf:
   `php bin/php/warm.php --allow-root-user` ruft jede veröffentlichte Seite ab.
3. **OPcache** (nächster Teil).
4. **APCu** für die Speicherstufe des Antwort-Caches von Velocity und den SQL-Abfrage-Cache im Modus `shared`:
   `velocity.ini [PHPSettings] IniOptions[]=apc.shm_size=256M` ist ausgeliefert; ist er zu klein, verdrängen sich die
   beiden gegenseitig.
5. **Der SQL-Abfrage-Cache** (`querycache.ini [QueryCacheSettings] Mode=request` oder `shared`), gemessen auf Ihrer
   Site ([SQL-Abfrage-Cache](../../en/us/bc/6.0/sql-query-cache.md)).
6. **Die Datenbank**: [Kapitel 9](09-datenbanken.md) (SQLite `ANALYZE`, persistente Verbindungen und DRCP bei Oracle).

**Lassen Sie den statischen Cache aus.** `[ContentSettings] StaticCache=disabled` ist der ausgelieferte Wert, und der
Installer schreibt ihn so. Mit eingeschaltetem statischem Cache erzeugte jedes Veröffentlichen jede betroffene Seite
auf jedem öffentlichen Siteaccess über HTTP innerhalb der Anfrage des Redakteurs neu, sodass ein Veröffentlichen, das
nichts änderte, Sekunden statt eines Bruchteils davon dauerte. Der Antwort-Cache von Velocity bietet Besuchern
denselben Vorteil ohne diese Kosten.

### OPcache

Unter **PHP-FPM** schalten Sie OPcache in der `php.ini` des Pools ein
([OPcache-Konfiguration](https://www.php.net/manual/en/opcache.configuration.php)):

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=32
; keep timestamp checks on: Exponential rewrites PHP cache files (INI caches,
; compiled templates, expiry.php) under the same name
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```

Setzen Sie auf einer Exponential-Site niemals `opcache.validate_timestamps=0`: Eine nach einem Leeren neu geschriebene
Cache-Datei würde in ihrer alten Form ausgeliefert, bis PHP neu startet.

Unter **Velocity** übergibt der Server seine eigenen PHP-Optionen (`settings/velocity.ini [PHPSettings] IniOptions[]`),
und zwei davon weichen aus einem Grund von PHP-FPM ab, den zu verstehen sich lohnt. OPcache vergleicht die Zeit einer
Datei mit der **Startzeit der Anfrage**, und ein Velocity-Worker ist eine einzige lange Anfrage, die mit dem Worker
begann:

- `opcache.revalidate_freq=0`: Mit der Voreinstellung 2 prüfte ein Worker eine Datei nie wieder, sodass eine neu
  geschriebene Cache-Datei so ausgeliefert wurde, wie sie war, als der Worker sie zum ersten Mal kompilierte.
- `opcache.file_update_protection=0`: OPcache behält kein Skript, das weniger als so viele Sekunden vor dem Start der
  Anfrage geändert wurde. Für einen Worker galt jede nach seinem Start geschriebene Datei (INI-Caches, Override-Caches,
  kompilierte Templates, alle nach einem Deployment oder Leeren neu erzeugt) während seiner gesamten Lebensdauer als
  „zu neu“ und wurde bei jedem Include erneut kompiliert: Der Cache bediente nur wenige Prozent der Includes, und ein
  Rendern brauchte etwa viermal so viel CPU wie unter PHP-FPM. Der Schutz ist nicht nötig, weil der Server Includes
  vollständig liest und die gecachte Kopie einer in den letzten zwei Sekunden geänderten Datei selbst verwirft.

Die ausgelieferte `velocity.ini` setzt beide, zusammen mit `opcache.enable_cli=1` (das Kommandozeilen-PHP, das den
Server ausführt, hat OPcache womöglich systemweit ausgeschaltet), `memory_consumption=256`,
`max_accelerated_files=20000` und `interned_strings_buffer=32`. Prüfen Sie mit
`php bin/php/cache.php opcache status --allow-root-user`; wie Sie es profilieren, lesen Sie unter
[Velocity und der Opcode-Cache](../../en/us/features/6.0/velocity-opcode-cache-and-profile.md). Jede PHP-Einstellung, die mit
„der Anfragezeit“ vergleicht, verhält sich in einem Velocity-Worker wie eingefroren; verdächtigen Sie das zuerst, wenn
etwas unter PHP-FPM funktioniert und unter Velocity nicht.

## 10.13 Verwaltung von Extensions

Extensions liegen in `extension/<name>/` (weitere Wurzelverzeichnisse mit `site.ini [ExtensionSettings]
AdditionalExtensionDirectories[]`). Die meisten kommen über Composer (`composer.json`); einige werden hineinkopiert.

**Aktivieren.** Eine Extension tut nichts, bis sie aktiv ist:

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=myextension
```

`ActiveAccessExtensions[]` in der `site.ini.append.php` eines Siteaccess aktiviert eine Extension nur für diesen
Siteaccess (geladen, nachdem der Siteaccess bestimmt ist). Die Administration tut dasselbe unter
**Setup > Extensions**. Nach dem Aktivieren:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override,design_base,active_extensions --allow-root-user
```

und starten Sie Velocity neu (oder führen Sie `exp:velocity deploy` aus). Bringt die Extension Datenbanktabellen mit,
installieren Sie sie so, wie es ihre Dokumentation beschreibt (oft `share/db_schema.dba` mit
`bin/php/ezsqlinsertschema.php`).

**Reihenfolge.** Wenn zwei Extensions dieselbe Einstellung, dasselbe Template oder dieselbe Design-Datei bereitstellen,
gewinnt die **frühere** in `ActiveExtensions[]`: Das Einstellungsverzeichnis jeder Extension wird der Reihe nach
vorangestellt, sodass eine spätere Extension am Ende eine niedrigere Priorität hat. `settings/override/` gewinnt immer
gegen jede Extension, und die Einstellungen eines Siteaccess gegen die globalen. `[ExtensionSettings]
ExtensionOrdering=enabled` (ausgeliefert) sortiert Extensions zusätzlich nach den Abhängigkeiten, die sie in
`extension.xml` deklarieren, was nicht jeden Fall lösen kann (es ordnet nicht über `ActiveExtensions` und
`ActiveAccessExtensions` hinweg um). Daher:

- Eine Site- oder Theme-Extension, die eine andere überschreiben soll, steht **vor** ihr;
- eine Extension, die nur Einstellungen zu einer Extension ergänzt, die eine vollständige Datei mitbringt (zum Beispiel
  Verbindungseinstellungen für `ezoracle`), steht **vor** dieser Extension;
- ändern Sie die Reihenfolge unter **Setup > Extensions**, Karte *Ladereihenfolge*, die nur `ActiveExtensions`
  speichert und eine Sicherungskopie anlegt ([Ladereihenfolge der Extensions](../../en/us/features/6.0/extension-loading-order.md)).

Prüfen Sie nach jeder Änderung der Reihenfolge den wirksamen Wert einer Einstellung mit
`php bin/php/console exp:ini get <file>/<Block>/<Key> --allow-root-user`. Die installierten Versionen, Lizenzen und
Websites stehen auf der About-Seite (`/ezinfo/about`), die die im aktuellen Siteaccess aktiven Extensions und die auf
der Festplatte vorhandenen zählt (zum Beispiel „63 aktiv in diesem Siteaccess, 85 auf der Festplatte“), dieselben
Zahlen wie das Dashboard. Eigene Extensions erstellen und veröffentlichen: [Extensions](../anleitungen/extensions.md).

**Die Job-Warteschlange und Exponential Intelligence.** `expqueue` und `expai` liegen nach `composer install` auf der
Festplatte und sind inaktiv, bis Sie sie einschalten:

1. `ActiveExtensions[]=expqueue` in `settings/override/site.ini.append.php` (die Warteschlange muss überall aktiv
   sein, wo Jobs eingereiht und ausgeführt werden), dann `./console ext:expqueue:install-schema`.
2. `ActiveAccessExtensions[]=expai` in der `site.ini.append.php` **jedes** Admin-Siteaccess (oder
   `ActiveExtensions[]=expai` für alle), dann `php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>`.
3. Der Schlüssel des Anbieters in `settings/override/expai.ini.append.php` (Modus 0640, nie committet):
   `[AISettings] Enabled=true` und `ApiKey=...`, oder ein lokales Modell ([lokale Modelle](https://github.com/se7enxweb/expai/blob/main/doc/local-models.md)).
4. Autoloads, Caches und `exp:velocity deploy --packer` wie oben; die Richtlinie `expai/write` für Redakteure.
5. Cron, beide mit dem Admin-Siteaccess, wenn expai nur dort aktiv ist:
   `* * * * * ... php runcronjobs.php -q -s <admin siteaccess> expqueue` und
   `*/5 * * * * ... php runcronjobs.php -q -s <admin siteaccess> expai`.

Einzelheiten: [Exponential Intelligence](../../en/us/features/6.0/extensions/expai.md), [die Job-Warteschlange](../../en/us/features/6.0/extensions/expqueue.md).

## 10.14 Mehrsprachige Sites

Inhaltssprachen und Sprachen der Oberfläche sind getrennt:

- **Inhaltssprachen** sind die Sprachen, in die Objekte übersetzt werden ([die Anleitung](../anleitungen/inhaltssprachen.md)).
  Fügen Sie eine in der Administration unter **Setup > Sprachen** hinzu (die View `content/translations`); danach
  können Redakteure Objekte in diese Sprache übersetzen.
- **Die Sprache der Oberfläche** eines Siteaccess ist `site.ini [RegionalSettings] Locale` (ausgeliefert `eng-GB`);
  ein Administrator kann in den Benutzereinstellungen seine eigene wählen.

Je Siteaccess entscheidet `[RegionalSettings]`, was ein Besucher sieht:

```ini
# settings/siteaccess/site_de/site.ini.append.php
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
# the languages shown, in order of preference; the first that exists is used
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-GB
# disabled: objects in none of these languages are not shown
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

Eine typische mehrsprachige Site hat einen öffentlichen Siteaccess je Sprache (`site_en`, `site_de`), zugeordnet über
ein URL-Element oder einen Hostnamen ([10.2](#102-siteaccesses-und-adressen-der-site)), und führt sie alle in
`RelatedSiteAccessList[]` auf, weil sie sich eine Datenbank teilen. Ändern Sie `ContentObjectLocale` einer bestehenden
Site nicht leichtfertig: Objekte, die in der neuen Standardsprache nicht existieren, verschwinden aus Auflistungen.
Geben Sie bei der Installation jede Sprache in `--languages=` an (oder in `Languages[]` in `kickstart.ini`): Ein
Site-Paket, dessen Inhalte ein nicht installiertes Locale verwenden, zeigt Seiten ohne Bilder oder Unterelemente.

Übersetzungen der Oberfläche: `php bin/php/ezchecktranslation.php ger-DE --allow-root-user` meldet, wie vollständig
eine ist, und nach einer Änderung an einer `.ts`-Datei leeren Sie `--clear-tag=i18n`. Siehe
[Übersetzungen und Sprachen](../../en/us/features/6.0/translations-and-languages.md).

## 10.15 Eine Checkliste für die erste Woche

1. Das Administratorpasswort ist geändert, `var/log/initial-admin-password` ist entfernt, die E-Mail-Adresse des
   Administrators ist echt.
2. `AdminEmail` und der Mail-Transport sind gesetzt; eine Mail zur Passwortänderung ist in einem echten Postfach
   angekommen.
3. Die Crontab führt `frequent`, den Standardteil, `infrequent` und `cache_cleanup` aus; die Logs wachsen.
4. `php bin/php/cache.php status --allow-root-user` endet mit `PASS`.
5. Eine Sicherung wurde erstellt **und** auf einem anderen Rechner **wiederhergestellt**.
6. `var/log/error.log` enthält nichts, was Sie nicht erklären können.
7. Eine Benchmark-Baseline ist in `var/benchmark/` gespeichert.
8. Die Logs rotieren (eingebaute Grenzen in `config.php`, logrotate für Cron-Logs).
9. Hinter einem Proxy auf einem anderen Rechner: Seine Adresse steht in `TrustedProxies[]`, und HTTPS-Seiten verlinken
   auf `https://`.

## 10.16 Wenn etwas nicht funktioniert

Die Fehler, die in den ersten Wochen am häufigsten auftreten, mit dem Teil dieses Kapitels, der sie erklärt. Die
vollständige Liste steht in [Kapitel 12](12-fehlerbehebung.md).

| Symptom | Wahrscheinliche Ursache | Siehe |
|---|---|---|
| Geplante Inhalte erscheinen nie, Benachrichtigungen kommen nie an | die Crontab fehlt oder läuft als anderer Benutzer oder mit einem anderen PHP | [10.3](#103-cronjobs) |
| Es kommt keine Mail an, und im Log des Mailservers steht nichts | `Transport=file`: Die Mail liegt in `var/log/mail/` | [10.4](#104-mail-der-datei-transport-und-echter-versand) |
| Große Anbieter weisen Mails zurück | die Domain des Absenders hat keinen SPF-Eintrag für diesen Server, oder der Server sendet über IPv6 und SPF listet nur IPv4 | [10.4](#104-mail-der-datei-transport-und-echter-versand) |
| Eine geänderte Einstellung wirkt nicht | der INI-Cache, oder Velocity-Worker, die noch die alten Einstellungen halten | [10.6](#106-caches) |
| „Class ... not found“ nach dem Hinzufügen einer Extension | das Autoload-Array der Extensions wurde nicht neu generiert | [10.7](#107-autoloads-der-klassen) |
| Eine neue Template-Datei ergibt eine Seite mit leerem Inhaltsbereich | der Template-Override-Cache | [10.6](#106-caches) |
| Links auf einer HTTPS-Site zeigen auf `http://` | der vorgeschaltete Proxy steht nicht in `TrustedProxies[]` | [10.2](#102-siteaccesses-und-adressen-der-site) |
| Eine Änderung wirkt für einige Neuladevorgänge, dann kehrt die alte Seite zurück | ein Cache vor PHP wurde geleert, bevor PHP den neuen Code ausführte | [10.6](#106-caches), „Die Reihenfolge nach einer Code-Änderung“ |
| Fehler über Dateien, die nach Wartungsarbeiten nicht geschrieben werden können | ein Skript lief als `root` und hat Dateien in `var/` hinterlassen, die `root` gehören | [Kapitel 12](12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft) |

**Bei einer öffentlichen Demo** (Umgebungsvariable `EXP_DEMO_MODE=1`) gelten die obigen Schritte zur Änderung des
Administratorpassworts nicht: Das veröffentlichte Passwort soll bestehen bleiben und kann im Demo-Modus weder geändert
noch zurückgesetzt werden. Prüfen Sie stattdessen, dass das Banner **Demo-Modus** in der Administration erscheint und
dass `var/log/security.log` Zeilen mit `demo/mode` für den Webserver und für Cron enthält
([Demo-Modus](../../en/us/bc/6.0/demo-mode.md), [Probleme mit dem Demo-Modus](../../en/us/help/hosting/runbooks/demo-mode-problems.md)).

## Referenzen

In diesem Repository:

- [Eine Site betreiben](../anleitungen/betrieb-einer-website.md): Caches, Cronjobs, statischer Cache, Preload, Sicherungen, Logs,
  Reparaturen.
- [Erste Schritte](../anleitungen/erste-schritte.md), [Bereitstellung](../anleitungen/bereitstellung.md),
  [Sicherheit und Audit](../anleitungen/sicherheit-und-audit.md), [Extensions](../anleitungen/extensions.md),
  [Upgrades](../anleitungen/aktualisieren.md), [der Lernpfad](../anleitungen/README.md).
- [Benachrichtigungen: Betrieb und Fehlerbehebung](../anleitungen/benachrichtigungen-administrator.md),
  [Spezifikation der Benachrichtigungen](../../en/us/specifications/6.0/notifications.md),
  [E-Mail-Präferenzen für Administratoren](../anleitungen/mail-einstellungen-administrator.md),
  [Spezifikation von cjw_newsletter](../../en/us/specifications/6.0/cjw_newsletter.md).
- [Cronjob-Konsole](../../en/us/features/6.0/cronjobs-console.md),
  [Befehle, Cronjob-Teile und Views als Klassen](../../en/us/specifications/6.0/runnable-commands-cronjobs-views.md),
  [Inhaltsjobs](../../en/us/features/6.0/content-jobs.md), [Velocity-Scheduler](../../en/us/features/6.0/velocity-scheduler.md).
- [Cache-Konsole](../../en/us/bc/6.0/cache-console.md), [Cache-Leerungen, die Verzeichnisse beiseiteschieben](../../en/us/features/6.0/cache-clear-rename-aside.md),
  [HTTP-Cache](../../en/us/bc/6.0/http-caching.md), [SQL-Abfrage-Cache](../../en/us/bc/6.0/sql-query-cache.md),
  [Antwort-Cache von Velocity](../../en/us/features/6.0/velocity-response-cache.md),
  [Voreinstellungen des statischen Caches](../../en/us/bc/6.0/static-cache-defaults.md), [View „Preload Sites“](../../en/us/features/6.0/preload-sites-view.md).
- [Velocity-Engines und exp:velocity deploy](../../en/us/bc/6.0/velocity-engines.md),
  [Velocity-Server mit persistenten Workern](../../en/us/features/6.0/velocity-persistent-worker-server.md),
  [Einstellungen der Velocity-Engine](../../en/us/specifications/6.0/velocity-engine-settings.md),
  [Velocity und der Opcode-Cache](../../en/us/features/6.0/velocity-opcode-cache-and-profile.md).
- [Benchmark](../../en/us/features/6.0/benchmark.md), [Wartungsmodus](../../en/us/features/6.0/maintenance-mode.md),
  [Ein Benutzerpasswort zurücksetzen](../../en/us/features/6.0/reset-user-password.md), [Ihr Passwort ändern](../../en/us/features/6.0/modern-password-change.md),
  [Audit-Protokoll](../../en/us/features/6.0/audit-trail.md).
- [Vertrauenswürdige Proxys](../../en/us/bc/6.0/trusted-proxies.md), [exp:ini](../../en/us/features/6.0/exp-ini-command.md), [Ladereihenfolge der Extensions](../../en/us/features/6.0/extension-loading-order.md),
  [Zusätzliche Extension-Verzeichnisse](../../en/us/bc/6.0/AdditionalExtensionDirectories.md),
  [Einstellungen je Site innerhalb von Extensions](../../en/us/features/6.0/multi-site-ini-overrides.md),
  [Übersetzungen und Sprachen](../../en/us/features/6.0/translations-and-languages.md).
- [Eine Installation reparieren](../../en/us/bc/6.0/repair.md), [Glossar](../../en/us/glossary.md).
- Die Einstellungen selbst: [`settings/site.ini`](../../../settings/site.ini), [`settings/cronjob.ini`](../../../settings/cronjob.ini),
  [`settings/image.ini`](../../../settings/image.ini), [`settings/velocity.ini`](../../../settings/velocity.ini),
  [`settings/querycache.ini`](../../../settings/querycache.ini), [`.autoloadignore`](../../../.autoloadignore),
  [`config.php-RECOMMENDED`](../../../config.php-RECOMMENDED).

Extern:

- PHP: [OPcache-Konfiguration](https://www.php.net/manual/en/opcache.configuration.php) (einschließlich
  `opcache.file_update_protection` und `opcache.revalidate_freq`), [APCu](https://www.php.net/manual/en/book.apcu.php),
  [Garbage Collection der Sitzungen](https://www.php.net/manual/en/session.configuration.php).
- [crontab(5)](https://man7.org/linux/man-pages/man5/crontab.5.html), [logrotate(8)](https://man7.org/linux/man-pages/man8/logrotate.8.html).
- Mail: [SPF (RFC 7208)](https://www.rfc-editor.org/rfc/rfc7208.html), [DKIM (RFC 6376)](https://www.rfc-editor.org/rfc/rfc6376.html),
  [DMARC (RFC 9989, der RFC 7489 ersetzt hat)](https://www.rfc-editor.org/rfc/rfc9989.html).
- Datenbanken: die Referenzen in [Kapitel 9](09-datenbanken.md#quellen).

[Inhalt](README.md) · Zurück: [9. Datenbanken](09-datenbanken.md) · Weiter: [11. Upgrade](11-aktualisieren.md)
