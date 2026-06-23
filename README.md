# Blood Bank Management System — Full Integrated Project
**North East University Bangladesh · CSE Department**

## Folder Structure
```
Blood Bank Management System/
├── index.php                          ← Entry point (redirects to visitor home)
├── login.php                          ← UNIFIED LOGIN (admin + user in one form)
├── blood_bank_management_system.sql   ← Complete database (import this)
├── visitor/                           ← Public landing site (no login required)
│   ├── index.php
│   ├── blood_availability.php
│   ├── donors.php
│   ├── hospitals.php
│   └── contact.php
├── admin/                             ← Admin management panel
│   ├── dashboard.php
│   ├── view_donors / add_donor ...
│   └── view_requests (approve/reject)
└── user/                              ← Registered user portal (LifeBank)
    ├── register.php
    ├── dashboard.php
    ├── new_request.php
    └── my_requests.php
```

## Setup
1. Copy `Blood Bank Management System/` into XAMPP `htdocs/`
2. Open phpMyAdmin → Import `blood_bank_management_system.sql`
3. Visit `http://localhost/Blood%20Bank%20Management%20System/`

## Login Flow
All logins go through **one page**: `http://localhost/Blood%20Bank%20Management%20System/login.php`

| Who       | Credential            | Password   | Goes to             |
|-----------|-----------------------|------------|---------------------|
| Admin     | `admin` (username)    | `admin123` | `admin/dashboard`   |
| User      | `user@demo.com`       | `user123`  | `user/dashboard`    |
| New user  | Register at `/user/register.php` first | — | `user/dashboard` |

## How it's connected
- **Visitor** → public pages, no login. "Login" button goes to unified login.
- **Unified login** → checks `admin` table first, then `user` table → routes accordingly.
- **Admin panel** → full CRUD: donors, donations, patients, hospitals, staff, requests.
  - Approving a request auto-fulfils from available donations via `donation_to_request`.
- **User portal** → users submit blood requests, track status, view stock and donations.
  - Requests submitted by users are visible to admin for approval.
- **Logout** from any portal → always returns to unified login page.
