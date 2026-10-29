---
title: Velocity ist Teil von Exponential 6.0.15
audience: admin, developer, editor, user, press
category: getting-started
order: 2
summary: Seit 6.0.15 ist der Internet- und Webanwendungsserver Velocity Teil von Exponential selbst, wird mit jeder Installation mitgeliefert und über einen einzigen Konsolenbefehl gesteuert. Warum das ein Wendepunkt ist, was er von Haus aus mitbringt, wie er neben Apache, nginx und FrankenPHP steht und warum Exponential jetzt ein einziger funktionierender Stack für Content-Management, Digital Experience und E-Commerce ist.
keywords: Velocity, Exponential Velocity, Anwendungsserver, Webserver, Internetserver, 6.0.15, Wendepunkt, Grundstein, Schlussstein, Integration, ein Stack, RAD, schnelle Anwendungsentwicklung, CMS, DXP, E-Commerce, composer, exp:velocity, FrankenPHP, Apache, nginx, PHP-FPM, PHP 8.0, Red Hat Enterprise Linux, application server, web server, Internet server, turning point, cornerstone, capstone, one stack, rapid application development, e-commerce
featured: true
type: help
---
# Velocity ist Teil von Exponential 6.0.15

Diese Seite ist für alle, die wissen möchten, was sich geändert hat, als Exponential 6.0.15 seinen eigenen Internet-
und Webanwendungsserver, **Exponential Velocity**, in das Kernprodukt aufgenommen hat: für Betreiber von Websites,
Administratoren, Hosting-Anbieter, Entwickler, Entscheider und die Presse. Zum Lesen brauchen Sie kein technisches
Wissen; die Befehle stehen für alle da, die sie ausprobieren möchten.

## Inhalt

