# The REST writes through the kernel, nxc_powercontent no longer needed (6.0.15, ezprestapi 1.2.9)

Read this page before you update an installation that uses the REST API to create, update or remove content, that
has `nxc_powercontent` installed, or that writes content from its own PHP code. From Exponential 6.0.15 the kernel
writes content from plain values itself, with [expContentWriter](../../features/6.0/content-writer.md), and
`ezprestapi` 1.2.9 writes through it instead of through `nxc_powercontent`.

## In short

| | Before (kernel up to 6.0.14, ezprestapi up to 1.2.8) | Since (kernel 6.0.15, ezprestapi 1.2.9) |
|---|---|---|
| Who writes REST content | `nxc_powercontent` (`nxcPowerContent`), required by `ezprestapi` | the kernel's `expContentWriter`; `ezprestapi` requires no other extension |
| Update over REST | answered `501` (not implemented) | `POST` or `PUT /api/ezp/v2/content/node/<node id>` publishes a new version; `200`, or `202` when a workflow holds it |
| Request body of a write | form fields | form fields or a JSON object (`Content-Type: application/json`) |
| File value | a path on the server, or any URL (redirects followed) | a public `http(s)` address only, no redirects, at most 50 MB; a server path answers `400` |
| Unknown attribute identifier | ignored | `400 invalid_request` naming the identifiers |
| Value that is not text | written as whatever it became | `400 invalid_request` |
| Create held by a workflow | `201` | `202` with `"message":"Waiting for a workflow"` |
| Create answer | `{"message","objectId","nodeId"}` | the same plus `version` |
| A write that failed | `500` | `500` for an error during the write (rolled back), `400` for input that cannot be written |
| A write on an older kernel | | `501 not_implemented` (no `expContentWriter`); reads work as before |
| An address that names no provider, without `ezprestapi` | fatal error loop, `500` | `404` |
| PHP code that creates content | by hand, `eZContentFunctions`, or an extension | also `expContentWriter::create()`, `update()`, `remove()` |

Unchanged: the routes, the reads at v1 and v2 and their answers, authentication with OAuth2 tokens and personal API
keys, the permission checks of 1.2.5, the delete (it removes every location and what is below them, not to the
trash, as before).

## What to do when you upgrade

1. **Update the kernel to 6.0.15 first**, then `ezprestapi`. With `ezprestapi` 1.2.9 on an older kernel the writes
   answer `501`; reads keep working.

   ```bash
   composer update se7enxweb/exponential se7enxweb/ezprestapi
   ```

   The root `composer.json` of 6.0.15 requires `se7enxweb/ezprestapi ~1.2.9`; a site's own `composer.json` with an
   older constraint (`~1.2.8`) needs it raised first.

