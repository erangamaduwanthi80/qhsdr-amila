# Defect Rate Dashboard — Project Plan

---

## Modules

### [M1] Roles & Permissions

**[SM1] Roles**
- [R1] Role Name (QHS Executive, QA Operator, QHS Manager, Supervisor, Production Executive, Production Supervisor)
- [R2] Add New Role

**[SM2] Permissions**
- [R1] Assign Permissions per Role (Add, View, Edit, Delete, Approve)
- [R2] Apply permissions per Module (Machine List, Defect List, Data Feed, Dashboard)

---

### [M2] Machine List

**[SM1] Add Machine**
- [R1] Machine Type (Dropdown: Pressing Machine, Wrapping Machine, Plug Hole Machine)
- [R2] Machine No
- [R3] Machine Name
- [R4] Machine Photo (optional upload)
- [R5] Remark

**[SM2] View Machine List**
- [R1] Searchable and filterable list
- [R2] This list is the source for dropdowns in M4 and M5

---

### [M3] Shift Breakdown

**[SM1] Shift**
- [R1] Shift Name (Day, Night)

**[SM2] Hour**
- [R1] Hours per Shift (6 hr, 7 hr, 8 hr, 9 hr)

---

### [M4] Defect List

**[SM1] Add Defect**
- [R1] Machine Type (Dropdown from M2.SM2)
- [R2] Defect Code
- [R3] Defect Name

**[SM2] View Defect List**
- [R1] Filterable list — source for dropdown in M5.SM1

---

### [M5] Data Feed

**[SM1] Data Entry**
- [R1] Date
- [R2] Shift (Dropdown from M3.SM1)
- [R3] Hour (Dropdown from M3.SM2, filtered by selected Shift)
- [R4] Machine Type (Dropdown from M2.SM2)
- [R5] Machine No (Dropdown from M2.SM2, filtered by selected Machine Type)
- [R6] Defect Type (Dropdown from M4.SM2, filtered by selected Machine Type)
- [R7] Defect Quantity

**[SM2] Data List**
- [R1] View submitted records with filters: Date, Shift, Hour, Machine Type, Machine No, Defect Type
- [R2] Export option (CSV / Excel)

---

### [M6] Dashboard

- [R1] Defect rate by Machine Type — Bar Chart
- [R2] Defect distribution by Defect Type — Pie Chart
- [R3] Defect trend over time — Line Chart
- [R4] Shift-wise defect comparison — Grouped Bar Chart
- [R5] Summary KPI cards (Total Defects, Top Defective Machine, Top Defect Type)
- [R6] Date range filter applied across all charts

---

## Folder Structure

Root: `C:\laragon\www\QHS DR`

```
QHS DR/
│
├── app/
│   ├── config/
│   │   └── db.php                      # MySQL DB connection using PDO
│   │
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── RoleController.php
│   │   ├── MachineController.php
│   │   ├── ShiftController.php
│   │   ├── DefectController.php
│   │   ├── DataFeedController.php
│   │   └── DashboardController.php
│   │
│   ├── models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Machine.php
│   │   ├── Shift.php
│   │   ├── Defect.php
│   │   └── DataFeed.php
│   │
│   └── views/
│       ├── layout/
│       │   ├── header.php
│       │   ├── sidebar.php
│       │   └── footer.php
│       ├── auth/
│       │   └── login.php
│       ├── roles/
│       │   ├── index.php
│       │   └── form.php
│       ├── machines/
│       │   ├── index.php
│       │   └── form.php
│       ├── shifts/
│       │   └── index.php
│       ├── defects/
│       │   ├── index.php
│       │   └── form.php
│       ├── datafeed/
│       │   ├── index.php
│       │   └── list.php
│       └── dashboard/
│           └── index.php
│
├── public/
│   ├── index.php                        # Front controller — entry point
│   ├── .htaccess                        # Routes all requests to index.php
│   └── assets/
│       ├── css/
│       ├── js/
│       └── img/
│
├── uploads/
│   └── machines/                        # Machine photo uploads (filename stored in DB)
│
├── .env                                 # DB credentials (do not commit)
└── .htaccess                            # Redirects all traffic to public/
```

---

## Development Notes

- **Local environment:** Laragon with MySQL
- **Language:** PHP (plain, no framework)
- **DB access:** PDO in `app/config/db.php`
- **Entry point:** `public/index.php` — set Laragon virtual host root to `public/`
- **Routing:** `.htaccess` in `public/` routes all requests to `index.php`
- **Uploads:** Machine photos saved to `uploads/machines/`, only filename stored in DB
- **Export:** CSV export handled server-side in PHP; Excel export via a lightweight library (e.g., PhpSpreadsheet)
- **Charts:** Dashboard charts rendered client-side using Chart.js (CDN, no install needed)
