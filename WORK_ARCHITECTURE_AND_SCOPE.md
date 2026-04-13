# WORK ARCHITECTURE AND SCOPE: Specialized School CRM

## 1. Project Overview
This specialized CRM is engineered to automate and govern the lifecycle of school-based sales operations. It provides a robust, centralized platform that connects field agents, management, and financial controllers in a single source of truth.

### **Purpose of the System**
To streamline school visit logging, kit distribution, order formalization, and revenue collection into a unified digital workflow.

### **Business Objectives**
*   **Operational Governance**: Ensuring every sales order is audited by management before fulfillment.
*   **Financial Integrity**: Real-time visibility into "Pending Dues" vs. "Actual Collections."
*   **Strategic Growth**: Analyzing lead sources (Digital vs. Field) to allocate resources effectively.

---

## 2. Dynamic Roles & Permissions System
The CRM features a granular, dynamic authorization engine. Unlike static systems, the Admin can modify access levels in real-time without code changes.

### **How it Works**
*   **Permissions**: Atomic actions (e.g., `create-order`, `approve-visit`, `view-revenue-reports`).
*   **Roles**: Bundles of permissions (e.g., "Sales Manager" role includes `approve-visit` and `view-team-dashboard`).
*   **Assignment**: Users are assigned roles, which dynamically shapes their UI and navigation menus.

---

## 3. Role-Based Responsibility Matrix

| Role | Primary Responsibility | Key Actions | Data Access |
| :--- | :--- | :--- | :--- |
| **Sales Person (SP)** | Field Operations | Log visits, distribute kits, capture cheque details, initiate orders. | Own data only. |
| **Sales Manager (SM)** | Audit & Approval | Review pending orders, approve/reject with feedback, track team KPIs. | Department/Team data. |
| **Accounts Team** | Financial Verification | Verify bank credits, update payment status, reconcile pending amounts. | Financial/Order data. |
| **Marketing (Admin)** | Strategy & Leads | Manage lead sources, school database, user access, and global reports. | System-wide data. |

---

## 4. Sequential Workflow Movement

### **Step 1: Field Execution (Sales Person)**
*   **Action**: SP visits a school and records metadata (Name, Address, Contact Person).
*   **Transaction**: Records "Teacher Kits" distributed (Level 1/2/3).
*   **Initiation**: Captures Payment info (Cheque No/Amount) and creates a **Pending Sales Order**.

### **Step 2: Management Audit (Sales Manager)**
*   **Review**: SM receives a notification of a new order.
*   **Decision**: 
    *   **Approve**: Order is formalized; triggers "Print Order" availability.
    *   **Reject**: Order goes back to SP with a mandatory rejection reason for correction.

### **Step 3: Document Formalization**
*   **Outcome**: Once approved, a professional **Printed Sales Order** is generated for the school.

### **Step 4: Revenue Reconciliation (Accounts)**
*   **Action**: Accounts team monitors the "Approved Orders" queue.
*   **Update**: Upon bank clearance, they mark the payment as "Received" (Full/Partial).
*   **Closing**: The order status updates, reflecting in the "Total Collection" dashboard.

---

## 5. Specialized Dashboards & Analytics

Each user role is greeted with a tailored dashboard providing high-impact data at a glance.

### **Dashboard Specifications**

| Role | Widget Name | Functionality |
| :--- | :--- | :--- |
| **Sales Person** | **Visit Overview** | Shows today's visits vs. targets. |
| | **My Pending Orders** | List of orders awaiting SM approval. |
| | **Kit Tracker** | Summary of kits distributed in the current month. |
| **Sales Manager** | **Approval Queue** | Real-time list of field entries requiring immediate audit. |
| | **Team Performance** | Leaderboard of SPs by visit volume and approved sales value. |
| | **Rejection Analytics** | Tracks common reasons for order rejections to improve field training. |
| **Accounts Team** | **Revenue Pipeline** | Total value of approved orders awaiting payment. |
| | **Collection Summary** | Daily/Weekly/Monthly breakdown of verified bank credits. |
| | **Aging Report** | Highlights schools with long-standing pending dues. |
| **Marketing (Admin)** | **Lead Source ROI** | Chart showing conversion rates (Digital vs. Organic vs. Field). |
| | **Geographic Penetration**| State/City-wise distribution of school coverage. |
| | **Global Revenue** | Total system-wide sales vs. collections. |

---

## 6. Full Functional Feature List

### **A. School Management**
*   Comprehensive database with Pin Code, City, and State categorization.
*   Multiple contact person management for each school.
*   Lead source tracking for every school entry.

### **B. Field Visit Management**
*   Digital visit logs with date/time stamping.
*   Kit distribution tracking (specific item sets).
*   Direct linkage to Sales Order generation.

### **C. Sales Order Lifecycle**
*   Automated order numbering.
*   Managerial approval/rejection workflow.
*   Professional PDF/Print generation for approved orders.

### **D. Financial Tracking**
*   Cheque/Payment detail capture during field entry.
*   Accounts-side verification and partial payment handling.
*   Real-time "Pending Amount" calculations per order and per school.

### **E. User & Access Management**
*   Dynamic role creation.
*   Granular permission mapping.
*   Audit logs for user activities.

---

## 7. Delivery Roadmap & Timeline

| Phase | Activity | Duration |
| :--- | :--- | :--- |
| **Phase 1** | Requirement Finalization & DB Restructuring | Week 1 |
| **Phase 2** | Role-Based UI & Dashboard Specialization | Week 2 |
| **Phase 3** | SM Approval & Accounts Workflow Development | Week 3 |
| **Phase 4** | Reporting Engine & PDF Print Optimization | Week 4 |
| **Phase 5** | End-to-End Testing & Bug Squashing | Week 5 |
| **Phase 6** | UAT & Production Launch | Week 6 |

---
**Prepared By:** CTO & Solution Architecture Team
**Confidentiality Notice:** This document is intended solely for the client and contains proprietary workflow designs.
