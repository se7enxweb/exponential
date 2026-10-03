# Change ledger: qbix-webserver

Every change made to `qbix-webserver` since the se7enxweb era began, oldest first: 715 changes touching 2820 files. Each line gives the date, the commit, what kind of change it is, its message, and how many files it touched (+ lines added / − lines removed). Release tags are marked. The narrative documentation (features, bc notes, specifications, the history chronicle) explains what these changes deliver; this ledger is the complete record they are built from.

| Kind | Changes |
|---|---|
| Updated | 364 |
| Other | 137 |
| Added | 124 |
| Merged | 81 |
| Removed | 5 |
| Renamed | 4 |

## 2026-07 (47 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-20 | `0480c29` | Other | Initial commit | 1 | +21 / −0 |  |
| 2026-07-20 | `eaa711a` | Other | Initial release — pure PHP web server, 55-73% of nginx throughput | 27 | +7191 / −1 |  |
| 2026-07-20 | `895314e` | Updated | Updated filenames referenced in README.md | 1 | +3 / −3 |  |
| 2026-07-20 | `71f3ed3` | Updated | Updated filenames referenced in README.md | 1 | +10 / −10 |  |
| 2026-07-20 | `0292445` | Updated | Updated README to highlight comparisons to other set-ups | 1 | +286 / −34 |  |
| 2026-07-20 | `473b1bb` | Updated | Updated README to mention HTTP/2 and amphp autodetection | 1 | +57 / −0 |  |
| 2026-07-20 | `0f9e400` | Updated | Updated README to have a compelling introduction | 1 | +32 / −16 |  |
| 2026-07-20 | `f3f0c2a` | Updated | Updated README to compare to FrankenPHP, Swoole, etc. | 1 | +69 / −0 |  |
| 2026-07-20 | `618638e` | Updated | Fix str_repeat → str_pad, add composer.json, clean up Revolt driver | 8 | +147 / −63 |  |
| 2026-07-20 | `fe52e9a` | Updated | Updated README with more practical advice for PHP developers | 1 | +187 / −87 |  |
| 2026-07-20 | `1672e0c` | Added | Added support for autoloading, event handlers, and more | 5 | +681 / −84 |  |
| 2026-07-20 | `71c3ad3` | Other | Improved documentation of built-in Q methods for PHP scripts | 1 | +76 / −8 |  |
| 2026-07-20 | `c47141b` | Updated | Updated README to highlight the ability to handle 10x more traffic | 1 | +21 / −2 |  |
| 2026-07-20 | `dd06d06` | Added | Added support for Windows | 5 | +211 / −18 |  |
| 2026-07-21 | `dce9b96` | Other | Started handling websockets using long-lived PHP processes | 10 | +2030 / −24 |  |
| 2026-07-21 | `5983f11` | Added | Added support for legacy scripts via CGI mode instead of CLI SAPI | 6 | +559 / −65 |  |
| 2026-07-21 | `a99b9fe` | Added | Added tests | 12 | +577 / −0 |  |
| 2026-07-21 | `901f371` | Other | Finalized carve-out for legacy PHP compatibility using CGI SAPI | 3 | +460 / −4 |  |
| 2026-07-21 | `ce8cfac` | Other | Qbix Server is now faster than nginx even on static files (with keep-alive) | 6 | +902 / −387 |  |
| 2026-07-21 | `b2cf9c7` | Other | Highlighted ease-of-use for making safe-by-default, real-time apps in README | 1 | +37 / −0 |  |
| 2026-07-21 | `42ed5a5` | Added | Added support for socket.io and simple JSON websocket payloads via /Q/socket.js | 8 | +1465 / −400 |  |
| 2026-07-21 | `62d9b23` | Added | Added a cron-like scheduler in the web server, to kick off CLI PHP scripts | 5 | +246 / −4 |  |
| 2026-07-21 | `bdac23b` | Added | Added support for hot reload, start, stop, restart, and virtual hosts | 9 | +469 / −10 |  |
| 2026-07-21 | `8d44599` | Added | Added socket->disconnect() method for use by child processes | 4 | +18 / −1 |  |
| 2026-07-22 | `c36cc98` | Added | Added automatic PSR-4 support for class autoloading config | 4 | +108 / −6 |  |
| 2026-07-23 | `e421368` | Added | Added tests, hardening and bug fixes | 14 | +443 / −17 |  |
| 2026-07-23 | `e1021c2` | Other | README.md added table showing how server takes care of most things now | 1 | +27 / −0 |  |
| 2026-07-24 | `c7b9146` | Added | Added support for php://input and simplified RegExconfig | 7 | +358 / −16 |  |
| 2026-07-27 | `cd3b4b2` | Added | Added welcome page and getting started instructions. Made default error pages a lot friendlier. | 10 | +743 / −40 |  |
| 2026-07-27 | `1dec563` | Other | Merged: now there's a friendly welcome page and getting started instructions | 0 | +0 / −0 |  |
| 2026-07-27 | `05c4ee2` | Other | Made webserver control panel more powerful and useful | 3 | +188 / −26 |  |
| 2026-07-27 | `a1cc65c` | Other | Improved control panel and added a playground for getting started | 7 | +148 / −25 |  |
| 2026-07-27 | `ebad1f6` | Other | Made control panel able to better manage apps and plugins | 4 | +233 / −28 |  |
| 2026-07-28 | `01c44de` | Other | Improved handling of temp files, chunked encoding, HTTP input | 5 | +142 / −19 |  |
| 2026-07-29 | `c9912be` | Other | Bundled optional Qbix front-end assets. Server went from ~0.5MB to ~1.5MB | 263 | +51089 / −4 |  |
| 2026-07-29 | `f2bb847` | Added | Added support for Windows shortcuts and Mac aliases to act as symlinks | 3 | +282 / −8 |  |
| 2026-07-29 | `01dff5f` | Other | Improved first-class support for directory listings and image galleries | 7 | +1566 / −53 |  |
| 2026-07-29 | `599f11a` | Added | Added minimal custom versions of Handlebars and jQuery | 5 | +279 / −15 |  |
| 2026-07-29 | `16828c1` | Other | Improved optional minimal jQuery drop-in replacement | 2 | +18 / −12 |  |
| 2026-07-29 | `48ae3a1` | Added | Added support for Save-Data and Accept headers for images | 3 | +36 / −15 |  |
| 2026-07-29 | `025ecac` | Other | Now Qbix Server can power federated and distributed web apps | 8 | +1614 / −33 |  |
| 2026-07-29 | `2e86f0d` | Other | Started parsing PHPDoc, YUIDoc for public API discovery endpoints | 2 | +359 / −20 |  |
| 2026-07-29 | `0bceb7c` | Added | Added 11 more entries to Features table in README.md | 1 | +17 / −5 |  |
| 2026-07-29 | `673e442` | Other | Legacy PHP scripts should replace header() with Q_Response::header() calls | 3 | +100 / −75 |  |
| 2026-07-30 | `ff2b029` | Updated | Fixed issues 3 and 4 | 6 | +172 / −127 |  |
| 2026-07-30 | `3f0b451` | Updated | Fixed issue 5 | 2 | +52 / −22 |  |
| 2026-07-30 | `914b41f` | Updated | Fixed issues 1 and 2 | 2 | +90 / −5 |  |

