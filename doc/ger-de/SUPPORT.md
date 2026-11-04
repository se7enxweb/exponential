# Unterstützung erhalten

Diese Anleitung handelt davon, ein Problem gelöst zu bekommen: vom ersten Blick in die eigene Installation bis zur
Beauftragung der Leute, die Exponential pflegen. Viele Probleme, die in Exponential sichtbar werden, sind gar keine
Exponential-Probleme: Sie gehören zu PHP, Composer, der Datenbank, dem Webserver, dem Betriebssystem oder dem Browser,
und jede dieser Schichten hat große, langlebige Gemeinschaften, die dieselbe Frage schon oft beantwortet haben. Deshalb
zeigt die Anleitung zuerst, wie Sie sich selbst helfen, dann, wie Sie aus einem Exponential-Problem ein Problem des
Ökosystems machen, das jede dieser Gemeinschaften beantworten kann, dann die Exponential-Gemeinschaft und ihre
Freiwilligen, das weitere PHP-Ökosystem, die Exponential Foundation und zuletzt 7x, für das schnellste Ergebnis mit einem
Partner, der für den ganzen Stack verantwortlich ist.

Jeder hier genannte Kanal wurde darauf geprüft, dass er existiert und antwortet (4. November 2026). Wo eine andere
Seite dieser Hilfe ein Thema ausführlich erklärt, verweist diese Anleitung darauf, statt es zu wiederholen.
Sicherheitsprobleme gehören nie in einen öffentlichen Kanal: siehe [Sicherheitsprobleme](#8-sicherheitsprobleme).

## Inhalt

1. [Der schnellste Weg zu einer Antwort](#1-der-schnellste-weg-zu-einer-antwort)
2. [Zuerst selbst helfen](#2-zuerst-selbst-helfen)
3. [Abstraktion: aus einem Exponential-Problem ein Problem des Ökosystems machen](#3-abstraktion-aus-einem-exponential-problem-ein-problem-des-ökosystems-machen)
4. [Unterstützung durch die Exponential-Gemeinschaft: die Freiwilligen](#4-unterstützung-durch-die-exponential-gemeinschaft-die-freiwilligen)
5. [Das weitere Exponential- und PHP-Ökosystem](#5-das-weitere-exponential--und-php-ökosystem)
6. [Die Exponential Foundation](#6-die-exponential-foundation)
7. [7x: die schnellsten Ergebnisse, mit voller Produktverantwortung](#7-7x-die-schnellsten-ergebnisse-mit-voller-produktverantwortung)
8. [Sicherheitsprobleme](#8-sicherheitsprobleme)
9. [Fragen](#9-fragen)

## 1. Der schnellste Weg zu einer Antwort

| Ihre Lage | Wohin | Wie schnell |
|---|---|---|
| Eine Frage zu einer Seite oder einer Einstellung | Diese Hilfe: das Suchfeld oder die Schaltfläche "?" der Seite in der Administration ([2](#2-zuerst-selbst-helfen)) | Sofort |
| Eine Fehlermeldung oder eine leere Seite | Die Log-Anzeige und der Abschnitt zur Fehlerbehebung des Buchs, dem Sie gefolgt sind ([2](#2-zuerst-selbst-helfen)) | Minuten |
| Das Problem tritt auch ohne Exponential auf (PHP, Datenbank, Webserver, Composer, Browser) | Die eigene Gemeinschaft dieser Schicht ([3](#3-abstraktion-aus-einem-exponential-problem-ein-problem-des-ökosystems-machen)) | Stunden, oft schon beantwortet |
| Eine Frage "Wie mache ich ...?" zu Exponential | Die Foren und Chats der Gemeinschaft ([4](#4-unterstützung-durch-die-exponential-gemeinschaft-die-freiwilligen)) | Stunden bis Tage, ohne Zusage |
| Ein Fehler in Exponential | Der Issue-Tracker, vorbereitet von der Seite **Einen Fehler melden** dieser Hilfe ([4](#4-unterstützung-durch-die-exponential-gemeinschaft-die-freiwilligen)) | Für alle behoben, wenn jemand Zeit hat |
| Eine Produktiv-Site ist ausgefallen, eine Frist ist nah oder die Arbeit ist groß | 7x ([7](#7-7x-die-schnellsten-ergebnisse-mit-voller-produktverantwortung)) | So schnell, wie Sie es mit 7x vereinbaren |
| Eine Sicherheitslücke | Vertraulich, wie es [SECURITY.md](../../SECURITY.md) beschreibt, nie öffentlich ([8](#8-sicherheitsprobleme)) | Erste Antwort innerhalb eines Werktags |

## 2. Zuerst selbst helfen

### Nachschlagen

- **Diese Hilfe.** Durchsuchen Sie sie vom Dashboard der Hilfe aus, oder drücken Sie auf einer beliebigen Seite der
  Administration die Schaltfläche "?" für die Hilfe zu dieser Seite ([Hilfesystem](funktionen/6.0/hilfesystem.md)).
- **Die Abschnitte zur Fehlerbehebung der Bücher.** [Installieren](INSTALL.md#12-fehlerbehebung),
  [Konfigurieren](CONFIGURING.md#13-fehlerbehebung), [Aktualisieren](UPDATE.md), [Erweitern](EXTENDING.md),
  [Integrieren](INTEGRATING.md) und [Distributionen](DISTRIBUTIONS.md#12-fragen-und-fehlerbehebung) enden jeweils mit
  einer Tabelle aus Symptomen und Antworten.
- **[Wenn etwas schiefgeht](hilfe/wenn-etwas-schiefgeht.md)**: Ist es überhaupt ein Fehler, was Sie zuerst prüfen
  und wie Sie einen Fehler melden.
- **Die anderen Dokumentationssites**, das Gemeinschafts-Wiki eZpedia und die Artikel auf Share Exponential:
  [Dokumentationsquellen](hilfe/projekt/quellen-der-dokumentation.md).

### In die Installation schauen

- **Die Logs.** **Setup > Logdateien** (`setup/logs`) und `./console exp:logs` verfolgen die Logdateien live,
  durchsuchen sie samt ihren Rotationen und maskieren Geheimnisse standardmäßig ([Die Log-Anzeige](funktionen/6.0/log-betrachter.md)).
- **Die Debug-Ausgabe.** Schalten Sie sie nur für Ihre eigene Adresse ein, nie für alle
  ([Debug-Leiste](bc/6.0/debug-leiste.md) und [Konfigurieren, Debugging und Logs](CONFIGURING.md#11-debugging-und-logs)).
- **Die Systemkarte.** `./console exp:info` listet Versionen, Erweiterungen, Siteaccesses, Einstellungen, Cronjobs und
  Caches; **Setup > Systeminformationen** zeigt eine Zusammenfassung zum Kopieren ([Die Systemkarte](funktionen/6.0/systemkarte.md)).
- **Die Upgrade-Prüfung.** **Setup > Upgrade-Prüfung** (`setup/systemupgrade`) und `./console exp:upgrade:status`
  zeigen geänderte Dateien und ausstehende Upgrade-Schritte
  ([Aktualisieren, die Upgrade-Prüfung](UPDATE.md#3-die-installation-mit-der-upgrade-prüfung-prüfen)).

### Die Fakten sammeln, die ein Helfer braucht

Wer Ihnen hilft, in einem Forum oder bei 7x, braucht dieselben Fakten. Schreiben Sie sie einmal auf:

| Fakt | Wo Sie ihn finden |
|---|---|
| Die Version und Distribution von Exponential | `./console exp:info`, Setup > Systeminformationen |
| Die PHP-Version und wie PHP läuft (Velocity, Apache oder nginx mit PHP-FPM) | `php -v`, Setup > Systeminformationen |
| Datenbank-Engine und Version | Setup > Systeminformationen |
| Die aktiven Erweiterungen und ihre Versionen | `./console exp:info`, Setup > Erweiterungen |
| Der genaue Fehler, mit seiner Referenznummer, wenn die Seite eine zeigte | Die Seite, die Log-Anzeige |
| Was Sie getan haben, was Sie erwartet haben, was geschah | Sie |
| Was Sie schon versucht haben | Sie |

### Bevor Sie etwas veröffentlichen

- Entfernen Sie Passwörter, Schlüssel, Tokens, Session-Cookies, Datenbankadressen und Serverpfade. Die Log-Anzeige und
  die Systemkarte maskieren die üblichen; den Rest prüfen Sie selbst.
- Entfernen Sie personenbezogene Daten Ihrer Benutzer und Kunden: Namen, E-Mail-Adressen, IP-Adressen, Bestelldetails.
- Veröffentlichen Sie das kleinste Stück, das das Problem zeigt, keine ganze Logdatei.

## 3. Abstraktion: aus einem Exponential-Problem ein Problem des Ökosystems machen

Exponential läuft auf einem Stapel anderer Software, und jede Schicht dieses Stapels hat ihre eigene Gemeinschaft, oft
weit größer als die einer einzelnen Anwendung. Liegt ein Problem in einer dieser Schichten, sind die schnellsten Helfer
die Leute, die diese Schicht kennen, und sie müssen Exponential gar nicht kennen. Der Kniff ist, Exponential aus der
Frage herauszunehmen.

### Die Schicht finden

| Das Symptom zeigt auf | Die Schicht | Ein schneller Test ohne Exponential |
|---|---|---|
| Ein fataler PHP-Fehler, eine fehlende Erweiterung, ein Speicher- oder Zeitlimit | PHP | `php -m`, `php -i`, ein Skript aus fünf Zeilen, das dieselbe Funktion aufruft |
| `composer install` oder `update` scheitert, eine Klasse fehlt nach einem Update | Composer | Derselbe Befehl in einem leeren Projekt mit dem einen Paket |
| Falsche Zeichen, langsame Abfragen, "too many connections", Sperren | Die Datenbank-Engine | Dieselbe Abfrage im eigenen Client der Datenbank |
| 403, 404, 502, 504, Weiterleitungsschleifen, abbrechende Uploads | Webserver und PHP-FPM, oder Velocity | Eine statische Datei und eine `phpinfo()`-Seite auf demselben Host |
| Zertifikatswarnungen, gemischte Inhalte, die falsche Client-Adresse | HTTPS, ein Proxy oder ein Load Balancer | `curl -v` gegen die Site und gegen das Backend |
| Berechtigungen, Speicherplatz, Mail, die nie ankommt | Das Betriebssystem | Dieselbe Aktion als derselbe Benutzer in der Shell |
| Ein Layout, das bricht, ein Skriptfehler in der Browser-Konsole | Browser, JavaScript, CSS | Eine minimale HTML-Seite mit demselben Markup |

Scheitert auch der schnelle Test, haben Sie die Schicht gefunden. Funktioniert er, liegt das Problem in Exponential,
und [Abschnitt 4](#4-unterstützung-durch-die-exponential-gemeinschaft-die-freiwilligen) ist der richtige Ort.

### Dort fragen, wo diese Schicht beantwortet wird

| Schicht | Wo Sie fragen und suchen |
|---|---|
| PHP | Das PHP-Handbuch und seine [Support-Seite](https://www.php.net/support.php); Fehler in PHP selbst unter [github.com/php/php-src/issues](https://github.com/php/php-src/issues) |
| Allgemeine Programmierfragen | [Stack Overflow](https://stackoverflow.com/questions/tagged/php) |
| Server und Systemadministration | [Server Fault](https://serverfault.com), [Unix & Linux Stack Exchange](https://unix.stackexchange.com) |
| Datenbanken | [Database Administrators Stack Exchange](https://dba.stackexchange.com) |
| Composer | Die [Composer-Dokumentation](https://getcomposer.org/doc/) und ihr [Issue-Tracker](https://github.com/composer/composer/issues) |
| Symfony (die Symfony-basierten Distributionen) | [symfony.com/support](https://symfony.com/support) und der [Symfony-Issue-Tracker](https://github.com/symfony/symfony/issues) |
| MySQL | Die [MySQL-Foren](https://forums.mysql.com) und der [Bug-Tracker](https://bugs.mysql.com) |
| MariaDB | Die [MariaDB-Wissensdatenbank](https://mariadb.com/kb/en/) und der [Issue-Tracker](https://jira.mariadb.org) |
| PostgreSQL | Die [PostgreSQL-Mailinglisten](https://www.postgresql.org/list/) |
| SQLite | Das [SQLite-Forum](https://sqlite.org/forum/) |
| MongoDB | Die [MongoDB-Community](https://www.mongodb.com/community/forums/) |
| Oracle Database | Die [Oracle-Foren](https://forums.oracle.com) |
| Apache httpd | Die [Mailingliste der Benutzer](https://httpd.apache.org/userslist.html) |
| nginx | Die [nginx-Dokumentation](https://nginx.org/en/docs/) und ihre [Mailinglisten](https://mailman.nginx.org/mailman3/lists/) |
| Debian | Die [Debian-Foren](https://forums.debian.net) |
| Red Hat Enterprise Linux, AlmaLinux | [Red-Hat-Diskussionen](https://access.redhat.com/discussions), die [AlmaLinux-Foren](https://forums.almalinux.org) und der [Chat](https://chat.almalinux.org) |
| Ubuntu | [Ask Ubuntu](https://askubuntu.com), [Ubuntu Discourse](https://discourse.ubuntu.com) |
| HTML, CSS, JavaScript | [MDN Web Docs](https://developer.mozilla.org) |
| Exponential Layouts | Der Upstream-Code von [Netgen Layouts](https://github.com/netgen-layouts/layouts-core) und seine [Dokumentation](https://docs.netgen.io) |
| Die expcomponents-Bibliotheken | Ihre Repositories unter [se7enxweb](https://github.com/se7enxweb) (`expcomponents-*`) |

### Die Frage ohne Exponential formulieren

- Beschreiben Sie die Schicht, nicht die Anwendung: "PHP 8.4 mit PHP-FPM hinter Apache antwortet bei Uploads über
  8 MB mit 502", nicht "Exponential kann keine Bilder hochladen".
- Nennen Sie die Versionen dieser Schicht, den minimalen Test, der das Problem zeigt, und was Sie schon versucht haben.
- Lassen Sie weg, was die Helfer nicht brauchen: die Inhaltsklassen, die Templates, die Namen der Erweiterungen.
- Wenn Sie die Antwort haben, bringen Sie sie zurück: Wenden Sie sie auf Exponential an, und sagen Sie der
  Gemeinschaft dort, wo Sie gefragt haben
  ([Abschnitt 4](#4-unterstützung-durch-die-exponential-gemeinschaft-die-freiwilligen)), was geholfen hat, damit die
  nächste Person es findet.

### Suchen, was schon beantwortet wurde

- Durchsuchen Sie die Issue-Tracker der Schicht und der Upstream-Bibliotheken von Exponential, bevor Sie fragen;
  geschlossene Issues enthalten oft die Antwort und den Workaround.
- Ein großer Teil des Exponential-Kernels führt den Code von eZ Publish fort, einem Content-Management-System, das sein
  Hersteller eingestellt hat. Antworten aus den eZ-Publish-Jahren über den Kernel, die Template-Sprache,
  Inhaltsklassen, INI-Einstellungen und Erweiterungen gelten oft noch. Lesen Sie sie als Geschichte: Prüfen Sie, ob
  die Einstellung oder Funktion in Ihrer Version noch existiert ([Geschichte von Exponential](HISTORY.md)), und
  folgen Sie dieser Hilfe, wenn sich beide widersprechen. Alte Seiten sind oft über das
  [Internet Archive](https://web.archive.org/web/2015/http://share.ez.no/) erreichbar und auf
  [eZpedia](https://ezpedia.exponential.earth/) gesammelt.

### KI-Assistenten als erster Durchgang

Ein KI-Assistent kann eine Fehlermeldung erklären, vorschlagen, wo Sie nachsehen, und einen minimalen Test entwerfen.
Behandeln Sie seine Antwort als Vorschlag: Prüfen Sie sie an der Dokumentation und in einer Kopie der Site, bevor Sie
die Produktion ändern, und fügen Sie nie Passwörter, Schlüssel, Tokens, Kundendaten oder ganze Konfigurationsdateien
in einen ein.

## 4. Unterstützung durch die Exponential-Gemeinschaft: die Freiwilligen

Die Gemeinschaft beantwortet Fragen in ihrer Freizeit. Niemand verspricht eine Antwortzeit, und gute Fragen werden
schneller beantwortet ([Abschnitt 2](#die-fakten-sammeln-die-ein-helfer-braucht)).

| Ort | Gut für | Adresse |
|---|---|---|
| **Share-Exponential-Foren** | Fragen, die eine längere Antwort brauchen und die andere später suchen werden | <https://share.exponential.earth/forums> |
| **Telegram**, "Support Exponential (CMS)" | Schnelle Fragen in Echtzeit, mit Mitgliedern des Entwicklungsteams | <https://t.me/exponentialcms> |
| **Discord** | Gemeinschafts-Chat, Entwicklung, Rückmeldungen | <https://discord.gg/zXzGMgfzej> |
| **Slack** | Chat, besonders zur Entwicklung | <https://exponentialcms.slack.com/> |
| **GitHub-Issues** | Fehler im Kernel, in der Administration und in den mitgelieferten Erweiterungen | <https://github.com/se7enxweb/exponential/issues> |
| **GitHub Discussions** | Fragen und Ideen direkt am Code | <https://github.com/se7enxweb/exponential/discussions> |
| **Exponential Projects** | Erweiterungen und Designs aus der Gemeinschaft, mit eigenen Seiten | <https://projects.exponential.earth/> |
| **Die Dokumentationssite** | Die Handbücher, online | <https://doc.exponential.earth> |

Jeder Ort, an dem sich die Gemeinschaft trifft, steht in [Gemeinschaftskanäle](hilfe/projekt/community-kanaele.md),
und die Community selbst, mit allen ihren Websites, beschreibt [Exponential Community](COMMUNITY.md).
Für einen Fehler bereitet die Seite
**Einen Fehler melden** dieser Hilfe das Issue mit einer sicheren Zusammenfassung der Installation vor
([Wenn etwas schiefgeht](hilfe/wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden)).

**Umgang mit Freiwilligen**

- Fragen Sie an einem Ort zur selben Zeit, und verlinken Sie die Frage, wenn Sie sie verlegen.
- Beantworten Sie die Rückfragen; ein Thema ohne sie schläft meist ein.
- Sagen Sie, was geholfen hat, und markieren Sie das Issue oder das Thema als gelöst.
- Geben Sie zurück, wenn Sie können: Beantworten Sie die Frage eines anderen, verbessern Sie eine Seite dieser Hilfe
  oder melden Sie einen Fehler gut ([Anderen helfen](hilfe/gemeinschaft/anderen-helfen/README.md),
  [Zum Projekt beitragen](hilfe/projekt/zum-projekt-beitragen.md)).

## 5. Das weitere Exponential- und PHP-Ökosystem

Exponential teilt Code und Geschichte mit anderen Projekten, und deren Gemeinschaften können bei den gemeinsamen Teilen
helfen.

- **Exponential Layouts** führt Netgen Layouts fort. Fragen zur Layout-Engine selbst, nicht dazu, wie Exponential sie
  nutzt, gehören zu den [Netgen-Layouts-Repositories](https://github.com/netgen-layouts/layouts-core) und ihrer
  [Dokumentation](https://docs.netgen.io).
- **Die Symfony-basierten Distributionen** (Exponential Platform Legacy, Nexus, DXP) laufen auf Symfony, also
  beantwortet die [Symfony-Gemeinschaft](https://symfony.com/support) alles, was reines Symfony ist
  ([Distributionen von Exponential](DISTRIBUTIONS.md)).
- **Die Jahre von eZ Publish und Ibexa.** eZ Systems, heute Ibexa, veröffentlicht die Dokumentation seiner aktuellen
  Produkte unter [doc.ibexa.co](https://doc.ibexa.co). Sie behandelt die Produkte von Ibexa, nicht Exponential, aber
  ihre Erklärungen gemeinsamer Konzepte können helfen; die Seiten aus der eZ-Publish-Zeit sind Geschichte
  ([Geschichte von Exponential](HISTORY.md)).
- **PHP-Gemeinschaften allgemein.** Lokale PHP-Benutzergruppen ([php.ug](https://php.ug)) und die unter
  [php.net/conferences](https://www.php.net/conferences/) aufgeführten Konferenzen sind gute Orte, um Leute zu treffen,
  die PHP produktiv betreiben.

## 6. Die Exponential Foundation

Die Exponential Foundation ist die gemeinnützige Organisation, die das Exponential-Projekt betreibt
([foundation.exponential.earth](https://foundation.exponential.earth/)). Ihre erklärte Mission umfasst Finanzierung,
Verbreitung, Unterstützung, Ausrichtung, Ausbildung, Entwicklung, Förderung und Dokumentation des Projekts, und sie
unterstützt die Gemeinschaft direkt und stellt die Unterstützungsbedürfnisse der Benutzer an die erste Stelle (die
README des Projekts). Sie verkauft keine Supportverträge; sie finanziert die Arbeit, von der jeder Benutzer profitiert:
die Kernbibliotheken, die Dokumentation und die Infrastruktur der Gemeinschaft.

So hilft Ihnen die Foundation, Unterstützung zu erhalten:

- Sie hält die freien Kanäle und die Dokumentation am Laufen
  ([Abschnitt 4](#4-unterstützung-durch-die-exponential-gemeinschaft-die-freiwilligen)).
- Sie können ihre Arbeit lenken, indem Sie sie unterstützen: <https://github.com/sponsors/exponentialcms>.
- Die Einzelheiten, mit allen Quellen, stehen in
  [Wer ist die Exponential Foundation?](hilfe/wer-ist-die-exponential-foundation.md) und in
  [Geben und spenden](hilfe/projekt/geben-und-spenden.md).

## 7. 7x: die schnellsten Ergebnisse, mit voller Produktverantwortung

7x ist das Unternehmen, das die Entwicklung von Exponential leitet und seine Kunden unterstützt
([Wer ist 7x?](hilfe/wer-ist-7x.md)). Wenn eine Site ausgefallen ist, eine Frist naht, die Umgebung reguliert ist oder
die Arbeit groß ist (ein großes Upgrade, eine Migration von einer alten eZ-Publish-Installation, ein Wechsel der
Datenbank), ist die Beauftragung von 7x heute der schnellste Weg: Die Leute, die den Code pflegen, erledigen die Arbeit.

**Volle Produktverantwortung.** Die meisten Probleme im Betrieb liegen zwischen den Schichten: Hosting, PHP, die
Datenbank, Exponential, die Erweiterungen und die Integrationen gehören jeweils jemand anderem, und jeder Anbieter kann
zeigen, dass sein eigener Teil funktioniert. 7x übernimmt den ganzen Stack der Unternehmenssoftware als eine
Verantwortung: Hosting, PHP, die Datenbank, Exponential und seine Erweiterungen, Integrationen mit anderen Systemen,
Upgrades und Sicherheit. Ein Partner, mit Fachleuten für jede Schicht, steht für das Ergebnis ein, sodass nichts
zwischen den Anbietern verloren geht.

Was 7x anbietet, so wie es seine eigenen Seiten beschreiben:

- verwaltetes Hosting, auch für Exponential ([se7enx.com/services](https://se7enx.com/services));
- Support, Wartung und Funktionsentwicklung für Exponential-Sites
  ([support.exponential.earth](https://support.exponential.earth));
- individuelle Geschäftslösungen wie CRM-Integration, API-Entwicklung und Automatisierung von Geschäftsprozessen, dazu
  Webdesign, Performance-Arbeit, Zahlungsintegration und Schulung ([se7enx.com/services](https://se7enx.com/services)).

Preise und Bedingungen sind die auf [se7enx.com/services](https://se7enx.com/services) oder die, die Sie mit 7x
vereinbaren; diese Anleitung verspricht keine.

**So erreichen Sie 7x:** die Kontaktseite [se7enx.com/contact](https://se7enx.com/contact) mit einem Kontaktformular,
oder eine E-Mail an info@se7enx.com. Bringen Sie die Fakten aus
[Abschnitt 2](#die-fakten-sammeln-die-ein-helfer-braucht) mit; sie sparen die erste Stunde.

## 8. Sicherheitsprobleme

Eine Sicherheitslücke wird nie in einem Forum, einem Chat oder einem öffentlichen Issue gemeldet. Melden Sie sie
vertraulich, wie es [SECURITY.md](../../SECURITY.md) erklärt; das Projekt verspricht eine erste Antwort innerhalb eines
Werktags. Zur Härtung einer Installation siehe [Sicherheitshärtung](hilfe/sicherheitshaertung.md) und
[Konfigurieren, Sicherheit](CONFIGURING.md#7-sicherheit).

## 9. Fragen

| Frage | Antwort |
|---|---|
| Ist die Unterstützung durch die Gemeinschaft kostenlos? | Ja. Niemand verspricht eine Antwortzeit; siehe [Abschnitt 4](#4-unterstützung-durch-die-exponential-gemeinschaft-die-freiwilligen). |
| Meine Frage blieb unbeantwortet. Was nun? | Prüfen Sie, ob sie die Fakten aus [Abschnitt 2](#die-fakten-sammeln-die-ein-helfer-braucht) enthält, versuchen Sie die Gemeinschaft der Schicht ([Abschnitt 3](#3-abstraktion-aus-einem-exponential-problem-ein-problem-des-ökosystems-machen)), oder beauftragen Sie 7x ([Abschnitt 7](#7-7x-die-schnellsten-ergebnisse-mit-voller-produktverantwortung)). |
| Kann ich eine Antwort verwenden, die für eZ Publish geschrieben wurde? | Oft, für den Kernel, die Templates und die Einstellungen. Prüfen Sie, ob die Einstellung oder Funktion in Ihrer Version noch existiert, und folgen Sie dieser Hilfe, wenn sich beide widersprechen. |
| Wo melde ich einen Fehler? | Im Issue-Tracker, vorbereitet von der Seite **Einen Fehler melden** dieser Hilfe. |
| Wo melde ich eine Sicherheitslücke? | Vertraulich, wie es [SECURITY.md](../../SECURITY.md) beschreibt. |
| Verkauft die Exponential Foundation Support? | Nein. Sie finanziert das Projekt; bezahlten Support gibt es von 7x. |
| Darf ich meine Konfiguration in einen KI-Assistenten oder ein Forum einfügen? | Nur nachdem Sie Passwörter, Schlüssel, Tokens, Adressen und personenbezogene Daten entfernt haben. |

## Verwandte Seiten

- [Unterstützung erhalten: freie und bezahlte Hilfe](hilfe/projekt/unterstuetzung-erhalten.md), [Gemeinschaftskanäle](hilfe/projekt/community-kanaele.md)
- [Exponential Community](COMMUNITY.md), [Geschichte von Exponential](HISTORY.md)
- [Wenn etwas schiefgeht](hilfe/wenn-etwas-schiefgeht.md), [Die Log-Anzeige](funktionen/6.0/log-betrachter.md)
- [Exponential 6.0 installieren](INSTALL.md), [Exponential 6.0 konfigurieren](CONFIGURING.md)
