# Introduction

Fluid Primitives is a headless component library for TYPO3 Fluid. Build accessible, composable UI components like accordions, dialogs, tabs and selects in your Fluid templates, and style them any way you like.

{% component: "ui:alert", arguments: {"title": "Under Construction", "text": "Fluid Primitives is in early development. The API can still change until v1.0.", "variant": "warning"} %}

Open an [issue](https://github.com/jramke/fluid-primitives/issues) if something is unclear, you found a bug or want a feature. You can also reach out via [Slack](https://typo3.slack.com/archives/D07KMQW2A6N).

## What you get

**Accessible.** Keyboard interaction, focus management and ARIA attributes come from [Zag.js](https://zagjs.com/) state machines.

**Composable.** Assemble a component from small parts in your template and put your own markup between them.

**Unstyled.** The parts carry no design. Style them with Tailwind, vanilla CSS or whatever your project uses.

**Server-rendered.** Fluid renders the full markup on the server and TypeScript hydrates it in the browser. The page loads without layout shift and search engines see the content.

## Acknowledgments

- [Zag.js](https://zagjs.com/) - The state machine foundation
- [Radix UI](https://www.radix-ui.com/primitives) - API design inspiration
- [Base UI](https://base-ui.com/) - Component behavior patterns
- [Ark UI](https://ark-ui.com/) - Zag.js integration patterns
