![The headless component library for TYPO3 Fluid](./packages/docs/Resources/Public/Images/og-image.png)

# Fluid Primitives

The headless component library for TYPO3 Fluid

## Documentation

The documentation can be found at [fluid-primitives.com](https://fluid-primitives.com).

## Development

Clone the repo with the fluid-primitives submodule, then run:

```bash
ddev start
ddev composer install
ddev snapshot restore --latest
ddev npm install
ddev npm run dev
```

Then open [https://fluid-primitives.ddev.site](https://fluid-primitives.ddev.site) in your browser.

## Deploy the docs site

The `Dockerfile` builds the image of fluid-primitives.com. In the Production context the site refuses to start without these environment variables, none of them is in the repository:

| Variable                                 | What it is                                                                                                                 |
| ---------------------------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| `TYPO3_ENCRYPTION_KEY`                   | The encryption key of the installation, 96 random hex characters. It signs form tokens, `cHash` and sessions.              |
| `TYPO3_DB_PASSWORD`                      | The password of the database. `TYPO3_DB_HOST`, `_PORT`, `_USERNAME` and `_NAME` default to the values of the ddev project. |
| `PROXY_IP` (optional)                    | The address or range of your reverse proxy. Without it the private networks of a container setup are believed.             |
| `TRUSTED_HOSTS_PATTERN` (optional)       | A regular expression for the host header, `(?:www\.)?fluid-primitives\.com` by default.                                    |
| `TYPO3_INSTALL_TOOL_PASSWORD` (optional) | The hash of the Install Tool password. Without it the Install Tool stays locked.                                           |

## Playground

You can find a Playground page under `/playground` where you can test components and context code without modifying the example files.

## Storybook

Run Storybook with:

```bash
ddev npm run storybook
```

and open [fluid-primitives.ddev.site:8080](https://fluid-primitives.ddev.site:8080) in your browser.
