# AI and LLMs

Give your coding agent accurate Fluid Primitives knowledge: an installable Claude Code skill, plus plain-text endpoints any agent can read.

## Claude Code Skill

The skill teaches the usage model and knows the live docs endpoints below, so the agent reads the current props, data attributes, machine options and JS API of a component from the docs instead of guessing. Install it from the plugin marketplace in the library repo:

```bash
/plugin marketplace add jramke/fluid-primitives
/plugin install fluid-primitives@fluid-primitives
```

## Endpoints

Any agent or tool can use these, no skill needed.

| Endpoint                                       | Returns                                                                                                                               |
| ---------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------- |
| [`/llms.txt`](/llms.txt)                       | Index of every documentation page with a short description, following [llmstxt.org](https://llmstxt.org).                             |
| `/<page>.md`                                   | Any documentation page as Markdown, e.g. `/docs/components/dialog.md`. Props tables, machine options and JS API are already resolved. |
| [`/registry/components`](/registry/components) | JSON list of the styled components the `typo3 ui:add` command can copy into your project.                                             |
| `/registry/components/<name>`                  | The manifest of one component, with its file names.                                                                                   |
| `/registry/components/<name>/files/<file>`     | The content of one file.                                                                                                              |

Each documentation page also has a "View as Markdown" link.
