# Changelog

All notable changes to `tapsilat-laravel` will be documented in this file.

## [Unreleased]

### Fixed

- Webhooks: verify the `Tapsilat-Signature: t=...,v1=...` header Tapsilat actually sends (HMAC-SHA256 of
  `timestamp.body`, 5-minute replay tolerance via `TAPSILAT_WEBHOOK_TOLERANCE`). The endpoint read
  `X-Tapsilat-Signature` with a body-only HMAC, so it answered 401 to every real callback.
- Webhooks: dispatch `OrderPaid`, `OrderFailed` and `OrderRefunded` from the order status in Tapsilat's
  `{type, data, token}` callbacks. They matched an `event` field that callbacks never carry.
- Event getters read the reference ID, conversation ID and order ID from every callback type.
- `getCheckoutUrl()` returns `null` instead of throwing a `TypeError` when an order has no checkout URL.
- `manualCallback()` passes the optional conversation ID.
- Require a released `tapsilat/tapsilat-php` instead of `dev-main`.
- `TAPSILAT_PAYMENT_SUCCESS_URL` and `TAPSILAT_PAYMENT_FAILURE_URL` were documented as order defaults but
  never applied; `createOrder()` now uses them when the order sets no payment URLs.
- `getOrderPaymentDetails()` and `orderManualCallback()` no longer send an empty `conversation_id`.
- README: the submerchant example passed 6 of the 13 required constructor arguments, and the raw client
  example called a method that does not exist.

### Added

- Any `TapsilatAPI` method the manager does not wrap (charge, card installments, VPOS, tokenization,
  marketplace, ...) is forwarded to the client through the facade.
- The facade documents every manager and SDK method (`@method`), so IDEs and static analysis see the
  whole API, and a test keeps that list in sync with the SDK.
- README sections for charging, order filters and payment details, submerchant settlement and payout
  events, card installments and saved cards, virtual POS, marketplace (API v2) and currencies.
- `getOrganizationLimitsById()`.

### Deprecated

- `getOrganizationLimits()`, `getOrganizationLimitUser()` and `setOrganizationLimitUser()`: the panel has no
  such routes.

## [1.0.0] - 2024-12-17

### Added

- Initial release
- Laravel 10.x and 11.x support
- Tapsilat PHP SDK integration
- Service Provider with auto-discovery
- Facade for easy access
- Configuration publishing
- Artisan commands:
  - `tapsilat:install` - Package installation
  - `tapsilat:health` - API health check
- Webhook handling:
  - Automatic signature verification
  - Event dispatching for all webhook types
- Events:
  - `WebhookReceived` - Generic webhook event
  - `OrderPaid` - Order payment successful
  - `OrderFailed` - Order payment failed
  - `OrderRefunded` - Order refunded
  - `SubscriptionCreated` - Subscription created
  - `SubscriptionCanceled` - Subscription canceled
  - `SubscriptionPaymentSucceeded` - Subscription payment successful
  - `SubscriptionPaymentFailed` - Subscription payment failed
- Full API support:
  - Order management (create, get, list, cancel, refund)
  - Order term management
  - Subscription management
  - Organization settings
  - Health monitoring
- Optional request/response logging
- Comprehensive documentation
