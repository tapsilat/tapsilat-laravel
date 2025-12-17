# Changelog

All notable changes to `tapsilat-laravel` will be documented in this file.

## [Unreleased]

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