## 2026-08 (35 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-08-04 | `fd97f5e` | Other | Improved --app mode and compressed caching | 51 | +15434 / −3070 |  |
| 2026-08-04 | `b757c60` | Other | Achieved parity with Qbix PHP webserver features | 51 | +15434 / −3070 |  |
| 2026-08-06 | `01e71a9` | Updated | Fixed --app mode, did Platform compatibility audit | 0 | +0 / −0 |  |
| 2026-08-10 | `b2c1f69` | Other | Implemented octane mode with persistent preforked workers | 11 | +1184 / −221 |  |
| 2026-08-10 | `2b3d8d7` | Other | Addressed issues 6, 7, 10, 12, 13 | 5 | +273 / −37 |  |
| 2026-08-11 | `54b3933` | Other | Improved server web dashboard and control panel interface | 7 | +116 / −121 |  |
| 2026-08-11 | `42947e2` | Removed | Removed webserver's index.html, you can make your own | 1 | +0 / −44 |  |
| 2026-08-11 | `3cdd7af` | Added | Added logging support | 9 | +699 / −72 |  |
| 2026-08-11 | `1983cf6` | Other | Made websockets work with socket.io in all modern browsers | 13 | +2538 / −204 |  |
| 2026-08-12 | `e69d889` | Other | Improved dashboard and control panel | 16 | +1142 / −110 |  |
| 2026-08-12 | `f310515` | Other | Addressed issues 14, 15, 16 | 11 | +192 / −13 |  |
| 2026-08-12 | `530af5f` | Updated | Fix $_SERVER on the preforked worker path (#14) | 1 | +28 / −2 |  |
| 2026-08-20 | `cebc30e` | Other | Made Qbix webserver methods compatible with Qbix Platform methods | 7 | +288 / −488 |  |
| 2026-08-20 | `ab263eb` | Removed | Removed U-related and Node-related files | 36 | +0 / −6232 |  |
| 2026-08-20 | `1bb1c9f` | Other | Brought README.md up-to-date | 1 | +1 / −1 |  |
| 2026-08-20 | `5c21db2` | Other | Improved README.md code examples | 1 | +72 / −56 |  |
| 2026-08-21 | `53fba6a` | Other | Addressed issue 17: favicon shadowing | 4 | +8 / −4 |  |
| 2026-08-24 | `645db21` | Added | Added support for Server-Sent Events | 5 | +149 / −2 |  |
| 2026-08-24 | `293e038` | Added | Added github static binary build workflow | 2 | +283 / −0 |  |
| 2026-08-24 | `019bcd9` | Removed | Remove old test/ directory (replaced by tests/) | 3 | +0 / −173 |  |
| 2026-08-24 | `025f20e` | Updated | Fixed github workflows support | 2 | +7 / −1 |  |
| 2026-08-24 | `f7a1820` | Updated | Fixes to webserver to make tests pass | 2 | +29 / −19 |  |
| 2026-08-24 | `3da7992` | Other | Automatically building binaries for Mac, Windows and Linux | 2 | +10 / −104 |  |
| 2026-08-24 | `1062104` | Added | Added automatic tests on GitHub Actions | 1 | +104 / −0 |  |
| 2026-08-24 | `7dcb94e` | Other | Building self-contained binaries for Mac, Windows and Linux | 1 | +4 / −2 |  |
| 2026-08-25 | `4983c0b` | Other | Improved compatibility with Qbix Platform | 8 | +381 / −882 |  |
| 2026-08-25 | `223d920` | Other | Improved building static binaries with GitHub Actions on some environments | 1 | +7 / −3 |  |
| 2026-08-25 | `ab326c7` | Other | Improved GitHub actions integration for automatically building binaries | 1 | +11 / −5 |  |
| 2026-08-26 | `6fe3bfa` | Other | Ready for v1.0.0 release build | 1 | +106 / −99 |  |
| 2026-08-30 | `1968668` | Added | Added multiple app examples to show how to use the server | 32 | +2635 / −13 |  |
| 2026-08-30 | `da88851` | Added | Added multiple app examples to show how to use the server | 50 | +2877 / −13 |  |
| 2026-08-30 | `10b0880` | Merged | Merge pull request #18 from zattak1/fix/server-vars-preforked-path | 0 | +0 / −0 |  |
| 2026-08-30 | `7a812ce` | Other | Improved examples showcasing webserver features | 0 | +0 / −0 |  |
| 2026-08-30 | `ed49cd6` | Merged | Merge branch 'main' of https://github.com/Qbix/webserver | 0 | +0 / −0 |  |
| 2026-08-31 | `e021010` | Added | Added examples of writing more secure apps, and improved README | 11 | +340 / −100 |  |

## 2026-09 (631 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-09-07 | `c57a8e0` | Other | Implemented forking off more workers to send large files | 1 | +166 / −15 |  |
| 2026-09-07 | `b2820fe` | Merged | Merge branch 'main' of https://github.com/Qbix/webserver | 0 | +0 / −0 |  |
| 2026-09-07 | `243d1ba` | Added | Added compatibility with Laravel, Symfony, Wordpress, Drupal, Joomla etc. | 4 | +1932 / −2 |  |
| 2026-09-07 | `6b43dd1` | Other | Finalized standalone binaries for Linux, and MacOS | 6 | +746 / −123 |  |
| 2026-09-17 | `8e69b26` | Other | Made existing PHP codebases run 12x faster than with Swoole and FrankenPHP | 13 | +765 / −824 |  |
| 2026-09-19 | `f8d92cf` | Other | Improved docs, dashboard, control panel and added Windows support | 32 | +10005 / −3087 |  |
| 2026-09-21 | `6f73688` | Other | Stop UPX-packing the binary, which corrupted every Linux release | 3 | +32 / −20 |  |
| 2026-09-21 | `a543313` | Other | Build the PHP micro SAPI before copying source into the image | 1 | +8 / −6 |  |
| 2026-09-21 | `c53ff6e` | Other | Keep the builder image so the cached PHP build survives | 1 | +4 / −1 |  |
| 2026-09-21 | `da1a605` | Other | Render phpinfo() as HTML instead of collapsed plain text | 2 | +83 / −2 |  |
| 2026-09-21 | `74bde3f` | Other | Rebuild phpinfo()'s HTML page from its plain-text output | 5 | +395 / −44 |  |
| 2026-09-21 | `722cc2d` | Other | Match phpinfo's own markup rather than approximating it | 2 | +448 / −144 |  |
| 2026-09-21 | `d36ef6d` | Other | Let the document root be listed like any other directory | 3 | +141 / −7 |  |
| 2026-09-21 | `db62801` | Updated | Change package name from qbix/server to se7enxweb/qbox-webserver | 1 | +1 / −1 |  |
| 2026-09-21 | `5741883` | Updated | Updated: Merged the micro binary build fixes from fwoldt/qbix_webserver, which stop the Linux releases shipping a corrupt executable and stop every build rebuilding PHP from source. | 0 | +0 / −0 |  |
| 2026-09-21 | `e8cd8d2` | Other | Shim session_id() instead of calling PHP's | 1 | +47 / −5 |  |
| 2026-09-21 | `822c531` | Updated | Fixed: Fixed the package name, which read se7enxweb/qbox-webserver where the project is qbix, so the name composer resolves matches the repository it comes from. | 1 | +1 / −1 | v0.0.1 |
| 2026-09-21 | `8fe2984` | Updated | Fixed: Fixed the package name, which read se7enxweb/qbox-webserver where the project is qbix, so the name composer resolves matches the repository it comes from. | 1 | +1 / −1 |  |
| 2026-09-21 | `449c030` | Other | Let the Docker build pick the PHP version | 1 | +9 / −5 |  |
| 2026-09-21 | `81b112f` | Other | Parse the sources before packaging them into the binary | 1 | +7 / −0 |  |
| 2026-09-21 | `685d86c` | Other | Carry cookies from the worker to the response | 4 | +152 / −17 |  |
| 2026-09-21 | `ea59aba` | Other | Leave autoloaders registered between requests | 3 | +145 / −6 |  |
| 2026-09-21 | `1c5c155` | Other | Run each script from its own directory | 3 | +142 / −0 |  |
| 2026-09-21 | `ce8ce7b` | Other | Write the transform's replacements fully qualified | 3 | +168 / −2 |  |
| 2026-09-21 | `047f744` | Other | Declare $context on the stream wrapper so notices stay out of bodies | 3 | +147 / −0 |  |
| 2026-09-21 | `9fc5c27` | Other | Keep idle octane workers alive past default_socket_timeout | 3 | +119 / −1 |  |
| 2026-09-21 | `8457079` | Other | Report SCRIPT_NAME as the script's path, not its basename | 3 | +146 / −2 |  |
| 2026-09-21 | `32d5b07` | Other | Carry binary response bodies through the worker socket | 3 | +164 / −0 |  |
| 2026-09-21 | `d4ac159` | Other | Fall back to default_mimetype when a script sets no Content-Type | 3 | +120 / −1 |  |
| 2026-09-21 | `936b449` | Other | Keep a closure in a static, reset everything else | 4 | +158 / −9 |  |
| 2026-09-21 | `509d94b` | Other | Made it easier to migrate from Apache, NGinX, Caddy | 14 | +1592 / −25 |  |
| 2026-09-22 | `49430bc` | Added | Added automatic logs, metrics, and multisig security | 22 | +2795 / −3089 |  |
| 2026-09-22 | `27e7501` | Added | Added: Added a setting naming globals an application keeps between requests. | 2 | +23 / −0 |  |
| 2026-09-22 | `a90fabb` | Updated | Fixed: Fixed a dropped connection killing the server through its own event loop. | 1 | +40 / −2 |  |
| 2026-09-22 | `041c4be` | Updated | Fixed: Fixed fclose() on an already-closed handle taking the whole server down. | 3 | +15 / −15 |  |
| 2026-09-22 | `aba6bbd` | Updated | Fixed: Fixed corrupted TLS records when a file over 1MB was served from a forked child. | 1 | +18 / −3 |  |
| 2026-09-22 | `26106fb` | Updated | Fixed: Fixed large responses being truncated because the count fwrite() returned was discarded. | 1 | +29 / −7 |  |
| 2026-09-22 | `547c11d` | Updated | Fixed: Fixed exit and die ending the worker instead of the request. | 2 | +102 / −0 |  |
| 2026-09-22 | `64da466` | Updated | Fixed: Fixed a Location header returning 200, so redirects were not redirects. | 2 | +30 / −1 |  |
| 2026-09-22 | `0a4a096` | Updated | Fixed: Fixed the file wrapper raising for a missing path, which silently broke a hosted application char set. | 1 | +23 / −5 |  |
| 2026-09-22 | `8dbe533` | Updated | Fixed: Fixed a raw Set-Cookie header being dropped whenever a script also called setcookie(). | 1 | +31 / −12 |  |
| 2026-09-22 | `160aef2` | Merged | Merge remote-tracking branch 'fwoldt/fix/do-not-upx-pack-micro-binary' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `c4f774e` | Merged | Merge remote-tracking branch 'fwoldt/fix/idle-worker-502-after-socket-timeout' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `b7c8ba8` | Merged | Merge remote-tracking branch 'fwoldt/fix/default-content-type-on-php-responses' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `2b2e852` | Merged | Merge remote-tracking branch 'fwoldt/fix/phpinfo-html-under-cli-sapi' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `226286a` | Merged | Merge remote-tracking branch 'fwoldt/fix/root-directory-listing' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `b267aaf` | Merged | Merge remote-tracking branch 'fwoldt/fix/setcookie-crosses-worker-boundary' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `36964c4` | Merged | Merge remote-tracking branch 'fwoldt/fix/session-id-shim' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `fc5d4a9` | Merged | Merge remote-tracking branch 'fwoldt/fix/stream-wrapper-context' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `762e6fc` | Merged | Merge remote-tracking branch 'fwoldt/feat/php-version-selectable' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `aef81ed` | Merged | Merge remote-tracking branch 'fwoldt/fix/keep-request-autoloaders' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `46fee0f` | Merged | Merge remote-tracking branch 'fwoldt/fix/chdir-to-script-directory' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `2905175` | Merged | Merge remote-tracking branch 'fwoldt/fix/script-name-keeps-its-path' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `cdd43a8` | Merged | Merge remote-tracking branch 'fwoldt/fix/transform-fully-qualified' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `3ebd8b3` | Merged | Merge remote-tracking branch 'fwoldt/fix/do-not-reset-new-class-statics' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `de80393` | Merged | Merge branch 'fix/keep-globals-setting' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `4177344` | Merged | Merge branch 'fix/event-loop-survives-dead-connections' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `bdb74d2` | Merged | Merge branch 'fix/guard-closed-stream-handles' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `af0cee1` | Merged | Merge branch 'fix/no-fork-for-tls-connections' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `99a9392` | Merged | Merge branch 'fix/headers-write-every-byte' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `a113ff3` | Merged | Merge branch 'fix/exit-ends-the-request-not-the-worker' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `bf8cbc9` | Merged | Merge branch 'fix/location-header-means-redirect' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `bde1da2` | Merged | Merge branch 'fix/file-wrapper-does-not-raise-for-missing-paths' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `ec6f957` | Merged | Merge branch 'fix/raw-set-cookie-survives-the-shim' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `7655911` | Updated | Fixed: Fixed a raw Set-Cookie header being dropped whenever a script also called setcookie(). | 2 | +45 / −12 |  |
| 2026-09-22 | `597c37b` | Merged | Merge branch 'fix/raw-set-cookie-survives-the-shim' into maintain | 0 | +0 / −0 | v0.0.2.1 |
| 2026-09-22 | `dc3dc45` | Updated | Fixed: Fixed HTTPS and REQUEST_SCHEME never being set, so every redirect over TLS pointed at http. | 1 | +29 / −1 |  |
| 2026-09-22 | `683eec6` | Merged | Merge branch 'fix/tell-the-worker-the-connection-is-tls' into maintain | 0 | +0 / −0 | v0.0.2.2 |
| 2026-09-21 | `63e3f04` | Other | Bundle gd so the image pipeline works in the binary | 2 | +18 / −8 |  |
| 2026-09-21 | `1d8852f` | Other | Bundle the XML, iconv and intl extensions | 2 | +5 / −5 |  |
| 2026-09-22 | `61bf785` | Other | Bundle mysqli, curl, bcmath and exif in the binary | 2 | +5 / −5 |  |
| 2026-09-22 | `196078d` | Merged | Merge pull request #2 from fwoldt/feat/bundle-mysqli-curl-bcmath-exif | 0 | +0 / −0 |  |
| 2026-09-22 | `24359a7` | Merged | Merge branch 'Qbix:main' into main | 0 | +0 / −0 |  |
| 2026-09-22 | `f9c18d6` | Merged | Merge branch 'Qbix:main' into main | 0 | +0 / −0 |  |
| 2026-09-22 | `39612e4` | Merged | Merge branch 'Qbix:main' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `6fd5766` | Updated | Fixed: Fixed the automatic worker count choosing a pool the machine cannot run, by measuring what a worker costs instead of assuming 200KB. | 1 | +33 / −4 |  |
| 2026-09-22 | `32b38db` | Merged | Merge branch 'fix/size-the-worker-pool-from-measured-cost' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `df221e7` | Merged | Merge remote-tracking branch 'origin/maintain' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `545d6c2` | Updated | Fixed: Fixed the worker limit prompt blocking a start that no person is watching, so a service manager can no longer be left waiting on a question. | 1 | +34 / −5 |  |
| 2026-09-22 | `344e877` | Merged | Merge branch 'fix/do-not-prompt-when-nobody-can-answer' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `32327c8` | Merged | Merge remote-tracking branch 'fwoldt/fix/binary-response-bodies' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `53d6a94` | Updated | Fixed: Fixed every file upload failing before the application saw it, by carrying a request body that is not valid UTF-8 through to the worker. | 1 | +50 / −2 |  |
| 2026-09-22 | `e53402a` | Merged | Merge branch 'fix/carry-binary-request-bodies-to-the-worker' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `7779b2f` | Updated | Fixed: Fixed is_uploaded_file() refusing every upload the pool parsed, so an application could not accept a file that had arrived intact. | 2 | +31 / −1 |  |
| 2026-09-22 | `bfd3703` | Merged | Merge branch 'fix/uploads-pass-the-check-that-accepts-them' into maintain | 0 | +0 / −0 | v0.0.2.5 |
| 2026-09-22 | `b4ccee6` | Other | Declare the property the reader catch assigns to | 1 | +11 / −0 |  |
| 2026-09-22 | `a3c2980` | Other | Stop the file wrapper losing writes that take a lock | 1 | +23 / −0 |  |
| 2026-09-22 | `e7cd4ee` | Other | Actually switch on the reverse proxy cache | 2 | +18 / −0 |  |
| 2026-09-22 | `3d2962c` | Merged | Merge pull request #3 from fwoldt/fix/declare-lasterror-on-streamselect | 0 | +0 / −0 |  |
| 2026-09-22 | `3aaa8ed` | Merged | Merge pull request #4 from fwoldt/fix/file-wrapper-loses-locked-writes | 0 | +0 / −0 |  |
| 2026-09-22 | `b7e725e` | Merged | Merge pull request #5 from fwoldt/fix/switch-on-the-reverse-proxy-cache | 0 | +0 / −0 | v0.0.2.6 |
| 2026-09-22 | `74e0878` | Other | Sweep expired entries out of the reverse proxy cache | 3 | +91 / −0 |  |
| 2026-09-22 | `5867f25` | Merged | Merge pull request #6 from fwoldt/fix/sweep-expired-cache-entries | 0 | +0 / −0 | v0.0.2.7 perf/measure-wrapper-vs-transform perf/count-transforms-per-request perf/cheap-checks-in-the-stream-wrapper |
| 2026-09-22 | `b5c6f3e` | Other | Rebuild bin/qbixserver.phar when its sources change | 4 | +172 / −2 |  |
| 2026-09-22 | `64bcd25` | Other | Remember that a file needs no transform, so it is not tokenized on every request | 1 | +30 / −2 |  |
| 2026-09-22 | `fbed426` | Merged | Merge branch 'fix/cache-the-files-that-need-no-transform' into maintain | 0 | +0 / −0 | v0.0.2.8 |
| 2026-09-22 | `3481d1e` | Merged | Merge pull request #7 from fwoldt/fix/rebuild-the-phar-on-release | 0 | +0 / −0 |  |
| 2026-09-22 | `038dc75` | Other | Rebuild bin/qbixserver.phar, which was missing two files and differed in ten more | 1 | +0 / −0 | v0.0.2.9 |
| 2026-09-22 | `6385e78` | Other | Log and count a pooled response when it goes out, not when it is dispatched | 2 | +80 / −6 |  |
| 2026-09-22 | `b2fbe0d` | Other | Let the access line be configured, and stop claiming HTTP/1.1 | 2 | +131 / −8 |  |
| 2026-09-22 | `547b01d` | Other | Let a static file say how long it may be kept | 2 | +40 / −4 |  |
| 2026-09-22 | `0711ec1` | Merged | Merge branch 'feat/configurable-cache-lifetime-for-static-files' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `769be5b` | Other | Rebuild bin/qbixserver.phar for the static cache lifetime change | 1 | +0 / −0 | v0.0.3.0 |
| 2026-09-22 | `876f025` | Merged | Merge pull request #9 from fwoldt/fix/pooled-responses-reach-the-log | 0 | +0 / −0 |  |
| 2026-09-22 | `06dbc4e` | Merged | Merge pull request #8 from fwoldt/feat/configurable-access-log-format | 0 | +0 / −0 |  |
| 2026-09-22 | `77a4028` | Other | Rebuild bin/qbixserver.phar for the access log changes | 1 | +0 / −0 | v0.0.3.1 |
| 2026-09-22 | `d7ff28e` | Other | HTTP/2 wave 1: HPACK, verified against the specification's own vectors | 2 | +683 / −0 |  |
| 2026-09-22 | `2f3d25c` | Other | HTTP/2 wave 2: frames and the connection state machine, answering a real client | 3 | +845 / −0 |  |
| 2026-09-22 | `a378de7` | Other | HTTP/2 wave 3: negotiate it on the real listener and answer from the pool | 3 | +264 / −12 |  |
| 2026-09-22 | `ddfa0eb` | Merged | Merge branch 'feat/http2' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `515cf79` | Other | HTTP/2 wave 4: a page instead of a blank window, and it is configurable | 4 | +362 / −3 |  |
| 2026-09-22 | `1d50f2d` | Other | Rebuild bin/qbixserver.phar for HTTP/2 | 1 | +0 / −0 | v0.0.4.0 |
| 2026-09-22 | `aadc2de` | Other | HTTP/2 wave 4: a page instead of a blank window, and it is configurable | 4 | +362 / −3 |  |
| 2026-09-22 | `00b1578` | Other | HTTP/2 wave 5: compress the body, and stop overrunning the flow-control windows | 1 | +119 / −17 |  |
| 2026-09-22 | `615a2f9` | Merged | Merge branch 'feat/http2' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `6cad614` | Other | Rebuild bin/qbixserver.phar for the HTTP/2 body fixes | 1 | +0 / −0 | v0.0.4.1 |
| 2026-09-22 | `b2385ff` | Merged | Merge branch 'feat/http2' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `53893c2` | Updated | Fix HTTP/2 responses never being compressed, because the test for it could not load | 1 | +35 / −4 |  |
| 2026-09-22 | `0bbfa7a` | Other | Rebuild bin/qbixserver.phar | 1 | +0 / −0 | v0.0.4.2 |
| 2026-09-22 | `eac4791` | Updated | Fix HTTP/2 costing 800ms a request, by draining the TLS buffer instead of reading once | 1 | +21 / −2 |  |
| 2026-09-22 | `1a691d9` | Other | Rebuild bin/qbixserver.phar | 1 | +0 / −0 | v0.0.4.3 |
| 2026-09-22 | `1b26a58` | Updated | Fix HTTP/2 rendering every page from scratch, by consulting the response cache | 1 | +28 / −1 |  |
| 2026-09-22 | `a786ec6` | Merged | Merge branch 'feat/http2' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `f65967b` | Other | Rebuild bin/qbixserver.phar | 1 | +0 / −0 | v0.0.4.4 |
| 2026-09-22 | `05d76c9` | Updated | Fix the TLS handshake stalling on a timer instead of completing | 1 | +26 / −1 |  |
| 2026-09-22 | `62c3cad` | Merged | Merge branch 'fix/tls-handshake-does-not-wait-for-a-timer' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `b0be81f` | Other | Let a TLS session be resumed, by giving the listener one context | 1 | +41 / −16 |  |
| 2026-09-22 | `e3027cf` | Merged | Merge branch 'fix/tls-sessions-can-be-resumed' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `73ec5a5` | Other | Retry the TLS handshake without a test that is always false | 1 | +15 / −5 |  |
| 2026-09-22 | `94878f7` | Merged | Merge branch 'fix/tls-handshake-retries-promptly' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `8d475b6` | Other | Rebuild bin/qbixserver.phar for the TLS handshake and session context changes | 1 | +0 / −0 | v0.0.4.5 |
| 2026-09-22 | `8b591ed` | Other | Send the whole of a response, whatever the window or the socket | 2 | +130 / −8 |  |
| 2026-09-22 | `a57c534` | Other | Let a request ask the cache to renew an entry rather than read it | 1 | +37 / −0 |  |
| 2026-09-22 | `8f8270d` | Merged | Merge branch 'fix/http2-truncated-responses' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `b6ce4c5` | Merged | Merge branch 'feat/cache-refresh-header' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `b7b005a` | Other | Rebuild bin/qbixserver.phar for the response truncation and cache refresh changes | 1 | +0 / −0 |  |
| 2026-09-22 | `c97ba06` | Added | Add HTTP/2 tests for a small flow-control window and a full socket buffer | 3 | +191 / −0 | v0.0.4.6 |
| 2026-09-22 | `a7123dc` | Other | Send the cookies a script set over HTTP/2 as well | 1 | +22 / −0 |  |
| 2026-09-22 | `f815713` | Other | Keep the port out of SERVER_NAME | 1 | +24 / −1 |  |
| 2026-09-22 | `479d4dd` | Merged | Merge branch 'fix/http2-drops-cookies' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `7ef1595` | Merged | Merge branch 'fix/server-name-carries-the-port' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `b6da4ef` | Added | Add an HTTP/2 test for the cookies a script sets | 1 | +88 / −0 |  |
| 2026-09-22 | `d1812ee` | Other | Rebuild bin/qbixserver.phar for the cookie and SERVER_NAME fixes | 1 | +0 / −0 | v0.0.4.7 |
| 2026-09-22 | `bce8816` | Other | Apply the source transform to code included from an archive | 1 | +48 / −0 |  |
| 2026-09-22 | `a815163` | Merged | Merge branch 'fix/compat-misses-phar-includes' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `13a442b` | Other | Rebuild bin/qbixserver.phar for the archive include fix | 1 | +0 / −0 | v0.0.4.8 |
| 2026-09-22 | `85fa820` | Other | Bound every resource an HTTP/2 peer can make the server hold | 4 | +378 / −1 |  |
| 2026-09-22 | `cfab96c` | Merged | Merge branch 'fix/http2-resource-limits' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `39be3c1` | Other | Refuse a header block that lies about its own lengths | 2 | +54 / −1 |  |
| 2026-09-22 | `019e092` | Merged | Merge branch 'fix/hpack-reads-past-the-buffer' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `7a7d2a5` | Added | Add a unit suite that needs no server, and run it first | 5 | +526 / −0 |  |
| 2026-09-22 | `4fc1075` | Merged | Merge branch 'feat/unit-test-suite' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `e3edc7c` | Other | Rebuild bin/qbixserver.phar for the resource limits and HPACK bounds fixes | 1 | +0 / −0 | v0.0.4.9 |
| 2026-09-22 | `5c1074a` | Other | Leave a nullsafe method call alone | 2 | +191 / −0 |  |
| 2026-09-22 | `0ba0af6` | Other | Make the HTTP/2 path guard testable, and test it | 2 | +151 / −1 |  |
| 2026-09-22 | `b696d72` | Merged | Merge branch 'fix/compat-misses-nullsafe-calls' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `3388c19` | Merged | Merge branch 'feat/path-safety-tests' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `c87b467` | Other | Rebuild bin/qbixserver.phar for the nullsafe transform fix | 1 | +0 / −0 | v0.0.4.10 |
| 2026-09-22 | `9ddf23b` | Added | Add a load sweep that says where the server stops getting faster | 1 | +565 / −0 |  |
| 2026-09-22 | `de2bd6f` | Merged | Merge branch 'feat/load-benchmark' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `e46557c` | Other | Disable Nagle on the TLS listener, as the plain one already did | 1 | +29 / −0 |  |
| 2026-09-22 | `f61e059` | Merged | Merge branch 'fix/tls-listener-leaves-nagle-on' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `14bb0c3` | Other | Rebuild bin/qbixserver.phar for the TLS Nagle fix | 1 | +0 / −0 | v0.0.4.11 |
| 2026-09-22 | `b553035` | Other | Stop storing a cached body inside the JSON that describes it | 2 | +185 / −4 |  |
| 2026-09-22 | `7568351` | Merged | Merge branch 'fix/cache-body-inside-json' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `51d3f7c` | Other | Rebuild bin/qbixserver.phar for the cache entry format | 1 | +0 / −0 | v0.0.4.12 |
| 2026-09-22 | `4174b60` | Other | Store the compressed body, instead of compressing it again on every hit | 3 | +280 / −0 |  |
| 2026-09-22 | `30d2d42` | Merged | Merge branch 'fix/cache-recompresses-every-hit' into maintain | 0 | +0 / −0 |  |
| 2026-09-22 | `e691a31` | Other | Rebuild bin/qbixserver.phar for the pre-compressed cache | 1 | +0 / −0 | v0.0.4.13 |
| 2026-09-22 | `54161e3` | Added | Added: Added conditional request handling to the response cache, so a reload of a page the browser already holds sends headers instead of the page. | 3 | +1024 / −29 |  |
| 2026-09-22 | `0deca9a` | Added | Added: Added stale-while-revalidate and negative caching, so an expiry costs one render rather than one per waiting request, and a not-found is not rendered again for every caller. | 2 | +368 / −0 |  |
| 2026-09-22 | `d5d3835` | Updated | Updated: Updated the request path so a cached response is recognised before the routing runs, the listen backlog is a queue rather than a cliff, and every response carries a Date. | 4 | +487 / −19 |  |
| 2026-09-22 | `9a32ab6` | Added | Added: Added optional whitespace collapsing for cached HTML, so the document a browser parses is not mostly indentation. | 2 | +253 / −0 |  |
| 2026-09-22 | `0ac79dd` | Added | Added: Added shared-dictionary compression for stored entries, off by default, with the measurements that say when it is worth switching on. | 2 | +430 / −0 |  |
| 2026-09-22 | `fdeeca1` | Updated | Updated: Updated the README with a configuration reference, worked measurement examples, the security posture and how to contribute. | 1 | +331 / −2 | v0.0.4.14 |
| 2026-09-22 | `93536aa` | Other | Rebuild bin/qbixserver.phar from fdeeca1 | 1 | +0 / −0 |  |
| 2026-09-23 | `5ea4cb3` | Other | Name the access and error logs | 2 | +82 / −10 |  |
| 2026-09-23 | `6755078` | Other | Give a virtual host its own access and error log | 5 | +678 / −25 |  |
| 2026-09-22 | `82c31bc` | Merged | Merge pull request #11 from fwoldt/feature/vhost-access-and-error-logs | 0 | +0 / −0 |  |
| 2026-09-23 | `fbf6286` | Other | Rebuild bin/qbixserver.phar from 82c31bc | 1 | +0 / −0 |  |
| 2026-09-23 | `cd9b844` | Other | Let the log and the cache be told what permissions to use | 6 | +909 / −25 |  |
| 2026-09-23 | `b34d115` | Other | Stop the precompressed cache defaulting to a world-readable directory | 1 | +33 / −4 |  |
| 2026-09-23 | `f05ab31` | Merged | Merge pull request #12 from fwoldt/feature/configurable-file-modes | 0 | +0 / −0 |  |
| 2026-09-23 | `f2f0978` | Other | Rebuild bin/qbixserver.phar from f05ab31 | 1 | +0 / −0 | v0.0.4.15 |
| 2026-09-23 | `72a0701` | Updated | Fixed: Fixed a reused response not saying how old it is, so a cache downstream no longer treats an almost-expired page as freshly generated. | 2 | +186 / −5 | v0.0.4.16 |
| 2026-09-23 | `d4f7ac8` | Other | Rebuild bin/qbixserver.phar from 72a0701 | 1 | +0 / −0 |  |
| 2026-09-23 | `e56038f` | Updated | Fixed: Fixed a forked worker inheriting the parent's whole descriptor table, so one visitor's connection is no longer held open by every worker. | 3 | +249 / −0 |  |
| 2026-09-23 | `3e5de8a` | Merged | Merge branch 'fix/worker-inherits-every-descriptor' into maintain | 0 | +0 / −0 |  |
| 2026-09-23 | `c5c019e` | Merged | Merge origin/maintain (phar rebuild) into the descriptor fix | 0 | +0 / −0 |  |
| 2026-09-23 | `3df93c2` | Other | Rebuild bin/qbixserver.phar from c5c019e | 1 | +0 / −0 |  |
| 2026-09-23 | `d1f97ca` | Other | Rebuild bin/qbixserver.phar from c5c019e | 1 | +0 / −0 |  |
| 2026-09-23 | `8f65b20` | Merged | Merge origin/maintain into the rebuilt archive | 0 | +0 / −0 | v0.0.4.18 |
| 2026-09-23 | `73f7aab` | Added | Added: Added the committed archive's staleness check to the suite a developer runs before a commit, so source drifting from bin/qbixserver.phar is visible then rather than at a release. | 1 | +16 / −1 |  |
| 2026-09-23 | `af95afd` | Merged | Merge branch 'fix/phar-check-missing-from-local-suite' into maintain | 0 | +0 / −0 | v0.0.4.19 |
| 2026-09-23 | `630e467` | Updated | Fixed: Fixed the request that fills the cache being served different bytes from every request after it, by handing the caller back the response as it was stored. | 3 | +51 / −18 |  |
| 2026-09-23 | `40a273b` | Merged | Merge branch 'fix/miss-serves-different-bytes-than-hit' into maintain | 0 | +0 / −0 | v0.0.4.20 |
| 2026-09-23 | `4d6e994` | Updated | Fixed: Fixed WebSocket frames and worker packets being written without checking that they went out whole. | 2 | +287 / −8 |  |
| 2026-09-23 | `475a46c` | Updated | Fixed: Fixed header values being able to write headers of their own, by giving every response one serialiser to go through. | 4 | +259 / −26 |  |
| 2026-09-23 | `b5f73b1` | Updated | Fixed: Fixed a hostname ending in a newline or a hyphen passing validation before it reached certbot, a resolver and the log. | 4 | +183 / −11 |  |
| 2026-09-23 | `3b73102` | Added | Added: Added a test for which content types are worth compressing. | 1 | +83 / −0 |  |
| 2026-09-23 | `863ae48` | Updated | Updated: Updated the committed archive to match the sources in this branch. | 1 | +0 / −0 | v0.0.4.21 |
| 2026-09-23 | `a9e2fbb` | Updated | Fixed: Fixed the worker pool counting a short write of a request as a success. | 4 | +90 / −32 |  |
| 2026-09-23 | `61e0aca` | Added | Added: Added a pre-warm cache that survives a restart, taking startup from 4.7s to 0.6s. | 3 | +394 / −15 |  |
| 2026-09-23 | `dd13a5d` | Updated | Fixed: Fixed the macOS build leg failing, which had been skipping the release job since v0.0.4.19. | 1 | +32 / −2 |  |
| 2026-09-23 | `e0233d9` | Updated | Updated: Updated the committed archive to match the sources in this branch. | 1 | +0 / −0 | v0.0.4.22 |
| 2026-09-23 | `44bf21c` | Updated | Fixed: Fixed the server ignoring SIGTERM, so stop and restart work instead of timing out. | 3 | +36 / −1 |  |
| 2026-09-23 | `97654a7` | Updated | Fixed: Fixed every release carrying one frozen name, and stopped a broken macOS binary blocking the other three platforms. | 1 | +7 / −3 | v0.0.4.23 |
| 2026-09-23 | `a804a46` | Updated | Fixed: Fixed --stop and --reload exiting 0 without sending the signal. | 2 | +11 / −1 |  |
| 2026-09-23 | `f3e8f50` | Added | Added: Added a platform matrix that watches the phar serve a page on systems we ship no binary for. | 2 | +274 / −0 |  |
| 2026-09-23 | `69d8dba` | Added | Added: Added musl, DragonFly, illumos, RISC-V and ARMv5 to the platform matrix. | 1 | +70 / −0 |  |
| 2026-09-23 | `1447c7a` | Updated | Updated: Updated the README to document where this runs, with links that point at this repository's releases. | 2 | +106 / −21 |  |
| 2026-09-23 | `8fd9221` | Updated | Fixed: Fixed every platform job failing, on two unrelated causes. | 2 | +15 / −7 |  |
| 2026-09-23 | `c8d57bb` | Updated | Fixed: Fixed the README offering Windows and macOS downloads that do not exist. | 1 | +26 / −15 |  |
| 2026-09-23 | `1c2a9d0` | Updated | Fixed: Fixed the BSD jobs dying at startup on a missing tokenizer. | 1 | +23 / −6 |  |
| 2026-09-23 | `5ad9a2a` | Updated | Fixed: Fixed the BSD install commands being mangled into one line. | 1 | +1 / −1 | v0.0.4.24 |
| 2026-09-23 | `12cd12e` | Updated | Updated: Updated the phar test to wait longer and say why it gave up. | 1 | +23 / −4 |  |
| 2026-09-23 | `5f9eb78` | Updated | Fixed: Fixed the BSD jobs losing their package installs to a sudo that is not there. | 1 | +4 / −0 |  |
| 2026-09-23 | `32702af` | Updated | Fixed: Fixed php-cgi mode answering every request with "Class Q_WebServer not found". | 2 | +21 / −4 |  |
| 2026-09-23 | `449c436` | Other | Make forkPerRequest able to log a user in | 2 | +40 / −15 |  |
| 2026-09-23 | `2b1dfb6` | Updated | Fixed: Fixed the worker request being written without checking it arrived whole, and made this test say why it failed. | 3 | +34 / −2 |  |
| 2026-09-23 | `e71b16f` | Updated | Updated: Updated the Windows download to retry rather than lose the platform to one bad fetch. | 1 | +13 / −0 |  |
| 2026-09-23 | `0fcfb8c` | Updated | Fixed: Restored sudo on the BSD package installs, which I had removed in error. | 1 | +11 / −11 |  |
| 2026-09-23 | `7d70b7f` | Other | Merged: Merged fix/fork-per-request-loses-headers, so a user can log in with forkPerRequest on. | 0 | +0 / −0 |  |
| 2026-09-23 | `71e824f` | Updated | Fixed: Fixed workers exiting after one request, from a write that handed the socket back non-blocking. | 2 | +20 / −3 |  |
| 2026-09-23 | `c29832c` | Updated | Fixed: Fixed the server dying on FreeBSD after its banner, and added a test for the class of fault behind it. | 3 | +229 / −0 |  |
| 2026-09-23 | `9a4b222` | Updated | Fixed: Fixed the last writes that could go out short without anyone noticing, on the IPC pipe and the session file. | 3 | +84 / −5 |  |
| 2026-09-23 | `4625f71` | Updated | Fixed: Fixed the Windows build being incapable of failing, and made the job check it produced something. | 1 | +24 / −3 |  |
| 2026-09-23 | `f2e434f` | Updated | Fixed: Fixed the macOS test never starting the server, and withdrew the claim that its binary is broken. | 2 | +26 / −12 |  |
| 2026-09-23 | `14809ff` | Updated | Fixed: Fixed riscv64 by turning off PCRE's JIT, which segfaults under emulation. | 1 | +8 / −1 |  |
| 2026-09-23 | `15b18d3` | Added | Added: Added the macOS binary to releases; it was never broken. | 2 | +13 / −17 |  |
| 2026-09-23 | `d3b2670` | Updated | Fixed: Fixed the BSDs dying on a missing SQLite3 extension. | 1 | +9 / −7 |  |
| 2026-09-23 | `cd81120` | Updated | Fixed: Pinned the Windows build's spc to a release instead of tracking upstream's tip. | 1 | +16 / −3 |  |
| 2026-09-23 | `b84b15a` | Updated | Fixed: Fixed DragonFly by dropping a sudo its action does not use. | 1 | +7 / −2 |  |
| 2026-09-23 | `ca56b0a` | Updated | Fixed: Fixed a worker answering from its own stat cache when deciding whether a revalidation claim is abandoned. | 3 | +28 / −0 |  |
| 2026-09-23 | `d625419` | Updated | Fixed: Fixed the server resolving contradictory request framing instead of refusing it, which is request smuggling. | 3 | +316 / −6 |  |
| 2026-09-23 | `db540e9` | Updated | Fixed: Fixed six HTTP/2 frames the RFC says must be refused being accepted instead. | 3 | +251 / −0 |  |
| 2026-09-23 | `35cd98e` | Updated | Fixed: Fixed a symbolic link inside the document root serving, and executing, files outside it. | 3 | +270 / −3 |  |
| 2026-09-23 | `4b8f42a` | Added | Added: Added a security page recording what the server refuses and why, for anyone maintaining this or a fork of it. | 2 | +200 / −0 |  |
| 2026-09-23 | `00220dc` | Updated | Fixed: Gave NetBSD a package repository, without which pkg_add finds nothing at all. | 1 | +8 / −2 | v0.0.4.25 |
| 2026-09-23 | `4494ca1` | Updated | Fixed: Fixed two tests leaving a server running every time they were run. | 2 | +52 / −15 |  |
| 2026-09-23 | `8ff7461` | Updated | Fixed: Fixed the last two BSDs, on two unrelated causes. | 2 | +22 / −1 |  |
| 2026-09-23 | `34ca6d9` | Updated | Fixed: Fixed a signed-in visitor's pages being cached and served to everybody else. | 3 | +124 / −1 | v0.0.4.26 |
| 2026-09-23 | `c0ed985` | Updated | Fixed: Fixed OpenBSD installing nothing at all, and gave NetBSD its missing dependency by name. | 1 | +16 / −3 |  |
| 2026-09-23 | `5f562e7` | Updated | Fixed: Fixed the Windows build failing on a line continuation I wrote in the wrong shell. | 1 | +5 / −2 |  |
| 2026-09-23 | `f4c3a02` | Updated | Fixed: Fixed HTTP/2 serving files that HTTP/1.1 refuses, including configuration and .git. | 2 | +45 / −2 |  |
| 2026-09-23 | `b663c4e` | Updated | Fixed: Fixed the Windows build asking for a library spc cannot build there, and made each release describe itself. | 1 | +32 / −4 |  |
| 2026-09-23 | `4951aea` | Updated | Updated: Updated the release title to carry the summary rather than repeat the tag. | 1 | +14 / −3 |  |
| 2026-09-23 | `6fd2b1f` | Updated | Updated: Updated the platform documentation to advertise what this actually runs on, in three honest tiers. | 1 | +77 / −57 |  |
| 2026-09-23 | `e1129a9` | Updated | Updated: Updated the Amiga answer to be a map for someone who might attempt it. | 1 | +64 / −2 |  |
| 2026-09-23 | `f52b291` | Updated | Fixed: Fixed the Windows build having no archiver to extract php-src with. | 1 | +40 / −0 |  |
| 2026-09-23 | `51e3515` | Updated | Fixed: Fixed the HTTP/2 route answering the server's own URLs, behind the refusals rather than in front of them. | 3 | +148 / −0 |  |
| 2026-09-23 | `35d9a33` | Added | Added: Added a changelog, and made a release something you decide to cut rather than something a tag does. | 3 | +307 / −27 |  |
| 2026-09-23 | `6350966` | Updated | Fixed: Fixed the dashboard not counting most of what HTTP/2 answers, including every refusal. | 4 | +69 / −2 |  |
| 2026-09-23 | `e4e46a0` | Updated | Fixed: Fixed the dashboard printing its JavaScript escapes instead of the characters they stand for. | 4 | +118 / −8 |  |
| 2026-09-23 | `83c830d` | Updated | Updated: Updated the release guidance, which was wrong about what a tag costs. | 1 | +54 / −15 |  |
| 2026-09-23 | `4002cee` | Updated | Fixed: Fixed durations being printed to thirteen decimal places, and a deprecation notice logged once per property. | 6 | +209 / −4 |  |
| 2026-09-23 | `a34150c` | Added | Added: Added a configurable product-name parameter used across the served /Q/ views. | 6 | +166 / −14 |  |
| 2026-09-23 | `277e034` | Added | Added: Added a footer to the served views and the build stamp to the version display. | 7 | +96 / −6 |  |
| 2026-09-23 | `529e9a4` | Added | Added: Added the fork's own shipped version to the display, keeping the upstream number as the engine it is built on. | 5 | +53 / −13 |  |
| 2026-09-23 | `a92d73b` | Updated | Updated: Linked the dashboard's Maintained by 7x line to se7enx.com. | 2 | +2 / −2 |  |
| 2026-09-23 | `7d47fa3` | Added | Added: Added a parent-side preload hook, so a heavy framework is loaded once and shared, not once per worker. | 2 | +42 / −0 |  |
| 2026-09-23 | `0795f46` | Added | Added: Added configurable links for the brand name and a maintainer credit. | 4 | +75 / −4 |  |
| 2026-09-23 | `0fc0c23` | Other | Rebuild bin/qbixserver.phar from 0795f46 | 1 | +0 / −0 |  |
| 2026-09-23 | `7f8196b` | Updated | Fixed: Fixed the worker-memory card reporting several times the memory actually used. | 3 | +80 / −7 |  |
| 2026-09-23 | `046db17` | Updated | Updated: Rebuilt bin/qbixserver.phar to match the dashboard fixes. | 1 | +0 / −0 |  |
| 2026-09-23 | `083d4e8` | Updated | Fixed: Fixed the worker-memory reads wedging the whole server at scale. | 3 | +37 / −34 |  |
| 2026-09-23 | `873581a` | Added | Added: Added swap to the System RAM card, which a percentage of RAM alone hides. | 1 | +14 / −2 |  |
| 2026-09-24 | `b23370a` | Other | Rebuild bin/qbixserver.phar from 873581a | 1 | +0 / −0 |  |
| 2026-09-23 | `48c00b2` | Added | Added: Added an 'exponential' framework preset, so Exponential runs under the server from one setting. | 4 | +100 / −5 |  |
| 2026-09-23 | `e4e6660` | Updated | Updated: Rebuilt bin/qbixserver.phar for the exponential preset. | 1 | +0 / −0 |  |
| 2026-09-23 | `6020928` | Updated | Updated: Updated the package documentation for the preset, the warm-up, and the memory and swap cards. | 5 | +127 / −1 |  |
| 2026-09-23 | `6a88174` | Updated | Fixed: Fixed the parent warm-up compiling the application without the source transform. | 6 | +257 / −14 |  |
| 2026-09-23 | `9b0a00d` | Updated | Updated: Updated the brand heading test for the heading's link to the dashboard. | 1 | +4 / −1 |  |
| 2026-09-23 | `4fa8b88` | Updated | Updated: Rebuilt bin/qbixserver.phar for the warm-up fix. | 1 | +0 / −0 |  |
| 2026-09-23 | `05b7416` | Updated | Updated: Updated the dashboard so its cards and header read at a glance. | 2 | +128 / −7 |  |
| 2026-09-23 | `43bcb6e` | Updated | Fixed: Fixed persistent workers growing without limit, one request at a time. | 12 | +1374 / −74 |  |
| 2026-09-23 | `ae56b0d` | Updated | Updated: Rebuilt bin/qbixserver.phar for the worker memory fixes and the dashboard layout. | 1 | +0 / −0 |  |
| 2026-09-23 | `8b3f72c` | Updated | Fixed: Fixed includes leaking a resource each, and cached scripts never picking up a changed file. | 3 | +265 / −22 |  |
| 2026-09-23 | `96d40d6` | Added | Added: Added a way for application code to have its worker replaced after it answers. | 3 | +69 / −1 |  |
| 2026-09-23 | `8c49a37` | Updated | Updated: Rebuilt bin/qbixserver.phar for the include cache and worker retirement. | 1 | +0 / −0 |  |
| 2026-09-24 | `12980d1` | Other | Stop refusing short request bodies as "not a number" | 3 | +89 / −0 |  |
| 2026-09-24 | `a661584` | Updated | Fixed: Fixed cyclic garbage piling up in workers, and exiting workers deleting the server's pid file. | 4 | +64 / −2 |  |
| 2026-09-24 | `5529b26` | Updated | Fixed: Fixed exit and die after a keyword ending the worker, and cut wrapper registrations to a dozen a request. | 5 | +248 / −24 |  |
| 2026-09-24 | `cf0b7fa` | Updated | Updated: Rebuilt bin/qbixserver.phar for the cycle collection, pid file and transform fixes. | 1 | +0 / −0 |  |
| 2026-09-24 | `d24f28b` | Updated | Updated: Updated the dashboard's Workers card to label what its numbers count. | 3 | +33 / −3 |  |
| 2026-09-24 | `d153c70` | Updated | Updated: Rebuilt bin/qbixserver.phar for the Workers card. | 1 | +0 / −0 |  |
| 2026-09-24 | `ab88695` | Updated | Updated: Updated the dashboard escapes test for the cards that now list one count per row. | 1 | +7 / −1 |  |
| 2026-09-24 | `080387e` | Other | Tell a pooled request the port it arrived on | 3 | +200 / −1 |  |
| 2026-09-24 | `0db408d` | Other | Tell a pooled request who is asking | 4 | +316 / −4 |  |
| 2026-09-24 | `8d0c42c` | Updated | Fixed: Fixed request bodies of 0-47 and 58-255 bytes being refused as "Content-Length is not a number". | 3 | +528 / −0 |  |
| 2026-09-24 | `4b70bff` | Updated | Fixed: Fixed a newly forked worker ending every TLS connection the server had open at that moment. | 3 | +190 / −3 |  |
| 2026-09-24 | `d0a172e` | Updated | Fixed: Fixed file stats and included files going stale within a request or within the same second. | 4 | +773 / −20 |  |
| 2026-09-24 | `5a1b0fc` | Added | Added: Added a dynamic worker pool that runs only the workers it needs, and made a worker's death never cost a request that could be served. | 9 | +1513 / −22 |  |
| 2026-09-24 | `4cf2ccf` | Merged | Merge pull request #14 from fwoldt/fix/content-length-read-as-char-code | 0 | +0 / −0 |  |
| 2026-09-24 | `62d51f4` | Merged | Merge pull request #15 from fwoldt/fix/remote-addr-in-pooled-requests | 0 | +0 / −0 |  |
| 2026-09-24 | `2d402e2` | Added | Added: Added icons, a web app manifest and link previews to the server's own pages. | 6 | +759 / −2 |  |
| 2026-09-24 | `6eeb99b` | Merged | Merge pull request #16 from fwoldt/fix/server-port-in-pooled-requests | 0 | +0 / −0 |  |
| 2026-09-24 | `e9399c8` | Added | Added: Added the exception class, file, line and a short trace to the log when a script's exception reaches the worker. | 3 | +54 / −0 |  |
| 2026-09-24 | `94b9bc8` | Updated | Fixed: Fixed the dashboard's Top paths running the count and average time together, and coloured System RAM by severity. | 3 | +320 / −15 |  |
| 2026-09-24 | `01204cc` | Updated | Fixed: Fixed the server's admin surface and cluster join being open to anyone who could reach the port. | 4 | +280 / −26 |  |
| 2026-09-24 | `4930d46` | Updated | Fixed: Fixed the medium and low findings of the security audit: request framing, log injection, cache personalisation, dashboard injection, HPACK amplification and IPv6 proxy ranges. | 8 | +286 / −23 |  |
| 2026-09-24 | `32c7da2` | Updated | Fixed: Fixed every browser's HTTP/2 connection failing after the HPACK table-size check, by applying the check only to size updates inside a header block. | 3 | +20 / −8 |  |
| 2026-09-24 | `a1bcc26` | Updated | Fixed: Fixed the dashboard's Live requests (empty memory column, newest entries hidden at the bottom) and TLS visitors being recorded as 0.0.0.0. | 6 | +371 / −18 |  |
| 2026-09-24 | `27e543e` | Updated | Fixed: Fixed browsers that cancel streams across many reloads being disconnected as a rapid-reset attack, and logged why every failing GOAWAY was sent. | 2 | +35 / −5 |  |
| 2026-09-24 | `d9ed091` | Updated | Fixed: Fixed HTTP/2 and HTTP/1.1 requests left unread in the TLS buffer, and a pooled response cancelling the reader of its whole HTTP/2 connection. | 1 | +37 / −4 |  |
| 2026-09-24 | `627cda1` | Updated | Fixed: Fixed a script answered over HTTP/2 in fork-per-request mode closing the whole connection, which left signed-in pages without their header, stylesheets and sub-items. | 3 | +176 / −1 |  |
| 2026-09-24 | `0c95046` | Updated | Fixed: Fixed an application's session name and cookie lifetime being ignored, and cached rewrites outliving a change to the rewrite rules. | 3 | +216 / −4 |  |
| 2026-09-24 | `51dcaa2` | Updated | Fixed: Fixed files rewritten after start being served as the warm-up saw them, until a restart. | 2 | +75 / −0 |  |
| 2026-09-24 | `717db84` | Added | Added: Added a generation marker to the response cache, so a deploy can invalidate every cached page by touching one file. | 3 | +144 / −0 |  |
| 2026-09-24 | `c2e899b` | Updated | Fixed: Fixed the server's own certificate, and the TLS tests' ones, being refused where the system will not sign with SHA-1. | 6 | +89 / −10 |  |
| 2026-09-24 | `8ad61e3` | Added | Added: Added a configuration directory laid out like Debian's /etc/apache2, read from /etc/vc or /etc/qbix. | 4 | +395 / −1 |  |
| 2026-09-24 | `4ca259e` | Updated | Fixed: Fixed one hanging test hanging the whole unit run, and leaving the servers it started behind. | 1 | +85 / −4 |  |
| 2026-09-24 | `9e560bc` | Updated | Fixed: Fixed one request for an unknown /Q/ path over HTTP/2 hanging the whole server. | 3 | +133 / −4 |  |
| 2026-09-24 | `ee64142` | Added | Added: Added designs on disk for the server's own pages, so the dashboard, panel, documentation, listing and error pages can be restyled without editing the engine. | 38 | +5249 / −2488 |  |
| 2026-09-24 | `0bec211` | Merged | Merge maintain into fix/tls-certificates-sha256 | 0 | +0 / −0 |  |
| 2026-09-24 | `5e98dfe` | Updated | Updated: Updated the configuration directory to stack overlay trees on /etc/qbix, and added a distribution option for engines built on this one. | 11 | +361 / −53 |  |
| 2026-09-24 | `fbcb3ec` | Added | Added: Added qbixconsole and qbixctl: a library-free console and apache2ctl-style control for the server. | 8 | +768 / −13 |  |
| 2026-09-24 | `ea95624` | Updated | Updated: Accept GNU and BSD spellings of every option on the qbix command lines. | 6 | +209 / −23 |  |
| 2026-09-24 | `3e7ded7` | Updated | Fixed: Fixed a regenerated script running its previous compile when the opcode cache does not check timestamps on every include. | 2 | +62 / −6 |  |
| 2026-09-24 | `c7677cc` | Updated | Updated: Merged the unit runner's per-test time limit (pull request #18 by fwoldt). | 0 | +0 / −0 |  |
| 2026-09-24 | `9454a6a` | Updated | Updated: Accept GNU and BSD spellings of qbixserver.php's one-letter value option, and documented the option styles. | 3 | +26 / −5 |  |
| 2026-09-24 | `ac05b79` | Updated | Updated: Merged SHA-256 signing for the server's own certificate and the TLS tests' ones (pull request #17 by fwoldt). | 0 | +0 / −0 |  |
| 2026-09-24 | `c2ff4ba` | Added | Added: Added a self-looking-after HTTPS certificate: a self-signed one made through a chain of providers, live certificate swaps, and HTTPS before HTTP. | 23 | +1745 / −106 |  |
| 2026-09-24 | `9c37306` | Added | Added: Added certificate sources for your own files, archives and PKCS#12 bundles, and built-in Let's Encrypt (any ACME CA), with a guide in docs/https.md. | 27 | +2621 / −543 |  |
| 2026-09-24 | `b83dde0` | Updated | Fixed: Fixed --verify-binary printing PHP warnings for an unsigned file, and --sign-binary exiting 0 when it could not sign. | 2 | +27 / −15 |  |
| 2026-09-24 | `eca5bec` | Updated | Updated: Run the tests with PHP's own JIT mode, tracing, instead of setup-php's opcache.jit=1235. | 1 | +15 / −0 |  |
| 2026-09-24 | `ef34f90` | Updated | Fixed: Fixed a pooled script answering HEAD with its body and seeing no PATH_INFO, found once CI reached the steps after the main suite. | 6 | +41 / −11 |  |
| 2026-09-24 | `1dc18e5` | Updated | Fixed: Fixed the source transform writing a script's code twice under PHP 8.2 and 8.3's function JIT (opcache.jit=1235). | 3 | +73 / −17 |  |
| 2026-09-24 | `ee0e885` | Added | Added: Added a toolbar linking the server's own views, and pages on the layout, console, designs, response cache and workers. | 19 | +828 / −18 |  |
| 2026-09-24 | `2e7ba1a` | Updated | Fixed: Fixed a file rewritten while a worker sat idle being served once more from the old compile, and a deprecation notice landing in response bodies. | 3 | +19 / −0 |  |
| 2026-09-24 | `111be01` | Updated | Updated: Updated the worker ceiling and include-rewrite tests to hold on any build, with or without opcache. | 4 | +28 / −5 |  |
| 2026-09-24 | `1cead14` | Added | Added: Added QBIX_SHIP_VERSION to build-phar.php, so a release's phar shows the version being released rather than the one before it. | 1 | +9 / −2 |  |
| 2026-09-24 | `05a4023` | Updated | Updated: Updated the changelog with the v0.0.4.27 section, covering everything since v0.0.4.25. | 1 | +115 / −1 |  |
| 2026-09-24 | `5a27804` | Updated | Updated: Rebuilt bin/qbixserver.phar for v0.0.4.27. | 1 | +0 / −0 | v0.0.4.27 |
| 2026-09-24 | `26e80c1` | Added | Added: Added two documentation pages of lessons, general and hard-won, listed in the documentation viewer under Lessons. | 7 | +464 / −0 |  |
| 2026-09-24 | `41ac4e6` | Updated | Updated: Rebuilt bin/qbixserver.phar for the lessons pages in the documentation viewer. | 1 | +0 / −0 |  |
| 2026-09-24 | `886ab81` | Updated | Fixed: Fixed an include of a file being rewritten in place occasionally running the head of one version joined to the tail of the next. | 3 | +146 / −2 |  |
| 2026-09-24 | `9952041` | Updated | Fixed: Fixed the OpenBSD and NetBSD platform jobs, which had failed on every run since they were added. | 1 | +20 / −1 |  |
| 2026-09-24 | `ad858fe` | Updated | Updated: Print the Windows PHP build's own log when that build fails. | 1 | +17 / −0 |  |
| 2026-09-24 | `e1d85e6` | Updated | Fixed: Fixed the panel answering the application's 404 over HTTP/2, the component cache that could not be switched on, and settings the worker pool ignored. | 13 | +1100 / −66 |  |
| 2026-09-24 | `4cbc7d6` | Added | Added: Added qbixctl panel:password, to set or change the control panel password from the command line. | 1 | +70 / −0 |  |
| 2026-09-24 | `2cb19eb` | Updated | Updated: Updated the documentation and help text to what the code does. | 11 | +83 / −31 |  |
| 2026-09-24 | `3130223` | Added | Added: Added a default panel key that must be changed on first sign-in, strict password rules with bcrypt storage, and a lockout, built on panel observers. | 15 | +2374 / −227 |  |
| 2026-09-24 | `03525e1` | Added | Added: Added docs/passwords.md, the panel password page: storage, every rule with an example, settings, the default key, lockout and the CLI. | 7 | +173 / −6 |  |
| 2026-09-24 | `3462c8c` | Updated | Updated: Kept inline code on the panel refusal page from breaking mid-word at narrow widths. | 3 | +5 / −4 |  |
| 2026-09-24 | `5f12e4e` | Added | Added: Added the PHP extension manifest and the ext:* commands, so every build, check and document works from one definition of the extensions the server provides. | 10 | +2803 / −1 |  |
| 2026-09-24 | `e3c07c1` | Updated | Updated: Let digits, symbols and separators repeat in a panel password, and forbid only the whole host name, so real operator keys are accepted. | 7 | +20 / −13 |  |
| 2026-09-24 | `c36885f` | Other | Rebuild bin/qbixserver.phar from e3c07c1 | 1 | +0 / −0 |  |
| 2026-09-24 | `5d82231` | Updated | Updated: Start every command-line script with #!/usr/bin/env and make it executable. | 19 | +13 / −4 |  |
| 2026-09-24 | `a15b6c3` | Updated | Fixed: Fixed the server dying at start with a document root directly under /, such as a container's /app. | 3 | +89 / −1 |  |
| 2026-09-24 | `3109c30` | Updated | Updated: Rebuilt bin/qbixserver.phar for the document-root-under-/ fix. | 1 | +0 / −0 |  |
| 2026-09-24 | `0d728d9` | Added | Added: Added docs/requirements.md and docs/extensions.md: the PHP extensions the server provides, and how to check, install and build them. | 5 | +461 / −2 |  |
| 2026-09-24 | `8460b96` | Added | Added: Added the extension check at start, in /Q/health and on the dashboard, so a PHP missing part of the standard set says so and how to fix it. | 11 | +197 / −1 |  |
| 2026-09-24 | `3a1a6ff` | Updated | Fixed: Fixed ext:build in the source kit rebuilding the phar from a tree without its designs. | 3 | +11 / −3 |  |
| 2026-09-24 | `2d9c4ce` | Updated | Updated: Show the configured brand in the control panel header, linked to the panel, as the dashboard does. | 3 | +2 / −2 |  |
| 2026-09-24 | `19e2a44` | Added | Added: Added the Q shell, a drop-down console on every server view. | 37 | +6569 / −8 |  |
| 2026-09-24 | `cf8806e` | Added | Added: Added a Shell activity card to the dashboard. | 3 | +16 / −0 |  |
| 2026-09-24 | `b3ef85a` | Added | Added: Added the Q shell documentation: the terminal, the language, tiers, safety, jobs and the REST API. | 2 | +179 / −0 |  |
| 2026-09-24 | `3b3fa0e` | Updated | Updated: Rebuilt the phar with the Q shell and its documentation. | 1 | +0 / −0 |  |
| 2026-09-24 | `7c9e60e` | Updated | Updated: Give the dashboard the control panel's layout: the same header, navigation, colours and cards. | 6 | +130 / −40 |  |
| 2026-09-24 | `c06f2a3` | Updated | Updated: Rebuilt the phar with the dashboard's new layout. | 1 | +0 / −0 |  |
| 2026-09-24 | `b313191` | Updated | Updated: Made the dashboard and panel faster and easier to read: a small header icon, legible text sizes, a week-long asset cache, and no redirect for this machine. | 11 | +67 / −64 |  |
| 2026-09-24 | `d466564` | Added | Added: Added a release build for every platform, PHP version and variant, computed from the extension baseline, with a source kit and checksums. | 8 | +482 / −131 |  |
| 2026-09-24 | `cf54d81` | Added | Added: Added deb and rpm packages for Debian 12 and 13, Ubuntu 22.04 and 24.04, and EL 9 and 10, each installed and checked in a clean container. | 15 | +395 / −3 |  |
| 2026-09-24 | `c919a4d` | Added | Added: Added Docker images for every PHP version and variant, for amd64 and arm64, with Oracle, Firebird and ODBC drivers in standard and full. | 8 | +555 / −0 |  |
| 2026-09-24 | `481ea2a` | Updated | Fixed: Fixed the NetBSD platform job on a flaky package mirror, and gave the image-based jobs the sockets extension the server's baseline lists. | 1 | +13 / −12 |  |
| 2026-09-24 | `20af3cf` | Added | Added: Added the extension baseline to the phar platform check: the server's own extensions must all be there, and the rest is reported. | 1 | +21 / −0 |  |
| 2026-09-24 | `50b6450` | Added | Added: Added the documentation for the variants, the Docker images and the OS packages. | 5 | +431 / −4 |  |
| 2026-09-24 | `b2efe08` | Updated | Updated: Rebuilt bin/qbixserver.phar for the documentation viewer's new pages. | 1 | +0 / −0 |  |
| 2026-09-24 | `d5d1a29` | Updated | Fixed: Fixed the server stopping minutes after the shell was used with a dashboard open, and the shell's commands failing without a word. | 9 | +134 / −13 |  |
| 2026-09-24 | `0fd54e0` | Added | Added: Added one registry of the PHP applications the server recognises, used by the panel's Apps and Frameworks tabs and the autohost, so an installation served from its own directory is no longer invisible. | 14 | +1558 / −246 |  |
| 2026-09-24 | `67f6e33` | Updated | Updated: Documented what the panel's Apps and Frameworks tabs detect, where they look, and how version files are read. | 1 | +57 / −1 |  |
| 2026-09-24 | `d76b67e` | Updated | Updated: Rebuilt bin/qbixserver.phar with the application registry. | 1 | +0 / −0 |  |
| 2026-09-24 | `f8478c2` | Updated | Updated: Show the dashboard cards two across on a phone instead of four, so their values no longer wrap mid-number. | 3 | +2 / −2 |  |
| 2026-09-24 | `db0dd8e` | Updated | Fixed: Fixed a signed-in control panel session not being recognised by the dashboard and the other /Q/ views. | 10 | +136 / −41 |  |
| 2026-09-24 | `fd62e76` | Updated | Updated: Show the shell's connection in its title bar, keep it open with a message when the session ends, and test every shell route's sign-in rule. | 4 | +197 / −8 |  |
| 2026-09-24 | `114b91d` | Updated | Fixed: Fixed the control panel flashing its sign-in form on reload for a signed-in visitor. | 8 | +147 / −29 |  |
| 2026-09-24 | `72b6842` | Updated | Updated: Rebuilt bin/qbixserver.phar for the panel opening on the right view. | 1 | +0 / −0 |  |
| 2026-09-24 | `a8d54e4` | Added | Added: List the Q shell page in the documentation's Operations menu. | 2 | +4 / −2 |  |
| 2026-09-24 | `0480a8e` | Added | Added: Added the installation's own tools to the shell as exp commands when the document root is an Exponential installation. | 5 | +180 / −1 |  |
| 2026-09-24 | `4070f91` | Updated | Fixed: Fixed the shell handing the server's sockets and environment to every command, and tightened what the shell trusts. | 9 | +310 / −25 |  |
| 2026-09-24 | `bce0d3d` | Updated | Updated: Rebuilt the phar with the shell's security fixes and exp commands. | 1 | +0 / −0 |  |
| 2026-09-24 | `255b4c5` | Added | Added: Added bookmarkable control panel tabs and a Logs tab that filters by text, method and status. | 9 | +1002 / −87 |  |
| 2026-09-24 | `f6ccbe8` | Added | Added: Added finding a running server without its pid file to qbixctl status, stop and graceful. | 3 | +229 / −4 |  |
| 2026-09-24 | `94fb17b` | Added | Added: Added the qbix distribution, fuller detection of Qbix apps with a site information command, and the same command for the served application. | 8 | +378 / −0 |  |
| 2026-09-24 | `cb81c1d` | Updated | Updated: Rebuilt the phar with the panel tabs and logs, server discovery and the qbix distribution. | 1 | +0 / −0 |  |
| 2026-09-24 | `1be9a47` | Added | Added: Added a separate credential and session store for the control panel, in the configuration tree and its state directory, trusted only when it passes a strict ownership check. | 10 | +1068 / −23 |  |
| 2026-09-24 | `916624f` | Updated | Updated: Documented where the control panel keeps its credentials and sessions, the rule they must pass, panel:check and the move from local/panel.json. | 4 | +78 / −8 |  |
| 2026-09-24 | `0203084` | Updated | Updated: Rebuilt bin/qbixserver.phar with the control panel's credential store. | 1 | +0 / −0 |  |
| 2026-09-24 | `17a4842` | Other | Merged the control panel tabs and logs with the panel data store, and rebuilt the phar from the merged tree. | 0 | +0 / −0 |  |
| 2026-09-24 | `0cd5f9c` | Added | Added: Added window controls to the shell (start or show, hide, maximise, close) and a Shell toolbar item that shows its state. | 8 | +306 / −11 |  |
| 2026-09-24 | `a507aae` | Updated | Updated: Rebuilt the phar with the shell's window controls and toolbar item. | 1 | +0 / −0 |  |
| 2026-09-24 | `85121ee` | Added | Added: Added a metrics view for browsers at /Q/metrics, in the control panel's header and navigation, while scrapers keep the Prometheus text format unchanged. | 9 | +477 / −14 |  |
| 2026-09-24 | `b56a012` | Updated | Updated: Gave the PHP Info view the control panel's header, navigation and colours, with a filter and a list of sections. | 9 | +401 / −3 |  |
| 2026-09-24 | `7e0c716` | Updated | Updated: Rebuilt the phar with the PHP Info view in the server's chrome. | 1 | +0 / −0 |  |
| 2026-09-24 | `aae1200` | Added | Added: Added the domains in use to the control panel's Domains tab, a status per domain (active, suspended, disabled), and the server gate that answers a suspended host with 503. | 12 | +888 / −26 |  |
| 2026-09-24 | `2d374fd` | Updated | Updated: Rebuilt the phar with the domains in use and the domain status gate. | 1 | +0 / −0 |  |
| 2026-09-24 | `8816eec` | Added | Added: Added domain aliases, subdomains and a document root per domain, with host routing on both protocols and a standard doc folder for new domains. | 12 | +1007 / −192 |  |
| 2026-09-25 | `f84e653` | Updated | Updated: Test domain routing over HTTP/2 with TLS as well: two domains, an alias, a subdomain, static files and /Q/, with the response cache on. | 1 | +43 / −0 |  |
| 2026-09-25 | `086bf0b` | Updated | Fixed: Fixed the Domains tab's status menu being cut off on a phone and its Add Domain button missing the panel's button style. | 4 | +4 / −2 |  |
| 2026-09-25 | `abe2931` | Added | Added: Added per-domain redirects (HTTP to HTTPS, preferred www or bare host, custom path and host forwarding), HSTS and custom error documents. | 10 | +947 / −24 |  |
| 2026-09-25 | `1166ba7` | Added | Added: Added the certificate covering each domain to the panel's Domains tab, and issuing or renewing it for one domain as a background job. | 11 | +748 / −41 |  |
| 2026-09-25 | `5cb5cbe` | Added | Added: Added per-domain traffic to the panel's Domains tab and a host filter for its Logs tab, with the Host written at the end of each access log line. | 11 | +487 / −25 |  |
| 2026-09-25 | `bee4ebd` | Added | Added: Added an SSL tab to the control panel: the served certificate, every certificate by expiry, safe settings, renew and reload, and a bounded certificate history. | 13 | +1154 / −0 |  |
| 2026-09-25 | `3475807` | Updated | Updated: Made every control panel settings write go through the panel store's lock, and moved the shell's old files into the state directory. | 6 | +417 / −92 |  |
| 2026-09-25 | `5fd7fa0` | Updated | Updated: Rebuilt bin/qbixserver.phar with the locked panel settings writes and the shell files move. | 1 | +0 / −0 |  |
| 2026-09-25 | `248b6a9` | Updated | Fixed: Fixed the IoPoll event loop backend failing to load, and made every backend behave the same and be tested. | 8 | +439 / −88 |  |
| 2026-09-25 | `68cc5b0` | Updated | Updated: Rebuilt bin/qbixserver.phar with the event loop backend fixes. | 1 | +0 / −0 |  |
| 2026-09-25 | `b54786d` | Updated | Updated: Compressed the server's own pages on HTTP/1.1, gave the views one shared stylesheet, and fixed the dashboard's text contrast and the panel sign-in heading order. | 22 | +402 / −311 |  |
| 2026-09-25 | `4eedbab` | Updated | Updated: Rebuilt bin/qbixserver.phar with the compressed server pages and the shared view stylesheet. | 1 | +0 / −0 |  |
| 2026-09-25 | `4bf4f8b` | Updated | Fixed: Fixed shell commands printing nothing when the console's socket is refused, and the socket being refused on every TLS page. | 5 | +16 / −4 |  |
| 2026-09-25 | `5188454` | Updated | Updated: Rebuilt bin/qbixserver.phar with the shell socket and Origin fixes. | 1 | +0 / −0 |  |
| 2026-09-25 | `8367c44` | Updated | Updated: Tested paths in the compat file wrapper with string comparisons instead of regular expressions. | 2 | +128 / −16 |  |
| 2026-09-25 | `93b5600` | Added | Added: Reported the previous exceptions of an uncaught error in the log, and its place and trace in the response with --debug. | 7 | +345 / −17 |  |
| 2026-09-25 | `2cf8ac8` | Updated | Updated: Rebuilt bin/qbixserver.phar with the wrapper's string path checks and the error diagnostics. | 1 | +0 / −0 |  |
| 2026-09-25 | `6b4fc91` | Updated | Updated: Made the shell's history hold a hundred thousand entries, paged to the console and searched on the server. | 12 | +880 / −84 |  |
| 2026-09-25 | `9884cc9` | Updated | Updated: Rebuilt bin/qbixserver.phar with the shell's paged history. | 1 | +0 / −0 |  |
| 2026-09-25 | `873c0f0` | Updated | Fixed: Fixed the shell's server commands failing for want of the server's configuration, and -f working only at the end of a line. | 8 | +729 / −28 |  |
| 2026-09-25 | `66c7ac6` | Updated | Updated: Rebuilt bin/qbixserver.phar with the shell's server commands run by the server. | 1 | +0 / −0 |  |
| 2026-09-25 | `7d949ee` | Updated | Updated: Let a newer push cancel the Docker and platform runs still queued for an older commit on the same branch, so superseded matrices no longer hold the shared runners. | 2 | +18 / −0 |  |
| 2026-09-25 | `6d2b5d8` | Updated | Fixed: Fixed the shell answering "the shell runner is missing" in every install started from the phar: the packages, the container image and the static binaries. | 12 | +383 / −33 |  |
| 2026-09-25 | `28109ef` | Updated | Fixed: Fixed shell jobs reporting exit code 1 when the server's child reaper reached them first. | 3 | +129 / −3 |  |
| 2026-09-25 | `fba3625` | Updated | Updated: Named the exact directory and command that unlock a locked control panel, first. | 3 | +144 / −3 |  |
| 2026-09-25 | `0da44a8` | Updated | Updated: Made the iopoll event loop opt-in until it has run against the real Io\Poll; auto chooses Revolt when installed, else stream_select, as before. | 4 | +17 / −9 |  |
| 2026-09-25 | `91eb3b1` | Updated | Updated: Added upgrade notes to the changelog: the control panel's storage and trust rule, the access log's new default format, and the shell working from the phar. | 1 | +35 / −1 |  |
| 2026-09-25 | `a408d5e` | Updated | Updated: Rebuilt bin/qbixserver.phar with the shell's runner and console tools embedded. | 1 | +0 / −0 |  |
| 2026-09-25 | `e8f27dd` | Updated | Fixed: Fixed the shell failing in the static binaries, where PHP runs as the micro SAPI with no PHP_BINARY. | 7 | +32 / −11 |  |
| 2026-09-25 | `d77494b` | Updated | Updated: Rebuilt bin/qbixserver.phar with the shell working in the static binaries. | 1 | +0 / −0 |  |
| 2026-09-25 | `77232ae` | Updated | Fixed: Fixed a second server on the same certificate directory and port taking HTTPS away from the first. | 4 | +261 / −16 |  |
| 2026-09-25 | `2e4d322` | Updated | Fixed: Fixed a stopping server removing the pid file or killing the watchdog of another server. | 1 | +27 / −2 |  |
| 2026-09-25 | `e55925e` | Updated | Updated: Rebuilt bin/qbixserver.phar with the certificate copies private to each server. | 1 | +0 / −0 |  |
| 2026-09-25 | `dce8e31` | Added | Added: Added a list of the scripts that may run by name, the front controllers URLs go to, and the files served as they are, so an application can keep everything else to its front controller. | 3 | +480 / −21 |  |
| 2026-09-25 | `e862a4f` | Updated | Updated: Rebuilt the phar with the script, front controller and static path lists. | 1 | +0 / −0 |  |
| 2026-09-25 | `77a94e7` | Updated | Fixed: Fixed a page stored while a script could still run being answered after the script list stopped it. | 2 | +111 / −5 |  |
| 2026-09-25 | `0864b71` | Updated | Updated: Documented anchoring the static path patterns, exact script names, server-wide scope and the cache generation the lists start. | 1 | +22 / −0 |  |
| 2026-09-25 | `2035e65` | Updated | Updated: Rebuilt bin/qbixserver.phar with the listed scripts, front controllers and static paths. | 1 | +0 / −0 |  |
| 2026-09-25 | `8f66809` | Updated | Updated: Updated the changelog with the v0.0.4.28 section, covering everything since v0.0.4.27. | 1 | +116 / −12 |  |
| 2026-09-25 | `82991d1` | Updated | Updated: Rebuilt bin/qbixserver.phar for v0.0.4.28. | 1 | +0 / −0 |  |
| 2026-09-25 | `244d16a` | Renamed | Renamed: Renamed the package to se7enxweb/exponential-velocity, after the repository moved to github.com/se7enxweb/exponential-velocity. | 11 | +29 / −26 |  |
| 2026-09-25 | `061f30f` | Renamed | Renamed: Renamed the operating-system packages, their paths and the image's install path from qbix-webserver to exponential-velocity, after the package. | 18 | +224 / −75 |  |
| 2026-09-25 | `087d655` | Added | Added: Added the takeover from qbix-webserver to the package scripts, and an upgrade test that installs the old package and then the new one over it. | 3 | +152 / −26 |  |
| 2026-09-25 | `086a5ad` | Updated | Updated: Rebuilt bin/qbixserver.phar for v0.0.4.28 with the renamed ACME client. | 1 | +0 / −0 |  |
| 2026-09-25 | `632c3d7` | Updated | Updated: Point the CI branch triggers and the service documentation link at the default branch's new name, main. | 4 | +3 / −4 |  |
| 2026-09-25 | `d37928e` | Updated | Updated: Show static-php-cli's own compiler errors when a Linux or macOS build fails, as the Windows build already does. | 1 | +16 / −0 |  |
| 2026-09-25 | `9905d60` | Updated | Fixed: Fixed the full variant failing to build on every platform, by leaving out what static-php-cli cannot build or register there, each with its reason. | 4 | +104 / −9 |  |
| 2026-09-25 | `debb3be` | Updated | Updated: Rebuilt bin/qbixserver.phar for v0.0.4.28 with the full variant's exclusions. | 1 | +0 / −0 |  |
| 2026-09-25 | `9d6b91c` | Updated | Updated: Redirect a browser to the control-panel login instead of a plain 403 for the admin views, and return to the requested page after signing in. | 5 | +181 / −17 |  |
| 2026-09-25 | `1bc3f58` | Updated | Updated: Rebuilt bin/qbixserver.phar with the admin-view login redirect. | 1 | +0 / −0 |  |
| 2026-09-26 | `4730328` | Updated | Updated: Let the omnios (illumos) platform check run without failing the matrix, and record why. | 3 | +44 / −1 |  |
| 2026-09-26 | `07d62a1` | Updated | Updated: Recorded the package rename and the new default branch in the v0.0.4.28 upgrade notes. | 1 | +10 / −0 |  |
| 2026-09-26 | `1a66410` | Added | Added: Mobile transport sources and the iOS & Android guide, brought from upstream. | 6 | +1907 / −0 |  |
| 2026-09-26 | `9f291e9` | Added | Added: Opt-in peer-to-peer mesh subsystem, disabled by default, brought from upstream. | 12 | +4027 / −0 |  |
| 2026-09-26 | `468ab44` | Added | Added: App-mode, static-files and image-processing docs, and bundle docs into the phar, from upstream. | 4 | +423 / −0 |  |
| 2026-09-26 | `4fdf2fd` | Updated | Fixed: Cast the status code to int before bucketing it in metrics, from upstream. | 1 | +1 / −0 |  |
| 2026-09-26 | `00cf4b6` | Updated | Updated: Expect the bundled docs and README in the phar-currency check. | 1 | +7 / −0 |  |
| 2026-09-26 | `f6fceeb` | Updated | Updated: Rebuilt bin/qbixserver.phar with the opt-in mesh subsystem and the metrics fix. | 1 | +0 / −0 |  |
| 2026-09-26 | `a24fa39` | Updated | Updated: Cap each Platforms job at 30 minutes so a stuck VM boot aborts instead of holding a runner. | 1 | +5 / −0 | v0.0.4.28 |
| 2026-09-26 | `b29cc05` | Updated | Updated: Flag the old ghcr.io/se7enxweb/qbix-webserver image as deprecated, pointing at the exponential-velocity image. | 1 | +3 / −0 |  |
| 2026-09-26 | `10d8a03` | Added | Added: Per-domain SNI certificate selection layered on the per-process cert store | 4 | +371 / −1 |  |
| 2026-09-26 | `8e86409` | Added | Added: Record client IP and user agent (never cookies) on 5xx error metrics | 3 | +135 / −2 |  |
| 2026-09-26 | `44093c3` | Updated | Updated: Rebuilt bin/qbixserver.phar with the per-domain SNI selection and the 5xx IP/UA metrics | 1 | +0 / −0 |  |
| 2026-09-26 | `70c81da` | Added | Added: README section answering how Exponential Velocity differs from Qbix, listing the distribution, control-panel, TLS, shell and operations features it adds. | 2 | +41 / −0 |  |
| 2026-09-26 | `8402fba` | Added | Added: Optional TOTP two-factor authentication for the control panel, off by default. | 18 | +1688 / −9 |  |
| 2026-09-26 | `e4b95b7` | Updated | Updated: Note the optional panel two-factor authentication in the README feature list. | 2 | +3 / −0 |  |
| 2026-09-26 | `cea459d` | Updated | Updated: Record the narrowed illumos (SunOS) serve-check findings in docs/binaries.md | 1 | +55 / −8 |  |
| 2026-09-26 | `c0df098` | Updated | Updated: Run the omnios (illumos) platform check on demand only, not on every push | 1 | +17 / −4 |  |
| 2026-09-26 | `42694ab` | Updated | Updated: Say Connection: close on the last response before keepAlive.max and after a 5xx, instead of promising a connection the server then closes | 3 | +114 / −2 |  |
| 2026-09-26 | `da988db` | Updated | Updated: Rebuilt bin/qbixserver.phar with the keep-alive Connection header fix and the current docs/binaries.md | 1 | +0 / −0 |  |
| 2026-09-26 | `7967099` | Updated | Updated: Remember which paths exist for the rest of a request in pool workers, and forget file facts after another program runs | 3 | +260 / −0 |  |
| 2026-09-26 | `a1cf4b8` | Added | Added: Q.compat.statTtl, to keep what is known about files across requests for up to that many seconds | 3 | +110 / −3 |  |
| 2026-09-26 | `971ad60` | Updated | Updated: Rebuilt bin/qbixserver.phar with the per-request existence memo and Q.compat.statTtl | 1 | +0 / −0 |  |
| 2026-09-26 | `3fbc8ab` | Added | Added: Q.webserver.zygote, to fork later workers from a process that never held a client connection | 2 | +525 / −10 |  |
| 2026-09-26 | `6433d03` | Updated | Updated: Rebuilt bin/qbixserver.phar with Q.webserver.zygote | 1 | +0 / −0 |  |
| 2026-09-26 | `c40f35b` | Updated | Updated: Retry a zygote hand-off interrupted by a signal instead of taking it for a dead zygote | 2 | +64 / −13 |  |
| 2026-09-26 | `e3bbce2` | Updated | Updated: Rebuilt bin/qbixserver.phar with the zygote hand-off retry | 1 | +0 / −0 |  |
| 2026-09-26 | `811b7b7` | Updated | Updated: Documented the keep-alive Connection header, the per-request existence memo and process shims, Q.compat.statTtl and Q.webserver.zygote | 8 | +346 / −12 |  |
| 2026-09-26 | `0179bb4` | Updated | Updated: Rebuilt bin/qbixserver.phar with the documentation | 1 | +0 / −0 |  |
| 2026-09-26 | `f7e7a94` | Updated | Updated: Send the dashboard's stats with at most one live request a second, so an open dashboard stops doubling the cost of every request | 4 | +131 / −6 |  |
| 2026-09-26 | `07f7a28` | Updated | Updated: Rebuilt bin/qbixserver.phar with the dashboard stats throttle | 1 | +0 / −0 |  |
| 2026-09-26 | `ebbcc3c` | Updated | Updated: Show swap on the dashboard's System RAM card as used / total, coloured by swap activity | 6 | +145 / −35 |  |
| 2026-09-26 | `824b384` | Added | Added: Response cache audit with measurements and a ranked list of improvements | 1 | +311 / −0 |  |
| 2026-09-26 | `b166c56` | Updated | Updated: Use APCu for the response cache only when it can hold entries, and say so at startup when it cannot | 3 | +250 / −7 |  |
| 2026-09-26 | `e6ed766` | Updated | Updated: Report where cache hits come from and what APCu holds in /Q/health | 4 | +225 / −3 |  |
| 2026-09-26 | `27f13dd` | Updated | Updated: File cached pages under the coding they are stored in, so brotli and gzip clients share one entry | 3 | +42 / −15 |  |
| 2026-09-26 | `0427ce2` | Updated | Updated: Keep the APCu copy of a cached page through its stale-while-revalidate window | 3 | +101 / −3 |  |
| 2026-09-26 | `8abd876` | Updated | Updated: Let the in-memory static file cache evict the least recently used file when it is full | 3 | +84 / −4 |  |
| 2026-09-26 | `da1d435` | Updated | Updated: Honour q=0 in Accept-Encoding for cached pages, static files and precompressed files | 7 | +80 / −10 |  |
| 2026-09-26 | `83d83a7` | Updated | Updated: Answer HEAD requests for cached pages from the cache | 4 | +87 / −7 |  |
| 2026-09-26 | `3c4377a` | Added | Added: Optional in-process memory layer in front of APCu for cached pages | 6 | +398 / −10 |  |
| 2026-09-26 | `c80278c` | Updated | Updated: Recorded the results of the response cache changes in the audit | 2 | +35 / −2 |  |
| 2026-09-26 | `9810b56` | Updated | Updated: Rebuilt bin/qbixserver.phar with the response cache changes | 1 | +0 / −0 |  |
| 2026-09-26 | `40e6bd0` | Added | Added: docs/panel.md, how to sign in to the control panel, starting with its default password | 3 | +106 / −0 |  |
| 2026-09-26 | `0700e2f` | Added | Added: Q.dashboard.hidePanelRequests, to keep the server's own /Q/ requests out of the dashboard | 6 | +139 / −2 |  |
| 2026-09-26 | `bfc7930` | Updated | Updated: Rebuilt bin/qbixserver.phar with the panel sign-in guide and hidePanelRequests | 1 | +0 / −0 |  |
| 2026-09-26 | `83637f0` | Updated | Updated: Name the zygote in the process list and let the pool tests count workers forked by it | 6 | +61 / −10 |  |
| 2026-09-26 | `2240e2e` | Updated | Updated: Fork pool workers from the zygote by default | 4 | +6 / −6 |  |
| 2026-09-26 | `038dd8b` | Updated | Updated: Rebuilt bin/qbixserver.phar with the zygote on by default | 1 | +0 / −0 |  |
| 2026-09-26 | `01f268c` | Updated | Updated: Q_WebServer_Cache::purge() says how many entries it removed and can be told whether its argument is a regex. | 1 | +33 / −5 |  |
| 2026-09-26 | `4526699` | Added | Added: Cache routes in the control panel API, with settings that are kept by the panel and survive a restart. | 4 | +1021 / −0 |  |
| 2026-09-26 | `8c4b095` | Added | Added: A Cache tab in the control panel, to run the response cache from the browser. | 4 | +1144 / −0 |  |
| 2026-09-26 | `92ad70c` | Added | Added: The Cache tab and its API in docs/dashboard.md, and a pointer from docs/cache.md. | 2 | +89 / −0 |  |
| 2026-09-26 | `183045a` | Updated | Updated: Rebuilt bin/qbixserver.phar with the control panel's Cache tab | 1 | +0 / −0 |  |
| 2026-09-26 | `1b904c3` | Merged | Merge branch 'zygote-default-on' into candidate | 0 | +0 / −0 |  |
| 2026-09-26 | `69d686d` | Other | Merged the dashboard's clearer swap figure, and rebuilt the phar from the merged tree | 0 | +0 / −0 |  |
| 2026-09-26 | `f7f09f8` | Other | Merged the response cache work and the control panel's Cache tab, sign-in guide and hidePanelRequests | 0 | +0 / −0 |  |
| 2026-09-26 | `9dfa420` | Updated | Updated: Send the designed error page, in plain words, for every error the server answers itself | 4 | +91 / −28 |  |
| 2026-09-26 | `b69ca16` | Updated | Updated: Rebuilt bin/qbixserver.phar with the designed error pages | 1 | +0 / −0 |  |
| 2026-09-26 | `aee8c9c` | Updated | Updated: Hand an uncaught exception to the application's exception handler, and keep its message out of responses | 8 | +128 / −17 |  |
| 2026-09-26 | `27f98ec` | Updated | Updated: Rebuilt bin/qbixserver.phar with the application exception handler and message-free error responses | 1 | +0 / −0 |  |
| 2026-09-26 | `9aeaf95` | Updated | Updated: Worker memory, reset cost and shim count in the documentation, from measurements | 8 | +92 / −38 |  |
| 2026-09-26 | `22b8ada` | Updated | Updated: Count every CPU in the load benchmark's machine report | 3 | +5 / −3 |  |
| 2026-09-26 | `ac22e8b` | Updated | Updated: Rebuilt bin/qbixserver.phar with the measured memory figures in the documentation | 1 | +0 / −0 |  |
| 2026-09-26 | `0f09447` | Updated | Updated: Changelog section for v0.0.4.29. | 1 | +79 / −1 |  |
| 2026-09-26 | `da5bff6` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.29. | 1 | +0 / −0 | v0.0.4.29 docs-measured-memory |
| 2026-09-26 | `bcb0901` | Updated | Updated: Use the zygote only where PHP passes sockets between processes intact | 4 | +133 / −3 |  |
| 2026-09-26 | `87456ca` | Updated | Updated: Rebuilt bin/qbixserver.phar with the zygote socket-passing check | 1 | +0 / −0 |  |
| 2026-09-26 | `eb254c2` | Updated | Updated: Changelog section for v0.0.4.30. | 1 | +26 / −0 |  |
| 2026-09-26 | `95a2c69` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.30. | 1 | +0 / −0 | v0.0.4.30 zygote-scm-rights-selftest |
| 2026-09-26 | `f7e9ad2` | Updated | Updated: Keep sessions where session.save_path says, in PHP's [N;[MODE;]]/path form, and create them with its mode | 2 | +183 / −9 |  |
| 2026-09-26 | `e05342c` | Updated | Updated: Rebuilt bin/qbixserver.phar with sessions kept where session.save_path says | 1 | +0 / −0 |  |
| 2026-09-26 | `fa4cedc` | Added | Added: Ask an application's own page cache before a worker (Q.web.appCache) | 5 | +308 / −0 |  |
| 2026-09-26 | `8bffb07` | Updated | Updated: Rebuilt bin/qbixserver.phar with the application cache hook | 1 | +0 / −0 |  |
| 2026-09-26 | `169a050` | Updated | Updated: Tell the application that HTTP/2 requests came over HTTPS | 2 | +9 / −1 |  |
| 2026-09-26 | `e50595f` | Updated | Updated: Rebuilt bin/qbixserver.phar with HTTP/2 requests marked as HTTPS | 1 | +0 / −0 |  |
| 2026-09-26 | `b4cd8c5` | Updated | Updated: Report the release and build of a checkout from git rather than from a leftover build stamp | 1 | +11 / −3 |  |
| 2026-09-26 | `c7c5ed3` | Updated | Updated: Rebuilt bin/qbixserver.phar with the release read from git in a checkout | 1 | +0 / −0 |  |
| 2026-09-27 | `d7b0a6c` | Added | Added: --version, --about and --copyright in every program, GNU style | 12 | +357 / −5 |  |
| 2026-09-27 | `6a00772` | Updated | Updated: Rebuilt bin/qbixserver.phar with --version, --about and --copyright | 1 | +0 / −0 |  |
| 2026-09-27 | `c532951` | Updated | Updated: Name the program without .php or .phar on the first line of --version | 2 | +2 / −2 |  |
| 2026-09-27 | `900eae9` | Updated | Updated: Rebuilt bin/qbixserver.phar with the program named without its extension | 1 | +0 / −0 |  |
| 2026-09-27 | `ce98c59` | Updated | Updated: Give the commit date of --version in UTC, as it says | 1 | +1 / −1 |  |
| 2026-09-27 | `3120c43` | Updated | Updated: Rebuilt bin/qbixserver.phar with the commit date in UTC | 1 | +0 / −0 |  |
| 2026-09-27 | `528d284` | Updated | Updated: Changelog for v0.0.4.31 | 1 | +40 / −0 |  |
| 2026-09-27 | `3598f05` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.31. | 1 | +0 / −0 | v0.0.4.31 |
| 2026-09-27 | `604964a` | Updated | Updated: Answer requests without a session cookie from the response cache before asking the application's cache | 3 | +94 / −6 |  |
| 2026-09-27 | `723077e` | Updated | Updated: Rebuilt bin/qbixserver.phar with the response cache asked first for requests without a session | 1 | +0 / −0 |  |
| 2026-09-27 | `2e81842` | Updated | Fixed: Run the zygote on PHP 8.2 and 8.3 too, handing each worker its connection as a stream | 3 | +39 / −30 |  |
| 2026-09-27 | `53c9177` | Updated | Updated: Keep the application cache's answer to a request without a session in the response cache | 2 | +32 / −2 |  |
| 2026-09-27 | `41e9016` | Updated | Updated: Rebuilt bin/qbixserver.phar with the zygote on PHP 8.2 and 8.3 and the application cache's answers kept | 1 | +0 / −0 |  |
| 2026-09-27 | `118a387` | Updated | Fixed: Keep a release from being held back by one refused Docker pull or musl download, and build PHP 8.5 without memcache | 6 | +27 / −2 |  |
| 2026-09-27 | `77b64ab` | Updated | Updated: Document that the response cache keeps the application cache's answers and must hear of its purges | 1 | +7 / −1 |  |
| 2026-09-27 | `76272c6` | Updated | Updated: Rebuilt bin/qbixserver.phar with PHP-version exclusions and the cache documentation | 1 | +0 / −0 |  |
| 2026-09-27 | `090bd97` | Updated | Fixed: Keep the application cache's gzip answer to a browser in the response cache, so browsers are answered from it too | 2 | +63 / −11 |  |
| 2026-09-27 | `2487afa` | Updated | Updated: Rebuilt bin/qbixserver.phar with the application cache's gzip answers kept in the response cache | 1 | +0 / −0 |  |
| 2026-09-27 | `aa48e49` | Updated | Updated: Document the Revolt event loop: what it needs, installing it system-wide, configuring it, and what it measured | 6 | +87 / −9 |  |
| 2026-09-27 | `6fca3c4` | Updated | Updated: Rebuilt bin/qbixserver.phar with the Revolt documentation and the extension manifest's event loop entries | 1 | +0 / −0 |  |
| 2026-09-27 | `5b6318a` | Added | Added: Q.webserver.reusePort, so several servers can listen on one port and cached pages use more than one core | 3 | +68 / −4 |  |
| 2026-09-27 | `16de465` | Updated | Updated: Rebuilt bin/qbixserver.phar with Q.webserver.reusePort | 1 | +0 / −0 |  |
| 2026-09-27 | `52cd58f` | Updated | Fixed: A purge reaches the response cache at once in the server process, not after the compat wrapper forgets its file stats | 2 | +24 / −0 |  |
| 2026-09-27 | `6856bf6` | Updated | Updated: Rebuilt bin/qbixserver.phar with the response cache seeing a purge at once in the server process | 1 | +0 / −0 |  |
| 2026-09-27 | `ac72c7a` | Updated | Fixed: The response cache really forgets the generation marker's remembered stat, so a purge is seen within the second | 2 | +62 / −1 |  |
| 2026-09-27 | `642064c` | Updated | Updated: Rebuilt bin/qbixserver.phar with the response cache forgetting the marker's stat on the right class | 1 | +0 / −0 |  |
| 2026-09-27 | `190fe92` | Updated | Updated: Store the application cache's compressed HTML answer like a rendered page, so every server holds the same bytes and validator | 2 | +54 / −2 |  |
| 2026-09-27 | `722d420` | Updated | Updated: Rebuilt bin/qbixserver.phar with compressed application cache answers stored like rendered pages | 1 | +0 / −0 |  |
| 2026-09-27 | `146e3ce` | Updated | Updated: Changelog for v0.0.4.32 | 1 | +65 / −0 |  |
| 2026-09-27 | `84dac5d` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.32. | 1 | +0 / −0 | v0.0.4.32 origin/release-prep-v0.0.4.32 compat-session-save-path |
| 2026-09-27 | `50176b3` | Updated | Fixed: A PHP file read through the compat file wrapper gives the file's own bytes, not the transformed source | 2 | +77 / −7 |  |
| 2026-09-27 | `8c7ec5d` | Updated | Updated: Changelog for v0.0.4.33 | 1 | +17 / −0 |  |
| 2026-09-27 | `3e80587` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.33. | 1 | +0 / −0 | v0.0.4.33 compat-transform-includes-only |
| 2026-09-27 | `e22031f` | Added | Added: Q.web.cache.pauseFile, a file whose existence pauses the response caches | 1 | +29 / −0 |  |
| 2026-09-27 | `dbda08a` | Added | Added: Workers give up root and run as a configured user and group, like Apache's User and Group | 8 | +685 / −6 |  |
| 2026-09-27 | `543b9db` | Updated | Updated: Documented --user, --group, Q.webserver.user/group and the RUN_USER/RUN_GROUP environment | 3 | +54 / −1 |  |
| 2026-09-27 | `50c0bbd` | Updated | Updated: Changelog for v0.0.4.34 | 1 | +34 / −0 |  |
| 2026-09-27 | `1d3f196` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.34. | 1 | +0 / −0 | v0.0.4.34 |
| 2026-09-27 | `0a43836` | Added | Added: Embedded OpenType fonts (.eot) are served with their media type | 1 | +2 / −2 |  |
| 2026-09-27 | `f3eff00` | Updated | Updated: The exponential preset brings its own static, script and front controller lists when the configuration names none | 2 | +76 / −0 |  |
| 2026-09-27 | `eb79d8e` | Updated | Fixed: A static file is served whole to a client that takes no gzip when precompression is on | 2 | +179 / −16 |  |
| 2026-09-27 | `b5ea8d2` | Other | Rebuild bin/qbixserver.phar from eb79d8e | 1 | +0 / −0 |  |
| 2026-09-27 | `78b3ecf` | Updated | Updated: The tests rebuild the phar first and run against it, so a push that changes the sources is tested with the phar that lands on main | 2 | +74 / −10 |  |
| 2026-09-27 | `0ac4f95` | Updated | Fixed: A WWW-Authenticate header answers 401 and every worker sees the request headers, as under PHP's own servers | 2 | +26 / −0 |  |
| 2026-09-27 | `24d231f` | Added | Added: Q.webserver.headers, headersOnScripts and hsts put configured headers and Strict-Transport-Security on every response, static files included, without a domain record | 6 | +625 / −25 |  |
| 2026-09-28 | `6b7c850` | Added | Added: GitHub funding metadata, the same as the other se7enxweb packages | 1 | +3 / −0 |  |
| 2026-09-28 | `1699885` | Other | Rebuild bin/qbixserver.phar from 6b7c850 | 1 | +0 / −0 |  |
| 2026-09-28 | `559acb4` | Updated | Fixed: The application's page cache is handed the request headers and the listener's port, so it finds a page stored behind a load balancer that ends TLS | 2 | +22 / −3 |  |
| 2026-09-28 | `5c7062d` | Other | Rebuild bin/qbixserver.phar from 559acb4 | 1 | +0 / −0 |  |
| 2026-09-28 | `e3353a5` | Merged | Merge pull request #20 from fwoldt/fix/appcache-hands-over-the-request-headers | 0 | +0 / −0 |  |
| 2026-09-28 | `1b6be3b` | Updated | Updated: CHANGELOG for v0.0.4.35 | 1 | +63 / −0 |  |
| 2026-09-28 | `fa6dd23` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.35. | 1 | +0 / −0 | v0.0.4.35 |
| 2026-09-28 | `9777e80` | Updated | Fixed: Parameters with a null default are typed as nullable, so PHP 8.4 and later raise no deprecation for them | 9 | +9 / −9 |  |
| 2026-09-28 | `fb035a3` | Other | Rebuild bin/qbixserver.phar from 9777e80 | 1 | +0 / −0 |  |
| 2026-09-28 | `2921872` | Updated | Fixed: A warm-up that fails halfway no longer leaves its request in every worker | 2 | +175 / −1 |  |
| 2026-09-28 | `30db6d3` | Updated | Updated: Rebuilt bin/qbixserver.phar from the current sources | 1 | +0 / −0 |  |
| 2026-09-28 | `64b078c` | Updated | Updated: CHANGELOG for v0.0.4.36 | 1 | +29 / −0 |  |
| 2026-09-28 | `e380110` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.36. | 1 | +0 / −0 | v0.0.4.36 |
| 2026-09-29 | `c1f3a26` | Updated | Fixed: Multipart form fields with nested names such as a[0][id] reach $_POST as nested arrays, as PHP builds them | 5 | +634 / −92 |  |
| 2026-09-29 | `dcdbab6` | Updated | Updated: CHANGELOG for v0.0.4.37 | 1 | +34 / −0 |  |
| 2026-09-29 | `afa21d3` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.37. | 1 | +0 / −0 | v0.0.4.37 fix-multipart-nested-keys |
| 2026-09-29 | `86ade5e` | Updated | Fixed: compress.zlib:// and other streams that need a file descriptor work through the file layer | 1 | +19 / −0 |  |
| 2026-09-29 | `81646a6` | Updated | Fixed: Stream options and quiet opens on files read through the file layer | 2 | +247 / −2 |  |
| 2026-09-29 | `4bae92e` | Updated | Fixed: A request body over post_max_size gets a clean 413, and one under it reaches the application whole | 6 | +805 / −33 |  |
| 2026-09-29 | `db4f18c` | Updated | Updated: CHANGELOG for v0.0.4.38 | 1 | +79 / −0 |  |
| 2026-09-29 | `33e31dd` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.38. | 1 | +0 / −0 |  |
| 2026-09-29 | `61b96d5` | Updated | Updated: CHANGELOG dates v0.0.4.38 on the day it is released | 1 | +1 / −1 | v0.0.4.38 release-prep-v0.0.4.38 fix-rest-and-cache-174-179 |
| 2026-09-30 | `a81335c` | Updated | Fixed: The response cache stays off unless a setting turns it on | 3 | +149 / −1 |  |
| 2026-09-30 | `ea907cd` | Updated | Updated: CHANGELOG for v0.0.4.39 | 1 | +37 / −0 |  |
| 2026-09-30 | `2f72c52` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.39. | 1 | +0 / −0 | v0.0.4.39 cache-default-off-without-configuration |
| 2026-09-30 | `3e1e9e7` | Updated | Fixed: qbixctl restart starts the server again with the options it was started with | 8 | +903 / −26 |  |
| 2026-09-30 | `1646ef5` | Updated | Updated: The race harness writes a test's configuration as the test gives it | 1 | +8 / −5 |  |
| 2026-09-30 | `4d35e1b` | Updated | Updated: tests/unit-distribution-vc.php checks the overlay's log directory | 1 | +4 / −2 |  |
| 2026-09-30 | `a446d5c` | Updated | Updated: CHANGELOG for v0.0.4.40 | 1 | +63 / −0 |  |
| 2026-09-30 | `9773cf3` | Updated | Updated: Rebuilt bin/qbixserver.phar stamped v0.0.4.40. | 1 | +0 / −0 | v0.0.4.40 qbixctl-restart-options-logs-harness |
| 2026-09-30 | `55ce925` | Renamed | Renamed: The server, qbixctl and qbixconsole move to sbin/ and the shell to bin/, with forwarders at their former paths | 21 | +2379 / −2112 |  |
| 2026-09-30 | `fdc2ffc` | Renamed | Renamed: The uwebserver binary moves to sbin/ and its C sources to native/uwebserver/ | 9 | +14 / −8 |  |
| 2026-09-30 | `7245e2f` | Updated | Updated: The packages install qbixserver, qbixctl and qbixconsole in /usr/sbin, with links at /usr/bin | 18 | +134 / −39 |  |
| 2026-09-30 | `c1b5fcb` | Updated | Updated: The workflows build, test, upload and combine sbin/qbixserver.phar | 5 | +30 / −21 |  |
| 2026-09-30 | `75cd116` | Updated | Updated: The tests run the programs at their new paths in sbin/ and bin/ | 51 | +73 / −68 |  |
| 2026-09-30 | `443833f` | Added | Added: A test that every former program path behaves exactly like the new one | 1 | +319 / −0 |  |
| 2026-09-30 | `bbb8953` | Updated | Updated: The README and the documentation describe the sbin/ and bin/ layout and name the new paths | 27 | +221 / −83 |  |
| 2026-09-30 | `d3bac25` | Updated | Fixed: tests/unit-worker-pool-dynamic.php waits for a fixed pool's workers instead of counting them once | 1 | +4 / −1 |  |
| 2026-09-30 | `310cfda` | Updated | Updated: CHANGELOG for v0.0.4.41 | 1 | +93 / −0 |  |
| 2026-09-30 | `048e474` | Updated | Updated: Rebuilt sbin/qbixserver.phar and its copy at bin/qbixserver.phar stamped v0.0.4.41. | 2 | +0 / −0 | v0.0.4.41 fhs-bin-sbin |
| 2026-09-30 | `ab9adff` | Updated | Updated: The README and the documentation name the server Velocity, and state once that it began as a fork of the Qbix web server engine | 22 | +90 / −86 |  |
| 2026-09-30 | `f798347` | Updated | Updated: Rebuilt sbin/qbixserver.phar and its copy at bin/qbixserver.phar with the Velocity wording of the documentation | 2 | +0 / −0 |  |
| 2026-09-30 | `1391a69` | Added | Added: The packages install the shell as /usr/bin/vc-qshell | 10 | +47 / −3 |  |
| 2026-09-30 | `b70e318` | Added | Added: docs/uwebserver.md describes what uwebserver is for, who runs it, how it is built and what it did in 0.0.4.41 | 1 | +108 / −0 |  |
| 2026-09-30 | `5535a44` | Updated | Fixed: uwebserver reads its whole command line before it acts, answers --help, and starts nothing on an argument it does not know | 6 | +619 / −59 |  |
| 2026-09-30 | `06be147` | Removed | Removed: The generated record types and the U runtime are no longer compiled into uwebserver | 1 | +10 / −963 |  |
| 2026-09-30 | `c24e765` | Added | Added: uwebserver serves a document root, listens on 127.0.0.1:8000 by default, and has options for every common need and a configuration file | 11 | +3220 / −792 |  |
| 2026-09-30 | `bf90c87` | Added | Added: uwebserver has a manual page, bash completion, a Makefile and full documentation, and its --help is grouped and fits 80 columns | 9 | +886 / −50 |  |
| 2026-09-30 | `b16812b` | Updated | Fixed: uwebserver reads request heads strictly, builds without a warning under -Werror, and runs its tests clean under AddressSanitizer and UndefinedBehaviorSanitizer | 10 | +289 / −39 |  |
| 2026-09-30 | `78ba6ef` | Updated | Fixed: uwebserver bounds what a client can hold: head parsing, the timeout sweep, and workers that keep ending | 4 | +255 / −5 |  |
| 2026-09-30 | `b260911` | Updated | Fixed: uwebserver writes only files that are its own, reads a configuration as root only from a file others cannot change, and sends no TLS session tickets | 5 | +201 / −22 |  |
| 2026-09-30 | `15ee8af` | Added | Added: uwebserver's release build is hardened, and its request parsing has a fuzz harness | 7 | +487 / −103 |  |
| 2026-09-30 | `bac39f0` | Updated | Updated: Rebuilt sbin/uwebserver from its sources with the Makefile, stamped v0.0.4.42 | 2 | +7 / −5 |  |
| 2026-09-30 | `a5f7754` | Updated | Updated: CHANGELOG for v0.0.4.42 | 1 | +103 / −0 |  |
| 2026-09-30 | `1c729ad` | Updated | Updated: Rebuilt sbin/qbixserver.phar and its copy at bin/qbixserver.phar stamped v0.0.4.42. | 2 | +0 / −0 |  |
| 2026-09-30 | `1d7a548` | Removed | Removed: The unused uwebserver runtime headers u_runtime.h and u_merkle_cache.h | 3 | +1 / −6975 |  |
| 2026-09-30 | `7309b98` | Updated | Updated: The uwebserver documentation says what changes on upgrading from 0.0.4.41, and that nothing in a site starts it | 3 | +33 / −1 |  |
| 2026-09-30 | `f334398` | Updated | Updated: Rebuilt sbin/qbixserver.phar and its copy at bin/qbixserver.phar stamped v0.0.4.42, with the uwebserver upgrade notes | 2 | +0 / −0 |  |
| 2026-09-30 | `c9854aa` | Updated | Fixed: A pool worker reports each request's own start time in REQUEST_TIME_FLOAT and REQUEST_TIME | 2 | +78 / −0 |  |
| 2026-09-30 | `4dc2cfc` | Updated | Fixed: The compat session handler decodes each session value from its own bytes, without a warning | 2 | +187 / −6 |  |

## 2026-10 (2 changes)

| Date | Commit | Kind | Change | Files | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-10-01 | `540d3d8` | Updated | Updated: CHANGELOG for v0.0.4.42 | 1 | +18 / −1 |  |
| 2026-10-01 | `da360b4` | Updated | Updated: Rebuilt sbin/qbixserver.phar and its copy at bin/qbixserver.phar stamped v0.0.4.42. | 2 | +0 / −0 | v0.0.4.42 origin/main origin/HEAD |
