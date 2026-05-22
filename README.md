# Blood Bank Management System — Final Version
**North East University Bangladesh · CSE Department**

## Setup
1. Copy `bbms_final/` into XAMPP `htdocs/`
2. Open phpMyAdmin → Import `blood_bank_management_system.sql`
3. Visit `http://localhost/bbms_final/login.php`
4. Login: **admin / admin123**

## Schema (9 tables)
`admin` · `staff` · `donor` · `blood` · `donation` · `hospital` · `contract` · `patient` · `request` · `donation_to_request`

## Pages
| File | Purpose |
|------|---------|
| login / logout | Authentication |
| dashboard | Live stats overview |
| add_donor / view_donors / edit_donor / delete_donor | Donor CRUD |
| add_donation / view_donations | Donation recording |
| add_patient / view_patients | Patient–disease records |
| add_hospital / view_hospitals | Hospital + contract view |
| add_request / view_requests | Request submit + approve/reject |
| add_staff / view_staff / delete_staff | Staff management |
| view_stock | Live blood inventory |
