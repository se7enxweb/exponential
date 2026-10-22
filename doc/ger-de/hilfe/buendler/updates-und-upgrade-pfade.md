---
title: Updates und Upgrade-Pfade für gebündelte Installationen
audience: bundler, hosting, admin
category: bundling
order: 420
summary: Wie gebündelte Installationen neue Exponential-Releases erhalten - die zwei Hälften jedes Updates (Code und Datenbank), die Update-Wege (neues Image, Paketmanager, Git und Composer, der Git Manager der Administration und ezupdate), wer was tut, wie Sie Updates ankündigen und Sicherheitsreleases.
keywords: Update, Upgrade, Upgrade-Pfad, Release, Sicherheitskorrektur, Paketmanager, Wartungsmodus, update, upgrade, upgrade path, release, security fix, dbupdate, update/database, Git, Composer, Git Manager, ezupdate, image, container, package manager, maintenance mode, bundle
type: help
---
# Updates und Upgrade-Pfade für gebündelte Installationen

Diese Seite richtet sich an Bündler, die die aus ihrem Bundle entstandenen Installationen aktuell halten müssen.
Legen Sie den Update-Weg **vor** dem ersten Release fest: Die Empfänger werden am ersten Tag, an dem eine Korrektur
erscheint, fragen, wie sie aktualisieren.

## Inhalt

1. [Zwei Hälften jedes Updates](#zwei-hälften-jedes-updates)
2. [Update-Wege je Art von Bundle](#update-wege-je-art-von-bundle)
3. [Die Routine auf einer Installation](#die-routine-auf-einer-installation)
4. [Wer was tut](#wer-was-tut)
5. [Sicherheitsreleases](#sicherheitsreleases)
6. [Updates ankündigen](#updates-ankündigen)

## Zwei Hälften jedes Updates

1. **Der Code**: der Kernel und die Erweiterungen, aus einem neuen Tag und einer neuen Lock-Datei.
2. **Die Datenbank**: die Update-Datei und die Skripte des Releases. Für die Linie 6.0 ist die Datei
   `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, für MySQL, PostgreSQL und SQLite; "Wenden Sie sie
   **einmal, vollständig und in Reihenfolge** an"
   ([11.6 Schritt 2](../../installation/11-aktualisieren.md#schritt-2-das-datenbank-update-anwenden)). Releases können auch Skripte
   ergänzen, etwa `php update/common/scripts/6.0/createaudittables.php` für den Audit-Index.

"Den Code zu bewegen ist nur die Hälfte eines Upgrades: Die Datenbank-Update-Dateien aus Kapitel 11 sind die andere
Hälfte" ([3.2 A](../../installation/03-den-code-beschaffen.md#a-git-clone-und-composer-install-empfohlen)). Lesen Sie dann
"die Checkliste jedes Zeitraums, den Sie durchlaufen"
([11.6](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand)).

## Update-Wege je Art von Bundle

| Bundle | Weg für den Code | Weg für die Datenbank |
|---|---|---|
| Container-Image | ein neues Image; die Volumes behalten `var/` und die Einstellungen | ein einmaliger Job mit dem neuen Image, bevor die neuen Container Verkehr übernehmen |
| Server-Image, Appliance | Ihr Update-Kanal oder Git und Composer auf der Box | derselbe Kanal, als separater, protokollierter Schritt |
| Panel-Installer | die Update-Schaltfläche des Panels oder Git und Composer des Kunden | der Kunde oder das Panel, mit verlinkter Anleitung |
| Distributionspaket | der Paketmanager | der Administrator, nach dem Hinweis, den Ihr Paket ausgibt ([Pakete für Linux-Distributionen](pakete-fuer-linux-distributionen.md)) |
| Starter-Kit-Projekt | Git und Composer im Projekt ([Mit Git aktualisieren](../mit-git-aktualisieren.md)) | das Projektteam |

## Die Routine auf einer Installation

[Mit Git aktualisieren](../mit-git-aktualisieren.md) nennt sieben Schritte für die Kommandozeile und die Administration:

| Schritt | Was |
|---|---|
| 1 | Sichern, das Wartungsfenster beginnen (`php bin/php/maintenance.php on`) |
| 2 | Sehen, was ansteht |
| 3 | Den Kernel mit Git aktualisieren |
| 4 | Die Erweiterungen mit Composer aktualisieren |
| 5 | Dateien und Datenbank prüfen, das Datenbank-Update anwenden |
| 6 | Die Caches leeren, den Code neu laden (`exp:velocity deploy` auf einer Velocity-Website) |
| 7 | Die Website prüfen, das Fenster verlassen (`php bin/php/maintenance.php off`) |

In der Administration zieht der **Git Manager** einen Branch, und **Setup > Updates and packages** (ezupdate) führt
Composer aus; "ein Release-Tag, ein Merge-Konflikt und der Neustart von Velocity brauchen weiterhin die
Kommandozeile". Das Aktualisieren in ezupdate ist "aus, bis `[UpdateSettings] AllowUpdate` eingeschaltet wird"
([Automatisierte Updates mit ezupdate](../hosting/betrieb/automatische-updates-mit-ezupdate.md)).

Folgen Sie bei Live-Websites "nur fertigen, getesteten Versionen": "dem neuesten **Release-Tag**"
([Wählen Sie, wie eng Sie folgen](../mit-git-aktualisieren.md#wählen-sie-wie-eng-sie-folgen)).

## Wer was tut

Schreiben Sie es in Ihre Bedingungen und Ihren Text für die ersten Schritte:

```text
We deliver:        new bundle releases within <time> of an Exponential release
We test:           <databases, PHP versions, upgrade from the last <n> bundle versions>
The recipient does: backups before updating; applying the update; their own extensions
Database updates:  <who runs them, how>
End of updates:    <date or policy per bundle version>
```

## Sicherheitsreleases

- "Sicherheitskorrekturen werden auf dem Branch `main` (der Linie 6.0.15, `dev-main`) vorgenommen und mit dem nächsten
  6.0.x-Release veröffentlicht. Ein veröffentlichter Tag wird nie geändert" (`SECURITY.md`).
- Beobachten Sie die Releases unter <https://github.com/se7enxweb/exponential/releases> und die Kanäle des Projekts;
  Partner erhalten Informationen über das Partnerprogramm
  ([Kunden-Websites aktuell halten](../partner/kunden-websites-aktuell-halten.md)).
- Veröffentlichen Sie zügig ein Bundle-Update und sagen Sie in dessen Hinweisen, welches Sicherheitsrelease es
  enthält.

## Updates ankündigen

- Ein Änderungsprotokoll je Bundle-Release, das die enthaltene Exponential-Version nennt.
- Die Datenbankschritte, ausdrücklich, auch wenn es keine gibt.
- Ein Hinweis, wenn ein voriges Bundle-Release fehlerhaft war, statt es zu ändern
  ([Versionen, Tags und Pinning](versionen-tags-und-pinning.md#ein-veröffentlichtes-release-ändert-sich-nie)).

## Verwandte Seiten

- [Kapitel 11: Upgrade](../../installation/11-aktualisieren.md)
- [Kunden-Websites aktualisieren](../hosting/betrieb/kunden-websites-aktualisieren.md)
- [Ein Bundle testen](ein-bundle-testen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
