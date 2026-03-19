# Security Report

## ⚠️ <ins>_This report is internal only_</ins> ⚠️

### Critical
1. API Bearer Token Handling — `src/Utilities/Services/Api.php`
   The secret API key is stored as a plain string property, passed through getter methods, and could leak in stack traces if exceptions occur.

Recommendation: Never store tokens as plain strings. Obfuscate in any debug output (show last 4 chars only). Clear from memory when no longer needed.

### High
2. Unvalidated URLs Passed to Payment API — src/Gateways/AbstractPaymentGateway.php:28-39
   return_url, cancel_url, and notification_url from the DTO are forwarded to the Payplug API without any validation. This opens the door to open redirect and phishing attacks.
``` PHP
'return_url' => $urls['return'],  // no validation
'cancel_url' => $urls['cancel'],
```
Recommendation: Validate with ``filter_var($url, FILTER_VALIDATE_URL)``, enforce HTTPS, and optionally restrict to your domain.

3. Empty Whitelist Arrays — `src/Utilities/Traits/DependenciesLoader.php:11-15`
   `$allowed_services` and `$allowed_gateways` are initialized empty and never populated, so in_array() checks always evaluate against an empty array — the whitelist mechanism is effectively disabled.

Recommendation: Populate these arrays with the actual allowed values at initialization.

4. Dynamic Class Instantiation — `src/Gateways/PaymentGateway.php:17-20`
   The payment method name (user input) is used directly to construct a class name, with no whitelist enforced.
``` PHP
$class = '\PayplugPluginCore\Gateways\Payment\\'
. str_replace('_', '', ucwords($payment_method_name, '_'))
. 'PaymentGateway';
```
Recommendation: Validate against an explicit whitelist (e.g., ['standard', 'apple_pay', 'google_pay']) before building the class name.

5. Exception Message Information Disclosure — src/Utilities/Services/Api.php:82
   Raw exception messages are re-thrown to callers, potentially leaking internal configuration details or API information.

Recommendation: Log full errors server-side, expose only generic messages to callers.

### Medium
6. Unvalidated Array Key Access — `src/Gateways/AbstractPaymentGateway.php:33-39`
   Direct array access (`$customer['billing']`, `$urls['return']`, etc.) without checking key existence. Will cause PHP warnings/errors on incomplete data.

Recommendation: Use `$array['key'] ?? null` or `array_key_exists()`.

7. Missing Input Validation on Payment Method — `src/Actions/PaymentAction.php:25-27`
   Only a null check is performed. No format, length, or character validation.

`// todo: add a validator to check if the given paymentDTO is usable  ← still a TODO`

Recommendation: Implement the noted validator, enforce an allowed-values whitelist.

8. Silent Type Coercion in DTO Hydration — `src/Models/Entities/PaymentInputDTO.php:64-71`
   Invalid values are silently cast (e.g., `(int) "abc" → 0`) instead of being rejected.

Recommendation: Validate before casting; return meaningful errors for type mismatches.

Low / Informational

| Issue | File                                                                                                          |
|-------|---------------------------------------------------------------------------------------------------------------|
| 9	    | Internal parameter names exposed in exception messages	src/Gateways/Payment/StandardPaymentGateway.php:40     |
| 10	   | Generic \Exception used everywhere (hard to distinguish errors)	src/Utilities/Exceptions/PayplugException.php |
| 11	   | Xdebug included in Docker build (should be dev-only)	Dockerfile                                               |


### Priority Action Plan

- Immediate: Add URL validation for `return_url / cancel_url / notification_url`
- Immediate: Populate and enforce service/gateway whitelists
- Short term: Add payment method whitelist validation
- Short term: Implement the `TODO` validator in `PaymentAction`
- Short term: Replace raw exception propagation with a safe error translation layer
- Medium term: Custom exception hierarchy + secure token handling