---
title: Trennung von Mandanten
audience: hosting
category: hosting
order: 110
summary: Wie weit die Exponential-Website eines Kunden auf geteilter Infrastruktur von der eines anderen getrennt ist - was Exponential trennt und was es dem Betriebssystem überlässt, die Grenze, die jede Einrichtung gibt (geteilter Prozess, ein Pool oder Velocity pro Website, Container, getrennte Maschinen), Installationen mit mehreren Websites, geteilte Pfade wie TemporaryDir, Datenbanken, Caches und Logs, und eine Entscheidungstabelle.
keywords: Mandantenfähigkeit, Trennung, Mandant, Trennung von Kunden, Shared Hosting, PHP-FPM-Pool, Velocity-Instanz, Benutzer des Betriebssystems, Container, mehrere Websites, Siteaccess, TemporaryDir, open_basedir, Datenbankbenutzer, Sitzungen, Caches, Logs, Sicherheitsgrenze, multi-tenant, isolation, tenant, customer separation, shared hosting, operating-system user, containers, multi-site, database user, sessions, security boundary
featured: true
type: help
---
# Trennung von Mandanten

Diese Seite ist für die Produktmanager, Architekten und Sicherheitsverantwortlichen eines Hosters, die entscheiden, wie Kunden sich
Maschinen teilen. Die Frage, die sie beantwortet: Wenn die Website eines Kunden kompromittiert wird oder einfach schlecht geschrieben ist,
was kann sie sonst noch erreichen? Die kurze Antwort aus dem README des Projekts: **Ein einziger Serverprozess, der mehrere Websites
ausliefert, ist eine Bequemlichkeit beim Routing, keine Sicherheitsgrenze.** Die Grenze kommt vom Betriebssystem: getrennte Benutzer,
getrennte Prozesse, getrennte Datenbanken.

## Inhalt

