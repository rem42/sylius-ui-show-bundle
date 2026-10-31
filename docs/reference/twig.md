# Twig reference

## Functions

| Function                                         | Description                                                                                     |
|--------------------------------------------------|-------------------------------------------------------------------------------------------------|
| `rem42_sylius_ui_show(resource, name = null, options = {})` | Builds and renders the show of a resource. Throws `ShowNotFoundException` if none supports it. |
| `rem42_sylius_ui_show_exists(resource, name = null)`       | Whether a show exists for the resource (or with this name). Returns `false` for non-objects.    |
| `rem42_sylius_ui_show_render(element, resource, options = {})` | Renders an element. Option `render_label: false` renders a field without its label row.     |
| `rem42_sylius_ui_show_value(resource, path)`               | Null-safe property access (`.` returns the resource).                                           |
| `rem42_sylius_ui_show_label(element)`                      | The label or generated translation key of an element (`''` when the label is `false`).         |
| `rem42_sylius_ui_show_attributes(element, defaults = {})`  | Renders escaped HTML attributes; `class` values are merged with the defaults.                   |
| `rem42_sylius_ui_show_template(type)`                      | The template configured for a type in the current theme (e.g. `twig_hooks`, `field_row`).     |

## Filters

| Filter                                 | Description                                                                                             |
|----------------------------------------|---------------------------------------------------------------------------------------------------------|
| `value\|rem42_sylius_ui_show_string(separator = ', ')` | Converts a value to text: enums (backed value or case name), dates (ATOM), iterables (joined), stringables. |

## Templates

Templates are grouped by theme (`rem42_sylius_ui_show.theme`: `bootstrap` or `shadcn`). Each type maps to
`<theme directory>/<path>` (`@Rem42SyliusUiShow/<theme>/<path>` for the built-in themes):

| Type               | Path                               |
|--------------------|------------------------------------|
| `show`             | `show.html.twig`                   |
| `field_row`        | `field/_row.html.twig`             |
| `card`             | `layout/card.html.twig`            |
| `column`           | `layout/column.html.twig`          |
| `datagrid`         | `layout/datagrid.html.twig`        |
| `details_table`    | `layout/details_table.html.twig`   |
| `hook`             | `layout/hook.html.twig`            |
| `section`          | `layout/section.html.twig`         |
| `side_nav`         | `layout/side_nav.html.twig`        |
| `side_nav_item`, `tab` | `layout/children.html.twig`    |
| `side_nav_divider` | `layout/side_nav_divider.html.twig` |
| `tabs`             | `layout/tabs.html.twig`            |
| `twig_hooks`       | `integration/twig_hooks.html.twig` (page wrapper of the Twig Hooks integration) |
| `<field type>`     | `field/<type>.html.twig`           |

The Twig Hooks hookable uses `@Rem42SyliusUiShow/integration/twig_hooks/show.html.twig`, which does not depend on the theme:
it includes the `twig_hooks` template of the theme.
