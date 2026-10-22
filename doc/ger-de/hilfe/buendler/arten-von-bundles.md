---
title: Arten von Bundles
audience: bundler, partner, hosting
category: bundling
order: 20
summary: Die zehn Arten von Exponential-Bundles - Container-Images, Server- und VM-Images, Installer für Hosting-Panels, Pakete für Linux-Distributionen, Starter-Kits für Agenturen, Platform-Distributionen, Seitenpakete, Erweiterungs-Bundles, Appliances sowie Schulungs- oder Demo-Bundles - mit ihrem Inhalt, ihrer Installation, ihren Updates und der Seite, die sie behandelt.
keywords: Arten von Bundles, Container, Image, virtuelle Maschine, Ein-Klick-Installer, Panel, Distributionspaket, Starter-Kit, Seitenpaket, Erweiterungs-Bundle, Appliance, Demo, Schulung, kinds of bundles, Docker, Podman, virtual machine, one-click installer, deb, rpm, starter kit, Nexus, Legacy, site package, extension bundle, classroom
type: help
---
# Arten von Bundles

Diese Seite richtet sich an Bündler, die sehen möchten, wo ihr Produkt hineinpasst. Jede Art von Bundle hat ihre
eigene Seite; diese hier stellt sie nebeneinander.

## Inhalt

