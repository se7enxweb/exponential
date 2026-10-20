---
title: Bereitstellung: Überblick
audience: hosting
category: hosting
order: 10
summary: Die Seiten zur Bereitstellung für Hosting-Anbieter - Ein-Klick-Installer auf Basis von exp:install, der Kickstarter und der Setup-Assistent, Seitenpakete, die Automatisierung neuer Kunden-Websites und mehrere Websites in einer Installation.
keywords: Bereitstellung, Ein-Klick-Installer, exp:install, Kickstarter, kickstart.ini, Setup-Assistent, Seitenpaket, Automatisierung, mehrere Websites, Siteaccess, neuer Kunde, provisioning, one-click installer, setup wizard, site package, automation, multisite, new customer
type: help
---
# Bereitstellung: Überblick

Dieser Bereich ist für Hosting-Anbieter, die Exponential-Websites für Kunden anlegen: von Hand, aus dem Ein-Klick-Installer eines
Control Panels oder aus ihrer eigenen Automatisierung. Er beantwortet: welcher Installer zu welchem Angebot passt, wie jeder gesteuert
wird, was er installiert und wie Sie ihn wiederholbar und sicher machen.

## Seiten in diesem Bereich

| Seite | Was sie Ihnen gibt |
|---|---|
| [Ein-Klick-Installer](ein-klick-installer-fuer-exponential.md) | die drei Oberflächen des einen Installers und wie ein Hoster darauf ein Ein-Klick-Angebot baut |
| [exp:install](exp-install-fuer-hoster.md) | die Installation über die Konsole: Optionen, Passwörter, Probelauf, Exit-Status, für Skripte und Panels |
| [Der Kickstarter](der-kickstarter-fuer-hoster.md) | installiert aus einer geprüften `kickstart.ini`; wiederholbare Installationen, Staging-Kopien |
| [Der Setup-Assistent auf Shared Hosting](der-setup-assistent-auf-shared-hosting.md) | der Installer im Browser für Kunden ohne Shell-Zugang |
| [Seitenpakete](seitenpakete-fuer-hoster.md) | was eine Installation auf die Website bringt und woher Pakete kommen |
| [Bereitstellung automatisieren](bereitstellung-automatisieren.md) | eine Pipeline zur Bereitstellung: Konto, Datenbank, Code, Installation, Server, Prüfungen |
| [Mehrere Websites in einer Installation](mehrere-websites-in-einer-installation.md) | mehrere Websites aus einer Installation über Siteaccesses |

## Die Kurzfassung

> Ein Installer, drei Oberflächen ([Kapitel 4](../../../installation/04-installationsweg-waehlen.md#41-ein-installer-drei-frontends)):
> Jede Methode führt dieselben Schritte aus und endet in `CreateSites`, sie installieren also dieselben Dinge.

| Situation | Nutzen Sie |
|---|---|
| Ein Panel oder Skript legt die Website unbeaufsichtigt an | `exp:install` |
| Dieselbe Website wird immer wieder gebaut (Staging, Schulung, eine geprüfte Vorlage) | den Kickstarter |
| Der Kunde installiert sie im Browser, ohne Shell | den Setup-Assistenten |

## Verwandte Seiten

- [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Kapitel 4: Eine Installationsmethode wählen](../../../installation/04-installationsweg-waehlen.md)
- [Erste Anmeldung und Standardpasswörter](../sicherheit/erste-anmeldung-und-standardpasswoerter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
