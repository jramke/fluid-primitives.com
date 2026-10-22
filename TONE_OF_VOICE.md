# Tone of Voice

How we write anything a library user reads: docs pages, examples, code comments and docblocks that
surface to users, `SKILL.md` and error messages. Read this before writing one.

Write like a colleague who knows the library well and wants you to get something working in the next
ten minutes. Friendly, direct, specific. Not a manual, not a sales page.

## Voice

- **Say "you".** "You can disable a single item", never "users can" or "one can".
- **Active voice, imperative for steps.** "Add `ui:attributes()` to forward extra attributes", not
  "`ui:attributes()` should be added in order to enable forwarding".
- **Get to the point.** Cut the lead-in ("Note that", "It is important to", "In order to") and the
  wrap-up that repeats what the reader just read.
- **Be specific.** Name the attribute, the prop, the key. A concrete value beats an adjective:
  "closes after 300 ms", not "closes quickly".
- **Stay human, sparingly.** A dry aside is fine. Exclamation marks, hype and forced jokes are not.
- **Pronouns for us.** Pages rarely need one. Where a recommendation reads better with it, "we
  recommend" is fine. `the-pitch.md` is the only personal page (first person, signed).
- **Hyphen, not em dash.** Where a dash is needed use `-`. Often two sentences read better.

## Start with the task

A page and every section in it starts with what the reader wants to get done, not with a concept.

- Headings name the task or the result, short: "Allow several open panels", not "Multiple" and not
  "Advanced Configuration Options". A component's own name stays as it is.
- Headings use sentence case ("Validate with a schema"). Proper nouns and code keep their own case.
  `Features`, `Anatomy`, `Installation`, `Examples` and `API Reference` stay as they are.
- Show first, explain second. A demo or a snippet comes before the paragraph about it.
- Headings are anchors. When you rename one, search for its slug (`#content-...`) and update the links.

## Show behaviour in demos, not in prose

A working demo explains behaviour better than any paragraph. Use `ui:componentExample`.

- A section is a heading, one or two sentences on what the demo shows, and the demo. The sentences say
  what the reader sees or can try, not how the code works.
- One scenario per demo, the smallest one that shows the feature. If a section needs more than a
  handful of sentences, it mixes scenarios: split it, or cut what is not asked for.
- Do not repeat the demo's source in a code block. The demo already has a code tab. A snippet is for the
  one line of API the section is about.
- Do not write a paragraph that retells what the code does.

## Describe what is

The library is approaching v1. What it did before is not relevant to a reader, release notes belong in
the changelog.

- **Never mention a change.** No "now", "no longer", "used to", "previously", "replaces", "was renamed",
  "Changed:", no "Upgrading" alert. Describe the current behaviour as if it had always been so.
- **Say what it does, not what it does not do.** No "there is no X", "this is not Y", "does not
  generate an id". Write the positive: "Zag.js adds the ids the ARIA links need when the component
  hydrates."
- **A limit the reader will run into is the exception.** State it once, where it matters, in the
  positive form when possible, and name the alternative: "File uploads go through `extbase.post`."
- **Keep other libraries out of it.** No comparisons, no "like in X". The acknowledgments on the
  introduction page are the place to credit them.

|       | Example                                                                                                  |
| ----- | -------------------------------------------------------------------------------------------------------- |
| Don't | The global context replaces `<ui:hydrationData>`, which is gone. A context class must no longer declare… |
| Do    | Expose constants for your entry files with a getter on the global context.                               |

## Recommend, do not dictate

A convention we ship is a recommendation. Reserve "must" for a hard requirement, and give the
consequence.

- Write "Recommended: keep primitives in `ui/` and your compositions next to it", not "The correct
  structure is" or "Always do this".
- Avoid "always", "never", "the only way", "the right way" for anything that is a choice. A reader with a
  different setup should not feel they are doing it wrong.
- A requirement states what breaks otherwise: "The sitepackage must require `jramke/fluid-primitives`,
  otherwise its overrides load before the library and lose."
- Give a reason only for a requirement. A recommendation that needs a long defence is probably not worth
  making.

## Use real-world scenarios

Examples, demo text and snippets come from projects a reader might actually build.

- Pick everyday TYPO3 scenarios: newsletter signup, contact form with an attachment, event registration,
  FAQ accordion, opening hours in tabs, cookie settings dialog, product filter, shipping country select,
  quantity stepper in a shop, price range slider, delivery date picker, copy-link button, language
  switcher, mega menu.
