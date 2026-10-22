---
title: Appliances
audience: bundler, partner
category: bundling
order: 360
summary: Wie Sie Exponential als Hardware- oder versiegelte virtuelle Appliance bündeln - was eine Appliance ihrem Besitzer nach der GPL schuldet, eine Einrichtung beim ersten Start, Updates und Sicherheitskorrekturen über die Lebensdauer der Appliance, Backups, Support und eine Checkliste für Appliances. Keine Rechtsberatung.
keywords: Appliance, Hardware, versiegelte virtuelle Maschine, Box, erster Start, Einrichtung, Quellcode, Updates, Sicherheitskorrekturen, Backup, Support, appliance, hardware, sealed virtual machine, box, first boot, setup, GPL, source code, updates, security fixes, backup, support, OEM, bundle
type: help
---
# Appliances

Diese Seite richtet sich an Anbieter, die Exponential in einer Box verkaufen: einem Hardwaregerät oder einer
virtuellen Maschine, die der Kunde nicht selbst administriert. Eine Appliance ist das Bundle mit der längsten
Lebensdauer und der am wenigsten sichtbaren Software, daher brauchen ihre Pflichten die meiste Sorgfalt.

## Inhalt

1. [Was eine Appliance unterscheidet](#was-eine-appliance-unterscheidet)
2. [Die GPL und eine Box](#die-gpl-und-eine-box)
3. [Erster Start](#erster-start)
4. [Updates über die Lebensdauer der Appliance](#updates-über-die-lebensdauer-der-appliance)
5. [Backups und Eigentum an den Daten](#backups-und-eigentum-an-den-daten)
6. [Checkliste für Appliances](#checkliste-für-appliances)

## Was eine Appliance unterscheidet

| Aspekt | Appliance |
|---|---|
| Wer das System administriert | Sie, über Ihren Update-Kanal |
| Wer die Website administriert | der Kunde, in der Administration von Exponential |
| Wie lange sie läuft | Jahre |
| Netzwerk | manchmal keines ([Offline-Bundles](offline-bundles.md)) |
| Datenbank | meist auf der Box: SQLite 3 für eine Maschine ([SQLite-Bundles](sqlite-bundles.md)) |
| Webserver | Velocity, mit eigenem TLS ([Velocity in einem Bundle](velocity-in-einem-bundle.md)) |

## Die GPL und eine Box

Eine Appliance gibt die GPL-Software darauf weiter. Der Kunde muss die Lizenztexte, die Hinweise und den zugehörigen
Quellcode erhalten, und Sie dürfen keine Einschränkungen hinzufügen, die die GPL verbietet
([GPL-Pflichten](gpl-pflichten-beim-weitergeben.md)). In der Praxis:

- bringen Sie die Hinweise in der Dokumentation der Box und in ihrer Weboberfläche unter (**Info > Copyright** bleibt
  erreichbar);
- veröffentlichen oder bieten Sie den Quellcode genau des Software-Images an, für jedes Image, das Sie ausliefern;
- seien Sie vorsichtig mit Maßnahmen, die einen Besitzer daran hindern, die GPL-Software auf einem Gerät zu ändern,
  das ihm gehört; fragen Sie einen Rechtsbeistand, welche Lizenzversionen Ihrer Komponenten dafür Regeln aufstellen.

Dies ist keine Rechtsberatung. Der OEM-Weiterverkauf über das Partnerprogramm wird unter
[OEM und das Partnerprogramm für Bündler](oem-und-das-partnerprogramm-fuer-buendler.md) beschrieben.

## Erster Start

Ein eigener Einrichtungsbildschirm sammelt, was der Installer braucht, und ruft ihn einmal auf
([Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md)):

1. die Adresse der Website oder die Adresse der Box, wenn es noch keine gibt;
2. die E-Mail des Administrators;
3. das Administratorpasswort: erzeugt (einmal angezeigt) oder vom Besitzer eingegeben, mindestens
   `MinPasswordLength` (10) Zeichen und nicht allgemein bekannt;
4. dann `exp:install` mit diesen Werten, und der Einrichtungsbildschirm deaktiviert sich selbst.

## Updates über die Lebensdauer der Appliance

- Sicherheitskorrekturen für Exponential kommen "auf dem Branch `main` ... und werden mit dem nächsten 6.0.x-Release
  veröffentlicht" (`SECURITY.md`). Ihr Kanal muss neue Releases liefern, nicht alte Tags flicken.
- Jedes Update hat eine Code-Hälfte und eine Datenbank-Hälfte
  ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).
- Nach jedem Update: Caches, Autoloads, Neustart von Velocity; `exp:velocity deploy` "führt jeden Schritt aus, den
  eine PHP-Änderung braucht, in der richtigen Reihenfolge"
  ([Velocity-Engines](../../bc/6.0/velocity-engines.md#kurz-gesagt)).
- Nennen Sie in Ihren Bedingungen, wie lange Sie für jedes Modell Updates liefern.

## Backups und Eigentum an den Daten

Die Inhalte der Website gehören dem Kunden. Geben Sie ihm eine Möglichkeit, sie zu sichern und mitzunehmen: die
Datenbank und `var/storage`, `settings/override`, `settings/siteaccess`
([Backups: was gesichert wird](../hosting/betrieb/backups-was-gesichert-werden-muss.md)). Inhaltsexporte und Pakete erlauben
den Umzug auf eine andere Installation
([Eine Website zwischen Hostern umziehen](../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)).

## Checkliste für Appliances

- [ ] Lizenzhinweise, Liste der Komponenten Dritter und Quellcode-Angebot für jedes Image.
- [ ] Kein gemeinsames Standardpasswort; der erste Start erzeugt eines oder fragt danach.
- [ ] HTTPS, Sicherheits-Header und Härtung angewendet ([Sicherheitsvorgaben](sicherheitsvorgaben-eines-bundles.md)).
- [ ] Ein Update-Kanal mit dokumentiertem Supportzeitraum.
- [ ] Backup und Export stehen dem Besitzer zur Verfügung.
- [ ] Offline-Installation getestet, falls die Box kein Netzwerk haben könnte.
- [ ] Grenzen des Supports schriftlich festgehalten ([Grenzen des Supports](grenzen-des-supports.md)).

## Verwandte Seiten

- [OEM und Weiterverkauf](../partner/oem-und-wiederverkauf.md)
- [Server- und VM-Images](server-und-vm-images.md)
- [Kundendaten schützen](../partner/kundendaten-schuetzen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
