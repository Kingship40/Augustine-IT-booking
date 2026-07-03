# Project Blueprint

## 1. Refined System Goal

Design and implement a responsive web application, usable on phones and desktops, that connects IT service seekers with IT service providers through a centralized platform. The system should support service discovery, requests, wallet-based payments, provider withdrawals, reviews, communication, and admin supervision.

## 2. Roles

### Service Seeker

- register and log in
- manage profile
- browse providers and services
- create service requests
- fund wallet
- pay for services
- track request status
- chat with admin
- review completed providers

### Service Provider

- register and log in
- create provider profile
- set services, skills, pricing, and availability
- accept or manage assigned jobs
- update job progress
- view wallet earnings
- request withdrawals
- receive ratings and reviews
- chat with admin

### Admin

- register and log in
- verify providers
- manage users
- manage services and categories
- monitor wallet transactions
- approve or reject withdrawal requests
- manage disputes and support chat
- view reports and analytics

## 3. Core Modules

### Authentication Module

- sign up
- sign in
- forgot password
- role-based dashboard redirect

### User Management Module

- seeker profile CRUD
- provider profile CRUD
- admin account CRUD
- provider verification status

### Services Module

- service categories
- provider services
- pricing
- search and filtering

### Request and Order Module

- create request
- assign provider
- status flow
- completion confirmation
- cancellation handling

Suggested order statuses:

- `pending`
- `accepted`
- `in_progress`
- `awaiting_confirmation`
- `completed`
- `cancelled`
- `disputed`

### Wallet Module

- seeker wallet balance
- fund wallet transaction
- escrow or platform-held payment
- provider earnings ledger
- withdrawal request and approval

### Review Module

- seeker rates provider after completed job
- one review per completed order
- average provider rating

### Admin Chat Module

- user-to-admin messaging
- provider-to-admin messaging
- ticket-like support conversations

### Reporting Module

- total users
- total providers
- total orders
- completed jobs
- pending withdrawals
- wallet transaction summary
- provider performance

## 4. Functional Requirements

- the system shall allow seekers, providers, and admins to register and log in
- the system shall restrict access based on user roles
- the system shall allow providers to publish service offerings
- the system shall allow seekers to submit service requests
- the system shall allow seekers to fund a wallet before payment
- the system shall record all wallet transactions
- the system shall allow providers to request withdrawals
- the system shall allow admins to approve or reject withdrawals
- the system shall allow seekers to review completed jobs
- the system shall provide admin chat support
- the system shall generate reports for monitoring and decisions

## 5. Non-Functional Requirements

- responsive mobile-first interface
- secure password hashing
- input validation and sanitization
- audit-friendly transaction records
- role-based authorization
- maintainable CRUD architecture
- reliable database backups

## 6. Key Workflows

### Seeker Payment Flow

1. seeker signs up or logs in
2. seeker browses providers
3. seeker creates service request
4. seeker funds wallet
5. payment is reserved for the order
6. provider delivers service
7. seeker confirms completion
8. provider earnings become withdrawable

### Provider Withdrawal Flow

1. provider completes jobs
2. earnings are credited to provider wallet
3. provider submits withdrawal request
4. admin reviews request
5. admin approves or rejects
6. transaction status is updated

### Review Flow

1. order is marked completed
2. seeker submits rating and review
3. provider average rating is recalculated

## 7. Recommended Database Entities

- users
- seeker_profiles
- provider_profiles
- admins
- service_categories
- services
- service_requests
- wallets
- wallet_transactions
- payout_requests
- reviews
- chat_threads
- chat_messages
- notifications

## 8. Security Notes

- hash passwords with `password_hash()`
- use prepared statements with `PDO`
- protect admin routes with role middleware
- validate file uploads if provider documents are added later
- record transaction references for every wallet event

## 9. Admin Dashboard KPIs

- active seekers
- active providers
- pending service requests
- total funded amount
- pending withdrawals
- completed jobs
- average provider rating

## 10. First Development Milestone

Build these pages first:

- landing page
- seeker signup/login
- provider signup/login
- admin signup/login
- dashboards for each role
- provider listing
- request creation form
- wallet page
- withdrawal page
- reviews page
- admin chat page
