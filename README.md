# 🌐 Post Once, Publish Everywhere

**Post once, publish everywhere — seamless cross-platform social posting.**  
A lightweight MVP for managing and publishing content across multiple social platforms from one dashboard.

## ✨ Features (MVP)

- 🔐 User authentication & simple profile.
- 🔗 Connect 2–3 platforms (Twitter/X, LinkedIn, Facebook).
- 📝 Create a post (text + image upload).
- 📢 Publish simultaneously across connected platforms.
- ⏰ Post scheduling (basic).
- 📊 Dashboard showing posted history.

## 🏗 Tech Stack

**Frontend:** Vue / Inertia  
**Backend:** Laravel
**Database:** PostgreSQL  
**APIs:** Twitter/X API, Facebook Graph API, LinkedIn API  
**Infrastructure:** AWS

## 🚀 Getting Started

```bash
# Clone repo
git clone https://github.com/zerexei/posexei.git
cd posexei

# Install dependencies
composer install
npm install

# Run app
composer run dev
```

(v2)
-- OAuth Accounts table
CREATE TABLE accounts (
    id BIGINT PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),
    provider VARCHAR(50),               -- facebook, linkedin, twitter, instagram, youtube, etc.
    external_user_id VARCHAR(255),      -- platform user id
    access_token TEXT,
    refresh_token TEXT,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Account destinations (pages, profiles, companies, channels)
CREATE TABLE account_destinations (
    id BIGINT PRIMARY KEY,
    account_id BIGINT REFERENCES accounts(id),
    destination_type VARCHAR(50),       -- profile, page, company, channel, group
    external_id VARCHAR(255),           -- platform-specific destination id
    name VARCHAR(255),
    metadata_json JSONB,                -- optional metadata (followers, picture, etc.)
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Posts table
CREATE TABLE posts (
    id BIGINT PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),
    post_type VARCHAR(50),              -- text, image, video, carousel, link, article
    text_content TEXT,
    link_url TEXT,
    status VARCHAR(50) DEFAULT 'draft', -- draft, scheduled, published
    publish_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Post media table (for images/videos/etc.)
CREATE TABLE post_media (
    id BIGINT PRIMARY KEY,
    post_id BIGINT REFERENCES posts(id),
    media_type VARCHAR(50),             -- image, video, etc.
    url TEXT,
    position INT,                        -- order for carousel or multi-image posts
    metadata_json JSONB,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Post destinations (fan-out table for publishing)
CREATE TABLE post_destinations (
    id BIGINT PRIMARY KEY,
    post_id BIGINT REFERENCES posts(id),
    destination_id BIGINT REFERENCES account_destinations(id),
    status VARCHAR(50) DEFAULT 'pending', -- pending, publishing, success, failed
    external_post_id VARCHAR(255),       -- platform-specific post id after publishing
    retry_count INT DEFAULT 0,
    error_message TEXT,
    platform_payload_json JSONB,         -- optional payload for retries/debugging
    published_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(post_id, destination_id)
);






(v1)

#users
- organization_id

#organizations
- owner_id
- invoice_counter 

#platform
- name (Facebook/Instagram/LinkedIn)
- endpoint

#platform_connections
- platform_id
- user_id // created_by
- organization_id
- account_name
- access_token
- refresh_token
- token_expires_at

#posts
- user_id // created_by
- organization_id
- content
- scheduled_at
- status (draft / scheduled)

#post_media
- post_id
- type (image, video, gif)
- url
- order_index (to preserve order for carousels)
- metadata (jsonb for dimensions, duration, etc.)

// if post has media -> handle media else text

#post_platform_status
- post_id
- platform_connection_id
- status (pending / success / failed)
- platform_post_id

platform->posts
post->platform

#packages
- name
- description
- amount
- billing_interval (monthly / yearly)
- max_users
- max_posts_per_month

#add_ons
- name
- description
- amount
- billing_interval (monthly / yearly)

#payment_providers
- name
- description
- endpoint
- status (active / inactive)

#package_subscriptions
- package_id
- organization_id
- status
- start_date
- end_date
- auto_renew
- payment_provider_id
- reference_id

#addon_subscriptions
- addon_id
- organization_id
- status
- start_date
- end_date
- quantity
- auto_renew

#invoices
- invoice_number
- total_amount
- status (pending / paid / failed / cancelled)

#invoice_items
- invoice_id
- subscription_id
- subscription_type
- quantity

#payment_transactions
- invoice_id
- transaction_type (charge / refund)
- payment_provider_id
- provider_transaction_id 
- amount
- currency
- status (pending | succeeded | failed | refunded)
- raw_payload 



