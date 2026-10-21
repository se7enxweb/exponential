---
title: Sicherheitsupdates und Offenlegung
audience: hosting, admin
category: hosting
order: 100
summary: Welche Versionen von Exponential Sicherheitskorrekturen erhalten, wie Korrekturen veröffentlicht werden, wie ein Hoster Releases verfolgt und Updates auf Kunden-Websites ausrollt, wie Sie prüfen, dass Dateien nicht verändert wurden, was sonst noch Updates braucht und wie Sie eine Schwachstelle vertraulich melden.
keywords: Sicherheitsupdates, unterstützte Versionen, SECURITY.md, Releases, unveränderliche Tags, Changelog, composer outdated, composer audit, Upgrade-Prüfung, filelist.md5, checkmanifest.php, ezupdate, Schwachstelle, Offenlegung, security@se7enx.com, Sicherheitshinweis, Patch-Management, security updates, supported versions, immutable tags, upgrade check, vulnerability, disclosure, advisory, patch management
type: help
---
# Sicherheitsupdates und Offenlegung

Diese Seite ist für die Sicherheitsverantwortlichen und die Systemadministratoren eines Hosters, die viele Exponential-Websites aktuell
halten, und für Support-Mitarbeitende, die eine Meldung über eine mögliche Schwachstelle erhalten. Sie sagt, welche Versionen unterstützt
werden, wie eine Korrektur eine Website erreicht und wie Sie ein Problem melden. Die Richtlinie selbst ist die
[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) des Repositorys; die Routine für Updates ist
[13.15 Aktuell bleiben](../../../installation/13-sicherheitshaertung.md#1315-auf-dem-neuesten-stand-bleiben).

## Inhalt

1. [Unterstützte Versionen](#unterstützte-versionen)
2. [Wie Korrekturen veröffentlicht werden](#wie-korrekturen-veröffentlicht-werden)
3. [Releases verfolgen](#releases-verfolgen)
4. [Updates ausrollen](#updates-ausrollen)
5. [Dateien nach einem Update prüfen](#dateien-nach-einem-update-prüfen)
6. [Alles andere, was Updates braucht](#alles-andere-was-updates-braucht)
7. [Eine Schwachstelle melden](#eine-schwachstelle-melden)
8. [Kunden informieren](#kunden-informieren)
9. [Checkliste](#checkliste)

## Unterstützte Versionen

Aus `SECURITY.md`:

| Version | Unterstützt |
|---|---|
| 6.0.x, aktuelle Linie: `main` (`dev-main`) und das nächste daraus erstellte Tag | ja |
| 6.0.x, veröffentlichte Tags `v6.0.0` bis `v6.0.14` | durch ein Upgrade auf die neueste 6.0.x korrigiert |
| 5.x, 4.x, 3.x und älter | nein |

Sicherheitskorrekturen werden auf `main` gemacht und mit dem nächsten Release 6.0.x veröffentlicht. Websites auf älteren Linien,
einschließlich derer, die noch das Produkt betreiben, das sein Hersteller nach fast 18 Jahren eingestellt hat, erhalten keine Korrekturen;
planen Sie ihre Migration ([Kunden migrieren: Überblick](../migrationen/README.md)).

## Wie Korrekturen veröffentlicht werden

**Ein veröffentlichtes Tag wird nie geändert.** Eine Korrektur für ein Problem in einem früheren Tag erreicht Sie als nächstes Release,
nicht als korrigierte Kopie des alten. Releases sind unveränderliche Tags auf Packagist und GitHub, mit Changelogs. Für einen Hoster heißt
das:

- Die Versionsnummer, die eine Website meldet, ist verlässlich: Dieselbe Version ist überall derselbe Code.
- Die Frage, ob eine Website gepatcht ist, wird beantwortet, indem Sie ihre Version mit dem Release vergleichen, das das Problem
  korrigiert hat.
- Jede Erweiterung ist ein eigenes Composer-Paket mit eigenen Releases; verfolgen Sie auch diese.

## Releases verfolgen

```bash
composer outdated                 # what is newer than the locked versions
composer audit                    # known vulnerabilities in the locked dependencies
composer show se7enxweb/exponential   # the version a site runs
```

Führen Sie diese pro Installation aus einem zentralen Job aus und sammeln Sie die Ergebnisse, oder nutzen Sie den Bildschirm
**Setup > Updates and packages** der Erweiterung ezupdate, dessen Suche nach Updates nichts ändert
([ezupdate](../../../funktionen/6.0/erweiterungen/ezupdate.md),
[Automatische Updates mit ezupdate](../betrieb/automatische-updates-mit-ezupdate.md)). Halten Sie `composer.lock` in der
Versionsverwaltung jeder Website, damit das Ergebnis auf jeder Maschine dasselbe ist.

## Updates ausrollen

1. **Lesen Sie den Changelog und die Hinweise zum Upgrade** des Release ([Upgrades](../../../anleitungen/aktualisieren.md),
   [Kapitel 11](../../../installation/11-aktualisieren.md)).
2. **Aktualisieren Sie zuerst eine Kopie** (eine Staging-Kopie der Website des Kunden oder eine repräsentative Test-Website).
3. **Dann die Live-Website**: `composer update <package>`, die Datenbank-Updates, die das Release nennt, dann
   `php bin/php/console exp:velocity deploy` (oder Autoloads, Leeren des Caches und ein Neuladen von PHP-FPM von Hand).
4. **Prüfen Sie die Konsistenz der Dateien** (unten).
5. **Machen Sie einen Rauchtest** der Startseite und der Anmeldung der Administration.

Für viele Websites auf einmal: [Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md) und
[Änderungen ausrollen](../betrieb/aenderungen-ausrollen.md). Nutzen Sie den [Wartungsmodus](../betrieb/wartungsmodus.md), wo das
Update Änderungen an der Datenbank braucht.

> **Hinweis**
> Ob Sicherheitsupdates vom Hoster automatisch, in einem festen Zeitfenster oder nur mit Zustimmung des Kunden eingespielt werden, ist eine
> Entscheidung für jeden Tarif. Die Quellen setzen keinen Zeitrahmen; der Hoster wählt einen und nennt ihn in seiner Leistungsbeschreibung
> ([SLA-Optionen](../geschaeft/sla-optionen-fuer-exponential-hosting.md)).

## Dateien nach einem Update prüfen

**Setup > Upgrade-Prüfung** vergleicht jede Datei mit der `share/filelist.md5` des Release (**Überprüfe Dateikonsistenz**) und das
Schema mit dem des Release (**Überprüfe Datenbankkonsistenz**). Beide lesen nur. Dasselbe aus einer Shell, ohne Datenbank:

```bash
php bin/php/checkmanifest.php --all                 # exit 0 when nothing is modified or missing
php bin/php/checkmanifest.php --all --extensions    # also extensions with their own manifest
```

Eine veränderte Datei des Kernels, die niemand geändert hat, verdient einen Blick; siehe [Kompromittierte Website](../runbooks/kompromittierte-website.md)
([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).

## Alles andere, was Updates braucht

- **PHP**, der **Datenbankserver** und das **Betriebssystem**.
- **Die Engine von Velocity** (auch ihre Releases korrigieren Sicherheitsprobleme), dann `exp:velocity deploy`.
- **Die Binärdatei von FrankenPHP**: Version und Prüfsummen sind in `[FrankenPHPSettings] Version` und `Sha256[]` festgelegt; ändern Sie
  sie zusammen.
- **Die eigenen Erweiterungen des Kunden**: Prüfen Sie sie auf die Muster, die im Kernel korrigiert wurden (SQL aus Anfragedaten gebaut,
  Shell-Befehle ohne `escapeshellarg()`, Ausgabe ohne Maskierung), und erzeugen Sie Erweiterungen neu, die von den RAD-Assistenten vor
  deren Korrektur erstellt wurden ([RAD-Werkzeuge: Sicherheit](../../../bc/6.0/rad-sicherheit.md),
  [Hinweise zur Härtung in 6.0.13](../../../bc/6.0/haertung.md)).
- **request-shield**, wenn installiert, als eigenes Paket.

## Eine Schwachstelle melden

Melden Sie Sicherheitsprobleme **vertraulich**, nie in einem öffentlichen Issue-Tracker oder einer Diskussion:

- per Mail an **security@se7enx.com** oder
- als vertraulichen Sicherheitshinweis auf GitHub: <https://github.com/se7enxweb/exponential/security/advisories/new>.

`SECURITY.md` sagt, dass eine erste Antwort innerhalb eines Werktags zu erwarten ist. Geben Sie an:

- die Version (`composer show se7enxweb/exponential` oder das Tag oder den Commit) und die PHP-Version;
- die Engine, die die Website ausliefert (Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM), und die Datenbank;
- was ein Angreifer tun kann, und die Schritte, um es nachzustellen.

Lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg, auch die des Kunden, dessen Website das Problem gezeigt hat. Die
meldende Person erfährt, ob die Meldung angenommen wird, und wird auf dem Laufenden gehalten, während eine Korrektur vorbereitet wird; ein
angenommenes Problem wird auf `main` korrigiert und mit der nächsten Version 6.0.x veröffentlicht. Probleme in einer Erweiterung von
Dritten gehen an deren Betreuer; die [Seite zum Melden](../../wenn-etwas-schiefgeht.md#sicherheitsprobleme) der Hilfe erklärt die
Tracker.

## Kunden informieren

Wenn ein Release ein Sicherheitsproblem korrigiert, kann ein Hoster wählen:

- betroffene Kunden zu informieren, welche Websites wann aktualisiert wurden;
- für Websites, die nicht sofort aktualisiert werden können, die Lücke mit einem Datum zu dokumentieren, bis zu dem sie geschlossen wird,
  wie die [Checkliste für die Sicherheitsprüfung](../checklisten/checkliste-fuer-die-sicherheitspruefung.md) es verlangt;
- nie Details eines nicht korrigierten Problems zu veröffentlichen, bevor das Projekt die Korrektur veröffentlicht hat.

## Checkliste

- [ ] Jede Website auf der neuesten 6.0.x, oder die Lücke mit einem Datum dokumentiert.
- [ ] `composer outdated` und `composer audit` regelmäßig für jede Installation ausgeführt; `composer.lock` in der Versionsverwaltung.
- [ ] Updates zuerst auf einer Kopie getestet; Konsistenz der Dateien nach jedem Update geprüft.
- [ ] PHP, Datenbankserver, Betriebssystem, die Engine von Velocity und Erweiterungen in der Routine enthalten.
- [ ] Eine benannte verantwortliche Person, die Release-Hinweise liest und Sicherheitsmails empfängt.
- [ ] Support-Mitarbeitende kennen den vertraulichen Meldeweg und nutzen nie einen öffentlichen Tracker für Sicherheitsmeldungen.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)
- [Sicherheitshärtung: Eine Schwachstelle melden](../../sicherheitshaertung.md#eine-schwachstelle-melden)
- [Mit dem Projekt und 7x zusammenarbeiten](../geschaeft/mit-dem-projekt-und-7x-zusammenarbeiten.md)
- [Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
