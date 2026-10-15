# The secret check of extension packages

Since 6.0.15, no package takes an extension that holds secret values or key material. Setup > Extensions, the
package wizard and `ezpm.php add` all ask `eZExtensionPackageHandler::packRefusal()`, and `addExtension()` asks
again on the copy it makes. A refused extension is named in the page's notice with its first five findings: the file
and the setting (or the kind of secret), never the value.

## What is refused

| Finding | What it means |
|---|---|
| `<file>: <Setting>` | A setting whose name marks a secret (`Password`, `ApiKey`, `ClientSecret`, `Token` ...) has a value, or another setting carries a password inside its value (`Source=https://user:pw@host/`). |
| `<file>: (private key)` | A private key block (`-----BEGIN ... PRIVATE KEY-----`), in any file, text or binary. Always refused. |
| `<file>: (bearer token)`, `(API key)`, `(constant)`, `(address with a password)` | The shape of a secret in any text: `Authorization: Bearer ...`, a service token (`sk-...`, `AKIA...`, `ghp_...`), `define( 'DB_PASS', '...' )`, `scheme://user:password@host`. |
| `<file>: key material` | A key file by its name: `*.key`, `*.p12`, `*.pfx`, `*.jks`, `*.keystore`, `*.kdbx`, `*.p8`, `*.ppk`, `*.gpg`, `.env`, `id_rsa`, `credentials*`, `*service-account*`, a file below `jwt/` or `.ssh/`. Always refused. |
| `<file>: copy of a settings file` | A backup of a settings file (`*.bak`, `*.orig`, `*~` ...). Always refused: delete the copy. |

Settings files (below `settings/`, or named `*.ini*`) are read as text, commented lines included, so a password that
eZINI would skip is still found.

A `node_modules` directory (the dependencies of a build, such as exp_oe_tiptap's editor) is not packed and not read:
the extension runs from its design files, and a library's test certificates are not the extension's.

## What passes

The check takes these for what they are, under any name:

- nothing, or a placeholder: `...`, `xxx`, `***`, `<your key>`, `Enter your private key here`;
- a switch: `enabled`, `disabled`, `true`, `false`, `none`, `null`, `0` (`ezformtoken=disabled`, `Auth=none`);
- a template placeholder: `%password%`, `{password}`, `${DB_PASSWORD}`, `{{ secret }}`, `%env(DB_PASS)%`, and a value
  put together when it is used (`key: composer-${{ matrix.php }}`);
- a measure or a version under a name that says so (`MaxTokens=4096`, `IEDisableWithPassword[]=11`);
- an address, a header name, a type, mode or method (`TokenURL=https://...` without a password in it,
  `ApiKeyHeader=Authorization`) and a public key (`PublicKey=...`, a captcha site key);
- a certificate or a public certificate bundle (`cacert.pem`): `*.pem`, `*.crt`, `*.der` and `*.asc` are refused only
  for a private key block in them;
- example text: in a comment, a constant or an address of a document or a test, a documentation word stands for the
  value (`# Password=changeme`, `define( 'DB_PASS', 'dbpassword' )`, `https://user:pass@...`, `abc:xyz@`), and so do
  test tokens (`Bearer test-...`, `sk-test-SECRET...`). A comment that explains a setting in words is no value.

An active setting is taken at its word: `Password=secret` and `Password=changeme` are refused, because they may be
the password in use.

## Telling the check about an example: package_check.ini

When the check refuses a value that is only an example (a key-shaped value in a documentation comment, a sample
configuration), the extension's maintainer lists it in a plain file `package_check.ini` at the root of the extension:

```ini
[PackageCheck]
# The example keys of the documentation comment
AllowedFindings[]=settings/recaptcha.ini.append.php:PrivateKey
# Example addresses in the tests
AllowedFindings[]=tests/:(address with a password)
```

- One finding per line, as the refusal names it: `<path below the extension>:<setting name or (kind)>`. A path that
  ends in `/` covers that directory.
- It only leaves out what it names. A path with `..`, a leading `/` or a wildcard is ignored, and so is the file
  when it is a symbolic link or bigger than 64 KB.
- It can never let through key material, a private key block, a copy of a settings file, a link or a file that could
  not be read.
- The file is packed with the extension and read again on the copy in the package.

Before adding an entry, make sure the value really is an example: a real secret listed here leaves the server in
every package.

## Tests

`tests/tests/kernel/classes/setup/ExtensionDownloadTest.php`: the regression table of refused inputs, the table of
inputs that pack, the allow file, and the extensions that ship with the kernel (all must pack).
