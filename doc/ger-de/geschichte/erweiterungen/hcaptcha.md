# hcaptcha (Datentyp hCaptcha): Chronik

hCaptcha entstand im August 2024 aus der Erweiterung reCAPTCHA (Release 1.0) und erhielt im September und Oktober 2026 den Namen Exponential und fehlende Metadaten-Dateien. Siehe die [Funktionsseite](../../../eng-us/features/6.0/extensions/hcaptcha.md).

Diese Seite führt **jede der 16 Änderungen** des Repositorys `hcaptcha` zwischen 2024-08-06 und 2026-10-02 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/hcaptcha.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/hcaptcha.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../../eng-us/features/6.0/extensions/hcaptcha.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 5 |
| Fehlerbehebung | 1 |
| Dokumentation | 6 |
| Release | 2 |
| ohne Nutzen für Anwender | 2 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-08-11 | v1.0 | [`5aab54d`](https://github.com/se7enxweb/hcaptcha/commit/5aab54d) |
| 2026-09-30 | v1.1 | [`0b1a73f`](https://github.com/se7enxweb/hcaptcha/commit/0b1a73f) |
| 2026-10-02 | v1.2 | [`7b78234`](https://github.com/se7enxweb/hcaptcha/commit/7b78234) |

## Zeitleiste

### 2024-08

Der Monat über alle Erweiterungen: [August 2024](monate/2024-08.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/hcaptcha.md#2024-08-10-änderungen).

- 2024-08-06 [`713b105`](https://github.com/se7enxweb/hcaptcha/commit/713b105) (Funktion) Refactored extension for latest v2 captcha support and PHP8 and eZ 5 Datatype Changes and Google Recaptcha Autoloads Names Space Usage. Works with eZ 6.x
- 2024-08-05 [`ff2ec1d`](https://github.com/se7enxweb/hcaptcha/commit/ff2ec1d) (Dokumentation) Create README.md
- 2024-08-05 [`8e8f6e9`](https://github.com/se7enxweb/hcaptcha/commit/8e8f6e9) (Dokumentation) Delete README replaced with README.md
- 2024-08-06 [`0167b15`](https://github.com/se7enxweb/hcaptcha/commit/0167b15) (Funktion) Refactor composer support to require google/recaptcha composer package
- 2024-08-06 [`e6250a5`](https://github.com/se7enxweb/hcaptcha/commit/e6250a5) (Dokumentation) Renamed CHANGELOG File to md file extension
- 2024-08-06 [`48e1073`](https://github.com/se7enxweb/hcaptcha/commit/48e1073) (Dokumentation) Renamed CHANGELOG File to md file extension
- 2024-08-06 [`ce40504`](https://github.com/se7enxweb/hcaptcha/commit/ce40504) (Funktion) Added recaptcha v3 Pure JS feedback_form template override to extension standard design offering
- 2024-08-06 [`1033e65`](https://github.com/se7enxweb/hcaptcha/commit/1033e65) (Funktion) Refactor to use ini for siteKey data storage
- 2024-08-11 [`cd25e67`](https://github.com/se7enxweb/hcaptcha/commit/cd25e67) (Funktion) Mass update of internals to support hcaptcha apis. Tested as working with Exponential 6
- 2024-08-11 [`5aab54d`](https://github.com/se7enxweb/hcaptcha/commit/5aab54d) (ohne Nutzen für Anwender) Create FUNDING.yml **Release v1.0.**

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/hcaptcha.md#2026-03-1-änderung).

- 2026-03-02 [`f585a15`](https://github.com/se7enxweb/hcaptcha/commit/f585a15) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/hcaptcha.md#2026-09-3-änderungen).

- 2026-09-30 [`5e280b0`](https://github.com/se7enxweb/hcaptcha/commit/5e280b0) (Dokumentation) The about page names the extension hCaptcha extension for Exponential
- 2026-09-30 [`a345c9e`](https://github.com/se7enxweb/hcaptcha/commit/a345c9e) (Dokumentation) The description calls the product Exponential
- 2026-09-30 [`0b1a73f`](https://github.com/se7enxweb/hcaptcha/commit/0b1a73f) (Release) Version 1.1 **Release v1.1.**

### 2026-10

Der Monat über alle Erweiterungen: [Oktober 2026](monate/2026-10.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/hcaptcha.md#2026-10-2-änderungen).

- 2026-10-02 [`e9030b3`](https://github.com/se7enxweb/hcaptcha/commit/e9030b3) (Fehlerbehebung) Fixed: Added extension.xml, which the extension lacked, and the website in ezinfo.php, so that the about page and the upgrade checks show the extension's version, license and website.
- 2026-10-02 [`7b78234`](https://github.com/se7enxweb/hcaptcha/commit/7b78234) (Release) Version 1.2 **Release v1.2.**

## Verwandte Seiten

- [Funktionsseite](../../../eng-us/features/6.0/extensions/hcaptcha.md)
- [Release Notes](../../../changelogs/extensions/hcaptcha.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/hcaptcha.md)
- [Verhaltensänderungen der Erweiterungen](../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
