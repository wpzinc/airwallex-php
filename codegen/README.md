# Code generation

`spec.json` lists every Airwallex endpoint the SDK wraps. `generate.php` turns it into:

- `src/Service/**/*Service.php`: one class per resource, one method per endpoint
- `src/Service/**/*ServiceFactory.php`: the `$airwallex->billing->...` accessors
- `src/Resource/**/*.php`: typed resource classes returned by the services
- `docs/SERVICES.md`

Generated files are marked `@generated` and are deleted and rewritten on each run, so don't edit them by hand. `FileService` and everything outside `Service/` and `Resource/` is hand-written.

```bash
composer generate
composer test
```

## Spec format

```json
{
  "namespace": "Billing",
  "property": "subscriptions",
  "class": "SubscriptionService",
  "resource": "Subscription",
  "doc": "https://www.airwallex.com/docs/api/billing/subscriptions",
  "methods": [
    {
      "name": "create",
      "http": "POST",
      "path": "/api/v1/billing/subscriptions/create",
      "summary": "Create a subscription",
      "returns": "object",
      "request_id": "required"
    }
  ]
}
```

- `namespace`: `""` for top-level services, otherwise the group (e.g. `Billing` becomes `$airwallex->billing`).
- `resource`: class returned by the service's methods, or `null` to return a plain `AirwallexObject`.
- `returns`: `object` or `collection` (paginated list).
- `request_id`: `required`, `optional` or `none`. On `required` and `optional` endpoints the SDK adds a UUID `request_id` when the caller doesn't pass one.
- Path placeholders (`{id}`, `{global_account_id}`) become leading `string` arguments, in order.

The spec was built from the API reference for version `2026-08-21`, using the full docs navigation (including pages not linked from the sidebar, such as Spend and Batch Transfers). Each page is available as Markdown by appending `.md` to its URL, and `request_id` flags were taken from each endpoint's request body.
