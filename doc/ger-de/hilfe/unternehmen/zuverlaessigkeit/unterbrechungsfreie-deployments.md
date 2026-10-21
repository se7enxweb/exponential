---
title: Unterbrechungsfreie Deployments
audience: businesses, admin, developer, project-manager
category: business-solutions
order: 100
summary: Wie Sie Änderungen auf eine laufende Exponential-Website bringen, ohne Anfragen von Besuchern zu verlieren oder veraltete Seiten auszuliefern, was das Deployment mit einem Befehl tut und welche Regeln ein Unternehmen dafür festlegen sollte, wann und wie Änderungen live gehen.
keywords: Deployment, unterbrechungsfrei, sanfter Neustart, ohne Ausfallzeit, PHP-FPM neu laden, Cache-Reihenfolge, Änderungsfenster, Zurückrollen, Release, deploy, graceful, graceful restart, zero downtime, exp:velocity deploy, PHP-FPM reload, cache order, change window, rollback, release
type: help
---
# Unterbrechungsfreie Deployments

Diese Seite richtet sich an Führungskräfte, die Änderungen an der Live-Website genehmigen, und an den Entwickler oder die
Person im Betrieb, die sie ausliefert. Sie erklärt, was bei einem Deployment schiefgehen kann, wie die Deploy-Werkzeuge
von Exponential verlorene Anfragen und veraltete Seiten vermeiden und auf welche Regeln sich ein Unternehmen einigen
sollte. Die technische Anleitung ist
[Bereitstellung, eine PHP-Änderung mit einem Befehl ausliefern](../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern).

## Inhalt

