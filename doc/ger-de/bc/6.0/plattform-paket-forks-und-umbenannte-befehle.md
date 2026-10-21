# Forks der Platform-Pakete, umbenannte Befehle und Versionsregeln

Lesen Sie diese Seite, bevor Sie ein Projekt auf Basis der Symfony-basierten Exponential Platform (3.x, 4.6, v5),
einer Nexus-Distribution oder der Legacy Bridge von einer Version aktualisieren, die noch die Upstream-Paketnamen
verwendete, oder bevor Sie einen Branch zusammenführen, der auf die Pakete von se7enxweb umstellt. Die Schritte stehen
in der Reihenfolge, in der Sie sie ausführen sollten. Installationen nur des Legacy-Kernels betrifft nur der letzte
Schritt.

Überblick: [Ökosystem](../../../eng-us/history/ecosystem.md). Referenz:
[Paketzuordnung](../../spezifikationen/6.0/platform-paketzuordnung.md),
[Befehlsnamen](../../spezifikationen/6.0/platform-konsolenbefehle.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Etwa fünfzig Platform-Pakete werden vom Vendor `se7enxweb` installiert; Konsolenbefehle erhielten das Präfix `exponential:`; Markennamen und Lizenzangaben haben sich geändert. |
| Wer betroffen ist | Projekte mit Platform, Nexus und Legacy Bridge. Installationen des Legacy-Kernels: nur das Datenbank-Update in Schritt 9. |
| Wie Sie es prüfen | `composer why ibexa/admin-ui` und `composer show \| grep -E "^(ibexa\|ezsystems\|netgen)/"` |
| Wie Sie es beheben | Folgen Sie den Schritten 1 bis 9 unten. Die meisten erfordern keine Änderung; die alten Namen funktionieren weiter. |

## Schritt 1: Pakete werden vom Vendor se7enxweb installiert

**Was sich geändert hat.** Zwischen September 2025 und April 2026 wurden die Platform-Pakete auf den Vendor
`se7enxweb` umbenannt: Aus `ibexa/admin-ui` wurde `se7enxweb/admin-ui`, aus `ezsystems/ezplatform-kernel` wurde
`se7enxweb/ezplatform-kernel`, aus `netgen/site-bundle` wurde `se7enxweb/site-bundle`, aus `netgen/layouts-core`
wurde `se7enxweb/layouts-core`, aus `twig/twig` wurde `se7enxweb/twig`, und so weiter für etwa fünfzig Pakete
([Tabelle](../../spezifikationen/6.0/platform-paketzuordnung.md)).

**Warum Ihre `composer.json` nicht bricht.** Jeder Fork deklariert den Upstream-Namen unter `replace` (seit dem
2026-04-12 mit dem Platzhalter `*`, nicht `self.version`, sodass der Fork jede Versionsbedingung erfüllt, die ein
anderes Paket an den Upstream-Namen stellt). Composer installiert nur den Fork. Pakete Dritter, die `ibexa/admin-ui`
oder `ezsystems/ezplatform-kernel` verlangen, funktionieren weiter.

**So prüfen Sie es.**

```bash
composer why ibexa/admin-ui               # which package provides it; expect se7enxweb/admin-ui
composer why ezsystems/ezplatform-kernel
composer show | grep -E "^(ibexa|ezsystems|netgen)/"   # upstream packages still installed next to forks
```

Wird noch ein Upstream-Paket aufgeführt, verlangt etwas in Ihrem Projekt eine Version, die der Fork nicht ersetzt,
oder die Lock-Datei ist älter als der Fork. Aktualisieren Sie den Abhängigkeitsbaum zuerst auf einer Kopie des
Projekts und lesen Sie den Plan, den Composer ausgibt.

**So beheben Sie es.** Nur wenn Ihre eigene `composer.json` einen Upstream-Namen direkt verlangt und Sie möchten, dass
das Projekt eindeutig ist: Verlangen Sie stattdessen den Namen von se7enxweb. Für Platform v5 ist der einzige Eintrag
das Metapaket:

```json
{
    "require": {
        "se7enxweb/exponential-platform-dxp": "*"
    }
}
```

**Installieren Sie nie beide.** Ein Projekt darf das Upstream-Paket und das geforkte Paket nicht gemeinsam installiert
haben. Die Klassen des Forks und die des Upstream teilen sich Namensräume, und der Autoloader würde zufällig eine davon
nehmen. Der Fork `site-bundle` deklariert außerdem `netgen/site-bundle:*` als ersetzt, um genau das zu verhindern
(Release `3.0.6`, 2026-04-19).

## Schritt 2: Verwenden Sie Tags, keine Branch-Aliase

Am 2026-03-26 wurden mehreren Forks Branch-Aliase wie `1.3-se7enx` und `2.3-se7enx` hinzugefügt und am selben Tag
wieder entfernt, „um die automatische Auflösung durch externe Projekte zu verhindern“. Verlassen Sie sich nicht auf
einen Alias eines Entwicklungs-Branches. Verlangen Sie ein getaggtes Release, oder bewusst `dev-main`.

## Schritt 3: Versionen von PHP und Node prüfen

| Linie | PHP | Node.js (Asset-Builds) |
|---|---|---|
| Platform v5 (`exponential-platform-dxp`, Skeleton) | 8.3 oder neuer; der Nexus-v5-Starter 8.4 oder neuer | 20 (Anleitung zum DXP-Skeleton), 22 (Nexus-v5-Starter) |
| Platform-4.6.x-Skeleton | 7.4 bis 8.5 | 20 |
| Platform-3.x-Forks (`ezplatform-*`) | 7.3 bis 8.5 (Bedingung am 2026-03-25 auf `^8.5` erweitert) | je nach Projekt |
| Nexus 1.x / 2.5 | 7.1 bis 8.5 | 20 (die `cjw`-Nexus wechselte am 2026-02-12 von 14 auf 20) |

- Unter PHP 8.4 oder 8.5 braucht ein älterer Stack die [Framework-Forks](../../../eng-us/features/6.0/platform-php85-framework-forks.md).
- Unter PHP 8.4 braucht der Stack von Layouts 2.0 den [Fork von layouts-core](../../../eng-us/features/6.0/platform-layouts-core-fork.md).
- Nur für die Linie 1.x überspringt Composer die Plattformprüfung mit `--ignore-platform-reqs` (die
  Installationsanleitung von Nexus 1.x sagt das). Verwenden Sie diese Option bei anderen Linien nicht.

## Schritt 4: Namen der Konsolenbefehle

**Was sich geändert hat.** Am 2026-04-07 erhielten die Befehle das Präfix `exponential:`. Die alten Namen sind
veraltete Aliase und funktionieren weiter.

**So prüfen Sie es.** Suchen Sie nach Skripten und Cron-Einträgen, die Befehlsnamen oder die Ausgabe von `list`
auswerten:

```bash
grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d ~/bin ./deploy 2>/dev/null
```

**So beheben Sie es.** Nichts, bis ein künftiges Release die Aliase entfernt. Neue Skripte sollten die primären Namen
verwenden ([Liste](../../spezifikationen/6.0/platform-konsolenbefehle.md)). Zwei Befehle der Legacy Bridge haben ihre
Schreibweise über das Präfix hinaus geändert: `install_extensions` heißt jetzt `install-extensions`, und
`assets_install` heißt jetzt `assets-install`.

## Schritt 5: Markennamen in installierten Daten und Templates

Zeichenketten, die „Ibexa DXP“, „Ibexa Platform“ oder „eZ Platform“ lauteten, lauten jetzt „Exponential Platform DXP“
oder „Exponential Platform“ in:

- der Admin-Oberfläche (Seitentitel, Willkommensseite, E-Mail zum Zurücksetzen des Passworts, Logo, Favicons);
- dem Debug-Layout des Kernels;
- den SQL-Startdaten (`cleandata.sql`) und dem Text des Installationsprogramms;
- dem Copyright-Template von `legacy_admin`.

Tests oder Überwachungen, die nach den alten Zeichenketten suchen, müssen angepasst werden. Von Ihnen erstellte
Inhalte bleiben unberührt; geändert haben sich nur Startdaten und ausgelieferte Templates.

## Schritt 6: Lizenzangaben

Die `composer.json` der Forks gibt „GPL 2.0 or later“ an (zum Beispiel `se7enxweb/admin-ui`:
`(GPL-2.0-or-later or proprietary)`; Nexus: „from GPLv2 only to GPLv2 or later“, 2025-07-01). Wenn Sie
Lizenzberichte erzeugen, erwarten Sie den neuen Wert.

## Schritt 7: Legacy Bridge, den passenden Branch wählen

| Ihre Plattform | Verwenden Sie |
|---|---|
| eZ Platform 2.5 LTS | Tags `2.1.x` (`v2.1.10`, `v2.1.11`) |
| eZ Platform 3.3 / Symfony 5.4 | Tags `3.x` (`3.0.0.1` bis `v3.0.0.28`) |
| Ibexa-4.6-kompatible Installation | Tags `4.x` (`v4.0.0.0` bis `v4.0.0.3`) |
| Platform v5 | Tags `5.x` (`v5.0.0.0`, `v5.0.1` bis `v5.0.9`) |

Änderungen im Branch `3.x`, die Sie in Ihrem eigenen Code bemerken können:

- der `TreeBuilder` des Bundles verwendet die API von Symfony 5;
- Legacy-Befehle werden als Dienste mit Konstruktorinjektion registriert (kein `ContainerAwareCommand`);
- die Namen der Ereignisklassen sind die der Symfony Contracts (`RequestEvent`, `ResponseEvent`);
- die Klassen des Config-Resolvers und des Twig-Loaders haben Typdeklarationen.

Eigene Klassen, die Klassen der Bridge erweitern, brauchen dieselben Signaturen. Siehe
[die Anleitung zur Legacy Bridge](../../../eng-us/features/6.0/legacy-bridge.md).

## Schritt 8: Nexus 1.x, wo Assets und gespeicherte Dateien liegen

Die Installation von Nexus 1.x ersetzt das Legacy-Verzeichnis, wenn Composer `se7enxweb/exponential` aktualisiert.
Der Inhaltsspeicher und die Erweiterung `app` liegen daher in `src/AppBundle/ezpublish_legacy` und werden mit den
symbolischen Links der Installationsanleitung eingebunden
([Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md#install-nexus-100x-the-25-line)). Halten Sie
hochgeladene Dateien nie nur innerhalb von `ezpublish_legacy/var`.

## Schritt 9: Legacy-Kernel, Datenbank-Update von 5.4 auf 6.0

Für den Legacy-Kernel selbst besteht das Datenbank-Update von 5.4 auf 6.0 aus zwei Anweisungen, die die neue Version
festhalten. Sichern Sie zuerst die Datenbank und führen Sie dann die Datei aus, die zu Ihrer Datenbank passt:

```bash
mysql -u <user> -p <database> < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
psql -U <user> -d <database> -f update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
```

Sie setzen in `ezsite_data` `ezpublish-version` auf `6.0.0` und `ezpublish-release` auf `1`. Updates von 6.0.0 auf
6.0.15 verwenden `update/database/{mysql,postgresql,sqlite}/6.0/dbupdate-6.0.0-6.0.15.sql`. Weitere
Verhaltensänderungen des Legacy-Kernels stehen in den anderen Anleitungen dieses Verzeichnisses, beginnend mit
[Unterstützung für PHP 8](php8.md).

## Verwandte Seiten

- [Paketzuordnung](../../spezifikationen/6.0/platform-paketzuordnung.md) und [Befehlsnamen](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- [Legacy Bridge](../../../eng-us/features/6.0/legacy-bridge.md) und ihre [Bundle-Spezifikation](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- [Framework-Forks für PHP 8.5](../../../eng-us/features/6.0/platform-php85-framework-forks.md)
- Funktionen: [Administrationsoberfläche der Platform](../../../eng-us/features/6.0/platform-admin-ui-fork.md);
  [DXP-Skeleton](../../../eng-us/features/6.0/platform-dxp-skeleton.md); [Layouts auf der Platform](../../../eng-us/features/6.0/platform-layouts-core-fork.md);
  [Nexus-Starter](../../../eng-us/features/6.0/platform-nexus-starter.md); [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md);
  [SQLite für Exponential Platform](../../../eng-us/features/6.0/platform-sqlite-install.md);
  [Datenbankmanager AdminNeo](../../../eng-us/features/6.0/adminneo-database-manager.md)
- [Platform-SQLite-Installer](../../spezifikationen/6.0/platform-sqlite-installer.md)
- [Changelog der Platform](../../../changelogs/extensions/exponential-platform.md)
- Geschichte: [Überblick über das Ökosystem](../../../eng-us/history/ecosystem.md), eine Seite für jeden Monat ab 2018-11 in
  [Monate des Ökosystems](../../../eng-us/history/ecosystem/months/2026-04.md) und das [Änderungsbuch](../../../eng-us/history/ledger/README.md)
