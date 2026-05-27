# Mobile Calls API Documentation

## Endpoint

```http
POST /api/v1/mobile-calls
```

---

# Authentication

This API requires an API key in the request header.

## Required Header

```http
X-BRODERSEN-KEY: your_api_key
```

Example:

```http
X-BRODERSEN-KEY: mysecretkey123
```

---

# Request Content Type

```http
Content-Type: application/json
```

---

# Request Body

## Required Fields

| Field | Type | Description |
|---|---|---|
| db_no | string | Unique DB number for the call |
| campaign_id | string | Campaign identifier |
| call_date | date | Call date |
| start_epoch | integer | Call start timestamp in epoch seconds |
| user | string | Username or agent name |
| status_name | string | Call status |

## Optional Fields

| Field | Type | Description |
|---|---|---|
| end_epoch | integer | Call end timestamp in epoch seconds |

If `end_epoch` is not provided, current server timestamp will be used automatically.

---

# Example Request

```bash
curl -X POST http://your-domain.com/api/v1/mobile-calls \
  -H "Content-Type: application/json" \
  -H "X-BRODERSEN-KEY: mysecretkey123" \
  -d '{
    "db_no": "DB1001",
    "campaign_id": "CMP001",
    "call_date": "2026-05-27",
    "start_epoch": 1779878400,
    "end_epoch": 1779878465,
    "user": "john",
    "status_name": "Completed"
}'
```

---

# Success Response

## HTTP Status

```http
201 Created
```

## Response

```json
{
  "success": true,
  "message": "Mobile call stored successfully",
  "data": {
    "id": 1,
    "db_no": "DB1001",
    "campaign_id": "CMP001",
    "call_date": "2026-05-27",
    "start_epoch": 1779878400,
    "end_epoch": 1779878465,
    "length_in_sec": 65,
    "user": "john",
    "status_name": "Completed",
    "created_at": "2026-05-27T10:00:00.000000Z",
    "updated_at": "2026-05-27T10:00:00.000000Z"
  }
}
```

---

# Validation Error Response

## HTTP Status

```http
422 Unprocessable Entity
```

## Response

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "db_no": [
      "The db no field is required."
    ]
  }
}
```

---

# Missing API Key Response

## HTTP Status

```http
401 Unauthorized
```

## Response

```json
{
  "success": false,
  "message": "API key missing"
}
```

---

# Invalid API Key Response

## HTTP Status

```http
403 Forbidden
```

## Response

```json
{
  "success": false,
  "message": "Invalid API key"
}
```

---

# Server Error Response

## HTTP Status

```http
500 Internal Server Error
```

## Response

```json
{
  "success": false,
  "message": "Something went wrong"
}
```

---

# Notes

- `db_no` must be unique.
- `start_epoch` and `end_epoch` must be valid epoch timestamps in seconds.
- `length_in_sec` is automatically calculated as:

```text
end_epoch - start_epoch
```

- If `end_epoch` is smaller than `start_epoch`, API will return validation error.