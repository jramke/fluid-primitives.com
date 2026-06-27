# Introduction

Fluid Primitives brings modern component patterns to TYPO3. Build accessible, composable UI components with the same developer experience you'd expect from React libraries like Radix or Base UI - but for Fluid templates.

{% component: "ui:alert", arguments: {"title": "Under Construction", "text": "Fluid Primitives is in early development. Expect breaking changes until v1.0. We're actively shipping new components and features.", "variant": "warning"} %}

If something is unclear, you found a bug or want a feature, please open an [issue](https://github.com/jramke/fluid-primitives/issues) or reach out via [slack](https://typo3.slack.com/archives/D07KMQW2A6N).

## What You Get

**Accessible by default.** Every interactive component handles interaction and semantics automatically in a WAI-ARIA compliant way via [Zag.js](https://zagjs.com/) state machines.

**Composable API.** No more prop drilling. Build complex UIs by composing small, focused parts that work together.

**Unstyled.** Zero design opinions. Use Tailwind, vanilla CSS, or any styling approach. You control every pixel.

**Server-rendered.** Components render on the server with PHP/Fluid, then hydrate on the client. No layout shift, great for SEO.

## Acknowledgments

- [Zag.js](https://zagjs.com/) - The state machine foundation
- [Radix UI](https://www.radix-ui.com/primitives) - API design inspiration
- [Base UI](https://base-ui.com/) - Component behavior patterns
- [Ark UI](https://ark-ui.com/) - Zag.js integration patterns