2. **Let Composer remove nxc_powercontent, or keep it on purpose.** `ezprestapi` 1.2.9 no longer requires
   `se7enxweb/nxc_powercontent`. When nothing else requires it, the update above removes it, and with it the folder
   `extension/nxc_powercontent`. Find out beforehand:

   ```bash
   composer why se7enxweb/nxc_powercontent
   grep -rn "nxc_powercontent\|nxcPowerContent" settings/ extension/*/classes extension/*/modules extension/*/bin 2>/dev/null | grep -v "^extension/nxc_powercontent/"
   ```

   - `composer why` names only `se7enxweb/ezprestapi`, and the `grep` finds nothing: nothing of yours uses it.
     Let the update remove it.
   - Your own `composer.json` requires it: it stays. Remove it with `composer remove se7enxweb/nxc_powercontent` when
     you no longer need it.
   - Your own code calls `nxcPowerContent`, or a template uses `powercontent_create_gui`: keep it (require it in your
     own `composer.json`) or move the code to `expContentWriter`
     ([Moving code to expContentWriter](#moving-code-to-expcontentwriter)).

   `nxc_powercontent` is not the same extension as `powercontent` (`se7enxweb/powercontent`, the content creation
   tools for templates). The root `composer.json` still requires `powercontent`; nothing on this page changes it.

3. **Take nxc_powercontent out of the settings** if it is gone. Remove `ActiveExtensions[]=nxc_powercontent` (and any
   `ActiveAccessExtensions[]=nxc_powercontent`) from `settings/override/site.ini.append.php` and the siteaccesses.
   While it was active, it replaced the kernel's `content` module with its own copy (`ModuleSettings
   ExtensionRepositories[]=nxc_powercontent`, `ModuleList[]=content`); without it the kernel's own `content` module
   runs; it has every view that copy had.

4. **Regenerate the autoloads, clear the caches and reload PHP.** The autoload generator maps classes of every folder
   below `extension/`, active or not, and `nxc_powercontent` carries copies of kernel classes in its `modules/content`
   folder. After the folder is gone, regenerate so `var/autoload/ezp_override.php` names none of them:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e -o
   ./console exp:velocity deploy --allow-root-user     # INI and template caches, PHP-FPM reload, Velocity restart
   ```

   Without the deploy command: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, reload the PHP-FPM that
   serves the site, and restart Velocity if it runs.

5. **Check your REST clients** against the table above. What a client may have to change:
   - a file value that names a path on the server: give a public `https` address of the file instead;
   - fields that are not attributes of the class: leave them out. Every form field of a create or update besides
     `parentNodeID`, `classIdentifier`, `languageLocale` and `_method` is taken as an attribute value, so a client
     that posts its token as the form field `oauth_token` gets `400`; send the token in the `Authorization` header;
   - a create can answer `202` when a workflow holds the new object: treat it as accepted, not as an error;
   - a client that worked around the missing update (remove and create again, or a write through expservices) can
     now update in place, which keeps the object id, the node id, the URL and the history.

6. **Give update keys the `edit` scope.** A personal API key needs the scope `edit` for an update
   (`rest.ini [ApiKeySettings] RouteScopes[ezp7xRestContentController_UpdateContentNode]=edit`, shipped), and the
   key's user needs `content/edit` in the language written.

7. **Test a write.** With a key or token of a test user:

   ```bash
   curl -s -X POST -H "Authorization: Bearer $EXP_API_KEY" -H "Content-Type: application/json" \
        -d '{"parentNodeID":2,"classIdentifier":"folder","languageLocale":"eng-US","name":"REST test"}' \
        https://www.example.com/api/ezp/v2/content/node/create
   # {"message":"Created","objectId":<object id>,"nodeId":<node id>,"version":1}
   curl -s -X PUT -H "Authorization: Bearer $EXP_API_KEY" -H "Content-Type: application/json" \
        -d '{"name":"REST test, updated"}' https://www.example.com/api/ezp/v2/content/node/<node id>
   # {"message":"Updated","objectId":<object id>,"nodeId":<node id>,"version":2,"languageLocale":"eng-US"}
   curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" \
        https://www.example.com/api/ezp/v2/content/node/delete/<node id>
   # {"message":"Removed","nodeId":<node id>,"objectId":<object id>}
   ```

   A `501` means the kernel is older than 6.0.15; a `403` that the key lacks the scope or its user the policy.

## Moving code to expContentWriter

| nxc_powercontent | expContentWriter |
|---|---|
| `$pc = new nxcPowerContent(); $pc->createObject( array( 'parentNode' => $node, 'class' => $class, 'languageLocale' => 'eng-US', 'attributes' => $values ) )` | `expContentWriter::create( array( 'parentNodeID' => $nodeID, 'classIdentifier' => 'article', 'languageLocale' => 'eng-US', 'attributes' => $values ) )` |
| returns the object or `false` | returns `status`, `message`, `object`, `version` |
| `$pc->updateObject( array( 'object' => $object, 'attributes' => $values ) )`: changes the current version in place | `expContentWriter::update( $object, $values, 'eng-US' )`: publishes a new version, the old one stays in the history |
| `$pc->removeObject( $object )` | `expContentWriter::remove( $object )` (or `remove( $object, true )` for the trash) |
| file values: a server path or any URL | a public `http(s)` address; for files on the server, `eZContentFunctions` with `storage_dir` |
| rich text as HTML | rich text as HTML (the Online Editor's parser; links and embeds become relations) |
| `parentNode` on update moves the object; `additionalParentNodeIDs` sets more locations; `visibility` hides | not part of the writer: `eZContentObjectTreeNodeOperations::move()`, `eZContentOperationCollection::addAssignment()`, `eZContentObjectTreeNode::hideSubTree()` |
| debug output on the command line | the answer's `message`; errors in the debug log, held versions in `var/log/contentwriter.log` |

The guide has complete examples: [Writing content from PHP with expContentWriter](../../guides/writing-content-from-php.md).

## Code and tests

- `kernel/classes/contentwriter/expcontentwriter.php` (kernel commit "Added: expContentWriter creates, updates and
  removes content from plain values", 12 November 2026); DB-less tests in
  `tests/tests/kernel/classes/contentwriter/expContentWriterTest.php` (CW-01 to CW-06).
- `extension/ezprestapi/classes/controllers/content.php` (`doCreateContentNode`, `doUpdateContentNode`,
  `doDeleteContentNode`) and `classes/rest_provider.php` (the route `ezpNodeUpdate`, `POST` and `PUT`), ezprestapi 1.2.9.
- `kernel/private/rest/classes/router.php`: an address without a provider reaches the error route when the default
  provider `ezp` is missing; route matching tests RM-08 and RM-12 in
  `tests/tests/kernel/private/restapi/ezpRestRouteMatchingTest.php`.

## Related pages

- [Content from plain values: expContentWriter](../../features/6.0/content-writer.md)
- [New in 6.0.15: the REST API updates content](../../help/whats-new-in-6-0-15-rest-api.md)
- [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md), [Personal API keys](../../guides/api-keys.md)
- [ezprestapi](../../features/6.0/extensions/ezprestapi.md), [nxc_powercontent](../../features/6.0/extensions/nxc_powercontent.md)
- [Behavior changes of the extensions](extensions-behaviour-changes.md)
- [Upgrading](../../guides/upgrading.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