1. [Die zehn Arten auf einen Blick](#die-zehn-arten-auf-einen-blick)
2. [Wie jede Art installiert und aktualisiert wird](#wie-jede-art-installiert-und-aktualisiert-wird)
3. [Kombinationen](#kombinationen)
4. [Ihre Art wählen](#ihre-art-wählen)

## Die zehn Arten auf einen Blick

| Art | Sie liefern | Der Empfänger | Seite |
|---|---|---|---|
| Container-Image | ein Image mit PHP, Exponential und einem Einstiegspunkt | ein Plattformteam, eine Hosting-Firma, ein Entwickler | [Container-Images](container-images.md) |
| Server- oder VM-Image | ein Betriebssystem mit PHP, einem Webserver und Exponential | ein Cloud- oder Virtualisierungskunde | [Server- und VM-Images](server-und-vm-images.md) |
| Installer für ein Hosting-Panel | ein Skript oder Panel-Modul, das Exponential in ein Konto installiert | ein Hosting-Kunde | [Installer für Hosting-Panels](installer-fuer-hosting-panels.md) |
| Paket einer Linux-Distribution | ein `.deb` oder `.rpm` und seine Abhängigkeiten | ein Systemadministrator | [Pakete für Linux-Distributionen](pakete-fuer-linux-distributionen.md) |
| Starter-Kit einer Agentur | ein Repository mit Composer-Dateien, Erweiterungen, einem Theme, einem Seitenpaket und Installationskonfiguration | Ihre Projektteams, Ihre Kunden | [Starter-Kits für Agenturen](starter-kits-fuer-agenturen.md) |
| Platform-Distribution | ein Symfony-Projekt, das Exponential 6 enthält (Legacy, Nexus) | Entwickler und Agenturen | [Platform-Distributionen als Basis](platform-distributionen-als-basis.md) |
| Seitenpaket | ein `.ezpkg` (Klassen, Inhalte, Einstellungen, Installationsskripte) | alle, die die Installer verwenden | [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md) |
| Erweiterungs-Bundle | mehrere Erweiterungen, gemeinsam veröffentlicht und installiert | Website-Ersteller | [Erweiterungs-Bundles](erweiterungs-bundles.md) |
| Appliance | Hardware oder eine versiegelte virtuelle Maschine mit Exponential darin | eine Organisation, die eine fertige Box möchte | [Appliances](appliances.md) |
| Schulungs- oder Demo-Bundle | ein Schnellstart-Image oder -Skript, oft mit SQLite, manchmal mit veröffentlichter Anmeldung | Studierende, Trainer, Interessenten | [Bundles für Schulung und Demo](bundles-fuer-schulung-und-demo.md) |

## Wie jede Art installiert und aktualisiert wird

| Art | Installiert die Website mit | Aktualisiert den Code durch | Typische Datenbank |
|---|---|---|---|
| Container-Image | `exp:install` im Einstiegspunkt, einmalig | ein neues Image | ein Datenbankserver-Container oder ein verwalteter Dienst; SQLite für einen einzelnen Container |
| Server-Image | `exp:install` oder den Kickstarter beim ersten Start | Git und Composer auf dem Server oder Ihre Pakete | SQLite oder PostgreSQL auf derselben Maschine |
| Panel-Installer | `exp:install`, ausgeführt als Benutzer des Kontos | das Panel oder den Kunden mit Git und Composer | die Datenbank, die das Panel anlegt |
| Distributionspaket | einen Post-Install-Schritt oder einen Befehl, den der Administrator ausführt | den Paketmanager | nach Wahl des Administrators |
| Starter-Kit | den Kickstarter mit einer geprüften `kickstart.ini` | Composer, Git | je Projekt |
| Platform-Distribution | den eigenen Installer der Platform (`exponential:install` in Nexus) | Composer | je Distribution |
| Seitenpaket | einen der drei Installer | eine neue Paketversion | beliebig |
| Erweiterungs-Bundle | Composer und `ActiveExtensions` | Composer | beliebig |
| Appliance | einen eigenen Assistenten beim ersten Start, der `exp:install` aufruft | Ihren Update-Kanal | meist auf der Box |
| Demo-Bundle | `exp:install` auf SQLite, oft nach Zeitplan | Neuaufbau von Grund auf | SQLite |

Quellen zu den Installern: [Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md); zu
Nexus: [Exponential Platform Nexus](../../funktionen/6.0/plattform-nexus-starter.md).

## Kombinationen

Die meisten echten Bundles kombinieren mehrere Arten:

- Eine Hosting-Firma baut ein **Server-Image**, das einen **Panel-Installer** enthält, der ein **Seitenpaket**
  installiert.
- Eine Agentur pflegt ein **Starter-Kit**-Repository mit einem **Erweiterungs-Bundle** und einem eigenen
  **Seitenpaket** und baut daraus ein **Container-Image** für ihre Kunden.
- Eine Schule verwendet ein **Schulungs-Bundle**: ein **Container-Image**, das `exp:install` auf SQLite ausführt, mit
  einem für den Kurs gewählten Seitenpaket.

Jede Schicht bringt ihre eigenen Pflichten mit. Ein Container, der ein Seitenpaket enthält, trägt auch dessen Lizenz
([Komponenten Dritter und ihre Lizenzen](komponenten-dritter-und-ihre-lizenzen.md)).

## Ihre Art wählen

| Wenn Ihr Empfänger ... möchte | Liefern Sie |
|---|---|
| es ohne Einrichtung auf seiner eigenen Plattform betreiben | ein Container-Image |
| einen fertigen Server in seinem Cloud-Konto | ein Server-Image |
| eine Schaltfläche in seinem Hosting-Panel | einen Panel-Installer |
| Updates über `apt` oder `dnf` | ein Distributionspaket (und rechnen Sie mit Arbeit: siehe dessen Seite) |
| einen Ausgangspunkt für ein neues Projekt | ein Starter-Kit |
| ein Website-Design und Inhaltsmodell für jede Installation | ein Seitenpaket |
| einen Funktionsumfang für bestehende Websites | ein Erweiterungs-Bundle |
| eine Box, die nach dem Einstecken funktioniert | eine Appliance |
| es eine Stunde lang ausprobieren | ein Demo-Bundle |

## Verwandte Seiten

- [Was ein Bundle ist](was-ein-bundle-ist.md)
- [Ein-Klick-Installer für Exponential](../hosting/bereitstellung/ein-klick-installer-fuer-exponential.md)
- [OEM und Weiterverkauf](../partner/oem-und-wiederverkauf.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