1. [In einem Absatz](#in-einem-absatz)
2. [Was sich in 6.0.15 geändert hat](#was-sich-in-6015-geändert-hat)
3. [Warum es ein Wendepunkt ist](#warum-es-ein-wendepunkt-ist)
4. [Ein Stack, nicht nur ein Framework](#ein-stack-nicht-nur-ein-framework)
5. [Was Velocity von Haus aus mitbringt](#was-velocity-von-haus-aus-mitbringt)
6. [Velocity neben Apache, nginx und FrankenPHP](#velocity-neben-apache-nginx-und-frankenphp)
7. [Die Befehle](#die-befehle)
8. [Die dokumentierten Zahlen](#die-dokumentierten-zahlen)
9. [Was es für Sie bedeutet](#was-es-für-sie-bedeutet)
10. [Fragen und Antworten](#fragen-und-antworten)
11. [Weiterlesen](#weiterlesen)

## In einem Absatz

Seit Exponential 6.0.15 ist der Velocity Internet / Web Application Server Teil von Exponential. Die
`composer.json` im Wurzelverzeichnis **verlangt** das Paket `se7enxweb/exponential-velocity` in der Version
`~0.0.4.57`; es ist nicht mehr nur ein Vorschlag. Jede mit Composer erstellte Installation hat den Server daher, auf
jeder PHP-Version, die Exponential unterstützt (PHP 8.0 und neuer, einschließlich des mitgelieferten PHP 8.0 von Red
Hat Enterprise Linux 9). Gesteuert wird er über dieselbe Konsole wie der Rest von Exponential:
`./console exp:velocity deploy`, `start`, `restart` und `cache clear`. Das markiert einen Wendepunkt für das Projekt,
Grundstein und Schlussstein zugleich: eine neue Stufe der Integration von Anwendung und System, bei der das
Kernprodukt von Haus aus und standardmäßig noch mehr Funktionen bietet.

## Was sich in 6.0.15 geändert hat

| Vor 6.0.15 | Seit 6.0.15 |
|---|---|
| Exponential war eine PHP-Anwendung, vor die ein Webserver nach fremder Wahl gestellt werden musste. | Exponential liefert seinen eigenen Internet- und Webanwendungsserver mit und läuft weiterhin auch hinter jedem anderen. |
| Velocity war ein eigenes Paket, in der `composer.json` unter `suggest` aufgeführt und von Hand hinzuzufügen. | Die `composer.json` verlangt `se7enxweb/exponential-velocity ~0.0.4.57`; `composer install` installiert es. |
| Velocity brauchte PHP 8.1, Installationen auf PHP 8.0 (Red Hat Enterprise Linux 9) mussten darauf verzichten. | Velocity 0.0.4.54 läuft auf PHP 8.0 bis 8.5, demselben Bereich wie der Kernel von Exponential. |
| Ausliefern, Cachen und Bereitstellen waren getrennte Aufgaben mit getrennten Werkzeugen. | `./console exp:velocity deploy` erneuert Autoloads, Einstellungen, Caches, die PHP-Prozesse und den Server in einem geordneten Durchlauf. |

Nichts, was eine bestehende Site tut, hört auf zu funktionieren. Eine Site, die von Apache mit PHP-FPM, von nginx
oder von FrankenPHP ausgeliefert wird, wird weiter so ausgeliefert wie bisher; der Server ist einfach da und bereit
für den Tag, an dem Sie ihn möchten. Die mitgelieferte `settings/velocity.ini` startet standardmäßig weiterhin den
eingebauten Server von PHP (`[ServerSettings] Engine=php`), weil das immer funktioniert; eine einzige Einstellung,
`Engine=qbix`, schaltet auf den eigenen Server von Velocity um.

## Warum es ein Wendepunkt ist

Fast während ihrer ganzen Geschichte endete die Software, die heute Exponential heißt, am Rand von PHP. Was zwischen
dem Besucher und der Anwendung geschah (der Webserver, sein Prozessmanager, das TLS-Zertifikat, der Antwort-Cache,
das Neuladen nach einem Deployment), baute jedes Team für sich selbst darum herum, und jedes Team lernte dieselben
Lektionen von Neuem.

Exponential 6.0.15 schließt diese Lücke:

- **Ein Grundstein.** Der Server gehört jetzt zum Fundament, auf dem der Rest des Produkts aufbaut. Funktionen des
  Kernels werden für ihn geschrieben und mit ihm getestet: persistente Worker, die einmal starten, ein Antwort-Cache,
  der antwortet, bevor ein Worker ausgewählt wird, ein Deploy-Befehl, der weiß, welche Caches und Prozesse eine
  Änderung betrifft.
- **Ein Schlussstein.** Er vollendet, was die Linie 6.0 aufgebaut hat: aktuelles PHP von 8.0 bis 8.5, SQLite als
  Standarddatenbank, sodass kein Datenbankserver nötig ist, sechs Datenbanksysteme auf einem Kernel, ein Installer mit
  einem einzigen Befehl, die Konsole, das Prüfprotokoll und das Hilfecenter. Mit dem Server gehört der ganze Weg von
  einer Anfrage bis zur fertig dargestellten Seite zu einem einzigen Produkt, an einem Ort dokumentiert.
- **Eine neue Stufe der Integration von Anwendung und System.** Anwendung und Server werden gemeinsam entwickelt und
  aufeinander abgestimmt, statt nur kompatibel gemacht zu werden. Das Ergebnis ist ein Produkt, das von Haus aus, im
  Kern und standardmäßig noch mehr Funktionen bietet.

## Ein Stack, nicht nur ein Framework

Exponential ist eine vollständige, funktionierende Einheit, nicht nur ein Framework, das Sie erst fertigstellen
müssen, bevor es etwas tut. Ein einziges `composer install` liefert einen Stack für die schnelle
Anwendungsentwicklung (RAD) von Internetanwendungen, der zugleich ein Content-Management-System, eine Digital
Experience Platform und ein E-Commerce-System ist:

| Schicht | Was mitgeliefert wird | Weiterlesen |
|---|---|---|
| Server | Exponential Velocity: HTTP/1.1, HTTP/2 über TLS, WebSockets, eigene HTTPS-Zertifikate, ein Antwort-Cache, statische Dateien und Bilder | [Velocity-Webserver](../funktionen/6.0/velocity-webserver.md) |
| Anwendungsplattform | Der Kernel von Exponential: Inhaltsklassen und Objekte, Versionen, Rollen und Richtlinien, Multisite, mehr als 360 Locales, REST, Cronjobs, die Konsole | [README](https://github.com/se7enxweb/exponential/blob/main/README.md) |
| Schnelle Entwicklung | Die RAD-Werkzeuge: ein Katalog der Erweiterungspunkte, Assistenten, die eine funktionierende Erweiterung schreiben, eine Bestandsaufnahme und eine Zustandsprüfung | [RAD-Werkzeuge](../funktionen/6.0/rad-werkzeuge.md) |
| Content-Management | Die Administration (Admin 4), der Online-Editor, Veröffentlichen und Versionen, der Papierkorb, das Prüfprotokoll | [Hilfe für Redakteure und Benutzer](README.md) |
| Digital Experience | Exponential Layouts (ein visueller Seitenbaukasten mit Zonen, Blöcken und Regeln), Multisite und mehrsprachige Sites, der Schreibassistent Exponential Intelligence, der nur Entwürfe schreibt | [Der Layout-Editor](layout-editor.md) |
| E-Commerce | Der Shop: Warenkorb, Bestellungen, Zahlungsgateways, Bestellbelege und das Shop-Dashboard | [Shop-Dashboard](../funktionen/6.0/shop-dashboard.md) |
| Daten | Standardmäßig SQLite; PostgreSQL, MongoDB, MariaDB, Oracle und MySQL auf demselben Kernel | [Datenbankunterstützung](datenbankunterstuetzung.md) |

Zur Familie von Exponential gehört außerdem Exponential Platform DXP, das reine Symfony-Mitglied, für Teams, die die
Linie der Symfony-basierten Digital Experience Platform möchten; es ist ein eigenes Produkt mit eigenem
Funktionsumfang ([Wer ist die Exponential Foundation?](wer-ist-die-exponential-foundation.md)).

Weil jede dieser Schichten aus einer Quelle kommt, mit einem Installer, einer Konsole und einer Dokumentation, kommt
der Einsatz von Exponential überall auf der Welt ohne die übliche Integrationsarbeit aus: Es gibt keinen Server, den
Sie vor der ersten Seite auswählen müssen, keine Dokumentation eines zweiten Anbieters, die Sie damit in Einklang
bringen müssen, und keine PHP-Version, auf der die Hälfte des Stacks fehlt. Exponential läuft auf fast jedem
Betriebssystem, das PHP 8.0 oder neuer hat ([Betriebssystemunterstützung](betriebssystemunterstuetzung.md)).

## Was Velocity von Haus aus mitbringt

Jede dieser Funktionen ist eine dokumentierte, ausgelieferte Funktion von Velocity 0.0.4.x:

| Funktion | Was sie tut | Seite |
|---|---|---|
| Persistente Worker | Die Anwendung startet einmal in einen Elternprozess; ein Pool von Workern hält sie zwischen den Anfragen geladen. | [Server mit persistenten Workern](../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md) |
| Antwort-Cache | Öffentliche Seiten werden im Serverprozess beantwortet, bevor ein Worker ausgewählt wird, mit `304`, `ETag` und stale-while-revalidate. | [Antwort-Cache](../funktionen/6.0/velocity-antwort-cache.md) |
| HTTPS, das sich selbst versorgt | Ein selbstsigniertes Zertifikat, wenn keines eingerichtet ist; Ihre eigenen Dateien, ein PKCS#12-Bündel, Let's Encrypt oder eine andere ACME-Zertifizierungsstelle, wenn eines eingerichtet ist; erneuerte Zertifikate werden ohne Neustart eingewechselt. | [HTTPS und Zertifikate](../funktionen/6.0/velocity-https-zertifikate.md) |
| Statische Dateien und Bilder | Dateien werden ohne PHP ausgeliefert, validiert, komprimiert und in Bereichen gesendet, und Bilder werden auf Anfrage umgewandelt oder skaliert. | [Statische Dateien und Bilder](../funktionen/6.0/velocity-statische-dateien-und-bilder.md) |
| Echtzeit | WebSockets, Server-Sent Events und Räume, in den Server eingebaut. | [WebSockets und Ereignisse](../funktionen/6.0/velocity-websockets-und-ereignisse.md) |
| Dashboard und Bedienfeld | Laufende Anfragen, Worker und Speicher unter `/Q/dashboard`; ein passwortgeschütztes Bedienfeld unter `/Q/panel`. | [Bedienfeld und Dashboard](../funktionen/6.0/velocity-control-panel.md) |
| Q-Shell | Eine ausklappbare Konsole auf jeder Ansicht des Servers für Status, Worker, Cache, Sites, Zertifikate und Logs. | [Q-Shell](../funktionen/6.0/velocity-q-shell.md) |
| Zeitplaner | Ein kleiner Cron im Server für wiederkehrende Arbeit, ohne zweiten Prozessmanager. | [Zeitplaner](../funktionen/6.0/velocity-zeitplaner.md) |
| Pakete und Binärdateien | Systempakete, Docker-Images und einzelne Binärdateien für den Einsatz außerhalb einer Installation von Exponential. | [Pakete und Binärdateien](../funktionen/6.0/velocity-pakete-und-binaerdateien.md) |
| Vertraute Konfiguration | Eine Verzeichnisstruktur im Stil von Debians `/etc/apache2`: `sites-available`, `conf-available`, `mods-available`, aktiviert mit `exp:velocity site\|conf\|mod enable`. | [Verzeichnisstruktur](../bc/6.0/velocity-verzeichnisstruktur.md) |

## Velocity neben Apache, nginx und FrankenPHP

Velocity nimmt Ihnen die Wahl des Webservers nicht ab; es legt den besten mit in den Karton. Exponential wird von
jedem dieser Server ausgeliefert, und `exp:velocity` steuert die letzten beiden:

| Engine | Rolle |
|---|---|
| Apache mit PHP-FPM, nginx mit PHP-FPM, LiteSpeed und die anderen | Klassische Server. Vollständig unterstützt und die Antwort, wo ein Hoster keinen dauerhaft laufenden Prozess erlaubt. |
| **Der eigene Server von Velocity** (`Engine=qbix`) | **Die empfohlene Engine für jede Stufe**, von der Entwicklung bis zur Produktion, und die schnellste. Liefert HTTP und HTTPS selbst aus. |
| **FrankenPHP** (`Engine=frankenphp`) | **Die produktionsreife Alternative**: Caddy mit eingebautem PHP, von `exp:velocity install` als eine einzige Binärdatei mit geprüfter Prüfsumme heruntergeladen. |
| Der eingebaute Server von PHP (`Engine=php`) | Nur für die Entwicklung; der mitgelieferte Standard, weil er immer funktioniert. |

Velocity läuft auch problemlos hinter Apache oder nginx als Reverse Proxy, sodass ein Hoster mit einem fein
abgestimmten Frontserver diesen behält. Wo sich mehrere Kunden eine Maschine teilen und die Dateien der anderen nicht
lesen dürfen, betreiben Sie eine Velocity-Instanz pro Site, jede unter ihrem eigenen Benutzer des Betriebssystems.
Die Einzelheiten stehen unter [Velocity-Engines](../bc/6.0/velocity-engines.md).

## Die Befehle

```bash
./console exp:velocity deploy --allow-root-user       # after a change: autoloads, settings, caches, PHP-FPM, Velocity
./console exp:velocity start --allow-root-user        # start the configured engine
./console exp:velocity restart --allow-root-user      # restart it, so workers load changed classes
./console exp:velocity cache clear --apply --allow-root-user  # empty Velocity's response cache
```

`deploy --dry-run` listet auf, was es tun würde, und tut sonst nichts; `deploy --kernel` erstellt zusätzlich die
Autoloads des Kernels neu, nachdem eine Klasse des Kernels hinzugefügt oder umbenannt wurde. Worker halten die
Anwendung zwischen den Anfragen im Speicher; nach einer Änderung an einer Klasse oder an Einstellungen braucht der
Server daher `deploy` (oder `restart`), nicht nur ein Leeren des Caches.

## Die dokumentierten Zahlen

Dies sind die Zahlen, die für diesen Stack bereits dokumentiert sind, mit ihrer Methode, in der
[README](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-velocity--the-fast-option):
**0,36 ms** Serverarbeit pro gecachter Seite (ein Median über Hunderte von Anfragen), **8.941 Anfragen pro Sekunde**
dauerhaft auf einer Maschine mit 12 Kernen, die eine echte Seite ohne einen einzigen Fehler bei bis zu 384
gleichzeitig offenen Verbindungen ausliefert, und das Neuladen eines wiederkehrenden Besuchers, das mit **202 Byte**
statt mit der ganzen Seite beantwortet wird. Die README sagt auch deutlich, dass der größte Teil der Geschwindigkeit
von der Verbesserung von Exponential und seinem Cache kam, was auch unter Apache und nginx hilft; Velocity hebt die
Obergrenze noch weiter an. Wie die Zahlen gemessen werden:
[How Velocity figures are measured](presse/thema-wie-die-velocity-zahlen-gemessen-werden.md).

## Was es für Sie bedeutet

- **Betreiber von Websites und Entscheider:** ein Produkt, auf das Sie setzen, ein Ort zum Nachlesen, eine
  Lieferantenbeziehung. Der Server ist kein zusätzliches Projekt.
- **Hosting-Anbieter:** Velocity ist auf jeder Site mit Exponential installiert und läuft auf dem PHP, das Ihre
  Maschinen bereits haben, 8.0 eingeschlossen. Bieten Sie es an, wo dauerhaft laufende Prozesse erlaubt sind;
  behalten Sie Apache oder nginx mit PHP-FPM, wo sie es nicht sind. Beginnen Sie bei
  [Hosting-Anbieter: hier beginnen](hosting/hosting-anbieter-hier-beginnen.md).
- **Entwickler:** Entwickeln Sie mit derselben Engine, die auch in der Produktion läuft, mit den RAD-Werkzeugen für
  Erweiterungen und mit WebSockets, dem Zeitplaner und dem Antwort-Cache, ohne etwas hinzufügen zu müssen.
- **Administratoren:** Ein Befehl stellt eine Änderung in der richtigen Reihenfolge bereit und hält beim ersten Fehler
  an.

## Fragen und Antworten

**Muss ich Velocity verwenden?**
Nein. Es wird mit jeder Installation mitgeliefert und ist die empfohlene Engine, aber Exponential läuft weiterhin
unter Apache, nginx, LiteSpeed und den anderen genau wie bisher.

**Funktioniert es unter Red Hat Enterprise Linux 9 mit dessen mitgeliefertem PHP 8.0?**
Ja. Velocity 0.0.4.54 läuft auf PHP 8.0 bis 8.5. Es braucht die PHP-Erweiterung `sockets`; `pcntl` und `openssl`
werden für den Worker-Pool und HTTPS empfohlen.

**Ist Velocity oder FrankenPHP experimentell?**
Nein. Velocity ist die empfohlene Engine für jede Stufe, und FrankenPHP ist eine produktionsreife Alternative.

**Muss ich beim Upgrade auf 6.0.15 etwas ändern?**
Führen Sie wie gewohnt `composer install` aus; es installiert den Server. Nichts wechselt von selbst die Engine.

## Weiterlesen

- [Velocity-Webserver](../funktionen/6.0/velocity-webserver.md) und
  [Exponential in einem Webserver mit persistenten Workern betreiben](../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Velocity-Engines: Qbix, FrankenPHP, der eingebaute Server von PHP](../bc/6.0/velocity-engines.md)
- [Installation, Kapitel 8: Die Website ausliefern](../installation/08-die-website-ausliefern.md)
- [Das Changelog von 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Betriebssystemunterstützung](betriebssystemunterstuetzung.md)
