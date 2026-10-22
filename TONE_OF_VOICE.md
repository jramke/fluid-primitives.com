# Tone of Voice

How we write anything a library user reads: docs pages, examples, code comments and docblocks that
surface to users, `SKILL.md` or error messages. Read this before writing one.

## Document our part only

Write about what Fluid Primitives does. The frameworks under it have their own docs, so do not explain them
here, and do not teach a reader how TYPO3, Symfony, Fluid or Composer work.

- You can use a framework feature in an example without the need to explain it if its used as normal and expected. `#[AsAlias]` on a class is shown, not
  described. How the container resolves it, what autowiring does or how TYPO3 orders extensions is not our
  topic.
- Do not repeat boilerplate the reader already has from the framework skeleton (a `Services.yaml` with the
  `resource: '../Classes/*'` glob, a stock `composer.json`). Show only what our feature adds.
- If the framework behaviour is a hard requirement of our feature, state the requirement in one sentence and
  link the page where it belongs. No need to explain the mechanism in detail. Example: the sitepackage must `require`
  `jramke/fluid-primitives` in its own `composer.json`, so it loads after the library and its overrides win.

## Stay focused

- One concept per section, the smallest example that shows our API.
- No paragraph that restates the code, defends a design choice or lists what is possible but not asked for.
- Say a requirement once, where the reader needs it, and link to it from other pages.
- A comment states a non-obvious constraint, never what the code does (see Code style in `CLAUDE.md`).

## Conventions

- Examples use the placeholders `MyVendor\MySitepackage` and `my_sitepackage`.
- A list item separates the term from its description with `-`.
- A warning that must not be missed is a `ui:alert`, not a bold sentence. Keep the alert content short.

## Extend this file

When the maintainer corrects a docs paragraph, an example or a comment, write the lesson here in a line or
two, with a concrete example, so the next agent does not repeat it.
