# Blood Bank Management System
**North East University Bangladesh — CSE Department**

## Setup Instructions

### Requirements
- XAMPP (or any LAMP/WAMP stack)
- PHP 7.4+
- MySQL / MariaDB

### Steps

1. **Copy project files**
   - Place the entire `bbms_fixed` folder inside `htdocs/` (XAMPP) or your web root.
   - Rename the folder to `Blood_Bank_Management_System` if desired.

2. **Import the database**
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Click **Import**, choose `blood_bank_management_system.sql`
   - Click **Go**

3. **Configure DB connection** (if needed)
   - Open `config.php`
   - Change `root` / password / host if your MySQL credentials differ

4. **Run the app**
   - Visit: `http://localhost/Blood_Bank_Management_System/login.php`
   - Login: **admin / admin123**

## Default Credentials
| Username | Password |
|----------|----------|
| admin    | admin123 |

## Modules
| Page | Description |
|------|-------------|
| dashboard.php | Overview stats |
| add_donor / view_donors | Donor CRUD |
| add_donation / view_donations | Record donations, auto-updates stock |
| add_patient / view_patients | Patient records |
| add_hospital / view_hospitals | Hospital records |
| add_request / view_requests | Blood requests with approve/reject |
| view_stock | Blood inventory per group |

## Notes
- Approving a request automatically deducts from `blood_stock`.
- Recording a donation automatically increments `blood_stock`.
- Expired blood records are highlighted in `view_donations.php`.
