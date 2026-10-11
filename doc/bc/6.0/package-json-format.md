# The JSON package format

Exponential packages (`.ezpkg`, Setup > Packages, `ezpm.php`) can be written in a second serialization: JSON. It is
the same package model as the classic XML format, element for element, so a package converts from XML to JSON and
back without loss, a JSON package installs through the same package handlers, and every datatype's own
serialization is used unchanged. The classic format stays the default; nothing about a classic package, its archive
or its installation changed.

Use the JSON format when a package is read or written by tools that handle JSON better than XML (scripts, AI agents,
review tools, diffs in a pull request), and the classic format everywhere else.

## In short

| | Classic | JSON |
|---|---|---|
| Definition | `package.xml` | `package.json` |
| A document of the package | `ezcontentobject/contentobjects.xml`, `ezcontentclass/class-article.xml`, `ezcontentobject/object-<remote id>.xml` ... | the same name with `.json` |
| Every other file (images, binary files, templates, settings, extension files) | as it is | as it is, byte for byte |
| Checksums | MD5 of design files only | SHA-256 of every file, in `package.json` |
| Archive | `<name>-<version>-<release>.ezpkg` (gzip compressed tar) | `<name>-<version>-<release>.json.ezpkg` (gzip compressed tar) |
| Detection | by content: `package.xml` or `package.json` at the top of the archive or directory | |
| Schema | the kernel's reader | [`share/package/schema/package-v1.json`](../../../share/package/schema/package-v1.json) (JSON Schema 2020-12) |

## Why this design

- **1:1 with the XML, not a new content model.** Every XML document of a package is mirrored in
  [JsonML](http://www.jsonml.org/), the established lossless mapping of XML to JSON arrays. Because the JSON *is*
  the XML, no package handler, datatype or class serializer needed a change: the kernel turns a JSON document back
  into the same DOM the XML file gives (`eZPackage::fetchDOMFromFile()`), and the handlers run as they always did.
  A content-specific JSON model would have meant a second serializer per datatype, kept in step with the first for
  ever.
- **Rich text stays rich text.** The value of an `ezxmltext` attribute is kept as its XML string (`["#xml", "..."]`),
  so it reads as the editor's XML and does not explode into nested arrays. The list of datatypes treated that way is
  `[PackageFormatSettings] RawXMLDatatypes[]` in `package.ini`.
- **The same archive.** A JSON package is an `.ezpkg` (gzip compressed tar) like any other: the upload page, the
  upload checks (`eZPackageUploadInspector`: safe paths, sizes, entry types), `ezpm import`, the package server and
  the setup wizard's upload take it without a second code path. The format is read from the content (which
  definition file is at the top), never from the file name; the `.json.ezpkg` name only helps people tell the two
  downloads apart.
- **Files by hash.** Binary files stay where the classic layout puts them (`simplefiles/<key>.<ext>`,
  `files/...`), named as the package handlers name them; `package.json` adds the SHA-256 of every file, so a changed
  or missing file is found before anything is installed.

## A document

Every JSON document is an envelope around the root element of the XML document it mirrors:

```json
{
    "$schema": "https://raw.githubusercontent.com/se7enxweb/exponential/main/share/package/schema/package-v1.json",
    "format": "exponential-package",
    "format-version": "1.0",
    "kind": "document",
    "source": "ezcontentobject/object-media-o-1200.xml",
    "root": ["ezremote:object",{"xmlns:ezremote":"http://ez.no/ezobject","xmlns:ezobject":"http://ez.no/object/","name":"Home Hero","remote_id":"media-o-1200","class_remote_id":"b86c970a6ad8e3d268581ec10991a958","ezremote:class_identifier":"ng_component_hero","initial_language":"eng-US"},
        ["ezobject:version-list",{"active_version":"16"},
            ["ezobject:version",{"xmlns:ezremote":"http://ez.no/ezobject","ezremote:version":"16","ezremote:status":"1"},
                ["ezobject:object-translation",{"language":"eng-US","object_name":"Home Hero"},
                    ["ezobject:attribute",{"ezremote:identifier":"title","name":"Title","type":"ezstring"},
                        ["text","Scale your fitness business with us"]
                    ],
                    ["ezobject:attribute",{"ezremote:identifier":"description","name":"Description","type":"ezxmltext"},
                        ["#xml","<section><paragraph>We help gym owners ...</paragraph></section>"]
                    ],
                    ["ezobject:attribute",{"ezremote:identifier":"image","name":"Image","type":"ezimage","image-file-key":"2f4d2aa580e85579751310ac32afc509","alternative-text":""}]
                ]
            ]
        ]
    ]
}
```