1. [Was Exponential trennt und was nicht](#was-exponential-trennt-und-was-nicht)
2. [Die Einrichtungen und ihre Grenzen](#die-einrichtungen-und-ihre-grenzen)
3. [Mehrere Websites auf einer Installation](#mehrere-websites-auf-einer-installation)
4. [Geteilte Pfade, auf die Sie achten müssen](#geteilte-pfade-auf-die-sie-achten-müssen)
5. [Datenbanken](#datenbanken)
6. [Caches, Sitzungen und Logs](#caches-sitzungen-und-logs)
7. [Wählen](#wählen)
8. [Checkliste](#checkliste)

## Was Exponential trennt und was nicht

Innerhalb einer Installation trennt Exponential **Benutzer** durch Rollen und Richtlinien, **Siteaccesses** durch eigene Einstellungen,
und es kann jedem Siteaccess ein eigenes Sitzungscookie geben (`SessionNameHandler=custom`). Das sind Grenzen der Anwendung: Sie halten,
solange der PHP-Code nicht kompromittiert ist.

Zwischen **Installationen** bietet Exponential nichts Eigenes und braucht nichts Besonderes: Jede Installation ist ein Verzeichnis, eine
Datenbank und eine Gruppe von Prozessen. Ob ein Kunde die Dateien eines anderen lesen kann, hängt nur davon ab, wie der Hoster diese
Prozesse betreibt. Das README sagt es deutlich: Wenn mehrere Kunden die Daten der anderen nicht lesen dürfen, betreiben Sie eine
Velocity-Instanz pro Website hinter einem Proxy, jede als eigener Benutzer des Betriebssystems, denn der Prozess, der eine Website
ausliefert, kann jede Datei lesen, die die anderen halten.

Und aus [Kapitel 8](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle): wenn mehrere Kunden auf einer Maschine voneinander
auf Ebene des Betriebssystems getrennt sein müssen (eine Velocity pro Website, jede als eigener Benutzer, oder ein klassischer Server mit
einem PHP-FPM-Pool pro Website).

## Die Einrichtungen und ihre Grenzen

| Einrichtung | Grenze zwischen Kunden | Hinweise |
|---|---|---|
| Eine Installation, mehrere Kunden als Siteaccesses | **keine** auf Ebene des Betriebssystems: eine Codebasis, ein PHP-Benutzer, meist eine Datenbank | nur für Websites einer Organisation ([Mehrere Websites auf einer Installation](#mehrere-websites-auf-einer-installation)) |
| Mehrere Installationen, ein geteilter PHP-Benutzer oder eine geteilte Velocity | **keine**: Der geteilte Prozess kann jede Installation lesen | für verschiedene Kunden vermeiden |
| Ein PHP-FPM-Pool pro Website, jeder als eigener Benutzer | Benutzer des Betriebssystems und Dateiberechtigungen | das klassische Modell des Shared Hostings; [Benutzer und Pools pro Kunde](benutzer-und-pools-pro-kunde.md) |
| Eine Velocity-Instanz pro Website, jede als eigener Benutzer | Benutzer des Betriebssystems; ein eigener Prozessbaum | hinter einem Proxy, der nach Hostnamen routet; [Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md) |
| Ein Container pro Website | die Trennung der Container-Laufzeit, plus ein eigener Benutzer | [Container](../modelle/container-docker-und-podman.md), [Kubernetes](../modelle/kubernetes.md) |
| Eine virtuelle oder physische Maschine pro Kunde | der Hypervisor oder die Hardware | [VPS-Hosting](../modelle/vps-hosting.md), [Dedizierte Server](../modelle/dedizierte-server.md) |

Das eigene Prozessmodell von Velocity innerhalb einer Website: Der Elternprozess darf root bleiben, um Ports zu binden und Zertifikate zu
lesen, und jeder Worker gibt root auf, bevor Code der Anwendung läuft; `[ServerSettings] User` und `Group` entscheiden, wer er wird, und
Worker als root werden verweigert, es sei denn `AllowRootWorkers=enabled`
([8.3.5](../../../installation/08-die-website-ausliefern.md#835-adresse-ports-benutzer-und-gruppe)). Persistente Worker bedienen viele Anfragen **derselben**
Website; `ForkPerRequest=enabled` gibt eine Anfrage pro Worker, also dieselbe Trennung wie bei PHP-FPM
([8.3.7](../../../installation/08-die-website-ausliefern.md#837-persistente-worker-und-keepglobals)). Keine der beiden Einstellungen trennt Kunden;
getrennte Instanzen tun es.

## Mehrere Websites auf einer Installation

Exponential kann viele Websites aus einer Installation ausliefern: jede Website ein Siteaccess oder eine Website-Erweiterung mit eigenem
`VarDir`, eigenen Logs (`UseGlobalLogDir=disabled`, `LogVarDir`), Caches (`CacheVarDir`) und INI-Cache (`INICacheDir=site`)
([Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)). Das hält die Daten der
Websites für den Betrieb auseinander, nicht für die Sicherheit: Ein PHP-Benutzer schreibt sie alle, und ein Administrator mit Richtlinien
`setup/*` auf einem Siteaccess arbeitet an der geteilten Installation. Nutzen Sie es für die Websites eines Kunden oder einer
Organisation, nicht, um Kunden ohne Bezug zueinander zu trennen. Einrichtung:
[Mehrere Websites auf einer Installation](../bereitstellung/mehrere-websites-in-einer-installation.md).

## Geteilte Pfade, auf die Sie achten müssen

Selbst mit einem Benutzer pro Website zeigen einige Standards auf Orte, die jeder Benutzer der Maschine teilt:

| Pfad oder Einstellung | Standard | Was zu tun ist |
|---|---|---|
| `site.ini [FileSettings] TemporaryDir` | `/tmp/` | ein eigenes Verzeichnis der Website unter `var/` ([13.11](../../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer)) |
| Berechtigungen in `[FileSettings]` | `0777` für Verzeichnisse, `0666` für Dateien | `0770` / `0660` mit der Gruppe der Website |
| Sitzungsspeicher von PHP | `session.save_handler` der `php.ini` (files), wenn `[Session] Handler` leer ist | ein Sitzungsverzeichnis pro Pool oder der Sitzungs-Handler der Datenbank |
| `upload_tmp_dir` von PHP | der Standard des Systems | pro Pool, im Bereich der Website |
| Log- und Cache-Verzeichnisse von Velocity | `var/vc/qbix/log` (Dateien `0640`, Verzeichnis `0750`) | bereits in jeder Installation |
| Konfiguration von Velocity | `/etc/vc/sites-available/<site>.conf`, Modus `0600`, eine pro Installation | den Modus behalten; sie enthält Pfade zu Zertifikaten und Tokens |

Panels setzen oft auch `open_basedir` pro Konto. Der Setup-Assistent warnt nur davor (die Einstellung kann wegen Fehlern in einigen
PHP-Versionen Probleme beim Betrieb von Exponential verursachen); testen Sie eine Website unter der Einstellung Ihres Panels, bevor Sie sich
darauf verlassen ([Shared Hosting](../modelle/shared-hosting.md)).

## Datenbanken

Geben Sie jeder Installation eine eigene Datenbank und einen eigenen Datenbankbenutzer mit Rechten nur auf diese Datenbank. Die
Verbindung, einschließlich des Passworts, steht in `settings/override/site.ini.append.php` der Installation; halten Sie diese Datei auf
`0640` oder strenger, damit andere Benutzer sie nicht lesen können ([Datenbanken und Benutzer](../control-panels/datenbanken-und-benutzer.md)). Die
unterstützten Datenbanken in der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB ·
5. Oracle · 6. MySQL; Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich
([Datenbankunterstützung](../../datenbankunterstuetzung.md)). Mit SQLite 3 ist die Datenbank eine Datei unterhalb der Installation
(standardmäßig `var/storage/sqlite3/`), sodass die Dateiberechtigungen die ganze Grenze sind.

## Caches, Sitzungen und Logs

- **Caches** gibt es pro Installation (`var/`); mit einem geteilten Reverse Proxy oder CDN davor stellen Sie sicher, dass die Schlüssel
  den Hostnamen enthalten und angemeldete Seiten nie gespeichert werden (13.10, „Zwischenspeichernde Proxys“).
- **Sitzungen**: ein Cookie pro Siteaccess mit `SessionNameHandler=custom`; verschiedene Installationen auf verschiedenen Hostnamen teilen
  nie Cookies, es sei denn, eine Cookie-Domain ist weiter als der Host gesetzt.
- **Logs** nennen Besucher. Halten Sie die Logs jeder Website in ihrem eigenen Baum und nur für ihren Benutzer lesbar; eine zentrale
  Log-Plattform, die sie sammelt, braucht eigene Zugriffsregeln pro Kunde.

## Wählen

| Bedarf des Kunden | Vorgeschlagenes Modell |
|---|---|
| Kleine Websites, preissensibel, keine Shell | ein PHP-FPM-Pool und Benutzer pro Website, geteilte Maschine |
| Leistung von Velocity, dennoch auf einer geteilten Maschine | eine Velocity-Instanz pro Website, jede als eigener Benutzer, hinter einem Proxy |
| Regulierte Daten, strikte Trennung vertraglich verlangt | eigener Container oder eigene virtuelle Maschine, eigener Datenbankserver oder eigene Instanz |
| Viele Websites einer Organisation | eine Installation mit mehreren Websites ist möglich; getrennte Installationen, wenn Abteilungen einander nicht sehen dürfen |

Das sind Möglichkeiten; der Hoster wählt und nennt das Modell in seinen Tarifen
([Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md)).

## Checkliste

- [ ] Die Installation jedes Kunden läuft als eigener Benutzer des Betriebssystems, nie als geteilter PHP-Benutzer oder geteilte
      Velocity.
- [ ] Code für diesen Benutzer nur lesbar; `var/` und `settings/` nur für ihn und seine Gruppe beschreibbar.
- [ ] Keine für alle beschreibbaren Berechtigungen; `TemporaryDir`, Sitzungen und Uploads in Verzeichnissen pro Website.
- [ ] Eine Datenbank und ein Datenbankbenutzer pro Installation; `settings/override` mit Modus `0640` oder strenger.
- [ ] Installationen mit mehreren Websites nur innerhalb einer Organisation genutzt.
- [ ] Geteilte Proxys und CDNs speichern nie angemeldete Seiten.
- [ ] Das Modell der Trennung in der Tarifbeschreibung festgehalten.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Benutzer und Pools pro Kunde](benutzer-und-pools-pro-kunde.md)
- [Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md)
- [Shared Hosting](../modelle/shared-hosting.md)
- [Kapitel 8: Dateiberechtigungen und Eigentum](../../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