1. [Was bei einem unbedachten Deployment schiefgeht](#was-bei-einem-unbedachten-deployment-schiefgeht)
2. [Sanftes Neuladen: keine verlorenen Anfragen](#sanftes-neuladen-keine-verlorenen-anfragen)
3. [Das Deployment mit einem Befehl](#das-deployment-mit-einem-befehl)
4. [Warum die Reihenfolge der Schritte wichtig ist](#warum-die-reihenfolge-der-schritte-wichtig-ist)
5. [Kleine Änderungen brauchen weniger](#kleine-änderungen-brauchen-weniger)
6. [Wann ein Deployment ein Wartungsfenster braucht](#wann-ein-deployment-ein-wartungsfenster-braucht)
7. [Regeln für das Unternehmen](#regeln-für-das-unternehmen)
8. [Checkliste](#checkliste)

## Was bei einem unbedachten Deployment schiefgeht

- **Verlorene Anfragen**: Ein hart neu gestarteter Server lehnt für einen Moment Verbindungen ab, und ein Kunde mitten
  im Bestellvorgang bekommt einen Fehler.
- **Alter Code läuft weiter**: PHP-FPM und Velocity halten Klassen im Speicher. „Führen Sie nie nur
  `exp:cache clear --all` aus und vergessen dann den Webserver: FPM und Velocity behalten die alten Klassen im
  Speicher, bis sie neu geladen werden“ ([Bereitstellung](../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)).
- **Veraltete Seiten werden erneut gecacht**: Werden die Seiten-Caches geleert, bevor der Server den neuen Code ausführt,
  wird eine in der Zwischenzeit vom alten Code gerenderte Seite erneut gespeichert und ausgeliefert, als hätte die
  Änderung nicht funktioniert ([Betrieb einer Site](../../../anleitungen/betrieb-einer-website.md#die-caches-vor-php)).
- **Ein langsamer erster Besucher**: Nach dem Leeren eines Caches bezahlt der erste Besucher für das Rendern.

## Sanftes Neuladen: keine verlorenen Anfragen

Unter Exponential Velocity „führt `graceful` den Server neu aus, ohne den lauschenden Socket freizugeben, sodass ein
Deployment keine Anfragen verliert“ ([README, „Using it“](https://github.com/se7enxweb/exponential/blob/main/README.md#using-it)):

```bash
php bin/php/console exp:velocity graceful --allow-root-user
```

Was jede Engine bei `graceful` tut, aus [Velocity-Engines](../../../bc/6.0/velocity-engines.md#kurz-gesagt):

| Engine | `graceful` |
|---|---|
| Qbix | Neuausführung, Socket bleibt erhalten |
| FrankenPHP | Neuladen der Konfiguration, keine abgebrochene Verbindung |
| Eingebauter Server von PHP | Neustart (nur für die Entwicklung) |

Wenn sich mehrere Qbix-Instanzen einen Port teilen, „lädt `graceful` sie nacheinander neu, sodass der Port weiter
antwortet“ ([Velocity-Engines](../../../bc/6.0/velocity-engines.md#mehrere-qbix-server-auf-einem-port-instances)). Ab
Engine 0.0.4.50 lässt auch ein Stopp HTTP/2-Verbindungen sanft auslaufen (dieselbe Seite).

Unter Apache oder Nginx mit PHP-FPM übernimmt ein sanftes Neuladen des PHP-FPM-Dienstes dieselbe Rolle; siehe
[Bereitstellung, nach jeder PHP-Änderung](../../../anleitungen/bereitstellung.md#34-nach-jeder-php-änderung-das-richtige-php-fpm-neu-laden).

## Das Deployment mit einem Befehl

Für eine Änderung an PHP-Code, einem Operator oder einer Einstellungsdatei führt ein einziger Befehl alle Schritte in der
richtigen Reihenfolge aus ([Bereitstellung](../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)):

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do, nothing else
php bin/php/console exp:velocity deploy --allow-root-user             # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

Was ein Unternehmen darüber wissen sollte:

- Der **Probelauf** gibt jeden Schritt aus, ohne etwas zu ändern. Verlangen Sie, dass er Teil jedes Änderungsantrags
  ist.
- Ein echter Lauf gibt für jeden Schritt PASS, FAIL oder SKIP und die Dauer aus und **stoppt beim ersten Fehler** mit
  dem Exit-Status 1. Ein fehlgeschlagenes Deployment läuft nicht halb fertig weiter.
- Der letzte Schritt ruft im Hintergrund Adressen zum Vorwärmen auf (`[DeploySettings] WarmUpURL[]`), „damit der erste
  Besucher die Caches bereits aufgebaut vorfindet“.
- Optionen decken Websites ohne Velocity (`--no-velocity`, nur Apache) und Websites ohne PHP-FPM (`--no-fpm`) ab.
- Ein Deployment, dessen Aufrufer zwischen Stopp und Start beendet wird, schließt von selbst ab, sodass der Server nicht
  ausgefallen zurückbleibt
  ([Velocity-Engines](../../../bc/6.0/velocity-engines.md#ein-abgeschnittener-neustart-oder-deploy)).

## Warum die Reihenfolge der Schritte wichtig ist

„Die Reihenfolge ist wichtig: Die Caches, die fertige Seiten enthalten, werden zuletzt geleert, nachdem PHP-FPM und
Velocity den neuen Code ausführen, sodass eine vom alten Code gerenderte Seite nie erneut gecacht wird“
([Bereitstellung](../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)). Das Deployment vermeidet
außerdem ein unnötiges Neuladen von PHP-FPM, wenn OPcache die neuen Dateien von selbst übernimmt, denn „ein Neuladen
hält jeden Pool dieser PHP-Version an, bis seine längste Anfrage endet“ (dieselbe Seite).

## Kleine Änderungen brauchen weniger

Nicht jede Änderung braucht ein vollständiges Deployment (dieselbe Seite):

| Änderung | Was sie braucht |
|---|---|
| Ein Template oder ein Stylesheet | Template-Cache und Antwortcache von Velocity leeren; kein Neustart |
| Eine Einstellungsdatei, eine PHP-Klasse oder ein Operator | `exp:velocity deploy` |
| Eine neue oder umbenannte Kernel-Klasse | `exp:velocity deploy --kernel` |
| Inhalte | Kein Deployment: Das Veröffentlichen leert die gecachten Ansichten des Elements, seiner übergeordneten Elemente und verwandter Objekte ([View-Cache und Cache-Blöcke](../../ersteller/website-ersteller/view-cache-und-cache-bloecke.md#was-ein-veröffentlichen-leert)) |

## Wann ein Deployment ein Wartungsfenster braucht

`exp:velocity deploy` „schaltet die Wartung weder ein noch aus“. Öffnen Sie für ein Deployment, das die Datenbank oder
viele Dateien auf einmal ändert, zuerst ein Wartungsfenster und schließen Sie es nach dem Smoke-Test
([Anleitung zum Wartungsmodus](../../../anleitungen/wartungsmodus.md#10-installationen-cronjobs-und-bereitstellungen)). Siehe
[Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md).

## Regeln für das Unternehmen

Vereinbaren Sie Folgendes mit Ihrem Team oder Partner und halten Sie es schriftlich fest:

- **Änderungen werden zuerst getestet**, an einer Kopie der Website, nie zuerst an der Live-Website.
- **Jede Änderung hat einen Probelauf** und eine benannte Person, die sie genehmigt.
- **Keine Deployments kurz vor einer Spitze** (ein Sale, ein Newsletter-Versand, der Start einer Kampagne) oder am Ende
  der Woche.
- **Jede Änderung lässt sich rückgängig machen**: Die vorherige Version des Codes liegt in Git, und ein Backup existiert,
  wenn sich die Datenbank ändert ([Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md)).
- **Jedes Deployment wird geprüft**: Startseite, eine tiefe Unterseite, die Administration, `var/log/error.log`.
- **Jedes Deployment wird festgehalten**: Das Prüfprotokoll zeichnet Deployments in seinem Kanal `system` auf
  ([Prüfprotokoll für den Betrieb](audit-trail-fuer-den-betrieb.md)); ergänzen Sie auch eine Zeile in Ihrem eigenen
  Änderungsprotokoll.

## Checkliste

- [ ] Die Änderung wurde an einer Kopie der Website getestet.
- [ ] `exp:velocity deploy --dry-run` wurde gelesen und genehmigt.
- [ ] Ein Wartungsfenster ist offen, wenn sich die Datenbank oder viele Dateien ändern.
- [ ] Das Deployment endete mit jedem Schritt auf PASS oder SKIP.
- [ ] Startseite, tiefe Unterseite, Administration und Fehler-Log wurden danach geprüft.
- [ ] Der Weg zurück ist bekannt, und ein Backup existiert.

## Verwandte Seiten

- [Velocity für Unternehmen](velocity-fuer-unternehmen.md)
- [Aktualisierungen ohne Überraschungen](aktualisierungen-ohne-ueberraschungen.md)
- [Änderungen ausrollen (Hosting)](../../hosting/betrieb/aenderungen-ausrollen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
