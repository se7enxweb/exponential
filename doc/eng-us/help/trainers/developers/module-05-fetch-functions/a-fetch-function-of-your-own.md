---
title: "Lesson 5.4: A fetch function of your own"
audience: trainer, developer
category: training
order: 54
summary: Learners add a module to their extension, describe a fetch function in function_definition.php, write the function collection class that returns its result, register the module and call the new fetch from a template. 60 minutes.
keywords: developers course, fetch function, function_definition.php, FunctionList, call_method, parameters, function collection, result, module.php, ExtensionRepositories, ModuleList, module.ini, subTreeByNodeID, autoloads
type: help
---
# Lesson 5.4: A fetch function of your own

When a template needs data that no kernel fetch offers, or the same complicated fetch appears in many templates,
write a fetch function of your own. It needs three small files: a module, a description of the function, and a PHP
class that returns the data. In this lesson learners write `fetch( 'hello', 'latest', ... )`, which returns the
newest nodes of a class.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 5: Fetch functions](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Advanced |
| Audience | Developers |
| Prerequisites | [Lesson 5.3](fetches-of-other-modules.md); `mysite_hello` is active (lesson 3.1) |

## Learning objectives

After this lesson, learners can:

- write a minimal `module.php` so a module exists;
- describe a fetch function in `function_definition.php` with `call_method` and `parameters`;
- write a function collection class whose method returns `array( 'result' => ... )`;
- register the module with `ExtensionRepositories[]` and `ModuleList[]` in `module.ini`;
- regenerate the autoloads and call the fetch from a template.

## Trainer's notes

### How to present it

Sources: `kernel/content/function_definition.php` and a real extension, for example
`extension/eztags/modules/tags/function_definition.php`.

1. **Three files** (4 minutes). `modules/hello/module.php` (the module exists), `modules/hello/function_definition.php`
   (what the fetch is called and which parameters it takes), and a class in `classes/` (what it does).
2. **The description** (5 minutes). Walk through the `$FunctionList['latest']` block of the exercise: `name`,
   `operation_types`, `call_method` with `class` and `method`, `parameter_type => 'standard'`, and each parameter
   with `name`, `type`, `required` and `default`. Parameters are passed to the method in the order listed.
3. **The class** (4 minutes). The method returns `array( 'result' => $value )`; the template receives `$value`.
   An error is returned as `array( 'error' => ... )`, as the kernel's collections do.
4. **Registration** (4 minutes). `settings/module.ini.append.php` of the extension:
   `[ModuleSettings] ExtensionRepositories[]=mysite_hello` and `ModuleList[]=hello`. Then the autoloads.
5. **Where to look up nodes in PHP** (3 minutes). `eZContentObjectTreeNode::subTreeByNodeID( $params, $nodeID )`
   takes `ClassFilterType`, `ClassFilterArray`, `SortBy`, `Limit`, `Offset` and `Depth`, like the template fetch.

### Common questions

**Can a fetch function change data?** It should not. Fetches are for reading; changes belong in a module view with
a form and a permission check (module 6).

**Does the fetch check permissions?** `subTreeByNodeID` respects the current user's read rights by default, like
the content fetches.

**Can I use the RAD tools?** Yes: **Setup** > **RAD**, the template extension wizard (`/setup/templateoperator`)
writes template operators and fetch functions. Writing one by hand first makes the generated code readable.

### Pitfalls

- **Forgetting `'result'`.** Returning the array of nodes directly gives the template nothing useful.
- **Module not registered.** Without `ModuleList[]` and `ExtensionRepositories[]` the fetch fails with an error
  about an unknown module in the debug output.
- **Class not found.** Run `php bin/php/ezpgenerateautoloads.php -e` after adding the class.

## Hands-on exercise

On the development installation, in `extension/mysite_hello`.

1. Create `modules/hello/module.php`:

   ```php
   <?php
   $Module = array( 'name' => 'hello' );
   $ViewList = array();
   $FunctionList = array();
   ```

2. Create `modules/hello/function_definition.php`:

   ```php
   <?php
   $FunctionList = array();
   $FunctionList['latest'] = array(
       'name' => 'latest',
       'operation_types' => array( 'read' ),
       'call_method' => array( 'class' => 'mysiteHelloFunctionCollection',
                               'method' => 'fetchLatest' ),
       'parameter_type' => 'standard',
       'parameters' => array( array( 'name' => 'class_identifier',
                                     'type' => 'string',
                                     'required' => true ),
                              array( 'name' => 'limit',
                                     'type' => 'integer',
                                     'required' => false,
                                     'default' => 5 ) ) );
   ```

3. Create `classes/mysitehellofunctioncollection.php`:

   ```php
   <?php
   /**
    * Fetch functions of the hello module: {fetch( 'hello', 'latest', hash( 'class_identifier', 'article' ) )}
    */
   class mysiteHelloFunctionCollection
   {
       /**
        * The newest published nodes of one class, newest first.
        *
        * @param string $classIdentifier
        * @param int $limit
        * @return array array( 'result' => eZContentObjectTreeNode[] )
        */
       public static function fetchLatest( $classIdentifier, $limit )
       {
           $nodes = eZContentObjectTreeNode::subTreeByNodeID(
               array( 'ClassFilterType'  => 'include',
                      'ClassFilterArray' => array( $classIdentifier ),
                      'SortBy'           => array( 'published', false ),
                      'Limit'            => (int)$limit ),
               2 );
           return array( 'result' => $nodes ? $nodes : array() );
       }
   }
   ```

4. Check all three with `php -l`.
5. Register the module:

   ```bash
   php bin/php/console exp:ini add module.ini/ModuleSettings/ExtensionRepositories mysite_hello extension:mysite_hello
   php bin/php/console exp:ini add module.ini/ModuleSettings/ModuleList hello extension:mysite_hello
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini
   ```

6. In the playground, call the fetch:
   `{foreach fetch( 'hello', 'latest', hash( 'class_identifier', 'folder', 'limit', 3 ) ) as $n}{$n.name|wash}; {/foreach}`.
7. Call it with `'note'` and with a class that does not exist; compare.
8. Leave out `class_identifier` and run with `--debug=warning,error`; read the message about the required parameter.
9. Commit the four files.

## Expected result

- Step 6 prints up to three folder names, newest first.
- Step 7: the notes of lesson 4.1, and nothing for the unknown class.
- Step 8: the debug output shows the error "Missing parameter 'class_identifier' for function 'latest' in module
  'hello'".
- The trainer checks one learner's `module.ini.append.php` and the playground output.

## Quiz

1. Which three files make a fetch function?
2. What must the method of a function collection return?
   A. The value itself B. `array( 'result' => $value )` C. `true` D. A template
3. In which order are parameters passed to the method?
4. Which two settings register an extension module?
5. Why should a fetch function not change data?

<details>
<summary>Answers</summary>

1. `modules/<module>/module.php`, `modules/<module>/function_definition.php`, and the class in `classes/`.
2. **B.** `array( 'result' => $value )`.
3. In the order the parameters are listed in `function_definition.php`.
4. `module.ini [ModuleSettings] ExtensionRepositories[]` and `ModuleList[]`.
5. Fetches run while pages are drawn, often from caches and without forms or permission checks for changes;
   changes belong in module views.

</details>

## Further reading

- [RAD tools](../../../../features/6.0/rad-tools.md): the template extension wizard.
- [Extension points](../../../../bc/6.0/rad-extension-points.md): fetch functions among the template extension points.
- [Extension development: the book](../../../extension-development/README.md): Part 3 covers fetch functions in depth.

## Related pages

- [Module 5: Fetch functions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
