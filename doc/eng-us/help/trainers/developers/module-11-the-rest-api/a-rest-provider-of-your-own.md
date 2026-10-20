---
title: "Lesson 11.3: A REST provider of your own"
audience: trainer, developer
category: training
order: 113
summary: Learners read how a REST provider declares versioned routes, a view controller and a controller with do actions, write a small provider that returns the stored greetings as JSON under its own prefix, register it in rest.ini, and compare it with what the RAD REST provider wizard writes. 60 minutes.
keywords: developers course, REST provider, ezpRestProviderInterface, getRoutes, getViewController, ezpRestVersionedRoute, ezpMvcRailsRoute, ezpRestMvcController, ezpRestMvcResult, ezpRestJsonView, rest.ini ProviderClass, prefix, RAD REST provider wizard
type: help
---
# Lesson 11.3: A REST provider of your own

When an app needs data that the content routes do not offer, an extension adds a **REST provider**: its own prefix,
its own routes and controllers, with the kernel's authentication, versions and output. In this lesson learners read
how the `ezprestapi` provider is built, write a small provider that returns the greetings of lesson 9.3, and compare
their code with the RAD wizard's.

> **Note**
> This is the most advanced lesson of the course. Rehearse it on your own installation before teaching it; keep the
> finished files to hand out.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 11: The REST API](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Advanced |
| Audience | Developers |
| Prerequisites | [Lesson 11.2](calling-the-api-with-a-key.md) and [Lesson 9.3](../module-09-the-console-and-persistent-objects/persistent-objects.md) |

## Learning objectives

After this lesson, learners can:

- describe the three parts of a provider: routes, a view controller and controllers;
- declare a versioned route with `ezpRestVersionedRoute` and `ezpMvcRailsRoute`;
- write a controller action `do<Action>()` that returns an `ezpRestMvcResult`;
- register a provider for a prefix with `rest.ini [ApiProvider] ProviderClass[<prefix>]`;
- check permissions inside a controller and find the RAD **REST provider** wizard.

## Trainer's notes

### How to present it

Sources: `kernel/private/rest/classes/interfaces/rest_provider.php`, `extension/ezprestapi/classes/rest_provider.php`,
`extension/ezprestapi/classes/view_controller.php`, `extension/ezprestapi/classes/controllers/content.php`, and
[REST API v1 and v2, section 5](../../../../guides/rest-api-v1-and-v2.md#5-configuration).

1. **The interface** (4 minutes). `ezpRestProviderInterface` has two methods: `getRoutes()` returns named,
   versioned routes; `getViewController()` returns the object that chooses a view (JSON, Atom ...) for a result.
2. **A route** (4 minutes). `new ezpRestVersionedRoute( new ezpMvcRailsRoute( '/content/node/:nodeId',
   'ezp7xRestContentController', array( 'http-get' => 'viewContent' ) ), <versions> )`: a path with placeholders,
   the controller class, an action per HTTP method, the versions it answers at.
3. **A controller** (4 minutes). A class extending `ezpRestMvcController`; the action `viewContent` is the method
   `doViewContent()`, which builds an `ezpRestMvcResult` and fills `$result->variables`. Placeholders such as
   `:nodeId` are available as properties.
4. **Registration** (3 minutes). `ProviderClass[mysite]=mysiteRestProvider` in `rest.ini` makes
   `/api/mysite/v1/...` answer through your provider, with the kernel's authentication.
5. **Permissions** (3 minutes). The framework authenticates; deciding what the user may see is the controller's job.
   The content controller refuses with 403 when a policy is missing; do the same with `eZUser::currentUser()` and
   `hasAccessTo()`.
6. **The wizard** (2 minutes). **Setup** > **RAD** lists **REST provider** (`/setup/handlerextension/restprovider`).

### Common questions

**Why my own prefix and not a route in `ezp`?** The `ezp` routes belong to `ezprestapi`; a prefix of your own keeps
your API independent of its releases.

**How are answers cached?** The framework has an answer cache with settings per controller action; see
`settings/rest.ini`.

**Can I return something else than JSON?** The view controller decides; `ezprestapi` returns Atom for its feed
controller and JSON otherwise.

### Pitfalls

- **No permission check.** Authentication is not authorization. Check the user's policies in every action.
- **Personal data in answers.** Return only what the client needs; never e-mail addresses of other users.
- **Class not found.** Providers and controllers are classes: run `ezpgenerateautoloads.php -e`.

## Hands-on exercise

On your training installation, in `extension/mysite_hello`, with the class `mysiteGreeting` of lesson 9.3 and an API
key from lesson 11.2 (make a new one if you revoked it).

1. Read the route table and the view controller of `ezprestapi`:
   `sed -n 1,60p extension/ezprestapi/classes/rest_provider.php` and `cat extension/ezprestapi/classes/view_controller.php`.
2. Create `classes/rest/mysiterestprovider.php`:

   ```php
   <?php
   /**
    * REST provider of mysite_hello: GET /api/mysite/v1/greetings
    */
   class mysiteRestProvider implements ezpRestProviderInterface
   {
       public function getRoutes()
       {
           return array(
               'mysiteGreetings' => new ezpRestVersionedRoute(
                   new ezpMvcRailsRoute( '/greetings', 'mysiteRestController', array( 'http-get' => 'list' ) ),
                   1
               ),
           );
       }

       public function getViewController()
       {
           return new mysiteRestViewController();
       }
   }
   ```

3. Create `classes/rest/mysiterestviewcontroller.php`:

   ```php
   <?php
   class mysiteRestViewController implements ezpRestViewControllerInterface
   {
       public function loadView( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result )
       {
           return new ezpRestJsonView( $request, $result );
       }
   }
   ```

4. Create `classes/rest/mysiterestcontroller.php`:

   ```php
   <?php
   class mysiteRestController extends ezpRestMvcController
   {
       public function doList()
       {
           $result = new ezpRestMvcResult();
           $access = eZUser::currentUser()->hasAccessTo( 'hello', 'greet' );
           if ( $access['accessWord'] === 'no' )
           {
               $result->status = new ezpRestHttpResponse( 403, 'Forbidden' );
               return $result;
           }
           $greetings = array();
           foreach ( mysiteGreeting::fetchByName( 'Ada', 20 ) as $greeting )
           {
               $greetings[] = array( 'id' => (int)$greeting->attribute( 'id' ),
                                     'created' => (int)$greeting->attribute( 'created' ) );
           }
           $result->variables['greetings'] = $greetings;
           return $result;
       }
   }
   ```

5. Check the three files with `php -l`, then `php bin/php/ezpgenerateautoloads.php -e`.
6. Register the provider:
   `php bin/php/console exp:ini set "rest.ini/ApiProvider/ProviderClass[mysite]" mysiteRestProvider extension:mysite_hello`,
   and clear the `ini` cache.
7. Call it: `curl -s -H "Authorization: Bearer $EXP_API_KEY" http://127.0.0.1:8087/api/mysite/v1/greetings`.
8. Call it without the key and read the status.
9. Open **Setup** > **RAD**, choose **REST provider**, fill in a test name, press the preview, and compare the files it
   would write with yours (do not write them).
10. Commit the three classes and the settings.

## Expected result

- Step 7: JSON with a `greetings` list of the stored greetings named Ada (the user needs the `hello/greet` policy of
  lesson 6.3, otherwise 403).
- Step 8: `401`.
- Step 9: the wizard lists a provider class, a controller and the `rest.ini` registration, like the exercise.
- The trainer calls one learner's route with the learner's key on the projector.

## Quiz

1. Which two methods does `ezpRestProviderInterface` require?
2. Which method of the controller answers the action `list`?
   A. `list()` B. `doList()` C. `actionList()` D. `getList()`
3. Which setting registers a provider for the prefix `mysite`?
4. Who decides what the user may see: the framework or the controller?
5. Why does an extension use its own prefix instead of adding routes to `ezp`?

<details>
<summary>Answers</summary>

1. `getRoutes()` and `getViewController()`.
2. **B.** `doList()`.
3. `rest.ini [ApiProvider] ProviderClass[mysite]=mysiteRestProvider`.
4. The controller: the framework authenticates, the controller checks the policies.
5. The `ezp` routes belong to `ezprestapi`; an own prefix keeps the API independent of that extension's releases.

</details>

## Further reading

- [REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md).
- [RAD tools](../../../../features/6.0/rad-tools.md) and [Extension points](../../../../bc/6.0/rad-extension-points.md).
- [API and protocol standards](../../../../standards/apis-and-protocols.md).

## Related pages

- [Module 11: The REST API](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
