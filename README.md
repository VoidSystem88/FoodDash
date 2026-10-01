🍕 FoodDash
A complete local food delivery system built with Laravel 12. Supports customers, restaurants, riders, and admin — all with real-time updates, live tracking, AI-powered analytics, and push notifications.

✨ Features
🛒 For Customers
Browse restaurants by cuisine

Search & filter by location

Save favorite restaurants and favorite foods

Place orders with delivery address

Live order tracking with real-time map

In-app chat with riders (with typing indicators & read receipts)

Rate restaurants & riders

Leave detailed reviews with photos

Vote on reviews (helpful / not helpful)

Report inappropriate reviews

Reorder in one click

Cancel pending orders with reason

Push notifications for order updates

Dark mode support

PWA installable

🏪 For Restaurants
Confirm or reject incoming orders

Mark orders as preparing and ready for pickup (rider gets notified)

Manage menu items with images (soft delete support)

Toggle availability in real-time

Set operating hours with auto open/close

Track sales analytics and top-selling items

AI-powered analytics assistant (ask questions in English or Tagalog)

Reply to customer reviews

Add external orders (walk-in / phone)

Upload cover & profile images

Custom prep time per restaurant

Custom badge (e.g. "Best Seller")

Platform commission tracking

🛵 For Riders
Go online / offline toggle (locked while on active delivery)

Multi-batch radius expansion for automatic delivery offers

Accept offers in real-time with live countdown

Floating draggable map with live navigation

Update order status (picked up, out for delivery, delivered)

Live location sharing with customers

Chat with customers

Daily earnings dashboard with charts

Delivery history with ratings

Record payment upon delivery

Chat inbox with unread badges

Vehicle type & plate management

Free-form: can decline offers

🛡️ For Admins
Approve or disable accounts

Separate light/dark mode logos with size slider

System configuration (town, service radius, delivery fee, commission rate)

View all orders with advanced filters

Export orders to CSV

Generate sales reports with commission breakdown

Track top restaurants and riders

Real-time order monitoring

Review moderation (hide, publish, delete)

Flagged review management

🚀 Tech Stack
Layer	Technology
Backend	Laravel 12, PHP 8.2+
Frontend	Blade, Alpine.js 3, Tailwind CSS 3
Database	MySQL / MariaDB / SQLite
Real-time	Laravel Reverb (WebSockets)
Push	Web Push API + VAPID
Maps	Leaflet + OpenStreetMap + OSRM routing
Charts	Chart.js 4
AI	Groq API (gpt-oss-120b)
PWA	Service Worker + Manifest
Build	Vite 6
📋 Requirements
PHP >= 8.2

Composer

Node.js >= 18

MySQL / MariaDB (or SQLite for local dev)

OpenSSL (for VAPID keys)

🔧 Installation
1. Clone the repository
bash
git clone https://github.com/VoidSystem88/FoodDash.git
cd FoodDash
2. Install dependencies
bash
composer install
npm install
3. Setup environment
bash
cp .env.example .env
php artisan key:generate
4. Configure .env
env
APP_NAME=FoodDash
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fooddash
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting (Reverb)
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Push Notifications (generate with: php artisan webpush:vapid)
VAPID_SUBJECT=mailto:you@example.com
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=

# AI Assistant (optional)
GROQ_API_KEY=
GROQ_MODEL=openai/gpt-oss-120b
GROQ_DAILY_LIMIT=20

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS="noreply@fooddash.test"
MAIL_FROM_NAME="${APP_NAME}"
5. Generate VAPID keys (for push notifications)
bash
php artisan webpush:vapid
Copy the generated keys to your .env.

6. Run migrations & seed
bash
php artisan migrate --seed
7. Create storage symlink
bash
php artisan storage:link
8. Build assets
bash
npm run build
9. Run the application
Open 4 terminals:

Terminal 1 — Laravel server:

bash
php artisan serve
Terminal 2 — Reverb WebSocket server:

bash
php artisan reverb:start --debug
Terminal 3 — Queue worker:

bash
php artisan queue:work
Terminal 4 — Vite dev server (or skip if you ran npm run build):

bash
npm run dev
Visit: http://localhost:8000

🔑 Default Accounts
After seeding, you can log in with these accounts (password: password):

Role	Name	Email
Admin	System Admin	adminvoid00@gmail.com
Customer	Jelvie	aparicijelvie09x@gmail.com
Restaurant	Kid (PizzaKid)	jelvieaparici09x@gmail.com
Restaurant	Ben (Oro Eatery)	restaurantoro90@gmail.com
Rider	El Isog	voidsystem88@gmail.com
Rider	Juan	fooddashrider@gmail.com
🎯 Key Features Explained
🛵 Multi-Batch Rider Search
When a restaurant confirms an order, the system searches for riders in expanding radius batches:

