# 📦 Laravel 11 Inventory API

This is a **Laravel 11 RESTful API** for managing product inventory with stock synchronization based on incoming and outgoing transactions. The system includes:

-   🔐 Authentication with Laravel Sanctum
-   📦 Product CRUD
-   📥 Incoming Transactions (increase stock)
-   📤 Outgoing Transactions (decrease stock)
-   🛡️ Input validation & error handling
-   🔄 Transaction-safe database operations

---

## 🚀 Tech Stack

-   Laravel 11 (PHP 8.2+)
-   MySQL / PostgreSQL (or any database supported by Laravel)
-   Laravel Sanctum (API Authentication)

---

## 📂 API Endpoints

### Authentication

| Method | Endpoint        | Description                |
| ------ | --------------- | -------------------------- |
| POST   | `/api/register` | Register a new user        |
| POST   | `/api/login`    | Login & receive token      |
| GET    | `/api/profile`  | Get authenticated user     |
| POST   | `/api/logout`   | Logout user (token revoke) |

---

> 🔐 All protected routes require Bearer Token.

### Product Management

| Method | Endpoint             | Description        |
| ------ | -------------------- | ------------------ |
| GET    | `/api/products`      | Get all products   |
| GET    | `/api/products/{id}` | Get product detail |
| POST   | `/api/products`      | Create new product |
| PUT    | `/api/products/{id}` | Update product     |
| DELETE | `/api/products/{id}` | Delete product     |

**Product Fields:**

```json
{
    "code": "string",
    "name": "string",
    "price": "decimal",
    "stock": "integer"
}
```

---

### Incoming Transaction (Add Stock)

| Method | Endpoint             | Description                   |
| ------ | -------------------- | ----------------------------- |
| GET    | `/api/incoming`      | Get all incoming transactions |
| GET    | `/api/incoming/{id}` | Get transaction detail        |
| POST   | `/api/incoming`      | Create incoming transaction   |
| PUT    | `/api/incoming/{id}` | Update incoming transaction   |
| DELETE | `/api/incoming/{id}` | Delete transaction            |

**Product Fields:**

```json
{
    "product_id": "integer",
    "incoming_discount": "decimal (nullable)",
    "quantity": "integer"
}
```

---

### Outgoing Transaction (Reduce Stock)

| Method | Endpoint             | Description                   |
| ------ | -------------------- | ----------------------------- |
| GET    | `/api/outgoing`      | Get all outgoing transactions |
| GET    | `/api/outgoing/{id}` | Get transaction detail        |
| POST   | `/api/outgoing`      | Create outgoing transaction   |
| PUT    | `/api/outgoing/{id}` | Update outgoing transaction   |
| DELETE | `/api/outgoing/{id}` | Delete transaction            |

**Product Fields:**

```json
{
    "product_id": "integer",
    "outgoing_discount": "decimal (nullable)",
    "quantity": "integer"
}
```
