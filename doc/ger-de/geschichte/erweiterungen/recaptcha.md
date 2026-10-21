# recaptcha (Datentyp reCAPTCHA): Chronik

Der Datentyp reCAPTCHA wurde im August 2024 für reCAPTCHA v2, PHP 8 und Exponential 6 überarbeitet und übernahm im September 2026 die Metadaten von Exponential. Siehe die [Funktionsseite](../../funktionen/6.0/erweiterungen/recaptcha.md).

Diese Seite führt **jede der 18 Änderungen** des Repositorys `recaptcha` zwischen 2024-08-06 und 2026-09-30 auf, nach Monat, jeweils mit der Art der Änderung. Lesen Sie sie, um herauszufinden, wann ein Verhalten hinzukam und welches Release Sie dafür brauchen. Die vollständige, maschinell erzeugte Aufzeichnung mit Größen ist das [Änderungsverzeichnis](../aenderungsverzeichnis/recaptcha.md); was jedes Release enthält, steht in den [Release Notes](../../../changelogs/extensions/recaptcha.md); wie Sie die Erweiterung verwenden, steht auf ihrer [Funktionsseite](../../funktionen/6.0/erweiterungen/recaptcha.md). Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Funktion | 6 |
| Fehlerbehebung | 1 |
| Dokumentation | 6 |
| Release | 4 |
| ohne Nutzen für Anwender | 1 |

## Releases

| Datum | Release | Commit |
|---|---|---|
| 2024-08-06 | 1.4 | [`0167b15`](https://github.com/se7enxweb/recaptcha/commit/0167b15) |
| 2024-09-14 | v1.4.1 | [`88e92fe`](https://github.com/se7enxweb/recaptcha/commit/88e92fe) |
| 2024-09-14 | v1.4.2 | [`c5e7294`](https://github.com/se7enxweb/recaptcha/commit/c5e7294) |
| 2026-09-27 | v1.4.3 | [`a26e413`](https://github.com/se7enxweb/recaptcha/commit/a26e413) |
| 2026-09-28 | v1.4.4 | [`bfcb48b`](https://github.com/se7enxweb/recaptcha/commit/bfcb48b) |
| 2026-09-30 | v1.4.5 | [`fbf206e`](https://github.com/se7enxweb/recaptcha/commit/fbf206e) |

## Zeitleiste

### 2024-08

Der Monat über alle Erweiterungen: [August 2024](monate/2024-08.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/recaptcha.md#2024-08-8-änderungen).

- 2024-08-06 [`713b105`](https://github.com/se7enxweb/recaptcha/commit/713b105) (Funktion) Refactored extension for latest v2 captcha support and PHP8 and eZ 5 Datatype Changes and Google Recaptcha Autoloads Names Space Usage. Works with eZ 6.x
- 2024-08-05 [`ff2ec1d`](https://github.com/se7enxweb/recaptcha/commit/ff2ec1d) (Dokumentation) Create README.md
- 2024-08-05 [`8e8f6e9`](https://github.com/se7enxweb/recaptcha/commit/8e8f6e9) (Dokumentation) Delete README replaced with README.md
- 2024-08-06 [`0167b15`](https://github.com/se7enxweb/recaptcha/commit/0167b15) (Funktion) Refactor composer support to require google/recaptcha composer package **Release 1.4.**
- 2024-08-06 [`e6250a5`](https://github.com/se7enxweb/recaptcha/commit/e6250a5) (Dokumentation) Renamed CHANGELOG File to md file extension
- 2024-08-06 [`48e1073`](https://github.com/se7enxweb/recaptcha/commit/48e1073) (Dokumentation) Renamed CHANGELOG File to md file extension
- 2024-08-06 [`ce40504`](https://github.com/se7enxweb/recaptcha/commit/ce40504) (Funktion) Added recaptcha v3 Pure JS feedback_form template override to extension standard design offering
- 2024-08-06 [`1033e65`](https://github.com/se7enxweb/recaptcha/commit/1033e65) (Funktion) Refactor to use ini for siteKey data storage

### 2024-09

Der Monat über alle Erweiterungen: [September 2024](monate/2024-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/recaptcha.md#2024-09-4-änderungen).

- 2024-09-14 [`c38c963`](https://github.com/se7enxweb/recaptcha/commit/c38c963) (Fehlerbehebung) Update recaptchalib.php Bugfix for booting extension in class editor from composer installation
- 2024-09-14 [`88e92fe`](https://github.com/se7enxweb/recaptcha/commit/88e92fe) (Release) Update ezinfo.php bump version number **Release v1.4.1.**
- 2024-09-14 [`8dcae38`](https://github.com/se7enxweb/recaptcha/commit/8dcae38) (Funktion) Update recaptchatype.php Translate german validation error messages to english for general use.
- 2024-09-14 [`c5e7294`](https://github.com/se7enxweb/recaptcha/commit/c5e7294) (Release) Update ezinfo.php version bump **Release v1.4.2.**

### 2026-03

Der Monat über alle Erweiterungen: [März 2026](monate/2026-03.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/recaptcha.md#2026-03-1-änderung).

- 2026-03-02 [`1ef3f78`](https://github.com/se7enxweb/recaptcha/commit/1ef3f78) (ohne Nutzen für Anwender) chore: add GitHub Sponsors funding metadata

### 2026-09

Der Monat über alle Erweiterungen: [September 2026](monate/2026-09.md). [Änderungsverzeichnis dieses Monats](../aenderungsverzeichnis/recaptcha.md#2026-09-5-änderungen).

- 2026-09-27 [`a26e413`](https://github.com/se7enxweb/recaptcha/commit/a26e413) (Dokumentation) The extension states its version, license and website **Release v1.4.3.**
- 2026-09-28 [`9b14348`](https://github.com/se7enxweb/recaptcha/commit/9b14348) (Funktion) Every visible text of the extension is a translation string, with German
- 2026-09-28 [`bfcb48b`](https://github.com/se7enxweb/recaptcha/commit/bfcb48b) (Release) Version 1.4.4 **Release v1.4.4.**
- 2026-09-30 [`f393ea8`](https://github.com/se7enxweb/recaptcha/commit/f393ea8) (Dokumentation) The description calls the product Exponential
- 2026-09-30 [`fbf206e`](https://github.com/se7enxweb/recaptcha/commit/fbf206e) (Release) Version 1.4.5 **Release v1.4.5.**

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/recaptcha.md)
- [Release Notes](../../../changelogs/extensions/recaptcha.md)
- [Änderungsverzeichnis](../aenderungsverzeichnis/recaptcha.md)
- [Verhaltensänderungen der Erweiterungen](../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
