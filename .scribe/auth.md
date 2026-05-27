# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_KEY}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

To authenticate, first call the **Login** endpoint to get a Bearer token. Then include it in the `Authorization` header as: `Bearer {token}`.
