# Copilot Instructions

These instructions apply to code reviews on pull requests in this repository.

## Project Context

This is `payplug-plugin-mcp`, a PHP library that acts as the core integration layer for Payplug across multiple e-commerce plugins (WooCommerce, PrestaShop, etc.). It handles payment creation, refunds, DTO hydration, validation, and API communication via the `payplug/payplug-php` SDK.

**Stack**: PHP 7.4 (development and composer constraint), `payplug/payplug-php ^4.0`, PHPUnit 9.5, Mockery ^1.3

**PHP compatibility note**: the composer constraint is `^7.4`, but the source code is intentionally written without PHP 7.4+ syntax (no typed properties, no arrow functions, no `??=`) so that existing client deployments still running PHP 7.1 are not broken at runtime. Do not conflate the composer requirement with the runtime compatibility target.

**Namespace**: `PayPlugPluginMcp\`

**Key structure**:
- `src/Actions/PaymentAction.php` — orchestrates payment and refund flows
- `src/Gateways/` — payment method gateways (`StandardPaymentGateway`, `EmailLinkPaymentGateway`) and `RefundGateway`
- `src/Models/Entities/` — DTOs: `PaymentInputDTO`, `PaymentOutputDTO`, `RefundInputDTO`, `RefundOutputDTO`
- `src/Validators/` — `PaymentResourceValidator`, `RefundValidator`
- `src/Utilities/Services/Api.php` — wraps the PayPlug PHP SDK

## Intentional Patterns — Do Not Flag as Issues

- **Docblock type hints instead of typed properties** — all class properties use `/** @var type */` style on purpose. Typed properties are a PHP 7.4 feature; since this code must remain syntactically compatible with PHP 7.1 runtimes, they cannot be used. Do not suggest converting docblocks to typed properties.
- **`get_api()`, `get_payment_gateway()`, `get_refund_gateway()` are public in `PaymentAction`** — these factory methods are public so they can be mocked in unit tests via Mockery. This is the intended testability pattern for this project.
- **Amount in cents** — all monetary amounts in DTOs and API calls are in the smallest currency unit (cents). Any conversion must be explicit and done by the calling plugin, not this library.

## PayPlug PHP SDK — Dynamic Properties

The `payplug/payplug-php` SDK models (`Payplug\Resource\Payment`, `Payplug\Resource\Refund`, etc.) use **dynamic properties** — API response fields are set at runtime via `__set()` and are not declared as class properties. This means:

- Static analysis tools (PHPStan, IDEs) will emit warnings like `Property Payment::$failure does not exist` or `Property Payment::$is_paid does not exist` — **these are expected and not bugs**.
- There is no compile-time guarantee that a given field exists on a resource object. All access to SDK resource properties is inherently dynamic.
- Do not flag missing property declarations on SDK resource classes as issues.
- Do not suggest adding `@property` annotations to SDK classes — they are a vendored dependency.
- Null-checks before accessing SDK properties (e.g. `isset($payment->failure)`, `null !== $resource->id`) are intentional defensive patterns, not unnecessary guards.

## Code Review Dimensions

### Security
- API bearer tokens must never appear in logs, exception messages, or HTTP responses
- Payment amounts must be validated server-side — `RefundValidator` enforces this; bypassing it is a bug
- `redirect_url` values must come from the PayPlug API response, never constructed from user input
- IPN/webhook payloads must be verified via the SDK before any processing
- Card data (PAN, CVV) must never appear in logs, error messages, or stored metadata

### Performance
- Unnecessary object instantiation in hot paths
- Unbounded loops or unvalidated array traversal in DTOs
- Algorithmic complexity in validators

### Correctness
- `declare(strict_types=1)` is required on all files — flag any new file missing it
- PHP 7.4+ syntax (arrow functions, typed properties, `??=`, named arguments) must not be introduced — the source code must remain syntactically compatible with PHP 7.1 runtimes even though the composer constraint is `^7.4`
- DTOs must validate all required fields in `hydrate()` and throw on missing/invalid required inputs
- Refund amount must not exceed `payment.amount - payment.amount_refunded`
- `PaymentResourceValidator::validateIsPaid()` must be called before any refund — skipping it is a bug
- API bearer token must be loaded (`Api::load()`) before any SDK call — using an uninitialised API client is a bug
- `RefundOutputDTO` and `PaymentOutputDTO` must reflect the exact API response structure — silent field drops are bugs

### Maintainability
- Naming clarity, single responsibility, duplication
- Test coverage: PHPUnit in `tests/` with unit and integration groups
- PHPStan compliance at configured level — suppressions must go in the baseline, not inline
- PHP-CS-Fixer compliance with `.php-cs-fixer.dist.php` — `@PHP71Migration` ruleset, no 7.4+ fixer rules
- New gateway classes must extend `AbstractPaymentGateway` and implement `formatPaymentAttributes()`
- New validators must throw `\Exception` with a descriptive message on failure — no silent returns

## Output Format

Structure the review comment exactly as follows:

### 1. What's Good

A bullet list of positive observations — things done well, non-obvious correct decisions, solid patterns.

---

### 2. Summary table

A markdown table with two columns: **Dimension** and **Rating**. One row per review dimension. Use emoji inline with the rating text:

| Dimension | Rating |
|---|---|
| Security | ✅ Fine |
| Correctness | ⚠️ Medium (short reason) |
| Performance | ✅ Fine |
| Maintainability | ⚠️ Low (short reason) |

Severity scale:
- ✅ **Fine** — no issues
- ⚠️ **Low / Medium** — should be fixed but not blocking
- ❌ **High / Critical** — must be fixed before merge

---

### 3. Closing one-liner

A single sentence summarising what needs to be addressed before merge (or that the PR is ready if nothing critical).

---

### 4. Individual findings (one section per issue)

Each finding follows this exact structure:

**Heading:** `[Dimension] [emoji] [Severity]` — e.g. `Security ⚠️ Medium`

**Subtitle (bold):** short title followed by the file path and line number as a markdown link — e.g. `**Missing bearer validation** (Api.php:42)`

**Code block:** the relevant snippet from the diff showing the problem.

**Explanation paragraph:** what the risk is and why it matters. Be concrete.

**Fix line:** start with `Fix:` in bold, then a brief description, followed by a code block showing the suggested fix.

Lead with Critical/High findings. Omit the findings section entirely if there are no issues.

## Iterative Reviews

When reviewing a new commit on a PR that already has open review threads:

- **Resolve threads** for issues that have been addressed in the new commit — do not leave them open if the fix is present.
- **Do not re-open or re-comment** on issues that were already resolved in a previous round.
- Only open new threads for issues that are genuinely new or that remain unresolved.
- If a previous finding was partially addressed, update the thread with what still needs attention rather than opening a duplicate.