Batch 1 (1km) → wait 50s

Batch 2 (2km) → wait 50s

Batch 3 (3km) → wait 50s

...up to service radius

Riders not in the inner batch are skipped in outer batches. First to accept wins.

📍 Live Order Tracking
Customers can track their order on a real-time map with:

Restaurant marker (custom restaurant logo)

Rider marker (with pulse animation)

Customer marker (gender-based icon)

Live route drawn with OSRM routing (real road path, not straight line)

Distance & ETA calculations

🤖 AI Analytics Assistant
Restaurants can chat with an AI assistant (powered by Groq) that answers questions about their business. It uses tool calling to fetch real data:

get_sales_stats — sales, orders, commission

get_top_items — best sellers

get_busy_hours — peak hours

get_reviews_summary — ratings breakdown

get_rejected_orders — failed orders with reasons

get_orders_count — order counts by status

Understands English and Tagalog and responds in kind.

💰 Commission System
Platform commission rate configured per installation (default 10%)

Each order stores: commission_rate, commission_amount, restaurant_earnings

Admin dashboard shows total platform earnings

Rider sees net restaurant payment during delivery

🔔 Real-time Everything
New orders → restaurant gets instant notification

Order confirmed → rider search starts

Restaurant starts preparing → rider notified (verified state)

Food ready → rider notified to pickup

Order status changed → customer notified

New chat message → receiver notified with typing indicator

Live rider location → customer sees on map

Delivery offers → rider gets slide-down banner + notification

📁 Project Structure
text
app/
├── Console/Commands/          # Auto-toggle restaurants, recalculate ratings
├── Events/                    # Broadcast events (orders, chat, location, offers)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/             # Account, config, review moderation
│   │   ├── Customer/          # Order, favorite, review, chat
│   │   ├── Restaurant/        # Menu, order, AI assistant, review reply
│   │   └── Rider/             # Availability, order
│   └── Middleware/            # Role, approval, last seen
├── Jobs/                      # FindRiderForOrder
├── Models/                    # Eloquent models
├── Notifications/             # 17+ notification classes
└── Services/
    ├── Ai/                    # Groq client + analytics tools
    └── RiderSearchService.php # Multi-batch rider search

resources/views/
├── admin/                     # Dashboard, accounts, orders, reports, settings
├── auth/                      # Login, register, OTP, password reset
├── customer/                  # Restaurants, orders, favorites, chat, track
├── restaurant/                # Dashboard, menu, hours, analytics, reviews
├── rider/                     # Dashboard, earnings, history, chat
├── layouts/app.blade.php      # Main layout with nav, notifications, PWA
└── notifications/             # Notification list

database/migrations/           # 45+ migrations
routes/
├── web.php                    # Main routes
├── auth.php                   # Auth + OTP routes
├── channels.php               # Broadcast channels
└── console.php                # Scheduled tasks
📅 Scheduled Tasks
The following command runs every minute to auto open/close restaurants based on their schedule:

bash
php artisan restaurants:auto-toggle
Add to cron or supervisor for production.

🧪 Testing
bash
php artisan test
🔒 Security Notes
Never commit your .env file — it contains API keys, DB credentials, VAPID keys

Email verification via 6-digit OTP (expires in 10 min, 5 attempts max)

Password reset via OTP (not magic link)

Rate limiting on: OTP requests, AI chat (20/day per restaurant), password reset

Role-based middleware on all routes

CSRF protection on all POST/PUT/DELETE requests

All file uploads validated (mime type + size)

XSS protected via Blade escaping

🌐 Browser Support
Feature	Chrome	Firefox	Safari	Edge
Core App	✅	✅	✅	✅
Push Notifications	✅	✅	⚠️ (iOS 16.4+, requires PWA install)	✅
PWA Install	✅	✅	✅	✅
Live Map	✅	✅	✅	✅
Real-time Chat	✅	✅	✅	✅
📝 License
This project is open-sourced software licensed under the MIT license.

👨‍💻 Author
VoidSystem88

GitHub: @VoidSystem88

🙏 Acknowledgements
Laravel — Backend framework

Laravel Reverb — WebSocket server

Alpine.js — Lightweight JS framework

Tailwind CSS — Utility-first CSS

Leaflet — Interactive maps

OpenStreetMap — Map data

OSRM — Route calculation

Chart.js — Data visualization

Groq — Fast AI inference
