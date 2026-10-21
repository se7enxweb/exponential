---
title: Übergabe der Dokumentation beim Ausstieg
audience: government, project-manager, admin, partner
category: public-sector
order: 60
summary: Welche Dokumentation ein Lieferant übergeben muss, damit eine andere Partei eine Exponential-Website betreiben kann - Systembericht, Inhaltsmodell, Rollen, Einstellungen, Integrationen, eigener Code, Betriebshandbücher und redaktionelle Leitfäden - mit den Seiten der Administration und den Befehlen, die vieles davon automatisch erzeugen.
keywords: Übergabe der Dokumentation, Wissenstransfer, Systemdokumentation, Dokumentation des Inhaltsmodells, Betriebshandbuch, Runbook, Einstellungen, Rollen, eigener Code, README, Ausstieg, Übergang, documentation handover, knowledge transfer, system documentation, content model documentation, runbook, settings, roles, custom code, exit, transition
type: help
---
# Übergabe der Dokumentation beim Ausstieg

Diese Seite richtet sich an Projektleitungen, die festlegen, welche Dokumentation ein Lieferant während des Vertrags und
an dessen Ende liefert, und an Administratoren, die sie prüfen. Vieles, was ein neuer Lieferant braucht, lässt sich aus
der Installation selbst erzeugen; der Rest ist Wissen, das nur der bisherige Lieferant hat. Beides gehört in die
Übergabe.

## Inhalt
1. [Was sich aus der Website erzeugen lässt](#was-sich-aus-der-website-erzeugen-lässt)
2. [Was nur der Lieferant weiß](#was-nur-der-lieferant-weiß)
3. [Die Produktdokumentation](#die-produktdokumentation)
4. [Eine Checkliste für die Übergabe](#eine-checkliste-für-die-übergabe)
5. [Fallstricke](#fallstricke)

## Was sich aus der Website erzeugen lässt

| Dokument | Wie Sie es erzeugen | Dokumentation |
|---|---|---|
| Systembericht: Versionen, PHP, Datenbank, Erweiterungen, Zustandsprüfungen | **Setup > Systeminformationen**, `/setup/info/report` lädt ihn als Textdatei herunter; oder `./console exp:system:info --json` | [Systeminformationen](../../../../anleitungen/systeminformationen.md) |
| Inhaltsmodell: Klassen und ihre Attribute | **Setup > Klassen**, die Seite jeder Klasse; oder das typisierte Manifest des Website-Archivs | [Klassengruppen](../../../../anleitungen/klassengruppen.md), [Formate für den Datenexport](formate-fuer-den-datenexport.md) |
| Rollen, Richtlinien und Zuweisungen | **Benutzerkonten** > Rollen; die Seite jeder Rolle | [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) |
| Sektionen und Objektzustände | **Setup > Sektionen**, **Setup > Zustände** | [Sektionen](../../../../anleitungen/sektionen.md), [Objektzustände](../../../../anleitungen/objektzustaende.md) |
| Workflows und Trigger | **Setup > Workflows**, **Setup > Trigger** | [Workflows](../../../../anleitungen/workflows.md) |
| Einstellungen und woher jeder Wert stammt | die Einstellungsseite; oder `exp:ini` | [Die Einstellungsseite](../../../../anleitungen/einstellungsseite.md), [exp:ini](../../../../funktionen/6.0/exp-ini-befehl.md) |
| Aktive Erweiterungen und ihre Reihenfolge | **Setup > Extensions** | [Die Seite Extensions](../../../../anleitungen/extensions-seite.md) |
| Geplante Aufgaben | **Setup > Cronjobs** | [Cronjobs](../../../../anleitungen/cronjobs.md) |
| URL-Aliase und Platzhalter | **URL-Übersetzer**, **URL Platzhalter** | [URLs und Aliase](../../../../anleitungen/urls-und-aliase.md) |

Der Systembericht lässt Passwörter, Schlüssel und Serverpfade weg und kann daher mit einem neuen Lieferanten geteilt
werden; Einstellungsdateien nicht (siehe [Übergabe von Schlüsseln und Geheimnissen](uebergabe-von-schluesseln-und-geheimnissen.md)).

## Was nur der Lieferant weiß

Verlangen Sie eine schriftliche, während des Vertrags aktuell gehaltene Dokumentation zu:

- **Eigener Code**: jede eigene Erweiterung und jedes eigene Design, sein Zweck, seine Templates und Overrides, wie es
  gebaut und ausgerollt wird, in einer README in seinem Git-Repository.
- **Integrationen**: jedes angebundene System, Richtung, Protokoll, Ablageort der Zugangsdaten (nicht die Zugangsdaten),
  Ansprechpersonen.
- **Deployment**: wie eine Änderung in die Produktion gelangt, Umgebungen, die Deploy-Befehle
  ([Ausrollen](../../../../anleitungen/bereitstellung.md)).
- **Betrieb**: Zeitplan und Ablageort der Sicherungen, Überwachung, Cron-Einträge, Rotation der Logdateien, bekannte
  wiederkehrende Probleme und ihre Behebung (Betriebshandbücher im Stil der [Hosting-Runbooks](../../../hosting/runbooks/README.md)).
- **Entscheidungen**: warum das Inhaltsmodell so aufgebaut ist, Entscheidungen zur Barrierefreiheit des Designs,
  Sicherheitseinstellungen, die von den Standardwerten abweichen.
- **Redaktionelle Leitfäden**, die für Ihre Redaktion geschrieben wurden ([Übergabe an die Redaktion](../../../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md)).

## Die Produktdokumentation

Die Produktdokumentation muss nicht übergeben werden: Sie wird mit jeder Installation in `doc/` ausgeliefert und in der
Administration als Hilfe angezeigt ([Hilfesystem](../../../../funktionen/6.0/hilfesystem.md)). Sie ist freie Dokumentation
unter der GNU FDL 1.3 oder neuer, Ihre eigenen Handbücher dürfen sie also kopieren und anpassen ([Freie Dokumentation](../../../freie-dokumentation.md)).
Ihre Übergabedokumente müssen nur abdecken, was für Ihre Website spezifisch ist.

## Eine Checkliste für die Übergabe

```text
[ ] System report (Setup > System information > report), dated
[ ] Content model: classes and attributes, with their purpose
[ ] Roles, policies, assignments; sections; states; workflows and triggers
[ ] Settings changed from the defaults, and why
[ ] Active extensions, versions, order; custom extensions with READMEs in Git
[ ] Integrations described; credentials locations listed
[ ] Deployment procedure and environments
[ ] Operations: backups, monitoring, cron, runbooks
[ ] Editorial guides
[ ] Open issues and known bugs
[ ] Walk-through session with the receiving party held on <date>
```

## Fallstricke

- Dokumentation, die erst am Ende aus dem Gedächtnis geschrieben wird. Verlangen Sie sie als Lieferung zu jedem
  Meilenstein.
- In Dokumente eingefügte Zugangsdaten. Dokumentieren Sie, wo sie liegen, nie, wie sie lauten.
- Eine Übergabe ohne gemeinsamen Durchgang: Vereinbaren Sie einen Termin, in dem die übernehmende Partei Fragen stellt.

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Vorlage für einen Ausstiegsplan](vorlage-fuer-einen-ausstiegsplan.md)
- [Wechsel zu einem anderen Anbieter](wechsel-zu-einem-anderen-anbieter.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
