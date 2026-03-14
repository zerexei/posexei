# System Architecture

## Overview
Posexei is built with a modular, service-oriented architecture designed for scalability and high availability. The application enables users to connect multiple social accounts and publish content across various platforms simultaneously.

## Core Components

### 1. Application Layer (Laravel)
- **Framework**: Laravel 11.x (PHP 8.3/8.4).
- **Communication**: Inertia.js (Vue) for the frontend, JSON APIs for internal service interaction.
- **Service Providers**: 
  - `SocialProviderManager`: Handles the abstraction of different social platforms (Facebook, Twitter, LinkedIn).
  - `TelemetryServiceProvider`: Manages OpenTelemetry instrumentation for traces and metrics.

### 2. Infrastructure Layer
- **Reverse Proxy**: Traefik v3 (handling routing, SSL, and load balancing).
- **Database**: PostgreSQL (Primary application data).
- **Cache & Queue**: Redis (Using `redis` driver for high performance).
- **Object Storage**: MinIO (S3-compatible) for media and artifacts.

## Data Model

### Identity & Access
- `User`: Standard Laravel user model with `Organization` relationship.
- `SocialAccount`: Represents a user's connection to a provider (e.g., a Facebook login). Stores encrypted `access_token` and `refresh_token`.
- `SocialChannel`: Represents specific destinations under an account (e.g., a Facebook Page or Group).

### Content & Publishing
- `Post`: User-created content (text, media).
- `PostMedia`: Polymorphic media attachments.
- `PostPlatformStatus`: Tracks the lifecycle of a post across different destinations (pending, success, failed).

## Process Flows

### Social Channel Sync
1. User connects an account via OAuth.
2. `LinkSocialAccount` action validates the token.
3. `SyncSocialChannels` action fetches available destinations.
4. `UpsertSocialChannels` job processes destinations in the background.

### Post Publishing
1. User creates a post and selects channels.
2. The application dispatches jobs for each platform.
3. Observers and state managers track the status through the `post_platform_status` table.