| Member | Meaning |
|---|---|
| `$schema` | the JSON Schema of the format (optional) |
| `format` | always `exponential-package` |
| `format-version` | `1.x`; a reader of 1.x reads every 1.x document |
| `kind` | `definition` for `package.json`, `document` for every other document |
| `source` | the XML document it mirrors, relative to the package directory (optional; checked by `validate`) |
| `root` | the root element in JsonML |
| `checksums` | `package.json` only: path => SHA-256 of every other file of the package |

### JsonML

| XML | JSON |
|---|---|
| `<name attr="v">children</name>` | `["name", {"attr": "v"}, child, ...]`; the attribute object is left out when there are no attributes |
| namespace declaration `xmlns:p="uri"` | an attribute `"xmlns:p": "uri"`, before the other attributes, on the element that declares it |
| text | a string |
| `<!-- c -->` | `["#comment", " c "]` |
| `<![CDATA[x]]>` | `["#cdata", "x"]` |
| `<?target data?>` | `["?target", "data"]` |
| rich text (the value of an attribute of a datatype in `RawXMLDatatypes[]`) | `["#xml", "<section ...>...</section>"]` |

Text that is only white space with a line break is formatting, as the kernel's XML reader already treats it, and is
not written; any other text, a single space included, is kept. A document is refused when it is not this format,
when a name is not an XML name, when a value is not a string, when rich text has a DOCTYPE, an entity declaration or
an XML declaration, or when the XML it gives is not well formed. Documents larger than
`[PackageFormatSettings] MaxDocumentBytes` (64 MB) or nested deeper than 512 levels are refused.

### Which files are documents

