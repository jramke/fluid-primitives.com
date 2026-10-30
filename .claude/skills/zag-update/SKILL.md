---
name: zag-update
description: 'Update the Zag.js packages of Fluid Primitives to a new release and work out what it means for the library: changes that need a change in our code, new things worth using, and fixes. Use when Zag.js has a new release (a stable version or a new prerelease) to bump to, or when asked to check what changed in Zag.'
---

# Zag update

The library wraps Zag.js machines (`@zag-js/*`, pinned in `packages/fluid-primitives/package.json` and, for `docs`
and `hotkeys`, in the root `package.json`). A new Zag release can rename a part, change a default or fix a bug our
wrappers, Fluid templates, registry styles, docs and tests depend on. This skill bumps to the release, finds out what
changed, sorts it by what it means for us, and implements what needs doing, one branch per change.

Reports and commits are persisted text: write them in plain language, not in caveman style.

## 1. Bump

- **from** is the version the library pins now. **to** is the release to move to (`npm view @zag-js/core dist-tags`;
  the prerelease line is `next`). Both have a tag `@zag-js/core@<version>` in `chakra-ui/zag`.
- Bump the library dependencies with `ddev npm run primitives:bump-zag <to>`, the two root dependencies
  (`@zag-js/docs`, `@zag-js/hotkeys`) by hand, then `ddev npm install`. All Zag packages must resolve to one copy:
  `ddev exec 'npm ls @zag-js/core'` shows no `invalid` and `packages/fluid-primitives/node_modules/@zag-js` does not
  exist (two copies mean two layer stacks, and nested popups close their parent).
- Regenerate the committed Zag metadata of the docs tables with `ddev exec "vendor/bin/typo3 docs:generate-zag-docs"`,
  in a commit of its own: it touches a file per machine.
- Run `ddev npm run primitives:build`, `ddev npm run types`, `ddev exec "cd packages/fluid-primitives && npm run
types"` and `ddev npm run primitives:test`. Every failure belongs to the report under "Needs a change". The PHP tests
  (`ddev composer test`) matter once a change touched PHP or a template.

## 2. Collect what changed

```bash
git -C ~/personal/dev/zag fetch upstream --tags    # origin of that clone is the user's fork
python3 .claude/skills/zag-update/collect-changes.py --repo ~/personal/dev/zag \
    --from @zag-js/core@<from> --to @zag-js/core@<to> \
    --lib packages/fluid-primitives/package.json --out <scratchpad>/collect
```

It writes `packages.txt` (machines added or removed), `used.txt` (CHANGELOG lines of the packages we depend on, directly
or through other Zag packages), `other.txt` (everything else) and `commits.txt` (the commits worth reading). Read
`used.txt` and `commits.txt` in full and skim `other.txt` for new things. What each is good for:

- **CHANGELOGs** carry the intent and, for a breaking change, the migration. A release of the prerelease line also
  carries the stable releases that were merged into it.
- **Commits** catch what a CHANGELOG words badly. A `!` is a breaking change. Read the diff of anything ambiguous:
  `git -C ~/personal/dev/zag show <sha> -- packages/machines/<name>` (`*.connect.ts`, `*.types.ts`, `*.machine.ts`).
- **`packages.txt`** names new machines. Compare them with what the library already wraps.

## 3. Sort every change

Decide per change against our code, not against Zag's wording. Search with `rg` (the shell's `grep` is a function and
`ls` an alias here, and zsh does not split an unquoted `$VARIABLE` of paths). Where our wrappers live:

- `Primitives/<Name>/<Name>.ts` (hydration, `spreadProps`, `render()`), `*.fluid.html` (parts and `ui:prop`s),
  `*.entry.ts`, and `Primitives/<Name>/src/*.machine.ts` (self-made machines and vendored copies of Zag machines such
  as Field and Fieldset: compare these against the upstream machine by hand, npm will not warn),
- `Classes/Contexts/`, `Classes/Enum/`, `Resources/Private/Language/*.xlf` (translation ids follow Zag's keys),
- `Resources/Private/Registry/` and `packages/docs/Resources/Private/Components/ui/` (styled wrappers, whose Tailwind
  classes select Zag's `data-*` attributes),
- `packages/docs/Resources/Private/Content/docs/` and the `tests/`.

The categories, in the order of the report:

1. **Needs a change in our code** (the focus). A renamed or removed part, prop, API method, translation key, data
   attribute or CSS variable we use or document; a changed default; a behaviour our template or style relied on; a new
   part we must render to stay accessible; anything that makes types or tests fail after the bump. Name the files.
2. **New things** (machines, utilities, parts, props, translations, `data-*` attributes): what each does and where we
   could use it (a new primitive for `add-zag-primitive`, a prop on a primitive we have, a registry style, an example).
3. **Fixes and behaviour changes in what we use** that need no action: one line each, grouped by component. Say when a
   fix removes a workaround of ours.
4. **Not relevant**: one line naming the packages that changed and that we do not use.

Be exact about what the library does today: a wrong "we already handle this" costs more than a longer report. When
the library is unaffected, say why (the search that came back empty, the file that already matches).

## 4. Report

Show the list in the chat, in the four categories, as a numbered list so the user can say "do 3 and 5". One bullet per
change: what changed, where it comes from (CHANGELOG version, PR or commit), and for category 1 and 2 what it means
for us. Also write it to `.claude/zag-updates/<from>-to-<to>.md` (do not commit it). Do not start implementing before
the user has seen the list, unless they named changes to start with: then start with exactly those.

## 5. Implement

One branch per change or per coherent group (a rename that touches five files is one change), one after the other:
worktrees do not fit the submodule setup. The library is its own repository (`packages/fluid-primitives`, the
submodule), so a change usually has a branch of the same name in both repositories, and the monorepo branch carries the
docs, registry examples and the submodule pointer.

- Branch from the branch that carries the bump (`feature/zag-<topic>`, or `fix/`).
- Follow `CLAUDE.md`: the changelog skill before each library commit (a breaking Zag change that reaches the user is a
  breaking-change entry under `## Unreleased`), `TONE_OF_VOICE.md` for docs, tests that fail when the guarded code is
  removed (break the code once on purpose), and the format hook.
- Prefer the structural fix over a flag: when Zag renames something, rename ours to match instead of aliasing it.
- Verify as in step 1, plus a look in the docs site for what only a browser shows (memory `headless-browser-verification`).
- Each branch ends with a short note: what changed, what was verified, what was not.

## Traps

- The docs site imports the library from `packages/fluid-primitives/dist` and Zag through Vite's dependency cache.
  After a bump or a change of a primitive, run `ddev npm run primitives:build`. After a Zag bump the dev server keeps
  serving the old Zag from `node_modules/.vite/deps`: delete that folder and change `vite.config.ts` once (add a blank
  line, then `git checkout` it) so the server restarts and optimises again. A browser check against the old Zag proves
  nothing.
- A `ui:prop` description is an attribute of a Fluid tag: braces (`{ display: none }`) and double quotes inside it
  break the parse (`Cannot cast an array to string`). Use single quotes and keep CSS in the docs page.
- Prettier reflows lines the format hook has not seen when a script edited a file: re-read a file before an edit that
  depends on its exact text, and run `ddev npm run format -- <paths>` after scripted edits.
- `ddev` writes `node_modules` and the version files through a sync that can lag by seconds: a command that ran in the
  container may show old content on the host for a moment.
- Zag renames its translation keys together with its parts; our `.xlf` ids and `translations` converters follow them.
