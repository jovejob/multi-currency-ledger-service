# Multi-Currency Ledger Service - Dev Research Document

## 1. Introduction

This document provides a high-level overview of the **Multi-Currency Ledger Service**, including architecture, design choices, database structure, API endpoints, and testing strategies. The goal is to offer clarity for the QA and product teams on how the service operates and ensure alignment with business requirements.

## 2. Architecture Overview

The service is built using **Symfony 7.2** and **PHP 8.3**, ensuring a modern and scalable backend. The system follows a **CQRS (Command Query Responsibility Segregation) pattern** to efficiently handle high transaction loads and maintain data integrity.

### Key Components

- **Controllers**: Manage HTTP requests and responses.
- **DTOs (Data Transfer Objects)**: Ensure type safety and validation for request payloads.
- **Command Handlers**: Process write operations (e.g., creating ledgers and transactions).
- **Repositories**: Interact with the database for persistence and querying.
- **Services**: Encapsulate business logic for balance calculations and transactional integrity.

## 3. API Design

The system exposes the following endpoints:

### **1. Create a Ledger**

**Endpoint:** `POST /ledgers`

- **Request Payload:**
  ```json
  {
    "name": "New ikgqagain Try Other Test Ledger",
    "currency": "EUR",
    "balance": 9000.5
  }
  ```
- **Response:**
  ```json
  {
    "id": 10,
    "name": "New ikgqagain Try Other Test Ledger",
    "currency": "EUR",
    "balance": 9000.5,
    "createdAt": {
      "date": "2025-02-05 01:00:07.565884",
      "timezone_type": 3,
      "timezone": "UTC"
    }
  }
  ```

### **2. Record a Transaction**

**Endpoint:** `POST /transactions`

- **Request Payload:**
  ```json
  {
    "ledgerId": 10,
    "type": "debit",
    "amount": 10.5,
    "currency": "USD"
  }
  ```
- **Response:**
  ```json
  {
    "transactionId": 269,
    "ledgerId": 10,
    "transactionType": "debit",
    "amount": 10.5,
    "currency": "USD",
    "createdAt": "2025-02-05 01:56:26"
  }
  ```

### **3. Get Ledger Balance**

**Endpoint:** `GET /balances/{ledgerId}`

- **Response(in progress...):**

  ```json
  {
    "ledgerId": "abc123",
    "balances": {
      "USD": 500,
      "EUR": 300
    }
  }
  ```

- **Response:**
  ```json
  {
    "ledgerId": 10,
    "balance": 8948,
    "currency": "EUR"
  }
  ```

## 4. Database Schema

**Tables:**

### **Ledgers**

| Column     | Type      | Description                              |
| ---------- | --------- | ---------------------------------------- |
| id         | Integer   | Primary key (Indexed)                    |
| currency   | String(3) | Default currency of the ledger (Indexed) |
| created_at | DateTime  | Timestamp (Indexed)                      |
| balance    | Float     | Current balance                          |
| name       | String    | Ledger name                              |

### **Transactions**

| Column     | Type      | Description                      |
| ---------- | --------- | -------------------------------- |
| id         | Integer   | Primary key                      |
| ledger_id  | Integer   | Foreign key to Ledgers (Indexed) |
| type       | ENUM      | 'credit' or 'debit'              |
| amount     | Float     | Transaction amount               |
| currency   | String(3) | Transaction currency (Indexed)   |
| created_at | DateTime  | Timestamp (Indexed)              |

## 5. Concurrency & Performance

- **Database Transactions**: Uses Doctrine's transactional integrity to ensure ACID compliance.
- **Optimized Queries**: Indexed critical fields for fast lookups.
- **Rate Limiting**: Symfony Rate Limiter configured to handle up to **1,000 transactions per minute**.
- **Dockerized**: Supports easy horizontal scaling with containerized deployment.

## 6. Testing Strategy

### **Unit Tests**

- Handlers and Services are tested using PHPUnit.
- Mocked repositories for isolated testing.

### **Integration Tests**

- Controllers are tested using Symfony’s **WebTestCase**.
- Uses test_db (in-memory later optional) database for consistent results.

### **Performance Testing (in progress...)**

- **Gatling/JMeter** simulations to validate 1,000 transactions per minute capacity.

## 7. Additional Enhancements (Bonus Challenges)

- **Multi-Currency Support**: Transactions can be recorded in different currencies per ledger.
- **Mock Currency Conversion API**: Converts balances between supported currencies(in progress...).
- **Rate Limiting**: Prevents API abuse with Symfony Rate Limiter.
- **Cloud Deployment**: Configured Docker for cloud deployment readiness (in progress...).

## 8. Conclusion

This document provides an in-depth understanding of the **Multi-Currency Ledger Service**, ensuring clear communication between development, QA, and product teams. It aligns with **CQRS**, transactional integrity, and performance best practices to meet business requirements.

### **Next Steps / TODOs:**

1. **Database Optimization:**

   - Add indexing on `ledger_id` in Transactions for faster lookups.
   - Consider a separate `currencies` table for handling multiple currencies efficiently.
   - Index `currency` for optimized queries.
   - Index `created_at` for efficient time-based queries.

2. **Performance Optimization:**

   - Use batch processing for large transaction inserts.
   - Implement read replicas for scaling read operations.
   - Optimize queries by using proper indexing and query caching.

3. **Messaging & Scalability:**

   - Integrate RabbitMQ or Kafka for event-driven transaction processing.
   - Ensure message deduplication and idempotency for reliability.
   - Design the system to handle up to 1,000 transactions per minute with real-time balance reporting.

4. **Real-time Balance Calculation:**
   - Implement a caching layer (Redis) for quick balance lookups.
   - Maintain precomputed balance snapshots to reduce query overhead.
   - Use an event-driven architecture to update balances asynchronously.
