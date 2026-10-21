---
title: "Eine Upgrade-Testumgebung"
audience: designer, developer
category: theme-lifecycle
order: 90
summary: Einen Ort einrichten, an dem Sie ein Theme gegen neue Versionen von Exponential und PHP testen - eine eigene, mit Composer installierte Installation, Demo-Inhalte, das Theme als Git-Checkout und eine Möglichkeit, sie zurückzusetzen - nie eine Live-Website.
keywords: Testumgebung, Staging, Upgrade-Test, Testinstallation, Demo-Inhalte, zurücksetzen, Composer, Git-Checkout, test environment, staging, upgrade test, test installation, demo content, reset, git checkout
type: help
---
# Eine Upgrade-Testumgebung

Diese Seite ist für Designer, die Themes betreuen. Sie brauchen eine Installation, auf der es in Ordnung ist, Dinge
kaputtzumachen.

## Woraus sie besteht

| Teil | Wie |
|---|---|
| Eine Installation von Exponential | Mit Composer installiert, auf einem Server oder Ihrem Rechner; SQLite ist die Standarddatenbank und braucht keinen Server |
| Ihr Theme | Als Git-Checkout, damit Sie Branches wechseln können ([Mit Git entwickeln](../../mit-git-entwickeln.md)) |
| Demo-Inhalte | Das Demopaket oder die Startdaten des Themes ([Demo-Inhalte](../veroeffentlichung/demo-inhalte.md)) |
| Eine zweite PHP-Version | Um die älteste und die neueste angegebene PHP-Version zu testen |
| Ein Zurücksetzen | Eine Kopie der Datenbank und von `var/`, die Sie wiederherstellen können, oder eine geskriptete Neuinstallation |

## Schritt für Schritt

1. Installieren Sie Exponential in ein neues Verzeichnis, wie in [Erste Schritte](../../../anleitungen/erste-schritte.md) beschrieben.
2. Lassen Sie Composer Ihr Theme als Quell-Checkout klonen:

   ```bash
   composer config 'preferred-install.yourvendor/*' source
   composer require yourvendor/mytheme:dev-main
   ```

3. Aktivieren Sie das Theme und wählen Sie es für einen Siteaccess ([Ein Theme aktivieren](../erweiterungen/eine-theme-erweiterung-aktivieren.md)).
4. Importieren Sie die Demo-Inhalte.
5. Legen Sie einen Rücksetzpunkt an: Kopieren Sie die Datenbankdatei (SQLite) oder sichern Sie die Datenbank, und
   `var/storage`.
6. Schreiben Sie die Schritte in `doc/testing.md` Ihres Themes, damit der nächste Test in Minuten beginnt.

## Regeln

- Testen Sie nie auf einer Live-Website oder mit einer Kopie echter Kundendaten.
- Leiten Sie die Mails der Testinstallation in Dateien, nie an echte Adressen.

## Verwandte Seiten

- [Upgrade-Test Schritt für Schritt](upgrade-test-schritt-fuer-schritt.md)
- [Upgraden](../../../anleitungen/aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