`package.json`, the file of every install and uninstall item (`<sub-directory>/<filename>.json`), and the object
files a content object item names (`<object-file filename="object-x.xml"/>`, in `ezcontentobject/`). References
inside documents keep their classic names (`filename="object-x.xml"`): the reader resolves `x.xml` to `x.json` when
there is no XML file. Every other file is payload and is copied as it is, also when it is XML or JSON (an extension's
`extension.xml`, a site package's own `cjw-paths.json`).

### Package metadata

A package can carry free key => value strings in its definition, `<metadata><entry key="...">value</entry></metadata>`
(`"metadata"` in JsonML), for tools that need to keep information with a package; keys are 1-100 characters of
`A-Z a-z 0-9 . _ : -` and namespaced by the tool (`expai.ai-generated`). `eZPackage::setMetadata()`,
`eZPackage::metadata()`, `$package.metadata` in templates; Setup > Packages shows them on the package's page. A
package without metadata is written exactly as before.

## Command line

```bash
# export in the JSON format (as <name>-<version>-<release>.json.ezpkg); --format=xml for the classic one
php ezpm.php export mypackage -d /path/to/dir --format=json

# convert an archive or a package directory; --to defaults to the other format
php ezpm.php convert mypackage-1.0-1.ezpkg mypackage-1.0-1.json.ezpkg --to=json
php ezpm.php convert mypackage-1.0-1.json.ezpkg /path/to/new-directory --to=xml

# check a package (archive, directory or repository package) without importing it:
# the schema, every document as XML, the definition, the documents the items name, the checksums
php ezpm.php validate mypackage-1.0-1.json.ezpkg

# import and install: the format is detected
php ezpm.php import mypackage-1.0-1.json.ezpkg
php ezpm.php install mypackage

# what an install would meet here, by remote id (nothing is written); --all lists every object
php ezpm.php check mypackage

# install the content objects as drafts
php ezpm.php install mypackage --as-drafts [--drafts-existing=update|skip]
```

`./console ezpm ...` runs the same commands. Exit code 1 on any failure; `validate` prints `PASS`/`FAIL`.

## Setup > Packages

- **Download / JSON** on every package card and **Download / Download as JSON** on the package's page:
  `package/export/<name>[/<repository>]/(format)/xml` or `/(format)/json`. Without `(format)` the package is
  exported in the format it is stored in. The page shows the package's format and its metadata.
- **Upload** (`package/upload`) takes either format.
- **Install** (`package/install/<name>`) shows what the install meets here (below) and offers **Install as drafts**.

## What the install meets here

`expPackageConflictReport::build( $package )` (and `ezpm check`, and the install page) reads every content object
and class of the package and looks each up by remote id, without writing anything:

| Object action | When |
|---|---|
| `create` | no object has its remote id |
| `update` | an object of the same class has its remote id: an install updates it, an install as drafts adds a draft |
| `conflict` | the remote id is an object of another class, or a location's node remote id is a node of another object |
| `error` | its class is neither installed nor in the package |

Every location says where it would go: `top` (the top node chosen at install), `installed` (the parent node is in the
tree), `in_package` (the parent is another object of the package) or `missing`. Every class says whether a class with
its remote id or identifier exists.

## Install as drafts

A generic install option of the kernel (`$installParameters['as_drafts'] = true`; `ezpm install --as-drafts`; the
checkbox on the install page) for content that a person reviews before it goes live, such as content written by AI
or delivered by another team:

- **Nothing is published** and no published object, node, name, section or always-available flag is changed.
- A **new object** becomes an object with one draft: the package's active version (else its last one), in every
  language of that version.
- An **existing object** (same remote id) gets a new draft version with the package's values;
  `drafts_existing=skip` (`--drafts-existing=skip`, "is left alone") leaves it alone. An existing object of **another
  class** is a conflict and is always left alone (`conflict`).
- Only **content object** items are installed. Classes, settings, design files, templates, overrides, fetch aliases,
  extensions and install scripts would change the site at once; they are listed as not installed
  (`$installParameters['skipped-items']`). Install the package normally, or the class package first, for those.
- A draft whose parent is another new object of the package **waits** for it (`$installParameters['drafts-waiting']`):
  a draft parent has no node yet. Publish the parent (its node gets the package's node remote id), then install the
  package as drafts again: the parents are found and the children get their place.
- The package is **not marked installed**: installing as drafts can be repeated.
- `$installParameters['drafts']` lists every draft (remote id, object id, version, name, `new_draft`,
  `draft_of_existing`, `skipped` or `conflict`); the install page links each to `content/versionview`, where it is reviewed and
  sent for publishing (approval workflows apply).

## PHP API

| Call | Does |
|---|---|
| `$package->exportToArchive( $path, 'tar.gz', 'json' )` | export in the JSON format (`'xml'` classic, `false` as stored) |
| `$package->exportName( 'json' )` | `<name>-<version>-<release>.json.ezpkg` |
| `$package->attribute( 'format' )` | `xml` or `json`: the format the package is stored in |
| `$package->setMetadata( $key, $value )`, `$package->metadata( $key )`, `$package->attribute( 'metadata' )` | package metadata |
| `eZPackage::import( $archive, $name )` | either format, detected by content |
| `eZPackage::fetchDOMFromFile( $path )` | a document by its XML name, from the XML or the JSON file |
| `eZPackage::definitionFilePath( $dir )` | `package.xml`, else `package.json` |
| `eZPackage::archiveDirectory( $dir, $archive )` | a directory as an `.ezpkg` |
| `expPackageJson::convert( $source, $target, $to )` | convert an archive or directory into an archive or directory |
| `expPackageJson::convertDirectory( $source, $target, $to )` | convert a package directory |
| `expPackageJson::validateDirectory( $dir )` | the checks of `ezpm validate` |
| `expPackageJson::extractArchive( $archive, $dir )` | extract with the upload checks |
| `expPackageJson::canonical( $dom )` | the canonical form two documents are compared by |
| `expPackageConflictReport::build( $package )` | what an install meets here |
| `expJsonSchemaValidator::validate( $data, $schema )` | JSON Schema check (the subset the kernel's schemas use) |

## Settings

`package.ini`:

```ini
[PackageFormatSettings]
# datatypes whose attribute value is kept as an XML string in the JSON format (["#xml", "..."])
RawXMLDatatypes[]
RawXMLDatatypes[]=ezxmltext
# the largest JSON document read, in bytes
MaxDocumentBytes=67108864
```

## Round trip, verified

`XML -> JSON -> XML` and `JSON -> XML -> JSON` on every package of `var/storage/packages/7x` (5 packages, 540
documents, 390 of them with rich text) and on a test subtree exported with `ezpm` (a folder in eng-US and ger-DE
with rich text links, an embed and keywords, an article with a relation, an image, a file, a nested article): every
document canonically equal (C14N of the DOM the kernel reads, formatting white space aside), every other file byte
for byte, the JSON a fixed point (written again byte for byte). The test subtree's JSON package installed with every
attribute value as exported, in both languages; installed as drafts it published nothing. Tests:
`tests/tests/kernel/classes/packages/expPackageJsonTest.php`.

Byte for byte the XML is not always the same as the original file: the original's indentation, quote style or
`<text></text>` for an empty element differ between the tools that wrote it; the kernel reads both the same.
