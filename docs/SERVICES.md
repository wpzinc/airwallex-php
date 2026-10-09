# Services

Every endpoint the SDK wraps, generated from `codegen/spec.json`. Path parameters come first, then `$params` and `$opts`.

## Core

### `$airwallex->accountCapabilities`

[API reference](https://www.airwallex.com/docs/api/account_capability/account_capability)

| Method | Endpoint | Description |
| --- | --- | --- |
| `enable($id)` | `POST /api/v1/account_capabilities/{id}/enable` | Enable an account capability |
| `allFundingLimits()` | `GET /api/v1/account_capabilities/funding_limits` | List funding limits |
| `retrieve($id)` | `GET /api/v1/account_capabilities/{id}` | Retrieve an account capability |

### `$airwallex->accounts`

[API reference](https://www.airwallex.com/docs/api/scale/accounts)

| Method | Endpoint | Description |
| --- | --- | --- |
| `agreeToTerms($id)` | `POST /api/v1/accounts/{id}/terms_and_conditions/agree` | Agree to terms and conditions |
| `create()` | `POST /api/v1/accounts/create` | Create an account |
| `createAmendment()` | `POST /api/v1/account/amendments/create` | Create an account amendment |
| `retrieveCurrent()` | `GET /api/v1/account` | Retrieve account details |
| `all()` | `GET /api/v1/accounts` | List all connected accounts |
| `reactivate($id)` | `POST /api/v1/accounts/{id}/reactivate` | Reactivate an account |
| `retrieve($id)` | `GET /api/v1/accounts/{id}` | Retrieve an account |
| `retrieveAmendment($amendmentId)` | `GET /api/v1/account/amendments/{amendment_id}` | Retrieve an account amendment |
| `retrieveWalletInfo()` | `GET /api/v1/account/wallet_info` | Retrieve account wallet information |
| `submit($id)` | `POST /api/v1/accounts/{id}/submit` | Submit an account for activation |
| `suspend($id)` | `POST /api/v1/accounts/{id}/suspend` | Suspend an account |
| `update($id)` | `POST /api/v1/accounts/{id}/update` | Update a connected account |

### `$airwallex->authentication`

[API reference](https://www.airwallex.com/docs/api/authentication/embedded_components)

| Method | Endpoint | Description |
| --- | --- | --- |
| `authorize()` | `POST /api/v1/authentication/authorize` | Authorize an Account |

### `$airwallex->balances`

[API reference](https://www.airwallex.com/docs/api/core_resources/balances)

| Method | Endpoint | Description |
| --- | --- | --- |
| `current()` | `GET /api/v1/balances/current` | Retrieve current balances |
| `history()` | `GET /api/v1/balances/history` | Retrieve balance history |

### `$airwallex->beneficiaries`

[API reference](https://www.airwallex.com/docs/api/payouts/beneficiaries)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/beneficiaries/create` | Create a beneficiary |
| `delete($id)` | `POST /api/v1/beneficiaries/{id}/delete` | Delete a beneficiary |
| `generateApiSchema()` | `POST /api/v1/beneficiary_api_schemas/generate` | Generate beneficiary API schema |
| `generateFormSchema()` | `POST /api/v1/beneficiary_form_schemas/generate` | Generate beneficiary form schema |
| `all()` | `GET /api/v1/beneficiaries` | List beneficiaries |
| `allSupportedFinancialInstitutions()` | `GET /api/v1/beneficiary_form_schemas/supported_financial_institutions` | Get supported financial institutions |
| `retrieve($id)` | `GET /api/v1/beneficiaries/{id}` | Retrieve a beneficiary |
| `update($id)` | `POST /api/v1/beneficiaries/{id}/update` | Update a beneficiary |
| `validate()` | `POST /api/v1/beneficiaries/validate` | Validate a beneficiary |
| `verifyAccount()` | `POST /api/v1/beneficiaries/verify_account` | Verify a beneficiary account |

### `$airwallex->charges`

[API reference](https://www.airwallex.com/docs/api/scale/charges)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/charges/create` | Create a charge against a connected account |
| `all()` | `GET /api/v1/charges` | List charges |
| `retrieve($id)` | `GET /api/v1/charges/{id}` | Retrieve a charge |

### `$airwallex->confirmationLetters`

[API reference](https://www.airwallex.com/docs/api/confirmation_letter/confirmation_letter)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/confirmation_letters/create` | Create a confirmation letter |

### `$airwallex->connectedAccountTransfers`

[API reference](https://www.airwallex.com/docs/api/scale/connected_account_transfers)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/connected_account_transfers/create` | Create a transfer between accounts on the same platform |
| `all()` | `GET /api/v1/connected_account_transfers` | List connected account transfers |
| `retrieve($id)` | `GET /api/v1/connected_account_transfers/{id}` | Retrieve a connected account transfer |

### `$airwallex->connectedStores`

[API reference](https://www.airwallex.com/docs/api/supporting_services/connected_stores)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/ecosystem/connected_stores/{id}` | Retrieve a Connected Store |
| `all()` | `GET /api/v1/ecosystem/connected_stores` | List Connected Stores |

### `$airwallex->customers`

[API reference](https://www.airwallex.com/docs/api/payments/customers)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/customers/create` | Create a customer |
| `generateClientSecret($id)` | `GET /api/v1/pa/customers/{id}/generate_client_secret` | Generate a client secret for a customer |
| `all()` | `GET /api/v1/pa/customers` | List customers |
| `retrieve($id)` | `GET /api/v1/pa/customers/{id}` | Retrieve a customer |
| `update($id)` | `POST /api/v1/pa/customers/{id}/update` | Update a customer |

### `$airwallex->deposits`

[API reference](https://www.airwallex.com/docs/api/core_resources/deposits)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/deposits/create` | Create a deposit via direct debit |
| `all()` | `GET /api/v1/deposits` | List all deposits |
| `retrieve($id)` | `GET /api/v1/deposits/{id}` | Retrieve a deposit |

### `$airwallex->directDebits`

[API reference](https://www.airwallex.com/docs/api/core_resources/direct_debits)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($transactionId)` | `GET /api/v1/direct_debits/{transaction_id}` | Retrieve a Direct Debit |
| `all()` | `GET /api/v1/direct_debits` | List all Direct Debits |
| `cancel($transactionId)` | `POST /api/v1/direct_debits/{transaction_id}/cancel` | Cancel a Direct Debit |

### `$airwallex->globalAccounts`

[API reference](https://www.airwallex.com/docs/api/core_resources/global_accounts)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/global_accounts/create` | Create a Global Account |
| `retrieve($id)` | `GET /api/v1/global_accounts/{id}` | Retrieve a Global Account |
| `all()` | `GET /api/v1/global_accounts` | List all Global Accounts |
| `update($id)` | `POST /api/v1/global_accounts/{id}/update` | Update an existing Global Account |
| `generateStatementLetter($id)` | `POST /api/v1/global_accounts/{id}/generate_statement_letter` | Generate a Global Account statement |
| `close($id)` | `POST /api/v1/global_accounts/{id}/close` | Close a Global Account |
| `createAlias($globalAccountId)` | `POST /api/v1/global_accounts/{global_account_id}/aliases/create` | Create an alias for a Global Account |
| `retrieveAlias($globalAccountId, $id)` | `GET /api/v1/global_accounts/{global_account_id}/aliases/{id}` | Retrieve an alias |
| `allAliases($globalAccountId)` | `GET /api/v1/global_accounts/{global_account_id}/aliases` | List all aliases |
| `initiateAliasPort($globalAccountId, $id)` | `POST /api/v1/global_accounts/{global_account_id}/aliases/{id}/initiate_port` | Initiate an alias port |
| `submitAliasVerificationCode($globalAccountId, $id)` | `POST /api/v1/global_accounts/{global_account_id}/aliases/{id}/submit_verification_code` | Submit an alias verification code |
| `requestNewAliasVerificationCode($globalAccountId, $id)` | `POST /api/v1/global_accounts/{global_account_id}/aliases/{id}/request_new_verification_code` | Request a new alias verification code |
| `cancelAlias($globalAccountId, $id)` | `POST /api/v1/global_accounts/{global_account_id}/aliases/{id}/cancel` | Cancel an alias |
| `retrieveMandate($globalAccountId, $id)` | `GET /api/v1/global_accounts/{global_account_id}/mandates/{id}` | Retrieve a Direct Debit Payout Mandate |
| `allMandates($globalAccountId)` | `GET /api/v1/global_accounts/{global_account_id}/mandates` | List all Direct Debit Payout Mandates |
| `cancelMandate($globalAccountId, $id)` | `POST /api/v1/global_accounts/{global_account_id}/mandates/{id}/cancel` | Cancel a Direct Debit Payout Mandate |
| `allTransactions($id)` | `GET /api/v1/global_accounts/{id}/transactions` | List all Global Account transactions |

### `$airwallex->linkedAccounts`

[API reference](https://www.airwallex.com/docs/api/core_resources/linked_accounts)

| Method | Endpoint | Description |
| --- | --- | --- |
| `startAuth()` | `POST /api/v1/linked_accounts/auth` | Start a new linked account authentication |
| `completeAuth($id)` | `POST /api/v1/linked_accounts/{id}/complete_auth` | Complete an authentication refresh |
| `confirm($id)` | `POST /api/v1/linked_accounts/{id}/confirm` | Confirm a linked account from candidates |
| `create()` | `POST /api/v1/linked_accounts/create` | Create a linked account |
| `all()` | `GET /api/v1/linked_accounts` | List all linked accounts |
| `retrieveBalances($id)` | `GET /api/v1/linked_accounts/{id}/balances` | Get available balances for a linked account |
| `refreshAuth($id)` | `POST /api/v1/linked_accounts/{id}/auth` | Refresh authentication for a linked account |
| `retrieve($id)` | `GET /api/v1/linked_accounts/{id}` | Retrieve a linked account |
| `retrieveMandate($id)` | `GET /api/v1/linked_accounts/{id}/mandate` | Retrieve a direct debit deposit mandate |
| `updateMandate($id)` | `POST /api/v1/linked_accounts/{id}/mandate` | Update a direct debit deposit mandate |
| `suspend($id)` | `POST /api/v1/linked_accounts/{id}/suspend` | Suspend a linked account |
| `verifyMicrodeposits($id)` | `POST /api/v1/linked_accounts/{id}/verify_microdeposits` | Verify a linked account using micro-deposits |

### `$airwallex->paymentAttempts`

[API reference](https://www.airwallex.com/docs/api/payments/payment_attempts)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/pa/payment_attempts/{id}` | Retrieve a Payment Attempt |
| `all()` | `GET /api/v1/pa/payment_attempts` | List Payment Attempts |

### `$airwallex->paymentConsents`

[API reference](https://www.airwallex.com/docs/api/payments/payment_consents)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/payment_consents/create` | Create a Payment Consent |
| `retrieve($id)` | `GET /api/v1/pa/payment_consents/{id}` | Retrieve a Payment Consent |
| `all()` | `GET /api/v1/pa/payment_consents` | List Payment Consents |
| `update($id)` | `POST /api/v1/pa/payment_consents/{id}/update` | Update a Payment Consent |
| `verify($id)` | `POST /api/v1/pa/payment_consents/{id}/verify` | Verify a Payment Consent |
| `verifyContinue($id)` | `POST /api/v1/pa/payment_consents/{id}/verify_continue` | Continue verifying a Payment Consent |
| `disable($id)` | `POST /api/v1/pa/payment_consents/{id}/disable` | Disable a Payment Consent |

### `$airwallex->paymentDisputes`

[API reference](https://www.airwallex.com/docs/api/payments/payment_disputes)

| Method | Endpoint | Description |
| --- | --- | --- |
| `accept($id)` | `POST /api/v1/pa/payment_disputes/{id}/accept` | Accept a PaymentDispute |
| `challenge($id)` | `POST /api/v1/pa/payment_disputes/{id}/challenge` | Challenge a PaymentDispute |
| `all()` | `GET /api/v1/pa/payment_disputes` | List PaymentDisputes |
| `allRelatedPaymentIntents($id)` | `GET /api/v1/pa/payment_disputes/{id}/related_payment_intents` | List payment intents related to a PaymentDispute |
| `retrieve($id)` | `GET /api/v1/pa/payment_disputes/{id}` | Retrieve a PaymentDispute |
| `update($id)` | `POST /api/v1/pa/payment_disputes/{id}/update` | Update a PaymentDispute |

### `$airwallex->paymentIntents`

[API reference](https://www.airwallex.com/docs/api/payments/payment_intents)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/payment_intents/create` | Create a Payment Intent |
| `retrieve($id)` | `GET /api/v1/pa/payment_intents/{id}` | Retrieve a Payment Intent |
| `all()` | `GET /api/v1/pa/payment_intents` | List Payment Intents |
| `update($id)` | `POST /api/v1/pa/payment_intents/{id}/update` | Update a Payment Intent |
| `confirm($id)` | `POST /api/v1/pa/payment_intents/{id}/confirm` | Confirm a Payment Intent |
| `capture($id)` | `POST /api/v1/pa/payment_intents/{id}/capture` | Capture a Payment Intent |
| `confirmContinue($id)` | `POST /api/v1/pa/payment_intents/{id}/confirm_continue` | Continue to confirm a Payment Intent |
| `incrementAuthorization($id)` | `POST /api/v1/pa/payment_intents/{id}/increment_authorization` | Increment authorization for a Payment Intent |
| `cancel($id)` | `POST /api/v1/pa/payment_intents/{id}/cancel` | Cancel a Payment Intent |

### `$airwallex->paymentLinks`

[API reference](https://www.airwallex.com/docs/api/payments/payment_links)

| Method | Endpoint | Description |
| --- | --- | --- |
| `activate($id)` | `POST /api/v1/pa/payment_links/{id}/activate` | Activate a payment link |
| `create()` | `POST /api/v1/pa/payment_links/create` | Create a payment link |
| `deactivate($id)` | `POST /api/v1/pa/payment_links/{id}/deactivate` | Deactivate a payment link |
| `all()` | `GET /api/v1/pa/payment_links` | List payment links |
| `notifyShopper($id)` | `POST /api/v1/pa/payment_links/{id}/notify_shopper` | Send a payment link to a shopper |
| `retrieve($id)` | `GET /api/v1/pa/payment_links/{id}` | Retrieve a payment link |
| `update($id)` | `POST /api/v1/pa/payment_links/{id}/update` | Update a payment link |

### `$airwallex->paymentMethods`

[API reference](https://www.airwallex.com/docs/api/payments/payment_methods)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/payment_methods/create` | Create a payment method |
| `disable($id)` | `POST /api/v1/pa/payment_methods/{id}/disable` | Disable a payment method |
| `all()` | `GET /api/v1/pa/payment_methods` | List payment methods |
| `retrieve($id)` | `GET /api/v1/pa/payment_methods/{id}` | Retrieve a payment method |
| `update($id)` | `POST /api/v1/pa/payment_methods/{id}/update` | Update a payment method |

### `$airwallex->reference`

[API reference](https://www.airwallex.com/docs/api/supporting_services/reference_data)

| Method | Endpoint | Description |
| --- | --- | --- |
| `allIndustryCategories()` | `GET /api/v1/reference/industry_categories` | Industry categories |
| `allInvalidConversionDates()` | `GET /api/v1/reference/invalid_conversion_dates` | Invalid conversion dates |
| `allSupportedCurrencies()` | `GET /api/v1/reference/supported_currencies` | Supported currencies |
| `retrieveExpiringQuota()` | `GET /api/v1/reference/order_items/expiring_quota` | Get expiring transfer or deposit limit |
| `retrieveQuota()` | `GET /api/v1/reference/order_items/quota` | Get transfer or deposit limit |
| `increaseQuota()` | `POST /api/v1/reference/order_items/upload` | Increase transfer or deposit limit |

### `$airwallex->refunds`

[API reference](https://www.airwallex.com/docs/api/payments/refunds)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/refunds/create` | Create a refund |
| `all()` | `GET /api/v1/pa/refunds` | List refunds |
| `retrieve($id)` | `GET /api/v1/pa/refunds/{id}` | Retrieve a refund |

### `$airwallex->securityAuditLogs`

[API reference](https://www.airwallex.com/docs/api/security/activity_log)

| Method | Endpoint | Description |
| --- | --- | --- |
| `all()` | `GET /api/v1/audit_log/security_audit_logs` | List security audit logs |

### `$airwallex->transfers`

[API reference](https://www.airwallex.com/docs/api/payouts/transfers)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/transfers/create` | Create a transfer |
| `retrieve($id)` | `GET /api/v1/transfers/{id}` | Retrieve a transfer |
| `all()` | `GET /api/v1/transfers` | List all transfers |
| `confirmFunding($id)` | `POST /api/v1/transfers/{id}/confirm_funding` | Confirm funding for a transfer |
| `validate()` | `POST /api/v1/transfers/validate` | Validate a transfer |
| `cancel($id)` | `POST /api/v1/transfers/{id}/cancel` | Cancel a transfer |

### `$airwallex->webhooks`

[API reference](https://www.airwallex.com/docs/api/webhooks/webhooks)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/webhooks/create` | Create a webhook |
| `retrieve($id)` | `GET /api/v1/webhooks/{id}` | Retrieve a webhook |
| `all()` | `GET /api/v1/webhooks` | List webhooks |
| `update($id)` | `POST /api/v1/webhooks/{id}/update` | Update a webhook |
| `delete($id)` | `POST /api/v1/webhooks/{id}/delete` | Delete a webhook |

## Accounting

### `$airwallex->accounting->fieldValues`

[API reference](https://www.airwallex.com/docs/api/accounting/accounting_field_values)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/accounting/accounting_field_values/create` | Create an accounting field value |
| `retrieve($id)` | `GET /api/v1/accounting/accounting_field_values/{id}` | Retrieve an accounting field value |
| `all()` | `GET /api/v1/accounting/accounting_field_values` | List accounting field values |
| `update($id)` | `POST /api/v1/accounting/accounting_field_values/{id}/update` | Update an accounting field value |

### `$airwallex->accounting->fields`

[API reference](https://www.airwallex.com/docs/api/accounting/accounting_fields)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/accounting/accounting_fields/create` | Create an accounting field |
| `retrieve($id)` | `GET /api/v1/accounting/accounting_fields/{id}` | Retrieve an accounting field |
| `all()` | `GET /api/v1/accounting/accounting_fields` | List accounting fields |
| `update($id)` | `POST /api/v1/accounting/accounting_fields/{id}/update` | Update an accounting field |

### `$airwallex->accounting->glAccounts`

[API reference](https://www.airwallex.com/docs/api/accounting/general_ledger_accounts)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/accounting/gl_accounts/create` | Create a General Ledger Account |
| `retrieve($id)` | `GET /api/v1/accounting/gl_accounts/{id}` | Retrieve a General Ledger Account |
| `all()` | `GET /api/v1/accounting/gl_accounts` | List General Ledger Accounts |
| `update($id)` | `POST /api/v1/accounting/gl_accounts/{id}/update` | Update a General Ledger Account |

### `$airwallex->accounting->taxCodes`

[API reference](https://www.airwallex.com/docs/api/accounting/tax_codes)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/accounting/tax_codes/create` | Create a tax code |
| `retrieve($id)` | `GET /api/v1/accounting/tax_codes/{id}` | Retrieve a tax code |
| `all()` | `GET /api/v1/accounting/tax_codes` | List tax codes |
| `update($id)` | `POST /api/v1/accounting/tax_codes/{id}/update` | Update a tax code |

## Billing

### `$airwallex->billing->checkouts`

[API reference](https://www.airwallex.com/docs/api/billing/billing_checkouts)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/billing/billing_checkouts/create` | Create a billing checkout |
| `retrieve($id)` | `GET /api/v1/billing/billing_checkouts/{id}` | Retrieve a billing checkout |
| `all()` | `GET /api/v1/billing/billing_checkouts` | List billing checkouts |
| `update($id)` | `POST /api/v1/billing/billing_checkouts/{id}/update` | Update a billing checkout |
| `cancel($id)` | `POST /api/v1/billing/billing_checkouts/{id}/cancel` | Cancel a billing checkout |

### `$airwallex->billing->coupons`

[API reference](https://www.airwallex.com/docs/api/billing/coupons)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/billing/coupons/create` | Create a coupon |
| `retrieve($id)` | `GET /api/v1/billing/coupons/{id}` | Retrieve a coupon |
| `all()` | `GET /api/v1/billing/coupons` | List all coupons |
| `update($id)` | `POST /api/v1/billing/coupons/{id}/update` | Update a coupon |

### `$airwallex->billing->creditNotes`

[API reference](https://www.airwallex.com/docs/api/billing/credit_notes)

| Method | Endpoint | Description |
| --- | --- | --- |
| `addLineItems($id)` | `POST /api/v1/billing/credit_notes/{id}/add_line_items` | Add line items to a credit note |
| `create()` | `POST /api/v1/billing/credit_notes/create` | Create a credit note |
| `delete($id)` | `POST /api/v1/billing/credit_notes/{id}/delete` | Delete a credit note |
| `deleteLineItems($id)` | `POST /api/v1/billing/credit_notes/{id}/delete_line_items` | Remove line items from a credit note |
| `finalize($id)` | `POST /api/v1/billing/credit_notes/{id}/finalize` | Finalize a credit note |
| `all()` | `GET /api/v1/billing/credit_notes` | List credit notes |
| `allLineItems($id)` | `GET /api/v1/billing/credit_notes/{id}/line_items` | List a credit note's line items |
| `preview()` | `POST /api/v1/billing/credit_notes/preview` | Preview a credit note |
| `retrieve($id)` | `GET /api/v1/billing/credit_notes/{id}` | Retrieve a credit note |
| `retrieveLineItem($id, $lineItemId)` | `GET /api/v1/billing/credit_notes/{id}/line_items/{line_item_id}` | Retrieve a credit note line item |
| `update($id)` | `POST /api/v1/billing/credit_notes/{id}/update` | Update a credit note |
| `updateLineItems($id)` | `POST /api/v1/billing/credit_notes/{id}/update_line_items` | Update line items in a credit note |
| `void($id)` | `POST /api/v1/billing/credit_notes/{id}/void` | Void a credit note |

### `$airwallex->billing->customers`

[API reference](https://www.airwallex.com/docs/api/billing/billing_customers)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/billing/billing_customers/create` | Create a Billing Customer |
| `retrieve($id)` | `GET /api/v1/billing/billing_customers/{id}` | Retrieve a Billing Customer |
| `retrieveBankTransferInstructions($id)` | `GET /api/v1/billing/billing_customers/{id}/bank_transfer_instructions` | Retrieve Bank Transfer Instructions of a Billing Customer |
| `all()` | `GET /api/v1/billing/billing_customers` | List all Billing Customers |
| `update($id)` | `POST /api/v1/billing/billing_customers/{id}/update` | Update a Billing Customer |

### `$airwallex->billing->eInvoicingDocuments`

[API reference](https://www.airwallex.com/docs/api/billing/e-invoicing_documents)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/billing/e_invoicing_documents/{id}` | Retrieve an E-Invoicing Document |
| `all()` | `GET /api/v1/billing/e_invoicing_documents` | List E-Invoicing Documents |

### `$airwallex->billing->invoices`

[API reference](https://www.airwallex.com/docs/api/billing/invoices)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/billing/invoices/create` | Create an invoice |
| `retrieve($id)` | `GET /api/v1/billing/invoices/{id}` | Retrieve an invoice |
| `all()` | `GET /api/v1/billing/invoices` | List all invoices |
| `update($id)` | `POST /api/v1/billing/invoices/{id}/update` | Update an invoice |
| `preview()` | `POST /api/v1/billing/invoices/preview` | Preview an invoice |
| `finalize($id)` | `POST /api/v1/billing/invoices/{id}/finalize` | Finalize an invoice |
| `markAsPaid($id)` | `POST /api/v1/billing/invoices/{id}/mark_as_paid` | Mark an invoice as paid |
| `pay($id)` | `POST /api/v1/billing/invoices/{id}/pay` | Pay an invoice |
| `delete($id)` | `POST /api/v1/billing/invoices/{id}/delete` | Delete a draft invoice |
| `void($id)` | `POST /api/v1/billing/invoices/{id}/void` | Void an invoice |
| `addLineItems($id)` | `POST /api/v1/billing/invoices/{id}/add_line_items` | Add line items to an invoice |
| `retrieveLineItem($id, $itemId)` | `GET /api/v1/billing/invoices/{id}/line_items/{item_id}` | Retrieve an invoice line item |
| `allLineItems($id)` | `GET /api/v1/billing/invoices/{id}/line_items` | List invoice line items |
| `updateLineItems($id)` | `POST /api/v1/billing/invoices/{id}/update_line_items` | Update line items in an invoice |
| `deleteLineItems($id)` | `POST /api/v1/billing/invoices/{id}/delete_line_items` | Delete line items within an invoice |
| `retrieveLineItemGroup($id, $groupId)` | `GET /api/v1/billing/invoices/{id}/line_item_groups/{group_id}` | Retrieve an invoice line item group |
| `allLineItemGroups($id)` | `GET /api/v1/billing/invoices/{id}/line_item_groups` | List invoice line item groups |

### `$airwallex->billing->meters`

[API reference](https://www.airwallex.com/docs/api/billing/meters)

| Method | Endpoint | Description |
| --- | --- | --- |
| `archive($id)` | `POST /api/v1/billing/meters/{id}/archive` | Archive a meter |
| `create()` | `POST /api/v1/billing/meters/create` | Create a meter |
| `all()` | `GET /api/v1/billing/meters` | List meters |
| `allSummaries($id)` | `GET /api/v1/billing/meters/{id}/summaries` | Retrieve usage summaries for a meter |
| `restore($id)` | `POST /api/v1/billing/meters/{id}/restore` | Restore an archived meter |
| `retrieve($id)` | `GET /api/v1/billing/meters/{id}` | Retrieve a meter |
| `update($id)` | `POST /api/v1/billing/meters/{id}/update` | Update a meter |

### `$airwallex->billing->paymentSources`

[API reference](https://www.airwallex.com/docs/api/billing/payment_sources)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/billing/payment_sources/create` | Create a payment source |
| `all()` | `GET /api/v1/billing/payment_sources` | List payment sources |
| `retrieve($id)` | `GET /api/v1/billing/payment_sources/{id}` | Retrieve a payment source |

### `$airwallex->billing->prices`

[API reference](https://www.airwallex.com/docs/api/billing/prices)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/billing/prices/create` | Create a price |
| `all()` | `GET /api/v1/billing/prices` | List prices |
| `retrieve($id)` | `GET /api/v1/billing/prices/{id}` | Retrieve a price |
| `update($id)` | `POST /api/v1/billing/prices/{id}/update` | Update a price |

### `$airwallex->billing->products`

[API reference](https://www.airwallex.com/docs/api/billing/products)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/billing/products/create` | Create a product |
| `retrieve($id)` | `GET /api/v1/billing/products/{id}` | Retrieve a product |
| `all()` | `GET /api/v1/billing/products` | List products |
| `update($id)` | `POST /api/v1/billing/products/{id}/update` | Update a product |

### `$airwallex->billing->rateCards`

[API reference](https://www.airwallex.com/docs/api/billing/rate_cards)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/billing/rate_cards/create` | Create a rate card |
| `update($id)` | `POST /api/v1/billing/rate_cards/{id}/update` | Update a rate card |
| `retrieve($id)` | `GET /api/v1/billing/rate_cards/{id}` | Retrieve a rate card |
| `all()` | `GET /api/v1/billing/rate_cards` | List rate cards |
| `createVersion($id)` | `POST /api/v1/billing/rate_cards/{id}/versions/create` | Create a rate card version |
| `allVersions($id)` | `GET /api/v1/billing/rate_cards/{id}/versions` | List rate card versions |
| `retrieveVersion($id, $version)` | `GET /api/v1/billing/rate_cards/{id}/versions/{version}` | Retrieve a rate card version |
| `allVersionItems($id, $version)` | `GET /api/v1/billing/rate_cards/{id}/versions/{version}/items` | List items in a rate card version |
| `retrieveVersionItem($id, $version, $itemId)` | `GET /api/v1/billing/rate_cards/{id}/versions/{version}/items/{item_id}` | Retrieve an item in a rate card version |

### `$airwallex->billing->subscriptions`

[API reference](https://www.airwallex.com/docs/api/billing/subscriptions)

| Method | Endpoint | Description |
| --- | --- | --- |
| `cancel($id)` | `POST /api/v1/billing/subscriptions/{id}/cancel` | Cancel a subscription |
| `create()` | `POST /api/v1/billing/subscriptions/create` | Create a subscription |
| `all()` | `GET /api/v1/billing/subscriptions` | List subscriptions |
| `allItems($id)` | `GET /api/v1/billing/subscriptions/{id}/items` | List subscription items |
| `retrieve($id)` | `GET /api/v1/billing/subscriptions/{id}` | Retrieve a subscription |
| `retrieveItem($id, $itemId)` | `GET /api/v1/billing/subscriptions/{id}/items/{item_id}` | Retrieve a subscription item |
| `update($id)` | `POST /api/v1/billing/subscriptions/{id}/update` | Update a subscription |

### `$airwallex->billing->transactions`

[API reference](https://www.airwallex.com/docs/api/billing/billing_transactions)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/billing/billing_transactions/{id}` | Retrieve a Billing Transaction |
| `all()` | `GET /api/v1/billing/billing_transactions` | List all Billing Transactions |

### `$airwallex->billing->usageEvents`

[API reference](https://www.airwallex.com/docs/api/billing/usage_events)

| Method | Endpoint | Description |
| --- | --- | --- |
| `ingest()` | `POST /api/v1/billing/usage_events/ingest` | Ingest a usage event |
| `batchIngest()` | `POST /api/v1/billing/usage_events/batch_ingest` | Batch ingest usage events |
| `void()` | `POST /api/v1/billing/usage_events/void` | Void a usage event |

## Checkout

### `$airwallex->checkout->sessions`

[API reference](https://www.airwallex.com/docs/api/checkout/checkout_sessions)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/checkout/checkout_sessions/create` | Create a checkout session |
| `retrieve($id)` | `GET /api/v1/checkout/checkout_sessions/{id}` | Retrieve a checkout session |
| `all()` | `GET /api/v1/checkout/checkout_sessions` | List checkout sessions |
| `update($id)` | `POST /api/v1/checkout/checkout_sessions/{id}/update` | Update a checkout session |
| `cancel($id)` | `POST /api/v1/checkout/checkout_sessions/{id}/cancel` | Cancel a checkout session |

## Finance

### `$airwallex->finance->financialReports`

[API reference](https://www.airwallex.com/docs/api/finance/financial_reports)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/finance/financial_reports/create` | Create a financial report |
| `retrieve($id)` | `GET /api/v1/finance/financial_reports/{id}` | Retrieve a financial report |
| `all()` | `GET /api/v1/finance/financial_reports` | List financial reports |
| `retrieveContent($id)` | `GET /api/v1/finance/financial_reports/{id}/content` | Retrieve financial report content |

### `$airwallex->finance->financialTransactions`

[API reference](https://www.airwallex.com/docs/api/finance/financial_transactions)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/financial_transactions/{id}` | Retrieve a Financial Transaction |
| `all()` | `GET /api/v1/financial_transactions` | List all Financial Transactions |

### `$airwallex->finance->settlements`

[API reference](https://www.airwallex.com/docs/api/finance/settlements)

| Method | Endpoint | Description |
| --- | --- | --- |
| `all()` | `GET /api/v1/pa/financial/settlements` | List settlements |
| `retrieveReport($id)` | `GET /api/v1/pa/financial/settlements/{id}/report` | Download the batch settlement report |
| `retrieve($id)` | `GET /api/v1/pa/financial/settlements/{id}` | Retrieve a settlement |

## Fx

### `$airwallex->fx->conversionAmendments`

[API reference](https://www.airwallex.com/docs/api/transactional_fx/conversion_amendments)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/fx/conversion_amendments/create` | Create a conversion amendment |
| `retrieve($conversionAmendmentId)` | `GET /api/v1/fx/conversion_amendments/{conversion_amendment_id}` | Retrieve an amendment |
| `all()` | `GET /api/v1/fx/conversion_amendments` | List all amendments |
| `quote()` | `POST /api/v1/fx/conversion_amendments/quote` | Quote an amendment |

### `$airwallex->fx->conversions`

[API reference](https://www.airwallex.com/docs/api/transactional_fx/conversion)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/fx/conversions/create` | Create a conversion |
| `retrieve($conversionId)` | `GET /api/v1/fx/conversions/{conversion_id}` | Retrieve a conversion |
| `all()` | `GET /api/v1/fx/conversions` | List all conversions |

### `$airwallex->fx->quotes`

[API reference](https://www.airwallex.com/docs/api/transactional_fx/quotes)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/fx/quotes/create` | Create an FX quote |
| `retrieve($quoteId)` | `GET /api/v1/fx/quotes/{quote_id}` | Retrieve a quote |

### `$airwallex->fx->rates`

[API reference](https://www.airwallex.com/docs/api/transactional_fx/rates)

| Method | Endpoint | Description |
| --- | --- | --- |
| `current()` | `GET /api/v1/fx/rates/current` | Retrieve a current rate |

## Issuing

### `$airwallex->issuing->authorizations`

[API reference](https://www.airwallex.com/docs/api/issuing/authorizations)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/issuing/authorizations/{id}` | Retrieve an Authorization |
| `all()` | `GET /api/v1/issuing/authorizations` | List all Authorizations |

### `$airwallex->issuing->cardTransactionEvents`

[API reference](https://www.airwallex.com/docs/api/issuing/card_transaction_events)

| Method | Endpoint | Description |
| --- | --- | --- |
| `all()` | `GET /api/v1/issuing/card_transaction_events` | Lists card transaction events |
| `retrieve($id)` | `GET /api/v1/issuing/card_transaction_events/{id}` | Retrieves a single card transaction event by ID |

### `$airwallex->issuing->cardTransactionLifecycles`

[API reference](https://www.airwallex.com/docs/api/issuing/card_transaction_lifecycles)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/issuing/lifecycles/{id}` | Retrieves one card transaction lifecycle by its ID |
| `all()` | `GET /api/v1/issuing/lifecycles` | Lists all card transaction lifecycles |

### `$airwallex->issuing->cardTransactions`

[API reference](https://www.airwallex.com/docs/api/issuing/card_transactions)

| Method | Endpoint | Description |
| --- | --- | --- |
| `all()` | `GET /api/v1/issuing/card_transactions` | Retrieves a list of card transactions |
| `retrieve($id)` | `GET /api/v1/issuing/card_transactions/{id}` | Retrieves a single card transaction by its ID |

### `$airwallex->issuing->cardholders`

[API reference](https://www.airwallex.com/docs/api/issuing/cardholders)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/issuing/cardholders/create` | Create a cardholder |
| `delete($id)` | `POST /api/v1/issuing/cardholders/{id}/delete` | Delete cardholder |
| `all()` | `GET /api/v1/issuing/cardholders` | Get all cardholders |
| `retrieve($id)` | `GET /api/v1/issuing/cardholders/{id}` | Get cardholder details |
| `update($id)` | `POST /api/v1/issuing/cardholders/{id}/update` | Update a cardholder |

### `$airwallex->issuing->cards`

[API reference](https://www.airwallex.com/docs/api/issuing/cards)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/issuing/cards/create` | Create a new card |
| `retrieve($id)` | `GET /api/v1/issuing/cards/{id}` | Retrieve a single card |
| `all()` | `GET /api/v1/issuing/cards` | List all cards |
| `update($id)` | `POST /api/v1/issuing/cards/{id}/update` | Update an existing card |
| `activate($id)` | `POST /api/v1/issuing/cards/{id}/activate` | Activate a card |
| `provisionDigitalToken($id)` | `POST /api/v1/issuing/cards/{id}/provision_digital_token` | Add a card to a digital wallet |
| `retrieveDetails($id)` | `GET /api/v1/issuing/cards/{id}/details` | List a card's sensitive details |
| `retrieveLimits($id)` | `GET /api/v1/issuing/cards/{id}/limits` | List a card's remaining limits |

### `$airwallex->issuing->config`

[API reference](https://www.airwallex.com/docs/api/issuing/config)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve()` | `GET /api/v1/issuing/config` | Retrieve the Issuing Config |
| `update()` | `POST /api/v1/issuing/config/update` | Update the Issuing Config |

### `$airwallex->issuing->digitalWalletTokens`

[API reference](https://www.airwallex.com/docs/api/issuing/digital_wallet_tokens)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/issuing/digital_wallet_tokens/{id}` | Retrieve a single digital wallet token |
| `all()` | `GET /api/v1/issuing/digital_wallet_tokens` | List all digital wallet tokens |
| `updateStatus($id)` | `POST /api/v1/issuing/digital_wallet_tokens/{id}/lifecycle` | Update the status of a digital wallet token |

### `$airwallex->issuing->merchantBrands`

[API reference](https://www.airwallex.com/docs/api/issuing/merchant_brands)

| Method | Endpoint | Description |
| --- | --- | --- |
| `all()` | `GET /api/v1/issuing/merchant_brands` | Returns the merchant brands that match a given query |
| `retrieve($id)` | `GET /api/v1/issuing/merchant_brands/{id}` | Returns one merchant brand by ID |

### `$airwallex->issuing->transactionDisputes`

[API reference](https://www.airwallex.com/docs/api/issuing/transaction_disputes)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/issuing/transaction_disputes/create` | Create a dispute for a card transaction |
| `retrieve($id)` | `GET /api/v1/issuing/transaction_disputes/{id}` | Retrieve a transaction dispute |
| `all()` | `GET /api/v1/issuing/transaction_disputes` | List all transaction disputes |
| `update($id)` | `POST /api/v1/issuing/transaction_disputes/{id}/update` | Update a transaction dispute |
| `submit($id)` | `POST /api/v1/issuing/transaction_disputes/{id}/submit` | Submit a transaction dispute |
| `cancel($id)` | `POST /api/v1/issuing/transaction_disputes/{id}/cancel` | Cancel a transaction dispute |

### `$airwallex->issuing->transactions`

[API reference](https://www.airwallex.com/docs/api/issuing/transactions)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/issuing/transactions/{id}` | Retrieve a single transaction by its ID |
| `all()` | `GET /api/v1/issuing/transactions` | List all transactions for cards created within your organisation |

## Payments

### `$airwallex->payments->config`

[API reference](https://www.airwallex.com/docs/api/payments/config)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieveReservePlan()` | `GET /api/v1/pa/config/reserve_plan` | Retrieve the merchant's current reserve plan |
| `allPaymentMethodTypes()` | `GET /api/v1/pa/config/payment_method_types` | List payment method types available to the merchant |
| `allBanks()` | `GET /api/v1/pa/config/banks` | List available bank names |
| `allApplePayDomains()` | `GET /api/v1/pa/config/applepay/registered_domains` | List registered Apple Pay domains |
| `addApplePayDomains()` | `POST /api/v1/pa/config/applepay/registered_domains/add_items` | Add Apple Pay registered domains |
| `removeApplePayDomains()` | `POST /api/v1/pa/config/applepay/registered_domains/remove_items` | Remove Apple Pay registered domains |
| `allConvertibleShopperCurrencies()` | `GET /api/v1/pa/config/convertible_shopper_currencies` | List currencies shoppers can convert from |

### `$airwallex->payments->conversionQuotes`

[API reference](https://www.airwallex.com/docs/api/payments/conversion_quotes)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/conversion_quotes/create` | Create a conversion quote fixing the exchange rate |
| `retrieve($id)` | `GET /api/v1/pa/conversion_quotes/{id}` | Retrieve a conversion quote |

### `$airwallex->payments->customerBalanceTransactions`

[API reference](https://www.airwallex.com/docs/api/payments/customer_balance_transactions)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/pa/customer_balance_transactions/{id}` | Retrieve a Customer Balance Transaction |

### `$airwallex->payments->customsDeclarations`

[API reference](https://www.airwallex.com/docs/api/payments/customs_declarations)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/customs_declarations/create` | Create a customs declaration |
| `retrieve($id)` | `GET /api/v1/pa/customs_declarations/{id}` | Retrieve a customs declaration |
| `update($id)` | `POST /api/v1/pa/customs_declarations/{id}/update` | Update a customs declaration |
| `redeclare($id)` | `POST /api/v1/pa/customs_declarations/{id}/redeclare` | Redeclare a customs declaration |

### `$airwallex->payments->fundsSplitReversals`

[API reference](https://www.airwallex.com/docs/api/payments/funds_split_reversals)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/funds_split_reversals/create` | Create a Funds Split Reversal |
| `retrieve($id)` | `GET /api/v1/pa/funds_split_reversals/{id}` | Retrieve a Funds Split Reversal |
| `all()` | `GET /api/v1/pa/funds_split_reversals` | List Funds Split Reversals |

### `$airwallex->payments->fundsSplits`

[API reference](https://www.airwallex.com/docs/api/payments/funds_splits)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/funds_splits/create` | Create a Funds Split |
| `all()` | `GET /api/v1/pa/funds_splits` | List Funds Splits |
| `release($id)` | `POST /api/v1/pa/funds_splits/{id}/release` | Release a Funds Split |
| `retrieve($id)` | `GET /api/v1/pa/funds_splits/{id}` | Retrieve a Funds Split |

### `$airwallex->payments->referenceData`

[API reference](https://www.airwallex.com/docs/api/payments/reference_data)

| Method | Endpoint | Description |
| --- | --- | --- |
| `lookupBin()` | `GET /api/v1/pa/reference/bin/lookup` | Look up BIN details for a card number |

### `$airwallex->payments->settlementRecords`

[API reference](https://www.airwallex.com/docs/api/payments/settlement_records)

| Method | Endpoint | Description |
| --- | --- | --- |
| `all()` | `GET /api/v1/pa/settlement_records` | Get settlement records |

### `$airwallex->payments->terminals`

[API reference](https://www.airwallex.com/docs/api/payments/terminals)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/pa/pos/terminals/create` | Create a terminal |
| `retrieve($id)` | `GET /api/v1/pa/pos/terminals/{id}` | Retrieve a terminal |
| `all()` | `GET /api/v1/pa/pos/terminals` | List terminals |
| `update($id)` | `POST /api/v1/pa/pos/terminals/{id}/update` | Update a terminal |
| `activate($id)` | `POST /api/v1/pa/pos/terminals/{id}/activate` | Activate a terminal |
| `processPaymentIntent($id)` | `POST /api/v1/pa/pos/terminals/{id}/process_payment_intent` | Process a payment intent on a terminal |
| `resetPassword($id)` | `POST /api/v1/pa/pos/terminals/{id}/reset_password` | Reset a terminal's password |
| `deactivate($id)` | `POST /api/v1/pa/pos/terminals/{id}/deactivate` | Deactivate a terminal |
| `terminate($id)` | `POST /api/v1/pa/pos/terminals/{id}/terminate` | Terminate a terminal |
| `cancelCurrentOperation($id)` | `POST /api/v1/pa/pos/terminals/{id}/cancel_current_operation` | Cancel the current operation on a terminal |

## Risk

### `$airwallex->risk->fraudFeedback`

[API reference](https://www.airwallex.com/docs/api/risk/fraud_feedback_issuing)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/risk/issuing/fraud_feedback` | Reports whether an issuing transaction was authorized by the cardholder |

### `$airwallex->risk->reports`

[API reference](https://www.airwallex.com/docs/api/risk/risk_reports)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/risk/risk_reports/{id}` | Retrieves a risk report to track its generation status |
| `generate()` | `POST /api/v1/risk/risk_reports/generate` | Submits a query to generate a risk report and returns its id |

### `$airwallex->risk->rfis`

[API reference](https://www.airwallex.com/docs/api/risk/request_for_information_rfi)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/rfis/{id}` | Fetches a single RFI by its ID |
| `all()` | `GET /api/v1/rfis` | Returns a list of RFIs |
| `respond($id)` | `POST /api/v1/rfis/{id}/respond` | Submits a response to a specific RFI |

### `$airwallex->risk->sellers`

[API reference](https://www.airwallex.com/docs/api/risk/sellers)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/sellers/create` | Create a Seller |
| `retrieve($id)` | `GET /api/v1/sellers/{id}` | Retrieve a Seller |
| `deactivate($id)` | `POST /api/v1/sellers/{id}/deactivate` | Deactivate a Seller |

### `$airwallex->risk->watchlist`

[API reference](https://www.airwallex.com/docs/api/risk/watchlist)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($itemId)` | `GET /api/v1/risk/watchlist/{item_id}` | Retrieves a specific Watchlist item using its item_id |
| `all()` | `GET /api/v1/risk/watchlist` | Lists all Watchlist items |
| `update($itemId)` | `POST /api/v1/risk/watchlist/{item_id}/update` | Changes an existing Watchlist item |
| `create()` | `POST /api/v1/risk/watchlist/create` | Adds a new item to the Watchlist |

## Scale

### `$airwallex->scale->hostedFlows`

[API reference](https://www.airwallex.com/docs/api/scale/hosted_flows)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/hosted_flows/create` | Create a hosted flow for a connected account |
| `retrieve($id)` | `GET /api/v1/hosted_flows/{id}` | Retrieve a hosted flow |
| `authorize($id)` | `POST /api/v1/hosted_flows/{id}/authorize` | Authorize a hosted flow |

### `$airwallex->scale->invitationLinks`

[API reference](https://www.airwallex.com/docs/api/scale/invitation_links)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/accounts/invitation_links/create` | Create an account invitation link |
| `retrieve($id)` | `GET /api/v1/accounts/invitation_links/{id}` | Retrieve an account invitation link |

### `$airwallex->scale->offboardings`

[API reference](https://www.airwallex.com/docs/api/scale/offboardings)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create($accountId)` | `POST /api/v1/accounts/{account_id}/offboardings/create` | Submit an account closure request |
| `retrieve($accountId, $id)` | `GET /api/v1/accounts/{account_id}/offboardings/{id}` | Retrieve an offboarding |
| `all($accountId)` | `GET /api/v1/accounts/{account_id}/offboardings` | List offboardings for a connected account |

### `$airwallex->scale->platformLiquidityPrograms`

[API reference](https://www.airwallex.com/docs/api/scale/platform_liquidity_programs)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/platform_liquidity_programs/{id}` | Retrieve a liquidity program |
| `deposit($id)` | `POST /api/v1/platform_liquidity_programs/{id}/deposit` | Deposit funds into a liquidity program |
| `withdraw($id)` | `POST /api/v1/platform_liquidity_programs/{id}/withdraw` | Withdraw funds from a liquidity program |
| `createSpendingAccount($id)` | `POST /api/v1/platform_liquidity_programs/{id}/program_spending_accounts/create` | Create a program spending account |
| `retrieveSpendingAccount($id, $spendingAccountId)` | `GET /api/v1/platform_liquidity_programs/{id}/program_spending_accounts/{spending_account_id}` | Retrieve a program spending account |
| `allSpendingAccounts($id)` | `GET /api/v1/platform_liquidity_programs/{id}/program_spending_accounts` | List program spending accounts |
| `adjustSpendingAccountLimits($id, $spendingAccountId)` | `POST /api/v1/platform_liquidity_programs/{id}/program_spending_accounts/{spending_account_id}/adjust_limits` | Adjust limits on a program spending account |
| `allTransactions($id)` | `GET /api/v1/platform_liquidity_programs/{id}/transactions` | List program transactions |

### `$airwallex->scale->platformReports`

[API reference](https://www.airwallex.com/docs/api/scale/platform_reports)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/platform_reports/create` | Request a platform report |
| `retrieve($id)` | `GET /api/v1/platform_reports/{id}` | Retrieve a platform report |

### `$airwallex->scale->pspSettlementDeposits`

[API reference](https://www.airwallex.com/docs/api/scale/psp_settlement_deposits)

| Method | Endpoint | Description |
| --- | --- | --- |
| `all()` | `GET /api/v1/psp_settlement_deposits` | List PSP settlement deposits |

### `$airwallex->scale->pspSettlementIntents`

[API reference](https://www.airwallex.com/docs/api/scale/psp_settlement_intents)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/psp_settlement_intents/create` | Create a PSP settlement intent |
| `retrieve($id)` | `GET /api/v1/psp_settlement_intents/{id}` | Retrieve a PSP settlement intent |
| `all()` | `GET /api/v1/psp_settlement_intents` | List PSP settlement intents |
| `update($id)` | `POST /api/v1/psp_settlement_intents/{id}/update` | Update a PSP settlement intent |
| `submit($id)` | `POST /api/v1/psp_settlement_intents/{id}/submit` | Submit a PSP settlement intent |
| `cancel($id)` | `POST /api/v1/psp_settlement_intents/{id}/cancel` | Cancel a PSP settlement intent |

### `$airwallex->scale->pspSettlementSplits`

[API reference](https://www.airwallex.com/docs/api/scale/psp_settlement_splits)

| Method | Endpoint | Description |
| --- | --- | --- |
| `retrieve($id)` | `GET /api/v1/psp_settlement_splits/{id}` | Retrieve a PSP settlement split |
| `all()` | `GET /api/v1/psp_settlement_splits` | List PSP settlement splits |
| `create($pspSettlementIntentId)` | `POST /api/v1/psp_settlement_intents/{psp_settlement_intent_id}/split` | Split a PSP settlement intent |
| `release($id)` | `POST /api/v1/psp_settlement_splits/{id}/release` | Release a PSP settlement split |
| `cancel($id)` | `POST /api/v1/psp_settlement_splits/{id}/cancel` | Cancel a PSP settlement split |

## Simulation

### `$airwallex->simulation->accounts`

[API reference](https://www.airwallex.com/docs/api/simulation/accounts)

| Method | Endpoint | Description |
| --- | --- | --- |
| `approveAmendment($id)` | `POST /api/v1/simulation/account/amendments/{id}/approve` | Simulate approving an account amendment |
| `rejectAmendment($id)` | `POST /api/v1/simulation/account/amendments/{id}/reject` | Simulate rejecting an account amendment |
| `updateStatus($accountId)` | `POST /api/v1/simulation/accounts/{account_id}/update_status` | Simulate changing a connected account status |

### `$airwallex->simulation->billing`

[API reference](https://www.airwallex.com/docs/api/simulation/billing)

| Method | Endpoint | Description |
| --- | --- | --- |
| `failNextAutocharge($id)` | `POST /api/v1/simulation/billing/payment_sources/{id}/fail_next_autocharge` | Simulate a failed next autocharge for a payment source |

### `$airwallex->simulation->deposits`

[API reference](https://www.airwallex.com/docs/api/simulation/deposits)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/simulation/deposit/create` | Simulate a deposit into a Global Account |
| `reject($depositId)` | `POST /api/v1/simulation/deposits/{deposit_id}/reject` | Reject a Direct Debit deposit |
| `reverse($depositId)` | `POST /api/v1/simulation/deposits/{deposit_id}/reverse` | Reverse a Direct Debit deposit |
| `settle($depositId)` | `POST /api/v1/simulation/deposits/{deposit_id}/settle` | Settle a Direct Debit deposit |

### `$airwallex->simulation->issuing`

[API reference](https://www.airwallex.com/docs/api/simulation/issuing)

| Method | Endpoint | Description |
| --- | --- | --- |
| `createTransaction()` | `POST /api/v1/simulation/issuing/create` | Simulate a transaction on an issued card |
| `captureLifecycle($cardTransactionLifecycleId)` | `POST /api/v1/simulation/issuing/card_transaction_lifecycles/{card_transaction_lifecycle_id}/capture` | Simulate capturing a card transaction lifecycle |
| `createRefund()` | `POST /api/v1/simulation/issuing/refund` | Simulate a card refund |
| `reverseLifecycle($cardTransactionLifecycleId)` | `POST /api/v1/simulation/issuing/card_transaction_lifecycles/{card_transaction_lifecycle_id}/reverse` | Simulate reversing a card transaction lifecycle |
| `passCardholderReview($id)` | `POST /api/v1/simulation/issuing/cardholders/{id}/pass_review` | Simulate a cardholder passing review |
| `notifyThreeDs()` | `POST /api/v1/simulation/issuing/threeds/notify` | Simulate a 3DS notification |

### `$airwallex->simulation->linkedAccounts`

[API reference](https://www.airwallex.com/docs/api/simulation/linked_accounts)

| Method | Endpoint | Description |
| --- | --- | --- |
| `acceptMandate($linkedAccountId)` | `POST /api/v1/simulation/linked_accounts/{linked_account_id}/mandate/accept` | Simulate accepting a mandate |
| `rejectMandate($linkedAccountId)` | `POST /api/v1/simulation/linked_accounts/{linked_account_id}/mandate/reject` | Simulate rejecting a mandate |
| `cancelMandate($linkedAccountId)` | `POST /api/v1/simulation/linked_accounts/{linked_account_id}/mandate/cancel` | Simulate cancelling a mandate |
| `failMicrodeposits($linkedAccountId)` | `POST /api/v1/simulation/linked_accounts/{linked_account_id}/fail_microdeposits` | Simulate failed micro deposits verification |

### `$airwallex->simulation->offboardings`

[API reference](https://www.airwallex.com/docs/api/simulation/offboardings)

| Method | Endpoint | Description |
| --- | --- | --- |
| `cancel($accountId, $id)` | `POST /api/v1/simulation/accounts/{account_id}/offboardings/{id}/cancel` | Simulate cancelling an offboarding |
| `complete($accountId, $id)` | `POST /api/v1/simulation/accounts/{account_id}/offboardings/{id}/complete` | Simulate completing an offboarding |

### `$airwallex->simulation->payments`

[API reference](https://www.airwallex.com/docs/api/simulation/payments)

| Method | Endpoint | Description |
| --- | --- | --- |
| `shopperAction($action)` | `POST /api/v1/simulation/pa/shopper_actions/{action}` | Simulate a shopper action |
| `createDispute()` | `POST /api/v1/simulation/pa/payment_disputes/create` | Create a simulated payment dispute |
| `resolveDispute($id)` | `POST /api/v1/simulation/pa/payment_disputes/{id}/resolve` | Resolve a simulated payment dispute |
| `escalateDispute($id)` | `POST /api/v1/simulation/pa/payment_disputes/{id}/escalate` | Escalate a simulated payment dispute |

### `$airwallex->simulation->rfis`

[API reference](https://www.airwallex.com/docs/api/simulation/request_for_information_rfi)

| Method | Endpoint | Description |
| --- | --- | --- |
| `create()` | `POST /api/v1/simulation/rfis/create` | Create an RFI |
| `followUp($id)` | `POST /api/v1/simulation/rfis/{id}/follow_up` | Send a follow-up for an RFI |
| `close($id)` | `POST /api/v1/simulation/rfis/{id}/close` | Close an RFI |

### `$airwallex->simulation->terminals`

[API reference](https://www.airwallex.com/docs/api/simulation/terminal_simulation)

| Method | Endpoint | Description |
| --- | --- | --- |
| `generateActivationCode()` | `POST /api/v1/simulation/pa/pos/terminals/generate_activation_code` | Generate a terminal activation code |
| `turnOn()` | `POST /api/v1/simulation/pa/pos/terminals/turn_on` | Power on a simulated terminal |
| `turnOff()` | `POST /api/v1/simulation/pa/pos/terminals/turn_off` | Power off a simulated terminal |
| `confirmPaymentIntent()` | `POST /api/v1/simulation/pa/pos/terminals/confirm_payment_intent` | Confirm a terminal payment using a scenario |
| `allPaymentScenarios()` | `GET /api/v1/simulation/pa/pos/terminals/payment_scenarios` | List available payment scenarios |

### `$airwallex->simulation->transfers`

[API reference](https://www.airwallex.com/docs/api/simulation/transfers)

| Method | Endpoint | Description |
| --- | --- | --- |
| `transition($id)` | `POST /api/v1/simulation/transfers/{id}/transition` | Transition a transfer status |
