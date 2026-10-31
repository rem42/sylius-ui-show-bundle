# Configuration reference

```yaml
# config/packages/rem42_sylius_ui_show.yaml
rem42_sylius_ui_show:
    # Prefix of the translation keys generated for elements without an explicit label.
    label_prefix: 'sylius.ui.'

    # Theme providing the default templates (see the Twig reference): "bootstrap" (sylius/bootstrap-admin-ui),
    # "shadcn" (rem42/shadcn-admin-ui) or one of "themes".
    # Default: "shadcn" when Rem42ShadcnAdminUiBundle is enabled, "bootstrap" otherwise.
    theme: ~

    # Additional themes: name => template directory.
    themes:
        # acme: '@AcmeAdmin/show'

    # Templates per element type, merged with the ones of the theme (see the Twig reference).
    templates:
        # card: 'admin/show/card.html.twig'
        # money: 'admin/show/field/money.html.twig'

    # Sylius Twig Hooks integration (only active when SyliusTwigHooksBundle is enabled).
    twig_hooks:
        enabled: true
        hook: 'sylius_admin.common.show.content'
        hookable: 'rem42_sylius_ui_show'
        priority: -50
```

## Service tags

| Tag                          | Attributes                                   | Added automatically by                      |
|------------------------------|----------------------------------------------|---------------------------------------------|
| `rem42_sylius_ui_show.show`            | `resource_class` (required), `name`, `priority` | `#[AsShow]`                               |
| `rem42_sylius_ui_show.field_guesser`   | `priority`                                   | implementing `FieldGuesserInterface`        |