- Labels, values and messages read like real content ("Subscribe to the newsletter", "Berlin"), not
  `Foo`, `Item 1`, `Example 1` or lorem ipsum.
- Nothing from this repo leaks into an example: no docs namespace or paths (`EXT:docs`), no names of our
  own demo components or helpers (`GuestList`, `FormExample`), no logic that only exists to make the docs
  site work.
- Code uses one invented extension everywhere: `MyVendor\MyExtension`, extension key `my_extension`,
  `EXT:my_extension/Resources/Private/...`, `LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:key`.
  Not `my_ext`, `site_package`, `MySitepackage` or the like.
- A snippet that is not about the extension stays free of it. Show a template without a namespace
  wrapper, a class without its `use` block, unless the import is what the section teaches.

## Stay in our stack

We write for TYPO3, Fluid templates, PHP and plain TypeScript on top of Zag.js. Not for React, Vue or
any other frontend framework.

- Use the words of the stack: template, ViewHelper, component, slot, DOM, event, data attribute,
  `addEventListener`. Not hooks, JSX, state, re-render, controlled/uncontrolled or "prop drilling".
- Explain a concept through Fluid or TYPO3, not through an analogy to another framework.
- Do not send the reader to another framework's docs to learn how our part works.
- TypeScript examples are plain DOM code that works in any bundler setup.

## Document our part only

Write about what Fluid Primitives does. The frameworks under it have their own docs, so do not explain
them here, and do not teach a reader how TYPO3, Symfony, Fluid or Composer work.

- Use a framework feature in an example without explaining it when it is used as expected. `#[AsAlias]`
  on a class is shown, not described. How the container resolves it is not our topic.
- Do not repeat boilerplate the reader already has from the framework skeleton (a `Services.yaml` with the
  `resource: '../Classes/*'` glob, a stock `composer.json`). Show only what our feature adds.
- If a framework behaviour is a hard requirement of our feature, state the requirement in one sentence and
  link the page where it belongs. Example: the sitepackage must `require` `jramke/fluid-primitives` in
  its own `composer.json`, so it loads after the library and its overrides win.
- Write for someone who knows their way around TYPO3 and Fluid and is new to this library.

## Stay focused

- One concept per section, the smallest example that shows our API.
- No paragraph that restates the code, defends a design choice or lists what is possible but not asked for.
- Say a requirement once, where the reader needs it, and link to it from other pages.
- Do not open a page or section by restating its title.
- A comment states a non-obvious constraint, never what the code does (see Code style in `CLAUDE.md`).
- Prevent the common mistake where it happens: one sentence next to the step, naming what breaks.

## Words

- Use: build, set up, add, use, pass, works with, "let's" in a guided tutorial.
- Say what it does instead of: leverage, utilize, facilitate, robust, seamless, powerful, cutting-edge,
  "out of the box", "under the hood", "magic".
- Drop: just, simply, easily, obviously, of course. If a step were that simple, the reader would not be
  reading about it.
- "Simple" is fine when it is true.

## Conventions

- Link text names the target ("the [global context](...)"), never "click here" or "here".
- A list item separates the term from its description with `-`.
- A warning that must not be missed is a `ui:alert`, not a bold sentence. An alert holds one or two
  short sentences (about 150 characters) on one concern, and is for what breaks, not for what is merely
  good to know. Anything longer is a paragraph or a short section.
- A component page keeps its shape: title, one-line summary in bold, demo, `Features`, `Anatomy`,
  `Installation`, `Examples`, `API Reference`. Keep `Anatomy` and `API Reference` intact, `SKILL.md` and
  the Markdown endpoints rely on them.
- An error message or exception says what is wrong, with the offending value, and what to do about it:
  `Unknown part "foo" for accordion. Available parts: root, item, itemTrigger.`

## Before you hand in

Search the page for words that usually signal a violation, and rewrite what you find: `now`,
`no longer`, `used to`, `instead of`, `not`, `never`, `always`, `must`, `only`, `just`, `simply`,
`my_ext`, `site_package`, `EXT:docs`, `React`.

## Extend this file

When the maintainer corrects a docs paragraph, an example or a comment, write the lesson here in a line or
two, with a concrete example, so the next agent does not repeat it.
